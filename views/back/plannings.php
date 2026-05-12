<?php
// views/back/plannings.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des plannings - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="views/assets/css/admin-global.css">

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
            <h1>📋 Gestion des plannings nutritionnels</h1>
            <button class="btn-add" onclick="location.href='index.php?page=admin_add_planning'">
                + Ajouter un planning
            </button>
        </header>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-success">✓ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $planningsData['total'] ?? 0 ?></div>
                <div class="stat-label">Total plannings</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $planningsData['active'] ?? 0 ?></div>
                <div class="stat-label">✅ Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $planningsData['draft'] ?? 0 ?></div>
                <div class="stat-label">📝 Brouillons</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $planningsData['completed'] ?? 0 ?></div>
                <div class="stat-label">🏆 Terminés</div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters">
            <input type="text" id="searchInput" class="search-input" placeholder="Rechercher par nom...">
            <select id="statusFilter" class="filter-select">
                <option value="all">Tous les statuts</option>
                <option value="active">✅ Actifs</option>
                <option value="draft">📝 Brouillons</option>
                <option value="completed">🏆 Terminés</option>
            </select>
            <select id="userFilter" class="filter-select">
                <option value="all">Tous les utilisateurs</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <button id="searchBtn" class="btn-save" style="padding:10px 20px;">🔍 Filtrer</button>
            <a href="index.php?page=admin_plannings" class="btn-cancel">🔄 Réinitialiser</a>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Utilisateur</th>
                        <th>Dates</th>
                        <th>Statut</th>
                        <th>Menus</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($planningsData['plannings'])): ?>
                        <?php foreach ($planningsData['plannings'] as $planning): 
                            $statusClass = $planning['status'] == 'active' ? 'badge-active' : ($planning['status'] == 'draft' ? 'badge-draft' : 'badge-completed');
                            $statusText = $planning['status'] == 'active' ? '✅ Actif' : ($planning['status'] == 'draft' ? '📝 Brouillon' : '🏆 Terminé');
                        ?>
                            <tr data-status="<?= $planning['status'] ?>" data-user="<?= $planning['user_id'] ?>" data-name="<?= strtolower($planning['name']) ?>">
                                <td>
                                    <strong><?= htmlspecialchars($planning['name']) ?></strong>
                                    <?php if(!empty($planning['description'])): ?>
                                        <br><small><?= htmlspecialchars(substr($planning['description'], 0, 50)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($planning['prenom'] . ' ' . $planning['nom']) ?></td>
                                <td>
                                    <small>
                                        📅 <?= date('d/m/Y', strtotime($planning['start_date'])) ?><br>
                                        → <?= date('d/m/Y', strtotime($planning['end_date'])) ?>
                                    </small>
                                </td>
                                <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                                <td><?= $planning['menus_count'] ?? 0 ?></td>
                                <td class="actions">
                                    <a href="index.php?page=admin_planning_details&id=<?= $planning['id'] ?>" class="btn-view">👁️</a>
                                    <a href="index.php?page=admin_edit_planning&id=<?= $planning['id'] ?>" class="btn-edit">✏️</a>
                                    <form method="POST" action="index.php?page=admin_delete_planning" style="display:inline;" onsubmit="return confirm('Supprimer ce planning ?')" novalidate>
                                        <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="id" value="<?= $planning['id'] ?>">
                                        <button type="submit" class="btn-delete">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:40px;">Aucun planning trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($planningsData['totalPages']) && $planningsData['totalPages'] > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $planningsData['totalPages']; $i++): ?>
                <?php if ($i == $planningsData['page']): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=admin_plannings&page_num=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const userFilter = document.getElementById('userFilter');
const searchBtn = document.getElementById('searchBtn');
const rows = document.querySelectorAll('.admin-table tbody tr');

function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedStatus = statusFilter.value;
    const selectedUser = userFilter.value;

    rows.forEach(row => {
        const name = row.dataset.name || '';
        const status = row.dataset.status || '';
        const userId = row.dataset.user || '';
        const matchSearch = searchTerm === '' || name.includes(searchTerm);
        const matchStatus = selectedStatus === 'all' || status === selectedStatus;
        const matchUser = selectedUser === 'all' || userId === selectedUser;
        row.style.display = (matchSearch && matchStatus && matchUser) ? '' : 'none';
    });
}

searchBtn.addEventListener('click', filterTable);
statusFilter.addEventListener('change', filterTable);
userFilter.addEventListener('change', filterTable);
searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') filterTable();
});
</script>
</body>
</html>