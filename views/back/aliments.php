<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des aliments - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/users.css">
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
                <h1>Base de données des aliments</h1>
                <button class="btn-add" onclick="location.href='index.php?page=admin_add_aliment'">+ Ajouter un aliment</button>
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
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Calories (100g)</th>
                            <th>P / G / L</th>
                            <th>Durable</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($alimentsList)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:20px;">Aucun aliment dans la base de données.</td></tr>
                        <?php else: ?>
                            <?php foreach($alimentsList as $aliment): ?>
                            <tr>
                                <td style="font-weight:600;"><?= htmlspecialchars($aliment['nom']) ?></td>
                                <td><span class="badge active" style="background:#e0f2f1; color:#00695c;"><?= htmlspecialchars($aliment['categorie']) ?></span></td>
                                <td><?= $aliment['calories'] ?> kcal</td>
                                <td style="font-size: 0.9em; color:#666;">
                                    <?= $aliment['proteines'] ?>g / <?= $aliment['glucides'] ?>g / <?= $aliment['lipides'] ?>g
                                </td>
                                <td>
                                    <?= $aliment['durable'] ? '🌍 Oui' : '❌ Non' ?>
                                </td>
                                <td class="action-buttons">
                                    <!-- Bouton MODIFIER (✏️) - AJOUTÉ ici -->
                                    <a href="index.php?page=admin_edit_aliment&id=<?= $aliment['id'] ?>" 
                                       class="btn-edit" 
                                       style="text-decoration:none; display:inline-flex; align-items:center; gap:5px; background:#2196f3; color:white; padding:6px 12px; border-radius:6px; font-size:13px;">
                                        ✏️ Modifier
                                    </a>
                                    <!-- Bouton Supprimer -->
                                    <a href="index.php?page=admin_delete_aliment&id=<?= $aliment['id'] ?>" 
                                       class="btn-delete" 
                                       style="text-decoration:none; display:inline-flex; align-items:center; gap:5px; background:#f44336; color:white; padding:6px 12px; border-radius:6px; font-size:13px; margin-left:8px;" 
                                       onclick="return confirm('Sûr de vouloir supprimer cet aliment ?');">
                                        🗑️ Supprimer
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="assest/js/main.js"></script>
</body>
</html>