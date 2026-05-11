<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Détail du menu - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <section style="padding: 2rem 0 1rem 0;">
            <a href="javascript:history.back()" style="display:inline-block; margin-bottom:1rem; color:#2e7d32; font-weight:600; text-decoration:none;">← Retour</a>
            <h1 class="hero-title" style="font-size:2.4rem; margin-bottom:0.5rem;">Menu <span class="highlight"><?= htmlspecialchars($menu['title']) ?></span></h1>
            <p class="hero-subtitle" style="margin-bottom:0.5rem;"><strong>Objectif :</strong> <?= htmlspecialchars($menu['goal']) ?: 'Aucun objectif défini' ?></p>
            <p class="hero-desc" style="margin-bottom:0.5rem;"><strong>Calories ciblées :</strong> <?= intval($menu['calories_target']) ?></p>
            <p class="hero-desc" style="margin-bottom:1rem;"><strong>Utilisateur affecté :</strong> <?= htmlspecialchars($menu['assigned_name'] ?: 'Non affecté') ?></p>
        </section>

        <section class="features" style="align-items:stretch; justify-content:flex-start; gap:18px;">
            <?php if(empty($menuMeals)): ?>
                <div class="feature-card" style="max-width:700px; width:100%;">
                    <div class="feature-icon">📭</div>
                    <h3 class="feature-title">Aucun repas défini</h3>
                    <p class="feature-desc">Ce menu ne contient encore aucune description de repas.</p>
                </div>
            <?php else: ?>
                <?php
                $days = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'];
                $grouped = [];
                foreach($menuMeals as $meal) {
                    $grouped[intval($meal['day_of_week'])][] = $meal;
                }
                ?>

                <?php foreach($days as $dayIndex => $dayName): ?>
                    <?php if(empty($grouped[$dayIndex])) continue; ?>
                    <div class="feature-card" style="text-align:left; max-width:700px; width:100%;">
                        <div class="feature-icon">📅</div>
                        <h3 class="feature-title"><?= $dayName ?></h3>
                        <?php foreach($grouped[$dayIndex] as $meal): ?>
                            <div style="margin-bottom:12px; padding:12px; background:#f7fdf7; border-radius:10px;">
                                <p style="margin:0 0 6px; font-weight:700; text-transform:capitalize;"><?= htmlspecialchars($meal['meal_type']) ?></p>
                                <p style="margin:0; color:#334d14; white-space:pre-line;"><?= nl2br(htmlspecialchars($meal['description'])) ?></p>
                            </div>
                        <?php endforeach; ?>
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
