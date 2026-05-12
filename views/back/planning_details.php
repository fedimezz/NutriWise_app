<?php
// views/back/planning_details.php

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'active': return 'badge-active';
        case 'draft': return 'badge-draft';
        case 'completed': return 'badge-completed';
        default: return 'badge-draft';
    }
}

function getStatusText($status) {
    switch ($status) {
        case 'active': return '✅ Actif';
        case 'draft': return '📝 Brouillon';
        case 'completed': return '🏆 Terminé';
        default: return $status;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du planning - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/admin-global.css">
    <style>
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        .info-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        .info-label {
            font-size: 0.8rem;
            color: #6b8a66;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2e7d32;
        }
        .day-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            padding: 15px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #c8e6c9;
        }
        .daily-stat {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 12px;
        }
        .daily-stat-value {
            font-size: 1.2rem;
            font-weight: 800;
            color: #2e7d32;
        }
        .daily-stat-label {
            font-size: 0.7rem;
            color: #6b8a66;
        }
        .menu-totals {
            margin-top: 15px;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 12px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .menu-total {
            font-size: 0.85rem;
        }
        .menu-total strong {
            color: #2e7d32;
        }
        @media (max-width: 768px) {
            .day-stats { grid-template-columns: repeat(2, 1fr); }
            .aliments-table { font-size: 0.75rem; }
        }
    </style>
</head>
<body>
<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>
        <nav>
            <a href="index.php?page=admin_dashboard">📊 Dashboard</a>
            <a href="index.php?page=admin_users">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes">📖 Recettes</a>
            <a href="index.php?page=admin_plannings" class="active">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1>📋 Détails du planning</h1>
            <div class="actions">
                <a href="index.php?page=admin_edit_planning&id=<?= $planning['id'] ?>" class="btn-edit">✏️ Modifier</a>
                <form method="POST" action="index.php?page=admin_delete_planning" style="display:inline;" onsubmit="return confirm('Supprimer ce planning ?')" novalidate>
                    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $planning['id'] ?>">
                    <button type="submit" class="btn-delete">🗑️ Supprimer</button>
                </form>
                <a href="index.php?page=admin_plannings" class="btn-cancel" style="padding:8px 16px;">← Retour</a>
            </div>
        </header>

        <!-- Informations générales -->
        <div class="info-card" style="background:white; border-radius:20px; padding:25px; margin-bottom:30px;">
            <h2 style="color:#2e7d32; margin-bottom:15px;"><?= htmlspecialchars($planning['name']) ?></h2>
            <p style="color:#6b8a66; margin-bottom:20px;"><?= htmlspecialchars($planning['description'] ?? 'Aucune description') ?></p>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Utilisateur</div>
                    <div class="info-value"><?= htmlspecialchars($planning['prenom'] . ' ' . $planning['nom']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($planning['email']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Période</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($planning['start_date'])) ?> - <?= date('d/m/Y', strtotime($planning['end_date'])) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Statut</div>
                    <div class="info-value">
                        <span class="badge <?= getStatusBadgeClass($planning['status']) ?>">
                            <?= getStatusText($planning['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plannings par jour -->
        <?php foreach ($planningsByDay as $dayIndex => $dayData): ?>
            <div class="day-card" style="background:white; border-radius:20px; margin-bottom:25px; overflow:hidden;">
                <div class="day-header" style="background:linear-gradient(135deg,#2e7d32,#4caf50); color:white; padding:15px 20px; font-size:1.2rem; font-weight:700;">
                    📆 <?= htmlspecialchars($dayData['jour']) ?>
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
                
                <?php foreach ($dayData['menus'] as $menu): ?>
                    <div class="menu-item" style="padding:20px; border-bottom:1px solid #c8e6c9;">
                        <div class="menu-title" style="font-size:1.1rem; font-weight:700; color:#2e7d32; margin-bottom:15px; display:flex; align-items:center; gap:10px;">
                            🍽️ <?= htmlspecialchars($menu['menu_name']) ?>
                        </div>
                        
                        <?php if (!empty($menu['aliments'])): ?>
                            <table class="admin-table">
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
                                            <td><strong><?= htmlspecialchars($aliment['nom']) ?></strong></td>
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
                        <?php else: ?>
                            <p style="color:#6b8a66; text-align:center; padding:20px;">Aucun aliment dans ce menu</p>
                        <?php endif; ?>
                        
                        <div class="menu-totals">
                            <div class="menu-total"><strong>📊 Total du menu :</strong></div>
                            <div class="menu-total">🔥 <?= round($menu['total_calories']) ?> kcal</div>
                            <div class="menu-total">💪 <?= round($menu['total_proteines'], 1) ?>g protéines</div>
                            <div class="menu-total">🍞 <?= round($menu['total_glucides'], 1) ?>g glucides</div>
                            <div class="menu-total">🧈 <?= round($menu['total_lipides'], 1) ?>g lipides</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </main>
</div>
</body>
</html>