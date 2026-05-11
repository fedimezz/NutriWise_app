-- Script SQL pour implementer la base de donnees dans XAMPP (phpMyAdmin)

-- Creation de la base de donnees
CREATE DATABASE IF NOT EXISTS nutriwise_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nutriwise_db;

-- 1. Table users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    taille FLOAT DEFAULT NULL,
    poids FLOAT DEFAULT NULL,
    imc FLOAT DEFAULT NULL,
    objectif VARCHAR(100) DEFAULT NULL,
    role VARCHAR(50) DEFAULT 'user',
    statut VARCHAR(50) DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion de l'administrateur par defaut
INSERT INTO users (prenom, nom, email, password, role, statut) 
VALUES ('Super', 'Admin', 'admin@nutriwise.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'actif')
ON DUPLICATE KEY UPDATE role='admin';
-- Le mot de passe par defaut est 'password'

-- 2. Table categories (pour les aliments)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion de categories par defaut
INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Légumes'),
(2, 'Fruits'),
(3, 'Céréales et Féculents'),
(4, 'Viandes et Volailles'),
(5, 'Poissons et Fruits de mer'),
(6, 'Produits laitiers'),
(7, 'Matières grasses'),
(8, 'Produits sucrés'),
(9, 'Boissons');

-- 3. Table aliments
CREATE TABLE IF NOT EXISTS aliments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    calories FLOAT NOT NULL DEFAULT 0,
    proteines FLOAT NOT NULL DEFAULT 0,
    glucides FLOAT NOT NULL DEFAULT 0,
    lipides FLOAT NOT NULL DEFAULT 0,
    eco_score VARCHAR(10) DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion d'exemples d'aliments
INSERT INTO aliments (nom, category_id, calories, proteines, glucides, lipides, eco_score) VALUES
('Pomme', 2, 52, 0.3, 14, 0.2, 'A'),
('Poulet (blanc)', 4, 165, 31, 0, 3.6, 'B'),
('Brocoli', 1, 34, 2.8, 6.6, 0.4, 'A');

-- 4. Table suivis (pour le module de suivi utilisateur)
CREATE TABLE IF NOT EXISTS suivis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date_suivi DATE NOT NULL,
    poids FLOAT NOT NULL,
    calories_necessaires INT NOT NULL,
    etat VARCHAR(50) NOT NULL,
    eau_bue_du_jour FLOAT DEFAULT 0,
    etat_du_jour VARCHAR(100) DEFAULT NULL,
    jour_reussi TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table consultations
CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    suivi_id INT NOT NULL,
    date_consultation DATE NOT NULL,
    remarque TEXT NOT NULL,
    conseil TEXT NOT NULL,
    poids_cible FLOAT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (suivi_id) REFERENCES suivis(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fin du script
