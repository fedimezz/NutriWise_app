USE nutriwise;

ALTER TABLE suivis
    ADD COLUMN IF NOT EXISTS eau_bue_du_jour FLOAT DEFAULT 0,
    ADD COLUMN IF NOT EXISTS etat_du_jour VARCHAR(100) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS jour_reussi TINYINT(1) DEFAULT 0;

CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    suivi_id INT NOT NULL,
    date_consultation DATE NOT NULL,
    remarque TEXT NOT NULL,
    conseil TEXT NOT NULL,
    poids_cible FLOAT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_consultations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_consultations_suivi FOREIGN KEY (suivi_id) REFERENCES suivis(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
