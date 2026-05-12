<?php
// views/front/nutrition_plan_details.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($planning['name']) ?> - NutriWise</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .header .dates {
            opacity: 0.9;
            margin-bottom: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }
        
        .status-draft {
            background: #f39c12;
            color: white;
        }
        
        .status-active {
            background: #27ae60;
            color: white;
        }
        
        .status-completed {
            background: #3498db;
            color: white;
        }
        
        .btn-back {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 15px;
            transition: background 0.3s;
        }
        
        .btn-back:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .day-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .day-header {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 20px;
            font-size: 1.3em;
            font-weight: bold;
        }
        
        .day-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .daily-stat {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .daily-stat-value {
            font-size: 1.2em;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .daily-stat-label {
            font-size: 0.8em;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .menus-container {
            padding: 20px;
        }
        
        .menu-item {
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 20px;
        }
        
        .menu-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #27ae60;
        }
        
        .aliments-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .aliments-table th {
            text-align: left;
            padding: 10px;
            background: #e9ecef;
            color: #495057;
        }
        
        .aliments-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .aliments-table tr:hover {
            background: #f1f3f5;
        }
        
        .menu-totals {
            margin-top: 15px;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 8px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .menu-total {
            font-size: 0.9em;
        }
        
        .menu-total strong {
            color: #27ae60;
        }
        
        @media (max-width: 768px) {
            .day-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .aliments-table {
                font-size: 0.85em;
            }
            
            .menu-totals {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 <?= e($planning['name']) ?></h1>
            <div class="dates">
                📅 Du <?= date('d/m/Y', strtotime($planning['start_date'])) ?> au <?= date('d/m/Y', strtotime($planning['end_date'])) ?>
            </div>
            <div class="status-badge status-<?= e($planning['status']) ?>">
                <?php
                $statusText = [
                    'draft' => '📝 Brouillon',
                    'active' => '✅ Actif',
                    'completed' => '🏆 Terminé'
                ];
                echo $statusText[$planning['status']] ?? $planning['status'];
                ?>
            </div>
            <?php if ($planning['description']): ?>
                <p style="margin-top: 15px; opacity: 0.95;">📝 <?= e($planning['description']) ?></p>
            <?php endif; ?>
            <a href="index.php?page=nutrition_plans" class="btn-back">← Retour à mes plannings</a>
        </div>
        
        <?php foreach ($planningsByDay as $dayIndex => $dayData): ?>
            <div class="day-card">
                <div class="day-header">
                    📆 <?= e($dayData['jour']) ?>
                </div>
                
                <div class="day-stats">
                    <div class="daily-stat">
                        <div class="daily-stat-value"><?= round($dailyTotals[$dayIndex]['calories']) ?> kcal</div>
                        <div class="daily-stat-label">Calories</div>
                    </div>
                    <div class="daily-stat">
                        <div class="daily-stat-value"><?= round($dailyTotals[$dayIndex]['proteines']) ?> g</div>
                        <div class="daily-stat-label">Protéines</div>
                    </div>
                    <div class="daily-stat">
                        <div class="daily-stat-value"><?= round($dailyTotals[$dayIndex]['glucides']) ?> g</div>
                        <div class="daily-stat-label">Glucides</div>
                    </div>
                    <div class="daily-stat">
                        <div class="daily-stat-value"><?= round($dailyTotals[$dayIndex]['lipides']) ?> g</div>
                        <div class="daily-stat-label">Lipides</div>
                    </div>
                </div>
                
                <div class="menus-container">
                    <?php foreach ($dayData['menus'] as $menu): ?>
                        <div class="menu-item">
                            <div class="menu-title">🍽️ <?= e($menu['menu_name']) ?></div>
                            
                            <?php if (!empty($menu['aliments'])): ?>
                                <table class="aliments-table">
                                    <thead>
                                        <tr>
                                            <th>Aliment</th>
                                            <th>Quantité</th>
                                            <th>Calories</th>
                                            <th>Protéines</th>
                                            <th>Glucides</th>
                                            <th>Lipides</th>
                                            <th>Eco Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($menu['aliments'] as $aliment): ?>
                                            <tr>
                                                <td><?= e($aliment['nom']) ?></td>
                                                <td><?= $aliment['quantite'] ?>g</td>
                                                <td><?= round($aliment['calories'] * $aliment['quantite'] / 100) ?> kcal</td>
                                                <td><?= round($aliment['proteines'] * $aliment['quantite'] / 100, 1) ?>g</td>
                                                <td><?= round($aliment['glucides'] * $aliment['quantite'] / 100, 1) ?>g</td>
                                                <td><?= round($aliment['lipides'] * $aliment['quantite'] / 100, 1) ?>g</td>
                                                <td>⭐ <?= $aliment['eco_score'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                            
                            <div class="menu-totals">
                                <div class="menu-total"><strong>📊 Total menu:</strong></div>
                                <div class="menu-total">🔥 <?= round($menu['total_calories']) ?> kcal</div>
                                <div class="menu-total">💪 <?= round($menu['total_proteines'], 1) ?>g protéines</div>
                                <div class="menu-total">🍞 <?= round($menu['total_glucides'], 1) ?>g glucides</div>
                                <div class="menu-total">🧈 <?= round($menu['total_lipides'], 1) ?>g lipides</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>