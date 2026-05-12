<?php
// views/front/recette_details.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($recette['nom'] ?? 'Recette') ?> - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/front-global.css">
    <style>
        .detail-container { max-width: 1100px; margin: 2rem auto; padding: 0 1.5rem; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #2e7d32;
            text-decoration: none;
            margin-bottom: 2rem;
            font-weight: 600;
            background: white;
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .back-link:hover { gap: 0.8rem; background: #f0f7ed; }
        .detail-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .detail-hero { position: relative; height: 400px; overflow: hidden; }
        .detail-hero img { width: 100%; height: 100%; object-fit: cover; }
        .detail-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 2rem;
        }
        .detail-title { font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .detail-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 1rem; }
        .meta-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; }
        .difficulte { padding: 0.3rem 0.8rem; border-radius: 20px; background: rgba(255,255,255,0.2); }
        .favorite-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .favorite-btn.active { background: #ff4081; }
        .detail-body { padding: 2rem; }
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
        .description-text { font-size: 1.1rem; line-height: 1.6; color: #5a7a55; margin-bottom: 1rem; }
        .tags { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem; }
        .tag { background: #eef5ec; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; color: #2e7d32; }
        .empty-state { text-align: center; padding: 4rem; background: white; border-radius: 32px; }
        @media (max-width: 768px) {
            .detail-hero { height: 300px; }
            .detail-title { font-size: 1.8rem; }
            .detail-overlay { padding: 1rem; }
            .detail-body { padding: 1rem; }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/partials/navbar.php'; ?>

    <div class="container">
        <div class="detail-container">
            <a href="javascript:history.back()" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour aux recettes
            </a>

            <?php if($recette): 
                $imageUrl = !empty($recette['image']) && filter_var($recette['image'], FILTER_VALIDATE_URL) 
                    ? $recette['image'] 
                    : (!empty($recette['image']) ? 'views/assets/uploads/recettes/' . $recette['image'] : null);
                $fallbackImage = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=500&fit=crop';
            ?>
                <div class="detail-card">
                    <div class="detail-hero">
                        <img src="<?= htmlspecialchars($imageUrl ?: $fallbackImage) ?>" alt="<?= htmlspecialchars($recette['nom']) ?>">
                        <div class="detail-overlay">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                <h1 class="detail-title"><?= htmlspecialchars($recette['nom']) ?></h1>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <a href="?page=recette_details&id=<?= $recette['id'] ?>&favorite=toggle" class="favorite-btn <?= $isFavorite ? 'active' : '' ?>">
                                        <i class="fas fa-heart"></i> <?= $isFavorite ? 'Favori' : 'Ajouter aux favoris' ?>
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
                    <p>La recette que vous recherchez n'existe pas.</p>
                    <a href="index.php?page=recettes" class="btn-primary" style="margin-top:1rem; display:inline-block;">Voir toutes les recettes</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-logo">🌿 NutriWise</div>
        <p>© 2024 NutriWise - Nutrition intelligente et durable</p>
    </footer>
</body>
</html>