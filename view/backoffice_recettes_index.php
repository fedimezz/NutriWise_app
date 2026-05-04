<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des recettes - NutriWise</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .dashboard { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: #1B4D1B; color: white; position: fixed; height: 100vh; }
        .sidebar .logo { padding: 25px 20px; font-size: 24px; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar nav a { display: block; padding: 12px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: background 0.3s; font-weight: normal; }
        .sidebar nav a:hover { background: #2E7D32; }
        .sidebar nav a.active { background: #4CAF50; }
        .main-content { flex: 1; margin-left: 280px; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: #1B4D1B; font-weight: bold; }
        .admin-badge { background: #4CAF50; color: white; padding: 8px 16px; border-radius: 20px; font-weight: normal; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2E7D32; color: white; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        
        .btn-add { background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; font-weight: normal; transition: background 0.3s; }
        .btn-add:hover { background: #2E7D32; }
        
        .btn-edit { background: #2196F3; color: white; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 12px; display: inline-block; transition: background 0.3s; font-weight: normal; }
        .btn-edit:hover { background: #0b7dda; }
        
        .btn-delete { background: #f44336; color: white; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 12px; display: inline-block; transition: background 0.3s; margin-left: 8px; font-weight: normal; }
        .btn-delete:hover { background: #d32f2f; }
        
        .badge-published { background: #4CAF50; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; font-weight: normal; }
        .badge-draft { background: #ff9800; color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; font-weight: normal; }
        
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #28a745; font-weight: normal; }
        .search-box { margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        .search-box input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .search-box button { background: #2E7D32; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: normal; transition: background 0.3s; }
        .search-box button:hover { background: #1B5E20; }
        
        .action-cell {
            white-space: nowrap;
            width: 130px;
        }
        .action-cell a {
            display: inline-block;
            text-align: center;
        }
        
        td {
            font-weight: normal;
        }
        
        @media (max-width: 768px) {
            .sidebar { width: 80px; }
            .sidebar .logo span:last-child, .sidebar nav a span:last-child { display: none; }
            .main-content { margin-left: 80px; }
            table { font-size: 12px; }
            th, td { padding: 8px 5px; }
            .btn-edit, .btn-delete { padding: 4px 8px; font-size: 10px; }
            .action-cell { width: 100px; }
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
                <a href="../controller/index.php?controller=recette&action=index&area=back" class="active">📖 Recettes</a>
                <a href="#">👥 Utilisateurs</a>
                <a href="#">📅 Plans</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>📖 Gestion des recettes</h1>
                <div class="admin-badge">Admin</div>
            </div>
            
            <a href="../controller/index.php?controller=recette&action=create&area=back" class="btn-add">+ Ajouter une recette</a>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="success">Opération réussie !</div>
            <?php endif; ?>
            
            <form method="GET" class="search-box">
                <input type="hidden" name="controller" value="recette">
                <input type="hidden" name="action" value="index">
                <input type="hidden" name="area" value="back">
                <input type="text" name="search" placeholder="Rechercher une recette..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit">🔍 Rechercher</button>
                <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <a href="../controller/index.php?controller=recette&action=index&area=back" style="background:#666; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:normal;">Annuler</a>
                <?php endif; ?>
            </form>
            
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Ingrédients</th>
                        <th>Difficulté</th>
                        <th>Saison</th>
                        <th>Durée</th>
                        <th>Statut</th>
                        <th>Score</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($recettes) > 0): ?>
                        <?php foreach($recettes as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['title']) ?></td>
                            <td><?= htmlspecialchars(substr($r['aliments_list'] ?? '-', 0, 50)) ?>...</td>
                            <td><?= $r['difficulte'] ?></td>
                            <td><?= $r['saison'] ?></td>
                            <td><?= $r['duree'] ?> min</td>
                            <td>
                                <span class="<?= $r['statut'] == 'Programmée' ? 'badge-published' : 'badge-draft' ?>"><?= $r['statut'] ?></span>
                                <?php if($r['statut'] == 'Programmée' && !empty($r['date_publication'])): ?>
                                    <br><small style="color:#666;">📅 <?= date('d/m/Y H:i', strtotime($r['date_publication'])) ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">⭐ <?= $r['score_durabilite'] ?></td>
                            <td class="action-cell" style="text-align: center;">
                                <a href="../controller/index.php?controller=recette&action=edit&id=<?= $r['id'] ?>&area=back" class="btn-edit">✏️ Modifier</a>
                                <a href="../controller/index.php?controller=recette&action=delete&id=<?= $r['id'] ?>&area=back" class="btn-delete" onclick="return confirm('Supprimer cette recette ?')">🗑️ Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center; padding: 40px;">Aucune recette trouvée</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>