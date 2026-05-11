<?php
// controllers/HistoriqueActivitesController.php

require_once 'models/HistoriqueActivitesModel.php';

class HistoriqueActivitesController {
    private $model;

    public function __construct() {
        $this->model = new HistoriqueActivitesModel();
    }

    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }
    }

    /**
     * Display activity history page
     */
    public function index() {
        $this->requireLogin();

        $user_id = $_SESSION['user_id'];
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;

        // Get activities with pagination
        $activities = $this->model->getActivitiesByUser($user_id, $per_page, $offset);

        // Get statistics
        $stats = $this->model->getActivityStats($user_id);

        // Get total count for pagination
        $total_activities = $this->model->getActivitiesByUser($user_id);
        $total_pages = ceil(count($total_activities) / $per_page);

        require_once 'views/front/historique_activites.php';
    }

    /**
     * Get activities by type (AJAX endpoint)
     */
    public function getByType() {
        $this->requireLogin();

        $type = $_GET['type'] ?? 'all';
        $user_id = $_SESSION['user_id'];

        if ($type === 'all') {
            $activities = $this->model->getActivitiesByUser($user_id);
        } else {
            $activities = $this->model->getActivitiesByType($user_id, $type);
        }

        header('Content-Type: application/json');
        echo json_encode($activities);
        exit();
    }

    /**
     * Get activities by date range (AJAX endpoint)
     */
    public function getByDateRange() {
        $this->requireLogin();

        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        $user_id = $_SESSION['user_id'];

        $activities = $this->model->getActivitiesByDateRange($user_id, $start_date, $end_date);

        header('Content-Type: application/json');
        echo json_encode($activities);
        exit();
    }

    /**
     * Add activity to history (called automatically by other controllers)
     */
    public static function addSuiviActivity($user_id, $suivi_id, $date_suivi, $poids, $eau_bue, $jour_reussi, $etat, $etat_du_jour = null) {
        $model = new HistoriqueActivitesModel();
        return $model->addSuiviActivity($user_id, $suivi_id, $date_suivi, $poids, $eau_bue, $jour_reussi, $etat, $etat_du_jour);
    }

    /**
     * Add consultation activity to history (called automatically by other controllers)
     */
    public static function addConsultationActivity($user_id, $consultation_id, $date_consultation, $remarque, $conseil, $poids_cible = null) {
        $model = new HistoriqueActivitesModel();
        return $model->addConsultationActivity($user_id, $consultation_id, $date_consultation, $remarque, $conseil, $poids_cible);
    }
}
?>