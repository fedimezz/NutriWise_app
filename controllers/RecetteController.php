<?php
// controllers/RecetteController.php

require_once 'models/RecetteModel.php';

class RecetteController {
    private $pdo;
    private $recetteModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->recetteModel = new RecetteModel($pdo);
    }
    
    /**
     * Afficher la liste des recettes (admin)
     */
    public function adminList() {
        require_role(['admin', 'owner']);
        
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $search = $_GET['search'] ?? '';
        $perPage = 10;
        
        $recettesList = $this->recetteModel->getAllRecettes($search, null, $page, $perPage);
        $totalRecettes = $this->recetteModel->countRecettes($search, null);
        $totalPages = ceil($totalRecettes / $perPage);
        $currentPage = $page;
        
        // Calculer le nombre total d'ingrédients
        $totalIngredients = $this->getTotalIngredientsCount();
        
        require_once 'views/back/recettes.php';
    }
    
    /**
     * Ajouter une recette (admin) - AVEC NOTIFICATION
     */
    public function adminAdd() {
        require_role(['admin', 'owner']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categorie = $_POST['categorie'] ?? '';
            $difficulte = $_POST['difficulte'] ?? 'Facile';
            $temps_preparation = (int)($_POST['temps_preparation'] ?? 0);
            $temps_cuisson = (int)($_POST['temps_cuisson'] ?? 0);
            $portions = (int)($_POST['portions'] ?? 4);
            
            if (empty($nom)) {
                $_SESSION['error'] = "Le nom de la recette est obligatoire";
                require_once 'views/back/recette_add.php';
                return;
            }
            
            // Gestion de l'image
            $image = null;
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
            
            try {
                $this->pdo->beginTransaction();
                
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
                
                $recetteId = $this->pdo->lastInsertId();
                
                // CRÉER LES NOTIFICATIONS POUR TOUS LES UTILISATEURS
                $this->recetteModel->createRecetteNotification($recetteId, current_user_id());
                
                $this->pdo->commit();
                
                $_SESSION['success'] = "Recette ajoutée avec succès ! Une notification a été envoyée à tous les utilisateurs.";
                redirect('index.php?page=admin_recettes');
                
            } catch (PDOException $e) {
                $this->pdo->rollBack();
                $_SESSION['error'] = "Erreur : " . $e->getMessage();
                require_once 'views/back/recette_add.php';
            }
        } else {
            require_once 'views/back/recette_add.php';
        }
    }
    
    /**
     * Modifier une recette (admin)
     */
    public function adminEdit() {
        require_role(['admin', 'owner']);
        
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
            csrf_check();
            
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categorie = $_POST['categorie'] ?? '';
            $difficulte = $_POST['difficulte'] ?? 'Facile';
            $temps_preparation = (int)($_POST['temps_preparation'] ?? 0);
            $temps_cuisson = (int)($_POST['temps_cuisson'] ?? 0);
            $portions = (int)($_POST['portions'] ?? 4);
            
            if (empty($nom)) {
                $_SESSION['error'] = "Le nom de la recette est obligatoire";
                require_once 'views/back/recette_edit.php';
                return;
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
                require_once 'views/back/recette_edit.php';
            }
        } else {
            require_once 'views/back/recette_edit.php';
        }
    }
    
    /**
     * Supprimer une recette (admin)
     */
    public function adminDelete() {
        require_role(['admin', 'owner']);
        
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id > 0) {
            try {
                // Supprimer les dépendances
                $this->pdo->prepare("DELETE FROM recette_ingredients WHERE recette_id = ?")->execute([$id]);
                $this->pdo->prepare("DELETE FROM recette_etapes WHERE recette_id = ?")->execute([$id]);
                $this->pdo->prepare("DELETE FROM recette_favoris WHERE recette_id = ?")->execute([$id]);
                $this->pdo->prepare("DELETE FROM recettes WHERE id = ?")->execute([$id]);
                
                $_SESSION['success'] = "Recette supprimée avec succès !";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
            }
        }
        
        redirect('index.php?page=admin_recettes');
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
}
?>