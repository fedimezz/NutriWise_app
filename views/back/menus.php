<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menus - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/users.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">🌿 NutriWise</div>
            <nav>
                <a href="index.php?page=admin_dashboard">📊 Tableau de bord</a>
                <a href="index.php?page=admin_users">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments">🥗 Aliments</a>
                <a href="index.php?page=admin_plannings">🗓️ Plannings</a>
            </nav>
            <a href="index.php?page=home" class="back-to-site">← Retour au site</a>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Gestion des menus</h1>
                <button class="btn-add" onclick="location.href='index.php?page=admin_add_menu'">+ Ajouter un menu</button>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <p class="alert-message" style="color:green; margin-top:10px; font-weight:bold; background:#e8f5e9; padding:10px; border-radius:5px;">
                    <?= htmlspecialchars($_GET['success']) ?>
                </p>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <p class="alert-message" style="color:red; margin-top:10px; font-weight:bold; background:#ffebee; padding:10px; border-radius:5px;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </p>
            <?php endif; ?>

            <div class="users-table-container" style="margin-top: 20px;">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom du menu</th>
                            <th>Utilisateur</th>
                            <th>Objectif</th>
                            <th>Calories</th>
                            <th>Repas</th>
                            <th>Actif</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($menus)): ?>
                            <tr><td colspan="8" style="text-align:center; padding:20px;">Aucun menu trouvé.</td></tr>
                        <?php else: ?>
                            <?php foreach($menus as $menu): ?>
                                <tr>
                                    <td>#<?= $menu['id'] ?></td>
                                    <td style="font-weight:600;"><?= htmlspecialchars($menu['title']) ?></td>
                                    <td><?= htmlspecialchars($menu['assigned_name'] ?: 'Non affecté') ?></td>
                                    <td><?= htmlspecialchars($menu['goal']) ?></td>
                                    <td><?= intval($menu['calories_target']) ?></td>
                                    <td><?= intval($menu['meal_count']) ?></td>
                                    <td><?= $menu['is_active'] ? 'Oui' : 'Non' ?></td>
                                    <td class="action-buttons">
                                        <a href="index.php?page=admin_edit_menu&id=<?= $menu['id'] ?>" class="btn-icon">✏️</a>
                                        <a href="index.php?page=admin_delete_menu&id=<?= $menu['id'] ?>" class="btn-icon" onclick="return confirm('Voulez-vous vraiment supprimer ce menu ?');">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
