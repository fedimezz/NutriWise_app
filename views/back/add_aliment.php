<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un aliment - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- On réutilise votre CSS des formulaires -->
    <link rel="stylesheet" href="views/assets/css/edit_user.css">
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
                <?php if(isset($error)): ?>
                    <p style="color:red; margin-bottom:15px; font-weight:bold;"><?= $error ?></p>
                <?php endif; ?>

                <form action="index.php?page=admin_add_aliment" method="POST">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom de l'aliment *</label>
                            <input type="text" name="nom" placeholder="Ex: Avocat, Quinoa, Saumon..." required>
                        </div>
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select name="categorie">
                                <option value="Fruits">Fruits</option>
                                <option value="Légumes">Légumes</option>
                                <option value="Protéines végétales">Protéines végétales</option>
                                <option value="Viandes & Poissons">Viandes & Poissons</option>
                                <option value="Féculents">Féculents & Céréales</option>
                                <option value="Laitages">Laitages</option>
                                <option value="Matières grasses">Matières grasses</option>
                            </select>
                        </div>
                    </div>

                    <h3 style="margin: 20px 0 15px; font-size:16px; color:#333;">Valeurs nutritionnelles (pour 100g)</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Calories (kcal)</label>
                            <input type="number" name="calories" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Protéines (g)</label>
                            <input type="number" step="0.1" name="proteines" value="0" min="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Glucides (g)</label>
                            <input type="number" step="0.1" name="glucides" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Lipides (g)</label>
                            <input type="number" step="0.1" name="lipides" value="0" min="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="flex-direction: row; align-items: center; gap: 10px;">
                            <input type="checkbox" name="durable" id="durable" style="width: auto;" checked>
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
</body>
</html>