<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recette['nom'] ?? 'Recette') ?> - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafdf8;
            color: #1a2e1a;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ============ NAVBAR STYLES (identique à page recettes) ============ */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 60px;
            margin: 1rem 0 2rem 0;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2e7d32;
            text-decoration: none;
        }

        .logo-icon {
            font-size: 1.8rem;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: #5a7a55;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 0;
        }

        .nav-link:hover, .nav-link.active {
            color: #2e7d32;
            border-bottom: 2px solid #4caf50;
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: #2e7d32;
            font-weight: 500;
        }

        .nav-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #4caf50;
        }

        .btn-logout {
            background: transparent;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: #dc3545;
            color: white;
        }

        .btn-login, .btn-register {
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-login {
            color: #2e7d32;
            border: 2px solid #2e7d32;
        }

        .btn-login:hover {
            background: #2e7d32;
            color: white;
        }

        .btn-register {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46,125,50,0.3);
        }

        /* ============ STYLES DÉTAIL RECETTE ============ */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #2e7d32;
            text-decoration: none;
            margin: 1rem 0;
            font-weight: 600;
            transition: all 0.3s;
        }

        .back-link:hover {
            gap: 0.8rem;
        }

        .detail-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .detail-hero {
            position: relative;
            height: 400px;
            overflow: hidden;
        }

        .detail-hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .detail-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 2rem;
        }

        .detail-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .detail-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .difficulte {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
        }

        .favorite-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .favorite-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .favorite-btn.active {
            background: #ff4081;
            color: white;
        }

        .detail-body {
            padding: 2rem;
        }

        .section-title {
            font-size: 1.4rem;
            color: #2e7d32;
            margin: 1.5rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e8f5e9;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ingredients-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 0.8rem;
        }

        .ingredients-list li {
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ingredients-list li span:first-child {
            font-weight: 500;
        }

        .ingredients-list li span:last-child {
            font-weight: 600;
            color: #2e7d32;
            background: #e8f5e9;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
        }

        .steps-list {
            list-style: none;
            counter-reset: step;
        }

        .steps-list li {
            counter-increment: step;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 16px;
            display: flex;
            gap: 1rem;
        }

        .steps-list li::before {
            content: counter(step);
            background: #2e7d32;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .description-text {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #5a7a55;
            margin-bottom: 1rem;
        }

        .tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .tag {
            background: #eef5ec;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #2e7d32;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 32px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #c8e6c9;
            margin-bottom: 1rem;
        }

        .footer {
            margin-top: 3rem;
            padding: 2rem 0;
            border-top: 1px solid #e0e8dc;
            text-align: center;
        }

        .footer-logo {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .detail-hero {
                height: 300px;
            }
            .detail-title {
                font-size: 1.8rem;
            }
            .navbar {
                flex-direction: column;
                text-align: center;
            }
            .nav-links {
                justify-content: center;
            }
            .auth-buttons {
                justify-content: center;
            }
            .detail-overlay {
                padding: 1rem;
            }
            .detail-body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- NAVBAR IDENTIQUE À LA PAGE RECETTES -->
        <?php 
        if (file_exists(__DIR__ . '/partials/navbar.php')) {
            include_once __DIR__ . '/partials/navbar.php';
        } elseif (file_exists(__DIR__ . '/../partials/navbar.php')) {
            include_once __DIR__ . '/../partials/navbar.php';
        } else {
            include_once 'partials/navbar.php';
        }
        ?>

        <a href="javascript:history.back()" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <?php if($recette): ?>
            <div class="detail-card">
                <div class="detail-hero">
                    <?php 
                    $imageUrl = !empty($recette['image']) && filter_var($recette['image'], FILTER_VALIDATE_URL) 
                        ? $recette['image'] 
                        : (!empty($recette['image']) ? 'views/assets/uploads/recettes/' . $recette['image'] : null);
                    $fallbackImage = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=500&fit=crop';
                    ?>
                    <img src="<?= htmlspecialchars($imageUrl ?: $fallbackImage) ?>" 
                         alt="<?= htmlspecialchars($recette['nom']) ?>" 
                         onerror="this.src='<?= $fallbackImage ?>'">
                    <div class="detail-overlay">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <h1 class="detail-title"><?= htmlspecialchars($recette['nom']) ?></h1>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="?page=recette_details&id=<?= $recette['id'] ?>&favorite=toggle" 
                                   class="favorite-btn <?= $isFavorite ? 'active' : '' ?>">
                                    <i class="fas <?= $isFavorite ? 'fa-heart' : 'fa-heart' ?>"></i> 
                                    <?= $isFavorite ? 'Favori ❤️' : 'Ajouter aux favoris' ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="detail-meta">
                            <span class="meta-item"><i class="far fa-clock"></i> Préparation: <?= $recette['temps_preparation'] ?? 0 ?> min</span>
                            <span class="meta-item"><i class="fas fa-fire"></i> Cuisson: <?= $recette['temps_cuisson'] ?? 0 ?> min</span>
                            <span class="meta-item"><i class="fas fa-utensils"></i> <?= $recette['portions'] ?? 4 ?> portions</span>
                            <span class="meta-item difficulte">
                                <?php 
                                $difficultyIcon = match($recette['difficulte'] ?? 'Moyen') {
                                    'Facile' => '😊',
                                    'Moyen' => '👍',
                                    'Difficile' => '🔥',
                                    default => '📖'
                                };
                                echo $difficultyIcon . ' ' . htmlspecialchars($recette['difficulte'] ?? 'Moyen');
                                ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="detail-body">
                    <?php if(!empty($recette['description'])): ?>
                        <p class="description-text"><?= nl2br(htmlspecialchars($recette['description'])) ?></p>
                    <?php endif; ?>

                    <h2 class="section-title"><i class="fas fa-shopping-basket"></i> Ingrédients</h2>
                    <ul class="ingredients-list">
                        <?php foreach($ingredients as $ingredient): ?>
                            <li>
                                <span><?= htmlspecialchars($ingredient['nom']) ?></span>
                                <span><?= $ingredient['quantite'] ?> <?= htmlspecialchars($ingredient['unite']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <h2 class="section-title"><i class="fas fa-list-ol"></i> Préparation</h2>
                    <ul class="steps-list">
                        <?php foreach($etapes as $etape): ?>
                            <li><?= nl2br(htmlspecialchars($etape['description'])) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if(!empty($recette['tags'])): ?>
                        <div class="tags">
                            <?php foreach(explode(',', $recette['tags']) as $tag): ?>
                                <span class="tag">#<?= trim(htmlspecialchars($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>Recette non trouvée</h3>
                <p>La recette que vous recherchez n'existe pas ou a été supprimée.</p>
                <a href="index.php?page=recettes" style="display:inline-block; margin-top:1rem; background:#2e7d32; color:white; padding:0.8rem 1.8rem; border-radius:40px; text-decoration:none;">
                    Voir toutes les recettes
                </a>
            </div>
        <?php endif; ?>

        <footer class="footer">
            <div class="footer-logo">🌿 NutriWise</div>
            <p>© 2024 NutriWise - Nutrition intelligente et durable</p>
        </footer>
    </div>
</body>
</html>