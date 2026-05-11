<?php
// controllers/AuthController.php
require_once 'models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function handleRegister() {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $prenom = $_POST['prenom'] ?? '';
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if(!empty($prenom) && !empty($nom) && !empty($email) && !empty($password)) {
                if($this->userModel->register($prenom, $nom, $email, $password)) {
                    header("Location: index.php?page=login&success=Inscription réussie ! Connectez-vous.");
                    exit();
                } else {
                    $error = "Cet email est déjà utilisé ou erreur système.";
                }
            } else {
                $error = "Veuillez remplir tous les champs.";
            }
        }
        require_once 'views/front/register.php';
    }

    public function handleLogin() {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if(!empty($email) && !empty($password)) {
                $user = $this->userModel->login($email, $password);
                if($user) {
                    // Stockage des informations en session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'] ?? 'user';
                    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                    
                    // REDIRECTION SELON LE RÔLE
                    if($_SESSION['user_role'] === 'admin') {
                        header("Location: index.php?page=admin_dashboard");
                    } else {
                        header("Location: index.php?page=home&success=Bonjour " . $user['prenom']);
                    }
                    exit();
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            } else {
                $error = "Veuillez remplir tous les champs.";
            }
        }
        require_once 'views/front/login.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?page=home");
        exit();
    }
}
?>