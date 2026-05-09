<?php
// controllers/ChatbotController.php

class ChatbotController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * API endpoint pour le chatbot
     */
    public function api() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $message = strtolower(trim($input['message'] ?? ''));
        
        if (empty($message)) {
            echo json_encode(['response' => 'Veuillez poser une question.']);
            return;
        }
        
        $response = $this->processMessage($message);
        echo json_encode(['response' => $response]);
    }

    /**
     * Traiter le message et générer une réponse
     */
    private function processMessage($message) {
        // Recherche de recettes
        if (strpos($message, 'recette') !== false || strpos($message, 'plat') !== false) {
            return $this->searchRecipes($message);
        }
        
        // Recherche d'aliments / calories
        if (strpos($message, 'calorie') !== false || strpos($message, 'kcal') !== false || 
            strpos($message, 'valeur') !== false || strpos($message, 'nutrition') !== false) {
            return $this->searchFoodNutrition($message);
        }
        
        // Recherche d'ingrédients spécifiques
        if (strpos($message, 'combien') !== false || strpos($message, 'contient') !== false) {
            return $this->searchFoodInfo($message);
        }
        
        // Suggestions générales
        if (strpos($message, 'bonjour') !== false || strpos($message, 'salut') !== false) {
            return "👋 Bonjour ! Je suis l'assistant NutriWise. Je peux vous aider à trouver des recettes ou des informations nutritionnelles. Que souhaitez-vous ?";
        }
        
        if (strpos($message, 'aide') !== false || strpos($message, 'help') !== false) {
            return "🤖 Voici ce que je peux faire :\n\n• Trouver des recettes par catégorie (petit-déjeuner, entrée, plat, dessert)\n• Donner les calories et valeurs nutritionnelles des aliments\n• Suggérer des recettes rapides ou végétariennes\n\nExemples :\n- 'Recette de petit-déjeuner'\n- 'Calories d'une banane'\n- 'Recette végétarienne facile'";
        }
        
        // Réponse par défaut
        return $this->getDefaultResponse($message);
    }

    /**
     * Rechercher des recettes
     */
    private function searchRecipes($message) {
        $categorie = $this->extractCategory($message);
        $difficulte = $this->extractDifficulty($message);
        $isVegetarian = strpos($message, 'végétarien') !== false || strpos($message, 'vegetarien') !== false;
        $isRapide = strpos($message, 'rapide') !== false || strpos($message, 'vite') !== false;
        
        $sql = "SELECT nom, description, categorie, difficulte, temps_preparation, temps_cuisson 
                FROM recettes WHERE 1=1";
        $params = [];
        
        if ($categorie) {
            $sql .= " AND categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        
        if ($difficulte) {
            $sql .= " AND difficulte = :difficulte";
            $params[':difficulte'] = $difficulte;
        }
        
        if ($isRapide) {
            $sql .= " AND (temps_preparation + temps_cuisson) <= 30";
        }
        
        $sql .= " ORDER BY vues DESC LIMIT 5";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($recettes)) {
                return "😕 Je n'ai pas trouvé de recette correspondant à votre recherche. Essayez avec d'autres mots-clés !";
            }
            
            $response = "🍽️ Voici quelques recettes qui pourraient vous intéresser :\n\n";
            foreach ($recettes as $recette) {
                $temps = ($recette['temps_preparation'] + $recette['temps_cuisson']);
                $response .= "• <strong>" . htmlspecialchars($recette['nom']) . "</strong>\n";
                $response .= "  " . htmlspecialchars(substr($recette['description'] ?? '', 0, 80)) . "...\n";
                $response .= "  ⏱️ " . ($temps > 0 ? $temps . ' min' : 'Temps non spécifié') . "\n\n";
            }
            $response .= "✨ Vous souhaitez plus de détails sur une recette ? Dites-moi laquelle !";
            
            return $response;
        } catch (PDOException $e) {
            return "❌ Désolé, je n'ai pas pu accéder aux recettes pour le moment.";
        }
    }

    /**
     * Rechercher les infos nutritionnelles d'un aliment
     */
    private function searchFoodNutrition($message) {
        $aliment = $this->extractFoodName($message);
        
        if (!$aliment) {
            return "🥗 Quel aliment souhaitez-vous connaître ? Donnez-moi son nom (ex: 'Calories de la banane')";
        }
        
        $sql = "SELECT nom, calories, proteines, glucides, lipides, fibres 
                FROM aliments WHERE LOWER(nom) LIKE :nom LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':nom' => '%' . strtolower($aliment) . '%']);
            $food = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($food) {
                return "🥑 <strong>" . htmlspecialchars($food['nom']) . "</strong>\n\n" .
                       "📊 Valeurs nutritionnelles pour 100g :\n" .
                       "🔥 Calories : " . ($food['calories'] ?? 'N/A') . " kcal\n" .
                       "💪 Protéines : " . ($food['proteines'] ?? 'N/A') . " g\n" .
                       "🍚 Glucides : " . ($food['glucides'] ?? 'N/A') . " g\n" .
                       "🧈 Lipides : " . ($food['lipides'] ?? 'N/A') . " g\n" .
                       "🌾 Fibres : " . ($food['fibres'] ?? 'N/A') . " g";
            } else {
                return "😕 Je n'ai pas trouvé d'information nutritionnelle pour '{$aliment}'. Essayez un autre aliment (pomme, poulet, riz...)";
            }
        } catch (PDOException $e) {
            return "❌ Désolé, je n'ai pas pu accéder à la base de données des aliments.";
        }
    }

    /**
     * Rechercher des infos générales sur un aliment
     */
    private function searchFoodInfo($message) {
        return $this->searchFoodNutrition($message);
    }

    /**
     * Extraire la catégorie du message
     */
    private function extractCategory($message) {
        if (strpos($message, 'petit-déjeuner') !== false || strpos($message, 'petit dejeuner') !== false) {
            return 'Petit-déjeuner';
        }
        if (strpos($message, 'entrée') !== false || strpos($message, 'entree') !== false) {
            return 'Entrée';
        }
        if (strpos($message, 'plat principal') !== false || strpos($message, 'principal') !== false) {
            return 'Plat principal';
        }
        if (strpos($message, 'dessert') !== false) {
            return 'Dessert';
        }
        return null;
    }

    /**
     * Extraire la difficulté du message
     */
    private function extractDifficulty($message) {
        if (strpos($message, 'facile') !== false) {
            return 'Facile';
        }
        if (strpos($message, 'moyen') !== false) {
            return 'Moyen';
        }
        if (strpos($message, 'difficile') !== false) {
            return 'Difficile';
        }
        return null;
    }

    /**
     * Extraire le nom d'un aliment du message
     */
    private function extractFoodName($message) {
        $mots = explode(' ', $message);
        $alimentsConnus = ['banane', 'pomme', 'avocat', 'poulet', 'boeuf', 'poisson', 'riz', 'pâtes', 
                           'pain', 'oeuf', 'fromage', 'lait', 'yaourt', 'carotte', 'brocoli', 'tomate'];
        
        foreach ($mots as $mot) {
            $mot = preg_replace('/[^a-zàâçéèêëîïôûùüÿñæœ]/i', '', strtolower($mot));
            if (in_array($mot, $alimentsConnus)) {
                return $mot;
            }
        }
        
        // Si on trouve pas, prendre le dernier mot (souvent l'aliment)
        $lastWord = end($mots);
        $lastWord = preg_replace('/[^a-zàâçéèêëîïôûùüÿñæœ]/i', '', strtolower($lastWord));
        return strlen($lastWord) > 2 ? $lastWord : null;
    }

    /**
     * Réponse par défaut
     */
    private function getDefaultResponse($message) {
        $responses = [
            "🤔 Je n'ai pas bien compris. Essayez de me parler de recettes ou d'aliments !\n\nExemples :\n• 'Recette de plat principal facile'\n• 'Calories d'une pomme'\n• 'Recette végétarienne rapide'",
            "😊 Je suis spécialisé dans la nutrition ! Vous pouvez me demander :\n• Des recettes par catégorie\n• Les calories d'un aliment\n• Des suggestions de plats sains",
            "💡 Astuce : Pour une recette, dites 'Recette de [catégorie]'\nPour un aliment, dites 'Calories de [aliment]'"
        ];
        return $responses[array_rand($responses)];
    }
}
?>