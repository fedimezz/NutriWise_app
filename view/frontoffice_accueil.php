<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriWise - Nutrition intelligente et durable</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f8f0; }
        .container { max-width: 1300px; margin: 0 auto; padding: 20px; }
        
        .navbar { background: #1B4D1B; padding: 12px 20px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: nowrap; gap: 10px; width: 100%; }
        .navbar .logo { color: white; font-size: 20px; font-weight: bold; white-space: nowrap; }
        .nav-links { display: flex; gap: 8px; flex-wrap: nowrap; }
        .nav-links a { color: white; text-decoration: none; padding: 6px 12px; border-radius: 8px; transition: background 0.3s; font-weight: normal; font-size: 14px; white-space: nowrap; }
        .nav-links a:hover, .nav-links a.active { background: #4CAF50; }
        .auth-buttons { display: flex; gap: 6px; flex-wrap: nowrap; }
        .btn-login { background: transparent; border: 1px solid white; color: white; padding: 6px 12px; border-radius: 25px; text-decoration: none; font-weight: normal; font-size: 13px; white-space: nowrap; }
        .btn-login:hover { background: white; color: #1B4D1B; }
        .btn-register { background: #4CAF50; color: white; padding: 6px 12px; border-radius: 25px; text-decoration: none; font-weight: normal; font-size: 13px; white-space: nowrap; }
        .btn-register:hover { background: #2E7D32; }
        
        .hero { display: flex; align-items: center; gap: 50px; padding: 50px 0; flex-wrap: wrap; }
        .hero-content { flex: 1; }
        .hero-title { font-size: 48px; color: #1B4D1B; margin-bottom: 20px; }
        .hero-title span { color: #4CAF50; }
        .hero-subtitle { font-size: 20px; color: #666; margin-bottom: 30px; }
        .btn-primary { background: #4CAF50; color: white; padding: 15px 35px; border: none; border-radius: 30px; font-size: 16px; cursor: pointer; transition: background 0.3s; }
        .btn-primary:hover { background: #2E7D32; }
        .hero-image { flex: 1; text-align: center; font-size: 200px; }
        
        .features { display: flex; gap: 30px; margin: 60px 0; flex-wrap: wrap; }
        .feature-card { background: white; border-radius: 20px; padding: 30px; flex: 1; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s; cursor: pointer; }
        .feature-card:hover { transform: translateY(-5px); }
        .feature-icon { font-size: 48px; margin-bottom: 20px; }
        .feature-title { font-size: 20px; color: #1B4D1B; margin-bottom: 15px; font-weight: bold; }
        .feature-desc { color: #666; line-height: 1.6; }
        
        .admin-access { text-align: center; margin: 40px 0; padding: 20px; background: #e8f5e9; border-radius: 12px; }
        .admin-access a { color: #1B4D1B; text-decoration: none; font-weight: bold; margin: 0 10px; }
        .admin-access a:hover { text-decoration: underline; }
        
        .footer { text-align: center; margin-top: 60px; padding: 30px; border-top: 1px solid #ddd; color: #666; }
        
        @media (max-width: 768px) {
            .hero { flex-direction: column; text-align: center; }
            .hero-title { font-size: 32px; }
            .navbar { flex-direction: column; text-align: center; }
            .nav-links, .auth-buttons { flex-wrap: wrap; justify-content: center; }
            .features { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div class="logo">🌿 NutriWise</div>
           <div class="nav-links">
              <a href="../controller/index.php">Accueil</a>
              <a href="../controller/index.php?controller=aliment&action=index&area=front">Aliments</a>
              <a href="../controller/index.php?controller=recette&action=index&area=front">Recettes</a>
              <a href="#">Suivi</a>
            </div>
            <div class="auth-buttons">
                <a href="#" class="btn-login">Connexion</a>
                <a href="#" class="btn-register">Inscription</a>
            </div>
        </nav>

        <section class="hero">
            <div class="hero-content">
                <h1 class="hero-title">Bienvenue sur <span>NutriWise</span></h1>
                <p class="hero-subtitle">Nutrition intelligente et durable</p>
                <button class="btn-primary" onclick="location.href='../controller/index.php?controller=aliment&action=index&area=front'">Commencer</button>
            </div>
            <div class="hero-image">🥗</div>
        </section>

        <section class="features">
            <div class="feature-card" onclick="location.href='../controller/index.php?controller=aliment&action=index&area=front'">
                <div class="feature-icon">🥗</div>
                <h3 class="feature-title">Explorez les aliments sains</h3>
                <p class="feature-desc">Découvrez une base de données complète d'aliments nutritifs et leurs bienfaits.</p>
            </div>
            <div class="feature-card" onclick="location.href='../controller/index.php?controller=recette&action=index&area=front'">
                <div class="feature-icon">📖</div>
                <h3 class="feature-title">Découvrez des recettes adaptées</h3>
                <p class="feature-desc">Des recettes personnalisées selon vos objectifs, vos goûts et la saison.</p>
            </div>
            <div class="feature-card" onclick="location.href='#'">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Suivez votre alimentation</h3>
                <p class="feature-desc">Analysez vos repas, obtenez des conseils et restez motivé au quotidien.</p>
            </div>
        </section>

        <div class="admin-access">
            🔐 Accès administrateur : 
            <a href="../controller/index.php?area=back">📊 Dashboard</a> |
            <a href="../controller/index.php?controller=aliment&action=index&area=back">🥗 Aliments</a> | 
            <a href="../controller/index.php?controller=recette&action=index&area=back">📖 Recettes</a>
        </div>

        <footer class="footer">
            <p>© 2025 NutriWise - Nutrition intelligente et durable</p>
        </footer>
    </div>

    <!-- Chatbot IA NutriBot -->
    <?php include __DIR__ . '/chatbot_widget.php'; ?>
</body>
</html>