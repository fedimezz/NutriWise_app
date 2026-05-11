<?php
require_once 'Database.php';

class ConsultationModel {
    private $conn;
    private $table = "consultations";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addConsultation($user_id, $suivi_id, $date_consultation, $remarque, $conseil, $poids_cible) {
        $query = "INSERT INTO " . $this->table . " (user_id, suivi_id, date_consultation, remarque, conseil, poids_cible)
                  VALUES (:user_id, :suivi_id, :date_consultation, :remarque, :conseil, :poids_cible)";
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute([
            ':user_id' => $user_id,
            ':suivi_id' => $suivi_id,
            ':date_consultation' => $date_consultation,
            ':remarque' => $remarque,
            ':conseil' => $conseil,
            ':poids_cible' => $poids_cible
        ])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function updateConsultation($id, $user_id, $suivi_id, $date_consultation, $remarque, $conseil, $poids_cible) {
        $query = "UPDATE " . $this->table . "
                  SET user_id = :user_id,
                      suivi_id = :suivi_id,
                      date_consultation = :date_consultation,
                      remarque = :remarque,
                      conseil = :conseil,
                      poids_cible = :poids_cible
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $user_id,
            ':suivi_id' => $suivi_id,
            ':date_consultation' => $date_consultation,
            ':remarque' => $remarque,
            ':conseil' => $conseil,
            ':poids_cible' => $poids_cible
        ]);
    }

    public function deleteConsultation($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function getConsultationById($id) {
        $query = "SELECT c.*, u.prenom, u.nom, u.email, s.date_suivi, s.poids AS poids_suivi
                  FROM " . $this->table . " c
                  INNER JOIN users u ON c.user_id = u.id
                  LEFT JOIN suivis s ON c.suivi_id = s.id
                  WHERE c.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getConsultationsByUser($user_id) {
        $query = "SELECT c.*, s.date_suivi, s.poids AS poids_suivi, s.etat_du_jour
                  FROM " . $this->table . " c
                  LEFT JOIN suivis s ON c.suivi_id = s.id
                  WHERE c.user_id = :user_id
                  ORDER BY c.date_consultation DESC, c.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllConsultations() {
        $query = "SELECT c.*, u.prenom, u.nom, u.email, s.date_suivi, s.poids AS poids_suivi
                  FROM " . $this->table . " c
                  INNER JOIN users u ON c.user_id = u.id
                  LEFT JOIN suivis s ON c.suivi_id = s.id
                  ORDER BY c.date_consultation DESC, c.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSuivisByUser($user_id) {
        $query = "SELECT id, date_suivi, poids, etat_du_jour
                  FROM suivis
                  WHERE user_id = :user_id
                  ORDER BY date_suivi DESC, id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSuivisWithUsers() {
        $query = "SELECT s.id, s.user_id, s.date_suivi, s.poids, u.prenom, u.nom, u.email
                  FROM suivis s
                  INNER JOIN users u ON s.user_id = u.id
                  ORDER BY s.date_suivi DESC, s.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countConsultations() {
        $query = "SELECT COUNT(*) AS total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>
