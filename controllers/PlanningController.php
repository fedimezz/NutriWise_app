<?php
// controllers/PlanningController.php

require_once 'models/PlanningModel.php';

class PlanningController {
    private $pdo;
    private $planningModel;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->planningModel = new PlanningModel();
    }
    
    /**
     * Afficher la liste des plannings de l'utilisateur connecté (avec likes et favoris)
     */
    public function frontList() {
        require_login();
        
        $userId = current_user_id();
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        
        $planningsData = $this->planningModel->getUserPlannings($userId, $page, 9);
        
        $likedPlannings = $this->planningModel->getUserLikedPlannings($userId);
        $favoritePlannings = $this->planningModel->getUserFavoritesIds($userId);
        
        foreach ($planningsData['plannings'] as &$planning) {
            $planning['likes_count'] = $this->planningModel->getLikeCount($planning['id']);
            $planning['is_liked'] = in_array($planning['id'], $likedPlannings);
        }
        
        require_once 'views/front/nutrition_plans.php';
    }
    
    /**
     * Afficher les détails d'un planning
     */
    public function details() {
        require_login();
        
        $planningId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$planningId) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=nutrition_plans');
        }
        
        $planning = $this->planningModel->getPlanningById($planningId);
        
        if (!$planning) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=nutrition_plans');
        }
        
        $userId = current_user_id();
        $userRole = current_user_role();
        
        // Vérifier l'autorisation
        $isOwner = ($planning['user_id'] == $userId);
        $isAdmin = ($userRole === 'admin' || $userRole === 'owner');
        
        // Vérifier si le planning a été créé par un admin (donc public)
        $sql = "SELECT role FROM users WHERE id = :id AND role IN ('admin', 'owner')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $planning['created_by']]);
        $isPublicPlanning = ($stmt->fetch() !== false);
        
        if (!$isOwner && !$isAdmin && !$isPublicPlanning) {
            $_SESSION['error'] = "Vous n'êtes pas autorisé à voir ce planning";
            redirect('index.php?page=nutrition_plans');
        }
        
        $planningsByDay = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        
        foreach ($planning['menus'] as $menu) {
            $day = $menu['jour'] - 1;
            if (!isset($planningsByDay[$day])) {
                $planningsByDay[$day] = [
                    'jour' => $jours[$day],
                    'menus' => []
                ];
            }
            $planningsByDay[$day]['menus'][] = $menu;
        }
        
        $dailyTotals = [];
        foreach ($planningsByDay as $dayIndex => $dayData) {
            $dailyTotals[$dayIndex] = [
                'calories' => 0,
                'proteines' => 0,
                'glucides' => 0,
                'lipides' => 0
            ];
            
            foreach ($dayData['menus'] as $menu) {
                $dailyTotals[$dayIndex]['calories'] += $menu['total_calories'];
                $dailyTotals[$dayIndex]['proteines'] += $menu['total_proteines'];
                $dailyTotals[$dayIndex]['glucides'] += $menu['total_glucides'];
                $dailyTotals[$dayIndex]['lipides'] += $menu['total_lipides'];
            }
        }
        
        require_once 'views/front/nutrition_plan_details.php';
    }
    
    /**
     * [ADMIN] Afficher tous les plannings
     */
    public function listPlannings() {
        require_role(['admin', 'owner']);
        
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $filters = [];
        
        if (!empty($_GET['user_id'])) {
            $filters['user_id'] = (int)$_GET['user_id'];
        }
        
        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        
        $planningsData = $this->planningModel->getAllPlannings($page, 15, $filters);
        $users = $this->planningModel->getAllUsers();
        
        require_once 'views/back/plannings.php';
    }
    
    /**
     * [ADMIN] Ajouter un planning
     */
  /**
 * [ADMIN] Ajouter un planning (PUBLIC pour tous les utilisateurs)
 */
public function addPlanning() {
    require_role(['admin', 'owner']);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_check();
        
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'active';
        $menuIds = $_POST['menu_ids'] ?? [];
        
        // Validation
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Le nom du planning est requis";
        }
        
        if (empty($startDate)) {
            $errors[] = "La date de début est requise";
        }
        
        if (empty($endDate)) {
            $errors[] = "La date de fin est requise";
        }
        
        if ($startDate && $endDate && strtotime($endDate) < strtotime($startDate)) {
            $errors[] = "La date de fin doit être postérieure à la date de début";
        }
        
        if (empty($menuIds)) {
            $errors[] = "Veuillez sélectionner au moins un menu";
        }
        
        if (empty($errors)) {
            $createdBy = current_user_id();
            
            // CRÉER UN PLANNING POUR TOUS LES UTILISATEURS
            // On met user_id = NULL pour indiquer que c'est public
            $planningId = $this->planningModel->addPublicPlanning($name, $description, $startDate, $endDate, $createdBy, $status, $menuIds);
            
            if ($planningId) {
                $_SESSION['success'] = "Planning public ajouté avec succès ! Tous les utilisateurs ont reçu une notification.";
                redirect('index.php?page=admin_plannings');
            } else {
                $errors[] = "Erreur lors de l'ajout du planning";
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        }
    }
    
    $users = $this->planningModel->getAllUsers();
    $menus = $this->planningModel->getAllMenus();
    
    require_once 'views/back/add_planning.php';
}
    
    /**
     * [ADMIN] Modifier un planning
     */
    public function editPlanning() {
        require_role(['admin', 'owner']);
        
        $planningId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$planningId) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=admin_plannings');
        }
        
        $planning = $this->planningModel->getPlanningById($planningId);
        if (!$planning) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=admin_plannings');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_check();
            
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $status = $_POST['status'] ?? 'draft';
            $menuIds = $_POST['menu_ids'] ?? [];
            
            $errors = [];
            
            if (empty($name)) {
                $errors[] = "Le nom du planning est requis";
            }
            
            if (empty($startDate)) {
                $errors[] = "La date de début est requise";
            }
            
            if (empty($endDate)) {
                $errors[] = "La date de fin est requise";
            }
            
            if ($startDate && $endDate && strtotime($endDate) < strtotime($startDate)) {
                $errors[] = "La date de fin doit être postérieure à la date de début";
            }
            
            if (empty($menuIds)) {
                $errors[] = "Veuillez sélectionner au moins un menu";
            }
            
            if (empty($errors)) {
                $result = $this->planningModel->updatePlanning($planningId, $name, $description, $startDate, $endDate, $status, $menuIds);
                
                if ($result) {
                    $_SESSION['success'] = "Planning modifié avec succès";
                    redirect('index.php?page=admin_plannings');
                } else {
                    $errors[] = "Erreur lors de la modification du planning";
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
            }
        }
        
        $users = $this->planningModel->getAllUsers();
        $menus = $this->planningModel->getAllMenus();
        $currentMenuIds = array_column($planning['menus'], 'menu_id');
        
        require_once 'views/back/edit_planning.php';
    }
    
    /**
     * [ADMIN] Supprimer un planning
     */
    public function deletePlanning() {
        require_role(['admin', 'owner']);
        
        csrf_check();
        
        $planningId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (!$planningId) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=admin_plannings');
        }
        
        $result = $this->planningModel->deletePlanning($planningId);
        
        if ($result) {
            $_SESSION['success'] = "Planning supprimé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression du planning";
        }
        
        redirect('index.php?page=admin_plannings');
    }
    
    /**
     * [ADMIN] Détails d'un planning
     */
    public function adminDetails() {
        require_role(['admin', 'owner']);
        
        $planningId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$planningId) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=admin_plannings');
        }
        
        $planning = $this->planningModel->getPlanningById($planningId);
        
        if (!$planning) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=admin_plannings');
        }
        
        $planningsByDay = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        
        foreach ($planning['menus'] as $menu) {
            $day = $menu['jour'] - 1;
            if (!isset($planningsByDay[$day])) {
                $planningsByDay[$day] = [
                    'jour' => $jours[$day],
                    'menus' => []
                ];
            }
            $planningsByDay[$day]['menus'][] = $menu;
        }
        
        $dailyTotals = [];
        foreach ($planningsByDay as $dayIndex => $dayData) {
            $dailyTotals[$dayIndex] = [
                'calories' => 0,
                'proteines' => 0,
                'glucides' => 0,
                'lipides' => 0
            ];
            
            foreach ($dayData['menus'] as $menu) {
                $dailyTotals[$dayIndex]['calories'] += $menu['total_calories'];
                $dailyTotals[$dayIndex]['proteines'] += $menu['total_proteines'];
                $dailyTotals[$dayIndex]['glucides'] += $menu['total_glucides'];
                $dailyTotals[$dayIndex]['lipides'] += $menu['total_lipides'];
            }
        }
        
        require_once 'views/back/planning_details.php';
    }
    
    /**
     * Ajouter/Retirer un like sur un planning (AJAX)
     */
    public function toggleLike() {
        require_login();
        
        header('Content-Type: application/json');
        
        $planningId = isset($_POST['planning_id']) ? (int)$_POST['planning_id'] : 0;
        $userId = current_user_id();
        
        if (!$planningId) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            exit;
        }
        
        $isLiked = $this->planningModel->isPlanningLiked($planningId, $userId);
        
        if ($isLiked) {
            $result = $this->planningModel->removeLike($planningId, $userId);
            $liked = false;
        } else {
            $result = $this->planningModel->addLike($planningId, $userId);
            $liked = true;
        }
        
        if ($result) {
            $likeCount = $this->planningModel->getLikeCount($planningId);
            echo json_encode(['success' => true, 'liked' => $liked, 'count' => $likeCount]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'opération']);
        }
        exit;
    }
    
    /**
     * Ajouter/Retirer un favori (AJAX)
     */
    public function toggleFavorite() {
        require_login();
        
        header('Content-Type: application/json');
        
        $planningId = isset($_POST['planning_id']) ? (int)$_POST['planning_id'] : 0;
        $userId = current_user_id();
        
        if (!$planningId) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            exit;
        }
        
        $isFavorite = $this->planningModel->isInFavorites($planningId, $userId);
        
        if ($isFavorite) {
            $result = $this->planningModel->removeFromFavorites($planningId, $userId);
            $favorite = false;
        } else {
            $result = $this->planningModel->addToFavorites($planningId, $userId);
            $favorite = true;
        }
        
        if ($result) {
            echo json_encode(['success' => true, 'favorite' => $favorite]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'opération']);
        }
        exit;
    }
    
    /**
     * Vue calendrier des plannings avec toutes les données
     */
    public function calendar() {
        require_login();
        
        $userId = current_user_id();
        
        $planningsData = $this->planningModel->getUserPlannings($userId, 1, 100);
        $plannings = $planningsData['plannings'] ?? [];
        
        $likedIds = $this->planningModel->getUserLikedPlannings($userId);
        $likedPlannings = [];
        foreach ($likedIds as $id) {
            $planning = $this->planningModel->getPlanningById($id);
            if ($planning) $likedPlannings[] = $planning;
        }
        
        $favoriteIds = $this->planningModel->getUserFavoritesIds($userId);
        $favoritePlannings = [];
        foreach ($favoriteIds as $id) {
            $planning = $this->planningModel->getPlanningById($id);
            if ($planning) $favoritePlannings[] = $planning;
        }
        
        $scheduledPlannings = $this->planningModel->getUserScheduledPlannings($userId);
        
        require_once 'views/front/nutrition_plans_calendar.php';
    }
    
    /**
     * Planifier un planning (choisir date) - AVEC ANTI-SPAM
     */
    public function schedule() {
        require_login();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            $planningId = isset($_POST['planning_id']) ? (int)$_POST['planning_id'] : 0;
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? null;
            $userId = current_user_id();
            
            if (!$planningId || !$startDate) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                exit;
            }
            
            $result = $this->planningModel->schedulePlanningSafe($planningId, $userId, $startDate, $endDate);
            echo json_encode($result);
            exit;
        }
        
        $planningId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$planningId) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=nutrition_plans');
        }
        
        $planning = $this->planningModel->getPlanningById($planningId);
        if (!$planning) {
            $_SESSION['error'] = "Planning non trouvé";
            redirect('index.php?page=nutrition_plans');
        }
        
        require_once 'views/front/schedule_planning.php';
    }
    
    /**
     * Afficher mes plannings planifiés
     */
    public function mySchedules() {
        require_login();
        
        $userId = current_user_id();
        $schedules = $this->planningModel->getUserScheduledPlannings($userId);
        
        require_once 'views/front/my_schedules.php';
    }
    
    /**
     * Supprimer une planification
     */
    public function deleteSchedule() {
        require_login();
        
        $scheduleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$scheduleId) {
            $_SESSION['error'] = "Planification non trouvée";
            redirect('index.php?page=my_schedules');
        }
        
        $result = $this->planningModel->removeSchedule($scheduleId, current_user_id());
        
        if ($result) {
            $_SESSION['success'] = "Planification supprimée avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
        
        redirect('index.php?page=my_schedules');
    }
    
    /**
     * Marquer comme terminé
     */
    public function completeSchedule() {
        require_login();
        
        $scheduleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$scheduleId) {
            $_SESSION['error'] = "Planification non trouvée";
            redirect('index.php?page=my_schedules');
        }
        
        $result = $this->planningModel->markAsCompleted($scheduleId, current_user_id());
        
        if ($result) {
            $_SESSION['success'] = "Félicitations ! Planning marqué comme terminé 🎉";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour";
        }
        
        redirect('index.php?page=my_schedules');
    }
    
    /**
     * Récupérer les notifications (AJAX)
     */
    public function getNotifications() {
        require_login();
        
        header('Content-Type: application/json');
        
        $userId = current_user_id();
        $notifications = $this->planningModel->getUnreadNotifications($userId);
        
        $formatted = [];
        foreach ($notifications as $notif) {
            $formatted[] = [
                'id' => $notif['id'],
                'message' => $notif['message'],
                'planning_id' => $notif['planning_id'],
                'created_at' => date('d/m/Y H:i', strtotime($notif['created_at']))
            ];
        }
        
        echo json_encode($formatted);
        exit;
    }
    
    /**
     * Récupérer le compteur de notifications (AJAX)
     */
    public function getNotificationCount() {
        require_login();
        
        header('Content-Type: application/json');
        
        $userId = current_user_id();
        $count = $this->planningModel->countUnreadNotifications($userId);
        
        echo json_encode(['count' => $count]);
        exit;
    }
    
    /**
     * Marquer une notification comme lue (AJAX)
     */
    public function markNotification() {
        require_login();
        
        header('Content-Type: application/json');
        
        $notificationId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $userId = current_user_id();
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            exit;
        }
        
        $result = $this->planningModel->markNotificationAsRead($notificationId, $userId);
        
        echo json_encode(['success' => $result]);
        exit;
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllNotifications() {
        require_login();
        
        header('Content-Type: application/json');
        
        $userId = current_user_id();
        $result = $this->planningModel->markAllNotificationsAsRead($userId);
        
        echo json_encode(['success' => $result]);
        exit;
    }
    /**
 * Partager un planning (créer une notification de partage)
 */
public function sharePlanning() {
    require_login();
    
    header('Content-Type: application/json');
    
    $planningId = isset($_POST['planning_id']) ? (int)$_POST['planning_id'] : 0;
    $userId = current_user_id();
    $userName = $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
    
    if (!$planningId) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        exit;
    }
    
    $planning = $this->planningModel->getPlanningById($planningId);
    if (!$planning) {
        echo json_encode(['success' => false, 'message' => 'Planning non trouvé']);
        exit;
    }
    
    try {
        // Créer une notification pour tous les utilisateurs (ou pour un utilisateur spécifique)
        $message = "📢 " . htmlspecialchars($userName) . " a partagé un planning : " . htmlspecialchars($planning['name']);
        
        // Récupérer tous les utilisateurs sauf celui qui partage
        $sql = "SELECT id FROM users WHERE id != :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $sqlInsert = "INSERT INTO notifications (user_id, planning_id, type, message, created_at) 
                      VALUES (:user_id, :planning_id, 'shared', :message, NOW())";
        $stmtInsert = $this->pdo->prepare($sqlInsert);
        
        foreach ($users as $user) {
            $stmtInsert->execute([
                ':user_id' => $user['id'],
                ':planning_id' => $planningId,
                ':message' => $message
            ]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Planning partagé avec succès !']);
        
    } catch (PDOException $e) {
        error_log("Erreur sharePlanning: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erreur lors du partage']);
    }
    exit;
}
}
?>