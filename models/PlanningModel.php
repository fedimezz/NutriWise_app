<?php
// models/PlanningModel.php

require_once 'Database.php';

class PlanningModel {
    private $conn;
    private $table = "nutrition_plans";
    
    public function __construct() {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }
    
    /**
     * Ajouter un nouveau planning
     */
   /**
 * Ajouter un planning PUBLIC (pour tous les utilisateurs)
 */
public function addPublicPlanning($name, $description, $startDate, $endDate, $createdBy, $status, $menuIds = []) {
    try {
        $this->conn->beginTransaction();
        
        // user_id = NULL pour indiquer que c'est un planning public
        $sql = "INSERT INTO " . $this->table . " (name, description, user_id, nutritionist_id, created_by, start_date, end_date, status, created_at, updated_at) 
                VALUES (:name, :description, NULL, :nutritionist_id, :created_by, :start_date, :end_date, :status, NOW(), NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':nutritionist_id' => $createdBy,
            ':created_by' => $createdBy,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':status' => $status
        ]);
        
        $planningId = $this->conn->lastInsertId();
        
        // Ajouter les menus
        foreach ($menuIds as $menuId) {
            $this->addMenuToPlanning($planningId, $menuId, 1, 1);
        }
        
        // CRÉER LES NOTIFICATIONS POUR TOUS LES UTILISATEURS
        $this->createPlanningNotification($planningId, $createdBy);
        
        $this->conn->commit();
        return $planningId;
    } catch (PDOException $e) {
        $this->conn->rollBack();
        error_log("Erreur addPublicPlanning: " . $e->getMessage());
        return false;
    }
}
    
    /**
     * Récupérer un planning par ID avec tous ses détails
     */
    public function getPlanningById($id) {
        try {
            $sql = "SELECT np.*, u.prenom, u.nom, u.email 
                    FROM " . $this->table . " np
                    LEFT JOIN users u ON np.user_id = u.id
                    WHERE np.id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $planning = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($planning) {
                $sqlMenus = "SELECT pm.id, pm.menu_id, pm.jour, pm.ordre, m.name as menu_name, m.description as menu_description
                             FROM planning_menus pm
                             JOIN menus m ON pm.menu_id = m.id
                             WHERE pm.planning_id = :planning_id
                             ORDER BY pm.jour, pm.ordre";
                $stmtMenus = $this->conn->prepare($sqlMenus);
                $stmtMenus->execute([':planning_id' => $id]);
                $menus = $stmtMenus->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($menus as &$menu) {
                    $sqlAliments = "SELECT ma.id, ma.menu_id, ma.aliment_id, ma.quantite, 
                                           a.nom, a.calories, a.proteines, a.glucides, a.lipides, a.eco_score
                                    FROM menu_aliments ma
                                    JOIN aliments a ON ma.aliment_id = a.id
                                    WHERE ma.menu_id = :menu_id";
                    $stmtAliments = $this->conn->prepare($sqlAliments);
                    $stmtAliments->execute([':menu_id' => $menu['menu_id']]);
                    $menu['aliments'] = $stmtAliments->fetchAll(PDO::FETCH_ASSOC);
                    
                    $menu['total_calories'] = 0;
                    $menu['total_proteines'] = 0;
                    $menu['total_glucides'] = 0;
                    $menu['total_lipides'] = 0;
                    
                    foreach ($menu['aliments'] as $aliment) {
                        $ratio = $aliment['quantite'] / 100;
                        $menu['total_calories'] += $aliment['calories'] * $ratio;
                        $menu['total_proteines'] += $aliment['proteines'] * $ratio;
                        $menu['total_glucides'] += $aliment['glucides'] * $ratio;
                        $menu['total_lipides'] += $aliment['lipides'] * $ratio;
                    }
                }
                
                $planning['menus'] = $menus;
            }
            
            return $planning;
        } catch (PDOException $e) {
            error_log("Erreur getPlanningById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupérer TOUS les plannings disponibles pour un utilisateur
     * (ceux créés pour lui + ceux créés par admin pour tous)
     */
  /**
 * Récupérer TOUS les plannings disponibles (pour tous les utilisateurs)
 */
/**
 * Récupérer TOUS les plannings disponibles (publics + personnels)
 */
public function getUserPlannings($userId, $page = 1, $perPage = 10) {
    try {
        $offset = ($page - 1) * $perPage;
        
        // Afficher les plannings publics (user_id IS NULL) 
        // ET les plannings personnels de l'utilisateur
        $sql = "SELECT np.*, 
                       u.prenom, u.nom,
                       (SELECT COUNT(*) FROM planning_menus WHERE planning_id = np.id) as menus_count,
                       (SELECT COUNT(*) FROM planning_likes WHERE planning_id = np.id) as total_likes
                FROM " . $this->table . " np
                LEFT JOIN users u ON np.user_id = u.id
                WHERE (np.user_id IS NULL OR np.user_id = :user_id)
                  AND np.status IN ('active', 'draft')
                GROUP BY np.id
                ORDER BY np.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $plannings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Compter le total
        $countSql = "SELECT COUNT(*) as total FROM " . $this->table . " 
                     WHERE (user_id IS NULL OR user_id = :user_id)
                       AND status IN ('active', 'draft')";
        $countStmt = $this->conn->prepare($countSql);
        $countStmt->execute([':user_id' => $userId]);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return [
            'plannings' => $plannings,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    } catch (PDOException $e) {
        error_log("Erreur getUserPlannings: " . $e->getMessage());
        return ['plannings' => [], 'total' => 0, 'page' => 1, 'perPage' => 10, 'totalPages' => 0];
    }
}
    /**
     * Récupérer tous les plannings (admin)
     */
    public function getAllPlannings($page = 1, $perPage = 10, $filters = []) {
        try {
            $offset = ($page - 1) * $perPage;
            
            $sql = "SELECT np.*, u.prenom, u.nom, u.email,
                           (SELECT COUNT(*) FROM planning_menus WHERE planning_id = np.id) as menus_count
                    FROM " . $this->table . " np
                    LEFT JOIN users u ON np.user_id = u.id
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($filters['user_id'])) {
                $sql .= " AND np.user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND np.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            $sql .= " ORDER BY np.start_date DESC, np.created_at DESC
                      LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $plannings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $countSql = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
            if (!empty($filters['user_id'])) {
                $countSql .= " AND user_id = :user_id";
            }
            if (!empty($filters['status'])) {
                $countSql .= " AND status = :status";
            }
            
            $countStmt = $this->conn->prepare($countSql);
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return [
                'plannings' => $plannings,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ];
        } catch (PDOException $e) {
            error_log("Erreur getAllPlannings: " . $e->getMessage());
            return ['plannings' => [], 'total' => 0, 'page' => 1, 'perPage' => 10, 'totalPages' => 0];
        }
    }
    
    /**
     * Mettre à jour un planning
     */
    public function updatePlanning($id, $name, $description, $startDate, $endDate, $status, $menuIds = []) {
        try {
            $this->conn->beginTransaction();
            
            $sql = "UPDATE " . $this->table . " 
                    SET name = :name, 
                        description = :description, 
                        start_date = :start_date, 
                        end_date = :end_date, 
                        status = :status,
                        updated_at = NOW()
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':description' => $description,
                ':start_date' => $startDate,
                ':end_date' => $endDate,
                ':status' => $status
            ]);
            
            $sqlDelete = "DELETE FROM planning_menus WHERE planning_id = :planning_id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->execute([':planning_id' => $id]);
            
            foreach ($menuIds as $menuId) {
                $this->addMenuToPlanning($id, $menuId, 1, 1);
            }
            
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erreur updatePlanning: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprimer un planning
     */
    public function deletePlanning($id) {
        try {
            $this->conn->beginTransaction();
            
            $sqlDeleteMenus = "DELETE FROM planning_menus WHERE planning_id = :planning_id";
            $stmtDeleteMenus = $this->conn->prepare($sqlDeleteMenus);
            $stmtDeleteMenus->execute([':planning_id' => $id]);
            
            $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erreur deletePlanning: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ajouter un menu à un planning
     */
    public function addMenuToPlanning($planningId, $menuId, $jour, $ordre) {
        try {
            $sql = "INSERT INTO planning_menus (planning_id, menu_id, jour, ordre) 
                    VALUES (:planning_id, :menu_id, :jour, :ordre)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':planning_id' => $planningId,
                ':menu_id' => $menuId,
                ':jour' => $jour,
                ':ordre' => $ordre
            ]);
        } catch (PDOException $e) {
            error_log("Erreur addMenuToPlanning: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Retirer un menu d'un planning
     */
    public function removeMenuFromPlanning($planningId, $menuId) {
        try {
            $sql = "DELETE FROM planning_menus WHERE planning_id = :planning_id AND menu_id = :menu_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':planning_id' => $planningId,
                ':menu_id' => $menuId
            ]);
        } catch (PDOException $e) {
            error_log("Erreur removeMenuFromPlanning: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les plannings dans une plage de dates
     */
    public function getPlanningsByDateRange($startDate, $endDate, $userId = null) {
        try {
            $sql = "SELECT np.*, u.prenom, u.nom
                    FROM " . $this->table . " np
                    LEFT JOIN users u ON np.user_id = u.id
                    WHERE np.start_date <= :end_date AND np.end_date >= :start_date";
            
            $params = [
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ];
            
            if ($userId) {
                $sql .= " AND np.user_id = :user_id";
                $params[':user_id'] = $userId;
            }
            
            $sql .= " ORDER BY np.start_date";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getPlanningsByDateRange: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter le nombre total de plannings
     */
    public function countPlannings($userId = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . $this->table;
            $params = [];
            
            if ($userId) {
                $sql .= " WHERE user_id = :user_id";
                $params[':user_id'] = $userId;
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            error_log("Erreur countPlannings: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Récupérer tous les utilisateurs
     */
    public function getAllUsers() {
        try {
            $sql = "SELECT id, prenom, nom, email, role FROM users ORDER BY prenom, nom";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllUsers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer tous les menus disponibles
     */
    public function getAllMenus() {
        try {
            $sql = "SELECT m.*, u.prenom, u.nom as creator_name
                    FROM menus m
                    LEFT JOIN users u ON m.user_id = u.id
                    ORDER BY m.name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($menus as &$menu) {
                $sqlAliments = "SELECT ma.*, a.calories, a.proteines, a.glucides, a.lipides
                               FROM menu_aliments ma
                               JOIN aliments a ON ma.aliment_id = a.id
                               WHERE ma.menu_id = :menu_id";
                $stmtAliments = $this->conn->prepare($sqlAliments);
                $stmtAliments->execute([':menu_id' => $menu['id']]);
                $aliments = $stmtAliments->fetchAll(PDO::FETCH_ASSOC);
                
                $menu['total_calories'] = 0;
                $menu['total_proteines'] = 0;
                $menu['total_glucides'] = 0;
                $menu['total_lipides'] = 0;
                
                foreach ($aliments as $aliment) {
                    $ratio = $aliment['quantite'] / 100;
                    $menu['total_calories'] += $aliment['calories'] * $ratio;
                    $menu['total_proteines'] += $aliment['proteines'] * $ratio;
                    $menu['total_glucides'] += $aliment['glucides'] * $ratio;
                    $menu['total_lipides'] += $aliment['lipides'] * $ratio;
                }
                
                $menu['aliments_count'] = count($aliments);
            }
            
            return $menus;
        } catch (PDOException $e) {
            error_log("Erreur getAllMenus: " . $e->getMessage());
            return [];
        }
    }
    
    // ========================================
    // GESTION DES LIKES
    // ========================================
    
    public function isPlanningLiked($planningId, $userId) {
        try {
            $sql = "SELECT id FROM planning_likes WHERE planning_id = :planning_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':planning_id' => $planningId, ':user_id' => $userId]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function addLike($planningId, $userId) {
        try {
            $sql = "INSERT INTO planning_likes (planning_id, user_id, created_at) VALUES (:planning_id, :user_id, NOW())";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':planning_id' => $planningId, ':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur addLike: " . $e->getMessage());
            return false;
        }
    }
    
    public function removeLike($planningId, $userId) {
        try {
            $sql = "DELETE FROM planning_likes WHERE planning_id = :planning_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':planning_id' => $planningId, ':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur removeLike: " . $e->getMessage());
            return false;
        }
    }
    
    public function getLikeCount($planningId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM planning_likes WHERE planning_id = :planning_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':planning_id' => $planningId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function getUserLikedPlannings($userId) {
        try {
            $sql = "SELECT planning_id FROM planning_likes WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // ========================================
    // GESTION DES FAVORIS
    // ========================================
    
    public function isInFavorites($planningId, $userId) {
        try {
            $sql = "SELECT id FROM user_planning_favorites WHERE planning_id = :planning_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':planning_id' => $planningId, ':user_id' => $userId]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function addToFavorites($planningId, $userId) {
        try {
            if ($this->isInFavorites($planningId, $userId)) return true;
            $sql = "INSERT INTO user_planning_favorites (planning_id, user_id, created_at) VALUES (:planning_id, :user_id, NOW())";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':planning_id' => $planningId, ':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur addToFavorites: " . $e->getMessage());
            return false;
        }
    }
    
    public function removeFromFavorites($planningId, $userId) {
        try {
            $sql = "DELETE FROM user_planning_favorites WHERE planning_id = :planning_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':planning_id' => $planningId, ':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur removeFromFavorites: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserFavoritesIds($userId) {
        try {
            $sql = "SELECT planning_id FROM user_planning_favorites WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // ========================================
    // PLANIFICATION PERSONNALISÉE
    // ========================================
    
    public function schedulePlanning($planningId, $userId, $startDate, $endDate = null) {
        try {
            if (!$endDate) {
                $original = $this->getPlanningById($planningId);
                if ($original && $original['start_date'] && $original['end_date']) {
                    $duration = (strtotime($original['end_date']) - strtotime($original['start_date'])) / (60 * 60 * 24);
                    $endDate = date('Y-m-d', strtotime($startDate . " + $duration days"));
                } else {
                    $endDate = $startDate;
                }
            }
            
            $sql = "SELECT id FROM user_planning_schedules 
                    WHERE user_id = :user_id AND planning_id = :planning_id 
                    AND ((start_date BETWEEN :start1 AND :end1) OR (end_date BETWEEN :start2 AND :end2))";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':planning_id' => $planningId,
                ':start1' => $startDate,
                ':end1' => $endDate,
                ':start2' => $startDate,
                ':end2' => $endDate
            ]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Ce planning est déjà planifié sur cette période'];
            }
            
            $sql = "INSERT INTO user_planning_schedules (planning_id, user_id, start_date, end_date, status, created_at) 
                    VALUES (:planning_id, :user_id, :start_date, :end_date, 'scheduled', NOW())";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':planning_id' => $planningId,
                ':user_id' => $userId,
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Planning planifié avec succès', 'id' => $this->conn->lastInsertId()];
            }
            return ['success' => false, 'message' => 'Erreur lors de la planification'];
            
        } catch (PDOException $e) {
            error_log("Erreur schedulePlanning: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur technique'];
        }
    }
    
    public function getUserScheduledPlannings($userId, $startDate = null, $endDate = null) {
        try {
            $sql = "SELECT ups.*, np.name, np.description, np.status as original_status
                    FROM user_planning_schedules ups
                    JOIN nutrition_plans np ON ups.planning_id = np.id
                    WHERE ups.user_id = :user_id";
            
            $params = [':user_id' => $userId];
            
            if ($startDate && $endDate) {
                $sql .= " AND ((ups.start_date BETWEEN :start1 AND :end1) OR (ups.end_date BETWEEN :start2 AND :end2))";
                $params[':start1'] = $startDate;
                $params[':end1'] = $endDate;
                $params[':start2'] = $startDate;
                $params[':end2'] = $endDate;
            }
            
            $sql .= " ORDER BY ups.start_date ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getUserScheduledPlannings: " . $e->getMessage());
            return [];
        }
    }
    
    public function removeSchedule($scheduleId, $userId) {
        try {
            $sql = "DELETE FROM user_planning_schedules WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $scheduleId, ':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur removeSchedule: " . $e->getMessage());
            return false;
        }
    }
    
    public function markAsCompleted($scheduleId, $userId) {
        try {
            $sql = "UPDATE user_planning_schedules SET status = 'completed', completed_at = NOW() WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $scheduleId, ':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur markAsCompleted: " . $e->getMessage());
            return false;
        }
    }
    
    // ========================================
    // ANTI-SPAM ET VÉRIFICATION DOUBLONS
    // ========================================
    
    public function checkRecentSchedule($planningId, $userId, $daysThreshold = 7) {
        try {
            $sql = "SELECT id, start_date, created_at 
                    FROM user_planning_schedules 
                    WHERE user_id = :user_id 
                    AND planning_id = :planning_id
                    AND created_at > DATE_SUB(NOW(), INTERVAL :days DAY)
                    ORDER BY created_at DESC
                    LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':planning_id' => $planningId,
                ':days' => $daysThreshold
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur checkRecentSchedule: " . $e->getMessage());
            return false;
        }
    }
    
    public function checkDateConflicts($userId, $startDate, $endDate, $excludeScheduleId = null) {
        try {
            $sql = "SELECT ups.*, np.name 
                    FROM user_planning_schedules ups
                    JOIN nutrition_plans np ON ups.planning_id = np.id
                    WHERE ups.user_id = :user_id
                    AND ups.status != 'cancelled'
                    AND (
                        (ups.start_date BETWEEN :start1 AND :end1) 
                        OR (ups.end_date BETWEEN :start2 AND :end2)
                        OR (:start3 BETWEEN ups.start_date AND ups.end_date)
                        OR (:end3 BETWEEN ups.start_date AND ups.end_date)
                    )";
            
            $params = [
                ':user_id' => $userId,
                ':start1' => $startDate,
                ':end1' => $endDate,
                ':start2' => $startDate,
                ':end2' => $endDate,
                ':start3' => $startDate,
                ':end3' => $endDate
            ];
            
            if ($excludeScheduleId) {
                $sql .= " AND ups.id != :exclude_id";
                $params[':exclude_id'] = $excludeScheduleId;
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur checkDateConflicts: " . $e->getMessage());
            return [];
        }
    }
    
    public function getDailyScheduleCount($userId) {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM user_planning_schedules 
                    WHERE user_id = :user_id 
                    AND DATE(created_at) = CURDATE()";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        } catch (PDOException $e) {
            error_log("Erreur getDailyScheduleCount: " . $e->getMessage());
            return 0;
        }
    }
    
    public function isDateInPast($date) {
        return strtotime($date) < strtotime(date('Y-m-d'));
    }
    
    public function areDatesValid($startDate, $endDate) {
        return strtotime($startDate) <= strtotime($endDate);
    }
    
    public function schedulePlanningSafe($planningId, $userId, $startDate, $endDate = null) {
        try {
            // 1. Limite de planifications par jour (max 5)
            $dailyCount = $this->getDailyScheduleCount($userId);
            if ($dailyCount >= 5) {
                return ['success' => false, 'message' => 'Vous avez atteint la limite de 5 planifications par jour. Réessayez demain.'];
            }
            
            // 2. Vérifier si la date n'est pas dans le passé
            if ($this->isDateInPast($startDate)) {
                return ['success' => false, 'message' => 'Impossible de planifier dans le passé. Choisissez une date future.'];
            }
            
            // 3. Calculer la date de fin
            if (!$endDate) {
                $original = $this->getPlanningById($planningId);
                if ($original && $original['start_date'] && $original['end_date']) {
                    $duration = (strtotime($original['end_date']) - strtotime($original['start_date'])) / (60 * 60 * 24);
                    $endDate = date('Y-m-d', strtotime($startDate . " + $duration days"));
                } else {
                    $endDate = $startDate;
                }
            }
            
            // 4. Vérifier que début <= fin
            if (!$this->areDatesValid($startDate, $endDate)) {
                return ['success' => false, 'message' => 'La date de début doit être antérieure ou égale à la date de fin.'];
            }
            
            // 5. Vérifier les spams (même planning récemment)
            $recentSchedule = $this->checkRecentSchedule($planningId, $userId, 3);
            if ($recentSchedule) {
                return ['success' => false, 'message' => 'Vous avez déjà planifié ce planning récemment. Attendez quelques jours avant de le replanifier.'];
            }
            
            // 6. Vérifier les conflits de dates
            $conflicts = $this->checkDateConflicts($userId, $startDate, $endDate);
            if (!empty($conflicts)) {
                $conflictNames = array_column($conflicts, 'name');
                return ['success' => false, 'message' => 'Conflit de dates avec le(s) planning(s) : ' . implode(', ', $conflictNames)];
            }
            
            // 7. Vérifier si déjà planifié exactement
            $sql = "SELECT id FROM user_planning_schedules 
                    WHERE user_id = :user_id AND planning_id = :planning_id 
                    AND start_date = :start_date AND end_date = :end_date
                    AND status != 'cancelled'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':planning_id' => $planningId,
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Ce planning est déjà planifié exactement sur cette période.'];
            }
            
            // 8. Insérer la planification
            $sql = "INSERT INTO user_planning_schedules (planning_id, user_id, start_date, end_date, status, created_at) 
                    VALUES (:planning_id, :user_id, :start_date, :end_date, 'scheduled', NOW())";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':planning_id' => $planningId,
                ':user_id' => $userId,
                ':start_date' => $startDate,
                ':end_date' => $endDate
            ]);
            
            if ($result) {
                return [
                    'success' => true, 
                    'message' => 'Planning planifié avec succès !', 
                    'id' => $this->conn->lastInsertId(),
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ];
            }
            return ['success' => false, 'message' => 'Erreur lors de la planification'];
            
        } catch (PDOException $e) {
            error_log("Erreur schedulePlanningSafe: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur technique. Veuillez réessayer.'];
        }
    }
    
    public function getScheduleStats($userId) {
        try {
            $stats = [];
            
            $sql = "SELECT COUNT(*) as total FROM user_planning_schedules WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $sql = "SELECT COUNT(*) as active FROM user_planning_schedules 
                    WHERE user_id = :user_id AND status = 'scheduled' AND end_date >= CURDATE()";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $stats['active'] = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
            
            $sql = "SELECT COUNT(*) as completed FROM user_planning_schedules 
                    WHERE user_id = :user_id AND status = 'completed'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $stats['completed'] = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
            
            $sql = "SELECT COUNT(*) as today FROM user_planning_schedules 
                    WHERE user_id = :user_id AND start_date <= CURDATE() AND end_date >= CURDATE()";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $stats['today'] = $stmt->fetch(PDO::FETCH_ASSOC)['today'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur getScheduleStats: " . $e->getMessage());
            return ['total' => 0, 'active' => 0, 'completed' => 0, 'today' => 0];
        }
    }
    /**
 * Récupérer les notifications non lues (plannings + recettes)
 */

    
     // ========================================
    // GESTION DES NOTIFICATIONS
    // ========================================
    
    /**
     * Créer une notification pour tous les utilisateurs (sauf admin)
     * Quand un admin crée un nouveau planning
     */
    public function createPlanningNotification($planningId, $adminId) {
        try {
            $planning = $this->getPlanningById($planningId);
            if (!$planning) {
                return false;
            }
            
            $sql = "SELECT id FROM users WHERE role = 'user' OR role_id = 4";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $message = "📋 Nouveau planning nutritionnel : " . htmlspecialchars($planning['name']) . 
                       " du " . date('d/m/Y', strtotime($planning['start_date'])) . 
                       " au " . date('d/m/Y', strtotime($planning['end_date']));
            
            $sqlInsert = "INSERT INTO notifications (user_id, planning_id, type, message, created_at) 
                          VALUES (:user_id, :planning_id, 'new_planning', :message, NOW())";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            
            $count = 0;
            foreach ($users as $user) {
                $stmtInsert->execute([
                    ':user_id' => $user['id'],
                    ':planning_id' => $planningId,
                    ':message' => $message
                ]);
                $count++;
            }
            
            error_log("Notifications créées pour $count utilisateurs (planning #$planningId)");
            return true;
            
        } catch (PDOException $e) {
            error_log("Erreur createPlanningNotification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les notifications (avec recettes et plannings)
     */
    public function getUserNotifications($userId, $limit = 20) {
        try {
            $sql = "SELECT n.*, np.name as planning_name, np.start_date, np.end_date,
                           r.nom as recette_name
                    FROM notifications n
                    LEFT JOIN nutrition_plans np ON n.planning_id = np.id
                    LEFT JOIN recettes r ON n.recette_id = r.id
                    WHERE n.user_id = :user_id
                    ORDER BY n.created_at DESC
                    LIMIT :limit";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getUserNotifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupérer les notifications non lues (plannings + recettes)
     */
    public function getUnreadNotifications($userId) {
        try {
            $sql = "SELECT n.*, 
                           np.name as planning_name,
                           r.nom as recette_name,
                           n.type
                    FROM notifications n
                    LEFT JOIN nutrition_plans np ON n.planning_id = np.id
                    LEFT JOIN recettes r ON n.recette_id = r.id
                    WHERE n.user_id = :user_id AND n.is_read = 0
                    ORDER BY n.created_at DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur getUnreadNotifications: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Compter les notifications non lues
     */
    public function countUnreadNotifications($userId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM notifications 
                    WHERE user_id = :user_id AND is_read = 0";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
            
        } catch (PDOException $e) {
            error_log("Erreur countUnreadNotifications: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markNotificationAsRead($notificationId, $userId) {
        try {
            $sql = "UPDATE notifications 
                    SET is_read = 1 
                    WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':id' => $notificationId,
                ':user_id' => $userId
            ]);
            
        } catch (PDOException $e) {
            error_log("Erreur markNotificationAsRead: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllNotificationsAsRead($userId) {
        try {
            $sql = "UPDATE notifications SET is_read = 1 
                    WHERE user_id = :user_id AND is_read = 0";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
            
        } catch (PDOException $e) {
            error_log("Erreur markAllNotificationsAsRead: " . $e->getMessage());
            return false;
        }
    }
}