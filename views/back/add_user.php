<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/edit_user.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">🌿 NutriWise</div>
            <nav>
                <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="<?= ($page=='admin_users' || $page=='admin_add_user' || $page=='admin_edit_user') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments" class="<?= ($page=='admin_aliments' || $page=='admin_add_aliment' || $page=='admin_edit_aliment') ? 'active' : '' ?>">🥗 Aliments</a>
                <a href="index.php?page=admin_recettes" class="<?= ($page=='admin_recettes') ? 'active' : '' ?>">📖 Recettes</a>
                <a href="index.php?page=admin_plans" class="<?= ($page=='admin_plans') ? 'active' : '' ?>">📅 Plans</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Ajouter un utilisateur</h1>
            </header>

            <div class="form-container">
                <?php if(isset($_SESSION['error'])): ?><p style="color:red; margin-bottom:15px;"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?></p><?php endif; ?>

                <form action="index.php?page=admin_add_user" method="POST"  novalidate>
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    <div class="form-row">
                        <div class="form-group"><label>Prénom *</label><input type="text" name="prenom" required></div>
                        <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email (optionnel)</label>
                            <input type="email" name="email" placeholder="Laisser vide pour générer automatiquement">
                        </div>
                        <div class="form-group"><label>Téléphone</label><input type="tel" name="telephone"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Mot de passe (optionnel)</label>
                            <input type="text" name="password" placeholder="Laisser vide pour générer un mot de passe (8 caractères)">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Rôle</label>
                            <select name="role">
                                <option value="user">Utilisateur (User)</option>
                                <option value="nutritionist">Nutritionniste</option>
                                <?php if(($_SESSION['user_role'] ?? '') === 'owner'): ?>
                                    <option value="admin">Administrateur (Admin)</option>
                                    <option value="owner">Owner (Full access)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Créer l'utilisateur</button>
                        <a href="index.php?page=admin_users" class="btn-cancel" style="text-decoration:none;">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>