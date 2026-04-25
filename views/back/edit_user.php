<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier utilisateur - NutriWise</title>
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
                <h1>Modifier l'utilisateur #<?= $userToEdit['id'] ?></h1>
            </header>

            <div class="form-container">
                <?php if(isset($_SESSION['success'])): ?><p style="color:green; font-weight:bold; margin-bottom:15px;"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?></p><?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?><p style="color:red; margin-bottom:15px;"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?></p><?php endif; ?>

                <form action="index.php?page=admin_edit_user&id=<?= $userToEdit['id'] ?>" method="POST"  novalidate>
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    <div class="form-row">
                        <div class="form-group"><label>Prénom</label><input type="text" name="prenom" value="<?= htmlspecialchars($userToEdit['prenom']) ?>"></div>
                        <div class="form-group"><label>Nom</label><input type="text" name="nom" value="<?= htmlspecialchars($userToEdit['nom']) ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($userToEdit['email']) ?>"></div>
                        <div class="form-group"><label>Téléphone</label><input type="tel" name="telephone" value="<?= htmlspecialchars($userToEdit['telephone'] ?? '') ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Statut</label>
                            <select name="statut">
                                <option value="actif" <?= strtolower($userToEdit['statut'] ?? '') == 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="inactif" <?= strtolower($userToEdit['statut'] ?? '') == 'inactif' ? 'selected' : '' ?>>Inactif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Rôle</label>
                            <select name="role">
                                <option value="user" <?= $userToEdit['role'] == 'user' ? 'selected' : '' ?>>Utilisateur</option>
                                <option value="nutritionist" <?= $userToEdit['role'] == 'nutritionist' ? 'selected' : '' ?>>Nutritionniste</option>
                                <?php if(($_SESSION['user_role'] ?? '') === 'owner'): ?>
                                    <option value="admin" <?= $userToEdit['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
                                    <option value="owner" <?= $userToEdit['role'] == 'owner' ? 'selected' : '' ?>>Owner</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Enregistrer</button>
                        <a href="index.php?page=admin_users" class="btn-cancel" style="text-decoration:none;">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>