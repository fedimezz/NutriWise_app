<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/users.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">🌿 NutriWise</div>
            <nav>
                <a href="index.php?page=admin_dashboard">📊 Tableau de bord</a>
                <a href="index.php?page=admin_users" class="active">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments" class="<?= ($page=='admin_aliments') ? 'active' : '' ?>">🥗 Aliments</a>
                <a href="#">📖 Recettes</a>
                <a href="#">📅 Plans alimentaires</a>
            </nav>
            <a href="index.php?page=home" class="back-to-site">← Retour au site</a>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Gestion des utilisateurs</h1>
                <button class="btn-add" onclick="location.href='index.php?page=admin_add_user'">+ Ajouter un utilisateur</button>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <p style="color:green; margin-top:10px; font-weight:bold;"><?= htmlspecialchars($_GET['success']) ?></p>
            <?php endif; ?>

            <div class="filters">
                <input type="text" placeholder="Rechercher..." class="search-input">
                <select class="filter-select">
                    <option>Tous les statuts</option><option>Actif</option><option>Inactif</option>
                </select>
            </div>

            <div class="users-table-container">
                <table class="users-table">
                    <thead>
                        <tr><th>ID</th><th>Nom complet</th><th>Email</th><th>Téléphone</th><th>IMC</th><th>Statut</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($usersList as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>
                            <td><?= $user['imc'] ? $user['imc'] : 'N/A' ?></td>
                            <td><span class="badge <?= $user['statut'] == 'Actif' ? 'active' : 'inactive' ?>"><?= $user['statut'] ?></span></td>
                            <td>
                                <a href="index.php?page=admin_edit_user&id=<?= $user['id'] ?>" class="btn-edit" style="text-decoration:none;">✏️</a>
                                <a href="index.php?page=admin_delete_user&id=<?= $user['id'] ?>" class="btn-delete" style="text-decoration:none; margin-left:10px;" onclick="return confirm('Sûr de vouloir supprimer ?');">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>