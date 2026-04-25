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
     * Joins with categories table to include category information
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
     * Add a new food item
     * 
     * @param string $nom Name of the food item
     * @param int $category_id Category ID
     * @param float $calories Calories per 100g
     * @param float $proteines Protein content (g)
     * @param float $glucides Carbohydrates (g)
     * @param float $lipides Fats (g)
     * @param string $eco_score Eco-score rating
     * 
     * @return bool True on success, false on failure
     */
    public function addAliment(
        string $nom,
        int $category_id,
        float $calories,
        float $proteines,
        float $glucides,
        float $lipides,
        string $eco_score
    ): bool {
        $query = "INSERT INTO " . $this->table . "
                  (nom, category_id, calories, proteines, glucides, lipides, eco_score)
                  VALUES (:nom, :cat_id, :cal, :prot, :glu, :lip, :eco)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nom' => $nom,
            ':cat_id' => $category_id,
            ':cal' => $calories,
            ':prot' => $proteines,
            ':glu' => $glucides,
            ':lip' => $lipides,
            ':eco' => $eco_score
        ]);
    }

    /**
     * Delete a food item by ID
     * 
     * @param int $id Aliment ID to delete
     * 
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
     * @param string $eco_score Eco-score rating
     * 
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
        string $eco_score
    ): bool {
        $query = "UPDATE " . $this->table . "
                  SET nom = :nom, category_id = :cat_id, calories = :cal,
                      proteines = :prot, glucides = :glu, lipides = :lip, eco_score = :eco
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':cat_id' => $category_id,
            ':cal' => $calories,
            ':prot' => $proteines,
            ':glu' => $glucides,
            ':lip' => $lipides,
            ':eco' => $eco_score
        ]);
    }

    /**
     * Retrieve a single food item by ID with category name
     * 
     * @param int $id Aliment ID to retrieve
     * 
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
}
?>