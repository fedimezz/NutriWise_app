<?php
class ChatbotController {
    
    // ===== CONFIGURATION API GEMINI =====
    // Pour obtenir une clé : https://aistudio.google.com/app/apikey
    private $apiKey = 'AIzaSyBdIUVcRt0I7QchsWodR17cArK48zkIQfI';
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    private $memoryLimit = 8;

    public function __construct() {
        // Priorite a la variable d'environnement, sinon fallback sur la constante eventuelle.
        $this->apiKey = getenv('GEMINI_API_KEY') ?: (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : $this->apiKey);
    }
    
    /**
     * Répondre à une requête AJAX du chatbot
     */
    public function respond() {
        // Nettoyer le buffer (éviter conflits avec session_start)
        if(ob_get_length()) ob_clean();
        
        header('Content-Type: application/json; charset=utf-8');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            echo json_encode(['response' => '❓ Merci d\'envoyer votre question depuis le formulaire du chatbot.']);
            exit;
        }
        
        // Lire le message envoyé par le chatbot widget
        $input = json_decode(file_get_contents('php://input'), true);
        $message = $input['message'] ?? '';
        $history = $this->normalizeHistory($input['history'] ?? []);
        
        if(empty($message)) {
            echo json_encode(['response' => '❓ Veuillez poser une question !']);
            exit;
        }
        
        // Appeler l'IA (Gemini) pour une réponse intelligente
        $response = $this->callGemini($message, $history);
        $this->updateConversationMemory($message, $response);
        
        echo json_encode(['response' => $response]);
        exit;
    }
    
    /**
     * Appeler l'API Google Gemini pour une réponse IA
     */
    private function callGemini($message, array $history = []) {
        if (empty($this->apiKey)) {
            return $this->getFallbackResponse($message);
        }
        
        // Prompt système : assistant generaliste avance
        $systemPrompt = "Tu es NutriBot, un assistant IA avancé et généraliste de la plateforme NutriWise. " .
            "Tu peux répondre à pratiquement toutes les questions: santé, technologie, études, productivité, cuisine, etc. " .
            "Si la question concerne la santé, donne des conseils prudents et invite à consulter un professionnel pour les urgences. " .
            "Réponds en français, de manière claire, précise et concise (max 220 mots). " .
            "Utilise des emojis pour rendre tes réponses agréables. " .
            "Ne mets jamais de markdown (pas de ** ou ##), utilise du HTML simple (<b>, <br>) pour le formatage.";
        
        $fullHistory = array_merge($this->getConversationMemory(), $history);
        $historyText = $this->buildHistoryContext($fullHistory);

        $prompt = $systemPrompt;
        if (!empty($historyText)) {
            $prompt .= "\n\nContexte de conversation récent:\n" . $historyText;
        }
        $prompt .= "\n\nQuestion actuelle de l'utilisateur: " . $message;

        $data = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $prompt]]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024
            ]
        ];
        $modelUrls = $this->getCandidateModelUrls();

        $decoded = null;
        $lastError = '';
        $lastCurlError = '';

        foreach ($modelUrls as $modelUrl) {
            $url = $modelUrl . '?key=' . $this->apiKey;
            $request = $this->sendGeminiRequest($url, $data);

            if (!empty($request['curlError'])) {
                $lastCurlError = $request['curlError'];
                continue;
            }

            if ((int) $request['httpCode'] === 200) {
                $decoded = json_decode($request['result'], true);
                break;
            }

            $lastError = $this->extractGeminiErrorMessage($request['result'], (int) $request['httpCode']);

            // Clé invalide => inutile de réessayer un autre modèle.
            if (stripos($lastError, 'api key not valid') !== false || stripos($lastError, 'invalid api key') !== false) {
                return $this->getFallbackResponse($message);
            }
        }

        if ($decoded === null) {
            if (!empty($lastCurlError)) {
                return $this->getFallbackResponse($message);
            }

            if (!empty($lastError)) {
                $lowerError = mb_strtolower($lastError);
                if (strpos($lowerError, 'quota exceeded') !== false || strpos($lowerError, 'rate limit') !== false || strpos($lowerError, 'billing') !== false) {
                    return $this->getFallbackResponse($message);
                }
                return $this->getFallbackResponse($message);
            }

            return $this->getFallbackResponse($message);
        }
        
        // Récupérer la réponse (ignorer les parties "thinking")
        if (isset($decoded['candidates'][0]['content']['parts'])) {
            $parts = $decoded['candidates'][0]['content']['parts'];
            $text = '';
            // Chercher la dernière partie non-thinking
            foreach ($parts as $part) {
                if (isset($part['text']) && empty($part['thought'])) {
                    $text = $part['text'];
                }
            }
            // Si rien trouvé, prendre la dernière partie
            if (empty($text) && !empty($parts)) {
                $lastPart = end($parts);
                $text = $lastPart['text'] ?? '';
            }
            if (!empty($text)) {
                // Nettoyer le markdown résiduel
                $text = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $text);
                $text = str_replace("\n", '<br>', $text);
                $text = preg_replace('/#{1,3}\s/', '', $text);
                return $text;
            }
        }
        
        return $this->getFallbackResponse($message);
    }

    private function sendGeminiRequest($url, array $data) {
        $result = '';
        $httpCode = 0;
        $curlError = '';

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            $result = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n",
                    'content' => json_encode($data),
                    'timeout' => 20
                ]
            ]);
            $result = @file_get_contents($url, false, $context);
            if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
                $httpCode = (int) $matches[1];
            }
            if ($result === false) {
                $curlError = 'Impossible de joindre le service IA.';
            }
        }

        return ['result' => $result, 'httpCode' => $httpCode, 'curlError' => $curlError];
    }

    private function getCandidateModelUrls() {
        $candidates = [$this->apiUrl];
        $listed = $this->listGeminiGenerateModels();

        if (empty($listed)) {
            return $candidates;
        }

        // Prioriser les modèles "flash" puis compléter avec les autres.
        $flash = [];
        $others = [];
        foreach ($listed as $url) {
            if (stripos($url, 'flash') !== false) {
                $flash[] = $url;
            } else {
                $others[] = $url;
            }
        }

        $ordered = array_merge($candidates, $flash, $others);
        return array_values(array_unique($ordered));
    }

    private function listGeminiGenerateModels() {
        if (empty($this->apiKey)) {
            return [];
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . urlencode($this->apiKey);
        $result = '';
        $httpCode = 0;

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $result = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 15
                ]
            ]);
            $result = @file_get_contents($url, false, $context);
            if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
                $httpCode = (int) $matches[1];
            }
        }

        if ($httpCode !== 200 || empty($result)) {
            return [];
        }

        $decoded = json_decode($result, true);
        if (!isset($decoded['models']) || !is_array($decoded['models'])) {
            return [];
        }

        $urls = [];
        foreach ($decoded['models'] as $model) {
            $name = $model['name'] ?? '';
            $methods = $model['supportedGenerationMethods'] ?? [];
            if (!is_string($name) || empty($name)) {
                continue;
            }
            if (!is_array($methods) || !in_array('generateContent', $methods, true)) {
                continue;
            }
            if (stripos($name, 'gemini') === false) {
                continue;
            }
            $urls[] = 'https://generativelanguage.googleapis.com/v1beta/' . $name . ':generateContent';
        }

        return $urls;
    }

    private function extractGeminiErrorMessage($result, $httpCode) {
        $decoded = json_decode((string) $result, true);
        $errorMsg = $decoded['error']['message'] ?? ('HTTP ' . $httpCode);
        return trim((string) $errorMsg);
    }

    private function buildHistoryContext(array $history) {
        if (empty($history)) {
            return '';
        }

        $lines = [];
        foreach ($history as $item) {
            $role = ($item['role'] ?? '') === 'assistant' ? 'NutriBot' : 'Utilisateur';
            $text = trim((string) ($item['content'] ?? ''));
            if ($text !== '') {
                $lines[] = '- ' . $role . ': ' . $text;
            }
        }

        return implode("\n", array_slice($lines, -$this->memoryLimit));
    }

    private function updateConversationMemory($userMessage, $assistantMessage) {
        if (!isset($_SESSION['chatbot_memory']) || !is_array($_SESSION['chatbot_memory'])) {
            $_SESSION['chatbot_memory'] = [];
        }

        $_SESSION['chatbot_memory'][] = ['role' => 'user', 'content' => trim((string) $userMessage)];
        $_SESSION['chatbot_memory'][] = ['role' => 'assistant', 'content' => trim((string) strip_tags($assistantMessage))];

        $_SESSION['chatbot_memory'] = array_slice($_SESSION['chatbot_memory'], -$this->memoryLimit);
    }

    private function getConversationMemory() {
        if (!isset($_SESSION['chatbot_memory']) || !is_array($_SESSION['chatbot_memory'])) {
            return [];
        }
        return array_slice($_SESSION['chatbot_memory'], -$this->memoryLimit);
    }

    private function normalizeHistory($history) {
        if (!is_array($history)) {
            return [];
        }

        $clean = [];
        foreach ($history as $item) {
            if (!is_array($item)) {
                continue;
            }
            $role = $item['role'] ?? '';
            $content = trim((string) ($item['content'] ?? ''));
            if (($role === 'user' || $role === 'assistant') && $content !== '') {
                $clean[] = ['role' => $role, 'content' => strip_tags($content)];
            }
        }

        return array_slice($clean, -$this->memoryLimit);
    }
    
    /**
     * Réponses de secours si l'API n'est pas disponible
     */
    private function getFallbackResponse($message) {
        $msg = mb_strtolower(trim($message));

        if (empty($msg)) {
            return '❓ Je n\'ai pas reçu de question. Vous pouvez me demander par exemple : "Que manger pour booster mon immunité ?"';
        }

        if (preg_match('/\b(bonjour|salut|hello|coucou)\b/u', $msg)) {
            return '👋 Bonjour ! Je suis <b>NutriBot</b>, votre assistant IA. Je peux répondre à vos questions générales (tech, études, productivité, santé, cuisine, etc.).';
        }

        if (preg_match('/\b(quel est ton nom|tu t\'appelles comment|ton nom)\b/u', $msg)) {
            return '🤖 Je m\'appelle <b>NutriBot</b>, votre assistant IA sur NutriWise.';
        }

        if (preg_match('/\b(merci|thanks)\b/u', $msg)) {
            return '🙏 Avec plaisir ! Je reste disponible pour vos questions nutrition.';
        }

        $knowledge = [
            'immunite' => [
                'keywords' => ['immunit', 'rhume', 'defense', 'virus'],
                'answer' => '🛡️ Pour soutenir l\'immunité :<br>• Vitamine C (agrumes, kiwi, poivron)<br>• Zinc (fruits de mer, graines de courge)<br>• Probiotiques (yaourt, kéfir)<br>• Sommeil 7-9h et bonne hydratation 💧'
            ],
            'digestion' => [
                'keywords' => ['digestion', 'ballonnement', 'constipation', 'ventre', 'transit'],
                'answer' => '🌿 Pour une meilleure digestion :<br>• Fibres progressives (légumes, avoine, légumineuses)<br>• Eau suffisante (1.5-2L/jour)<br>• Manger lentement et bien mastiquer<br>• Limiter les repas très gras tard le soir'
            ],
            'stress' => [
                'keywords' => ['stress', 'anx', 'fatigue nerveuse', 'surmenage'],
                'answer' => '🧘 Alimentation anti-stress :<br>• Magnésium (amandes, chocolat noir, légumineuses)<br>• Oméga-3 (sardines, noix, graines de lin)<br>• Réduire café et sucres rapides<br>• Repas réguliers pour stabiliser l\'énergie'
            ],
            'sommeil' => [
                'keywords' => ['sommeil', 'dormir', 'insomnie', 'nuit'],
                'answer' => '😴 Pour mieux dormir :<br>• Dîner léger 2-3h avant le coucher<br>• Favoriser tryptophane (oeufs, yaourt, banane)<br>• Eviter café/thé après 16h<br>• Limiter alcool et écrans le soir'
            ],
            'poids' => [
                'keywords' => ['maigrir', 'perdre du poids', 'mincir', 'regime', 'régime'],
                'answer' => '⚖️ Pour perdre du poids durablement :<br>• Deficit modere (300-500 kcal/jour)<br>• Proteines a chaque repas<br>• Plus de legumes et fibres, moins d\'ultra-transformes<br>• Activite physique reguliere (marche + renforcement)'
            ],
            'muscle' => [
                'keywords' => ['muscle', 'prise de masse', 'sport', 'protéine', 'proteine'],
                'answer' => '💪 Pour le sport et les muscles :<br>• Apport proteique cible : ~1.2 a 2 g/kg/jour selon l\'objectif<br>• Glucides complexes autour de l\'entrainement<br>• Hydratation et sommeil de qualite<br>• Collation post-effort: proteines + glucides'
            ],
            'diabete' => [
                'keywords' => ['diabete', 'glycemie', 'sucre', 'insuline'],
                'answer' => '🩸 Pour mieux gerer la glycemie :<br>• Prioriser fibres + proteines aux repas<br>• Choisir glucides a index glycémique bas (legumineuses, cereales completes)<br>• Eviter boissons sucrees<br>• Fractionner les portions de glucides'
            ],
            'hypertension' => [
                'keywords' => ['tension', 'hypertension', 'sel', 'pression'],
                'answer' => '❤️ Pour la tension arterielle :<br>• Reduire le sel (produits industriels, charcuteries)<br>• Augmenter potassium (banane, legumes, legumes secs)<br>• Plus d\'aliments frais et moins transformes<br>• Maintenir un poids sain'
            ],
            'etiquette' => [
                'keywords' => ['etiquette', 'étiquette', 'nutritionnelle', 'ingredients'],
                'answer' => '🔎 Lire une etiquette nutritionnelle :<br>• Verifier la portion de reference<br>• Regarder sucres, sel, graisses saturees<br>• Liste d\'ingredients la plus courte possible<br>• Plus un ingredient sucre apparait tot, plus il est present'
            ],
            'petit_dejeuner' => [
                'keywords' => ['petit dejeuner', 'petit-déjeuner', 'matin', 'dejeuner'],
                'answer' => '🌅 Petit-déjeuner équilibré :<br>• 1 source de protéines (oeufs, yaourt grec)<br>• 1 glucide complet (avoine, pain complet)<br>• 1 fruit frais<br>• 1 boisson non sucrée'
            ],
            'cholesterol' => [
                'keywords' => ['cholesterol', 'cholestérol', 'ldl', 'hdl'],
                'answer' => '🫀 Pour améliorer le cholestérol :<br>• Plus de fibres solubles (avoine, légumineuses)<br>• Bonnes graisses (huile d\'olive, noix, poissons gras)<br>• Réduire charcuteries/fritures<br>• Activité physique régulière'
            ],
            'hydratation' => [
                'keywords' => ['eau', 'hydrat', 'boire'],
                'answer' => '💧 Hydratation pratique :<br>• Visez 1.5 à 2L d\'eau/jour<br>• Augmentez si sport/chaleur<br>• Urines claires = bon indicateur<br>• Limitez sodas et jus sucrés'
            ]
        ];

        $matchedAnswers = [];
        foreach ($knowledge as $entry) {
            foreach ($entry['keywords'] as $keyword) {
                if (strpos($msg, $keyword) !== false) {
                    $matchedAnswers[] = $entry['answer'];
                    break;
                }
            }
        }

        if (!empty($matchedAnswers)) {
            return implode('<br><br>', array_slice(array_unique($matchedAnswers), 0, 2));
        }

        // Réponse générique dynamique pour éviter de répéter toujours le même texte.
        $intent = 'mieux manger';
        if (preg_match('/(maigr|poids|mincir|gras)/u', $msg)) $intent = 'perdre du poids';
        elseif (preg_match('/(muscl|sport|perform|entrain)/u', $msg)) $intent = 'améliorer vos performances sportives';
        elseif (preg_match('/(fatigue|energie|énergie)/u', $msg)) $intent = 'retrouver de l\'énergie';
        elseif (preg_match('/(digestion|ventre|transit)/u', $msg)) $intent = 'améliorer votre digestion';

        return '🤖 Bonne question. Même sans IA en ligne, je peux vous aider pour <b>' . $intent . '</b>.<br>' .
            'Voici une base simple :<br>' .
            '• Remplissez 1/2 assiette de légumes<br>' .
            '• Ajoutez une protéine à chaque repas<br>' .
            '• Choisissez des féculents complets en portion modérée<br>' .
            '• Limitez produits ultra-transformés et boissons sucrées<br>' .
            '• Buvez de l\'eau régulièrement 💧<br><br>' .
            'Pour des réponses IA avancées sur n\'importe quel sujet, utilisez une clé Gemini valide avec quota disponible.';
    }
}
?>
