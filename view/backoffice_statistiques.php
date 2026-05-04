<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques - NutriWise Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .dashboard { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: #1B4D1B; color: white; position: fixed; height: 100vh; }
        .sidebar .logo { padding: 25px 20px; font-size: 24px; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar nav a { display: block; padding: 12px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: background 0.3s; }
        .sidebar nav a:hover { background: #2E7D32; }
        .sidebar nav a.active { background: #4CAF50; }
        .main-content { flex: 1; margin-left: 280px; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: #1B4D1B; }
        .admin-badge { background: #4CAF50; color: white; padding: 8px 16px; border-radius: 20px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-card h3 { color: #2E7D32; margin-bottom: 15px; border-bottom: 2px solid #4CAF50; padding-bottom: 8px; }
        .stat-number { font-size: 36px; font-weight: bold; color: #1B4D1B; text-align: center; margin: 15px 0; }
        .stat-label { text-align: center; color: #666; }
        
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px; }
        .stat-item { text-align: center; padding: 10px; background: #f9f9f9; border-radius: 8px; }
        .stat-value { font-size: 24px; font-weight: bold; color: #2E7D32; }
        .stat-desc { font-size: 12px; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #e8f5e9; color: #2E7D32; }
        
        .progress-bar { background: #e0e0e0; border-radius: 10px; height: 25px; overflow: hidden; margin: 10px 0; }
        .progress-fill { height: 100%; color: white; font-size: 12px; line-height: 25px; padding-left: 10px; }

        .chart-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 24px; }
        .chart-title { color: #2E7D32; margin-bottom: 14px; border-bottom: 2px solid #4CAF50; padding-bottom: 8px; }
        .chart-subtitle { color: #666; font-size: 13px; margin-bottom: 16px; }
        .bar-chart { display: grid; gap: 10px; }
        .bar-row { display: grid; grid-template-columns: 50px 1fr 100px; align-items: center; gap: 12px; }
        .bar-label { font-weight: 700; color: #1B4D1B; text-align: center; }
        .bar-track { background: #edf3ed; border-radius: 999px; height: 24px; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 999px; transition: width 0.4s ease; min-width: 2%; }
        .bar-value { color: #2f2f2f; font-size: 12px; text-align: right; }
        .chart-legend { display: flex; flex-wrap: wrap; gap: 10px 14px; margin-top: 14px; }
        .chart-legend span { display: inline-flex; align-items: center; gap: 6px; color: #666; font-size: 12px; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .pie-layout { display: flex; flex-wrap: wrap; align-items: center; gap: 24px; }
        .pie-chart {
            width: 230px;
            height: 230px;
            border-radius: 50%;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05), 0 6px 18px rgba(0,0,0,0.08);
            flex: 0 0 auto;
        }
        .pie-legend-list { display: grid; gap: 8px; min-width: 220px; }
        .pie-legend-item { display: flex; justify-content: space-between; gap: 12px; align-items: center; font-size: 13px; color: #444; }
        .pie-legend-left { display: inline-flex; align-items: center; gap: 8px; }
        .pie-legend-value { font-weight: 600; color: #1B4D1B; }
        
        .badge-A, .badge-B, .badge-C, .badge-D, .badge-E { padding: 3px 8px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-A { background: #2E7D32; color: white; }
        .badge-B { background: #8BC34A; color: white; }
        .badge-C { background: #FFC107; color: white; }
        .badge-D { background: #FF9800; color: white; }
        .badge-E { background: #f44336; color: white; }
        
        @media (max-width: 768px) {
            .sidebar { width: 80px; }
            .sidebar .logo span:last-child, .sidebar nav a span:last-child { display: none; }
            .main-content { margin-left: 80px; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo"><span>🌿</span><span>NutriWise</span></div>
            <nav>
                <a href="../controller/index.php?area=back">📊 Dashboard</a>
                <a href="../controller/index.php?controller=aliment&action=index&area=back">🥗 Aliments</a>
                <a href="../controller/index.php?controller=recette&action=index&area=back">📖 Recettes</a>
                <a href="../controller/index.php?controller=statistique&action=index" class="active">📈 Statistiques</a>
                <a href="#">👥 Utilisateurs</a>
                <a href="#">📅 Plans</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>📈 Statistiques NutriWise</h1>
                <div class="admin-badge">Admin</div>
            </div>

            <?php
                $nutriData = $stats['aliments']['par_nutri_score'] ?? [];
                $nutriTotal = max(1, (int)($stats['aliments']['total_aliments'] ?? 0));
                $nutriColors = ['A' => '#2E7D32', 'B' => '#66BB6A', 'C' => '#FBC02D', 'D' => '#FB8C00', 'E' => '#E53935'];
                $segments = [];
                $legendRows = [];
                $start = 0;

                foreach($nutriData as $item) {
                    $label = strtoupper($item['nutri_score'] ?? '?');
                    $count = (int)($item['nombre'] ?? 0);
                    $percentage = round(($count / $nutriTotal) * 100, 1);
                    $color = $nutriColors[$label] ?? '#90A4AE';
                    $end = $start + $percentage;
                    $segments[] = $color . ' ' . $start . '% ' . $end . '%';
                    $legendRows[] = ['label' => $label, 'count' => $count, 'percentage' => $percentage, 'color' => $color];
                    $start = $end;
                }

                $pieGradient = !empty($segments) ? implode(', ', $segments) : '#e0e0e0 0% 100%';
            ?>

            <div class="chart-card">
                <h3 class="chart-title">📊 Diagramme Nutri-Score</h3>
                <p class="chart-subtitle">Vue rapide et lisible de la qualité nutritionnelle des aliments.</p>
                <div class="pie-layout">
                    <div class="pie-chart" style="background: conic-gradient(<?= $pieGradient ?>);"></div>
                    <div class="pie-legend-list">
                        <?php foreach($legendRows as $row): ?>
                            <div class="pie-legend-item">
                                <div class="pie-legend-left">
                                    <i class="legend-dot" style="background:<?= $row['color'] ?>;"></i>
                                    <span>Nutri-Score <?= htmlspecialchars($row['label']) ?></span>
                                </div>
                                <div class="pie-legend-value"><?= $row['count'] ?> (<?= $row['percentage'] ?>%)</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="chart-legend">
                    <span><i class="legend-dot" style="background:#2E7D32;"></i>Excellent (A)</span>
                    <span><i class="legend-dot" style="background:#66BB6A;"></i>Bon (B)</span>
                    <span><i class="legend-dot" style="background:#FBC02D;"></i>Moyen (C)</span>
                    <span><i class="legend-dot" style="background:#FB8C00;"></i>Faible (D)</span>
                    <span><i class="legend-dot" style="background:#E53935;"></i>À limiter (E)</span>
                </div>
            </div>

            <!-- Stats globales -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>🥗 Aliments</h3>
                    <div class="stat-number"><?= $stats['aliments']['total_aliments'] ?></div>
                    <div class="stat-label">Total aliments</div>
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['aliments']['aliments_durable']['pourcentage'] ?>%</div>
                            <div class="stat-desc">Aliments durables</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['aliments']['aliments_durable']['durables'] ?>/<?= $stats['aliments']['aliments_durable']['total'] ?></div>
                            <div class="stat-desc">Durables / Total</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>📖 Recettes</h3>
                    <div class="stat-number"><?= $stats['recettes']['total_recettes']['total'] ?></div>
                    <div class="stat-label">Total recettes</div>
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['recettes']['total_recettes']['publiees'] ?></div>
                            <div class="stat-desc">Programmées</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['recettes']['total_recettes']['brouillons'] ?></div>
                            <div class="stat-desc">Brouillons</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['recettes']['score_durabilite_moyen'] ?>⭐</div>
                            <div class="stat-desc">Score moyen</div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>⏱️ Durée des recettes</h3>
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['recettes']['duree']['moyenne'] ?> min</div>
                            <div class="stat-desc">Moyenne</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $stats['recettes']['duree']['min'] ?> min</div>
                            <div class="stat-desc">Plus courte</div>
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
                        Nutri-Score <?= $item['nutri_score'] ?> : <?= $item['nombre'] ?> aliments (<?= round($pourcentage,1) ?>%)
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Catégories -->
            <div class="stat-card">
                <h3>🥑 Aliments par catégorie</h3>
                <table>
                    <thead><tr><th>Catégorie</th><th>Nombre</th></tr></thead>
                    <tbody>
                    <?php foreach($stats['aliments']['par_categorie'] as $cat): ?>
                        <tr><td><?= $cat['categorie'] ?></td><td><?= $cat['nombre'] ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top ingrédients -->
            <div class="stat-card">
                <h3>🌿 Ingrédients les plus utilisés</h3>
                <table>
                    <thead><tr><th>Ingrédient</th><th>Catégorie</th><th>Recettes</th></tr></thead>
                    <tbody>
                    <?php foreach($stats['ingredients_populaires'] as $ing): ?>
                        <tr>
                            <td><?= $ing['ingredient'] ?></td>
                            <td><?= $ing['categorie'] ?? '-' ?></td>
                            <td><?= $ing['nombre_recettes'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top recettes notées -->
            <div class="stat-card">
                <h3>⭐ Top recettes les mieux notées</h3>
                <table>
                    <thead><tr><th>Recette</th><th>Note</th></tr></thead>
                    <tbody>
                    <?php foreach($stats['recettes']['top_recettes_notes'] as $rec): ?>
                        <tr>
                            <td><?= htmlspecialchars($rec['title']) ?></td>
                            <td>⭐ <?= round($rec['note_moyenne'], 1) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>
</html>