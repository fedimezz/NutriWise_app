<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une recette - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="views/assets/css/admin-global.css">

  
</head>
<body>
    <div class="dashboard-container">
    <aside class="sidebar">
    <div class="logo">🌿 NutriWise</div>

    <nav>
        <a href="index.php?page=admin_dashboard"
           class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">
            📊 Tableau de bord
        </a>

        <a href="index.php?page=admin_users"
           class="<?= ($page=='admin_users') ? 'active' : '' ?>">
            👥 Utilisateurs
        </a>

        <a href="index.php?page=admin_aliments"
           class="<?= ($page=='admin_aliments') ? 'active' : '' ?>">
            🥗 Aliments
        </a>

        <a href="index.php?page=admin_recettes"
           class="<?= ($page=='admin_recettes') ? 'active' : '' ?>">
            📖 Recettes
        </a>

        <a href="index.php?page=admin_plannings"
           class="<?= ($page=='admin_plannings') ? 'active' : '' ?>">
            📋 Plannings
        </a>
    </nav>

    <a href="index.php?page=logout" class="logout">
        🚪 Déconnexion
    </a>
</aside>

        <main class="main-content">
            <header>
                <h1>Ajouter une recette</h1>
                <a href="index.php?page=admin_recettes" class="back-link">← Retour à la liste</a>
            </header>

            <div class="form-container">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=admin_add_recette" method="POST" enctype="multipart/form-data" id="recetteForm" novalidate>
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                    <!-- Informations générales -->
                    <div class="form-section">
                        <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom de la recette *</label>
                                <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="categorie">Catégorie *</label>
                                <select id="categorie" name="categorie" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Petit-déjeuner">🍳 Petit-déjeuner</option>
                                    <option value="Entrée">🥗 Entrée</option>
                                    <option value="Plat principal">🍽️ Plat principal</option>
                                    <option value="Dessert">🍰 Dessert</option>
                                    <option value="Snack">🍪 Snack</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Temps et difficulté -->
                    <div class="form-section">
                        <h3><i class="fas fa-hourglass-half"></i> Temps et difficulté</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="temps_preparation">Temps de préparation (min)</label>
                                <input type="number" id="temps_preparation" name="temps_preparation" value="<?= htmlspecialchars($_POST['temps_preparation'] ?? '0') ?>" min="0">
                            </div>
                            <div class="form-group">
                                <label for="temps_cuisson">Temps de cuisson (min)</label>
                                <input type="number" id="temps_cuisson" name="temps_cuisson" value="<?= htmlspecialchars($_POST['temps_cuisson'] ?? '0') ?>" min="0">
                            </div>
                            <div class="form-group">
                                <label for="difficulte">Difficulté</label>
                                <select id="difficulte" name="difficulte">
                                    <option value="Facile">😊 Facile</option>
                                    <option value="Moyen" selected>👍 Moyen</option>
                                    <option value="Difficile">🔥 Difficile</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="portions">Nombre de portions</label>
                                <input type="number" id="portions" name="portions" value="<?= htmlspecialchars($_POST['portions'] ?? '4') ?>" min="1">
                            </div>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="form-section">
                        <h3><i class="fas fa-image"></i> Image</h3>
                        <div class="form-group">
                            <label style="display: inline-flex; align-items: center; gap: 15px;">
                                <input type="radio" name="image_type" value="upload" checked onchange="toggleImageInput()"> 📁 Upload image
                            </label>
                            <label style="display: inline-flex; align-items: center; gap: 15px; margin-left: 20px;">
                                <input type="radio" name="image_type" value="url" onchange="toggleImageInput()"> 🔗 URL externe
                            </label>
                        </div>
                        <div id="upload_input">
                            <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp">
                            <small>Formats: JPG, PNG, WebP (max 3MB)</small>
                        </div>
                        <div id="url_input" style="display: none;">
                            <input type="text" id="image_url" name="image_url" placeholder="https://exemple.com/recette.jpg">
                        </div>
                        <div id="image_preview" class="image-preview">
                            <img id="preview_img" src="" alt="Aperçu">
                        </div>
                    </div>

                    <!-- Ingrédients -->
                    <div class="form-section">
                        <h3><i class="fas fa-shopping-basket"></i> Ingrédients</h3>
                        <div id="ingredients-container">
                            <div class="ingredient-row" data-index="0">
                                <select name="ingredient_ids[]" required>
                                    <option value="">Choisir un ingrédient</option>
                                    <?php foreach($ingredients as $ingredient): ?>
                                        <option value="<?= $ingredient['id'] ?>"><?= htmlspecialchars($ingredient['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="quantites[]" placeholder="Quantité" step="0.1" required>
                                <input type="text" name="unites[]" placeholder="Unité (g, ml, c.à.s...)" value="g">
                                <button type="button" class="btn-remove-ingredient" onclick="removeIngredient(this)">🗑️</button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-ingredient" onclick="addIngredient()">+ Ajouter un ingrédient</button>
                    </div>

                    <!-- Étapes -->
                    <div class="form-section">
                        <h3><i class="fas fa-list-ol"></i> Étapes de préparation</h3>
                        <div id="steps-container">
                            <div class="step-row" data-index="0">
                                <span style="font-weight:bold;">1.</span>
                                <textarea name="etapes[]" placeholder="Décrivez l'étape..." required></textarea>
                                <button type="button" class="btn-remove-ingredient" onclick="removeStep(this)">🗑️</button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-ingredient" onclick="addStep()">+ Ajouter une étape</button>
                    </div>

                    <!-- Tags -->
                    <div class="form-section">
                        <h3><i class="fas fa-tags"></i> Tags</h3>
                        <div class="form-group">
                            <input type="text" id="tags" name="tags" placeholder="sain,rapide,végétarien (séparés par des virgules)" value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>">
                            <small>Séparez les tags par des virgules</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save">📌 Enregistrer la recette</button>
                        <a href="index.php?page=admin_recettes" class="btn-cancel">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let ingredientCount = 1;
        let stepCount = 1;

        function toggleImageInput() {
            const type = document.querySelector('input[name="image_type"]:checked').value;
            document.getElementById('upload_input').style.display = type === 'upload' ? 'block' : 'none';
            document.getElementById('url_input').style.display = type === 'url' ? 'block' : 'none';
        }

        document.getElementById('image')?.addEventListener('change', function(e) {
            const preview = document.getElementById('image_preview');
            const img = document.getElementById('preview_img');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    img.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        document.getElementById('image_url')?.addEventListener('input', function() {
            const preview = document.getElementById('image_preview');
            const img = document.getElementById('preview_img');
            if (this.value && (this.value.startsWith('http://') || this.value.startsWith('https://'))) {
                img.src = this.value;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });

        function addIngredient() {
            const container = document.getElementById('ingredients-container');
            const newRow = document.createElement('div');
            newRow.className = 'ingredient-row';
            newRow.setAttribute('data-index', ingredientCount);
            newRow.innerHTML = `
                <select name="ingredient_ids[]" required>
                    <option value="">Choisir un ingrédient</option>
                    <?php foreach($ingredients as $ingredient): ?>
                        <option value="<?= $ingredient['id'] ?>"><?= htmlspecialchars($ingredient['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="quantites[]" placeholder="Quantité" step="0.1" required>
                <input type="text" name="unites[]" placeholder="Unité" value="g">
                <button type="button" class="btn-remove-ingredient" onclick="removeIngredient(this)">🗑️</button>
            `;
            container.appendChild(newRow);
            ingredientCount++;
        }

        function removeIngredient(btn) {
            const container = document.getElementById('ingredients-container');
            if (container.children.length > 1) {
                btn.closest('.ingredient-row').remove();
            }
        }

        function addStep() {
            const container = document.getElementById('steps-container');
            const stepNumber = container.children.length + 1;
            const newRow = document.createElement('div');
            newRow.className = 'step-row';
            newRow.setAttribute('data-index', stepCount);
            newRow.innerHTML = `
                <span style="font-weight:bold;">${stepNumber}.</span>
                <textarea name="etapes[]" placeholder="Décrivez l'étape..." required></textarea>
                <button type="button" class="btn-remove-ingredient" onclick="removeStep(this)">🗑️</button>
            `;
            container.appendChild(newRow);
            stepCount++;
            renumberSteps();
        }

        function removeStep(btn) {
            const container = document.getElementById('steps-container');
            if (container.children.length > 1) {
                btn.closest('.step-row').remove();
                renumberSteps();
            }
        }

        function renumberSteps() {
            const steps = document.querySelectorAll('#steps-container .step-row');
            steps.forEach((step, index) => {
                step.querySelector('span').textContent = `${index + 1}.`;
            });
        }
    </script>
</body>
</html>