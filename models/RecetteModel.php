<?php
// models/RecetteModel.php

require_once 'Database.php';

class RecetteModel {
    private $conn;
    private $table = "recettes";

    public function __construct() {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }

    /**
     * Récupérer toutes les recettes
     */
    public function getAllRecettes($search = null, $categorie = null, $page = 1, $perPage = 12) {
        try {
            $sql = "SELECT r.*, 
                    COUNT(DISTINCT ri.id) as nb_ingredients,
                    COUNT(DISTINCT re.id) as nb_etapes
                    FROM " . $this->table . " r
                    LEFT JOIN recette_ingredients ri ON r.id = ri.recette_id
                    LEFT JOIN recette_etapes re ON r.id = re.recette_id";
            
            $conditions = [];
            $params = [];
            
            if ($search && $search !== '') {
                $conditions[] = "(r.nom LIKE :search OR r.description LIKE :search)";
                $params[':search'] = "%{$search}%";
            }
            
            if ($categorie && $categorie !== 'all' && $categorie !== '') {
                $conditions[] = "r.categorie = :categorie";
                $params[':categorie'] = $categorie;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $sql .= " GROUP BY r.id ORDER BY r.date_creation DESC";
            
            // Pagination
            $offset = ($page - 1) * $perPage;
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $perPage;
            $params[':offset'] = $offset;
            
            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $value) {
                if ($key === ':limit' || $key === ':offset') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllRecettes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Compter le nombre total de recettes
     */
    public function countRecettes($search = null, $categorie = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . $this->table;
            $conditions = [];
            $params = [];
            
            if ($search && $search !== '') {
                $conditions[] = "(nom LIKE :search OR description LIKE :search)";
                $params[':search'] = "%{$search}%";
            }
            
            if ($categorie && $categorie !== 'all' && $categorie !== '') {
                $conditions[] = "categorie = :categorie";
                $params[':categorie'] = $categorie;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Erreur countRecettes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer une recette par ID
     */
    public function getRecetteById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getRecetteById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer les ingrédients d'une recette
     */
    public function getIngredientsByRecetteId($recetteId) {
        try {
            $query = "SELECT ri.*, i.nom, i.calories, i.proteines, i.glucides, i.lipides
                      FROM recette_ingredients ri
                      JOIN ingredients i ON ri.ingredient_id = i.id
                      WHERE ri.recette_id = :recette_id
                      ORDER BY ri.id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':recette_id' => $recetteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getIngredientsByRecetteId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les étapes d'une recette
     */
    public function getEtapesByRecetteId($recetteId) {
        try {
            $query = "SELECT * FROM recette_etapes 
                      WHERE recette_id = :recette_id 
                      ORDER BY numero_etape";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':recette_id' => $recetteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getEtapesByRecetteId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ajouter une recette aux favoris
     */
    public function addToFavorites($userId, $recetteId) {
        try {
            $query = "INSERT INTO recette_favoris (user_id, recette_id) VALUES (:user_id, :recette_id)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':user_id' => $userId, ':recette_id' => $recetteId]);
        } catch (PDOException $e) {
            error_log("Erreur addToFavorites: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retirer une recette des favoris
     */
    public function removeFromFavorites($userId, $recetteId) {
        try {
            $query = "DELETE FROM recette_favoris WHERE user_id = :user_id AND recette_id = :recette_id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':user_id' => $userId, ':recette_id' => $recetteId]);
        } catch (PDOException $e) {
            error_log("Erreur removeFromFavorites: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si une recette est dans les favoris
     */
    public function isFavorite($userId, $recetteId) {
        try {
            $query = "SELECT id FROM recette_favoris WHERE user_id = :user_id AND recette_id = :recette_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':user_id' => $userId, ':recette_id' => $recetteId]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
    

    /**
     * Incrémenter le compteur de vues
     */
    public function incrementViews($recetteId) {
        try {
            $query = "UPDATE " . $this->table . " SET views = views + 1 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':id' => $recetteId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>