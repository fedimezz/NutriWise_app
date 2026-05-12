<?php
// views/back/edit_planning.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le planning - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/admin-global.css">
    <style>
        .readonly-field {
            background: #f8f9fa;
            padding: 12px 15px;
            border: 2px solid #c8e6c9;
            border-radius: 12px;
            color: #4a5568;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 15px;
            margin-top: 15px;
            max-height: 400px;
            overflow-y: auto;
            padding: 5px;
        }
        .menu-card {
            background: white;
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #c8e6c9;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .menu-card:hover {
            border-color: #2e7d32;
            transform: translateX(5px);
        }
        .menu-card.selected {
            border-color: #2e7d32;
            background: #e8f5e9;
        }
        .menu-card input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            margin: 0;
        }
        .menu-info {
            flex: 1;
        }
        .menu-name {
            font-weight: 700;
            color: #2e7d32;
        }
        .menu-stats {
            font-size: 0.75rem;
            color: #6b8a66;
            margin-top: 4px;
        }
        .menu-cals {
            font-size: 0.8rem;
            font-weight: 600;
            color: #2e7d32;
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
            <a href="index.php?page=admin_aliments">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes">📖 Recettes</a>
            <a href="index.php?page=admin_plannings" class="active">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1><i class="fas fa-edit"></i> Modifier le planning</h1>
            <div>
                <form method="POST" action="index.php?page=admin_delete_planning" style="display:inline;" onsubmit="return confirm('Supprimer ce planning ?')" novalidate>
                    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $planning['id'] ?>">
                    <button type="submit" class="btn-delete" style="padding:10px 20px;">🗑️ Supprimer</button>
                </form>
                <a href="index.php?page=admin_plannings" class="btn-cancel" style="padding:10px 20px;">← Retour</a>
            </div>
        </header>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert-error">
                <strong>Erreurs :</strong>
                <ul style="margin-left:20px; margin-top:8px;">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="" id="planningForm" novalidate>
                <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">

                <div class="form-group">
                    <label for="name">Nom du planning <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($planning['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?= htmlspecialchars($planning['description'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Utilisateur</label>
                        <div class="readonly-field">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($planning['prenom'] ?? '') ?> <?= htmlspecialchars($planning['nom'] ?? '') ?>
                            <br><small><?= htmlspecialchars($planning['email'] ?? '') ?></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Statut</label>
                        <select id="status" name="status">
                            <option value="draft" <?= ($planning['status'] ?? '') == 'draft' ? 'selected' : '' ?>>📝 Brouillon</option>
                            <option value="active" <?= ($planning['status'] ?? '') == 'active' ? 'selected' : '' ?>>✅ Actif</option>
                            <option value="completed" <?= ($planning['status'] ?? '') == 'completed' ? 'selected' : '' ?>>🏆 Terminé</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Date de début <span class="required">*</span></label>
                        <input type="date" id="start_date" name="start_date" required value="<?= htmlspecialchars($planning['start_date'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="end_date">Date de fin <span class="required">*</span></label>
                        <input type="date" id="end_date" name="end_date" required value="<?= htmlspecialchars($planning['end_date'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-utensils"></i> Sélectionner les menus <span class="required">*</span></h3>
                    <p style="margin-bottom:15px; color:#6b8a66;">Sélectionnez au moins un menu pour ce planning</p>
                    
                    <div class="menu-grid">
                        <?php foreach ($menus as $menu): ?>
                            <div class="menu-card" data-menu-id="<?= $menu['id'] ?>" onclick="toggleMenu(this, <?= $menu['id'] ?>)">
                                <input type="checkbox" id="menu_<?= $menu['id'] ?>" name="menu_ids[]" value="<?= $menu['id'] ?>"
                                    <?= (in_array($menu['id'], $currentMenuIds)) ? 'checked' : '' ?>
                                    onclick="event.stopPropagation()">
                                <div class="menu-info">
                                    <div class="menu-name"><?= htmlspecialchars($menu['name']) ?></div>
                                    <div class="menu-stats">
                                        <i class="fas fa-weight-hanging"></i> <?= $menu['aliments_count'] ?? 0 ?> aliments
                                    </div>
                                </div>
                                <div class="menu-cals">
                                    🔥 <?= round($menu['total_calories'] ?? 0) ?> kcal
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (empty($menus)): ?>
                        <p class="alert-error" style="text-align:center;">
                            ⚠️ Aucun menu disponible. 
                            <a href="index.php?page=admin_menus" style="color:#2e7d32;">Créez d'abord des menus</a>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Mettre à jour</button>
                    <a href="index.php?page=admin_plannings" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    const currentMenuIds = <?= json_encode($currentMenuIds) ?>;

    function toggleMenu(element, menuId) {
        const checkbox = document.getElementById('menu_' + menuId);
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            element.classList.add('selected');
        } else {
            element.classList.remove('selected');
        }
    }

    document.querySelectorAll('.menu-card').forEach(card => {
        const checkbox = card.querySelector('input[type="checkbox"]');
        if (checkbox && checkbox.checked) {
            card.classList.add('selected');
        }
    });

    document.getElementById('planningForm').addEventListener('submit', function(e) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            e.preventDefault();
            alert('❌ La date de fin doit être postérieure à la date de début');
            return false;
        }
        
        const checkboxes = document.querySelectorAll('input[name="menu_ids[]"]:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('❌ Veuillez sélectionner au moins un menu');
            return false;
        }
    });
</script>
</body>
</html>