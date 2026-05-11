<?php
require_once 'Database.php';

class NotificationModel {
    private $conn;
    private $table = 'notifications';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Récupère les notifications pour un utilisateur
     */
    public function getUserNotifications($userId, $limit = 10) {
        $query = "SELECT n.*, np.name as planning_name
                  FROM " . $this->table . " n
                  LEFT JOIN nutrition_plans np ON n.planning_id = np.id
                  WHERE n.user_id = :user_id
                  ORDER BY n.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les notifications non lues
     */
    public function getUnreadNotifications($userId) {
        $query = "SELECT n.*, np.name as planning_name
                  FROM " . $this->table . " n
                  LEFT JOIN nutrition_plans np ON n.planning_id = np.id
                  WHERE n.user_id = :user_id AND n.is_read = FALSE
                  ORDER BY n.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte les notifications non lues
     */
    public function countUnreadNotifications($userId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . 
                 " WHERE user_id = :user_id AND is_read = FALSE";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['count']);
    }

    /**
     * Crée une notification
     */
    public function createNotification($userId, $planningId, $title, $message = null, $type = 'info') {
        $query = "INSERT INTO " . $this->table . " (user_id, planning_id, title, message, type)
                  VALUES (:user_id, :planning_id, :title, :message, :type)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => $userId,
            ':planning_id' => $planningId,
            ':title' => $title,
            ':message' => $message,
            ':type' => $type
        ]);
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead($notificationId) {
        $query = "UPDATE " . $this->table . " 
                  SET is_read = TRUE, read_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $notificationId]);
    }

    /**
     * Marque toutes les notifications comme lues
     */
    public function markAllAsRead($userId) {
        $query = "UPDATE " . $this->table . " 
                  SET is_read = TRUE, read_at = NOW() 
                  WHERE user_id = :user_id AND is_read = FALSE";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Supprime une notification
     */
    public function deleteNotification($notificationId) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $notificationId]);
    }

    /**
     * Crée les notifications initiales pour un nouveau planning
     */
    public function createPlanningNotifications($userId, $planningId, $planningName, $startDate, $endDate) {
        // Notification de début
        $this->createNotification(
            $userId,
            $planningId,
            "Nouveau planning: $planningName",
            "Votre planning '$planningName' commence le " . date('d/m/Y', strtotime($startDate)),
            'info'
        );

        // Notification de fin (si dans 7 jours)
        $endTimestamp = strtotime($endDate);
        $nowTimestamp = time();
        $daysUntilEnd = ceil(($endTimestamp - $nowTimestamp) / (24 * 60 * 60));
        
        if($daysUntilEnd <= 7 && $daysUntilEnd > 0) {
            $this->createNotification(
                $userId,
                $planningId,
                "Planning se termine bientôt: $planningName",
                "Votre planning '$planningName' se termine le " . date('d/m/Y', strtotime($endDate)) . " ($daysUntilEnd jours restants)",
                'warning'
            );
        }
    }
}
?>
