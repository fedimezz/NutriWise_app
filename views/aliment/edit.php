<?php
// views/back/edit_aliment.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier aliment - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/admin-global.css">
    <style>
        .current-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #c8e6c9;
        }
        .image-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
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
            <h1><i class="fas fa-edit"></i> Modifier aliment : <?= htmlspecialchars($aliment['nom']) ?></h1>
            <a href="index.php?page=admin_aliments" class="btn-cancel" style="padding:8px 16px;">← Retour</a>
        </header>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="index.php?page=admin_edit_aliment&id=<?= (int)$aliment['id'] ?>" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nom de l'aliment <span class="required">*</span></label>
                        <input type="text" name="nom" required value="<?= htmlspecialchars($aliment['nom']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Catégorie <span class="required">*</span></label>
                        <select name="category_id" required>
                            <option value="">-- Choisir une catégorie --</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= ($category['id'] == ($aliment['category_id'] ?? 0)) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-chart-line"></i> Valeurs nutritionnelles (pour 100g)</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Calories (kcal)</label>
                            <input type="number" name="calories" step="0.1" value="<?= htmlspecialchars($aliment['calories']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Protéines (g)</label>
                            <input type="number" name="proteines" step="0.1" value="<?= htmlspecialchars($aliment['proteines']) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Glucides (g)</label>
                            <input type="number" name="glucides" step="0.1" value="<?= htmlspecialchars($aliment['glucides']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Lipides (g)</label>
                            <input type="number" name="lipides" step="0.1" value="<?= htmlspecialchars($aliment['lipides']) ?>">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Éco-score <span class="required">*</span></label>
                        <input type="number" name="eco_score" min="0" max="10" step="0.1" required value="<?= htmlspecialchars($aliment['eco_score'] ?? 0) ?>">
                        <small>Note de 0 à 10</small>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" accept="image/png,image/jpeg,image/webp">
                        <small>JPG, PNG, WebP - laissez vide pour garder l'image actuelle</small>
                        
                        <?php if(!empty($aliment['image'])): ?>
                            <?php $imagePath = 'views/uploads/aliments/' . $aliment['image']; ?>
                            <div style="margin-top:15px; display:flex; align-items:center; gap:15px;">
                                <img src="<?= htmlspecialchars($imagePath) ?>" class="current-image" alt="Image actuelle" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="image-placeholder" style="display:none;">🥗</div>
                                <div>
                                    <label style="display:flex; align-items:center; gap:8px;">
                                        <input type="checkbox" name="delete_image" style="width:auto;"> Supprimer l'image
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex-direction:row; align-items:center; gap:10px;">
                        <input type="checkbox" id="durable" name="durable" <?= !empty($aliment['durable']) ? 'checked' : '' ?> style="width:auto;">
                        <label for="durable" style="margin:0;">Aliment durable 🌍</label>
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
</body>
</html>