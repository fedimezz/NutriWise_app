<?php
// controllers/SuiviController.php

class SuiviController {
    private $pdo;
    private $userId;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->userId = $_SESSION['user_id'] ?? null;
        
        if (!$this->userId) {
            redirect('index.php?page=login');
        }
    }
    
    public function index() {
        $today = date('Y-m-d');
        
        $user = $this->getUserData();
        $activePlan = $this->getActivePlan();
        $dailyLog = $this->getDailyLog($today);
        $meals = $this->getTodayMeals();
        $activities = $this->getTodayActivities();
        $waterIntake = $this->getTodayWater();
        
        $totals = $this->calculateTotals($dailyLog, $meals, $activities, $waterIntake);
        $dailyScore = $this->calculateDailyScore($totals, $activePlan, $user);
        $insights = $this->generateInsights($totals, $user);
        $nextAction = $this->suggestNextAction($totals);
        $healthStatus = $this->calculateHealthStatus($user);
        $groupedMeals = $this->groupMealsByType($meals);
        
        // Extraire les variables pour la vue
        extract([
            'user' => $user,
            'activePlan' => $activePlan,
            'dailyLog' => $dailyLog,
            'meals' => $meals,
            'groupedMeals' => $groupedMeals,
            'activities' => $activities,
            'totals' => $totals,
            'dailyScore' => $dailyScore,
            'insights' => $insights,
            'nextAction' => $nextAction,
            'healthStatus' => $healthStatus,
            'today' => $today
        ]);
        
        require_once 'views/front/suivi.php';
    }
    
    // ========== AJAX ENDPOINTS ==========
    
    public function addMeal() {
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO meals (user_id, meal_type, food_name, quantity, calories, protein_g, carbs_g, fat_g, category, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $this->userId,
                $_POST['meal_type'] ?? 'Déjeuner',
                $_POST['food_name'] ?? '',
                (int)($_POST['quantity'] ?? 0),
                (int)($_POST['calories'] ?? 0),
                (float)($_POST['protein_g'] ?? 0),
                (float)($_POST['carbs_g'] ?? 0),
                (float)($_POST['fat_g'] ?? 0),
                $_POST['category'] ?? null
            ]);
            
            $mealId = $this->pdo->lastInsertId();
            $this->updateDailyLog();
            $updatedTotals = $this->getUpdatedTotals();
            $updatedScore = $this->calculateDailyScore($updatedTotals, $this->getActivePlan(), $this->getUserData());
            
            echo json_encode([
                'success' => true,
                'message' => 'Repas ajouté avec succès !',
                'data' => [
                    'id' => $mealId,
                    'meal_type' => $_POST['meal_type'],
                    'food_name' => $_POST['food_name'],
                    'quantity' => (int)($_POST['quantity'] ?? 0),
                    'calories' => (int)($_POST['calories'] ?? 0),
                    'protein_g' => (float)($_POST['protein_g'] ?? 0),
                    'carbs_g' => (float)($_POST['carbs_g'] ?? 0),
                    'fat_g' => (float)($_POST['fat_g'] ?? 0),
                    'totals' => $updatedTotals,
                    'dailyScore' => $updatedScore
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function addActivity() {
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO activities (user_id, activity_type, duration, calories_burned, intensity, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $this->userId,
                $_POST['activity_type'] ?? '',
                (int)($_POST['duration'] ?? 0),
                (int)($_POST['calories_burned'] ?? 0),
                $_POST['intensity'] ?? 'Modérée'
            ]);
            
            $activityId = $this->pdo->lastInsertId();
            $this->updateDailyLog();
            $updatedTotals = $this->getUpdatedTotals();
            $updatedScore = $this->calculateDailyScore($updatedTotals, $this->getActivePlan(), $this->getUserData());
            
            echo json_encode([
                'success' => true,
                'message' => 'Activité ajoutée avec succès !',
                'data' => [
                    'id' => $activityId,
                    'activity_type' => $_POST['activity_type'],
                    'duration' => (int)($_POST['duration'] ?? 0),
                    'calories_burned' => (int)($_POST['calories_burned'] ?? 0),
                    'intensity' => $_POST['intensity'] ?? 'Modérée',
                    'totals' => $updatedTotals,
                    'dailyScore' => $updatedScore
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function addWater() {
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO water_intake (user_id, glasses, created_at) 
                VALUES (?, 1, NOW())
            ");
            $stmt->execute([$this->userId]);
            
            $this->updateDailyLog();
            $updatedTotals = $this->getUpdatedTotals();
            $updatedScore = $this->calculateDailyScore($updatedTotals, $this->getActivePlan(), $this->getUserData());
            
            echo json_encode([
                'success' => true,
                'message' => '+1 verre d\'eau ajouté !',
                'data' => [
                    'totals' => $updatedTotals,
                    'dailyScore' => $updatedScore
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function deleteMeal() {
        header('Content-Type: application/json');
        
        try {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $this->pdo->prepare("DELETE FROM meals WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $this->userId]);
            
            $this->updateDailyLog();
            $updatedTotals = $this->getUpdatedTotals();
            $updatedScore = $this->calculateDailyScore($updatedTotals, $this->getActivePlan(), $this->getUserData());
            
            echo json_encode([
                'success' => true,
                'message' => 'Repas supprimé',
                'data' => [
                    'totals' => $updatedTotals,
                    'dailyScore' => $updatedScore
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }
    
    public function deleteActivity() {
        header('Content-Type: application/json');
        
        try {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $this->pdo->prepare("DELETE FROM activities WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $this->userId]);
            
            $this->updateDailyLog();
            $updatedTotals = $this->getUpdatedTotals();
            $updatedScore = $this->calculateDailyScore($updatedTotals, $this->getActivePlan(), $this->getUserData());
            
            echo json_encode([
                'success' => true,
                'message' => 'Activité supprimée',
                'data' => [
                    'totals' => $updatedTotals,
                    'dailyScore' => $updatedScore
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // ========== PRIVATE METHODS ==========
    
    private function getUserData() {
        $stmt = $this->pdo->prepare("
            SELECT id, prenom, nom, email, age, gender, taille, poids, 
                   objectif, activity_level, daily_calories_needs, target_weight,
                   water_goal, sleep_goal, activity_goal
            FROM users WHERE id = ?
        ");
        $stmt->execute([$this->userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Valeurs par défaut si null
        if (!$result) {
            $result = [
                'daily_calories_needs' => 2000,
                'water_goal' => 8,
                'sleep_goal' => 8,
                'activity_goal' => 150
            ];
        }
        return $result;
    }
    
    private function getActivePlan() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM plans
            WHERE (assigned_to = ? OR assigned_to IS NULL) AND is_active = 1
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$this->userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getDailyLog($date) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM daily_logs 
            WHERE user_id = ? AND day = ?
        ");
        $stmt->execute([$this->userId, $date]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$log) {
            $log = [
                'calories_consumed' => 0,
                'calories_burned' => 0,
                'protein_g' => 0,
                'carbs_g' => 0,
                'fat_g' => 0,
                'water_glasses' => 0,
                'sleep_hours' => 0,
                'weight_kg' => null
            ];
        }
        return $log;
    }
    
    private function getTodayMeals() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM meals 
            WHERE user_id = ? AND DATE(created_at) = CURDATE() 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$this->userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getTodayActivities() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM activities 
            WHERE user_id = ? AND DATE(created_at) = CURDATE() 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$this->userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getTodayWater() {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(glasses), 0) as total 
            FROM water_intake 
            WHERE user_id = ? AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute([$this->userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    private function getUpdatedTotals() {
        $today = date('Y-m-d');
        $dailyLog = $this->getDailyLog($today);
        $meals = $this->getTodayMeals();
        $activities = $this->getTodayActivities();
        $waterIntake = $this->getTodayWater();
        
        return $this->calculateTotals($dailyLog, $meals, $activities, $waterIntake);
    }
    
    private function updateDailyLog() {
        $today = date('Y-m-d');
        try {
            $stmt = $this->pdo->prepare("CALL update_daily_log(?, ?)");
            $stmt->execute([$this->userId, $today]);
        } catch (Exception $e) {
            // La procédure n'existe peut-être pas, on ignore
        }
    }
    
    private function calculateTotals($dailyLog, $meals, $activities, $waterIntake) {
        $caloriesFromMeals = array_sum(array_column($meals, 'calories'));
        $proteinFromMeals = array_sum(array_column($meals, 'protein_g'));
        $carbsFromMeals = array_sum(array_column($meals, 'carbs_g'));
        $fatFromMeals = array_sum(array_column($meals, 'fat_g'));
        
        $caloriesFromActivities = array_sum(array_column($activities, 'calories_burned'));
        
        return [
            'calories_consumed' => max($caloriesFromMeals, $dailyLog['calories_consumed'] ?? 0),
            'calories_burned' => max($caloriesFromActivities, $dailyLog['calories_burned'] ?? 0),
            'net_calories' => max($caloriesFromMeals, $dailyLog['calories_consumed'] ?? 0) - max($caloriesFromActivities, $dailyLog['calories_burned'] ?? 0),
            'protein' => max($proteinFromMeals, $dailyLog['protein_g'] ?? 0),
            'carbs' => max($carbsFromMeals, $dailyLog['carbs_g'] ?? 0),
            'fat' => max($fatFromMeals, $dailyLog['fat_g'] ?? 0),
            'water' => max($waterIntake, $dailyLog['water_glasses'] ?? 0),
            'sleep' => $dailyLog['sleep_hours'] ?? 0
        ];
    }
    
    private function calculateDailyScore($totals, $activePlan, $user) {
        $score = 0;
        $targetCalories = ($activePlan['calories_target'] ?? $user['daily_calories_needs'] ?? 2000);
        $targetWater = $user['water_goal'] ?? 8;
        
        // Calories score (40%)
        if ($targetCalories > 0) {
            $calRatio = min(1, $totals['calories_consumed'] / $targetCalories);
            $score += $calRatio * 40;
        }
        
        // Hydratation score (20%)
        if ($targetWater > 0) {
            $waterRatio = min(1, $totals['water'] / $targetWater);
            $score += $waterRatio * 20;
        }
        
        // Activité score (20%)
        $activityTarget = 300;
        if ($activityTarget > 0) {
            $activityRatio = min(1, $totals['calories_burned'] / $activityTarget);
            $score += $activityRatio * 20;
        }
        
        // Sommeil score (20%)
        $sleepTarget = 8;
        if ($sleepTarget > 0) {
            $sleepRatio = min(1, $totals['sleep'] / $sleepTarget);
            $score += $sleepRatio * 20;
        }
        
        return min(100, round($score));
    }
    
    private function generateInsights($totals, $user) {
        $insights = [];
        $targetCalories = $user['daily_calories_needs'] ?? 2000;
        
        if ($totals['calories_consumed'] > $targetCalories * 1.1) {
            $insights[] = [
                'type' => 'warning', 
                'icon' => '⚠️', 
                'message' => 'Calories élevées aujourd\'hui !', 
                'color' => '#e74c3c', 
                'action' => null
            ];
        } elseif ($totals['calories_consumed'] < $targetCalories * 0.7) {
            $insights[] = [
                'type' => 'warning', 
                'icon' => '⚠️', 
                'message' => 'Calories trop basses !', 
                'color' => '#f39c12', 
                'action' => ['text' => 'Ajouter une collation', 'type' => 'meal']
            ];
        } else {
            $insights[] = [
                'type' => 'success', 
                'icon' => '✅', 
                'message' => 'Objectif calorique bien respecté !', 
                'color' => '#27ae60', 
                'action' => null
            ];
        }
        
        if ($totals['water'] < 6) {
            $insights[] = [
                'type' => 'warning', 
                'icon' => '💧', 
                'message' => 'Hydratation insuffisante !', 
                'color' => '#e74c3c', 
                'action' => ['text' => '+1 verre d\'eau', 'type' => 'water']
            ];
        }
        
        if ($totals['calories_burned'] < 200) {
            $insights[] = [
                'type' => 'info', 
                'icon' => '🏃', 
                'message' => 'Ajoutez une activité physique', 
                'color' => '#3498db', 
                'action' => ['text' => 'Ajouter activité', 'type' => 'activity']
            ];
        }
        
        return $insights;
    }
    
    private function suggestNextAction($totals) {
        if ($totals['water'] < 6) {
            return ['icon' => '💧', 'text' => 'Boire un verre d\'eau', 'action' => 'water'];
        }
        if ($totals['calories_burned'] < 200) {
            return ['icon' => '🚶', 'text' => 'Faire une marche de 15 min', 'action' => 'activity'];
        }
        if ($totals['calories_consumed'] < 1500) {
            return ['icon' => '🍎', 'text' => 'Prendre une collation', 'action' => 'meal'];
        }
        return ['icon' => '🎉', 'text' => 'Continuez sur cette lancée !', 'action' => null];
    }
    
    private function calculateHealthStatus($user) {
        $height = $user['taille'] ?? null;
        $weight = $user['poids'] ?? null;
        
        $status = [
            'bmi' => null,
            'category' => null,
            'color' => null,
            'message' => null
        ];
        
        if ($height && $weight && $height > 0) {
            $bmi = round($weight / (($height / 100) ** 2), 1);
            $status['bmi'] = $bmi;
            
            if ($bmi < 18.5) {
                $status['category'] = 'Insuffisance pondérale';
                $status['color'] = '#f39c12';
                $status['message'] = 'Consultez un nutritionniste.';
            } elseif ($bmi < 25) {
                $status['category'] = 'Poids normal';
                $status['color'] = '#27ae60';
                $status['message'] = 'Excellent !';
            } elseif ($bmi < 30) {
                $status['category'] = 'Surpoids';
                $status['color'] = '#e74c3c';
                $status['message'] = 'Un petit effort suffit.';
            } else {
                $status['category'] = 'Obésité';
                $status['color'] = '#c0392b';
                $status['message'] = 'Suivi recommandé.';
            }
        }
        
        return $status;
    }
    
    private function groupMealsByType($meals) {
        $grouped = [
            'Petit-déjeuner' => [],
            'Déjeuner' => [],
            'Dîner' => [],
            'Collation' => []
        ];
        
        foreach ($meals as $meal) {
            $type = $meal['meal_type'] ?? 'Autre';
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $meal;
        }
        
        return $grouped;
    }
}
?>