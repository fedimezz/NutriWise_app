<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/users.css">
</head>
<body>
<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>
        <nav>
            <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Tableau de bord</a>
            <a href="index.php?page=admin_users" class="<?= ($page=='admin_users') ? 'active' : '' ?>">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments" class="<?= ($page=='admin_aliments') ? 'active' : '' ?>">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes" class="<?= ($page=='admin_recettes') ? 'active' : '' ?>">📖 Recettes</a>
            <a href="index.php?page=admin_plans" class="<?= ($page=='admin_plans') ? 'active' : '' ?>">📅 Plans</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <header>
            <h1>Gestion des utilisateurs</h1>
            <button class="btn-add" onclick="location.href='index.php?page=admin_add_user'">
                + Ajouter un utilisateur
            </button>
        </header>

        <!-- SUCCESS MESSAGE -->
        <?php if(isset($_GET['success'])): ?>
            <p style="color:green; margin-top:10px; font-weight:bold;">
                <?= htmlspecialchars($_GET['success']) ?>
            </p>
        <?php endif; ?>

        <!-- FILTERS -->
        <div class="filters">
            <input type="text" id="emailSearchInput"
                   placeholder="Rechercher par email..."
                   class="search-input">

            <select class="filter-select">
                <option>Tous les statuts</option>
                <option>Actif</option>
                <option>Inactif</option>
            </select>
        </div>

        <!-- BULK ACTIONS -->
        <button class="btn-delete" style="margin:10px 0;" onclick="deleteSelected()">
            🗑️ Supprimer sélection
        </button>

        <!-- TABLE -->
        <div class="users-table-container">
            <table class="users-table">

                <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
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

                    <!-- 🚫 HIDE OWNER -->
                    <?php if(isset($user['role']) && $user['role'] === 'owner') continue; ?>

                    <tr>
                        <td>
                            <input type="checkbox"
                                   class="user-checkbox"
                                   value="<?= $user['id'] ?>">
                        </td>

                        <td>
                            <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                        </td>

                        <td><?= htmlspecialchars($user['email']) ?></td>

                        <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>

                        <td><?= $user['imc'] ? $user['imc'] : 'N/A' ?></td>

                        <td>
                            <span class="badge <?= $user['statut'] == 'Actif' ? 'active' : 'inactive' ?>">
                                <?= $user['statut'] ?>
                            </span>
                        </td>

                        <td>
                            <a href="index.php?page=admin_edit_user&id=<?= $user['id'] ?>"
                               class="btn-edit">✏️</a>

                            <a href="index.php?page=admin_delete_user&id=<?= $user['id'] ?>"
                               class="btn-delete"
                               onclick="return confirm('Sûr de vouloir supprimer ?');">
                                🗑️
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </main>
</div>

<!-- JS -->
<script>
/* SEARCH BY EMAIL */
(function () {
    const input = document.getElementById('emailSearchInput');
    const rows = Array.from(document.querySelectorAll('.users-table tbody tr'));

    input.addEventListener('input', function () {
        const q = input.value.toLowerCase().trim();

        rows.forEach(row => {
            const email = row.children[2]?.textContent.toLowerCase() || '';
            row.style.display = (!q || email.includes(q)) ? '' : 'none';
        });
    });
})();

/* SELECT ALL */
document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});

/* BULK DELETE */
function deleteSelected() {
    const selected = Array.from(document.querySelectorAll('.user-checkbox:checked'))
        .map(cb => cb.value);

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