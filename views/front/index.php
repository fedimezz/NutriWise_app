<?php
// views/front/index.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>NutriWise | Nutrition intelligente et durable</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/front-global.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            border-radius: 32px;
            padding: 60px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0;
            gap: 2rem;
        }
        .hero-title { font-size: 3rem; margin-bottom: 1rem; }
        .highlight { color: #ffd54f; }
        .hero-subtitle { font-size: 1.2rem; opacity: 0.95; margin-bottom: 2rem; }
        .hero-image { position: relative; }
        .hero-illustration svg { width: 200px; height: 200px; }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin: 4rem 0; }
        .feature-card { text-align: center; padding: 2rem; background: white; border-radius: 28px; transition: all 0.3s; }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .feature-icon { font-size: 3rem; margin-bottom: 1rem; }
        .feature-title { font-size: 1.3rem; color: #2e7d32; margin-bottom: 1rem; }
        .cta-section { background: #e8f5e9; border-radius: 32px; padding: 4rem; text-align: center; margin: 4rem 0; }
        .cta-section h2 { color: #2e7d32; margin-bottom: 1rem; }
        @media (max-width: 768px) {
            .hero { flex-direction: column; text-align: center; padding: 40px; }
            .hero-title { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="hero">
            <div class="hero-content">
                <h1 class="hero-title">Bienvenue sur <span class="highlight">NutriWise</span></h1>
                <p class="hero-subtitle">Nutrition intelligente et durable</p>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=aliments" class="btn-primary">Explorer les aliments</a>
                <?php else: ?>
                    <a href="index.php?page=register" class="btn-primary">Commencer</a>
                <?php endif; ?>
            </div>
            <div class="hero-image">
                <div class="hero-illustration">
                    <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="100" cy="100" r="90" fill="#E8F5E5" stroke="#4CAF50" stroke-width="3"/>
                        <path d="M100 50 L115 85 L155 85 L123 108 L135 145 L100 122 L65 145 L77 108 L45 85 L85 85 Z" fill="#FFD54F" stroke="#F9A825" stroke-width="2"/>
                        <circle cx="70" cy="80" r="5" fill="#4CAF50"/>
                        <circle cx="130" cy="80" r="5" fill="#4CAF50"/>
                        <path d="M85 110 Q100 120 115 110" stroke="#4CAF50" stroke-width="3" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">🥗</div>
                <h3 class="feature-title">Explorez les aliments sains</h3>
                <p>Découvrez une base de données complète d'aliments nutritifs et leurs bienfaits.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📖</div>
                <h3 class="feature-title">Découvrez des recettes adaptées</h3>
                <p>Des recettes personnalisées selon vos objectifs, vos goûts et la saison.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Suivez votre alimentation</h3>
                <p>Analysez vos repas, obtenez des conseils et restez motivé au quotidien.</p>
            </div>
        </div>

        <div class="cta-section">
            <h2>Prêt à transformer votre alimentation ?</h2>
            <p>Rejoignez NutriWise et adoptez une nutrition intelligente et durable dès aujourd'hui.</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=profile" class="btn-primary" style="margin-top: 1.5rem; display: inline-block;">Compléter mon profil</a>
            <?php else: ?>
                <a href="index.php?page=register" class="btn-primary" style="margin-top: 1.5rem; display: inline-block;">Commencer l'aventure</a>
            <?php endif; ?>
        </div>

        <footer class="footer">
            <div class="footer-logo">🌿 NutriWise</div>
            <p>© 2024 NutriWise - Nutrition intelligente et durable</p>
        </footer>
    </div>
</body>
</html>