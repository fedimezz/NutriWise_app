<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Détail du planning - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <section style="padding: 2rem 0 1rem 0;">
            <a href="index.php?page=nutrition_plans" style="display:inline-block; margin-bottom:1rem; color:#2e7d32; font-weight:600; text-decoration:none;">← Retour aux plannings</a>
            <h1 class="hero-title" style="font-size:2.4rem; margin-bottom:0.5rem;">Planning <span class="highlight"><?= htmlspecialchars($planning['name']) ?></span></h1>
            <p class="hero-subtitle" style="margin-bottom:0.5rem;"><strong>Période :</strong> <?= htmlspecialchars($planning['start_date']) ?> → <?= htmlspecialchars($planning['end_date']) ?></p>
            <p class="hero-desc" style="margin-bottom:0.5rem;"><strong>Statut :</strong> <?= htmlspecialchars($planning['status']) ?></p>
            <p class="hero-desc" style="margin-bottom:0.5rem;"><strong>Utilisateur :</strong> <?= htmlspecialchars($planning['user_name']) ?></p>
            <p class="hero-desc" style="margin-bottom:1.5rem;"><strong>Nutritionniste :</strong> <?= htmlspecialchars($planning['nutritionist_name'] ?: 'Non spécifié') ?></p>
            <?php if($planning['description']): ?>
                <div style="margin-bottom:1.5rem; padding:1rem; background:#f1f8e9; border-radius:12px; color:#2e7d32;">
                    <?= nl2br(htmlspecialchars($planning['description'])) ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="features" style="align-items:stretch; justify-content:flex-start; gap:18px;">
            <?php if(empty($planningMenus)): ?>
                <div class="feature-card" style="max-width:700px; width:100%;">
                    <div class="feature-icon">📭</div>
                    <h3 class="feature-title">Aucun menu associé</h3>
                    <p class="feature-desc">Ce planning ne contient pas encore de menus.</p>
                </div>
            <?php else: ?>
                <?php foreach($planningMenus as $menu): ?>
                    <div class="feature-card" style="text-align:left; max-width:360px;">
                        <div class="feature-icon">📘</div>
                        <h3 class="feature-title"><?= htmlspecialchars($menu['title']) ?></h3>
                        <p class="feature-desc" style="margin-bottom:0.5rem;"><strong>Objectif :</strong> <?= htmlspecialchars($menu['goal']) ?></p>
                        <p class="feature-desc" style="margin-bottom:0.5rem;"><strong>Calories ciblées :</strong> <?= intval($menu['calories_target']) ?></p>
                        <p class="feature-desc" style="margin-bottom:1rem;"><strong>Actif :</strong> <?= $menu['is_active'] ? 'Oui' : 'Non' ?></p>
                        <a href="index.php?page=menu_details&id=<?= $menu['id'] ?>" class="btn-primary" style="display:inline-block; text-decoration:none; padding:0.6rem 1.2rem; font-size:0.9rem;">Voir le menu</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-logo">
                    <span class="logo-icon">🌿</span>
                    <span>NutriWise</span>
                </div>
                <p class="footer-copyright">© 2024 NutriWise - Nutrition intelligente et durable</p>
            </div>
        </footer>
    </div>
</body>
</html>
