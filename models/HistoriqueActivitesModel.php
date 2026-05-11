<?php
// models/HistoriqueActivitesModel.php

require_once 'Database.php';

class HistoriqueActivitesModel {
    private $conn;
    private $table = "historique_activites";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all activities for a user, ordered by date (newest first)
     */
    public function getActivitiesByUser($user_id, $limit = null, $offset = 0) {
        $limitClause = $limit ? " LIMIT $limit OFFSET $offset" : "";

        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id = :user_id
                  ORDER BY date_activite DESC, created_at DESC" . $limitClause;

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get activities by type for a user
     */
    public function getActivitiesByType($user_id, $type, $limit = null) {
        $limitClause = $limit ? " LIMIT $limit" : "";

        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id = :user_id AND type_activite = :type
                  ORDER BY date_activite DESC, created_at DESC" . $limitClause;

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':type' => $type
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get activities within a date range
     */
    public function getActivitiesByDateRange($user_id, $start_date, $end_date) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE user_id = :user_id
                    AND date_activite BETWEEN :start_date AND :end_date
                  ORDER BY date_activite DESC, created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new suivi activity to history
     */
    public function addSuiviActivity($user_id, $suivi_id, $date_suivi, $poids, $eau_bue, $jour_reussi, $etat, $etat_du_jour = null) {
        $titre = "Suivi quotidien - " . $poids . " kg";
        $description = "État: " . $etat;
        if (!empty($etat_du_jour)) {
            $description .= " | Détails: " . $etat_du_jour;
        }
        $description .= " | Eau: " . $eau_bue . " L";
        $description .= $jour_reussi ? " | ✅ Objectif atteint" : " | ❌ Objectif non atteint";

        $query = "INSERT INTO " . $this->table . "
                  (user_id, type_activite, reference_id, date_activite, titre, description, statut, poids, eau_bue, jour_reussi)
                  VALUES (:user_id, 'suivi', :reference_id, :date_activite, :titre, :description, 'completed', :poids, :eau_bue, :jour_reussi)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':reference_id' => $suivi_id,
            ':date_activite' => $date_suivi,
            ':titre' => $titre,
            ':description' => $description,
            ':poids' => $poids,
            ':eau_bue' => $eau_bue,
            ':jour_reussi' => $jour_reussi
        ]);
    }

    /**
     * Add a new consultation activity to history
     */
    public function addConsultationActivity($user_id, $consultation_id, $date_consultation, $remarque, $conseil, $poids_cible = null) {
        $titre = "Consultation - " . date('d/m/Y', strtotime($date_consultation));
        $description = "Remarque: " . substr($remarque, 0, 100);
        if (strlen($remarque) > 100) {
            $description .= "...";
        }
        if ($poids_cible) {
            $description .= " | Poids cible: " . $poids_cible . " kg";
        }

        $statut = $date_consultation < date('Y-m-d') ? 'completed' :
                 ($date_consultation == date('Y-m-d') ? 'today' : 'scheduled');

        $query = "INSERT INTO " . $this->table . "
                  (user_id, type_activite, reference_id, date_activite, titre, description, statut, poids_cible, remarque, conseil)
                  VALUES (:user_id, 'consultation', :reference_id, :date_activite, :titre, :description, :statut, :poids_cible, :remarque, :conseil)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':reference_id' => $consultation_id,
            ':date_activite' => $date_consultation,
            ':titre' => $titre,
            ':description' => $description,
            ':statut' => $statut,
            ':poids_cible' => $poids_cible,
            ':remarque' => $remarque,
            ':conseil' => $conseil
        ]);
    }

    /**
     * Update activity status
     */
    public function updateActivityStatus($activity_id, $statut) {
        $query = "UPDATE " . $this->table . "
                  SET statut = :statut, updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $activity_id,
            ':statut' => $statut
        ]);
    }

    /**
     * Get activity statistics for a user
     */
    public function getActivityStats($user_id) {
        $query = "SELECT
                    COUNT(*) as total_activities,
                    SUM(CASE WHEN type_activite = 'suivi' THEN 1 ELSE 0 END) as total_suivis,
                    SUM(CASE WHEN type_activite = 'consultation' THEN 1 ELSE 0 END) as total_consultations,
                    SUM(CASE WHEN type_activite = 'suivi' AND jour_reussi = 1 THEN 1 ELSE 0 END) as objectifs_atteints,
                    AVG(CASE WHEN type_activite = 'suivi' THEN poids END) as poids_moyen,
                    SUM(CASE WHEN type_activite = 'suivi' THEN eau_bue END) as eau_totale
                  FROM " . $this->table . "
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete activity from history
     */
    public function deleteActivity($activity_id, $user_id) {
        $query = "DELETE FROM " . $this->table . "
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $activity_id,
            ':user_id' => $user_id
        ]);
    }
}
?>