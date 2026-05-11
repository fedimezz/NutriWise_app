<?php
// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&error=Vous devez être connecté pour voir les détails");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($aliment['nom'] ?? 'Aliment'); ?> - NutriWise</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        .detail-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #2e7d32;
            text-decoration: none;
            margin-bottom: 2rem;
        }
        .detail-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .detail-header {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .aliment-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .detail-title {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .eco-score-large {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 0.3rem 1rem;
            border-radius: 50px;
            margin-top: 0.5rem;
        }
        .detail-body {
            padding: 2rem;
        }
        .nutrition-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .nutrition-card {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 20px;
        }
        .nutrition-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2e7d32;
        }
        .info-section {
            display: flex;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 20px;
            margin-bottom: 1rem;
        }
        .eco-section {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        }
        .tips-section {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
        }
        .info-icon {
            font-size: 2rem;
        }
        .progress-bar {
            background: #e0e0e0;
            border-radius: 10px;
            height: 10px;
            margin-top: 0.5rem;
        }
        .progress-fill {
            background: #2e7d32;
            border-radius: 10px;
            height: 100%;
            width: 0%;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="detail-container">
            <a href="javascript:history.back()" class="back-link">← Retour</a>

            <?php if($aliment): ?>
                <div class="detail-card">
                    <div class="detail-header">
                        <div class="aliment-icon">
                            <?php
                                $icons = ['Fruits'=>'🍎','Légumes'=>'🥬','Protéines'=>'🍗','Céréales'=>'🌾','Produits laitiers'=>'🥛'];
                                echo $icons[$aliment['category_name']] ?? '🥗';
                            ?>
                        </div>
                        <h1 class="detail-title"><?php echo htmlspecialchars($aliment['nom']); ?></h1>
                        <div class="category-badge" style="background:rgba(255,255,255,0.2); padding:0.3rem 1rem; border-radius:50px; display:inline-block;">
                            <?php echo htmlspecialchars($aliment['category_name'] ?? $aliment['categorie'] ?? 'Aliment'); ?>
                        </div>
                        <div class="eco-score-large">
                            🌱 Score éco: <?php echo $aliment['eco_score'] ?? 0; ?>/10
                        </div>
                    </div>

                    <div class="detail-body">
                        <div class="nutrition-grid">
                            <div class="nutrition-card">
                                <div class="nutrition-value"><?php echo $aliment['calories'] ?? 0; ?></div>
                                <div>Calories (kcal)</div>
                            </div>
                            <div class="nutrition-card">
                                <div class="nutrition-value"><?php echo $aliment['proteins'] ?? 0; ?>g</div>
                                <div>Protéines</div>
                            </div>
                            <div class="nutrition-card">
                                <div class="nutrition-value"><?php echo $aliment['glucides'] ?? 0; ?>g</div>
                                <div>Glucides</div>
                            </div>
                            <div class="nutrition-card">
                                <div class="nutrition-value"><?php echo $aliment['lipids'] ?? 0; ?>g</div>
                                <div>Lipides</div>
                            </div>
                        </div>

                        <div class="info-section eco-section">
                            <div class="info-icon">🌱</div>
                            <div class="info-content">
                                <h3>Impact environnemental</h3>
                                <p>Score éco-score: <strong><?php echo $aliment['eco_score'] ?? 0; ?>/10</strong></p>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo ($aliment['eco_score'] ?? 0) * 10; ?>%"></div>
                                </div>
                                <p style="margin-top: 0.5rem;">
                                    <?php if(($aliment['eco_score'] ?? 0) >= 7): ?>
                                        ✅ Excellent choix durable ! Cet aliment a un faible impact environnemental.
                                    <?php elseif(($aliment['eco_score'] ?? 0) >= 4): ?>
                                        👍 Impact modéré. Privilégiez les versions locales et de saison.
                                    <?php else: ?>
                                        ⚠️ Impact élevé. À consommer avec modération pour l'environnement.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div class="info-section tips-section">
                            <div class="info-icon">💡</div>
                            <div class="info-content">
                                <h3>Conseil NutriWise</h3>
                                <p>
                                    <?php if(($aliment['proteins'] ?? 0) > 15): ?>
                                        💪 Riche en protéines - Idéal pour vos objectifs musculaires !
                                    <?php elseif(($aliment['calories'] ?? 0) < 50): ?>
                                        🥗 Faible en calories - Parfait pour un snack santé !
                                    <?php elseif(($aliment['lipids'] ?? 0) > 10): ?>
                                        🫒 Riche en bonnes graisses - Excellent pour le cœur !
                                    <?php else: ?>
                                        🍽️ Aliment équilibré - Intégrez-le dans vos repas variés !
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state" style="text-align:center; padding:3rem; background:white; border-radius:20px;">
                    <div style="font-size:3rem;">😕</div>
                    <h3>Aliment non trouvé</h3>
                    <a href="index.php?page=aliments" class="btn-primary" style="margin-top:1rem; display:inline-block;">Voir tous les aliments</a>
                </div>
            <?php endif; ?>
        </div>

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