<?php
class Statistique {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Statistiques générales des aliments
     */
    public function getStatsAliments() {
        $stats = [];
        
        // Nombre total d'aliments
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM aliments");
        $stats['total_aliments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Nombre par catégorie
        $stmt = $this->conn->query("SELECT 
            c.name as categorie, 
            COUNT(a.id) as nombre
        FROM categories c
        LEFT JOIN aliments a ON c.id = a.category_id
        GROUP BY c.id
        ORDER BY nombre DESC");
        $stats['par_categorie'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Répartition par Eco-Score
        $stmt = $this->conn->query("SELECT 
            eco_score, 
            COUNT(*) as nombre 
        FROM aliments 
        GROUP BY eco_score 
        ORDER BY eco_score");
        $stats['par_eco_score'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Répartition par Nutri-Score
        $stmt = $this->conn->query("SELECT 
            nutri_score, 
            COUNT(*) as nombre 
        FROM aliments 
        WHERE nutri_score IS NOT NULL
        GROUP BY nutri_score 
        ORDER BY FIELD(nutri_score, 'A', 'B', 'C', 'D', 'E')");
        $stats['par_nutri_score'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Répartition par saison
        $stmt = $this->conn->query("SELECT 
            saison, 
            COUNT(*) as nombre 
        FROM aliments 
        GROUP BY saison");
        $stats['par_saison'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Aliments durables
        $stmt = $this->conn->query("SELECT 
            SUM(durable) as durables,
            COUNT(*) as total
        FROM aliments");
        $durable = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['aliments_durable'] = [
            'durables' => $durable['durables'],
            'total' => $durable['total'],
            'pourcentage' => $durable['total'] > 0 ? round($durable['durables'] / $durable['total'] * 100, 1) : 0
        ];
        
        return $stats;
    }
    
    /**
     * Moyennes nutritionnelles des aliments
     */
    public function getMoyennesNutritionnelles() {
        $stmt = $this->conn->query("SELECT 
            AVG(calories) as calories,
            AVG(proteins) as proteines,
            AVG(glucides) as glucides,
            AVG(lipids) as lipides,
            AVG(fibres) as fibres,
            MIN(calories) as min_calories,
            MAX(calories) as max_calories,
            MIN(proteins) as min_proteines,
            MAX(proteins) as max_proteines
        FROM aliments");
        $moyennes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'moyennes' => [
                'calories' => round($moyennes['calories'] ?? 0, 1),
                'proteines' => round($moyennes['proteines'] ?? 0, 1),
                'glucides' => round($moyennes['glucides'] ?? 0, 1),
                'lipides' => round($moyennes['lipides'] ?? 0, 1),
                'fibres' => round($moyennes['fibres'] ?? 0, 1)
            ],
            'extremes' => [
                'calories' => [
                    'min' => round($moyennes['min_calories'] ?? 0, 1),
                    'max' => round($moyennes['max_calories'] ?? 0, 1)
                ],
                'proteines' => [
                    'min' => round($moyennes['min_proteines'] ?? 0, 1),
                    'max' => round($moyennes['max_proteines'] ?? 0, 1)
                ]
            ]
        ];
    }
    
    /**
     * Top aliments par nutriment
     */
    public function getTopAliments($nutriment, $limit = 5) {
        $nutrimentValide = in_array($nutriment, ['calories', 'proteins', 'glucides', 'lipids', 'fibres']) 
            ? $nutriment : 'calories';
        
        $query = "SELECT id, nom, {$nutrimentValide} as valeur, 
                  (SELECT name FROM categories WHERE id = category_id) as categorie
                  FROM aliments 
                  ORDER BY {$nutrimentValide} DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Statistiques des recettes
     */
    public function getStatsRecettes() {
        $stats = [];
        
        // Nombre total de recettes
        $stmt = $this->conn->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN statut = 'Programmée' THEN 1 ELSE 0 END) as publiees,
            SUM(CASE WHEN statut = 'Brouillon' THEN 1 ELSE 0 END) as brouillons
        FROM recettes");
        $stats['total_recettes'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Répartition par difficulté
        $stmt = $this->conn->query("SELECT 
            difficulte, 
            COUNT(*) as nombre 
        FROM recettes 
        GROUP BY difficulte");
        $stats['par_difficulte'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Répartition par saison
        $stmt = $this->conn->query("SELECT 
            saison, 
            COUNT(*) as nombre 
        FROM recettes 
        GROUP BY saison");
        $stats['par_saison'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Durée moyenne
        $stmt = $this->conn->query("SELECT 
            AVG(duree) as duree_moyenne,
            MIN(duree) as duree_min,
            MAX(duree) as duree_max
        FROM recettes");
        $duree = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['duree'] = [
            'moyenne' => round($duree['duree_moyenne'] ?? 0, 0),
            'min' => $duree['duree_min'] ?? 0,
            'max' => $duree['duree_max'] ?? 0
        ];
        
        // Score durabilité moyen
        $stmt = $this->conn->query("SELECT 
            AVG(score_durabilite) as score_moyen
        FROM recettes");
        $stats['score_durabilite_moyen'] = round($stmt->fetch(PDO::FETCH_ASSOC)['score_moyen'] ?? 0, 1);
        
        // Top recettes notées
        $stmt = $this->conn->query("SELECT 
            r.id, r.title, COALESCE(AVG(urr.rating), 0) as note_moyenne
        FROM recettes r
        LEFT JOIN user_recette_ratings urr ON r.id = urr.recette_id
        WHERE r.statut = 'Programmée'
        GROUP BY r.id
        ORDER BY note_moyenne DESC
        LIMIT 5");
        $stats['top_recettes_notes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Recettes les plus consultées
        $stmt = $this->conn->query("SELECT 
            r.id, r.title, COUNT(urv.id) as vues
        FROM recettes r
        LEFT JOIN user_recette_views urv ON r.id = urv.recette_id
        WHERE r.statut = 'Programmée'
        GROUP BY r.id
        ORDER BY vues DESC
        LIMIT 5");
        $stats['recettes_populaires'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    /**
     * Analyse des ingrédients les plus utilisés
     */
    public function getIngredientsPopulaires($limit = 10) {
        $query = "SELECT 
            a.id, a.nom as ingredient,
            COUNT(ra.recette_id) as nombre_recettes,
            c.name as categorie
        FROM aliments a
        JOIN recette_aliments ra ON a.id = ra.aliment_id
        LEFT JOIN categories c ON a.category_id = c.id
        GROUP BY a.id
        ORDER BY nombre_recettes DESC
        LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Dashboard complet
     */
    public function getDashboardStats() {
        return [
            'aliments' => $this->getStatsAliments(),
            'recettes' => $this->getStatsRecettes(),
            'moyennes_nutritionnelles' => $this->getMoyennesNutritionnelles(),
            'ingredients_populaires' => $this->getIngredientsPopulaires(5),
            'top_aliments_proteines' => $this->getTopAliments('proteins', 5),
            'top_aliments_calories' => $this->getTopAliments('calories', 5)
        ];
    }
}
?>