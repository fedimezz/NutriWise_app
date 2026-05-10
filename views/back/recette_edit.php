<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une recette - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 1rem;
        }
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--vert-pale);
        }
        .form-section:last-child {
            border-bottom: none;
        }
        .form-section h3 {
            color: var(--vert-profond);
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
            font-size: 0.85rem;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid var(--vert-pale);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--vert-principal);
        }
        .current-image {
            margin-top: 10px;
        }
        .current-image img {
            max-width: 150px;
            border-radius: 8px;
            border: 1px solid var(--vert-pale);
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--vert-pale);
        }
        .btn-save {
            background: var(--vert-principal);
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-cancel {
            background: #f0f3ef;
            color: #666;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-save:hover, .btn-cancel:hover {
            opacity: 0.8;
        }
        .alert-error {
            background: #FFEBEE;
            color: #C62828;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: var(--vert-pale);
            color: var(--vert-profond);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>
        <nav>
            <a href="index.php?page=admin_dashboard">📊 Tableau de bord</a>
            <a href="index.php?page=admin_users">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes" class="active">📖 Recettes</a>
            <a href="index.php?page=admin_plans">📅 Plans</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1>✏️ Modifier la recette</h1>
            <a href="index.php?page=admin_recettes" style="color:#666; text-decoration:none;">← Retour</a>
        </header>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-success">
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom de la recette *</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($recette['nom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Catégorie *</label>
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
                    <button type="submit" class="btn-save">💾 Enregistrer</button>
                    <a href="index.php?page=admin_recettes" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>