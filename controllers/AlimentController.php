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
    private string $uploadDir;
    private array $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
    private int $maxImageSize = 3 * 1024 * 1024; // 3MB

    /**
     * Constructor - Initialize aliment model and upload directory
     */
    public function __construct()
    {
        $this->model = new AlimentModel();
        $this->uploadDir = realpath(__DIR__ . '/../views/uploads/aliments/') ?: __DIR__ . '/../views/uploads/aliments/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    private function uploadImage(array $file): array
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Erreur lors de l\'upload de l\'image.'];
        }

        if ($file['size'] > $this->maxImageSize) {
            return ['success' => false, 'error' => 'L\'image dépasse la taille maximale de 3MB.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedImageTypes, true)) {
            return ['success' => false, 'error' => 'Format d\'image non supporté. Utilisez JPG, PNG ou WebP.'];
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'aliment_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . strtolower($extension);
        $destination = $this->uploadDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'error' => 'Impossible de sauvegarder l\'image sur le serveur.'];
        }

        return ['success' => true, 'filename' => $filename];
    }

    private function deleteImageFile(?string $filename): void
    {
        if (!$filename) {
            return;
        }
        $path = $this->uploadDir . DIRECTORY_SEPARATOR . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    // ========================================
    // ADMIN FUNCTIONS - CRUD MANAGEMENT
    // ========================================

    /**
     * List all aliments (admin page)
     * 
     * Requires ADMIN or OWNER role
     * Displays full aliment inventory with pagination, search and category filter
     */
    public function listAliments()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);
        
        $page = (int)($_GET['p'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');
        
        $categories = $this->model->getAllCategories();
        $totalAliments = $this->model->countAliments();
        
        if (!empty($search)) {
            $alimentsList = $this->model->searchAliments($search);
            $totalPages = 1;
            $currentPage = 1;
        } elseif (!empty($category) && $category !== 'all') {
            $alimentsList = $this->model->getAlimentsByCategory((int)$category);
            $totalPages = 1;
            $currentPage = 1;
        } else {
            $paginated = $this->model->getAlimentsPaginated($page, 15);
            $alimentsList = $paginated['data'];
            $totalPages = $paginated['total_pages'];
            $currentPage = $paginated['page'];
        }
        
        require_once 'views/aliment/list.php';
    }

    /**
     * Add new aliment (GET: show form, POST: process form)
     * 
     * Requires ADMIN or OWNER role
     * Creates new food item with nutritional information
     */
    public function addAliment()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);
        
        $categories = $this->model->getAllCategories();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            
            // Sanitize and validate inputs
            $nom = trim($_POST['nom'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $calories = (float)($_POST['calories'] ?? 0);
            $proteines = (float)($_POST['proteines'] ?? 0);
            $glucides = (float)($_POST['glucides'] ?? 0);
            $lipides = (float)($_POST['lipides'] ?? 0);
            $eco_score = (float)($_POST['eco_score'] ?? 0);
            $durable = isset($_POST['durable']) ? 1 : 0;
            $imageFilename = null;

            // Upload image if provided
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->uploadImage($_FILES['image']);
                if (!$uploadResult['success']) {
                    $_SESSION['error'] = $uploadResult['error'];
                    require_once 'views/aliment/add.php';
                    return;
                }
                $imageFilename = $uploadResult['filename'];
            }

            // Validate required fields
            $errors = [];
            if (empty($nom)) {
                $errors[] = "Le nom de l'aliment est obligatoire.";
            }
            if ($category_id <= 0) {
                $errors[] = "Veuillez sélectionner une catégorie.";
            }
            if ($eco_score < 0 || $eco_score > 10) {
                $errors[] = "L'éco-score doit être compris entre 0 et 10.";
            }
            if ($calories < 0) {
                $errors[] = "Les calories ne peuvent pas être négatives.";
            }
            if ($proteines < 0 || $glucides < 0 || $lipides < 0) {
                $errors[] = "Les valeurs nutritionnelles ne peuvent pas être négatives.";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                require_once 'views/aliment/add.php';
                return;
            }

            $success = $this->model->addAliment($nom, $category_id, $calories, $proteines, $glucides, $lipides, $eco_score, $imageFilename, $durable);
            
            if ($success) {
                $_SESSION['success'] = "Aliment ajouté avec succès !";
                redirect("index.php?page=admin_aliments");
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout de l'aliment.";
                require_once 'views/aliment/add.php';
            }
        } else {
            // GET request - show form
            require_once 'views/aliment/add.php';
        }
    }

    /**
     * Edit existing aliment (GET: show form, POST: process form)
     * 
     * Requires ADMIN or OWNER role
     * Updates food item details and nutritional information
     */
    public function editAliment()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);
        
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = "ID d'aliment invalide.";
            redirect("index.php?page=admin_aliments");
        }

        $id = (int)$_GET['id'];
        $aliment = $this->model->getAlimentById($id);

        if (!$aliment) {
            $_SESSION['error'] = "Aliment introuvable.";
            redirect("index.php?page=admin_aliments");
        }

        $categories = $this->model->getAllCategories();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            
            // Sanitize and validate inputs
            $nom = trim($_POST['nom'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $calories = (float)($_POST['calories'] ?? 0);
            $proteines = (float)($_POST['proteines'] ?? 0);
            $glucides = (float)($_POST['glucides'] ?? 0);
            $lipides = (float)($_POST['lipides'] ?? 0);
            $eco_score = (float)($_POST['eco_score'] ?? 0);
            $durable = isset($_POST['durable']) ? 1 : 0;
            $keepImage = isset($_POST['keep_image']);
            $imageFilename = $aliment['image']; // Keep existing by default

            // Handle image upload/replacement
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->uploadImage($_FILES['image']);
                if (!$uploadResult['success']) {
                    $_SESSION['error'] = $uploadResult['error'];
                    require_once 'views/aliment/edit.php';
                    return;
                }
                // Delete old image if upload successful
                if (!empty($aliment['image'])) {
                    $this->deleteImageFile($aliment['image']);
                }
                $imageFilename = $uploadResult['filename'];
            } elseif (!$keepImage && !empty($aliment['image'])) {
                // User chose to remove image
                $this->deleteImageFile($aliment['image']);
                $imageFilename = null;
            }

            // Validate required fields
            $errors = [];
            if (empty($nom)) {
                $errors[] = "Le nom de l'aliment est obligatoire.";
            }
            if ($category_id <= 0) {
                $errors[] = "Veuillez sélectionner une catégorie.";
            }
            if ($eco_score < 0 || $eco_score > 10) {
                $errors[] = "L'éco-score doit être compris entre 0 et 10.";
            }
            if ($calories < 0) {
                $errors[] = "Les calories ne peuvent pas être négatives.";
            }
            if ($proteines < 0 || $glucides < 0 || $lipides < 0) {
                $errors[] = "Les valeurs nutritionnelles ne peuvent pas être négatives.";
            }

            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                require_once 'views/aliment/edit.php';
                return;
            }

            $success = $this->model->updateAliment($id, $nom, $category_id, $calories, $proteines, $glucides, $lipides, $eco_score, $imageFilename, $durable);
            
            if ($success) {
                $_SESSION['success'] = "Aliment modifié avec succès !";
                redirect("index.php?page=admin_aliments");
            } else {
                $_SESSION['error'] = "Erreur lors de la modification de l'aliment.";
                require_once 'views/aliment/edit.php';
            }
        } else {
            // GET request - show form
            require_once 'views/aliment/edit.php';
        }
    }

    /**
     * Delete aliment by ID
     * 
     * Requires ADMIN or OWNER role
     * Removes aliment from database and deletes associated image
     */
    public function deleteAliment()
    {
        require_role([ROLE_ADMIN, ROLE_OWNER]);

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = "ID d'aliment invalide.";
            redirect("index.php?page=admin_aliments");
        }

        $id = (int)$_GET['id'];
        $aliment = $this->model->getAlimentById($id);
        
        if (!$aliment) {
            $_SESSION['error'] = "Aliment introuvable.";
            redirect("index.php?page=admin_aliments");
        }
        
        // Delete image file if exists
        if (!empty($aliment['image'])) {
            $this->deleteImageFile($aliment['image']);
        }
        
        $success = $this->model->deleteAliment($id);
        
        if ($success) {
            $_SESSION['success'] = "Aliment supprimé avec succès !";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression de l'aliment.";
        }
        
        redirect("index.php?page=admin_aliments");
    }

    // ========================================
    // USER FUNCTIONS - BROWSING
    // ========================================

    /**
     * Display aliments list for users
     * 
     * Shows all aliments with categories, accessible to logged-in users
     * Features: search, filter by category, pagination
     */
    public function frontList()
    {
        require_login();
        
        $page = (int)($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? 'all');
        $sort = trim($_GET['sort'] ?? 'name');
        
        $categories = $this->model->getAllCategories();
        
        // Apply filters
        if (!empty($search)) {
            $aliments = $this->model->searchAliments($search);
            $currentCategory = 'all';
            $totalPages = 1;
            $currentPage = 1;
        } elseif ($category !== 'all') {
            $aliments = $this->model->getAlimentsByCategory((int)$category);
            $currentCategory = $category;
            $totalPages = 1;
            $currentPage = 1;
        } else {
            $paginated = $this->model->getAlimentsPaginated($page, 12);
            $aliments = $paginated['data'];
            $totalPages = $paginated['total_pages'];
            $currentPage = $paginated['page'];
            $currentCategory = 'all';
        }
        
        // Apply sorting
        usort($aliments, function($a, $b) use ($sort) {
            if ($sort === 'calories') {
                return $a['calories'] <=> $b['calories'];
            } elseif ($sort === 'eco_score') {
                return $b['eco_score'] <=> $a['eco_score'];
            }
            return strcmp($a['nom'], $b['nom']);
        });
        
        // Count durable aliments
        $durableCount = count(array_filter($aliments, fn($a) => ($a['eco_score'] ?? 0) >= 7));
        
        require_once 'views/front/aliments.php';
    }

    /**
     * Display details for a single aliment
     * 
     * Requires login and GET parameter 'id'
     * Shows full nutritional information and related aliments
     */
    public function details()
    {
        require_login();

        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            $_SESSION['error'] = "Aliment introuvable.";
            redirect("index.php?page=aliments");
        }

        $aliment = $this->model->getAlimentById((int)$id);
        
        if (!$aliment) {
            $_SESSION['error'] = "Aliment non trouvé.";
            redirect("index.php?page=aliments");
        }
        
        $relatedAliments = $this->model->getRelatedAliments((int)$id, $aliment['category_id'] ?? null, 4);
        
        require_once 'views/aliment/details.php';
    }

    // ========================================
    // ADDITIONAL USER FUNCTIONS
    // ========================================

    /**
     * Display durable aliments
     * 
     * Shows aliments with eco_score >= 7
     */
    public function durableAliments()
    {
        require_login();
        
        $aliments = $this->model->getDurableAliments();
        $categories = $this->model->getAllCategories();
        $currentCategory = 'durable';
        $totalPages = 1;
        $currentPage = 1;
        
        require_once 'views/front/aliments.php';
    }

    /**
     * Display aliments by category
     * 
     * @param int $categoryId Category ID
     */
    public function categoryAliments(int $categoryId)
    {
        require_login();
        
        $category = $this->model->getCategoryById($categoryId);
        if (!$category) {
            $_SESSION['error'] = "Catégorie introuvable.";
            redirect("index.php?page=aliments");
        }
        
        $aliments = $this->model->getAlimentsByCategory($categoryId);
        $categories = $this->model->getAllCategories();
        $currentCategory = $categoryId;
        $categoryName = $category['name'];
        $totalPages = 1;
        $currentPage = 1;
        
        require_once 'views/front/aliments.php';
    }

    /**
     * API endpoint for aliments (AJAX requests)
     * 
     * Returns JSON data for frontend filtering
     */
    public function apiGetAliments()
    {
        header('Content-Type: application/json');
        
        $category = trim($_GET['category'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        
        if (!empty($search)) {
            $aliments = $this->model->searchAliments($search);
        } elseif ($category !== 'all' && $category !== 'durable') {
            $aliments = $this->model->getAlimentsByCategory((int)$category);
        } elseif ($category === 'durable') {
            $aliments = $this->model->getDurableAliments();
        } else {
            $aliments = $this->model->getAllAliments();
        }
        
        echo json_encode([
            'success' => true,
            'data' => $aliments,
            'total' => count($aliments)
        ]);
        exit;
    }

    /**
     * Get category statistics (API)
     */
    public function apiGetCategoryStats()
    {
        header('Content-Type: application/json');
        
        $stats = $this->model->getCategoryStatistics();
        
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
        exit;
    }
}