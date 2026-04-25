<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NutriWise Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation Commune -->
        <aside class="sidebar">
            <div class="logo">
                <span>🌿</span><span>NutriWise</span>
            </div>
            <nav>
                <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="<?= ($page=='admin_users') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments" class="<?= ($page=='admin_aliments') ? 'active' : '' ?>">🥗 Aliments</a>
                <a href="index.php?page=admin_recettes" class="<?= ($page=='admin_recettes') ? 'active' : '' ?>">📖 Recettes</a>
                <a href="index.php?page=admin_plans" class="<?= ($page=='admin_plans') ? 'active' : '' ?>">📅 Plans</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Tableau de bord</h1>
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <a href="index.php?page=home" class="view-link" style="text-decoration:none;">← Retour à l’accueil</a>
                    <div class="admin-badge"><?= htmlspecialchars(ucfirst($_SESSION['user_role'] ?? 'admin'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
                </div>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <!-- Vraie donnée de la BDD -->
                    <div class="stat-number"><?= $totalUsers ?></div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
                <!-- Vous pourrez dynamiser ces 3 là quand vous ferez les tables Aliments/Recettes -->
                <div class="stat-card"><div class="stat-icon">🥗</div><div class="stat-number"><?= $totalAliments ?></div><div class="stat-label">Aliments</div></div>
                <a class="stat-card" href="index.php?page=admin_recettes" style="text-decoration:none; color:inherit;">
                    <div class="stat-icon">📖</div>
                    <div class="stat-number"><?= (int)($totalRecipes ?? 0) ?></div>
                    <div class="stat-label">Recettes</div>
                </a>
                <a class="stat-card" href="index.php?page=admin_plans" style="text-decoration:none; color:inherit;">
                    <div class="stat-icon">📅</div>
                    <div class="stat-number"><?= (int)($activePlans ?? 0) ?></div>
                    <div class="stat-label">Plans actifs</div>
                </a>
            </div>

          

            <div class="recent-users">
                <div class="section-header">
                    <h2>Derniers utilisateurs</h2>
                    <a href="index.php?page=admin_users" class="view-link">Voir tout →</a>
                </div>
                <table class="users-table">
                    <thead>
                        <tr><th>Nom</th><th>Email</th><th>IMC</th><th>Statut</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <!-- Boucle PHP pour les 5 derniers inscrits -->
                        <?php
                       $filteredUsers = array_filter($usersList, function ($u) {
    return !isset($u['role']) || $u['role'] !== 'owner';
});

$recentUsers = array_slice(array_values($filteredUsers), 0, 5); // Prend seulement les 5 premiers
                        foreach($recentUsers as $u):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= $u['imc'] ? $u['imc'] : 'N/A' ?></td>
                            <td><span class="badge <?= $u['statut'] == 'Actif' ? 'active' : 'inactive' ?>"><?= $u['statut'] ?></span></td>
                            <td><a href="index.php?page=admin_edit_user&id=<?= $u['id'] ?>" class="btn-icon">✏️</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>