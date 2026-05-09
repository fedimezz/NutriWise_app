<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un aliment - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/edit_user.css">
    <style>
        .image-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            display: none;
        }
        .image-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
        }
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .url-option {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .url-option label {
            font-weight: normal;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">🌿 NutriWise</div>
            <nav>
                <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Tableau de bord</a>
                <a href="index.php?page=admin_users" class="<?= ($page=='admin_users' || $page=='admin_add_user' || $page=='admin_edit_user') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments" class="<?= ($page=='admin_aliments' || $page=='admin_add_aliment' || $page=='admin_edit_aliment') ? 'active' : '' ?>">🥗 Aliments</a>
                <a href="index.php?page=admin_recettes" class="<?= ($page=='admin_recettes') ? 'active' : '' ?>">📖 Recettes</a>
                <a href="index.php?page=admin_plans" class="<?= ($page=='admin_plans') ? 'active' : '' ?>">📅 Plans</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Ajouter un nouvel aliment</h1>
            </header>

            <div class="form-container">
                <?php if(isset($_SESSION['error'])): ?>
                    <p style="color:red; margin-bottom:15px; font-weight:bold; background:#ffebee; padding:10px; border-radius:5px; border-left:4px solid #f44336;">
                        <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                    </p>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="index.php?page=admin_add_aliment" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom de l'aliment *</label>
                            <input type="text" id="nom" name="nom" placeholder="Ex: Avocat, Quinoa, Saumon..." required value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                        </div>
                        <div class="form-group">
                            <label for="category_id">Catégorie *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?= (int)$category['id'] ?>" <?= ((int)($_POST['category_id'] ?? 0) === (int)$category['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['icon'] ?? '🥗', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> <?= htmlspecialchars($category['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h3 style="margin: 20px 0 15px; font-size:16px; color:#333;">Valeurs nutritionnelles (pour 100g)</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="calories">Calories (kcal)</label>
                            <input type="number" id="calories" name="calories" value="<?= htmlspecialchars($_POST['calories'] ?? '0', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" min="0" step="0.1">
                        </div>
                        <div class="form-group">
                            <label for="proteines">Protéines (g)</label>
                            <input type="number" id="proteines" name="proteines" step="0.1" value="<?= htmlspecialchars($_POST['proteines'] ?? '0', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" min="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="glucides">Glucides (g)</label>
                            <input type="number" id="glucides" name="glucides" step="0.1" value="<?= htmlspecialchars($_POST['glucides'] ?? '0', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label for="lipides">Lipides (g)</label>
                            <input type="number" id="lipides" name="lipides" step="0.1" value="<?= htmlspecialchars($_POST['lipides'] ?? '0', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" min="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="eco_score">Éco-score *</label>
                            <input type="number" id="eco_score" name="eco_score" min="0" max="10" step="0.1" value="<?= htmlspecialchars($_POST['eco_score'] ?? '8', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>
                            <small style="color:#666; font-size:12px;">Note de 0 à 10 (10 = très écologique)</small>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <div style="margin-bottom: 10px;">
                                <label style="display: inline-flex; align-items: center; gap: 15px;">
                                    <input type="radio" name="image_type" value="upload" checked onchange="toggleImageInput()"> 📁 Upload image
                                </label>
                                <label style="display: inline-flex; align-items: center; gap: 15px; margin-left: 20px;">
                                    <input type="radio" name="image_type" value="url" onchange="toggleImageInput()"> 🔗 URL externe
                                </label>
                            </div>
                            <div id="upload_input">
                                <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp">
                                <small class="help-text">Formats acceptés: JPG, PNG, WebP (max 3MB)</small>
                            </div>
                            <div id="url_input" style="display: none;">
                                <input type="text" id="image_url" name="image_url" placeholder="https://exemple.com/image.jpg" style="width: 100%;">
                                <small class="help-text">Entrez l'URL complète de l'image (externe)</small>
                            </div>
                            <div id="image_preview" class="image-preview">
                                <img id="preview_img" src="" alt="Aperçu">
                                <p style="margin-top: 5px; font-size: 11px; color: #888;">Aperçu de l'image</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="flex-direction: row; align-items: center; gap: 10px;">
                            <input type="checkbox" id="durable" name="durable" style="width: auto;" <?= isset($_POST['durable']) ? 'checked' : 'checked' ?>>
                            <label for="durable" style="margin-bottom: 0;">Aliment à faible empreinte écologique (Durable 🌍)</label>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" class="btn-save">Enregistrer l'aliment</button>
                        <a href="index.php?page=admin_aliments" class="btn-cancel" style="text-decoration:none;">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleImageInput() {
            const imageType = document.querySelector('input[name="image_type"]:checked').value;
            const uploadDiv = document.getElementById('upload_input');
            const urlDiv = document.getElementById('url_input');
            const previewDiv = document.getElementById('image_preview');
            
            if (imageType === 'upload') {
                uploadDiv.style.display = 'block';
                urlDiv.style.display = 'none';
            } else {
                uploadDiv.style.display = 'none';
                urlDiv.style.display = 'block';
            }
            previewDiv.style.display = 'none';
        }
        
        // Preview pour upload
        document.getElementById('image')?.addEventListener('change', function(e) {
            const previewDiv = document.getElementById('image_preview');
            const previewImg = document.getElementById('preview_img');
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    previewDiv.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Preview pour URL
        document.getElementById('image_url')?.addEventListener('input', function(e) {
            const previewDiv = document.getElementById('image_preview');
            const previewImg = document.getElementById('preview_img');
            const url = this.value.trim();
            
            if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                previewImg.src = url;
                previewDiv.style.display = 'block';
            } else {
                previewDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>