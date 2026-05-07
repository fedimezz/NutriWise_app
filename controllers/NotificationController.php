<?php
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $model;
    private $userId;
    private $pdo;

    public function __construct($pdo) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->pdo = $pdo;
        $this->model = new Notification($pdo);
        $this->userId = $_SESSION['user_id'] ?? null;
    }

    // API for AJAX Polling
    public function getLatest() {
        if (!$this->userId) {
            echo json_encode([]);
            exit;
        }

        $data = $this->model->getUnread($this->userId);

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function markAsRead() {
        if (isset($_POST['id'])) {
            $this->model->markRead($_POST['id'], $this->userId);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
        exit;
    }

    // NOUVELLE MÉTHODE : Créer une notification pour un utilisateur
    public function createNotification($userId, $message, $type = 'info') {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notifications (user_id, type, message, is_read, created_at) 
                VALUES (?, ?, ?, 0, NOW())
            ");
            return $stmt->execute([$userId, $type, $message]);
        } catch (PDOException $e) {
            error_log("Erreur création notification: " . $e->getMessage());
            return false;
        }
    }

    // NOUVELLE MÉTHODE : Notifier tous les utilisateurs
    public function notifyAllUsers($message, $type = 'info', $excludeUserId = null) {
        try {
            if ($excludeUserId) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO notifications (user_id, type, message, is_read, created_at) 
                    SELECT id, ?, ?, 0, NOW() FROM users WHERE id != ?
                ");
                return $stmt->execute([$type, $message, $excludeUserId]);
            } else {
                $stmt = $this->pdo->prepare("
                    INSERT INTO notifications (user_id, type, message, is_read, created_at) 
                    SELECT id, ?, ?, 0, NOW() FROM users
                ");
                return $stmt->execute([$type, $message]);
            }
        } catch (PDOException $e) {
            error_log("Erreur notification tous: " . $e->getMessage());
            return false;
        }
    }
}