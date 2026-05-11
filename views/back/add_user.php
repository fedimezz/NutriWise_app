<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/edit_user.css">
    <style>
        .sidebar .logo { font-size: 1.2rem; font-weight: 700; }
        .sidebar nav a { display: block; margin-bottom: 0.75rem; padding: 0.8rem 1rem; border-radius: 10px; color: #fff; text-decoration: none; }
        .sidebar nav a.active { background: #1b5e20; }
        .sidebar { background: #2e7d32; color: #fff; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
                <div class="logo">NutriWise Admin</div>
                <nav>
                    <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                    <a href="index.php?page=admin_users" class="<?= (strpos($page, 'admin_user') !== false || strpos($page, 'user') !== false && $page != 'profile') ? 'active' : '' ?>">👥 Utilisateurs</a>
                    <a href="index.php?page=admin_suivis" class="<?= (strpos($page, 'suivi') !== false) ? 'active' : '' ?>">📈 Suivis</a>
                    <a href="index.php?page=admin_consultations" class="<?= (strpos($page, 'consultation') !== false) ? 'active' : '' ?>">🩺 Consultations</a>
                    <a href="index.php?page=admin_aliments" class="<?= (strpos($page, 'aliment') !== false) ? 'active' : '' ?>">🥗 Aliments</a>
                </nav>
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