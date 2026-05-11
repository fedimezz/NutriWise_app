<?php
require_once 'models/ConsultationModel.php';
require_once 'models/UserModel.php';

class ConsultationController {
    private $consultationModel;
    private $userModel;

    public function __construct() {
        $this->consultationModel = new ConsultationModel();
        $this->userModel = new UserModel();
    }

    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login&error=Vous devez etre connecte pour acceder aux consultations");
            exit();
        }
    }

    private function requireAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: index.php?page=home");
            exit();
        }
    }

    public function index() {
        $this->requireLogin();

        $user_id = $_SESSION['user_id'];
        $consultations = $this->consultationModel->getConsultationsByUser($user_id);
        $suivis = $this->consultationModel->getSuivisByUser($user_id);

        require_once 'views/front/consultations.php';
    }

    public function add() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?page=consultations");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $suivi_id = intval($_POST['suivi_id'] ?? 0);
        $date_consultation = $_POST['date_consultation'] ?? date('Y-m-d');
        $remarque = trim($_POST['remarque'] ?? '');
        $conseil = trim($_POST['conseil'] ?? '');
        $poids_cible = ($_POST['poids_cible'] ?? '') !== '' ? floatval($_POST['poids_cible']) : null;

        if ($suivi_id <= 0 || $remarque === '' || $conseil === '') {
            header("Location: index.php?page=consultations&error=Veuillez remplir tous les champs obligatoires");
            exit();
        }

        $consultation_id = $this->consultationModel->addConsultation($user_id, $suivi_id, $date_consultation, $remarque, $conseil, $poids_cible);

        if ($consultation_id) {
            // Add to activity history
            require_once 'controllers/HistoriqueActivitesController.php';
            HistoriqueActivitesController::addConsultationActivity($user_id, $consultation_id, $date_consultation, $remarque, $conseil, $poids_cible);

            $message = 'Consultation ajoutee avec succes';
            header("Location: index.php?page=consultations&success=" . urlencode($message));
        } else {
            $message = 'Erreur lors de l ajout de la consultation';
            header("Location: index.php?page=consultations&error=" . urlencode($message));
        }
        exit();
    }

    public function delete() {
        $this->requireLogin();

        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?page=consultations");
            exit();
        }

        $consultation = $this->consultationModel->getConsultationById($id);
        if (!$consultation || intval($consultation['user_id']) !== intval($_SESSION['user_id'])) {
            header("Location: index.php?page=consultations&error=Consultation introuvable");
            exit();
        }

        $this->consultationModel->deleteConsultation($id);
        header("Location: index.php?page=consultations&success=Consultation supprimee avec succes");
        exit();
    }

    public function edit() {
        $this->requireLogin();

        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?page=consultations");
            exit();
        }

        $consultation = $this->consultationModel->getConsultationById($id);
        if (!$consultation || intval($consultation['user_id']) !== intval($_SESSION['user_id'])) {
            header("Location: index.php?page=consultations&error=Consultation introuvable");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $suivi_id = intval($_POST['suivi_id'] ?? 0);
            $date_consultation = $_POST['date_consultation'] ?? date('Y-m-d');
            $remarque = trim($_POST['remarque'] ?? '');
            $conseil = trim($_POST['conseil'] ?? '');
            $poids_cible = ($_POST['poids_cible'] ?? '') !== '' ? floatval($_POST['poids_cible']) : null;

            if ($suivi_id > 0 && $remarque !== '' && $conseil !== '') {
                $this->consultationModel->updateConsultation($id, $user_id, $suivi_id, $date_consultation, $remarque, $conseil, $poids_cible);
                header("Location: index.php?page=consultations&success=Consultation modifiee avec succes");
                exit();
            }

            $error = "Veuillez remplir tous les champs obligatoires.";
            $consultation = array_merge($consultation, $_POST);
        }

        $suivis = $this->consultationModel->getSuivisByUser($_SESSION['user_id']);
        require_once 'views/front/edit_consultation.php';
    }

    public function adminList() {
        $this->requireAdmin();
        $consultations = $this->consultationModel->getAllConsultations();
        $usersList = $this->userModel->getAllUsers();
        $totalConsultations = count($consultations);
        $totalUsers = count($usersList);
        $totalLinkedSuivis = 0;
        foreach ($consultations as $consultation) {
            if (!empty($consultation['date_suivi'])) {
                $totalLinkedSuivis++;
            }
        }
        require_once 'views/back/consultations.php';
    }

    public function adminAdd() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id'] ?? 0);
            $suivi_id = intval($_POST['suivi_id'] ?? 0);
            $date_consultation = $_POST['date_consultation'] ?? date('Y-m-d');
            $remarque = trim($_POST['remarque'] ?? '');
            $conseil = trim($_POST['conseil'] ?? '');
            $poids_cible = ($_POST['poids_cible'] ?? '') !== '' ? floatval($_POST['poids_cible']) : null;

            if ($user_id > 0 && $suivi_id > 0 && $remarque !== '' && $conseil !== '') {
                $this->consultationModel->addConsultation($user_id, $suivi_id, $date_consultation, $remarque, $conseil, $poids_cible);
                
                // Send email to user
                $user = $this->userModel->getUserById($user_id);
                if ($user && !empty($user['email'])) {
                    $mailService = new MailService();
                    $mailService->sendConsultationEmail(
                        $user['email'],
                        $user['prenom'] . ' ' . $user['nom'],
                        [
                            'date' => $date_consultation,
                            'remarque' => $remarque,
                            'conseil' => $conseil,
                            'poids_cible' => $poids_cible
                        ]
                    );
                }
                
                header("Location: index.php?page=admin_consultations&success=Consultation ajoutee avec succes");
                exit();
            }

            $error = "Veuillez remplir tous les champs obligatoires.";
        }

        $users = $this->userModel->getAllUsers();
        $suivis = $this->consultationModel->getAllSuivisWithUsers();
        require_once 'views/back/add_consultation.php';
    }

    public function adminEdit() {
        $this->requireAdmin();

        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: index.php?page=admin_consultations");
            exit();
        }

        $consultation = $this->consultationModel->getConsultationById($id);
        if (!$consultation) {
            header("Location: index.php?page=admin_consultations&error=Consultation introuvable");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id'] ?? 0);
            $suivi_id = intval($_POST['suivi_id'] ?? 0);
            $date_consultation = $_POST['date_consultation'] ?? date('Y-m-d');
            $remarque = trim($_POST['remarque'] ?? '');
            $conseil = trim($_POST['conseil'] ?? '');
            $poids_cible = ($_POST['poids_cible'] ?? '') !== '' ? floatval($_POST['poids_cible']) : null;

            if ($user_id > 0 && $suivi_id > 0 && $remarque !== '' && $conseil !== '') {
                $this->consultationModel->updateConsultation($id, $user_id, $suivi_id, $date_consultation, $remarque, $conseil, $poids_cible);
                header("Location: index.php?page=admin_consultations&success=Consultation modifiee avec succes");
                exit();
            }

            $error = "Veuillez remplir tous les champs obligatoires.";
            $consultation = array_merge($consultation, $_POST);
        }

        $users = $this->userModel->getAllUsers();
        $suivis = $this->consultationModel->getAllSuivisWithUsers();
        require_once 'views/back/edit_consultation.php';
    }

    public function adminDelete() {
        $this->requireAdmin();

        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->consultationModel->deleteConsultation($id);
        }

        header("Location: index.php?page=admin_consultations&success=Consultation supprimee avec succes");
        exit();
    }
}
?>
