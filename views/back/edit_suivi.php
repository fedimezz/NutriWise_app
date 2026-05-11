<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer un Suivi - NutriWise Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <style>
        .form-container { background: white; padding: 2rem; border-radius: 16px; max-width: 600px; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; }
        .btn-primary { background: #2e7d32; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; }
        .sidebar .logo { font-size: 1.2rem; font-weight: 700; }
        .sidebar nav a { display: block; margin-bottom: 0.75rem; padding: 0.8rem 1rem; border-radius: 10px; color: #fff; text-decoration: none; }
        .sidebar nav a.active { background: #1b5e20; }
        .sidebar { background: #2e7d32; color: #fff; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo">
                <span>🌿</span><span>NutriWise</span>
            </div>
            <nav>
                <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="<?= (strpos($page, 'admin_user') !== false || strpos($page, 'user') !== false && $page != 'profile') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments" class="<?= (strpos($page, 'aliment') !== false) ? 'active' : '' ?>">🥗 Aliments</a>
                <a href="index.php?page=admin_suivis" class="<?= (strpos($page, 'suivi') !== false) ? 'active' : '' ?>">📈 Suivis</a>
                <a href="#">📖 Recettes</a>
                <a href="#">📅 Plans</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <h1>Éditer le Suivi</h1>
            <p style="color: #666;">Date: <?= date('d/m/Y', strtotime($suivi['date_suivi'])) ?></p>
            
            <div class="form-container" style="margin-top: 2rem;">
                <?php if(isset($success)): ?><p style="color:green; margin-bottom:15px; font-weight:bold;"><?= htmlspecialchars($success) ?></p><?php endif; ?>
                <?php if(isset($error)): ?><p style="color:red; margin-bottom:15px;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

                <form action="index.php?page=admin_edit_suivi&id=<?= $suivi['id'] ?>" method="POST">
                    <div class="form-group">
                        <label>Utilisateur</label>
                        <select name="user_id" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= intval($suivi['user_id']) === intval($u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['prenom'].' '.$u['nom']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date du suivi</label>
                        <input type="date" name="date_suivi" required value="<?= htmlspecialchars($suivi['date_suivi']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Poids (kg)</label>
                        <input type="number" step="0.1" name="poids" required value="<?= htmlspecialchars($suivi['poids']) ?>">
                    </div>

                    <div class="form-group">
                        <label>Calories Nécessaires (kcal/j)</label>
                        <input type="number" name="calories_necessaires" required value="<?= htmlspecialchars($suivi['calories_necessaires']) ?>">
                    </div>

                    <div class="form-group">
                        <label>État (évolution)</label>
                        <select name="etat" required>
                            <option value="Maintien" <?= ($suivi['etat'] == 'Maintien') ? 'selected' : '' ?>>Maintien</option>
                            <option value="S'affine (Perd du poids)" <?= ($suivi['etat'] == "S'affine (Perd du poids)") ? 'selected' : '' ?>>S'affine (Perd du poids)</option>
                            <option value="Grandit (Gagne du poids)" <?= ($suivi['etat'] == "Grandit (Gagne du poids)") ? 'selected' : '' ?>>Grandit (Gagne du poids)</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button type="submit" class="btn-primary">Mettre à jour</button>
                        <a href="index.php?page=admin_suivis" style="padding: 0.8rem 1.5rem; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px;">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
