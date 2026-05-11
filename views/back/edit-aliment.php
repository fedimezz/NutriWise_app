<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier aliment - NutriWise </title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

    <!-- Sidebar -->
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

    <!-- Main -->
    <main class="main-content">
        <header>
            <h1>Modifier l'aliment #<?= $aliment['id'] ?></h1>
        </header>

        <div class="form-container">

            <?php if(isset($success)): ?>
                <p style="color:green; font-weight:bold; margin-bottom:15px;">
                    <?= $success ?>
                </p>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <p style="color:red; margin-bottom:15px;">
                    <?= $error ?>
                </p>
            <?php endif; ?>

            <form action="index.php?page=admin_edit_aliment&id=<?= $aliment['id'] ?>" method="POST">

                <!-- NOM + CATEGORIE -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom de l'aliment</label>
                        <input type="text" name="nom"
                               value="<?= htmlspecialchars($aliment['nom']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="categorie">
                            <option value="Fruits" <?= $aliment['categorie']=="Fruits" ? 'selected' : '' ?>>Fruits</option>
                            <option value="Légumes" <?= $aliment['categorie']=="Légumes" ? 'selected' : '' ?>>Légumes</option>
                            <option value="Protéines végétales" <?= $aliment['categorie']=="Protéines végétales" ? 'selected' : '' ?>>Protéines végétales</option>
                            <option value="Viandes & Poissons" <?= $aliment['categorie']=="Viandes & Poissons" ? 'selected' : '' ?>>Viandes & Poissons</option>
                            <option value="Féculents" <?= $aliment['categorie']=="Féculents" ? 'selected' : '' ?>>Féculents</option>
                            <option value="Laitages" <?= $aliment['categorie']=="Laitages" ? 'selected' : '' ?>>Laitages</option>
                            <option value="Matières grasses" <?= $aliment['categorie']=="Matières grasses" ? 'selected' : '' ?>>Matières grasses</option>
                        </select>
                    </div>
                </div>

                <!-- NUTRITION -->
                <h3 style="margin: 20px 0 15px;">Valeurs nutritionnelles (100g)</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Calories (kcal)</label>
                        <input type="number" name="calories"
                               value="<?= $aliment['calories'] ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label>Protéines (g)</label>
                        <input type="number" step="0.1" name="proteines"
                               value="<?= $aliment['proteines'] ?>" min="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Glucides (g)</label>
                        <input type="number" step="0.1" name="glucides"
                               value="<?= $aliment['glucides'] ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label>Lipides (g)</label>
                        <input type="number" step="0.1" name="lipides"
                               value="<?= $aliment['lipides'] ?>" min="0">
                    </div>
                </div>

                <!-- DURABLE -->
                <div class="form-row">
                    <div class="form-group" style="flex-direction: row; align-items: center; gap:10px;">
                        <input type="checkbox" name="durable"
                               <?= $aliment['durable'] ? 'checked' : '' ?>>
                        <label style="margin:0;">Aliment durable 🌍</label>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="form-actions">
                    <button type="submit" class="btn-save">💾 Enregistrer</button>
                    <a href="index.php?page=admin_aliments"
                       class="btn-cancel"
                       style="text-decoration:none;">
                        Annuler
                    </a>
                </div>

            </form>
        </div>
    </main>
</div>

</body>
</html>