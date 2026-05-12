<?php
// views/back/dashboard.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NutriWise Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="views/assets/css/admin-global.css">

</head>
<body>
<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>
        <nav>
            <a href="index.php?page=admin_dashboard" class="active">📊 Dashboard</a>
            <a href="index.php?page=admin_users">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes">📖 Recettes</a>
            <a href="index.php?page=admin_plannings">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1>Tableau de bord</h1>
            <div>
                <a href="index.php?page=home" class="btn-cancel" style="padding:8px 16px;">← Retour accueil</a>
            </div>
        </header>

        <!-- Tabs -->
        <div class="admin-tabs">
            <button class="tab-btn active" data-tab="users">👥 Utilisateurs</button>
            <button class="tab-btn" data-tab="aliments">🥗 Aliments</button>
            <button class="tab-btn" data-tab="recettes">📖 Recettes</button>
            <button class="tab-btn" data-tab="plannings">📋 Plannings</button>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalUsers ?? 0 ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalAliments ?? 0 ?></div>
                <div class="stat-label">Aliments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= (int)($totalRecipes ?? 0) ?></div>
                <div class="stat-label">Recettes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= (int)($totalPlannings ?? 0) ?></div>
                <div class="stat-label">Plannings totaux</div>
            </div>
        </div>

        <!-- Derniers utilisateurs -->
        <div class="section-header">
            <h2>📋 Derniers utilisateurs</h2>
            <a href="index.php?page=admin_users" class="view-link">Voir tout →</a>
        </div>
        
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr><th>Nom</th><th>Email</th><th>IMC</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php
                    $filteredUsers = array_filter($usersList ?? [], fn($u) => !isset($u['role']) || $u['role'] !== 'owner');
                    $recentUsers = array_slice(array_values($filteredUsers), 0, 5);
                    foreach($recentUsers as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['imc'] ? $u['imc'] : 'N/A' ?></td>
                        <td><span class="badge <?= ($u['statut'] ?? 'Actif') == 'Actif' ? 'badge-active' : 'badge-inactive' ?>"><?= $u['statut'] ?? 'Actif' ?></span></td>
                        <td><a href="index.php?page=admin_edit_user&id=<?= $u['id'] ?>" class="btn-edit">✏️</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Dernières activités -->
        <div class="section-header" style="margin-top:30px;">
            <h2>📋 Dernières activités</h2>
            <a href="index.php?page=activity_feed" class="view-link">Voir tout →</a>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr><th>Utilisateur</th><th>Action</th><th>Description</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach($recentActivities ?? [] as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars(($a['prenom'] ?? '') . ' ' . ($a['nom'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($a['action'] ?? '') ?></td>
                        <td><?= htmlspecialchars($a['description'] ?? '') ?></td>
                        <td><?= htmlspecialchars($a['created_at'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
// Tabs functionality (optional)
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        // Add your tab switching logic here if needed
    });
});
</script>
</body>
</html>