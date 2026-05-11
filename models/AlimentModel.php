<?php
// models/AlimentModel.php
require_once 'Database.php';

class AlimentModel {
    private $conn;
    private $table = "aliments";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Récupérer tous les aliments AVEC le nom de la catégorie
    public function getAllAliments() {
        $query = "SELECT a.*, c.name as category_name 
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id 
                  ORDER BY a.nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAliments() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function addAliment($nom, $category_id, $calories, $proteines, $glucides, $lipides, $eco_score) {
        $query = "INSERT INTO " . $this->table . " (nom, category_id, calories, proteines, glucides, lipides, eco_score) 
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

    public function deleteAliment($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // Modifier un aliment (Correction des paramètres pour correspondre à la DB)
    public function updateAliment($id, $nom, $category_id, $calories, $proteines, $glucides, $lipides, $eco_score) {
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

    // Récupérer par ID AVEC le nom de la catégorie (pour éviter l'erreur Undefined Key category_name)
    public function getAlimentById($id) {
        $query = "SELECT a.*, c.name as category_name 
                  FROM " . $this->table . " a
                  LEFT JOIN categories c ON a.category_id = c.id 
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}