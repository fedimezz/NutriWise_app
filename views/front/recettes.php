<?php
// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&error=Vous devez être connecté pour accéder aux recettes");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Recettes - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>
        
        <div class="page-header">
            <h1 class="page-title">Nos Recettes</h1>
            <p class="page-subtitle">Découvrez des recettes délicieuses et équilibrées</p>
        </div>

        <div class="coming-soon">
            <div class="coming-soon-icon">🍳</div>
            <h2>Bientôt disponible</h2>
            <p>Des centaines de recettes adaptées à vos objectifs arrivent très prochainement !</p>
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

    <style>
        .page-header {
            text-align: center;
            padding: 3rem 2rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }
        
        .coming-soon {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 32px;
            margin: 2rem auto;
            max-width: 600px;
        }
        
        .coming-soon-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .coming-soon h2 {
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }
        
        .coming-soon p {
            color: #6c757d;
        }
    </style>
</body>
</html>