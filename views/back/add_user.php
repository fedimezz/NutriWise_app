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
                <a href="index.php?page=admin_dashboard">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="active">👥 Utilisateurs</a>
            </nav>
            <a href="index.php?page=home" class="back-to-site">← Retour au site</a>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Ajouter un utilisateur</h1>
            </header>

            <div class="form-container">
                <?php if(isset($error)): ?><p style="color:red; margin-bottom:15px;"><?= $error ?></p><?php endif; ?>

                <form action="index.php?page=admin_add_user" method="POST">
                    <div class="form-row">
                        <div class="form-group"><label>Prénom *</label><input type="text" name="prenom" required></div>
                        <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
                        <div class="form-group"><label>Téléphone</label><input type="tel" name="telephone"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Mot de passe *</label><input type="password" name="password" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Rôle</label>
                            <select name="role">
                                <option value="user">Utilisateur (User)</option>
                                <option value="admin">Administrateur (Admin)</option>
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