<?php
// controllers/SuiviController.php
require_once 'models/SuiviModel.php';
require_once 'models/UserModel.php';

class SuiviController {
    private $suiviModel;
    private $userModel;

    public function __construct() {
        $this->suiviModel = new SuiviModel();
        $this->userModel = new UserModel();
        
        // Vérifier si l'utilisateur est connecté pour toutes les actions HTML de suivi
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login&error=Vous devez être connecté pour accéder au suivi");
            exit();
        }
    }

    private function requireAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: index.php?page=admin_dashboard");
            exit();
        }
    }

    public function index() {
        $user_id = $_SESSION['user_id'];
        $suivis = $this->suiviModel->getSuivisByUser($user_id);
        $user_profile = $this->userModel->getUserById($user_id);
        
        require_once 'views/front/suivi.php';
    }

    public function adminList() {
        $this->requireAdmin();

        $suivis = $this->suiviModel->getAllSuivisWithUsers();
        $usersList = $this->userModel->getAllUsers();
        $totalSuivis = count($suivis);
        $totalUsers = count($usersList);
        $averageCalories = $totalSuivis ? round(array_sum(array_column($suivis, 'calories_necessaires')) / $totalSuivis, 1) : 0;
        require_once 'views/back/suivis.php';
    }

    public function adminAdd() {
        $this->requireAdmin();
        $usersList = $this->userModel->getAllUsers();
        $users = $usersList;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id'] ?? 0);
            $date_suivi = $_POST['date_suivi'] ?? date('Y-m-d');
            $poids = $_POST['poids'] ?? 0;
            $calories_necessaires = $_POST['calories_necessaires'] ?? 0;
            $etat = $_POST['etat'] ?? 'Maintien';

            if ($user_id > 0 && $date_suivi && $poids > 0 && $calories_necessaires > 0) {
                $success = $this->suiviModel->addSuivi($user_id, $date_suivi, $poids, $calories_necessaires, $etat, 0, '', false);
                if ($success) {
                    // Send email to user
                    $user = $this->userModel->getUserById($user_id);
                    if ($user && !empty($user['email'])) {
                        $mailService = new MailService();
                        $mailService->sendSuiviEmail(
                            $user['email'],
                            $user['prenom'] . ' ' . $user['nom'],
                            [
                                'date' => $date_suivi,
                                'poids' => $poids,
                                'calories' => $calories_necessaires,
                                'etat' => $etat
                            ]
                        );
                    }
                    
                    header("Location: index.php?page=admin_suivis&success=Suivi ajouté avec succès");
                    exit();
                }
            }
            $error = "Veuillez remplir tous les champs obligatoires correctement.";
        }

        require_once 'views/back/add_suivi.php';
    }

    public function adminEdit() {
        $this->requireAdmin();
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?page=admin_suivis");
            exit();
        }

        $suivi = $this->suiviModel->getSuiviById($id);
        if (!$suivi) {
            header("Location: index.php?page=admin_suivis&error=Suivi introuvable");
            exit();
        }

        $usersList = $this->userModel->getAllUsers();
        $users = $usersList;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id'] ?? 0);
            $date_suivi = $_POST['date_suivi'] ?? date('Y-m-d');
            $poids = $_POST['poids'] ?? 0;
            $calories_necessaires = $_POST['calories_necessaires'] ?? 0;
            $etat = $_POST['etat'] ?? 'Maintien';

            if ($user_id > 0 && $date_suivi && $poids > 0 && $calories_necessaires > 0) {
                $this->suiviModel->updateSuivi($id, $user_id, $date_suivi, $poids, $calories_necessaires, $etat);
                header("Location: index.php?page=admin_suivis&success=Suivi modifié avec succès");
                exit();
            }
            $error = "Veuillez remplir tous les champs obligatoires correctement.";
            $suivi = array_merge($suivi, $_POST);
        }

        require_once 'views/back/edit_suivi.php';
    }

    public function adminDelete() {
        $this->requireAdmin();
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->suiviModel->deleteSuivi($id);
        }
        header("Location: index.php?page=admin_suivis&success=Suivi supprimé avec succès");
        exit();
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $date_suivi = $_POST['date_suivi'] ?? date('Y-m-d');
            $poids = $_POST['poids'] ?? 0;
            $calories_necessaires = $_POST['calories_necessaires'] ?? 2000;
            $etat = $_POST['etat'] ?? 'actif'; // Champ etat générique (existant)
            $eau_bue = $_POST['eau_bue_du_jour'] ?? 0;
            $etat_du_jour = $_POST['etat_du_jour'] ?? '';
            $jour_reussi = isset($_POST['jour_reussi']) ? true : false;

            $suivi_id = $this->suiviModel->addSuivi($user_id, $date_suivi, $poids, $calories_necessaires, $etat, $eau_bue, $etat_du_jour, $jour_reussi);

            if ($suivi_id) {
                // Add to activity history
                require_once 'controllers/HistoriqueActivitesController.php';
                HistoriqueActivitesController::addSuiviActivity($user_id, $suivi_id, $date_suivi, $poids, $eau_bue, $jour_reussi, $etat, $etat_du_jour);

                // Send email summary
                $user = $this->userModel->getUserById($user_id);
                if ($user && !empty($user['email'])) {
                    require_once 'models/MailService.php';
                    $mailService = new MailService();
                    $mailService->sendSuiviEmail(
                        $user['email'],
                        $user['prenom'] . ' ' . $user['nom'],
                        [
                            'date' => $date_suivi,
                            'poids' => $poids,
                            'calories' => $calories_necessaires,
                            'etat' => $etat,
                            'eau_bue' => $eau_bue,
                            'etat_du_jour' => $etat_du_jour,
                            'jour_reussi' => $jour_reussi
                        ]
                    );
                }

                header("Location: index.php?page=suivi&success=Suivi ajouté avec succès");
            } else {
                header("Location: index.php?page=suivi&error=Erreur lors de l'ajout du suivi");
            }
            exit();
        }
    }
}
?>
