<?php
// controllers/AuthController.php
// Contrôleur principal pour l'authentification des utilisateurs

require_once 'models/UserModel.php';

class AuthController {
    private $userModel;
    private string $lastEmailError = '';

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // ===========================
    // FONCTIONS D'ENVOI D'EMAIL
    // ===========================

    /**
     * Envoie un email (SMTP ou mail() selon configuration)
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $message Corps du message
     * @return bool Succès ou échec
     */
    private function sendEmail(string $to, string $subject, string $message): bool {
        $this->lastEmailError = '';
        $to = trim($to);
        
        // Validation de l'email
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->lastEmailError = 'Adresse email invalide.';
            return false;
        }

        // If SMTP is intended but missing credentials, give a clear error.
        if (defined('SMTP_HOST') && SMTP_HOST !== '') {
            $u = defined('SMTP_USERNAME') ? trim((string)SMTP_USERNAME) : '';
            $p = defined('SMTP_PASSWORD') ? trim((string)SMTP_PASSWORD) : '';
            if ($u === '' || $p === '') {
                $this->lastEmailError = "SMTP non configuré: définissez SMTP_USERNAME et SMTP_PASSWORD (ex: via fichier .env).";
                return false;
            }
        }

        // Utiliser SMTP si configuré
        if (defined('SMTP_HOST') && SMTP_HOST !== '' && defined('SMTP_USERNAME') && SMTP_USERNAME !== '' && defined('SMTP_PASSWORD') && SMTP_PASSWORD !== '') {
            return $this->sendEmailSmtp($to, $subject, $message);
        }

        // Fallback vers mail() de PHP
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/plain; charset=UTF-8';
        $from = (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'no-reply@nutriwise.local');
        $fromName = (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'NutriWise');
        $headers[] = 'From: ' . $fromName . ' <' . $from . '>';
        $headersStr = implode("\r\n", $headers);
        $ok = @mail($to, $subject, $message, $headersStr);
        
        if (!$ok) {
            $this->lastEmailError = "Échec de l'envoi via mail(). Configurez SMTP pour Gmail.";
        }
        return $ok;
    }

    /**
     * Envoie un email via SMTP (pour Gmail, Outlook, etc.)
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $message Corps du message
     * @return bool Succès ou échec
     */
    private function sendEmailSmtp(string $to, string $subject, string $message): bool {
        $host = (string)SMTP_HOST;
        $port = (int)SMTP_PORT;
        $enc = strtolower((string)SMTP_ENCRYPTION);
        $username = trim((string)SMTP_USERNAME);
        // Supprimer les espaces des mots de passe d'application Gmail
        $password = str_replace(' ', '', trim((string)SMTP_PASSWORD));
        $fromEmail = (string)SMTP_FROM_EMAIL;
        $fromName = (string)SMTP_FROM_NAME;
        $verifyPeer = (defined('SMTP_VERIFY_PEER') ? (bool)SMTP_VERIFY_PEER : false);

        // Configuration de la connexion
        $remote = $host;
        if ($enc === 'ssl') {
            $remote = 'ssl://' . $host;
            if ($port <= 0) $port = 465;
        } else {
            if ($port <= 0) $port = 587;
        }

        // Connexion au serveur SMTP
        $errno = 0;
        $errstr = '';
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer' => $verifyPeer,
                'verify_peer_name' => $verifyPeer,
                'allow_self_signed' => !$verifyPeer,
                'SNI_enabled' => true,
                'peer_name' => $host,
            ],
        ]);
        $fp = @stream_socket_client($remote . ':' . $port, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            $this->lastEmailError = "Connexion SMTP impossible ({$remote}:{$port}) : {$errstr} ({$errno})";
            return false;
        }
        stream_set_timeout($fp, 15);

        // Fonctions d'aide pour la communication SMTP
        $read = function () use ($fp): string {
            $data = '';
            while (!feof($fp)) {
                $line = fgets($fp, 515);
                if ($line === false) break;
                $data .= $line;
                if (preg_match('/^\d{3}\s/', $line)) break;
            }
            return $data;
        };

        $send = function (string $cmd) use ($fp): void {
            fwrite($fp, $cmd . "\r\n");
        };

        $expect = function (string $resp, array $codes): bool {
            foreach ($codes as $c) {
                if (str_starts_with($resp, (string)$c)) return true;
            }
            return false;
        };

        // Échange SMTP initial
        $resp = $read();
        if (!$expect($resp, [220])) { 
            $this->lastEmailError = "Réponse SMTP inattendue: {$resp}"; 
            fclose($fp); 
            return false; 
        }

        $send('EHLO nutriwise');
        $resp = $read();
        if (!$expect($resp, [250])) { 
            $this->lastEmailError = "EHLO refusé: {$resp}"; 
            fclose($fp); 
            return false; 
        }

        // TLS si nécessaire
        if ($enc === 'tls') {
            $send('STARTTLS');
            $resp = $read();
            if (!$expect($resp, [220])) { 
                $this->lastEmailError = "STARTTLS refusé: {$resp}"; 
                fclose($fp); 
                return false; 
            }
            // Prefer TLS 1.2+ when available (Gmail requires modern TLS).
            $cryptoMethod = defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')
                ? STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
                : STREAM_CRYPTO_METHOD_TLS_CLIENT;
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                $cryptoMethod = $cryptoMethod | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
            }
            $cryptoOk = @stream_socket_enable_crypto($fp, true, $cryptoMethod);
            if ($cryptoOk !== true) { 
                $this->lastEmailError = "Échec négociation TLS (OpenSSL/CA). Essayez d'activer OpenSSL dans PHP et/ou définissez SMTP_VERIFY_PEER=0 en local."; 
                fclose($fp); 
                return false; 
            }
            $send('EHLO nutriwise');
            $resp = $read();
            if (!$expect($resp, [250])) { 
                $this->lastEmailError = "EHLO (après TLS) refusé: {$resp}"; 
                fclose($fp); 
                return false; 
            }
        }

        // Authentification LOGIN
        $send('AUTH LOGIN');
        $resp = $read();
        if (!$expect($resp, [334])) { 
            $this->lastEmailError = "AUTH LOGIN refusé: {$resp}"; 
            fclose($fp); 
            return false; 
        }
        
        $send(base64_encode($username));
        $resp = $read();
        if (!$expect($resp, [334])) { 
            $this->lastEmailError = "Username refusé: {$resp}"; 
            fclose($fp); 
            return false; 
        }
        
        $send(base64_encode($password));
        $resp = $read();
        if (!$expect($resp, [235])) { 
            $this->lastEmailError = "Mot de passe/app password refusé: {$resp}"; 
            fclose($fp); 
            return false; 
        }

        // Expéditeur
        $send('MAIL FROM:<' . $fromEmail . '>');
        $resp = $read();
        if (!$expect($resp, [250])) { 
            $this->lastEmailError = "MAIL FROM refusé: {$resp}"; 
            fclose($fp); 
            return false; 
        }

        // Destinataire
        $send('RCPT TO:<' . $to . '>');
        $resp = $read();
        if (!$expect($resp, [250, 251])) { 
            $this->lastEmailError = "RCPT TO refusé: {$resp}"; 
            fclose($fp); 
            return false; 
        }

        // Envoi des données
        $send('DATA');
        $resp = $read();
        if (!$expect($resp, [354])) { 
            $this->lastEmailError = "DATA refusé: {$resp}"; 
            fclose($fp); 
            return false; 
        }

        // Construction des en-têtes
        $headers = [];
        $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
        $headers[] = 'To: <' . $to . '>';
        $headers[] = 'Subject: ' . $this->encodeHeader($subject);
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';

        $data = implode("\r\n", $headers) . "\r\n\r\n" . $message;
        // Échappement des points (dot-stuffing)
        $data = preg_replace('/^\./m', '..', (string)$data);
        fwrite($fp, $data . "\r\n.\r\n");

        $resp = $read();
        $send('QUIT');
        fclose($fp);
        
        if (!$expect($resp, [250])) {
            $this->lastEmailError = "Envoi refusé: {$resp}";
            return false;
        }
        return true;
    }

    /**
     * Encode un sujet d'email pour l'UTF-8 (RFC 2047)
     * @param string $value Texte à encoder
     * @return string Texte encodé
     */
    private function encodeHeader(string $value): string {
        if (preg_match('/[^\x20-\x7E]/', $value)) {
            return '=?UTF-8?B?' . base64_encode($value) . '?=';
        }
        return $value;
    }

    /**
     * Génère un code à 6 chiffres pour la vérification
     * @return string Code à 6 chiffres
     */
    private function generateCode6(): string {
        return (string)random_int(100000, 999999);
    }

    // ===========================
    // FONCTIONS GOOGLE OAUTH
    // ===========================

    /**
     * Initie la connexion ou l'inscription via Google OAuth
     * @param string $mode 'login' ou 'signup'
     */
    public function googleLogin(string $mode = 'login'): void {
        // Vérification de la configuration Google
        if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === '' || !defined('GOOGLE_CLIENT_SECRET') || GOOGLE_CLIENT_SECRET === '') {
            $_SESSION['error'] = "Google Login non configuré (GOOGLE_CLIENT_ID/SECRET).";
            redirect("index.php?page=login");
        }

        // Sauvegarde du mode (login ou signup) pour l'utiliser après le callback
        $_SESSION['google_oauth_mode'] = $mode;
        
        // Génération d'un état unique pour prévenir les attaques CSRF
        $state = bin2hex(random_bytes(16));
        $_SESSION['google_oauth_state'] = $state;

        // Paramètres de la requête OAuth
        $params = [
            'client_id' => (string)GOOGLE_CLIENT_ID,
            'redirect_uri' => $this->googleRedirectUri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ];

        // Redirection vers Google
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        redirect($url);
    }

    /**
     * Gère le callback de Google OAuth après authentification
     */
    public function googleCallback(): void {
        // Vérification de la configuration
        if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === '' || !defined('GOOGLE_CLIENT_SECRET') || GOOGLE_CLIENT_SECRET === '') {
            $_SESSION['error'] = "Google Login non configuré (GOOGLE_CLIENT_ID/SECRET).";
            redirect("index.php?page=login");
        }

        // Récupération du mode (login ou signup) depuis la session
        $mode = $_SESSION['google_oauth_mode'] ?? 'login';
        unset($_SESSION['google_oauth_mode']);

        // Vérification de l'état pour prévenir les attaques CSRF
        $state = (string)($_GET['state'] ?? '');
        if ($state === '' || !hash_equals((string)($_SESSION['google_oauth_state'] ?? ''), $state)) {
            $_SESSION['error'] = "État OAuth invalide. Réessayez.";
            redirect("index.php?page=login");
        }
        unset($_SESSION['google_oauth_state']);

        // Vérification des erreurs
        if (!empty($_GET['error'])) {
            $_SESSION['error'] = "Google Login annulé ou refusé.";
            redirect("index.php?page=login");
        }

        // Récupération du code d'autorisation
        $code = (string)($_GET['code'] ?? '');
        if ($code === '') {
            $_SESSION['error'] = "Code OAuth manquant.";
            redirect("index.php?page=login");
        }

        // Échange du code contre un token d'accès
        $tokenResp = $this->httpPostForm('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => (string)GOOGLE_CLIENT_ID,
            'client_secret' => (string)GOOGLE_CLIENT_SECRET,
            'redirect_uri' => $this->googleRedirectUri(),
            'grant_type' => 'authorization_code',
        ]);
        
        if (!$tokenResp['ok']) {
            $_SESSION['error'] = "Échec échange token Google (HTTP {$tokenResp['status']}). " . ($tokenResp['error'] ?: $tokenResp['raw']);
            redirect("index.php?page=login");
        }

        // Décodage de la réponse
        $tokenData = json_decode((string)$tokenResp['raw'], true);
        if (!is_array($tokenData) || empty($tokenData['id_token'])) {
            $_SESSION['error'] = "Réponse token Google invalide.";
            redirect("index.php?page=login");
        }

        // Vérification du token ID
        $idToken = (string)$tokenData['id_token'];
        $info = $this->httpGetJson('https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode($idToken));
        
        if (!$info['ok'] || !is_array($info['data'])) {
            $_SESSION['error'] = "ID token Google invalide (HTTP {$info['status']}). " . ($info['error'] ?: $info['raw']);
            redirect("index.php?page=login");
        }

        // Extraction des données utilisateur
        $d = $info['data'];
        
        // Vérification que l'audience correspond à notre client ID
        if (($d['aud'] ?? '') !== (string)GOOGLE_CLIENT_ID) {
            $_SESSION['error'] = "Audience Google invalide.";
            redirect("index.php?page=login");
        }

        // Récupération de l'email
        $email = strtolower(trim((string)($d['email'] ?? '')));
        $emailVerified = (string)($d['email_verified'] ?? '') === 'true' || (int)($d['email_verified'] ?? 0) === 1;
        
        if ($email === '' || !$emailVerified) {
            $_SESSION['error'] = "Email Google non vérifié.";
            redirect("index.php?page=login");
        }

        // Récupération du nom
        $given = trim((string)($d['given_name'] ?? ''));
        $family = trim((string)($d['family_name'] ?? ''));
        if ($given === '' && $family === '') {
            $given = 'Utilisateur';
        }

        // Vérification si l'utilisateur existe déjà
        $existing = $this->userModel->getUserByEmail($email);

        // MODE SIGNUP : Créer l'utilisateur s'il n'existe pas
        if (!$existing && $mode === 'signup') {
            $randomPass = bin2hex(random_bytes(16));
            $this->userModel->createUser($given, ($family !== '' ? $family : 'Google'), $email, $randomPass, ROLE_USER, null, 'actif');
            $existing = $this->userModel->getUserByEmail($email);
        }

        // MODE LOGIN : Échouer si l'utilisateur n'existe pas
        if (!$existing && $mode === 'login') {
            $_SESSION['error'] = "Compte Google non trouvé. Veuillez vous inscrire d'abord.";
            redirect("index.php?page=register");
        }

        if (!$existing) {
            $_SESSION['error'] = "Impossible de créer/charger l'utilisateur.";
            redirect("index.php?page=login");
        }

        // Vérification du statut du compte
        $statut = strtolower(trim((string)($existing['statut'] ?? 'actif')));
        if (!in_array($statut, ['actif', 'active'], true)) {
            $_SESSION['error'] = "Compte inactif. Veuillez contacter l'administrateur.";
            redirect("index.php?page=login");
        }

        // Connexion réussie - Création de la session
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)($existing['id'] ?? 0);
        $_SESSION['user_role'] = (string)($existing['role'] ?? ROLE_USER);
        $_SESSION['user_name'] = trim((string)($existing['prenom'] ?? '') . ' ' . (string)($existing['nom'] ?? ''));
        $_SESSION['user_email'] = (string)($existing['email'] ?? $email);
        $_SESSION['user_image'] = (string)($existing['profile_image'] ?? 'default-avatar.png');

        // Redirection selon le rôle
        $this->redirectByRole();
    }

    /**
     * Retourne l'URI de redirection pour Google OAuth
     * @return string URI de callback
     */
    private function googleRedirectUri(): string {
        return "http://localhost/nutriwise_app/index.php?page=google_callback";
    }

    // ===========================
    // FONCTIONS HTTP (cURL)
    // ===========================

    /**
     * Envoie une requête POST avec des données de formulaire
     * @param string $url URL de destination
     * @param array $data Données à envoyer
     * @return array Réponse avec statut et contenu
     */
    private function httpPostForm(string $url, array $data): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 15,
        ]);
        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'ok' => ($raw !== false && $code >= 200 && $code < 300),
            'status' => $code,
            'raw' => ($raw === false ? '' : $raw),
            'error' => $err
        ];
    }

    /**
     * Envoie une requête GET et décode la réponse JSON
     * @param string $url URL de destination
     * @return array Réponse avec données JSON décodées
     */
    private function httpGetJson(string $url): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
        ]);
        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $data = null;
        if (is_string($raw) && $raw !== '') {
            $data = json_decode($raw, true);
        }
        
        return [
            'ok' => ($raw !== false && $code >= 200 && $code < 300 && is_array($data)),
            'status' => $code,
            'raw' => ($raw === false ? '' : $raw),
            'error' => $err,
            'data' => (is_array($data) ? $data : null)
        ];
    }

    // ===========================
    // FONCTIONS D'AUTHENTIFICATION
    // ===========================

    /**
     * Gère la connexion avec email/mot de passe + code de vérification
     */
    private function sendLoginCode(array $user): bool
{
    $code = (string)random_int(100000, 999999);

    $_SESSION['login_verify'] = [
        'user' => $user,
        'code_hash' => password_hash($code, PASSWORD_DEFAULT),
        'expires_at' => time() + 10 * 60,
        'tries' => 0,
    ];

    return $this->sendEmail(
        $user['email'],
        "Code de connexion NutriWise",
        "Bonjour,\n\nVotre code de connexion est : $code\n\nCe code expire dans 10 minutes."
    );
}
public function forgotStep()
{
    $step = $_GET['step'] ?? 'request';

    if ($step === 'verify') {
        require_once 'views/front/reset_verify.php';
    } else {
        require_once 'views/front/motpasse.php';
    }
}
public function handleLogin() {
    // 🔒 Vérifier si l'utilisateur est déjà connecté
    if (current_user_id()) {
        $this->redirectByRole();
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        csrf_check(); // 🔒 Protection CSRF

        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        // ✅ Rate limit basique (anti brute-force)
        $_SESSION['_login_rl'] = $_SESSION['_login_rl'] ?? ['count' => 0, 'reset_at' => time() + 300];
        if (time() > $_SESSION['_login_rl']['reset_at']) {
            $_SESSION['_login_rl'] = ['count' => 0, 'reset_at' => time() + 300];
        }
        if ($_SESSION['_login_rl']['count'] >= 8) {
            $_SESSION['error'] = "Trop de tentatives. Réessayez plus tard.";
            redirect("index.php?page=login");
        }

        // ✅ Validation des champs
        if ($email === '' || $password === '') {
            $_SESSION['error'] = "Veuillez remplir tous les champs.";
            redirect("index.php?page=login");
        }

        // ✅ Vérifier les identifiants
        $user = $this->userModel->login($email, $password);

        // ❌ Si aucun utilisateur trouvé
        if (!$user) {
            $_SESSION['_login_rl']['count']++;
            $_SESSION['error'] = "Email ou mot de passe incorrect.";
            redirect("index.php?page=login");
        }

        // ❌ Si l'utilisateur n'a pas encore vérifié son email
        if ((int)$user['is_verified'] === 0) {
            // 👉 Générer un nouveau code
            $code = (string)random_int(100000, 999999);
            $hash = password_hash($code, PASSWORD_DEFAULT);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            $this->userModel->saveVerificationCode($email, $hash, $expiry);

            // 👉 Envoyer le code par email
            $sent = $this->sendEmail(
                $email,
                "Code de vérification NutriWise",
                "Bonjour,\n\nVotre code est : $code\n\nExpire dans 10 minutes."
            );

            if (!$sent) {
                $_SESSION['error'] = "Erreur envoi email: " . $this->lastEmailError;
                redirect("index.php?page=login");
            }

            // 👉 Rediriger vers la page de vérification
            $_SESSION['error'] = "❌ Vérifiez votre email avant de vous connecter.";
            redirect("index.php?page=verify_email&email=" . urlencode($email));
        }

        // ✅ Connexion réussie (directe, sans code supplémentaire)
        session_regenerate_id(true);
        $_SESSION['user_id']    = (int)$user['id'];
        $_SESSION['user_role']  = $user['role'] ?? ROLE_USER;
        $_SESSION['user_name']  = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_image'] = $user['profile_image'] ?? 'default-avatar.png';
        unset($_SESSION['_login_rl']);

        // ✅ Vérifier si l'utilisateur doit changer son mot de passe
        if ($this->userModel->mustChangePassword($_SESSION['user_id'])) {
            $_SESSION['must_change_password'] = 1;
            redirect("index.php?page=change_password");
        }

        // ✅ Redirection selon le rôle
        $this->redirectByRole();
        exit();
    }

    // ✅ Afficher la vue de connexion
    require_once 'views/front/login.php';
}





    /**
     * Vérifie le code envoyé par email pour finaliser la connexion
     */
    public function verifyLogin() {
        // Vérification que l'utilisateur n'est pas déjà connecté
        if (current_user_id()) {
            $this->redirectByRole();
            exit();
        }

        // Récupération de la session de vérification
        $pending = $_SESSION['login_verify'] ?? null;
        if (!is_array($pending) || empty($pending['user']) || empty($pending['code_hash'])) {
            $_SESSION['error'] = "Session expirée. Veuillez vous reconnecter.";
            redirect("index.php?page=login");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $action = $_POST['action'] ?? 'verify';

            // Renvoi du code
            if ($action === 'resend') {
                $user = (array)$pending['user'];
                $email = (string)($user['email'] ?? '');
                if ($email === '') redirect("index.php?page=login");

                $code = $this->generateCode6();
                $_SESSION['login_verify']['code_hash'] = password_hash($code, PASSWORD_DEFAULT);
                $_SESSION['login_verify']['expires_at'] = time() + 10 * 60;
                $_SESSION['login_verify']['tries'] = 0;

                $sent = $this->sendEmail(
                    $email,
                    'Votre nouveau code de connexion NutriWise',
                    "Bonjour,\n\nVotre nouveau code NutriWise est : {$code}\n\nCe code expire dans 10 minutes."
                );
                
                if (!$sent) {
                    $detail = $this->lastEmailError !== '' ? (" Détail: " . $this->lastEmailError) : '';
                    $_SESSION['error'] = "Impossible d'envoyer l'email." . $detail;
                } else {
                    $_SESSION['success'] = "Nouveau code envoyé.";
                }
                redirect("index.php?page=verify_login");
            }

            // Vérification du code
            $code = trim((string)($_POST['code'] ?? ''));
            $_SESSION['login_verify']['tries'] = (int)($_SESSION['login_verify']['tries'] ?? 0) + 1;
            $tries = (int)($_SESSION['login_verify']['tries'] ?? 0);

            // Vérification de l'expiration
            if (time() > (int)($pending['expires_at'] ?? 0)) {
                unset($_SESSION['login_verify']);
                $_SESSION['error'] = "Code expiré. Veuillez vous reconnecter.";
                redirect("index.php?page=login");
            }
            
            // Limite des tentatives
            if ($tries > 6) {
                unset($_SESSION['login_verify']);
                $_SESSION['error'] = "Trop de tentatives. Veuillez vous reconnecter.";
                redirect("index.php?page=login");
            }
            
            // Vérification du code
            if ($code === '' || !password_verify($code, (string)$pending['code_hash'])) {
                $_SESSION['error'] = "Code incorrect.";
                redirect("index.php?page=verify_login");
            }

            // Finalisation de la connexion
            $u = (array)$pending['user'];
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)($u['id'] ?? 0);
            $_SESSION['user_role'] = (string)($u['role'] ?? ROLE_USER);
            $_SESSION['user_name'] = (string)($u['name'] ?? '');
            $_SESSION['user_email'] = (string)($u['email'] ?? '');
            $_SESSION['user_image'] = (string)($u['image'] ?? 'default-avatar.png');
            unset($_SESSION['login_verify']);

            $this->redirectByRole();
            exit();
        }

        // Afficher la vue de vérification
        $authStep = 'verify_login';
        require_once 'views/front/login.php';
    }

public function verifyEmail() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $code  = trim($_POST['code'] ?? '');

        $result = $this->userModel->verifyCode($email, $code);

        if ($result === "success") {
            $_SESSION['success'] = "Compte vérifié ✅ Vous pouvez vous connecter.";
            redirect("index.php?page=login");
        } else {
            $_SESSION['error'] = "Erreur vérification: " . $result;
            redirect("index.php?page=verify_email&email=" . urlencode($email));
        }
    }

    require 'views/front/verify.php';
}


    public function resendCode() {
        $email = trim($_GET['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "invalid email";
            exit;
        }

        // generate new code
        $code = $this->generateCode6();
        $hash = password_hash($code, PASSWORD_DEFAULT);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // save in DB
        $this->userModel->saveVerificationCode($email, $hash, $expiry);

        // send email
        $sent = $this->sendEmail(
            $email,
            "Nouveau code de vérification",
            "Bonjour,\n\nVotre nouveau code est : $code\n\nExpire dans 10 minutes."
        );

        if (!$sent) {
            echo "error: " . $this->lastEmailError;
            exit;
        }

        echo "sent";
    }

    /**
     * Gère l'inscription d'un nouvel utilisateur
     */
  public function handleRegister() {
    // 🔒 Vérifier si l'utilisateur est déjà connecté
    if (current_user_id()) {
        redirect("index.php?page=home");
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        csrf_check(); // 🔒 Protection CSRF

        $prenom   = trim($_POST['prenom'] ?? '');
        $nom      = trim($_POST['nom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        $errors = [];

        // ✅ Validation des champs
        if ($prenom === '') $errors[] = "Prénom requis";
        if ($nom === '') $errors[] = "Nom requis";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
        if (strlen($password) < 6) $errors[] = "Mot de passe trop faible (min 6)";
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{6,}$/', $password)) {
            $errors[] = "Mot de passe doit contenir lettres + chiffres";
        }

        if ($errors) {
            $_SESSION['error'] = implode(", ", $errors);
            redirect("index.php?page=register");
        }

        try {
            // ✅ Vérifier si l'email existe déjà
            if ($this->userModel->emailExists($email)) {
                $_SESSION['error'] = "Email déjà utilisé";
                redirect("index.php?page=register");
            }

            // ✅ Créer l'utilisateur
            $this->userModel->register($prenom, $nom, $email, $password);

            // ✅ Générer un code de vérification
            $code = (string)random_int(100000, 999999);
            $hash = password_hash($code, PASSWORD_DEFAULT);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // ✅ Sauvegarder le code dans la base
            $this->userModel->saveVerificationCode($email, $hash, $expiry);

            // ✅ Envoyer l'email avec le code
            $sent = $this->sendEmail(
                $email,
                "Code de vérification NutriWise",
                "Bonjour,\n\nVotre code est : $code\n\nExpire dans 10 minutes."
            );

            if (!$sent) {
                $_SESSION['error'] = "Erreur envoi email: " . $this->lastEmailError;
                redirect("index.php?page=register");
            }

            // ✅ Rediriger vers la page de vérification
            redirect("index.php?page=verify_email&email=" . urlencode($email));
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            redirect("index.php?page=register");
        }
    }

    // ✅ Afficher la vue d'inscription
    require_once 'views/front/register.php';
}


    /**
     * Gère la réinitialisation du mot de passe oublié
     */
public function handleForgotPassword()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_check();
        $action = $_POST['action'] ?? 'request_reset';

        // STEP 1: Demande de code
        if ($action === 'request_reset') {
            $email = trim($_POST['email'] ?? '');
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email invalide.";
                redirect("index.php?page=motpasse");
            }

            $user = $this->userModel->getUserByEmail($email);
            if (!$user) {
                $_SESSION['error'] = "Utilisateur introuvable.";
                redirect("index.php?page=motpasse");
            }

            $code = (string)random_int(100000, 999999);
            $_SESSION['reset_verify'] = [
                'email'      => $email,
                'user_id'    => $user['id'],
                'code_hash'  => password_hash($code, PASSWORD_DEFAULT),
                'expires_at' => time() + 600,
                'tries'      => 0
            ];

            $sent = $this->sendEmail(
                $email,
                "Code de réinitialisation NutriWise",
                "Votre code est : $code\n\nCe code expire dans 10 minutes."
            );

            if (!$sent) {
                unset($_SESSION['reset_verify']);
                $_SESSION['error'] = "Erreur lors de l'envoi de l'email.";
                redirect("index.php?page=motpasse");
            }

            $_SESSION['success'] = "Code envoyé à votre adresse email.";
            redirect("index.php?page=motpasse&step=verify");
        }

        // STEP 2: Vérification + reset
        if ($action === 'reset_password') {
            if (!isset($_SESSION['reset_verify'])) {
                $_SESSION['error'] = "Accès refusé. Code requis.";
                redirect("index.php?page=motpasse");
            }

            $pending = $_SESSION['reset_verify'];

            if (time() > $pending['expires_at']) {
                unset($_SESSION['reset_verify']);
                $_SESSION['error'] = "Code expiré.";
                redirect("index.php?page=motpasse");
            }

            $_SESSION['reset_verify']['tries']++;
            if ($_SESSION['reset_verify']['tries'] > 5) {
                unset($_SESSION['reset_verify']);
                $_SESSION['error'] = "Trop de tentatives.";
                redirect("index.php?page=motpasse");
            }

            $code = trim($_POST['code'] ?? '');
            $newPassword = trim($_POST['new_password'] ?? '');

            if ($code === '' || !password_verify($code, $pending['code_hash'])) {
                $_SESSION['error'] = "Code incorrect.";
                redirect("index.php?page=motpasse&step=verify");
            }

            if (strlen($newPassword) < 8) {
                $_SESSION['error'] = "Mot de passe trop court (min. 8 caractères).";
                redirect("index.php?page=motpasse&step=verify");
            }

            $user = $this->userModel->getUserByEmail($pending['email']);
            if (!$user) {
                unset($_SESSION['reset_verify']);
                $_SESSION['error'] = "Utilisateur introuvable.";
                redirect("index.php?page=motpasse");
            }

            $this->userModel->updateUserPassword($user['id'], $newPassword);

            unset($_SESSION['reset_verify']);
            $_SESSION['success'] = "Mot de passe changé avec succès.";
            redirect("index.php?page=login");
        }

        redirect("index.php?page=motpasse");
    }

    require_once 'views/front/motpasse.php';
}



    /**
     * Déconnexion de l'utilisateur
     */
    public function logout() {
        // Destruction complète de la session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        redirect("index.php?page=home");
    }

    /**
     * Redirige l'utilisateur selon son rôle
     */
    private function redirectByRole() {
        $role = current_user_role();
        switch($role) {
            case ROLE_OWNER:
            case ROLE_ADMIN:
                redirect("index.php?page=admin_dashboard");
                break;
            case ROLE_NUTRITIONIST:
                redirect("index.php?page=nutritionist_dashboard");
                break;
            default:
                redirect("index.php?page=home");
        }
    }

    /**
     * Fonction de test pour vérifier la configuration email
     * (Accessible uniquement aux propriétaires)
     */
    public function testEmail(): void {
        require_role([ROLE_OWNER]);
        $to = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $to = trim((string)($_POST['to'] ?? ''));
            $subject = 'Test SMTP NutriWise';
            $message = "Bonjour,\n\nCeci est un email de test envoyé par NutriWise.\n\nSi vous recevez ce message, la configuration SMTP fonctionne.\n\nDate: " . date('Y-m-d H:i:s');

            $sent = $this->sendEmail($to, $subject, $message);
            if ($sent) {
                $_SESSION['success'] = "Email envoyé à {$to}. Vérifiez votre boîte de réception (et Spam).";
            } else {
                $detail = $this->lastEmailError !== '' ? (" Détail: " . $this->lastEmailError) : '';
                $_SESSION['error'] = "Échec envoi email." . $detail;
            }
            redirect("index.php?page=test_email");
        }

        require_once 'views/front/test_email.php';
    }
}
?>