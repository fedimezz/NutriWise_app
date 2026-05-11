<?php
// controllers/NotificationController.php

require_once 'models/NotificationModel.php';

class NotificationController
{
    private NotificationModel $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    /** GET index.php?page=notifications — liste des notifications */
    public function index(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit();
        }

        $userId        = (int) $_SESSION['user_id'];
        $notifications = $this->model->getByUser($userId);
        $preferences   = $this->model->getPreferences($userId);

        require_once 'views/front/notifications.php';
    }

    /** GET index.php?page=notification_read&id=X */
    public function markRead(): void
    {
        if (!isset($_SESSION['user_id'])) { exit(); }
        $userId = (int) $_SESSION['user_id'];
        $id     = (int) ($_GET['id'] ?? 0);

        $this->model->markRead($id, $userId);
        header("Location: index.php?page=notifications&success=Notification+marquée+comme+lue");
        exit();
    }

    /** GET index.php?page=notifications_read_all */
    public function markAllRead(): void
    {
        if (!isset($_SESSION['user_id'])) { exit(); }
        $this->model->markAllRead((int) $_SESSION['user_id']);
        header("Location: index.php?page=notifications&success=Toutes+les+notifications+ont+été+lues");
        exit();
    }

    /** GET index.php?page=notification_delete&id=X */
    public function delete(): void
    {
        if (!isset($_SESSION['user_id'])) { exit(); }
        $userId = (int) $_SESSION['user_id'];
        $id     = (int) ($_GET['id'] ?? 0);

        $this->model->delete($id, $userId);
        header("Location: index.php?page=notifications&success=Notification+supprimée");
        exit();
    }

    /** POST index.php?page=save_notif_preferences */
    public function savePreferences(): void
    {
        if (!isset($_SESSION['user_id'])) { exit(); }
        $userId = (int) $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->savePreferences($userId, [
                'notif_rappel_suivi'    => isset($_POST['notif_rappel_suivi'])  ? 1 : 0,
                'notif_eau'             => isset($_POST['notif_eau'])            ? 1 : 0,
                'notif_objectif'        => isset($_POST['notif_objectif'])       ? 1 : 0,
                'notif_poids_alerte'    => isset($_POST['notif_poids_alerte'])   ? 1 : 0,
                'notif_encouragement'   => isset($_POST['notif_encouragement'])  ? 1 : 0,
                'notif_consultation'    => isset($_POST['notif_consultation'])   ? 1 : 0,
                'heure_rappel_suivi'    => $_POST['heure_rappel_suivi'] ?? '20:00',
            ]);
            header("Location: index.php?page=notifications&success=Préférences+enregistrées");
            exit();
        }

        header("Location: index.php?page=notifications");
        exit();
    }

    /** Retourne le nombre de notifications non lues (pour la navbar) */
    public static function unreadCount(): int
    {
        if (!isset($_SESSION['user_id'])) { return 0; }
        $model = new NotificationModel();
        return $model->countUnread((int) $_SESSION['user_id']);
    }
}