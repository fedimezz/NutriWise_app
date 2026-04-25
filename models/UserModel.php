<?php
/**
 * User Model
 * 
 * Handles all user-related database operations including:
 * - Authentication (login, registration)
 * - Profile management
 * - Recipes management
 * - Daily tracking / logging
 * - Nutrition plans
 * 
 * @package NutriWise
 * @subpackage Models
 */

require_once __DIR__ . '/Database.php';

class UserModel
{
    /** @var PDO Database connection */
    private PDO $conn;

    /** @var string Table name */
    private string $table = "users";

    /** @var array Cache for database column existence checks */
    private array $columnCache = [];

    /**
     * Constructor - Initialize database connection
     */
    public function __construct()
    {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Check if a column exists in a table
     * 
     * Uses caching to avoid repeated INFORMATION_SCHEMA queries
     * 
     * @param string $table Table name
     * @param string $column Column name
     * 
     * @return bool True if column exists, false otherwise
     */
    private function hasColumn(string $table, string $column): bool
    {
        $key = $table . '.' . $column;
        if (array_key_exists($key, $this->columnCache)) {
            return (bool)$this->columnCache[$key];
        }
        try {
            $stmt = $this->conn->prepare("
                SELECT 1
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = :t
                  AND COLUMN_NAME = :c
                LIMIT 1
            ");
            $stmt->execute([':t' => $table, ':c' => $column]);
            $exists = (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            // If INFORMATION_SCHEMA isn't accessible, assume column doesn't exist
            $exists = false;
        }
        $this->columnCache[$key] = $exists;
        return $exists;
    }

    // ========================================
    // AUTHENTICATION METHODS
    // ========================================

    /**
     * Register a new user
     * 
     * Creates new user account with default role 'user' and status 'actif'
     * 
     * @param string $prenom First name
     * @param string $nom Last name
     * @param string $email Email address
     * @param string $password Plain text password (will be hashed)
     * 
     * @return bool True on success, false on failure
     */
public function register(string $firstName, string $lastName, string $email, string $password): bool
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        throw new Exception("Email already exists");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $this->conn->prepare("
        INSERT INTO users (prenom, nom, email, password)
        VALUES (?, ?, ?, ?)
    ");

    return $stmt->execute([
        $firstName,
        $lastName,
        $email,
        $hashedPassword
    ]);
}

    /**
     * Authenticate user with email and password
     * 
     * Verifies credentials and returns user data if valid
     * Only returns users with 'actif' or 'active' status
     * 
     * @param string $email User email
     * @param string $password Plain text password to verify
     * 
     * @return array|false User data on success, false on failure
     */
  public function login(string $email, string $password) {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email AND statut = 'actif'");
    $stmt->execute([':email' => strtolower(trim($email))]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) return false;
    if (!password_verify($password, $user['password'])) return false;

    // Prevent login if not verified
    if ((int)$user['is_verified'] === 0) {
        return ['error' => 'not_verified'];
    }

    unset($user['password']);
    return $user;
}

    /**
     * Returns true if the table supports must-change-password and flag is set.
     */
    public function mustChangePassword(int $userId): bool
    {
        if (!$this->hasColumn($this->table, 'must_change_password')) return false;
        $stmt = $this->conn->prepare("SELECT must_change_password FROM " . $this->table . " WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $v = $stmt->fetchColumn();
        return (int)$v === 1;
    }

    public function setMustChangePassword(int $userId, bool $value): bool
    {
        if (!$this->hasColumn($this->table, 'must_change_password')) return true;
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET must_change_password = :v WHERE id = :id");
        return $stmt->execute([':v' => ($value ? 1 : 0), ':id' => $userId]);
    }
public function saveVerificationCode(string $email, string $hashedCode, string $expiry): bool {
    $stmt = $this->conn->prepare("
        UPDATE users 
        SET verification_code = :code, code_expiry = :expiry, is_verified = 0 
        WHERE email = :email
    ");
    return $stmt->execute([
        ':code' => $hashedCode,
        ':expiry' => $expiry,
        ':email' => $email
    ]);
}
public function deleteMultiple($ids)
{
    if (empty($ids)) return;

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $this->conn->prepare("DELETE FROM users WHERE id IN ($placeholders)");
    return $stmt->execute($ids);
}
public function verifyCode(string $email, string $inputCode): string {
    $stmt = $this->conn->prepare("SELECT verification_code, code_expiry FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) return "not_found";
    if (strtotime($user['code_expiry']) < time()) return "expired";
    if (!password_verify($inputCode, $user['verification_code'])) return "wrong";

    // ✅ Mise à jour correcte
    $stmt = $this->conn->prepare("
        UPDATE users 
        SET is_verified = 1, verification_code = NULL, code_expiry = NULL 
        WHERE email = :email
    ");
    $stmt->execute([':email' => $email]);

    return "success";
}

    /**
     * Retrieve user data by ID
     * 
     * @param int $id User ID
     * 
     * @return array|false User data or false if not found
     */
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve user by email address
     * 
     * @param string $email User email
     * 
     * @return array|null User data or null if not found
     */
    public function getUserByEmail(string $email): ?array {
        $email = strtolower(trim($email));
        $query = "SELECT * FROM " . $this->table . " WHERE LOWER(email) = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // ========================================
    // PROFILE MANAGEMENT
    // ========================================

    /**
     * Update user profile with health metrics
     * 
     * Calculates BMI and daily calorie needs based on user data
     * 
     * @param int $id User ID
     * @param string $prenom First name
     * @param string $nom Last name
     * @param string $email Email address
     * @param string $telephone Phone number
     * @param int|null $taille Height in cm
     * @param float|null $poids Weight in kg
     * @param string $objectif Fitness objective
     * @param int|null $age Age in years
     * @param string $gender Gender (male/female)
     * @param string $activity_level Activity level (sedentary/light/moderate/active/very_active)
     * @param string|null $allergies Allergies notes
     * 
     * @return bool True on success, false on failure
     */
    public function updateProfile($id, $prenom, $nom, $email, $telephone, $taille, $poids, $objectif, $age, $gender, $activity_level, $allergies = null) {
        // Calculate BMI
        $imc = null;
        if ($taille > 0 && $poids > 0) {
            $tailleM = $taille / 100;
            $imc = round($poids / ($tailleM * $tailleM), 1);
        }

        // Calculate daily calorie needs
        $daily_calories_needs = $this->calculateDailyCalories($poids, $taille, $age, $gender, $activity_level, $objectif);

        $setAllergies = $this->hasColumn($this->table, 'allergies');
        $query = "UPDATE " . $this->table . " 
                  SET prenom = :prenom, nom = :nom, email = :email, 
                      telephone = :telephone, taille = :taille, poids = :poids, 
                      imc = :imc, objectif = :objectif, age = :age, 
                      gender = :gender, activity_level = :activity_level,
                      daily_calories_needs = :daily_calories" . ($setAllergies ? ", allergies = :allergies" : "") . "
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $params = [
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':taille' => $taille,
            ':poids' => $poids,
            ':imc' => $imc,
            ':objectif' => $objectif,
            ':age' => $age,
            ':gender' => $gender,
            ':activity_level' => $activity_level,
            ':daily_calories' => $daily_calories_needs,
            ':id' => $id
        ];
        if ($setAllergies) {
            $params[':allergies'] = $allergies;
        }
        return $stmt->execute($params);
    }

    /**
     * Calculate daily calorie needs using Harris-Benedict formula
     * 
     * Uses BMR and activity factor to recommend daily calories
     * Adjusts based on fitness objective
     * 
     * @param float $weightKg Weight in kilograms
     * @param int $heightCm Height in centimeters
     * @param int $age Age in years
     * @param string $gender Gender (male/female)
     * @param string $activity_level Activity level
     * @param string $goal Fitness goal (Perte de poids/Prise de muscle/Maintien)
     * 
     * @return int Recommended daily calorie intake
     */
    private function calculateDailyCalories($weightKg, $heightCm, $age, $gender, $activity_level, $goal) {
        if (!$weightKg || !$heightCm || !$age) return 2000;
        
        // Calculate Basal Metabolic Rate (BMR)
        if ($gender === 'male') {
            $bmr = 10 * $weightKg + 6.25 * $heightCm - 5 * $age + 5;
        } else {
            $bmr = 10 * $weightKg + 6.25 * $heightCm - 5 * $age - 161;
        }
        
        // Apply activity factor
        $activityFactors = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9
        ];
        $factor = $activityFactors[$activity_level] ?? 1.55;
        $calories = $bmr * $factor;
        
        // Adjust based on fitness goal
        if ($goal === 'Perte de poids') $calories *= 0.85;
        if ($goal === 'Prise de muscle') $calories *= 1.15;
        
        return round($calories);
    }

    /**
     * Update user profile image
     * 
     * @param int $userId User ID
     * @param string $imageName Image filename
     * 
     * @return bool True on success, false on failure
     */
    public function updateProfileImage($userId, $imageName)
    {
        $query = "UPDATE " . $this->table . " SET profile_image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':image' => $imageName, ':id' => $userId]);
    }

    // ========================================
    // USER MANAGEMENT (ADMIN)
    // ========================================

    /**
     * Get all users
     * 
     * @return array List of all users sorted by newest first
     */
    public function getAllUsers() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total number of users
     * 
     * @return int Total user count
     */
    public function countUsers() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Create new user (admin function)
     * 
     * @param string $prenom First name
     * @param string $nom Last name
     * @param string $email Email address
     * @param string $password Plain text password (will be hashed)
     * @param string $role User role
     * @param string|null $telephone Phone number
     * @param string $statut User status (default: 'actif')
     * 
     * @return bool True on success, false on failure
     */
    public function createUser($prenom, $nom, $email, $password, $role, $telephone = null, $statut = 'actif') {
        $email = strtolower(trim((string)$email));
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $hasMustChange = $this->hasColumn($this->table, 'must_change_password');
        $cols = "prenom, nom, email, telephone, password, role, statut" . ($hasMustChange ? ", must_change_password" : "");
        $vals = ":prenom, :nom, :email, :telephone, :password, :role, :statut" . ($hasMustChange ? ", :must_change_password" : "");
        $query = "INSERT INTO " . $this->table . " ({$cols}) VALUES ({$vals})";
        $stmt = $this->conn->prepare($query);
        $params = [
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':password' => $hash,
            ':role' => $role,
            ':statut' => $statut
        ];
        if ($hasMustChange) {
            $params[':must_change_password'] = ($password === '12345678') ? 1 : 0;
        }
        return $stmt->execute($params);
    }

    /**
     * Check if email exists in database
     * 
     * @param string $email Email to check
     * 
     * @return bool True if email exists, false otherwise
     */
    public function emailExists(string $email): bool {
        $email = strtolower(trim($email));
        $query = "SELECT 1 FROM " . $this->table . " WHERE LOWER(email) = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Update user details (admin function)
     * 
     * @param int $id User ID
     * @param string $prenom First name
     * @param string $nom Last name
     * @param string $email Email address
     * @param string $role User role
     * @param string $statut User status
     * @param string|null $telephone Phone number
     * 
     * @return bool True on success, false on failure
     */
    public function updateUser($id, $prenom, $nom, $email, $role, $statut, $telephone = null) {
        $query = "UPDATE " . $this->table . " 
                  SET prenom = :prenom, nom = :nom, email = :email, 
                      telephone = :telephone, role = :role, statut = :statut 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':role' => $role,
            ':statut' => $statut,
            ':id' => $id
        ]);
    }

    /**
     * Update user password
     * 
     * @param int $id User ID
     * @param string $newPassword New plain text password (will be hashed)
     * 
     * @return bool True on success, false on failure
     */
    public function updateUserPassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':password' => $hash, ':id' => $id]);
    }

    /**
     * Delete user by ID
     * 
     * @param int $id User ID to delete
     * 
     * @return bool True on success, false on failure
     */
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // ========================================
    // RECIPES MANAGEMENT
    // ========================================

    /**
     * Get recipes with optional search filter
     * 
     * @param string|null $search Optional search term for recipe title
     * 
     * @return array List of recipes with creator information
     */
    public function getRecipes(?string $search = null): array {
        $sql = "SELECT r.*, u.prenom, u.nom
                FROM recipes r
                LEFT JOIN users u ON u.id = r.created_by";
        $params = [];
        if ($search !== null && trim($search) !== '') {
            $sql .= " WHERE r.title LIKE :q";
            $params[':q'] = '%' . trim($search) . '%';
        }
        $sql .= " ORDER BY r.created_at DESC, r.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get recipe by ID with creator information
     * 
     * @param int $id Recipe ID
     * 
     * @return array|null Recipe data or null if not found
     */
    public function getRecipeById(int $id): ?array {
        $sql = "SELECT r.*, u.prenom, u.nom
                FROM recipes r
                LEFT JOIN users u ON u.id = r.created_by
                WHERE r.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Create new recipe
     * 
     * @param int $userId Creator user ID
     * @param array $data Recipe data (title, description, calories, protein_g, carbs_g, fat_g)
     * 
     * @return bool True on success, false on failure
     */
    public function createRecipe(int $userId, array $data): bool {
        $sql = "INSERT INTO recipes (created_by, title, description, calories, protein_g, carbs_g, fat_g)
                VALUES (:created_by, :title, :description, :calories, :protein_g, :carbs_g, :fat_g)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':created_by' => $userId,
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':calories' => $data['calories'],
            ':protein_g' => $data['protein_g'],
            ':carbs_g' => $data['carbs_g'],
            ':fat_g' => $data['fat_g'],
        ]);
    }

    /**
     * Update recipe (creator or admin only)
     * 
     * @param int $recipeId Recipe ID to update
     * @param int $actorUserId User performing the action
     * @param string $actorRole Role of user performing the action
     * @param array $data Updated recipe data
     * 
     * @return bool True on success, false on failure or if unauthorized
     */
    public function updateRecipe(int $recipeId, int $actorUserId, string $actorRole, array $data): bool {
        $recipe = $this->getRecipeById($recipeId);
        if (!$recipe) return false;

        $canEdit = ((int)$recipe['created_by'] === $actorUserId) || (role_rank($actorRole) >= role_rank(ROLE_ADMIN)) || ($actorRole === ROLE_NUTRITIONIST);
        if (!$canEdit) return false;

        $sql = "UPDATE recipes
                SET title = :title, description = :description, calories = :calories,
                    protein_g = :protein_g, carbs_g = :carbs_g, fat_g = :fat_g
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $recipeId,
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':calories' => $data['calories'],
            ':protein_g' => $data['protein_g'],
            ':carbs_g' => $data['carbs_g'],
            ':fat_g' => $data['fat_g'],
        ]);
    }

    /**
     * Delete recipe (creator or admin only)
     * 
     * @param int $recipeId Recipe ID to delete
     * @param int $actorUserId User performing the action
     * @param string $actorRole Role of user performing the action
     * 
     * @return bool True on success, false on failure or if unauthorized
     */
    public function deleteRecipe(int $recipeId, int $actorUserId, string $actorRole): bool {
        $recipe = $this->getRecipeById($recipeId);
        if (!$recipe) return false;

        $canDelete = ((int)$recipe['created_by'] === $actorUserId) || (role_rank($actorRole) >= role_rank(ROLE_ADMIN));
        if (!$canDelete) return false;

        $stmt = $this->conn->prepare("DELETE FROM recipes WHERE id = :id");
        return $stmt->execute([':id' => $recipeId]);
    }

    // ========================================
    // DAILY TRACKING / LOGGING
    // ========================================

    /**
     * Get daily log for a specific user and date
     * 
     * @param int $userId User ID
     * @param string $day Date (YYYY-MM-DD format)
     * 
     * @return array|null Daily log data or null if not found
     */
    public function getDailyLog(int $userId, string $day): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM user_daily_logs WHERE user_id = :uid AND day = :day LIMIT 1");
        $stmt->execute([':uid' => $userId, ':day' => $day]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Insert or update daily log entry
     * 
     * Uses INSERT ON DUPLICATE KEY for upsert functionality
     * 
     * @param int $userId User ID
     * @param string $day Date (YYYY-MM-DD format)
     * @param float|null $weightKg Weight in kilograms
     * @param int $caloriesConsumed Total calories consumed
     * @param float $proteinG Protein in grams
     * @param float $carbsG Carbohydrates in grams
     * @param float $fatG Fats in grams
     * @param string|null $notes Additional notes
     * 
     * @return bool True on success, false on failure
     */
    public function upsertDailyLog(int $userId, string $day, ?float $weightKg, int $caloriesConsumed, float $proteinG, float $carbsG, float $fatG, ?string $notes): bool {
        $sql = "INSERT INTO user_daily_logs (user_id, day, weight_kg, calories_consumed, protein_g, carbs_g, fat_g, notes)
                VALUES (:uid, :day, :weight, :cal, :p, :c, :f, :notes)
                ON DUPLICATE KEY UPDATE
                  weight_kg = VALUES(weight_kg),
                  calories_consumed = VALUES(calories_consumed),
                  protein_g = VALUES(protein_g),
                  carbs_g = VALUES(carbs_g),
                  fat_g = VALUES(fat_g),
                  notes = VALUES(notes)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId,
            ':day' => $day,
            ':weight' => $weightKg,
            ':cal' => $caloriesConsumed,
            ':p' => $proteinG,
            ':c' => $carbsG,
            ':f' => $fatG,
            ':notes' => $notes,
        ]);
    }

    /**
     * Get daily logs for a date range
     * 
     * @param int $userId User ID
     * @param string $startDay Start date (YYYY-MM-DD format)
     * @param string $endDay End date (YYYY-MM-DD format)
     * 
     * @return array Array of daily logs in date range
     */
    public function getDailyLogRange(int $userId, string $startDay, string $endDay): array {
        $stmt = $this->conn->prepare("SELECT * FROM user_daily_logs WHERE user_id = :uid AND day BETWEEN :s AND :e ORDER BY day DESC");
        $stmt->execute([':uid' => $userId, ':s' => $startDay, ':e' => $endDay]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // ========================================
    // NUTRITION PLANS
    // ========================================

    /**
     * Create new nutrition plan
     * 
     * @param int $createdBy Creator user ID
     * @param int|null $assignedTo User ID to assign plan to
     * @param string $title Plan title
     * @param string|null $goal Plan goal/objective
     * @param int|null $caloriesTarget Target daily calories
     * @param int $isActive Whether plan is active (1 or 0)
     * 
     * @return bool True on success, false on failure
     */
    public function createPlan(int $createdBy, ?int $assignedTo, string $title, ?string $goal, ?int $caloriesTarget, int $isActive): bool {
        $stmt = $this->conn->prepare("INSERT INTO plans (created_by, assigned_to, title, goal, calories_target, is_active) VALUES (:cb, :at, :t, :g, :ct, :ia)");
        return $stmt->execute([
            ':cb' => $createdBy,
            ':at' => $assignedTo,
            ':t' => $title,
            ':g' => $goal,
            ':ct' => $caloriesTarget,
            ':ia' => $isActive,
        ]);
    }

    /**
     * Get nutrition plans
     * 
     * @param int|null $assignedTo Optional: filter by user assigned to
     * 
     * @return array Array of plans with creator and assignee information
     */
    public function getPlans(?int $assignedTo = null): array {
        $sql = "SELECT p.*, u1.prenom AS cb_prenom, u1.nom AS cb_nom, u2.prenom AS at_prenom, u2.nom AS at_nom
                FROM plans p
                LEFT JOIN users u1 ON u1.id = p.created_by
                LEFT JOIN users u2 ON u2.id = p.assigned_to";
        $params = [];
        if ($assignedTo !== null) {
            $sql .= " WHERE p.assigned_to = :uid";
            $params[':uid'] = $assignedTo;
        }
        $sql .= " ORDER BY p.created_at DESC, p.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // ========================================
    // ADMIN STATISTICS
    // ========================================

    /**
     * Count total recipes in database
     * 
     * @return int Total recipe count
     */
    public function countRecipes(): int {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM recipes");
            return (int)$stmt->fetchColumn();
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * Count active nutrition plans
     * 
     * @return int Number of active plans
     */
    public function countActivePlans(): int {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM plans WHERE is_active = 1");
            return (int)$stmt->fetchColumn();
        } catch (Throwable $e) {
            return 0;
        }
    }
}
?>
