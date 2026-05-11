<?php
// index.php
session_start();

// Inclusion des contrôleurs
require_once 'controllers/AuthController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/AlimentController.php';
require_once 'controllers/MenuController.php';
require_once 'controllers/PlanningController.php';

// On récupère la page demandée, sinon 'home'
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    // --- FRONTEND ---
    case 'home':
        // On inclut la vue de la page d'accueil
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

    case 'nutrition_plans':
        $planningController = new PlanningController();
        $planningController->frontList();
        break;

    case 'nutrition_plan_details':
        $planningController = new PlanningController();
        $planningController->details();
        break;

    case 'menu_details':
        $menuController = new MenuController();
        $menuController->details();
        break;

    // --- BACKEND (ADMIN) ---
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

    case 'admin_plannings':
        $planningController = new PlanningController();
        $planningController->listPlannings();
        break;

    case 'admin_planning_details':
        $planningController = new PlanningController();
        $planningController->adminDetails();
        break;

    case 'admin_add_planning':
        $planningController = new PlanningController();
        $planningController->addPlanning();
        break;

    case 'admin_edit_planning':
        $planningController = new PlanningController();
        $planningController->editPlanning();
        break;

    case 'admin_delete_planning':
        $planningController = new PlanningController();
        $planningController->deletePlanning();
        break;

    // Les menus sont gérés via les plannings, pas séparément
    /*
    case 'admin_menus':
        $menuController = new MenuController();
        $menuController->listMenus();
        break;

    case 'admin_add_menu':
        $menuController = new MenuController();
        $menuController->addMenu();
        break;

    case 'admin_edit_menu':
        $menuController = new MenuController();
        $menuController->editMenu();
        break;

    case 'admin_delete_menu':
        $menuController = new MenuController();
        $menuController->deleteMenu();
        break;
    */

    default:
        header("HTTP/1.0 404 Not Found");
        echo "Page introuvable.";
        break;
}