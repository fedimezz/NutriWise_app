<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Consultation - NutriWise Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <style>
        .form-container { background: #fff; padding: 2rem; border-radius: 16px; max-width: 760px; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.85rem;
            border: 1px solid #d7dfd5;
            border-radius: 10px;
            font-family: inherit;
        }
        .form-group textarea { min-height: 110px; resize: vertical; }
        .btn-primary { background: #2e7d32; color: white; padding: 0.8rem 1.4rem; border: none; border-radius: 8px; cursor: pointer; }
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
            <h1>Modifier une consultation</h1>

            <?php if (isset($error)): ?>
                <div style="padding:1rem; background:#fdecea; color:#b42318; border-radius:10px; margin:1rem 0;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-container" style="margin-top:2rem;">
                <form action="index.php?page=admin_edit_consultation&id=<?= $consultation['id'] ?>" method="POST">
                    <div class="form-group">
                        <label for="user_id">Utilisateur</label>
                        <select name="user_id" id="user_id" required>
                            <option value="">Selectionner un utilisateur</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= intval($consultation['user_id']) === intval($user['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?> (<?= htmlspecialchars($user['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="suivi_id">Suivi associe</label>
                        <select name="suivi_id" id="suivi_id" required>
                            <option value="">Selectionner un suivi</option>
                            <?php foreach ($suivis as $suivi): ?>
                                <option value="<?= $suivi['id'] ?>" <?= intval($consultation['suivi_id']) === intval($suivi['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($suivi['prenom'] . ' ' . $suivi['nom']) ?> - <?= date('d/m/Y', strtotime($suivi['date_suivi'])) ?> - <?= htmlspecialchars($suivi['poids']) ?> kg
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_consultation">Date consultation</label>
                        <input type="date" name="date_consultation" id="date_consultation" value="<?= htmlspecialchars($consultation['date_consultation']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="poids_cible">Poids cible (kg)</label>
                        <input type="number" step="0.1" name="poids_cible" id="poids_cible" value="<?= htmlspecialchars((string) $consultation['poids_cible']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="remarque">Remarque</label>
                        <textarea name="remarque" id="remarque" required><?= htmlspecialchars($consultation['remarque']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="conseil">Conseil</label>
                        <textarea name="conseil" id="conseil" required><?= htmlspecialchars($consultation['conseil']) ?></textarea>
                    </div>

                    <div style="display:flex; gap:1rem;">
                        <button type="submit" class="btn-primary">Mettre a jour</button>
                        <a href="index.php?page=admin_consultations" style="padding:0.8rem 1.5rem; text-decoration:none; color:#666; border:1px solid #ddd; border-radius:8px;">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
