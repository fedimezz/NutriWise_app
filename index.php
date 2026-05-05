<?php
declare(strict_types=1);

// --- App bootstrap (no extra files) ---
$rootDir = __DIR__;

// Load local environment variables from .env (for XAMPP/Windows setups)
// Format: KEY=value (no quotes needed). Lines starting with # are ignored.
$envFile = $rootDir . DIRECTORY_SEPARATOR . '.env';
if (is_file($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            $pos = strpos($line, '=');
            if ($pos === false) continue;
            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));
            if ($key === '') continue;
            // Don't overwrite existing env vars (SetEnv / system env should win),
            $existing = getenv($key);
            if ($existing === false || $existing === '') {
                putenv($key . '=' . $val);
                $_ENV[$key] = $val;
            }
        }
    }
}

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Roles
if (!defined('ROLE_OWNER')) define('ROLE_OWNER', 'owner');
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 'admin');
if (!defined('ROLE_NUTRITIONIST')) define('ROLE_NUTRITIONIST', 'nutritionist');
if (!defined('ROLE_USER')) define('ROLE_USER', 'user');

// Read environment with fallbacks
function env_value(string $key, string $default = ''): string {
    $v = getenv($key);
    if ($v !== false && $v !== '') return (string)$v;
    if (isset($_ENV[$key]) && is_string($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
    if (isset($_SERVER[$key]) && is_string($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
    return $default;
}

// SMTP (email) configuration (fill these for production)
// Recommended: use an App Password (Gmail/Outlook) and STARTTLS on port 587.
if (!defined('SMTP_HOST')) define('SMTP_HOST', env_value('SMTP_HOST', 'smtp.gmail.com'));           // e.g. smtp.gmail.com
if (!defined('SMTP_PORT')) define('SMTP_PORT', (int)env_value('SMTP_PORT', '587'));                 // 587 (STARTTLS) or 465 (SSL)
if (!defined('SMTP_ENCRYPTION')) define('SMTP_ENCRYPTION', env_value('SMTP_ENCRYPTION', 'tls'));    // 'tls', 'ssl', or ''
if (!defined('SMTP_USERNAME')) define('SMTP_USERNAME', env_value('SMTP_USERNAME', ''));             // e.g. your@gmail.com
if (!defined('SMTP_PASSWORD')) define('SMTP_PASSWORD', env_value('SMTP_PASSWORD', ''));             // App password
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', env_value('SMTP_FROM_EMAIL', env_value('SMTP_USERNAME', '')));
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', env_value('SMTP_FROM_NAME', 'NutriWise'));
// For local Windows/XAMPP, certificate validation may fail if CA bundle is missing.
// Set SMTP_VERIFY_PEER=1 in production.
if (!defined('SMTP_VERIFY_PEER')) define('SMTP_VERIFY_PEER', env_value('SMTP_VERIFY_PEER', '') === '1');

// Google OAuth (Login with Google)
// Create credentials in Google Cloud Console (OAuth 2.0 Client ID - Web application).
// Authorized redirect URI example:
//   http://localhost/<your-project>/index.php?page=google_callback
if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', env_value('GOOGLE_CLIENT_ID', ''));
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', env_value('GOOGLE_CLIENT_SECRET', ''));

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $location): void {
    header("Location: {$location}");
    exit();
}

// CSRF protection (basic, session-based)
function csrf_token(): string {
    if (!isset($_SESSION['_csrf']) || !is_string($_SESSION['_csrf']) || $_SESSION['_csrf'] === '') {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_check(): void {
    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || $token === '' || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(403);
        exit('CSRF validation failed');
    }
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_user_role(): string {
    return isset($_SESSION['user_role']) && is_string($_SESSION['user_role']) ? $_SESSION['user_role'] : ROLE_USER;
}

// Role ordering: owner > admin > nutritionist > user
function role_rank(string $role): int {
    switch ($role) {
        case ROLE_OWNER: return 4;
        case ROLE_ADMIN: return 3;
        case ROLE_NUTRITIONIST: return 2;
        default: return 1;
    }
}

function require_login(string $redirectTo = 'index.php?page=login&error=Veuillez vous connecter'): void {
    if (!current_user_id()) {
        redirect($redirectTo);
    }
}

// Require at least one of the provided roles (or higher rank).
function require_role(array $roles, string $redirectTo = 'index.php?page=home'): void {
    $current = current_user_role();

    // With hierarchical roles, the *lowest* required rank is enough.
    // Example: [admin, owner] should require admin (owner passes automatically).
    $requiredRank = null;
    foreach ($roles as $r) {
        $rank = role_rank((string)$r);
        $requiredRank = ($requiredRank === null) ? $rank : min($requiredRank, $rank);
    }
    if ($requiredRank === null) {
        $requiredRank = role_rank(ROLE_USER);
    }

    if (role_rank($current) < $requiredRank) {
        redirect($redirectTo);
    }
}

// Inclusion des contrôleurs
require_once 'controllers/AuthController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/AlimentController.php';
require_once 'controllers/ActivityLogController.php';


// On récupère la page demandée, sinon 'home'
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    // --- FRONTEND ---
    case 'home':
        // On inclut la vue de la page d'accueil
        require_once 'views/front/index.php';
        break;

    case 'login':
        $auth = new AuthController();
        $auth->handleLogin();
        break;

    case 'verify_login':
        $auth = new AuthController();
        $auth->verifyLogin();
        break;

    case 'register':
        $auth = new AuthController();
        $auth->handleRegister();
        break;
    case 'verify_email':
    $auth = new AuthController();
    $auth->verifyEmail();
    break;

    case 'resend_code':
    $auth = new AuthController();
    $auth->resendCode();
    break;

    case 'logout':
        $auth = new AuthController();
        $auth->logout();
        break;

    case 'profile':
        $user = new UserController();
        $user->profile();
        break;

    case 'change_password':
        $user = new UserController();
        $user->changePassword();
        break;

    case 'aliments':
        $alimentController = new AlimentController();
        $alimentController->frontList();
        break;

    case 'recettes':
        $user = new UserController();
        $user->recettes();
        break;

    case 'suivi':
        $user = new UserController();
        $user->suivi();
        break;

  case 'motpasse':
    // Sécurité : empêcher accès direct au step=verify sans session valide
    $step = $_GET['step'] ?? 'request';
    if ($step === 'verify' && !isset($_SESSION['reset_verify'])) {
        $_SESSION['error'] = "Veuillez d'abord demander un code.";
        redirect("index.php?page=motpasse");
    }

    $auth = new AuthController();
    $auth->handleForgotPassword();
    break;



    case 'forgot_step':
    $auth = new AuthController();
    $auth->forgotStep();
    break;

    case 'test_email':
        $auth = new AuthController();
        $auth->testEmail();
        break;

    case 'debug_env':
        require_role([ROLE_OWNER]);
        require_once 'views/front/debug_env.php';
        break;

    case 'google_login':
        $auth = new AuthController();
        $auth->googleLogin();
        break;
    case 'google_signup':
    $auth = new AuthController();
    $auth->googleLogin('signup');
    break;

    case 'google_callback':
        $auth = new AuthController();
        $auth->googleCallback();
        break;

    case 'add_aliment':
        // Legacy page: redirect to the admin flow
        redirect("index.php?page=admin_add_aliment");
        break;

    case 'aliment_details':
        $alimentController = new AlimentController();
        $alimentController->details();
        break;

    case 'nutritionist_dashboard':
        // Owner/admin can access too (higher rank).
        require_role([ROLE_NUTRITIONIST]);
        $page = 'nutritionist_dashboard';
        require_once 'views/front/suivi.php';
        break;

    // --- BACKEND (ADMIN) ---
    case 'admin_dashboard':
        $admin = new AdminController();
        $admin->dashboard();
        break;
    case 'admin_delete_users_bulk':
    $admin = new AdminController();
    $admin->deleteUsersBulk();
    break;
    case 'activity_feed':
    require_role([ROLE_ADMIN, ROLE_OWNER]); // secure access
    $activity = new ActivityLogController();
    $activity->feed();
    break;


    case 'admin_recettes':
        $admin = new AdminController();
        $admin->adminRecettes();
        break;

    case 'admin_plans':
        $admin = new AdminController();
        $admin->adminPlans();
        break;

    case 'admin_users':
        $admin = new AdminController();
        $admin->listUsers();
        break;

    case 'admin_add_user':
        $admin = new AdminController();
        $admin->addUser();
        break;

    case 'admin_edit_user':
        $admin = new AdminController();
        $admin->editUser();
        break;

    case 'admin_delete_user':
        $admin = new AdminController();
        $admin->deleteUser();
        break;

    case 'admin_aliments':
        $alimentController = new AlimentController();
        $alimentController->listAliments();
        break;

    case 'admin_add_aliment':
        $alimentController = new AlimentController();
        $alimentController->addAliment();
        break;

    case 'admin_delete_aliment':
        $alimentController = new AlimentController();
        $alimentController->deleteAliment();
        break;

    case 'admin_edit_aliment':
        $alimentController = new AlimentController();
        $alimentController->editAliment();
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "Page introuvable.";
        break;
}
?>