<?php
// Fichier de migration pour créer les tables de notifications
// À exécuter une fois pour initialiser la base de données

require_once 'models/Database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    // Créer la table des notifications
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS `notifications` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `planning_id` INT NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `message` TEXT,
        `type` ENUM('info', 'warning', 'urgent') DEFAULT 'info',
        `is_read` BOOLEAN DEFAULT FALSE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `read_at` TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (planning_id) REFERENCES nutrition_plans(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_planning_id (planning_id),
        INDEX idx_is_read (is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $conn->exec($createTableQuery);
    echo "✅ Table notifications créée avec succès!";

} catch(PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>
