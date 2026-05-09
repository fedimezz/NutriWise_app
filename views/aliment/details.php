<?php // Access control is handled in controller/router (PHP), not in the view. ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($aliment['nom'] ?? 'Aliment'); ?> - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Vos styles CSS existants (gardez-les identiques) */
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

        .detail-container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #2e7d32;
            text-decoration: none;
            margin-bottom: 2rem;
            font-weight: 600;
            transition: all 0.2s;
            background: white;
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .back-link:hover {
            gap: 0.8rem;
            background: #f0f7ed;
        }

        .detail-card {
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
            border: 1px solid rgba(46,125,50,0.15);
        }

        .detail-hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            min-height: 420px;
            background: linear-gradient(135deg, #f5fbf2, #eef5ea);
        }

        .hero-image {
            position: relative;
            background: linear-gradient(135deg, #e2f0de, #cde5c9);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            min-height: 380px;
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .hero-image:hover img {
            transform: scale(1.02);
        }

        .hero-copy {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.2rem;
        }

        .category-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(46,125,50,0.12);
            padding: 0.5rem 1rem;
            border-radius: 40px;
            width: fit-content;
            font-weight: 600;
            font-size: 0.85rem;
            color: #2e7d32;
        }

        .detail-title {
            font-size: 2.8rem;
            margin-bottom: 0.25rem;
            color: #1a3a1a;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .subheadline {
            color: #5a7a55;
            line-height: 1.6;
            font-size: 1rem;
        }

        .detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.2rem;
            border-radius: 999px;
            background: rgba(76,175,80,0.12);
            color: #2e7d32;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .detail-body {
            padding: 2.5rem;
        }

        .nutrition-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2.5rem;
        }

        .nutrition-card {
            text-align: center;
            padding: 1.6rem;
            background: linear-gradient(135deg, #f9fff7, #f1f8ef);
            border-radius: 24px;
            border: 1px solid rgba(76,175,80,0.15);
            transition: all 0.2s;
        }

        .nutrition-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .nutrition-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #2e7d32;
        }

        .nutrition-label {
            margin-top: 0.6rem;
            color: #6b8a66;
            font-weight: 500;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .split-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            padding: 1.8rem;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid rgba(76,175,80,0.12);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .info-card h3 {
            margin-bottom: 1rem;
            color: #2d5930;
            font-size: 1.3rem;
        }

        .progress-container {
            background: #e8f0e5;
            border-radius: 20px;
            height: 10px;
            margin: 1rem 0;
            overflow: hidden;
        }

        .progress-fill {
            background: linear-gradient(90deg, #4caf50, #2e7d32);
            height: 100%;
            border-radius: 20px;
            transition: width 0.6s ease;
        }

        .eco-level {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #7c9a76;
        }

        .detail-badges {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }

        .badge-pill {
            padding: 0.6rem 1.1rem;
            border-radius: 999px;
            background: #eef5ec;
            color: #2e7d32;
            font-weight: 700;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .related-section {
            margin-top: 2rem;
        }

        .related-section h2 {
            font-size: 1.6rem;
            margin-bottom: 1.2rem;
            color: #1a3a1a;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.2rem;
        }

        .related-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(76,175,80,0.12);
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.1);
        }

        .related-thumb {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: #eef5ec;
        }

        .related-content {
            padding: 1rem;
        }

        .related-content h4 {
            font-size: 1rem;
            margin-bottom: 0.3rem;
            color: #1e3a1e;
        }

        .empty-state {
            text-align: center;
            padding: 4rem;
            background: white;
            border-radius: 32px;
        }

        .footer {
            margin-top: 4rem;
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .detail-card {
            animation: fadeIn 0.4s ease;
        }

        @media (max-width: 860px) {
            .detail-hero {
                grid-template-columns: 1fr;
            }
            .split-grid {
                grid-template-columns: 1fr;
            }
            .detail-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'views/front/partials/navbar.php'; ?>

        <div class="detail-container">
            <a href="javascript:history.back()" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour aux aliments
            </a>

            <?php if($aliment): ?>
                <?php
                /**
                 * FONCTIONS DE GESTION D'IMAGES (déclarées une seule fois)
                 */
                
                /**
                 * Obtenir l'URL complète de l'image pour l'aliment principal
                 * 
                 * @param string|null $image Nom du fichier ou URL
                 * @param string $alimentName Nom de l'aliment pour les fallbacks
                 * @return string URL complète de l'image
                 */
                function getAlimentImageUrl($image, $alimentName = '') {
                    // Chemin de base pour les images locales
                    $baseLocalPath = 'views/uploads/aliments/';
                    
                    // Fallback par défaut
                    $fallbackImage = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&h=400&fit=crop';
                    
                    // Fallbacks spécifiques par nom d'aliment
                    $fallbacksByName = [
                        'avocat' => 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?w=600&h=400&fit=crop',
                        'banane' => 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=600&h=400&fit=crop',
                        'amande' => 'https://images.unsplash.com/photo-1525706616307-9301b5c3ad4f?w=600&h=400&fit=crop',
                        'pomme' => 'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?w=600&h=400&fit=crop',
                        'brocoli' => 'https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?w=600&h=400&fit=crop',
                        'poulet' => 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=600&h=400&fit=crop',
                        'saumon' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=600&h=400&fit=crop',
                        'oeuf' => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=600&h=400&fit=crop',
                        'carotte' => 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=600&h=400&fit=crop'
                    ];
                    
                    // Si aucune image n'est fournie
                    if (empty($image)) {
                        $lowerName = strtolower($alimentName);
                        foreach ($fallbacksByName as $keyword => $url) {
                            if (strpos($lowerName, $keyword) !== false) {
                                return $url;
                            }
                        }
                        return $fallbackImage;
                    }
                    
                    // Détection si c'est une URL externe
                    if (filter_var($image, FILTER_VALIDATE_URL) !== false) {
                        return htmlspecialchars($image);
                    }
                    
                    // Sinon, c'est une image locale
                    $cleanImage = preg_replace('/[^a-zA-Z0-9._-]/', '', $image);
                    return $baseLocalPath . htmlspecialchars($cleanImage);
                }
                
                /**
                 * Obtenir l'URL complète de l'image pour les aliments similaires
                 * 
                 * @param string|null $image Nom du fichier ou URL
                 * @param string $alimentName Nom de l'aliment pour les fallbacks
                 * @return string URL complète de l'image
                 */
                function getRelatedImageUrl($image, $alimentName = '') {
                    $baseLocalPath = 'views/uploads/aliments/';
                    $fallbackImage = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=280&fit=crop';
                    
                    $fallbacksByName = [
                        'avocat' => 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?w=400&h=280&fit=crop',
                        'banane' => 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=400&h=280&fit=crop',
                        'amande' => 'https://images.unsplash.com/photo-1525706616307-9301b5c3ad4f?w=400&h=280&fit=crop',
                        'pomme' => 'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?w=400&h=280&fit=crop',
                        'brocoli' => 'https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?w=400&h=280&fit=crop'
                    ];
                    
                    if (empty($image)) {
                        $lowerName = strtolower($alimentName);
                        foreach ($fallbacksByName as $keyword => $url) {
                            if (strpos($lowerName, $keyword) !== false) {
                                return $url;
                            }
                        }
                        return $fallbackImage;
                    }
                    
                    if (filter_var($image, FILTER_VALIDATE_URL) !== false) {
                        return htmlspecialchars($image);
                    }
                    
                    $cleanImage = preg_replace('/[^a-zA-Z0-9._-]/', '', $image);
                    return $baseLocalPath . htmlspecialchars($cleanImage);
                }
                
                // Image principale
                $imageUrl = getAlimentImageUrl($aliment['image'] ?? null, $aliment['nom'] ?? '');
                
                $ecoScore = (float)($aliment['eco_score'] ?? 0);
                $scorePercent = min(100, max(0, $ecoScore * 10));
                $scoreColor = $ecoScore >= 8 ? '#2e7d32' : ($ecoScore >= 6 ? '#f39c12' : '#e74c3c');
                ?>
                
                <div class="detail-card">
                    <div class="detail-hero">
                        <div class="hero-image">
                            <img 
                                src="<?= $imageUrl ?>" 
                                alt="<?= htmlspecialchars($aliment['nom']) ?>" 
                                loading="lazy"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&h=400&fit=crop'"
                            >
                        </div>
                        <div class="hero-copy">
                            <div class="category-pill">
                                <i class="fas fa-tag"></i> <?= htmlspecialchars($aliment['category_name'] ?? 'Aliment') ?>
                            </div>
                            <h1 class="detail-title"><?= htmlspecialchars($aliment['nom']) ?></h1>
                            <p class="subheadline">
                                Découvrez les valeurs nutritionnelles et l'impact environnemental de cet aliment.
                            </p>
                            <div class="detail-meta">
                                <div class="pill" style="background: <?= $scoreColor ?>20; color: <?= $scoreColor ?>;">
                                    <i class="fas fa-leaf"></i> Éco-score: <?= number_format($ecoScore, 1) ?>/10
                                </div>
                                <div class="pill">
                                    <?= ($aliment['durable'] ?? 0) ? '<i class="fas fa-seedling"></i> Aliment durable' : '<i class="fas fa-chart-line"></i> Consommation modérée' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-body">
                        <div class="nutrition-grid">
                            <div class="nutrition-card">
                                <div class="nutrition-value"><?= number_format($aliment['calories'] ?? 0, 0) ?></div>
                                <div class="nutrition-label"><i class="fas fa-fire"></i> Calories (kcal)</div>
                            </div>
                            <div class="nutrition-card">
                                <div class="nutrition-value"><?= number_format($aliment['proteines'] ?? 0, 1) ?>g</div>
                                <div class="nutrition-label"><i class="fas fa-dumbbell"></i> Protéines</div>
                            </div>
                            <div class="nutrition-card">
                                <div class="nutrition-value"><?= number_format($aliment['glucides'] ?? 0, 1) ?>g</div>
                                <div class="nutrition-label"><i class="fas fa-bread-slice"></i> Glucides</div>
                            </div>
                            <div class="nutrition-card">
                                <div class="nutrition-value"><?= number_format($aliment['lipides'] ?? 0, 1) ?>g</div>
                                <div class="nutrition-label"><i class="fas fa-oil-can"></i> Lipides</div>
                            </div>
                        </div>

                        <div class="split-grid">
                            <div class="info-card">
                                <h3><i class="fas fa-globe-europe"></i> Impact environnemental</h3>
                                <p>L'éco-score mesure l'empreinte carbone et l'utilisation des ressources.</p>
                                <div class="progress-container">
                                    <div class="progress-fill" style="width: <?= $scorePercent ?>%;"></div>
                                </div>
                                <div class="eco-level">
                                    <span><i class="fas fa-circle" style="color:#e74c3c; font-size:0.7rem;"></i> Fort impact</span>
                                    <span><i class="fas fa-circle" style="color:#f39c12; font-size:0.7rem;"></i> Moyen</span>
                                    <span><i class="fas fa-circle" style="color:#2e7d32; font-size:0.7rem;"></i> Faible impact</span>
                                </div>
                                <?php if($ecoScore >= 7): ?>
                                    <p style="margin-top: 1rem; color: #2e7d32; font-weight: 600;">
                                        <i class="fas fa-check-circle"></i> Excellent choix pour l'environnement !
                                    </p>
                                <?php elseif($ecoScore >= 4): ?>
                                    <p style="margin-top: 1rem; color: #f39c12;">
                                        <i class="fas fa-exclamation-triangle"></i> Impact modéré, à consommer avec conscience.
                                    </p>
                                <?php else: ?>
                                    <p style="margin-top: 1rem; color: #e74c3c;">
                                        <i class="fas fa-exclamation-circle"></i> Impact élevé, privilégiez des alternatives durables.
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="info-card">
                                <h3><i class="fas fa-lightbulb"></i> Conseils NutriWise</h3>
                                <div class="detail-badges">
                                    <?php if(($aliment['proteines'] ?? 0) > 15): ?>
                                        <span class="badge-pill"><i class="fas fa-dumbbell"></i> Riche en protéines</span>
                                    <?php endif; ?>
                                    <?php if(($aliment['calories'] ?? 0) < 80): ?>
                                        <span class="badge-pill"><i class="fas fa-leaf"></i> Faible en calories</span>
                                    <?php endif; ?>
                                    <?php if(($aliment['lipides'] ?? 0) > 10 && strpos(strtolower($aliment['nom']), 'avocat') !== false): ?>
                                        <span class="badge-pill"><i class="fas fa-heart"></i> Riche en bons gras</span>
                                    <?php endif; ?>
                                    <?php if(($aliment['durable'] ?? 0) == 1): ?>
                                        <span class="badge-pill"><i class="fas fa-seedling"></i> Label durable</span>
                                    <?php endif; ?>
                                </div>
                                <p style="margin-top: 1rem; line-height: 1.5; color: #4a6741;">
                                    <?php
                                        if(($aliment['proteines'] ?? 0) > 15) {
                                            echo "Parfait pour la construction musculaire et la satiété. Idéal après le sport !";
                                        } elseif(($aliment['calories'] ?? 0) < 80) {
                                            echo "Idéal pour une collation légère ou un accompagnement. Peut être consommé sans modération.";
                                        } elseif(($aliment['lipides'] ?? 0) > 10 && strpos(strtolower($aliment['nom']), 'avocat') !== false) {
                                            echo "Riche en acides gras mono-insaturés (oméga-9), excellent pour le système cardiovasculaire.";
                                        } else {
                                            echo "À intégrer dans une alimentation variée et équilibrée pour profiter de tous ses bienfaits.";
                                        }
                                    ?>
                                </p>
                            </div>
                        </div>

                        <?php if(!empty($relatedAliments)): ?>
                            <div class="related-section">
                                <h2><i class="fas fa-heart"></i> Aliments similaires</h2>
                                <div class="related-grid">
                                    <?php foreach($relatedAliments as $related): ?>
                                        <?php
                                            $relImg = getRelatedImageUrl($related['image'] ?? null, $related['nom'] ?? '');
                                        ?>
                                        <div class="related-card" onclick="window.location='index.php?page=aliment_details&id=<?= (int)$related['id'] ?>';">
                                            <img 
                                                class="related-thumb" 
                                                src="<?= $relImg ?>" 
                                                alt="<?= htmlspecialchars($related['nom']) ?>" 
                                                loading="lazy"
                                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=280&fit=crop'"
                                            >
                                            <div class="related-content">
                                                <h4><?= htmlspecialchars($related['nom']) ?></h4>
                                                <p style="color:#718f6b; font-size:0.75rem; margin-bottom:0.5rem;">
                                                    <?= htmlspecialchars($related['category_name'] ?? 'Aliment') ?>
                                                </p>
                                                <div style="display:flex; gap:0.8rem; font-size:0.8rem;">
                                                    <span style="color:#2e7d32;"><i class="fas fa-fire"></i> <?= number_format($related['calories'] ?? 0, 0) ?> kcal</span>
                                                    <span style="color:#f39c12;"><i class="fas fa-leaf"></i> <?= number_format($related['eco_score'] ?? 0, 1) ?>/10</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-search" style="font-size: 4rem; color: #c8e6c9;"></i>
                    <h3>Aliment non trouvé</h3>
                    <p style="margin: 1rem 0; color: #6b8a66;">L'aliment que vous recherchez n'existe pas ou a été supprimé.</p>
                    <a href="index.php?page=aliments" style="display: inline-block; background: #2e7d32; color: white; padding: 0.8rem 1.8rem; border-radius: 40px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left"></i> Voir tous les aliments
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <footer class="footer">
            <div class="footer-logo">
                <span>🌿 NutriWise</span>
            </div>
            <p class="footer-copyright">© 2024 NutriWise - Nutrition intelligente et durable</p>
        </footer>
    </div>
</body>
</html>