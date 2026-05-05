<?php
require_once 'models/ActivityLogModel.php';

class ActivityLogController {
    private $model;

    public function __construct() {
        global $pdo; // DB connection
        $this->model = new ActivityLogModel($pdo);
    }

    public function feed() {
        // ✅ Secure & cast inputs
        $user_id = isset($_GET['user_id']) && is_numeric($_GET['user_id'])
            ? (int)$_GET['user_id']
            : null;

        $action = isset($_GET['action'])
            ? trim($_GET['action'])
            : null;

        // ✅ Fix for your error (string - int)
        $page = isset($_GET['page']) && is_numeric($_GET['page'])
            ? (int)$_GET['page']
            : 1;

        // Prevent invalid page numbers
        $page = max(1, $page);

        $limit = 20;
        $offset = ($page - 1) * $limit;

        // ✅ Fetch logs
        $logs = $this->model->getLogs($user_id, $action, $limit, $offset);

        // ✅ Load view
        require_once 'views/back/activity_feed.php';
    }
}
?>