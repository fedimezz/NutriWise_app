<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Mes plannings - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <section style="padding: 2rem 0 1rem 0;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div>
                    <h1 class="hero-title" style="font-size:2.4rem; margin-bottom:0.5rem;">Mes <span class="highlight">plannings</span></h1>
                    <p class="hero-subtitle" style="margin-bottom:0;">Accédez à vos programmes nutritionnels et aux menus qui les composent.</p>
                </div>
            </div>
        </section>

        <?php if(isset($_GET['error'])): ?>
            <p style="color:#b71c1c; background:#ffebee; padding:12px 16px; border-radius:12px; margin-bottom:20px;">
                <?= htmlspecialchars($_GET['error']) ?>
            </p>
        <?php endif; ?>

        <section class="features" style="align-items:stretch;">
            <?php if(empty($plannings)): ?>
                <div class="feature-card" style="max-width:700px; width:100%;">
                    <div class="feature-icon">📅</div>
                    <h3 class="feature-title">Aucun planning disponible</h3>
                    <p class="feature-desc">Votre nutritionniste n'a pas encore créé de planning pour votre compte.</p>
                </div>
            <?php else: ?>
                <?php foreach($plannings as $planning): ?>
                    <div class="feature-card" style="text-align:left; max-width:360px;">
                        <div class="feature-icon">🗓️</div>
                        <h3 class="feature-title"><?= htmlspecialchars($planning['name']) ?></h3>
                        <p class="feature-desc" style="margin-bottom:0.5rem;"><strong>Période :</strong> <?= htmlspecialchars($planning['start_date']) ?> → <?= htmlspecialchars($planning['end_date']) ?></p>
                        <p class="feature-desc" style="margin-bottom:0.5rem;"><strong>Menus :</strong> <?= intval($planning['menu_count']) ?></p>
                        <p class="feature-desc" style="margin-bottom:1.2rem;"><strong>Statut :</strong> <?= htmlspecialchars($planning['status']) ?></p>
                        <a href="index.php?page=nutrition_plan_details&id=<?= $planning['id'] ?>" class="btn-primary" style="display:inline-block; text-decoration:none; padding:0.8rem 1.5rem; font-size:0.95rem;">Voir le planning</a>
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
