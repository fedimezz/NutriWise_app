<?php
require_once 'Database.php';

class MenuModel {
    private $conn;
    private $table = 'plans';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllMenus() {
        $query = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS assigned_name,
                         (SELECT COUNT(*) FROM plan_meals pm WHERE pm.plan_id = p.id) AS meal_count
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.assigned_to = u.id
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMenuById($id) {
        $query = "SELECT p.*, CONCAT(u.prenom, ' ', u.nom) AS assigned_name
                  FROM " . $this->table . " p
                  LEFT JOIN users u ON p.assigned_to = u.id
                  WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMenuMeals($menuId) {
        $query = "SELECT * FROM plan_meals WHERE plan_id = :plan_id ORDER BY day_of_week ASC, FIELD(meal_type, 'breakfast', 'lunch', 'dinner', 'snack')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':plan_id' => $menuId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMenu($title, $goal, $caloriesTarget, $assignedTo, $createdBy, $isActive, $meals = []) {
        $query = "INSERT INTO " . $this->table . " (title, goal, calories_target, assigned_to, created_by, is_active, created_at)
                  VALUES (:title, :goal, :calories_target, :assigned_to, :created_by, :is_active, NOW())";
        $stmt = $this->conn->prepare($query);
        $success = $stmt->execute([
            ':title' => $title,
            ':goal' => $goal,
            ':calories_target' => $caloriesTarget,
            ':assigned_to' => $assignedTo,
            ':created_by' => $createdBy,
            ':is_active' => $isActive,
        ]);

        if(!$success) {
            return false;
        }

        $menuId = $this->conn->lastInsertId();
        $this->syncMenuMeals($menuId, $meals);
        return $menuId;  // Retourner l'ID du menu créé
    }

    public function updateMenu($id, $title, $goal, $caloriesTarget, $assignedTo, $isActive, $meals = []) {
        $query = "UPDATE " . $this->table . " SET title = :title, goal = :goal, calories_target = :calories_target, assigned_to = :assigned_to, is_active = :is_active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $success = $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':goal' => $goal,
            ':calories_target' => $caloriesTarget,
            ':assigned_to' => $assignedTo,
            ':is_active' => $isActive,
        ]);

        if($success) {
            $this->syncMenuMeals($id, $meals);
        }

        return $success;
    }

    public function deleteMenu($id) {
        $deleteMeals = $this->conn->prepare("DELETE FROM plan_meals WHERE plan_id = :id");
        $deleteMeals->execute([':id' => $id]);

        $deleteLink = $this->conn->prepare("DELETE FROM nutrition_plan_menus WHERE plan_id = :id");
        $deleteLink->execute([':id' => $id]);

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    public function getAllUsers() {
        $query = "SELECT id, prenom, nom FROM users ORDER BY prenom ASC, nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function syncMenuMeals($menuId, $meals) {
        $deleteQuery = "DELETE FROM plan_meals WHERE plan_id = :plan_id";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->execute([':plan_id' => $menuId]);

        if(empty($meals) || !is_array($meals)) {
            return true;
        }

        $insertQuery = "INSERT INTO plan_meals (plan_id, day_of_week, meal_type, description) VALUES (:plan_id, :day_of_week, :meal_type, :description)";
        $insertStmt = $this->conn->prepare($insertQuery);

        foreach($meals as $dayOfWeek => $mealTypes) {
            if(!is_array($mealTypes)) {
                continue;
            }
            foreach($mealTypes as $mealType => $description) {
                $description = trim($description);
                if($description === '') {
                    continue;
                }
                $insertStmt->execute([
                    ':plan_id' => $menuId,
                    ':day_of_week' => intval($dayOfWeek),
                    ':meal_type' => $mealType,
                    ':description' => $description,
                ]);
            }
        }

        return true;
    }
}
