<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NutriWise Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
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
                <a href="index.php?page=admin_dashboard" class="active">📊 Dashboard</a>
                <a href="index.php?page=admin_users">👥 Utilisateurs</a>
                <a href="index.php?page=admin_suivis">📈 Suivis</a>
                <a href="index.php?page=admin_consultations">🩺 Consultations</a>
                <a href="index.php?page=admin_aliments">🥗 Aliments</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Tableau de bord</h1>
                <div class="admin-badge">Admin</div>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $totalUsers ?></div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $totalAliments ?></div>
                    <div class="stat-label">Aliments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $totalConsultations ?></div>
                    <div class="stat-label">Consultations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($usersList) ?></div>
                    <div class="stat-label">Comptes suivis</div>
                </div>
            </div>

            <div class="recent-users">
                <div class="section-header">
                    <h2>Derniers utilisateurs</h2>
                    <a href="index.php?page=admin_users" class="view-link">Voir tout</a>
                </div>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>IMC</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($usersList, 0, 5) as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= $u['imc'] ? htmlspecialchars($u['imc']) : 'N/A' ?></td>
                                <td><?= htmlspecialchars($u['statut']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
