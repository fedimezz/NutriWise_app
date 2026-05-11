<?php
require_once 'models/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS suivis (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        date_suivi DATE NOT NULL,
        poids FLOAT NOT NULL,
        calories_necessaires INT NOT NULL,
        etat VARCHAR(50) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $conn->exec($sql);
    echo "Table 'suivis' cree avec succes.\n";
} catch(PDOException $e) {
    echo "Erreur lors de la creation de la table : " . $e->getMessage() . "\n";
}
?>
