<?php
class NotificationController {
    private $model;
    private $userId;

    public function __construct($pdo) {
        session_start();
        $this->model = new Notification($pdo);
        $this->userId = $_SESSION['user_id'] ?? null;
    }

    // API for AJAX Polling
    public function getLatest() {
        if (!$this->userId) return;
        $data = $this->model->getUnread($this->userId);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function markAsRead() {
        if ($_POST['id']) {
            $this->model->markRead($_POST['id'], $this->userId);
            echo json_encode(['status' => 'success']);
        }
        exit;
    }
}