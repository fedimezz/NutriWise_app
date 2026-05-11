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
                <a href="index.php?page=admin_dashboard">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="active">👥 Utilisateurs</a>
            </nav>
            <a href="index.php?page=home" class="back-to-site">← Retour au site</a>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Modifier l'utilisateur #<?= $userToEdit['id'] ?></h1>
            </header>

            <div class="form-container">
                <?php if(isset($success)): ?><p style="color:green; font-weight:bold; margin-bottom:15px;"><?= $success ?></p><?php endif; ?>
                <?php if(isset($error)): ?><p style="color:red; margin-bottom:15px;"><?= $error ?></p><?php endif; ?>

                <form action="index.php?page=admin_edit_user&id=<?= $userToEdit['id'] ?>" method="POST">
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
                                <option value="Actif" <?= $userToEdit['statut'] == 'Actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="Inactif" <?= $userToEdit['statut'] == 'Inactif' ? 'selected' : '' ?>>Inactif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Rôle</label>
                            <select name="role">
                                <option value="user" <?= $userToEdit['role'] == 'user' ? 'selected' : '' ?>>Utilisateur</option>
                                <option value="admin" <?= $userToEdit['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
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