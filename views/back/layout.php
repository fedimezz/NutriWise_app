<?php
// views/back/layout.php - Template de base pour les pages admin
// Variables attendues: $page (pour l'active dans sidebar)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin - NutriWise' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <?php if (isset($extra_css)): ?>
        <style><?= $extra_css ?></style>
    <?php endif; ?>
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>
        <nav>
            <a href="index.php?page=admin_dashboard" class="<?= ($page == 'admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
            <a href="index.php?page=admin_users" class="<?= ($page == 'admin_users') ? 'active' : '' ?>">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments" class="<?= ($page == 'admin_aliments') ? 'active' : '' ?>">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes" class="<?= ($page == 'admin_recettes') ? 'active' : '' ?>">📖 Recettes</a>
            <a href="index.php?page=admin_plannings" class="<?= ($page == 'admin_plannings') ? 'active' : '' ?>">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <?= $content ?>
    </main>

</div>
</body>
</html>