<?php
// controllers/UserController.php
require_once 'models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function profile() {
        require_login();
        
        $userId = current_user_id();

        // Traitement du formulaire
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            csrf_check();
            $action = $_POST['action'] ?? '';

            // Upload de la photo
            if($action === 'upload_image') {
                if(!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== 0) {
                    $_SESSION['error'] = "Veuillez sélectionner une image valide.";
                    redirect("index.php?page=profile");
                }
                $uploadResult = $this->uploadImage($_FILES['profile_image'], $userId);
                if($uploadResult['success']) {
                    $this->userModel->updateProfileImage($userId, $uploadResult['filename']);
                    $_SESSION['success'] = "Photo de profil mise à jour !";
                    $_SESSION['user_image'] = $uploadResult['filename'];
                } else {
                    $_SESSION['error'] = $uploadResult['error'];
                }
                redirect("index.php?page=profile");
            }

            if($action === 'update_profile') {
                // Mise à jour des informations
                $prenom = trim($_POST['prenom'] ?? '');
                $nom = trim($_POST['nom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $telephone = trim($_POST['telephone'] ?? '');
                $taille = ($_POST['taille'] ?? '') !== '' ? (int)$_POST['taille'] : null;
                $poids = ($_POST['poids'] ?? '') !== '' ? (float)$_POST['poids'] : null;
                $objectif = $_POST['objectif'] ?? 'Maintien';
                $age = ($_POST['age'] ?? '') !== '' ? (int)$_POST['age'] : null;
                $gender = $_POST['gender'] ?? 'male';
                $activity_level = $_POST['activity_level'] ?? 'moderate';
                $allergies = trim($_POST['allergies'] ?? '');
                $allergies = $allergies !== '' ? $allergies : null;

                if($prenom === '' || $nom === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['error'] = "Veuillez vérifier prénom/nom/email.";
                    redirect("index.php?page=profile");
                }

                if($this->userModel->updateProfile($userId, $prenom, $nom, $email, $telephone, $taille, $poids, $objectif, $age, $gender, $activity_level, $allergies)) {
                    $_SESSION['user_name'] = $prenom . ' ' . $nom;
                    $_SESSION['success'] = "Profil mis à jour avec succès !";
                } else {
                    $_SESSION['error'] = "Erreur lors de la mise à jour.";
                }

                redirect("index.php?page=profile");
            }

            // Unknown POST action
            redirect("index.php?page=profile");
        }

        $userData = $this->userModel->getUserById($userId);
        require_once 'views/front/profile.php';
    }

    public function changePassword(): void
    {
        require_login();
        $userId = current_user_id() ?? 0;
        if ($userId <= 0) redirect("index.php?page=login");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $current = (string)($_POST['current_password'] ?? '');
            $new = (string)($_POST['new_password'] ?? '');
            $confirm = (string)($_POST['confirm_password'] ?? '');

            if (strlen($new) < 8) {
                $_SESSION['error'] = "Nouveau mot de passe: minimum 8 caractères.";
                redirect("index.php?page=change_password");
            }
            if ($new !== $confirm) {
                $_SESSION['error'] = "Confirmation incorrecte.";
                redirect("index.php?page=change_password");
            }

            // Verify current password using login() without creating session again
            $email = (string)($_SESSION['user_email'] ?? '');
            if ($email === '' || !$this->userModel->login($email, $current)) {
                $_SESSION['error'] = "Mot de passe actuel incorrect.";
                redirect("index.php?page=change_password");
            }

            if ($this->userModel->updateUserPassword($userId, $new)) {
                $this->userModel->setMustChangePassword($userId, false);
                unset($_SESSION['must_change_password']);
                $_SESSION['success'] = "Mot de passe mis à jour.";
                redirect("index.php?page=profile");
            }

            $_SESSION['error'] = "Erreur lors de la mise à jour.";
            redirect("index.php?page=change_password");
        }

        require_once 'views/front/change_password.php';
    }

    public function recettes() {
        require_login();
        $page = 'recettes';

        $userId = current_user_id();
        $role = current_user_role();

        // Handle create/update/delete via POST/GET
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $action = $_POST['action'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $calories = ($_POST['calories'] ?? '') !== '' ? (int)$_POST['calories'] : null;
            $protein_g = ($_POST['protein_g'] ?? '') !== '' ? (float)$_POST['protein_g'] : null;
            $carbs_g = ($_POST['carbs_g'] ?? '') !== '' ? (float)$_POST['carbs_g'] : null;
            $fat_g = ($_POST['fat_g'] ?? '') !== '' ? (float)$_POST['fat_g'] : null;

            if ($title === '') {
                $_SESSION['error'] = "Titre requis.";
                redirect("index.php?page=recettes");
            }

            $data = [
                'title' => $title,
                'description' => $description !== '' ? $description : null,
                'calories' => $calories,
                'protein_g' => $protein_g,
                'carbs_g' => $carbs_g,
                'fat_g' => $fat_g,
            ];

            if ($action === 'create_recipe') {
                if ($this->userModel->createRecipe($userId, $data)) {
                    $_SESSION['success'] = "Recette créée avec succès.";
                } else {
                    $_SESSION['error'] = "Erreur lors de la création.";
                }
                redirect("index.php?page=recettes");
            }

            if ($action === 'update_recipe') {
                $recipeId = (int)($_POST['recipe_id'] ?? 0);
                if ($recipeId <= 0) redirect("index.php?page=recettes");
                if ($this->userModel->updateRecipe($recipeId, $userId, $role, $data)) {
                    $_SESSION['success'] = "Recette modifiée avec succès.";
                } else {
                    $_SESSION['error'] = "Modification non autorisée ou erreur.";
                }
                redirect("index.php?page=recettes");
            }

            redirect("index.php?page=recettes");
        }

        if (($_GET['action'] ?? '') === 'delete_recipe') {
            $recipeId = (int)($_GET['id'] ?? 0);
            if ($recipeId > 0) {
                if ($this->userModel->deleteRecipe($recipeId, $userId, $role)) {
                    $_SESSION['success'] = "Recette supprimée.";
                } else {
                    $_SESSION['error'] = "Suppression non autorisée ou erreur.";
                }
            }
            redirect("index.php?page=recettes");
        }

        $search = trim($_GET['q'] ?? '');
        $recipes = $this->userModel->getRecipes($search !== '' ? $search : null);
        $editRecipe = null;
        if (($_GET['action'] ?? '') === 'edit_recipe') {
            $rid = (int)($_GET['id'] ?? 0);
            if ($rid > 0) $editRecipe = $this->userModel->getRecipeById($rid);
        }

        require_once 'views/front/recettes.php';
    }

    public function suivi() {
        require_login();
        $page = 'suivi';

        $userId = current_user_id();
        $userData = $this->userModel->getUserById($userId);

        $today = date('Y-m-d');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $action = $_POST['action'] ?? '';
            if ($action === 'save_daily_log') {
                $day = $_POST['day'] ?? $today;
                $weight = ($_POST['weight_kg'] ?? '') !== '' ? (float)$_POST['weight_kg'] : null;
                $cal = (int)($_POST['calories_consumed'] ?? 0);
                $p = (float)($_POST['protein_g'] ?? 0);
                $c = (float)($_POST['carbs_g'] ?? 0);
                $f = (float)($_POST['fat_g'] ?? 0);
                $notes = trim($_POST['notes'] ?? '');
                $notes = $notes !== '' ? $notes : null;

                if ($cal < 0) $cal = 0;
                if ($p < 0) $p = 0;
                if ($c < 0) $c = 0;
                if ($f < 0) $f = 0;

                if ($this->userModel->upsertDailyLog($userId, $day, $weight, $cal, $p, $c, $f, $notes)) {
                    $_SESSION['success'] = "Suivi enregistré.";
                } else {
                    $_SESSION['error'] = "Erreur lors de l'enregistrement.";
                }
                redirect("index.php?page=suivi");
            }
        }

        $todayLog = $this->userModel->getDailyLog($userId, $today);
        $start = date('Y-m-d', strtotime('-6 days'));
        $history = $this->userModel->getDailyLogRange($userId, $start, $today);

        // Recommendations (simple rule-based MVP)
        $recommendations = [];
        $dailyTarget = (int)($userData['daily_calories_needs'] ?? 2000);
        $goal = (string)($userData['objectif'] ?? 'Maintien');
        if ($goal === 'Perte de poids') { $pPct = 0.30; $cPct = 0.40; $fPct = 0.30; }
        elseif ($goal === 'Prise de muscle') { $pPct = 0.30; $cPct = 0.50; $fPct = 0.20; }
        else { $pPct = 0.25; $cPct = 0.50; $fPct = 0.25; }
        $pTarget = (float)round(($dailyTarget * $pPct) / 4, 1);
        $cTarget = (float)round(($dailyTarget * $cPct) / 4, 1);
        $fTarget = (float)round(($dailyTarget * $fPct) / 9, 1);

        $consumed = (int)($todayLog['calories_consumed'] ?? 0);
        if ($consumed > (int)round($dailyTarget * 1.10)) {
            $recommendations[] = "Vous êtes au-dessus de votre objectif calorique du jour. Pensez à équilibrer avec un repas léger riche en protéines et légumes.";
        } elseif ($consumed < (int)round($dailyTarget * 0.60)) {
            $recommendations[] = "Vous êtes encore loin de votre objectif calorique. Ajoutez une collation saine (yaourt, fruits, noix) pour éviter les fringales.";
        }

        $pNow = (float)($todayLog['protein_g'] ?? 0);
        $cNow = (float)($todayLog['carbs_g'] ?? 0);
        $fNow = (float)($todayLog['fat_g'] ?? 0);
        if ($pNow < $pTarget * 0.7) {
            $recommendations[] = "Protéines un peu basses aujourd’hui. Ajoutez une source protéinée (œufs, poulet, légumineuses, fromage blanc).";
        }
        if ($fNow > $fTarget * 1.3) {
            $recommendations[] = "Lipides un peu élevés. Favorisez des cuissons plus légères et des portions d’huiles maîtrisées.";
        }
        if (!empty($userData['allergies'])) {
            $recommendations[] = "Allergies enregistrées : " . (string)$userData['allergies'] . ". Nous éviterons ces ingrédients dans les suggestions.";
        }

        require_once 'views/front/suivi.php';
    }

    private function uploadImage($file, $userId){
        $targetDirWeb = "../views/assets/uploads/";
        $targetDirFs = __DIR__ . "/../views/assets/uploads/";

        // Créer le dossier s'il n'existe pas
        if (!is_dir($targetDirFs)) {
            if (!mkdir($targetDirFs, 0777, true) && !is_dir($targetDirFs)) {
                return ['success' => false, 'error' => "Impossible de créer le dossier d'upload."];
            }
        }

        // Validate real image (MIME + content)
        $tmp = $file['tmp_name'] ?? '';
        if (!is_string($tmp) || $tmp === '' || !is_uploaded_file($tmp)) {
            return ['success' => false, 'error' => "Upload invalide."];
        }

        $imgInfo = @getimagesize($tmp);
        if ($imgInfo === false) {
            return ['success' => false, 'error' => "Le fichier n'est pas une image valide."];
        }

        $mime = $imgInfo['mime'] ?? '';
        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        if (!isset($allowedMimes[$mime])) {
            return ['success' => false, 'error' => "Format non autorisé (JPG, PNG, GIF, WEBP)."];
        }
        
        $extension = $allowedMimes[$mime];
        
        if ($file['size'] > 5000000) {
            return ['success' => false, 'error' => "Fichier trop volumineux (max 5MB)"];
        }
        
        $newName = "user_" . $userId . "_" . time() . "." . $extension;
        $targetFileFs = $targetDirFs . $newName;
        
        if (move_uploaded_file($tmp, $targetFileFs)) {
            return ['success' => true, 'filename' => $newName];
        }
        
        return ['success' => false, 'error' => "Erreur lors de l'upload"];
    }
}
?>