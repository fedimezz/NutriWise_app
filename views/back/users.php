<?php
// views/back/users.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - NutriWise</title>
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
            <a href="index.php?page=admin_users" class="active">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes">📖 Recettes</a>
            <a href="index.php?page=admin_plannings">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1>Gestion des utilisateurs</h1>
            <button class="btn-add" onclick="location.href='index.php?page=admin_add_user'">
                + Ajouter un utilisateur
            </button>
        </header>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-success">✓ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="filters">
            <input type="text" id="emailSearchInput" class="search-input" placeholder="Rechercher par email...">
            <select class="filter-select" id="statusFilter">
                <option value="all">Tous les statuts</option>
                <option value="Actif">Actif</option>
                <option value="Inactif">Inactif</option>
            </select>
        </div>

        <button class="btn-delete" style="margin-bottom:15px; padding:8px 16px;" onclick="deleteSelected()">
            🗑️ Supprimer sélection
        </button>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>IMC</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usersList as $user): ?>
                        <?php if(isset($user['role']) && $user['role'] === 'owner') continue; ?>
                        <tr>
                            <td><input type="checkbox" class="user-checkbox" value="<?= $user['id'] ?>"></td>
                            <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>
                            <td><?= $user['imc'] ? $user['imc'] : 'N/A' ?></td>
                            <td>
                                <span class="badge <?= ($user['statut'] ?? 'Actif') == 'Actif' ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= $user['statut'] ?? 'Actif' ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="index.php?page=admin_edit_user&id=<?= $user['id'] ?>" class="btn-edit">✏️</a>
                                <a href="index.php?page=admin_delete_user&id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
const searchInput = document.getElementById('emailSearchInput');
const statusFilter = document.getElementById('statusFilter');
const rows = document.querySelectorAll('.admin-table tbody tr');

function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedStatus = statusFilter.value;

    rows.forEach(row => {
        const email = row.children[2]?.textContent.toLowerCase() || '';
        const status = row.children[5]?.textContent.trim() || '';
        const matchSearch = searchTerm === '' || email.includes(searchTerm);
        const matchStatus = selectedStatus === 'all' || status === selectedStatus;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

searchInput.addEventListener('input', filterTable);
statusFilter.addEventListener('change', filterTable);

document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
});

function deleteSelected() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert("Aucun utilisateur sélectionné");
        return;
    }
    if (!confirm("Supprimer les utilisateurs sélectionnés ?")) return;

    fetch('index.php?page=admin_delete_users_bulk', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ ids: selected })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) location.reload();
        else alert("Erreur suppression");
    });
}
</script>
</body>
</html>