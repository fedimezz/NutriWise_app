-- Migration pour les tables de notifications NutriWise
-- À exécuter après database.sql

USE nutriwise_db;

-- Table des notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    lu TINYINT(1) DEFAULT 0,
    lu_le TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_lu (lu),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des préférences utilisateur pour les notifications
CREATE TABLE IF NOT EXISTS user_preferences (
    user_id INT PRIMARY KEY,
    notif_rappel_suivi TINYINT(1) DEFAULT 1,
    notif_eau TINYINT(1) DEFAULT 1,
    notif_objectif TINYINT(1) DEFAULT 1,
    notif_poids_alerte TINYINT(1) DEFAULT 1,
    notif_encouragement TINYINT(1) DEFAULT 1,
    notif_consultation TINYINT(1) DEFAULT 1,
    heure_rappel_suivi TIME DEFAULT '20:00:00',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table de log pour les exécutions du cron
CREATE TABLE IF NOT EXISTS cron_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rule VARCHAR(50) NOT NULL,
    users_notified INT DEFAULT 0,
    details TEXT,
    run_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_rule (rule),
    INDEX idx_run_at (run_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;