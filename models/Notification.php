<?php
class Notification {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUnread($userId) {
        $stmt = $this->pdo->prepare("
            SELECT id, message, type, created_at 
            FROM notifications 
            WHERE user_id = ? AND is_read = 0 
            ORDER BY created_at DESC
            LIMIT 20
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll($userId, $limit = 50) {
        $stmt = $this->pdo->prepare("
            SELECT id, message, type, is_read, created_at 
            FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markRead($id, $userId) {
        $stmt = $this->pdo->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$id, $userId]);
    }

    public function markAllRead($userId) {
        $stmt = $this->pdo->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE user_id = ? AND is_read = 0
        ");
        return $stmt->execute([$userId]);
    }

    public function getUnreadCount($userId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}