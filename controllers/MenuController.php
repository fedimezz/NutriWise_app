<?php
require_once 'models/MenuModel.php';

class MenuController {
    private $model;

    public function __construct() {
        $this->model = new MenuModel();
    }

    public function listMenus() {
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            header("Location: index.php?page=home");
            exit();
        }

        $menus = $this->model->getAllMenus();
        require_once 'views/back/menus.php';
    }

    public function addMenu() {
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            header("Location: index.php?page=home");
            exit();
        }

        $users = $this->model->getAllUsers();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $goal = trim($_POST['goal'] ?? '');
            $caloriesTarget = intval($_POST['calories_target'] ?? 0);
            $assignedTo = intval($_POST['assigned_to'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $meals = $_POST['meals'] ?? [];

            if($title === '' || $assignedTo <= 0) {
                $error = 'Le titre et l’utilisateur sont obligatoires.';
                require_once 'views/back/add_menu.php';
                return;
            }

            $success = $this->model->addMenu($title, $goal, $caloriesTarget, $assignedTo, $_SESSION['user_id'] ?? null, $isActive, $meals);
            if($success) {
                header("Location: index.php?page=admin_menus&success=Menu ajouté avec succès !");
                exit();
            }

            $error = 'Erreur lors de l’ajout du menu.';
        }

        require_once 'views/back/add_menu.php';
    }

    public function editMenu() {
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            header("Location: index.php?page=home");
            exit();
        }

        if(!isset($_GET['id'])) {
            header("Location: index.php?page=admin_menus");
            exit();
        }

        $id = intval($_GET['id']);
        $menu = $this->model->getMenuById($id);
        if(!$menu) {
            header("Location: index.php?page=admin_menus&error=Menu introuvable");
            exit();
        }

        $users = $this->model->getAllUsers();
        $menuMeals = $this->model->getMenuMeals($id);
        $selectedMeals = [];
        foreach($menuMeals as $meal) {
            $selectedMeals[$meal['day_of_week']][$meal['meal_type']] = $meal['description'];
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $goal = trim($_POST['goal'] ?? '');
            $caloriesTarget = intval($_POST['calories_target'] ?? 0);
            $assignedTo = intval($_POST['assigned_to'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $meals = $_POST['meals'] ?? [];

            if($title === '' || $assignedTo <= 0) {
                $error = 'Le titre et l’utilisateur sont obligatoires.';
                require_once 'views/back/edit_menu.php';
                return;
            }

            $success = $this->model->updateMenu($id, $title, $goal, $caloriesTarget, $assignedTo, $isActive, $meals);
            if($success) {
                header("Location: index.php?page=admin_menus&success=Menu modifié avec succès !");
                exit();
            }

            $error = 'Erreur lors de la modification du menu.';
        }

        require_once 'views/back/edit_menu.php';
    }

    public function deleteMenu() {
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            header("Location: index.php?page=home");
            exit();
        }

        if(isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->model->deleteMenu($id);
        }

        header("Location: index.php?page=admin_menus&success=Menu supprimé avec succès !");
        exit();
    }

    public function details() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login&error=Connectez-vous pour voir le menu");
            exit();
        }

        if(!isset($_GET['id'])) {
            header("Location: index.php?page=nutrition_plans");
            exit();
        }

        $id = intval($_GET['id']);
        $menu = $this->model->getMenuById($id);

        if(!$menu) {
            header("Location: index.php?page=nutrition_plans&error=Menu introuvable");
            exit();
        }

        $menuMeals = $this->model->getMenuMeals($id);
        require_once 'views/front/menu_details.php';
    }
}
