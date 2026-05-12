<?php
// views/back/edit_recette.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une recette - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="views/assets/css/admin-global.css">

    <style>
        .current-image {
            margin-top: 10px;
        }
        .current-image img {
            max-width: 150px;
            border-radius: 8px;
            border: 1px solid #c8e6c9;
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #6b8a66;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>
        <nav>
            <a href="index.php?page=admin_dashboard">📊 Dashboard</a>
            <a href="index.php?page=admin_users">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes" class="active">📖 Recettes</a>
            <a href="index.php?page=admin_plannings">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1><i class="fas fa-edit"></i> Modifier la recette</h1>
            <a href="index.php?page=admin_recettes" class="btn-cancel" style="padding:8px 16px;">← Retour</a>
        </header>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-success">✓ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom de la recette <span class="required">*</span></label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($recette['nom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select name="categorie">
                                <option value="Petit-déjeuner" <?= ($recette['categorie'] ?? '') == 'Petit-déjeuner' ? 'selected' : '' ?>>🍳 Petit-déjeuner</option>
                                <option value="Entrée" <?= ($recette['categorie'] ?? '') == 'Entrée' ? 'selected' : '' ?>>🥗 Entrée</option>
                                <option value="Plat principal" <?= ($recette['categorie'] ?? '') == 'Plat principal' ? 'selected' : '' ?>>🍽️ Plat principal</option>
                                <option value="Dessert" <?= ($recette['categorie'] ?? '') == 'Dessert' ? 'selected' : '' ?>>🍰 Dessert</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"><?= htmlspecialchars($recette['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-hourglass-half"></i> Temps & difficulté</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Temps préparation (min)</label>
                            <input type="number" name="temps_preparation" value="<?= $recette['temps_preparation'] ?? 0 ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>Temps cuisson (min)</label>
                            <input type="number" name="temps_cuisson" value="<?= $recette['temps_cuisson'] ?? 0 ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label>Difficulté</label>
                            <select name="difficulte">
                                <option value="Facile" <?= ($recette['difficulte'] ?? '') == 'Facile' ? 'selected' : '' ?>>😊 Facile</option>
                                <option value="Moyen" <?= ($recette['difficulte'] ?? '') == 'Moyen' ? 'selected' : '' ?>>👍 Moyen</option>
                                <option value="Difficile" <?= ($recette['difficulte'] ?? '') == 'Difficile' ? 'selected' : '' ?>>🔥 Difficile</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Portions</label>
                            <input type="number" name="portions" value="<?= $recette['portions'] ?? 4 ?>" min="1">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-image"></i> Image</h3>
                    
                    <?php if(!empty($recette['image'])): ?>
                        <?php 
                        $imagePath = $recette['image'];
                        if(!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                            $imagePath = 'views/assets/uploads/recettes/' . $imagePath;
                        }
                        ?>
                        <div class="current-image">
                            <label>Image actuelle :</label><br>
                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="Image actuelle">
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group" style="margin-top: 1rem;">
                        <label>Nouvelle image (optionnel)</label>
                        <input type="file" name="image" accept="image/*">
                        <small>Laissez vide pour garder l'image actuelle</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="index.php?page=admin_recettes" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>