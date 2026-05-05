<?php
class Notification {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Create a specific type of notification
    public function create($userId, $type, $message) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $type, $message]);
    }

    // Fetch unread for the current user
    public function getUnread($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mark single as read
    public function markRead($id, $userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    // Mark all as read
    public function markAllRead($userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    // Delete a notification
    public function delete($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}