<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plannings - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/users.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #2E7D32;
            text-align: center;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2E7D32;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
        }

        .filters-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 0.75rem;
            border: 2px solid #C8E6C9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #2E7D32;
        }

        .filter-select {
            padding: 0.75rem;
            border: 2px solid #C8E6C9;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            background: white;
            transition: border-color 0.3s;
        }

        .filter-select:focus {
            outline: none;
            border-color: #2E7D32;
        }

        .filter-label {
            font-weight: 600;
            color: #2E7D32;
        }

        .sortable {
            cursor: pointer;
            user-select: none;
        }

        .sortable:hover {
            background: rgba(46, 125, 50, 0.05);
        }

        .sort-indicator {
            display: inline-block;
            margin-left: 0.5rem;
            opacity: 0.5;
        }

        .sort-indicator.active {
            opacity: 1;
        }
    </style>
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
                <h1>Gestion des plannings</h1>
                <button class="btn-add" onclick="location.href='index.php?page=admin_add_planning'">+ Ajouter un planning</button>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <p class="alert-message" style="color:green; margin-top:10px; font-weight:bold; background:#e8f5e9; padding:10px; border-radius:5px;">
                    <?= htmlspecialchars($_GET['success']) ?>
                </p>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <p class="alert-message" style="color:red; margin-top:10px; font-weight:bold; background:#ffebee; padding:10px; border-radius:5px;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </p>
            <?php endif; ?>

            <!-- Statistiques -->
            <?php if(!empty($plannings)): ?>
                <?php 
                    $totalPlannings = count($plannings);
                    $activePlannings = array_filter($plannings, fn($p) => $p['status'] === 'active');
                    $completedPlannings = array_filter($plannings, fn($p) => $p['status'] === 'completed');
                    $draftPlannings = array_filter($plannings, fn($p) => $p['status'] === 'draft');
                    $totalMenus = array_sum(array_column($plannings, 'menu_count'));
                ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📅</div>
                        <div class="stat-number"><?= $totalPlannings ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">✅</div>
                        <div class="stat-number"><?= count($activePlannings) ?></div>
                        <div class="stat-label">Actifs</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">✔️</div>
                        <div class="stat-number"><?= count($completedPlannings) ?></div>
                        <div class="stat-label">Complétés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📝</div>
                        <div class="stat-number"><?= count($draftPlannings) ?></div>
                        <div class="stat-label">Brouillons</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📚</div>
                        <div class="stat-number"><?= $totalMenus ?></div>
                        <div class="stat-label">Menus</div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filtres et Recherche -->
            <div class="filters-section">
                <input type="text" id="searchInput" class="search-input" placeholder="🔍 Rechercher par nom ou utilisateur...">
                <span class="filter-label">Statut :</span>
                <select id="statusFilter" class="filter-select">
                    <option value="">Tous les statuts</option>
                    <option value="draft">Brouillon</option>
                    <option value="active">Actif</option>
                    <option value="completed">Complété</option>
                </select>
                <button onclick="resetFilters()" style="padding: 0.75rem 1.5rem; background: #e0e0e0; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">Réinitialiser</button>
            </div>

            <!-- Tableau -->
            <div class="users-table-container">
                <table class="users-table" id="planningsTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable(0)">ID <span class="sort-indicator">⬍</span></th>
                            <th class="sortable" onclick="sortTable(1)">Nom <span class="sort-indicator">⬍</span></th>
                            <th class="sortable" onclick="sortTable(2)">Utilisateur <span class="sort-indicator">⬍</span></th>
                            <th class="sortable" onclick="sortTable(3)">Période <span class="sort-indicator">⬍</span></th>
                            <th class="sortable" onclick="sortTable(4)">Menus <span class="sort-indicator">⬍</span></th>
                            <th class="sortable" onclick="sortTable(5)">Statut <span class="sort-indicator">⬍</span></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if(empty($plannings)): ?>
                            <tr><td colspan="7" style="text-align:center; padding:20px;">Aucun planning trouvé.</td></tr>
                        <?php else: ?>
                            <?php foreach($plannings as $planning): ?>
                                <tr class="table-row" data-name="<?= htmlspecialchars(strtolower($planning['name'])) ?>" data-user="<?= htmlspecialchars(strtolower($planning['user_name'] ?: '')) ?>" data-status="<?= htmlspecialchars($planning['status']) ?>">
                                    <td>#<?= $planning['id'] ?></td>
                                    <td style="font-weight:600;"><?= htmlspecialchars($planning['name']) ?></td>
                                    <td><?= htmlspecialchars($planning['user_name'] ?: 'Non affecté') ?></td>
                                    <td><?= htmlspecialchars($planning['start_date']) ?> → <?= htmlspecialchars($planning['end_date']) ?></td>
                                    <td><?= intval($planning['menu_count']) ?></td>
                                    <td>
                                        <span style="display: inline-block; padding: 0.3rem 0.8rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600;
                                            <?php 
                                                if($planning['status'] === 'draft') echo 'background: #FFF9C4; color: #F57F17;';
                                                elseif($planning['status'] === 'active') echo 'background: #C8E6C9; color: #1B5E20;';
                                                else echo 'background: #B2DFDB; color: #004D40;';
                                            ?>
                                        ">
                                            <?= ucfirst(htmlspecialchars($planning['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="index.php?page=admin_planning_details&id=<?= $planning['id'] ?>" class="btn-icon" title="Voir">👁️</a>
                                        <a href="index.php?page=admin_edit_planning&id=<?= $planning['id'] ?>" class="btn-icon">✏️</a>
                                        <a href="index.php?page=admin_delete_planning&id=<?= $planning['id'] ?>" class="btn-icon" onclick="return confirm('Voulez-vous vraiment supprimer ce planning ?');">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem; padding: 1rem; background: #f5f5f5; border-radius: 8px; text-align: center; color: #666;">
                <strong id="resultCount"><?= count($plannings) ?></strong> planning(s) trouvé(s)
            </div>
        </main>
    </div>

    <script>
        let currentSort = { column: null, direction: 'asc' };

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#tableBody .table-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.dataset.name;
                const user = row.dataset.user;
                const status = row.dataset.status;

                const matchesSearch = name.includes(searchTerm) || user.includes(searchTerm);
                const matchesStatus = statusFilter === '' || status === statusFilter;

                if(matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('resultCount').textContent = visibleCount;

            if(visibleCount === 0) {
                let tbody = document.getElementById('tableBody');
                if(tbody.querySelector('.no-results')) tbody.querySelector('.no-results').remove();
                let tr = document.createElement('tr');
                tr.className = 'no-results';
                tr.innerHTML = '<td colspan="7" style="text-align:center; padding:20px; color:#999;">Aucun résultat trouvé.</td>';
                tbody.appendChild(tr);
            } else {
                let noResults = document.querySelector('.no-results');
                if(noResults) noResults.remove();
            }
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            filterTable();
        }

        function sortTable(columnIndex) {
            const table = document.getElementById('planningsTable');
            const tbody = document.getElementById('tableBody');
            const rows = Array.from(tbody.querySelectorAll('.table-row'));

            if(currentSort.column === columnIndex) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.direction = 'asc';
                currentSort.column = columnIndex;
            }

            rows.sort((a, b) => {
                let aVal = a.cells[columnIndex].textContent.trim();
                let bVal = b.cells[columnIndex].textContent.trim();

                // Tri numérique pour ID et Menus
                if(columnIndex === 0 || columnIndex === 4) {
                    aVal = parseInt(aVal) || 0;
                    bVal = parseInt(bVal) || 0;
                }

                if(aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
                if(aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            });

            rows.forEach(row => tbody.appendChild(row));

            // Mettre à jour les indicateurs de tri
            document.querySelectorAll('.sort-indicator').forEach((indicator, idx) => {
                indicator.classList.remove('active');
                if(idx === columnIndex) {
                    indicator.classList.add('active');
                    indicator.textContent = currentSort.direction === 'asc' ? '⬆️' : '⬇️';
                } else {
                    indicator.textContent = '⬍';
                }
            });
        }

        // Événements
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('statusFilter').addEventListener('change', filterTable);
    </script>
</body>
</html>
