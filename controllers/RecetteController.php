<?php
// controllers/RecetteController.php

require_once 'models/RecetteModel.php';

class RecetteController {
    private $pdo;
    private $recetteModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->recetteModel = new RecetteModel();
    }

    /**
     * Afficher la liste des recettes pour l'admin
     */
    public function adminList() {
        // Vérifier les droits admin
        if (role_rank(current_user_role()) < role_rank(ROLE_ADMIN)) {
            redirect('index.php?page=home');
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
        $totalCategories = $this->getTotalCategoriesCount();

        $totalPages = ceil($totalRecettes / $perPage);
        $currentPage = $page;

        // CHEMIN CORRIGÉ
        require_once 'views/back/recettes.php';
    }

    /**
     * Ajouter une recette
     */
    public function adminAdd() {
        if (role_rank(current_user_role()) < role_rank(ROLE_ADMIN)) {
            redirect('index.php?page=home');
        }

        // Récupérer les ingrédients pour le formulaire
        $ingredients = $this->getAllIngredients();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            
            $nom = $_POST['nom'] ?? '';
            $description = $_POST['description'] ?? '';
            $categorie = $_POST['categorie'] ?? '';
            $difficulte = $_POST['difficulte'] ?? 'Facile';
            $temps_preparation = (int)($_POST['temps_preparation'] ?? 0);
            $temps_cuisson = (int)($_POST['temps_cuisson'] ?? 0);
            $portions = (int)($_POST['portions'] ?? 4);
            
            // Gestion de l'image
            $image = '';
            if (isset($_POST['image_type']) && $_POST['image_type'] === 'url' && !empty($_POST['image_url'])) {
                $image = $_POST['image_url'];
            } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'views/assets/uploads/recettes/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $image = $fileName;
                }
            }
            
            try {
                $sql = "INSERT INTO recettes (nom, description, categorie, difficulte, temps_preparation, temps_cuisson, portions, image, date_creation) 
                        VALUES (:nom, :description, :categorie, :difficulte, :temps_preparation, :temps_cuisson, :portions, :image, NOW())";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':description' => $description,
                    ':categorie' => $categorie,
                    ':difficulte' => $difficulte,
                    ':temps_preparation' => $temps_preparation,
                    ':temps_cuisson' => $temps_cuisson,
                    ':portions' => $portions,
                    ':image' => $image
                ]);
                
                $_SESSION['success'] = "Recette ajoutée avec succès !";
                redirect('index.php?page=admin_recettes');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erreur : " . $e->getMessage();
            }
        }
        
        // CHEMIN CORRIGÉ
        require_once 'views/back/add_recette.php';
    }

    /**
     * Modifier une recette
     */
    public function adminEdit() {
        if (role_rank(current_user_role()) < role_rank(ROLE_ADMIN)) {
            redirect('index.php?page=home');
        }
        
        $id = (int)($_GET['id'] ?? 0);
        $recette = $this->recetteModel->getRecetteById($id);
        
        if (!$recette) {
            $_SESSION['error'] = "Recette non trouvée";
            redirect('index.php?page=admin_recettes');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            
            $nom = $_POST['nom'] ?? '';
            $description = $_POST['description'] ?? '';
            $categorie = $_POST['categorie'] ?? '';
            $difficulte = $_POST['difficulte'] ?? 'Facile';
            $temps_preparation = (int)($_POST['temps_preparation'] ?? 0);
            $temps_cuisson = (int)($_POST['temps_cuisson'] ?? 0);
            $portions = (int)($_POST['portions'] ?? 4);
            
            $image = $recette['image'];
            
            // Gestion du changement d'image
            if (isset($_POST['image_type'])) {
                if ($_POST['image_type'] === 'url' && !empty($_POST['image_url'])) {
                    $image = $_POST['image_url'];
                } elseif ($_POST['image_type'] === 'upload' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'views/assets/uploads/recettes/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = time() . '_' . basename($_FILES['image']['name']);
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $image = $fileName;
                    }
                }
                // Si image_type === 'keep', on garde l'image existante
            }
            
            try {
                $sql = "UPDATE recettes SET nom = :nom, description = :description, categorie = :categorie, 
                        difficulte = :difficulte, temps_preparation = :temps_preparation, temps_cuisson = :temps_cuisson, 
                        portions = :portions, image = :image WHERE id = :id";
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
            }
        }
        
        // CHEMIN CORRIGÉ
        require_once 'views/back/recette_edit.php';
    }

    /**
     * Supprimer une recette
     */
    public function adminDelete() {
        if (role_rank(current_user_role()) < role_rank(ROLE_ADMIN)) {
            redirect('index.php?page=home');
        }
        
        $id = (int)($_GET['id'] ?? 0);
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM recettes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['success'] = "Recette supprimée avec succès !";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
        }
        
        redirect('index.php?page=admin_recettes');
    }

    /**
     * Compter le nombre total d'ingrédients utilisés dans les recettes
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
     * Compter le nombre de catégories
     */
    private function getTotalCategoriesCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(DISTINCT categorie) as total FROM recettes WHERE categorie IS NOT NULL AND categorie != ''");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Récupérer tous les ingrédients
     */
    private function getAllIngredients() {
        try {
            $stmt = $this->pdo->query("SELECT id, nom FROM aliments ORDER BY nom");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>