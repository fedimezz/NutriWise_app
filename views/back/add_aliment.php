<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un aliment - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- On réutilise votre CSS des formulaires -->
    <link rel="stylesheet" href="./assets/css/edit_user.css">
    <style>
        .sidebar .logo { font-size: 1.2rem; font-weight: 700; }
        .sidebar nav a { display: block; margin-bottom: 0.75rem; padding: 0.8rem 1rem; border-radius: 10px; color: #fff; text-decoration: none; }
        .sidebar nav a.active { background: #1b5e20; }
        .sidebar { background: #2e7d32; color: #fff; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">NutriWise Admin</div>
            <nav>
                <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="<?= (strpos($page, 'admin_user') !== false || strpos($page, 'user') !== false && $page != 'profile') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_suivis" class="<?= (strpos($page, 'suivi') !== false) ? 'active' : '' ?>">📈 Suivis</a>
                <a href="index.php?page=admin_consultations" class="<?= (strpos($page, 'consultation') !== false) ? 'active' : '' ?>">🩺 Consultations</a>
                <a href="index.php?page=admin_aliments" class="<?= (strpos($page, 'aliment') !== false) ? 'active' : '' ?>">🥗 Aliments</a>
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