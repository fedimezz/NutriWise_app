-- =========================================
-- SQL à exécuter dans phpMyAdmin
-- NutriWise - Modifications calendrier
-- =========================================

-- 1. Changer la colonne statut de ENUM à VARCHAR
ALTER TABLE recettes MODIFY COLUMN statut VARCHAR(20) NOT NULL DEFAULT 'Brouillon';

-- 2. Ajouter la colonne date_publication (si elle n'existe pas)
ALTER TABLE recettes ADD COLUMN date_publication DATETIME NULL DEFAULT NULL AFTER statut;

-- 3. Mettre à jour les anciennes recettes "Publié" vers "Programmée"
UPDATE recettes SET statut = 'Programmée', date_publication = NOW() WHERE statut = 'Publié' OR statut = 'Publie';

-- 4. Nettoyer les statuts vides
UPDATE recettes SET statut = 'Brouillon' WHERE statut = '' OR statut IS NULL;
