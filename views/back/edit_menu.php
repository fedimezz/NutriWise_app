<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un menu - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/edit_user.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">🌿 NutriWise</div>
            <nav>
                <a href="index.php?page=admin_dashboard">📊 Tableau de bord</a>
                <a href="index.php?page=admin_users">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments">🥗 Aliments</a>
                <a href="index.php?page=admin_plannings">🗓️ Plannings</a>
            </nav>
            <a href="index.php?page=home" class="back-to-site">← Retour au site</a>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Modifier le menu #<?= $menu['id'] ?></h1>
            </header>

            <div class="form-container" style="max-width: 980px;">
                <?php if(isset($error)): ?>
                    <p style="color:red; margin-bottom:15px; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <form action="index.php?page=admin_edit_menu&id=<?= $menu['id'] ?>" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom du menu *</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? $menu['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Objectif</label>
                            <input type="text" name="goal" value="<?= htmlspecialchars($_POST['goal'] ?? $menu['goal']) ?>" placeholder="Ex: Perte de poids">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Calories ciblées</label>
                            <input type="number" name="calories_target" min="0" value="<?= isset($_POST['calories_target']) ? intval($_POST['calories_target']) : intval($menu['calories_target']) ?>" placeholder="Ex: 1800">
                        </div>
                        <div class="form-group">
                            <label>Utilisateur *</label>
                            <select name="assigned_to" required>
                                <option value="">Choisir un utilisateur</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= ((isset($_POST['assigned_to']) && intval($_POST['assigned_to']) === intval($user['id'])) || (!isset($_POST['assigned_to']) && intval($menu['assigned_to']) === intval($user['id']))) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Actif</label>
                        <input type="checkbox" name="is_active" value="1" <?= isset($_POST['is_active']) ? 'checked' : ($menu['is_active'] ? 'checked' : '') ?>> Activer ce menu
                    </div>

                    <div class="form-group">
                        <label>Description des repas</label>
                        <div style="border:1px solid #C8E6C9; border-radius:8px; padding:15px; background:#fafdf8; display:grid; gap:14px;">
                            <?php $days = [1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche']; ?>
                            <?php $mealTypes = ['breakfast' => 'Petit-déjeuner', 'lunch' => 'Déjeuner', 'dinner' => 'Dîner', 'snack' => 'Snack']; ?>
                            <?php foreach($days as $dayIndex => $dayName): ?>
                                <div style="padding:12px; background:#fff; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.05);">
                                    <h3 style="margin:0 0 10px; font-size:1rem; color:#1b5e20;"><?= $dayName ?></h3>
                                    <div style="display:grid; gap:10px;">
                                        <?php foreach($mealTypes as $mealKey => $mealLabel): ?>
                                            <div>
                                                <label style="font-weight:600; display:block; margin-bottom:5px;"><?= $mealLabel ?></label>
                                                <textarea name="meals[<?= $dayIndex ?>][<?= $mealKey ?>]" rows="2" placeholder="Description de <?= strtolower($mealLabel) ?>"><?= htmlspecialchars($_POST['meals'][$dayIndex][$mealKey] ?? $selectedMeals[$dayIndex][$mealKey] ?? '') ?></textarea>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" class="btn-save">Mettre à jour</button>
                        <a href="index.php?page=admin_menus" class="btn-cancel">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="views/assets/js/validation_menu.js"></script>
</body>
</html>
