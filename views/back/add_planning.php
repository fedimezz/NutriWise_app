<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un planning - NutriWise</title>
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
                <a href="index.php?page=admin_plannings" class="active">🗓️ Plannings</a>
            </nav>
            <a href="index.php?page=home" class="back-to-site">← Retour au site</a>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Ajouter un planning</h1>
            </header>

            <div class="form-container" style="max-width: 980px;">
                <?php if(isset($error)): ?>
                    <p style="color:red; margin-bottom:15px; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <form action="index.php?page=admin_add_planning" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom du planning *</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Utilisateur *</label>
                            <select name="user_id" required>
                                <option value="">Choisir un utilisateur</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= (isset($_POST['user_id']) && intval($_POST['user_id']) === intval($user['id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date de début *</label>
                            <input type="date" name="start_date" value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Date de fin *</label>
                            <input type="date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Menus existants du planning</label>
                        <div style="border:1px solid #C8E6C9; border-radius:8px; padding:15px; background:#fafdf8; display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; max-height:200px; overflow-y:auto;">
                            <?php foreach($menus as $menu): ?>
                                <label style="display:flex; align-items:center; gap:10px; font-weight:400; color:#1B4D1B;">
                                    <input type="checkbox" name="menus[]" value="<?= $menu['id'] ?>" <?= (isset($_POST['menus']) && in_array($menu['id'], $_POST['menus'])) ? 'checked' : '' ?>>
                                    <span><?= htmlspecialchars($menu['title']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Créer de nouveaux menus pour ce planning</label>
                        <div id="new-menus-container" style="border:1px solid #A5D6A7; border-radius:8px; padding:15px; background:#f5fdf3;">
                            <div id="menus-list"></div>
                            <button type="button" id="add-menu-btn" class="btn-save" style="margin-top:10px; background:#4CAF50; padding:8px 16px; font-size:14px;">+ Ajouter un menu</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Statut</label>
                        <select name="status">
                            <?php $status = $_POST['status'] ?? 'draft'; ?>
                            <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Actif</option>
                            <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Terminé</option>
                        </select>
                    </div>

                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" class="btn-save">Enregistrer le planning</button>
                        <a href="index.php?page=admin_plannings" class="btn-cancel">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="views/assets/js/validation_planning.js"></script>
    <script>
        const days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        const mealTypes = ['Petit-déjeuner', 'Déjeuner', 'Dîner', 'Collation'];
        let menuCount = 0;

        document.getElementById('add-menu-btn').addEventListener('click', function(e) {
            e.preventDefault();
            addMenuRow();
        });

        function addMenuRow() {
            const container = document.getElementById('menus-list');
            const menuIndex = menuCount++;
            
            const menuRow = document.createElement('div');
            menuRow.id = 'menu-row-' + menuIndex;
            menuRow.style.cssText = 'margin-bottom:20px; padding:15px; background:white; border-radius:8px; border:2px solid #A5D6A7;';
            
            let mealsHTML = '';
            days.forEach((day, dayIndex) => {
                mealsHTML += `
                    <div style="margin-bottom:15px; padding:10px; background:#f9f9f9; border-radius:6px;">
                        <h4 style="margin:0 0 10px 0; color:#1B4D1B;">${day}</h4>
                        <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px;">
                `;
                mealTypes.forEach(meal => {
                    const mealKey = meal.toLowerCase().replace('-', '_').replace('é', 'e');
                    mealsHTML += `
                        <div>
                            <label style="font-size:11px; color:#666;">${meal}</label>
                            <textarea name="new_menus[${menuIndex}][meals][${dayIndex}][${mealKey}]" 
                                      placeholder="Décrire..." 
                                      rows="2" 
                                      style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; resize:vertical;"></textarea>
                        </div>
                    `;
                });
                mealsHTML += `
                        </div>
                    </div>
                `;
            });
            
            menuRow.innerHTML = `
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:10px; margin-bottom:15px; padding-bottom:15px; border-bottom:1px solid #ddd;">
                    <div>
                        <label style="font-size:12px; font-weight:bold; color:#1B4D1B;">Titre du menu</label>
                        <input type="text" name="new_menus[${menuIndex}][title]" placeholder="Ex: Menu été" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:bold; color:#1B4D1B;">Objectif</label>
                        <input type="text" name="new_menus[${menuIndex}][goal]" placeholder="Ex: Perte de poids" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:bold; color:#1B4D1B;">Calories cible</label>
                        <input type="number" name="new_menus[${menuIndex}][calories_target]" placeholder="Ex: 2000" min="0" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                    </div>
                    <button type="button" class="btn-delete" onclick="removeMenuRow(${menuIndex})" style="background:#ff6b6b; color:white; border:none; padding:8px 12px; border-radius:4px; cursor:pointer; align-self:flex-end; height:fit-content;">✕</button>
                </div>
                <div>
                    <h3 style="margin:0 0 15px 0; color:#1B4D1B; font-size:14px;">Repas du menu</h3>
                    ${mealsHTML}
                </div>
            `;
            
            container.appendChild(menuRow);
        }

        function removeMenuRow(index) {
            const row = document.getElementById('menu-row-' + index);
            if(row) {
                row.remove();
            }
        }

        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            let errors = [];
            
            // Valider tous les nouveaux menus
            document.querySelectorAll('[id^="menu-row-"]').forEach((menuRow, idx) => {
                const titleInput = menuRow.querySelector('input[name^="new_menus"][name*="[title]"]');
                const mealsTextareas = menuRow.querySelectorAll('textarea[name^="new_menus"]');
                
                if(titleInput) {
                    const title = titleInput.value.trim();
                    if(title && title.length < 3) {
                        errors.push(`Menu ${idx + 1}: Le titre doit contenir au moins 3 caractères`);
                    } else if(title && title.length > 100) {
                        errors.push(`Menu ${idx + 1}: Le titre ne doit pas dépasser 100 caractères`);
                    }
                }
                
                // Vérifier qu'au moins une description de repas est complétée
                let mealsFilled = 0;
                mealsTextareas.forEach(textarea => {
                    if(textarea.value.trim().length > 0) {
                        mealsFilled++;
                        if(textarea.value.trim().length > 500) {
                            errors.push(`Menu ${idx + 1}: Une description de repas est trop longue (max 500 caractères)`);
                        }
                    }
                });
                
                if(titleInput && titleInput.value.trim() && mealsFilled === 0) {
                    errors.push(`Menu ${idx + 1}: Veuillez ajouter au moins une description de repas`);
                }
            });
            
            if(errors.length > 0) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs suivantes:\n\n' + errors.join('\n'));
                return false;
            }
        });
    </script>

