<?php
require_once 'Database.php';

class PlanningModel {
    private $conn;
    private $table = 'nutrition_plans';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllPlannings() {
        $query = "SELECT n.*, CONCAT(u.prenom, ' ', u.nom) AS user_name,
                         (SELECT COUNT(*) FROM nutrition_plan_menus npm WHERE npm.nutrition_plan_id = n.id) AS menu_count
                  FROM " . $this->table . " n
                  LEFT JOIN users u ON n.user_id = u.id
                  ORDER BY n.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlanningsByUser($userId) {
        $query = "SELECT n.*, CONCAT(u.prenom, ' ', u.nom) AS user_name,
                         (SELECT COUNT(*) FROM nutrition_plan_menus npm WHERE npm.nutrition_plan_id = n.id) AS menu_count
                  FROM " . $this->table . " n
                  LEFT JOIN users u ON n.user_id = u.id
                  WHERE n.user_id = :user_id
                  ORDER BY n.start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlanningById($id) {
        $query = "SELECT n.*, CONCAT(u.prenom, ' ', u.nom) AS user_name,
                         CONCAT(nut.prenom, ' ', nut.nom) AS nutritionist_name
                  FROM " . $this->table . " n
                  LEFT JOIN users u ON n.user_id = u.id
                  LEFT JOIN users nut ON n.nutritionist_id = nut.id
                  WHERE n.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPlanningMenus($planningId) {
        $query = "SELECT p.*
                  FROM nutrition_plan_menus npm
                  INNER JOIN plans p ON npm.plan_id = p.id
                  WHERE npm.nutrition_plan_id = :planning_id
                  ORDER BY npm.position ASC, p.title ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':planning_id' => $planningId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPlanning($name, $description, $startDate, $endDate, $userId, $nutritionistId, $status, $menuIds = []) {
        $query = "INSERT INTO " . $this->table . " (name, description, start_date, end_date, user_id, nutritionist_id, status, created_at)
                  VALUES (:name, :description, :start_date, :end_date, :user_id, :nutritionist_id, :status, NOW())";
        $stmt = $this->conn->prepare($query);
        $success = $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':user_id' => $userId,
            ':nutritionist_id' => $nutritionistId,
            ':status' => $status,
        ]);

        if(!$success) {
            return false;
        }

        $planningId = $this->conn->lastInsertId();
        $this->syncPlanningMenus($planningId, $menuIds);
        return $planningId;  // Retourner l'ID du planning créé
    }

    public function updatePlanning($id, $name, $description, $startDate, $endDate, $userId, $status, $menuIds = []) {
        $query = "UPDATE " . $this->table . " SET name = :name, description = :description, start_date = :start_date,
                  end_date = :end_date, user_id = :user_id, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $success = $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':description' => $description,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':user_id' => $userId,
            ':status' => $status,
        ]);

        if($success) {
            $this->syncPlanningMenus($id, $menuIds);
        }

        return $success;
    }

    public function deletePlanning($id) {
        $deleteLinks = $this->conn->prepare("DELETE FROM nutrition_plan_menus WHERE nutrition_plan_id = :id");
        $deleteLinks->execute([':id' => $id]);

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

    public function getAllMenus() {
        $query = "SELECT id, title FROM plans ORDER BY title ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function syncPlanningMenus($planningId, $menuIds) {
        $deleteQuery = "DELETE FROM nutrition_plan_menus WHERE nutrition_plan_id = :planning_id";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->execute([':planning_id' => $planningId]);

        if(empty($menuIds) || !is_array($menuIds)) {
            return true;
        }

        $insertQuery = "INSERT INTO nutrition_plan_menus (nutrition_plan_id, plan_id, position) VALUES (:planning_id, :plan_id, :position)";
        $insertStmt = $this->conn->prepare($insertQuery);
        $position = 1;
        foreach($menuIds as $menuId) {
            $insertStmt->execute([
                ':planning_id' => $planningId,
                ':plan_id' => intval($menuId),
                ':position' => $position++,
            ]);
        }

        return true;
    }
}
