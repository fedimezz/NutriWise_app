<?php
// controllers/UserController.php
require_once 'models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function profile() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login&error=Veuillez vous connecter pour accéder à votre profil");
            exit();
        }

        $userId = $_SESSION['user_id'];

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $prenom = $_POST['prenom'] ?? '';
            $nom = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $telephone = $_POST['telephone'] ?? '';
            $taille = $_POST['taille'] ?? null;
            $poids = $_POST['poids'] ?? null;
            $objectif = $_POST['objectif'] ?? '';

            if($this->userModel->updateProfile($userId, $prenom, $nom, $email, $telephone, $taille, $poids, $objectif)) {
                // Mettre à jour le nom en session
                $_SESSION['user_name'] = $prenom . ' ' . $nom;
                $success = "Profil mis à jour avec succès !";
            } else {
                $error = "Erreur lors de la mise à jour.";
            }
        }

        $userData = $this->userModel->getUserById($userId);
        require_once 'views/front/profile.php';
    }
}
?>