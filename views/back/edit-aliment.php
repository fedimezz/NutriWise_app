<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier aliment - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/edit_user.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
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

        <!-- Main -->
        <main class="main-content">
            <header>
                <h1>Modifier l'aliment: <?= htmlspecialchars($aliment['nom'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
            </header>

            <div class="form-container">
                <?php if(isset($_SESSION['error'])): ?>
                    <p style="color:red; margin-bottom:15px; font-weight:bold; background:#ffebee; padding:10px; border-radius:5px; border-left:4px solid #f44336;">
                        <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                    </p>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="index.php?page=admin_edit_aliment&id=<?= $aliment['id'] ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

                    <!-- NOM + CATEGORIE -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom de l'aliment *</label>
                            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($aliment['nom'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Catégorie *</label>
                            <select id="category_id" name="category_id" required>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?= (int)$category['id'] ?>" <?= ((int)$category['id'] === (int)($aliment['category_id'] ?? 0)) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- NUTRITION -->
                    <h3 style="margin: 20px 0 15px; font-size:16px; color:#333;">Valeurs nutritionnelles (pour 100g)</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="calories">Calories (kcal)</label>
                            <input type="number" id="calories" name="calories" value="<?= htmlspecialchars((string)$aliment['calories'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" min="0" step="0.1">
                        </div>
                        <div class="form-group">
                            <label for="proteines">Protéines (g)</label>
                            <input type="number" id="proteines" step="0.1" name="proteines" value="<?= htmlspecialchars((string)$aliment['proteines'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" min="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="glucides">Glucides (g)</label>
                            <input type="number" id="glucides" step="0.1" name="glucides" value="<?= htmlspecialchars((string)$aliment['glucides'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label for="lipides">Lipides (g)</label>
                            <input type="number" id="lipides" step="0.1" name="lipides" value="<?= htmlspecialchars((string)$aliment['lipides'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" min="0">
                        </div>
                    </div>

                    <!-- ECO-SCORE + IMAGE -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="eco_score">Éco-score *</label>
                            <input type="number" id="eco_score" name="eco_score" min="0" max="10" step="0.1" value="<?= htmlspecialchars((string)($aliment['eco_score'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>
                            <small style="color:#666; font-size:12px;">Note de 0 à 10 (10 = très écologique)</small>
                        </div>
                        <div class="form-group">
                            <label for="image">Image de l'aliment</label>
                            <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp">
                            <small style="color:#666; font-size:12px;">Formats acceptés: JPG, PNG, WebP (max 3MB)</small>
                            <?php if(!empty($aliment['image'])): ?>
                                <div style="margin-top: 0.75rem; display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                                    <img src="../../uploads/aliments/<?= htmlspecialchars($aliment['image'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="Image actuelle" style="width:80px; height:80px; object-fit:cover; border-radius:12px; border:1px solid #e0e0e0;">
                                    <div>
                                        <label style="display:flex; align-items:center; gap:5px; font-size:14px;">
                                            <input type="checkbox" name="keep_image" checked style="width:auto;">
                                            Garder l'image actuelle
                                        </label>
                                        <small style="color:#666; font-size:12px;">Décochez pour supprimer l'image</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- DURABLE -->
                    <div class="form-row">
                        <div class="form-group" style="flex-direction: row; align-items: center; gap:10px;">
                            <input type="checkbox" id="durable" name="durable" <?= $aliment['durable'] ? 'checked' : '' ?> style="width:auto;">
                            <label for="durable" style="margin:0;">Aliment durable 🌍 (faible empreinte écologique)</label>
                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div class="form-actions" style="margin-top:30px;">
                        <button type="submit" class="btn-save">💾 Enregistrer les modifications</button>
                        <a href="index.php?page=admin_aliments" class="btn-cancel" style="text-decoration:none;">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>