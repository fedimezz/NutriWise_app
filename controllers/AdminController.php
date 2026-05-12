<?php
/**
 * Admin Controller
 *
 * Handles admin and owner dashboard operations including:
 * - User management (list, create, edit, delete)
 * - Aliment/food item management
 * - Recipe administration
 * - Nutrition plan management
 * - Planning management (NEW)
 *
 * Access restricted to ADMIN and OWNER roles
 *
 * @package NutriWise
 * @subpackage Controllers
 */

require_once 'models/UserModel.php';
require_once 'models/RecetteModel.php';
require_once 'models/AlimentModel.php';
require_once 'models/ActivityLogModel.php';
require_once 'models/PlanningModel.php'; // AJOUTÉ

class AdminController
{
    /** @var UserModel User model instance */
    private UserModel $userModel;

    /** @var RecetteModel Recette model instance */
    private RecetteModel $recetteModel;

    /** @var AlimentModel Aliment model instance */
    private AlimentModel $alimentModel;
    
    /** @var PlanningModel Planning model instance */
    private PlanningModel $planningModel; // AJOUTÉ
    
    /** @var PDO Database connection */
    private $pdo;

    /**
     * Constructor - Verify admin/owner access and initialize models
     */
    public function __construct($pdo)
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);

        $this->pdo = $pdo;

        $this->userModel = new UserModel($pdo);
        $this->recetteModel = new RecetteModel($pdo);
        $this->alimentModel = new AlimentModel($pdo);
        $this->planningModel = new PlanningModel($pdo); // AJOUTÉ
    }
    
    // ========================================
    // DASHBOARD / OVERVIEW
    // ========================================

    /**
     * Display admin dashboard with statistics
     *
     * Shows overview of total users, aliments, recipes, active plans, and plannings
     */
    public function dashboard() {
        $page = 'admin_dashboard';

        // Statistiques existantes
        $totalUsers    = $this->userModel->countUsers();
        $totalAliments = $this->alimentModel->countAliments();
        $totalRecipes  = $this->recetteModel->countRecettes(null, null);
        $activePlans   = $this->userModel->countActivePlans();
        
        // NOUVELLES STATISTIQUES
        $totalPlannings = $this->planningModel->countPlannings();
        $activePlannings = $this->planningModel->countPlannings(null, 'active');

        // Derniers utilisateurs
        $usersList = $this->userModel->getAllUsers();

        // Dernières activités (5 dernières)
        $activityModel    = new ActivityLogModel();
        $recentActivities = $activityModel->getLogs(null, null, 5, 0);

        // Vue
        require_once 'views/back/dashboard.php';
    }

    // ========================================
    // USER MANAGEMENT
    // ========================================

    /**
     * List all users
     */
    public function listUsers()
    {
        $page = 'admin_users';
        $usersList = $this->userModel->getAllUsers();
        require_once 'views/back/users.php';
    }
    
    /**
     * Modifier une recette
     */
    public function editRecette() {
        $id = (int)($_GET['id'] ?? 0);
        
        // Récupérer la recette
        $stmt = $this->pdo->prepare("SELECT * FROM recettes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $recette = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$recette) {
            $_SESSION['error'] = "Recette non trouvée";
            redirect('index.php?page=admin_recettes');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF check
            if (!isset($_POST['_csrf']) || $_POST['_csrf'] !== $_SESSION['_csrf']) {
                $_SESSION['error'] = "Erreur de sécurité";
                redirect('index.php?page=admin_edit_recette&id=' . $id);
            }
            
            // Récupérer les données
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categorie = $_POST['categorie'] ?? '';
            $difficulte = $_POST['difficulte'] ?? 'Facile';
            $temps_preparation = (int)($_POST['temps_preparation'] ?? 0);
            $temps_cuisson = (int)($_POST['temps_cuisson'] ?? 0);
            $portions = (int)($_POST['portions'] ?? 4);
            
            // Validation simple
            if (empty($nom)) {
                $_SESSION['error'] = "Le nom de la recette est obligatoire";
                redirect('index.php?page=admin_edit_recette&id=' . $id);
            }
            
            // Gestion de l'image
            $image = $recette['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'views/assets/uploads/recettes/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = time() . '_' . uniqid() . '.' . $ext;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = $fileName;
                }
            }
            
            // Mise à jour
            try {
                $sql = "UPDATE recettes SET 
                        nom = :nom, 
                        description = :description, 
                        categorie = :categorie, 
                        difficulte = :difficulte, 
                        temps_preparation = :temps_preparation, 
                        temps_cuisson = :temps_cuisson, 
                        portions = :portions, 
                        image = :image 
                        WHERE id = :id";
                        
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':description' => $description,
                    ':categorie' => $categorie,
                    ':difficulte' => $difficulte,
                    ':temps_preparation' => $temps_preparation,
                    ':temps_cuisson' => $temps_cuisson,
                    ':portions' => $portions,
                    ':image' => $image,
                    ':id' => $id
                ]);
                
                $_SESSION['success'] = "Recette modifiée avec succès !";
                redirect('index.php?page=admin_recettes');
                
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erreur : " . $e->getMessage();
                redirect('index.php?page=admin_edit_recette&id=' . $id);
            }
        }
        
        // Afficher le formulaire
        require_once 'views/back/recette_edit.php';
    }

    /**
     * Afficher la liste des recettes pour l'admin
     */
    public function adminRecettes() {
        // Vérifier les droits admin
        if (role_rank(current_user_role()) < role_rank(ROLE_ADMIN)) {
            redirect('index.php?page=home');
        }

        // Handle recipe deletion
        if (($_GET['action'] ?? '') === 'delete_recipe') {
            $rid = (int)($_GET['id'] ?? 0);
            if ($rid > 0) {
                // Delete recipe and its relations
                try {
                    // Delete from recette_ingredients first
                    $stmt = $this->pdo->prepare("DELETE FROM recette_ingredients WHERE recette_id = :id");
                    $stmt->execute([':id' => $rid]);
                    
                    // Delete from recette_etapes
                    $stmt = $this->pdo->prepare("DELETE FROM recette_etapes WHERE recette_id = :id");
                    $stmt->execute([':id' => $rid]);
                    
                    // Delete from recette_favoris
                    $stmt = $this->pdo->prepare("DELETE FROM recette_favoris WHERE recette_id = :id");
                    $stmt->execute([':id' => $rid]);
                    
                    // Delete the recipe
                    $stmt = $this->pdo->prepare("DELETE FROM recettes WHERE id = :id");
                    $stmt->execute([':id' => $rid]);
                    
                    $_SESSION['success'] = "Recette supprimée avec succès !";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
                }
            }
            redirect("index.php?page=admin_recettes");
        }

        // Paramètres de pagination et recherche
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $search = $_GET['search'] ?? '';
        $perPage = 10;
        
        // Récupérer les recettes
        $recettesList = $this->recetteModel->getAllRecettes($search, null, $page, $perPage);
        $totalRecettes = $this->recetteModel->countRecettes($search, null);
        
        // Calculer le nombre total d'ingrédients utilisés
        $totalIngredients = $this->getTotalIngredientsCount();
        
        $totalPages = ceil($totalRecettes / $perPage);
        $currentPage = $page;
        
        // Inclure la vue
        require_once 'views/back/recettes.php';
    }

    /**
     * Compter le nombre total d'ingrédients utilisés
     */
    private function getTotalIngredientsCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(DISTINCT ingredient_id) as total FROM recette_ingredients");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Add new user (admin function)
     *
     * Handles user creation with auto-generated credentials if needed
     * Validates roles and enforces role hierarchy (only OWNER can create OWNER)
     */
    public function addUser()
    {
        $page = 'admin_add_user';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $prenom = trim($_POST['prenom'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $password = (string)($_POST['password'] ?? '');
            $role = $_POST['role'] ?? ROLE_USER;

            // Validate required fields
            if ($prenom === '' || $nom === '') {
                $_SESSION['error'] = "Le prénom et le nom sont obligatoires.";
                require_once 'views/back/add_user.php';
                return;
            }

            // Normalize and guard role assignment based on user's own role
            $allowedRoles = [ROLE_USER, ROLE_NUTRITIONIST, ROLE_ADMIN, ROLE_OWNER];
            if (!in_array($role, $allowedRoles, true)) {
                $role = ROLE_USER;
            }
            if (current_user_role() !== ROLE_OWNER) {
                // Admin cannot grant admin/owner roles, only OWNER can
                if ($role === ROLE_ADMIN || $role === ROLE_OWNER) {
                    $role = ROLE_USER;
                }
            }

            // Generate email if not provided
            $generatedEmail = null;
            if ($email === '') {
                $generatedEmail = $this->generateUniqueEmail($prenom, $nom);
                $email = $generatedEmail;
            }

            // Generate password if not provided
            $generatedPassword = null;
            if ($password === '') {
                $generatedPassword = $this->generatePassword(8);
                $password = $generatedPassword;
            }

            $success = $this->userModel->createUser($prenom, $nom, $email, $password, $role, $telephone, 'actif');
            if ($success) {
                $msg = "Utilisateur ajouté avec succès !";
                if ($generatedEmail || $generatedPassword) {
                    $msg .= " Identifiants générés : ";
                    if ($generatedEmail) $msg .= "Email: " . e($generatedEmail) . " ";
                    if ($generatedPassword) $msg .= "Mot de passe: " . e($generatedPassword);
                }
                $_SESSION['success'] = $msg;
                redirect("index.php?page=admin_users");
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout. L'email existe peut-être déjà.";
                require_once 'views/back/add_user.php';
            }
        } else {
            require_once 'views/back/add_user.php';
        }
    }

    /**
     * Generate unique email from first and last name
     *
     * Creates user-friendly email with format: prenom.nom@nutriwise.local
     * Appends numbers if email already exists
     *
     * @param string $prenom First name
     * @param string $nom Last name
     * 
     * @return string Generated unique email
     */
    private function generateUniqueEmail(string $prenom, string $nom): string
    {
        $base = $this->slug($prenom) . "." . $this->slug($nom);
        $base = trim($base, '.');
        if ($base === '') {
            $base = "user";
        }

        $domain = "nutriwise.local";
        $candidate = $base . "@" . $domain;
        $i = 1;

        // Increment counter until finding unique email
        while ($this->userModel->emailExists($candidate)) {
            $candidate = $base . $i . "@" . $domain;
            $i++;
            if ($i > 9999) {
                // Fallback to timestamp-based email
                $candidate = "user" . time() . "@" . $domain;
                break;
            }
        }
        return $candidate;
    }

    /**
     * Generate random password
     *
     * Uses alphanumeric characters, excludes ambiguous ones (0, O, I, l)
     *
     * @param int $length Password length
     *
     * @return string Generated password
     */
    private function generatePassword(int $length): string
    {
        // Exclude ambiguous characters: 0=O, I=l, 1=l
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $out;
    }

    /**
     * Convert text to URL-friendly slug
     *
     * Removes accents, converts to lowercase, replaces non-alphanumeric with dashes
     *
     * @param string $value Text to slugify
     *
     * @return string Slugified text
     */
    private function slug(string $value): string
    {
        $value = trim($value);
        if ($value === '') return '';

        // Remove accents using transliteration
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = strtolower($value);
        // Replace non-alphanumeric characters with dashes
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return trim((string)$value, '-');
    }

    /**
     * Edit user details and role
     *
     * Enforces role hierarchy: only OWNER can modify OWNER accounts
     * Allows changing user information and resetting password
     */
    public function editUser()
    {
        $page = 'admin_edit_user';
        if (!isset($_GET['id'])) {
            redirect("index.php?page=admin_users");
        }

        $id = (int)$_GET['id'];
        $userToEdit = $this->userModel->getUserById($id);

        if (!$userToEdit) {
            redirect("index.php?page=admin_users");
        }

        // Hard rule: only OWNER can edit OWNER accounts
        $targetRole = strtolower(trim((string)($userToEdit['role'] ?? ROLE_USER)));
        if ($targetRole === ROLE_OWNER && current_user_role() !== ROLE_OWNER) {
            $_SESSION['error'] = "Action interdite : seul un Owner peut modifier un compte Owner.";
            redirect("index.php?page=admin_users");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            $prenom = trim($_POST['prenom'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $role = $_POST['role'] ?? ROLE_USER;
            $statut = $_POST['statut'] ?? 'active';
            $password = $_POST['password'] ?? '';

            // Validate required fields
            if ($prenom === '' || $nom === '' || $email === '') {
                $_SESSION['error'] = "Le nom et l'email sont obligatoires.";
                require_once 'views/back/edit_user.php';
                return;
            }

            // Role changes: only OWNER can grant admin/owner roles
            $allowedRoles = [ROLE_USER, ROLE_NUTRITIONIST, ROLE_ADMIN, ROLE_OWNER];
            if (!in_array($role, $allowedRoles, true)) {
                $role = ROLE_USER;
            }
            if (current_user_role() !== ROLE_OWNER) {
                if ($role === ROLE_ADMIN || $role === ROLE_OWNER) {
                    // Fall back to existing role to prevent admin self-promotion
                    $role = $userToEdit['role'] ?? ROLE_USER;
                }
            }

            $success = $this->userModel->updateUser($id, $prenom, $nom, $email, $role, $statut, $telephone);
            if ($success && $password !== '') {
                $success = $this->userModel->updateUserPassword($id, $password);
            }

            if ($success) {
                $_SESSION['success'] = "Utilisateur modifié avec succès !";
                redirect("index.php?page=admin_users");
            } else {
                $_SESSION['error'] = "Erreur lors de la modification.";
            }
        }

        require_once 'views/back/edit_user.php';
    }

    /**
     * Delete user by ID
     * 
     * Enforces security rules:
     * - Cannot delete own account
     * - Only OWNER can delete OWNER accounts
     * - Only OWNER can delete ADMIN accounts
     */
    public function deleteUser()
    {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];

            // Prevent self-deletion
            if ($id == current_user_id()) {
                $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte";
                redirect("index.php?page=admin_users");
            }

            // Get target user and check role hierarchy
            $target = $this->userModel->getUserById($id);
            if (!$target) {
                $_SESSION['error'] = "Utilisateur introuvable.";
                redirect("index.php?page=admin_users");
            }

            $targetRole = (string)($target['role'] ?? ROLE_USER);

            // Only OWNER can delete OWNER accounts
            if ($targetRole === ROLE_OWNER && current_user_role() !== ROLE_OWNER) {
                $_SESSION['error'] = "Action interdite : seul un Owner peut supprimer un compte Owner.";
                redirect("index.php?page=admin_users");
            }

            // Only OWNER can delete ADMIN accounts
            if ($targetRole === ROLE_ADMIN && current_user_role() !== ROLE_OWNER) {
                $_SESSION['error'] = "Action interdite : seul un Owner peut supprimer un compte Admin.";
                redirect("index.php?page=admin_users");
            }

            $this->userModel->deleteUser($id);
            $_SESSION['success'] = "Utilisateur supprimé avec succès !";
        }
        redirect("index.php?page=admin_users");
    }

    // ========================================
    // ALIMENTS / FOOD ITEM MANAGEMENT
    // ========================================

    /**
     * List all food items
     */
    public function listAliments()
    {
        $alimentsList = $this->alimentModel->getAllAliments();
        require_once 'views/back/aliments.php';
    }

    // ========================================
    // NUTRITION PLANS
    // ========================================

    /**
     * Manage nutrition plans
     * 
     * Admin can create, view, and manage plans
     */
    public function deleteUsersBulk()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['ids']) || empty($data['ids'])) {
            echo json_encode(['success' => false, 'message' => 'No IDs']);
            return;
        }

        $ids = array_map('intval', $data['ids']);

        // 🚫 SECURITY: never allow owner deletion
        $ids = array_filter($ids, function($id) {
            return $id !== 1; // change if needed
        });

        try {
            $this->userModel->deleteMultiple($ids);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function adminPlans()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);
        $page = 'admin_plans';

        // Handle plan creation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_plan') {
            csrf_check();
            $title = trim($_POST['title'] ?? '');
            $assignedTo = ($_POST['assigned_to'] ?? '') !== '' ? (int)$_POST['assigned_to'] : null;
            $goal = trim($_POST['goal'] ?? '');
            $goal = $goal !== '' ? $goal : null;
            $calTarget = ($_POST['calories_target'] ?? '') !== '' ? (int)$_POST['calories_target'] : null;
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            // Validate required fields
            if ($title === '') {
                $_SESSION['error'] = "Titre requis.";
                redirect("index.php?page=admin_plans");
            }

            if ($this->userModel->createPlan(current_user_id() ?? 0, $assignedTo, $title, $goal, $calTarget, $isActive)) {
                $_SESSION['success'] = "Plan créé.";
            } else {
                $_SESSION['error'] = "Erreur lors de la création.";
            }
            redirect("index.php?page=admin_plans");
        }

        // Load data for display
        $plans = $this->userModel->getPlans(null);
        $usersList = $this->userModel->getAllUsers();
        require_once 'views/front/suivi.php';
    }
}
?>