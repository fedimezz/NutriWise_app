<?php
// views/back/add_recette.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une recette - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="views/assets/css/admin-global.css">

    <style>
        .image-type-group {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
        }
        .image-type-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
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
            <h1><i class="fas fa-plus-circle"></i> Ajouter une recette</h1>
            <a href="index.php?page=admin_recettes" class="btn-cancel" style="padding:8px 16px;">← Retour</a>
        </header>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="index.php?page=admin_add_recette" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom de la recette <span class="required">*</span></label>
                            <input type="text" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" placeholder="Ex: Omelette aux fines herbes">
                        </div>
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select name="categorie">
                                <option value="">Sélectionner</option>
                                <option value="Petit-déjeuner">🍳 Petit-déjeuner</option>
                                <option value="Entrée">🥗 Entrée</option>
                                <option value="Plat principal">🍽️ Plat principal</option>
                                <option value="Dessert">🍰 Dessert</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-hourglass-half"></i> Temps & difficulté</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Temps préparation (min)</label>
                            <input type="number" name="temps_preparation" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Temps cuisson (min)</label>
                            <input type="number" name="temps_cuisson" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Difficulté</label>
                            <select name="difficulte">
                                <option value="Facile">😊 Facile</option>
                                <option value="Moyen" selected>👍 Moyen</option>
                                <option value="Difficile">🔥 Difficile</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Portions</label>
                            <input type="number" name="portions" value="4" min="1">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-image"></i> Image</h3>
                    <div class="image-type-group">
                        <label><input type="radio" name="image_type" value="upload" checked> 📁 Upload image</label>
                        <label><input type="radio" name="image_type" value="url"> 🔗 URL externe</label>
                    </div>
                    <div id="upload_input">
                        <input type="file" name="image" accept="image/*">
                        <small>Formats: JPG, PNG, WebP</small>
                    </div>
                    <div id="url_input" style="display:none;">
                        <input type="text" name="image_url" placeholder="https://exemple.com/image.jpg">
                        <small>Entrez l'URL complète de l'image</small>
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

<script>
    document.querySelectorAll('input[name="image_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('upload_input').style.display = this.value === 'upload' ? 'block' : 'none';
            document.getElementById('url_input').style.display = this.value === 'url' ? 'block' : 'none';
        });
    });
</script>
</body>
</html>