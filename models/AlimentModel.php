<?php
/**
 * Aliment (Food item) Model
 * 
 * Handles database operations for food items/aliments.
 * Manages CRUD operations and category relationships.
 * 
 * @package NutriWise
 * @subpackage Models
 */

require_once 'Database.php';

class AlimentModel
{
    /** @var PDO Database connection */
    private PDO $conn;

    /** @var string Table name */
    private string $table = "aliments";

    /**
     * Constructor - Initialize database connection
     */
    public function __construct()
    {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }

    /**
     * Retrieve all food items with category names
     * 
     * @return array Array of all aliments with category details
     */
    public function getAllAliments(): array
    {
        $query = "SELECT a.*, c.name as category_name
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  ORDER BY a.nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total number of food items
     * 
     * @return int Total count of aliments
     */
    public function countAliments(): int
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get aliments with pagination
     * 
     * @param int $page Page number (starting from 1)
     * @param int $perPage Items per page
     * @return array Array with 'data' and 'total' keys
     */
    public function getAlimentsPaginated(int $page = 1, int $perPage = 12): array
    {
        // S'assurer que la page est au moins 1
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        
        try {
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM " . $this->table;
            $stmt = $this->conn->prepare($countQuery);
            $stmt->execute();
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get paginated data
            $query = "SELECT a.*, c.name as category_name
                      FROM " . $this->table . " a
                      LEFT JOIN categories c ON a.category_id = c.id
                      ORDER BY a.nom ASC
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'data' => $data,
                'total' => (int)$total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => (int)ceil($total / $perPage)
            ];
        } catch (PDOException $e) {
            error_log("Erreur getAlimentsPaginated: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => 0
            ];
        }
    }

    /**
     * Add a new food item
     * 
     * @param string $nom Name of the food item
     * @param int $category_id Category ID
     * @param float $calories Calories per 100g
     * @param float $proteines Protein content (g)
     * @param float $glucides Carbohydrates (g)
     * @param float $lipides Fats (g)
     * @param float $eco_score Eco-score rating
     * @param string|null $image Image filename
     * @param int $durable Durable flag
     * @return bool True on success, false on failure
     */
    public function addAliment(
        string $nom,
        int $category_id,
        float $calories,
        float $proteines,
        float $glucides,
        float $lipides,
        float $eco_score,
        ?string $image = null,
        int $durable = 0
    ): bool {
        $query = "INSERT INTO " . $this->table . "
                  (nom, category_id, calories, proteines, glucides, lipides, eco_score, image, durable)
                  VALUES (:nom, :cat_id, :cal, :prot, :glu, :lip, :eco, :image, :durable)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nom' => $nom,
            ':cat_id' => $category_id,
            ':cal' => $calories,
            ':prot' => $proteines,
            ':glu' => $glucides,
            ':lip' => $lipides,
            ':eco' => $eco_score,
            ':image' => $image,
            ':durable' => $durable,
        ]);
    }

    /**
     * Delete a food item by ID
     * 
     * @param int $id Aliment ID to delete
     * @return bool True on success, false on failure
     */
    public function deleteAliment(int $id): bool
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Update food item details
     * 
     * @param int $id Aliment ID to update
     * @param string $nom Name of the food item
     * @param int $category_id Category ID
     * @param float $calories Calories per 100g
     * @param float $proteines Protein content (g)
     * @param float $glucides Carbohydrates (g)
     * @param float $lipides Fats (g)
     * @param float $eco_score Eco-score rating
     * @param string|null $image Image filename
     * @param int $durable Durable flag
     * @return bool True on success, false on failure
     */
    public function updateAliment(
        int $id,
        string $nom,
        int $category_id,
        float $calories,
        float $proteines,
        float $glucides,
        float $lipides,
        float $eco_score,
        ?string $image = null,
        int $durable = 0
    ): bool {
        $query = "UPDATE " . $this->table . "
                  SET nom = :nom, category_id = :cat_id, calories = :cal,
                      proteines = :prot, glucides = :glu, lipides = :lip, eco_score = :eco,
                      durable = :durable";
        if ($image !== null) {
            $query .= ", image = :image";
        }
        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $params = [
            ':id' => $id,
            ':nom' => $nom,
            ':cat_id' => $category_id,
            ':cal' => $calories,
            ':prot' => $proteines,
            ':glu' => $glucides,
            ':lip' => $lipides,
            ':eco' => $eco_score,
            ':durable' => $durable,
        ];

        if ($image !== null) {
            $params[':image'] = $image;
        }

        return $stmt->execute($params);
    }

    /**
     * Retrieve a single food item by ID with category name
     * 
     * @param int $id Aliment ID to retrieve
     * @return array|false Aliment data with category, or false if not found
     */
    public function getAlimentById(int $id)
    {
        $query = "SELECT a.*, c.name as category_name
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get related aliments (same category or random)
     * 
     * @param int $excludeId ID of current aliment to exclude
     * @param int|null $categoryId Category ID to filter by
     * @param int $limit Maximum number of related aliments
     * @return array Array of related aliments
     */
    public function getRelatedAliments(int $excludeId, ?int $categoryId = null, int $limit = 4): array
    {
        try {
            if ($categoryId !== null) {
                $query = "SELECT a.*, c.name as category_name
                          FROM " . $this->table . " a
                          LEFT JOIN categories c ON a.category_id = c.id
                          WHERE a.id != :exclude AND a.category_id = :cat_id
                          ORDER BY RAND()
                          LIMIT :limit";
                $stmt = $this->conn->prepare($query);
                $stmt->bindValue(':exclude', $excludeId, PDO::PARAM_INT);
                $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($results)) {
                    return $results;
                }
            }

            $query = "SELECT a.*, c.name as category_name
                      FROM " . $this->table . " a
                      LEFT JOIN categories c ON a.category_id = c.id
                      WHERE a.id != :exclude
                      ORDER BY RAND()
                      LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':exclude', $excludeId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getRelatedAliments: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all food categories
     * 
     * @return array Array of all categories sorted by name
     */
    public function getAllCategories(): array
    {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get aliments by category ID
     * 
     * @param int $categoryId Category ID to filter by
     * @return array Array of aliments in the category
     */
    public function getAlimentsByCategory(int $categoryId): array
    {
        $query = "SELECT a.*, c.name as category_name
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  WHERE a.category_id = :category_id
                  ORDER BY a.nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get durable aliments (eco_score >= 7)
     * 
     * @param int $limit Maximum number of results
     * @return array Array of durable aliments
     */
    public function getDurableAliments(int $limit = 20): array
    {
        $query = "SELECT a.*, c.name as category_name
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  WHERE a.eco_score >= 7
                  ORDER BY a.eco_score DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search aliments by name
     * 
     * @param string $search Search term
     * @return array Array of matching aliments
     */
    public function searchAliments(string $search): array
    {
        $query = "SELECT a.*, c.name as category_name
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id
                  WHERE a.nom LIKE :search
                  ORDER BY a.nom ASC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$search}%";
        $stmt->execute([':search' => $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get category by ID with details
     * 
     * @param int $categoryId Category ID
     * @return array|false Category data or false if not found
     */
    public function getCategoryById(int $categoryId)
    {
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get category statistics (number of aliments per category)
     * 
     * @return array Array with category names and counts
     */
    public function getCategoryStatistics(): array
    {
        $query = "SELECT c.name, c.icon, COUNT(a.id) as aliment_count
                  FROM categories c
                  LEFT JOIN " . $this->table . " a ON c.id = a.category_id
                  GROUP BY c.id
                  ORDER BY aliment_count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>