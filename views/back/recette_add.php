<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une recette - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles spécifiques au formulaire */
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
        .ingredient-row, .step-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .ingredient-row select,
        .ingredient-row input {
            flex: 1;
        }
        .step-row textarea {
            flex: 1;
            min-height: 60px;
        }
        .btn-remove {
            background: #fee9e7;
            color: #d9544a;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-add {
            background: var(--vert-pale);
            color: var(--vert-profond);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }
        .image-preview {
            margin-top: 10px;
            display: none;
        }
        .image-preview img {
            max-width: 150px;
            border-radius: 8px;
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
        .alert-error {
            background: #FFEBEE;
            color: #C62828;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        small {
            color: #888;
            font-size: 0.7rem;
            display: block;
            margin-top: 0.25rem;
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
            <h1>➕ Ajouter une recette</h1>
            <a href="index.php?page=admin_recettes" style="color:#666; text-decoration:none;">← Retour</a>
        </header>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="index.php?page=admin_add_recette" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom de la recette *</label>
                            <input type="text" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Catégorie *</label>
                            <select name="categorie" required>
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
                        <label><input type="radio" name="image_type" value="upload" checked> 📁 Upload</label>
                        <label><input type="radio" name="image_type" value="url"> 🔗 URL</label>
                    </div>
                    <div id="upload_input">
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <div id="url_input" style="display:none;">
                        <input type="text" name="image_url" placeholder="https://...">
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