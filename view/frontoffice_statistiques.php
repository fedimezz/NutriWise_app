<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques - NutriWise</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f8f0; }
        .container { max-width: 1300px; margin: 0 auto; padding: 20px; }
        
        /* ===== NAVIGATION - IDENTIQUE À L'ACCUEIL ===== */
        .navbar { background: #1B4D1B; padding: 12px 20px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: nowrap; gap: 10px; width: 100%; }
        .navbar .logo { color: white; font-size: 20px; font-weight: bold; white-space: nowrap; }
        .nav-links { display: flex; gap: 8px; flex-wrap: nowrap; }
        .nav-links a { color: white; text-decoration: none; padding: 6px 12px; border-radius: 8px; transition: background 0.3s; font-weight: normal; font-size: 14px; white-space: nowrap; }
        .nav-links a:hover, .nav-links a.active { background: #4CAF50; }
        .auth-buttons { display: flex; gap: 6px; flex-wrap: nowrap; }
        .btn-login { background: transparent; border: 1px solid white; color: white; padding: 6px 12px; border-radius: 25px; text-decoration: none; font-weight: normal; font-size: 13px; white-space: nowrap; }
        .btn-login:hover { background: white; color: #1B4D1B; }
        .btn-register { background: #4CAF50; color: white; padding: 6px 12px; border-radius: 25px; text-decoration: none; font-weight: normal; font-size: 13px; white-space: nowrap; }
        .btn-register:hover { background: #2E7D32; }
        
        .back-link { margin: 20px 0; }
        .back-link a { color: #4CAF50; text-decoration: none; font-weight: bold; }
        
        h1 { color: #2E7D32; margin-bottom: 10px; font-size: 28px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 16px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-card h3 { color: #2E7D32; margin-bottom: 15px; border-bottom: 2px solid #4CAF50; padding-bottom: 8px; font-size: 18px; }
        .stat-number { font-size: 36px; font-weight: bold; color: #1B4D1B; text-align: center; margin: 15px 0; }
        .stat-label { text-align: center; color: #666; }
        
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-top: 15px; }
        .stat-item { text-align: center; padding: 10px; background: #f9f9f9; border-radius: 8px; }
        .stat-value { font-size: 22px; font-weight: bold; color: #2E7D32; }
        .stat-desc { font-size: 11px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #e8f5e9; color: #2E7D32; font-weight: bold; }
        
        .progress-bar { background: #e0e0e0; border-radius: 10px; height: 25px; overflow: hidden; margin: 10px 0; }
        .progress-fill { height: 100%; color: white; font-size: 11px; line-height: 25px; padding-left: 10px; }
        
        .badge-A, .badge-B, .badge-C, .badge-D, .badge-E { padding: 3px 8px; border-radius: 20px; font-size: 10px; display: inline-block; }
        .badge-A { background: #2E7D32; color: white; }
        .badge-B { background: #8BC34A; color: white; }
        .badge-C { background: #FFC107; color: white; }
        .badge-D { background: #FF9800; color: white; }
        .badge-E { background: #f44336; color: white; }
        
        .footer { text-align: center; margin-top: 50px; padding: 20px; color: #666; border-top: 1px solid #ddd; }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .nav-links, .auth-buttons { flex-wrap: wrap; justify-content: center; }
            .stats-grid { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- BARRE DE NAVIGATION IDENTIQUE À L'ACCUEIL -->
        <nav class="navbar">
            <div class="logo">🌿 NutriWise</div>
            <div class="nav-links">
                <a href="../controller/index.php">Accueil</a>
                <a href="../controller/index.php?controller=aliment&action=index&area=front">Aliments</a>
                <a href="../controller/index.php?controller=recette&action=index&area=front">Recettes</a>
                <a href="#">Suivi</a>
            </div>
            <div class="auth-buttons">
                <a href="#" class="btn-login">Connexion</a>
                <a href="#" class="btn-register">Inscription</a>
            </div>
        </nav>

        <div class="back-link">
            <a href="../controller/index.php">← Retour à l'accueil</a>
        </div>

        <h1>📈 Statistiques NutriWise</h1>
        <p class="subtitle">Découvrez les chiffres clés de notre base de données nutritionnelle</p>

        <!-- Stats globales -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>🥗 Aliments</h3>
                <div class="stat-number"><?= $stats['aliments']['total_aliments'] ?></div>
                <div class="stat-label">aliments référencés</div>
                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['aliments']['aliments_durable']['pourcentage'] ?>%</div>
                        <div class="stat-desc">Aliments durables</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <h3>📖 Recettes</h3>
                <div class="stat-number"><?= $stats['recettes']['total_recettes']['total'] ?></div>
                <div class="stat-label">recettes disponibles</div>
                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['recettes']['total_recettes']['publiees'] ?></div>
                        <div class="stat-desc">Programmées</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['recettes']['score_durabilite_moyen'] ?>⭐</div>
                        <div class="stat-desc">Score moyen</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <h3>⏱️ Temps de préparation</h3>
                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['recettes']['duree']['moyenne'] ?> min</div>
                        <div class="stat-desc">Moyenne</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['recettes']['duree']['min'] ?> min</div>
                        <div class="stat-desc">Plus rapide</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['recettes']['duree']['max'] ?> min</div>
                        <div class="stat-desc">Plus longue</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Moyennes nutritionnelles -->
        <div class="stat-card">
            <h3>📊 Moyennes nutritionnelles (pour 100g)</h3>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['moyennes_nutritionnelles']['moyennes']['calories'] ?> kcal</div>
                    <div class="stat-desc">Calories</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['moyennes_nutritionnelles']['moyennes']['proteines'] ?> g</div>
                    <div class="stat-desc">Protéines</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['moyennes_nutritionnelles']['moyennes']['glucides'] ?> g</div>
                    <div class="stat-desc">Glucides</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['moyennes_nutritionnelles']['moyennes']['lipides'] ?> g</div>
                    <div class="stat-desc">Lipides</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['moyennes_nutritionnelles']['moyennes']['fibres'] ?> g</div>
                    <div class="stat-desc">Fibres</div>
                </div>
            </div>
        </div>

        <!-- Répartition Nutri-Score -->
        <div class="stat-card">
            <h3>🏆 Répartition Nutri-Score</h3>
            <?php foreach($stats['aliments']['par_nutri_score'] as $item): ?>
            <?php
                $couleur = '';
                if($item['nutri_score'] == 'A') $couleur = '#2E7D32';
                elseif($item['nutri_score'] == 'B') $couleur = '#8BC34A';
                elseif($item['nutri_score'] == 'C') $couleur = '#FFC107';
                elseif($item['nutri_score'] == 'D') $couleur = '#FF9800';
                else $couleur = '#f44336';
                $pourcentage = ($item['nombre'] / $stats['aliments']['total_aliments']) * 100;
            ?>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $pourcentage ?>%; background: <?= $couleur ?>;">
                    <?= $item['nutri_score'] ?> : <?= $item['nombre'] ?> aliments (<?= round($pourcentage,1) ?>%)
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Top ingrédients -->
        <div class="stat-card">
            <h3>🌿 Ingrédients les plus populaires</h3>
            <table>
                <thead>
                    <tr><th>Ingrédient</th><th>Catégorie</th><th>Utilisations</th></tr>
                </thead>
                <tbody>
                    <?php foreach($stats['ingredients_populaires'] as $ing): ?>
                    <tr>
                        <td><?= $ing['ingredient'] ?></td>
                        <td><?= $ing['categorie'] ?? '-' ?></td>
                        <td><?= $ing['nombre_recettes'] ?> recette(s)</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Top recettes -->
        <div class="stat-card">
            <h3>⭐ Top 5 des recettes les mieux notées</h3>
            <table>
                <thead>
                    <tr><th>Recette</th><th>Note moyenne</th></tr>
                </thead>
                <tbody>
                    <?php foreach($stats['recettes']['top_recettes_notes'] as $rec): ?>
                    <tr>
                        <td><?= htmlspecialchars($rec['title']) ?></td>
                        <td>⭐ <?= round($rec['note_moyenne'], 1) ?>/5</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>© 2025 NutriWise - Nutrition intelligente et durable</p>
        </div>
    </div>
</body>
</html>