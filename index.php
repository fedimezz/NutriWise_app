<?php
session_start();

require_once 'controllers/AuthController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/AlimentController.php';
require_once 'controllers/ConsultationController.php';
require_once 'controllers/NotificationController.php';
require_once 'controllers/HistoriqueActivitesController.php';
require_once 'models/MailService.php';
require_once 'models/HistoriqueActivitesModel.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'home':
        require_once 'views/front/index.php';
        break;

    case 'login':
        $auth = new AuthController();
        $auth->handleLogin();
        break;

    case 'register':
        $auth = new AuthController();
        $auth->handleRegister();
        break;

    case 'logout':
        $auth = new AuthController();
        $auth->logout();
        break;

    case 'profile':
        $user = new UserController();
        $user->profile();
        break;

    case 'aliments':
        $alimentController = new AlimentController();
        $alimentController->frontList();
        break;

    case 'aliment_details':
        $alimentController = new AlimentController();
        $alimentController->details();
        break;

    case 'suivi':
        require_once 'controllers/SuiviController.php';
        $suiviController = new SuiviController();
        $suiviController->index();
        break;

    case 'add_suivi':
        require_once 'controllers/SuiviController.php';
        $suiviController = new SuiviController();
        $suiviController->add();
        break;

    case 'consultations':
        $consultationController = new ConsultationController();
        $consultationController->index();
        break;

    case 'add_consultation':
        $consultationController = new ConsultationController();
        $consultationController->add();
        break;

    case 'delete_consultation':
        $consultationController = new ConsultationController();
        $consultationController->delete();
        break;

    case 'edit_consultation':
        $consultationController = new ConsultationController();
        $consultationController->edit();
        break;

    case 'historique_activites':
        $historiqueController = new HistoriqueActivitesController();
        $historiqueController->index();
        break;

    case 'get_activities_by_type':
        $historiqueController = new HistoriqueActivitesController();
        $historiqueController->getByType();
        break;

    case 'get_activities_by_date':
        $historiqueController = new HistoriqueActivitesController();
        $historiqueController->getByDateRange();
        break;

    case 'notifications':
        $notificationController = new NotificationController();
        $notificationController->index();
        break;

    case 'notification_read':
        $notificationController = new NotificationController();
        $notificationController->markRead();
        break;

    case 'notifications_read_all':
        $notificationController = new NotificationController();
        $notificationController->markAllRead();
        break;

    case 'admin_dashboard':
        $admin = new AdminController();
        $admin->dashboard();
        break;

    case 'admin_users':
        $admin = new AdminController();
        $admin->listUsers();
        break;

    case 'admin_aliments':
        $alimentController = new AlimentController();
        $alimentController->listAliments();
        break;

    case 'admin_add_aliment':
        $alimentController = new AlimentController();
        $alimentController->addAliment();
        break;

    case 'admin_delete_aliment':
        $alimentController = new AlimentController();
        $alimentController->deleteAliment();
        break;

    case 'admin_edit_aliment':
        $alimentController = new AlimentController();
        $alimentController->editAliment();
        break;

    case 'admin_consultations':
        $consultationController = new ConsultationController();
        $consultationController->adminList();
        break;

    case 'admin_suivis':
        require_once 'controllers/SuiviController.php';
        $suiviController = new SuiviController();
        $suiviController->adminList();
        break;

    case 'admin_add_suivi':
        require_once 'controllers/SuiviController.php';
        $suiviController = new SuiviController();
        $suiviController->adminAdd();
        break;

    case 'admin_edit_suivi':
        require_once 'controllers/SuiviController.php';
        $suiviController = new SuiviController();
        $suiviController->adminEdit();
        break;

    case 'admin_delete_suivi':
        require_once 'controllers/SuiviController.php';
        $suiviController = new SuiviController();
        $suiviController->adminDelete();
        break;

    case 'admin_add_consultation':
        $consultationController = new ConsultationController();
        $consultationController->adminAdd();
        break;

    case 'admin_edit_consultation':
        $consultationController = new ConsultationController();
        $consultationController->adminEdit();
        break;

    case 'admin_delete_consultation':
        $consultationController = new ConsultationController();
        $consultationController->adminDelete();
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "Page introuvable.";
        break;
}
?>
