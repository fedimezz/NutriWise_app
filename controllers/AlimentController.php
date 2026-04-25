<?php
/**
 * Aliment (Food Item) Controller
 * 
 * Handles both admin and user-facing operations for food items:
 * - Admin: CRUD operations (list, add, edit, delete)
 * - Users: Browse and view aliment details
 * 
 * @package NutriWise
 * @subpackage Controllers
 */

require_once 'models/AlimentModel.php';

class AlimentController
{
    /** @var AlimentModel Aliment model instance */
    private AlimentModel $model;

    /**
     * Constructor - Initialize aliment model
     */
    public function __construct()
    {
        $this->model = new AlimentModel();
    }

    // ========================================
    // ADMIN FUNCTIONS - MANAGEMENT
    // ========================================

    /**
     * List all aliments (admin page)
     * 
     * Requires ADMIN or OWNER role
     * Displays full aliment inventory
     */
    public function listAliments()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);
        $page = 'admin_aliments';
        $alimentsList = $this->model->getAllAliments();
        require_once 'views/back/aliments.php';
    }

    // ========================================
    // USER FUNCTIONS - BROWSING
    // ========================================

    /**
     * Display aliments list for users
     * 
     * Shows all aliments with categories, accessible to logged-in users
     */
    public function frontList()
    {
        require_login();
        $aliments = $this->model->getAllAliments();
        $categories = $this->model->getAllCategories();
        require_once 'views/front/aliments.php';
    }

    /**
     * Display details for a single aliment
     * 
     * Requires login and GET parameter 'id'
     */
    public function details()
    {
        require_login();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            redirect("index.php?page=aliments&error=Aliment introuvable");
        }

        $aliment = $this->model->getAlimentById((int)$id);
        require_once 'views/front/aliment_details.php';
    }

    // ========================================
    // ADMIN FUNCTIONS - CREATE / UPDATE / DELETE
    // ========================================

    /**
     * Add new aliment
     * 
     * Requires ADMIN or OWNER role
     * Creates new food item with nutritional information
     */
    public function addAliment()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);
        $page = 'admin_add_aliment';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $nom = trim($_POST['nom']);
            $category_id = (int)$_POST['category_id'];
            $calories = (float)$_POST['calories'];
            $proteines = (float)$_POST['proteines'];
            $glucides = (float)$_POST['glucides'];
            $lipides = (float)$_POST['lipides'];
            $eco_score = (float)$_POST['eco_score'];
            $durable = isset($_POST['durable']) ? 1 : 0;

            // Validate required fields
            if (empty($nom)) {
                $_SESSION['error'] = "Le nom de l'aliment est obligatoire.";
                $categories = $this->model->getAllCategories();
                require_once 'views/back/add_aliment.php';
                return;
            }

            $success = $this->model->addAliment($nom, $category_id, $calories, $proteines, $glucides, $lipides, $eco_score, $durable);
            if ($success) {
                $_SESSION['success'] = "Aliment ajouté avec succès !";
                redirect("index.php?page=admin_aliments");
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout de l'aliment.";
            }
        }

        $categories = $this->model->getAllCategories();
        require_once 'views/back/add_aliment.php';
    }

    // ========================================
    // ADMIN FUNCTIONS - DELETE
    // ========================================

    /**
     * Delete aliment by ID
     * 
     * Requires ADMIN or OWNER role
     * Removes aliment from database
     */
    public function deleteAliment()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);

        if (isset($_GET['id'])) {
            $this->model->deleteAliment((int)$_GET['id']);
            $_SESSION['success'] = "Aliment supprimé avec succès !";
        }
        redirect("index.php?page=admin_aliments");
    }

    // ========================================
    // ADMIN FUNCTIONS - UPDATE/EDIT
    // ========================================

    /**
     * Edit existing aliment
     * 
     * Requires ADMIN or OWNER role
     * Updates food item details and nutritional information
     */
    public function editAliment()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);
        $page = 'admin_edit_aliment';

        if (!isset($_GET['id'])) {
            redirect("index.php?page=admin_aliments");
        }

        $id = (int)$_GET['id'];
        $aliment = $this->model->getAlimentById($id);

        if (!$aliment) {
            redirect("index.php?page=admin_aliments&error=Aliment introuvable");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $nom = trim($_POST['nom']);
            $category_id = (int)$_POST['category_id'];
            $calories = (float)$_POST['calories'];
            $proteines = (float)$_POST['proteines'];
            $glucides = (float)$_POST['glucides'];
            $lipides = (float)$_POST['lipides'];
            $eco_score = (float)$_POST['eco_score'];
            $durable = isset($_POST['durable']) ? 1 : 0;

            // Validate required fields
            if (empty($nom)) {
                $_SESSION['error'] = "Le nom de l'aliment est obligatoire.";
                $categories = $this->model->getAllCategories();
                require_once 'views/back/edit-aliment.php';
                return;
            }

            $success = $this->model->updateAliment($id, $nom, $category_id, $calories, $proteines, $glucides, $lipides, $eco_score, $durable);
            if ($success) {
                $_SESSION['success'] = "Aliment modifié avec succès !";
                redirect("index.php?page=admin_aliments");
            } else {
                $_SESSION['error'] = "Erreur lors de la modification.";
            }
        }

        $categories = $this->model->getAllCategories();
        require_once 'views/back/edit-aliment.php';
    }
}
?>