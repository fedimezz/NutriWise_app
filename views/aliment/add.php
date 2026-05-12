<?php
// views/back/add_aliment.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un aliment - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/admin-global.css">
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
            border-radius: 8px;
        }
        .help-text {
            font-size: 12px;
            color: #6b8a66;
            margin-top: 5px;
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
            <a href="index.php?page=admin_aliments" class="active">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes">📖 Recettes</a>
            <a href="index.php?page=admin_plannings">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1><i class="fas fa-plus-circle"></i> Ajouter un aliment</h1>
            <a href="index.php?page=admin_aliments" class="btn-cancel" style="padding:8px 16px;">← Retour</a>
        </header>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="index.php?page=admin_add_aliment" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nom de l'aliment <span class="required">*</span></label>
                        <input type="text" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" placeholder="Ex: Avocat, Quinoa...">
                    </div>
                    <div class="form-group">
                        <label>Catégorie <span class="required">*</span></label>
                        <select name="category_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-chart-line"></i> Valeurs nutritionnelles (pour 100g)</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Calories (kcal)</label>
                            <input type="number" name="calories" step="0.1" value="0">
                        </div>
                        <div class="form-group">
                            <label>Protéines (g)</label>
                            <input type="number" name="proteines" step="0.1" value="0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Glucides (g)</label>
                            <input type="number" name="glucides" step="0.1" value="0">
                        </div>
                        <div class="form-group">
                            <label>Lipides (g)</label>
                            <input type="number" name="lipides" step="0.1" value="0">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Éco-score <span class="required">*</span></label>
                        <input type="number" name="eco_score" min="0" max="10" step="0.1" value="8">
                        <small>Note de 0 à 10 (10 = très écologique)</small>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <div style="margin-bottom: 10px;">
                            <label style="margin-right: 15px;">
                                <input type="radio" name="image_type" value="upload" checked> 📁 Upload
                            </label>
                            <label>
                                <input type="radio" name="image_type" value="url"> 🔗 URL externe
                            </label>
                        </div>
                        <div id="upload_input">
                            <input type="file" name="image" accept="image/*">
                        </div>
                        <div id="url_input" style="display:none;">
                            <input type="text" name="image_url" placeholder="https://exemple.com/image.jpg">
                        </div>
                        <div id="image_preview" class="image-preview">
                            <img id="preview_img" src="" alt="Aperçu">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex-direction: row; align-items: center; gap: 10px;">
                        <input type="checkbox" id="durable" name="durable" checked style="width: auto;">
                        <label for="durable" style="margin:0;">Aliment durable 🌍 (faible empreinte écologique)</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="index.php?page=admin_aliments" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    function toggleImageInput() {
        const type = document.querySelector('input[name="image_type"]:checked').value;
        document.getElementById('upload_input').style.display = type === 'upload' ? 'block' : 'none';
        document.getElementById('url_input').style.display = type === 'url' ? 'block' : 'none';
    }
    
    document.querySelectorAll('input[name="image_type"]').forEach(radio => {
        radio.addEventListener('change', toggleImageInput);
    });
    
    document.getElementById('image')?.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('preview_img').src = event.target.result;
                document.getElementById('image_preview').style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    document.getElementById('image_url')?.addEventListener('input', function() {
        const url = this.value.trim();
        if (url) {
            document.getElementById('preview_img').src = url;
            document.getElementById('image_preview').style.display = 'block';
        }
    });
    
    toggleImageInput();
</script>
</body>
</html>