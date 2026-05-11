<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du planning - NutriWise Admin</title>
    <link rel="stylesheet" href="views/assets/css/users.css">
    <style>
        .detail-header {
            background: linear-gradient(135deg, #2E7D32 0%, #1B4D1B 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .detail-header h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: white;
        }

        .detail-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .info-label {
            font-weight: 600;
            color: #2E7D32;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.1rem;
            color: #333;
            word-break: break-word;
        }

        .description-box {
            background: #f1f8e9;
            border-left: 4px solid #2E7D32;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .menus-section {
            margin-top: 2rem;
        }

        .menus-section h2 {
            color: #2E7D32;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .menu-card {
            background: white;
            border: 2px solid #C8E6C9;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .menu-card h3 {
            color: #2E7D32;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .menu-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .menu-detail-item {
            background: #f5f5f5;
            padding: 0.8rem;
            border-radius: 6px;
        }

        .menu-detail-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
        }

        .menu-detail-value {
            font-size: 1rem;
            color: #333;
            font-weight: 600;
            margin-top: 0.3rem;
        }

        .empty-state {
            background: #fff3e0;
            border: 2px dashed #FF9800;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            color: #666;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-edit {
            background: #2E7D32;
            color: white;
        }

        .btn-edit:hover {
            background: #1B4D1B;
            transform: translateY(-2px);
        }

        .btn-back {
            background: #e0e0e0;
            color: #333;
        }

        .btn-back:hover {
            background: #ccc;
            transform: translateY(-2px);
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-draft {
            background: #FFF9C4;
            color: #F57F17;
        }

        .status-active {
            background: #C8E6C9;
            color: #1B5E20;
        }

        .status-completed {
            background: #B2DFDB;
            color: #004D40;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">🌿 NutriWise</div>
            <nav>
                <a href="index.php?page=admin_dashboard">📊 Tableau de bord</a>
                <a href="index.php?page=admin_users">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments">🥗 Aliments</a>
                <a href="index.php?page=admin_plannings" class="active">🗓️ Plannings</a>
            </nav>
            <a href="index.php?page=home" class="back-to-site">← Retour au site</a>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <div class="detail-header">
                <h1>📋 <?= htmlspecialchars($planning['name']) ?></h1>
                <span class="status-badge status-<?= htmlspecialchars($planning['status']) ?>">
                    <?= ucfirst(htmlspecialchars($planning['status'])) ?>
                </span>
            </div>

            <div class="detail-info">
                <div class="info-card">
                    <div class="info-label">Utilisateur</div>
                    <div class="info-value"><?= htmlspecialchars($planning['user_name'] ?: 'Non affecté') ?></div>
                </div>

                <div class="info-card">
                    <div class="info-label">Date de début</div>
                    <div class="info-value"><?= htmlspecialchars($planning['start_date']) ?></div>
                </div>

                <div class="info-card">
                    <div class="info-label">Date de fin</div>
                    <div class="info-value"><?= htmlspecialchars($planning['end_date']) ?></div>
                </div>

                <div class="info-card">
                    <div class="info-label">Nombre de menus</div>
                    <div class="info-value"><?= count($planningMenus) ?> menu(s)</div>
                </div>

                <div class="info-card">
                    <div class="info-label">Nutritionniste</div>
                    <div class="info-value"><?= htmlspecialchars($planning['nutritionist_name'] ?: 'Non spécifié') ?></div>
                </div>

                <div class="info-card">
                    <div class="info-label">Créé le</div>
                    <div class="info-value"><?= htmlspecialchars($planning['created_at'] ?: 'N/A') ?></div>
                </div>
            </div>

            <?php if($planning['description']): ?>
                <div class="description-box">
                    <strong style="color: #2E7D32;">Description :</strong>
                    <div style="margin-top: 0.8rem; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($planning['description'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="menus-section">
                <h2>📚 Menus associés</h2>

                <?php if(empty($planningMenus)): ?>
                    <div class="empty-state">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📭</div>
                        <p>Ce planning ne contient pas encore de menus.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($planningMenus as $menu): ?>
                        <div class="menu-card">
                            <h3><?= htmlspecialchars($menu['title']) ?></h3>
                            
                            <div class="menu-details">
                                <div class="menu-detail-item">
                                    <div class="menu-detail-label">Objectif</div>
                                    <div class="menu-detail-value"><?= htmlspecialchars($menu['goal']) ?></div>
                                </div>

                                <div class="menu-detail-item">
                                    <div class="menu-detail-label">Calories ciblées</div>
                                    <div class="menu-detail-value"><?= intval($menu['calories_target']) ?> kcal</div>
                                </div>

                                <div class="menu-detail-item">
                                    <div class="menu-detail-label">Statut</div>
                                    <div class="menu-detail-value"><?= $menu['is_active'] ? '✅ Actif' : '⏸️ Inactif' ?></div>
                                </div>

                                <div class="menu-detail-item">
                                    <div class="menu-detail-label">Nombre de repas</div>
                                    <div class="menu-detail-value"><?= intval($menu['meal_count'] ?? 0) ?> repas</div>
                                </div>
                            </div>

                            <a href="index.php?page=menu_details&id=<?= $menu['id'] ?>" 
                               style="display: inline-block; margin-top: 1rem; padding: 0.6rem 1.2rem; 
                                      background: #2E7D32; color: white; text-decoration: none; 
                                      border-radius: 6px; font-weight: 600; transition: all 0.3s;"
                               onmouseover="this.style.background='#1B4D1B'; this.style.transform='translateY(-2px)'"
                               onmouseout="this.style.background='#2E7D32'; this.style.transform='translateY(0)'">
                                Voir les détails du menu
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="action-buttons">
                <a href="index.php?page=admin_edit_planning&id=<?= $planning['id'] ?>" class="btn-action btn-edit">✏️ Modifier le planning</a>
                <a href="index.php?page=admin_plannings" class="btn-action btn-back">← Retour à la liste</a>
            </div>
        </main>
    </div>
</body>
</html>
