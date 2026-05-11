<?php
// models/SuiviModel.php
require_once 'Database.php';

class SuiviModel {
    private $conn;
    private $table = "suivis";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Ajouter un suivi journalier
    public function addSuivi($user_id, $date_suivi, $poids, $calories_necessaires, $etat, $eau_bue, $etat_du_jour, $jour_reussi) {
        $query = "INSERT INTO " . $this->table . "
                  (user_id, date_suivi, poids, calories_necessaires, etat, eau_bue_du_jour, etat_du_jour, jour_reussi)
                  VALUES (:user_id, :date_suivi, :poids, :calories_necessaires, :etat, :eau_bue, :etat_du_jour, :jour_reussi)";
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute([
            ':user_id' => $user_id,
            ':date_suivi' => $date_suivi,
            ':poids' => $poids,
            ':calories_necessaires' => $calories_necessaires,
            ':etat' => $etat,
            ':eau_bue' => $eau_bue,
            ':etat_du_jour' => $etat_du_jour,
            ':jour_reussi' => $jour_reussi ? 1 : 0
        ])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Récupérer les suivis d'un utilisateur
    public function getSuivisByUser($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY date_suivi DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les suivis avec les informations utilisateur
    public function getAllSuivisWithUsers() {
        $query = "SELECT s.*, u.prenom, u.nom, u.email
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.user_id = u.id
                  ORDER BY u.nom ASC, u.prenom ASC, s.date_suivi DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSuiviById($id) {
        $query = "SELECT s.*, u.prenom, u.nom, u.email
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.user_id = u.id
                  WHERE s.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSuivi($id, $user_id, $date_suivi, $poids, $calories_necessaires, $etat) {
        $query = "UPDATE " . $this->table . "
                  SET user_id = :user_id,
                      date_suivi = :date_suivi,
                      poids = :poids,
                      calories_necessaires = :calories_necessaires,
                      etat = :etat
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $user_id,
            ':date_suivi' => $date_suivi,
            ':poids' => $poids,
            ':calories_necessaires' => $calories_necessaires,
            ':etat' => $etat
        ]);
    }

    public function deleteSuivi($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function countSuivis() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>

