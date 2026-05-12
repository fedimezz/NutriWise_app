<?php
// views/back/recettes.php

function getAdminRecetteImageUrl($image) {
    if (empty($image)) return null;
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    }
    return 'views/assets/uploads/recettes/' . $image;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des recettes - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="views/assets/css/admin-global.css">

    <style>
        .recipe-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .image-placeholder {
            width: 50px;
            height: 50px;
            background: #e8f5e9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .badge-easy {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: inline-block;
        }
        .badge-medium {
            background: #fff3e0;
            color: #ef6c00;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: inline-block;
        }
        .badge-hard {
            background: #ffebee;
            color: #c62828;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            display: inline-block;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2e7d32;
        }
        .stat-label {
            color: #666;
            font-size: 0.8rem;
        }
        .btn-view {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.75rem;
            display: inline-block;
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
            <a href="index.php?page=admin_recettes" class="active">📖 Recettes</a>
            <a href="index.php?page=admin_plannings">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1>📖 Gestion des recettes</h1>
            <button class="btn-add" onclick="location.href='index.php?page=admin_add_recette'">
                + Ajouter une recette
            </button>
        </header>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-success">✓ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalRecettes ?? 0 ?></div>
                <div class="stat-label">Total recettes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalIngredients ?? 0 ?></div>
                <div class="stat-label">Ingrédients utilisés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalCategories ?? 0 ?></div>
                <div class="stat-label">Catégories</div>
            </div>
        </div>

        <div class="filters">
            <input type="text" id="searchInput" class="search-input"
                   placeholder="Rechercher par nom..."
                   value="<?= htmlspecialchars($search ?? '') ?>">
            <select id="categorieFilter" class="filter-select">
                <option value="all">Toutes les catégories</option>
                <option value="Petit-déjeuner">🍳 Petit-déjeuner</option>
                <option value="Entrée">🥗 Entrée</option>
                <option value="Plat principal">🍽️ Plat principal</option>
                <option value="Dessert">🍰 Dessert</option>
            </select>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Difficulté</th>
                        <th>Temps</th>
                        <th>Portions</th>
                        <th>Vues</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recettesList)): ?>
                        <?php foreach ($recettesList as $recette): ?>
                            <tr data-categorie="<?= htmlspecialchars($recette['categorie'] ?? '') ?>">
                                <td>
                                    <?php $img = getAdminRecetteImageUrl($recette['image'] ?? null); ?>
                                    <?php if ($img): ?>
                                        <img src="<?= htmlspecialchars($img) ?>" class="recipe-image">
                                    <?php else: ?>
                                        <div class="image-placeholder">🍳</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($recette['nom']) ?></strong>
                                    <?php if (!empty($recette['description'])): ?>
                                        <br><small><?= htmlspecialchars(substr($recette['description'], 0, 50)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($recette['categorie'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badgeClass = 'badge-easy';
                                    $badgeText = 'Facile';
                                    if (($recette['difficulte'] ?? '') == 'Moyen') {
                                        $badgeClass = 'badge-medium';
                                        $badgeText = 'Moyen';
                                    } elseif (($recette['difficulte'] ?? '') == 'Difficile') {
                                        $badgeClass = 'badge-hard';
                                        $badgeText = 'Difficile';
                                    }
                                    ?>
                                    <span class="<?= $badgeClass ?>"><?= $badgeText ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $temps = ($recette['temps_preparation'] ?? 0) + ($recette['temps_cuisson'] ?? 0);
                                    echo $temps > 0 ? $temps . ' min' : '—';
                                    ?>
                                </td>
                                <td><?= $recette['portions'] ?? '—' ?></td>
                                <td><?= number_format($recette['views'] ?? 0) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="index.php?page=admin_edit_recette&id=<?= $recette['id'] ?>" class="btn-edit">✏️</a>
                                        <a href="index.php?page=admin_delete_recette&id=<?= $recette['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer ?')">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;">Aucune recette trouvée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $currentPage): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=admin_recettes&page_num=<?= $i ?>&search=<?= urlencode($search ?? '') ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const categorieFilter = document.getElementById('categorieFilter');
    const rows = document.querySelectorAll('.admin-table tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategorie = categorieFilter.value;

        rows.forEach(row => {
            const nom = row.cells[1]?.textContent.toLowerCase() || '';
            const categorie = row.dataset.categorie || '';
            const matchSearch = searchTerm === '' || nom.includes(searchTerm);
            const matchCategorie = selectedCategorie === 'all' || categorie === selectedCategorie;
            row.style.display = (matchSearch && matchCategorie) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    categorieFilter.addEventListener('change', filterTable);
</script>
</body>
</html>