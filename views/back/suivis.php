<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Suivis - NutriWise Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <style>
        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge.grandit { background: #e3f2fd; color: #1976d2; }
        .badge.affine { background: #e8f5e9; color: #2e7d32; }
        .badge.maintien { background: #fff3e0; color: #e65100; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .table-controls { display: flex; gap: 0.8rem; flex-wrap: wrap; align-items: center; }
        .table-controls input { padding: 0.8rem 1rem; border: 1px solid #ccc; border-radius: 8px; min-width: 220px; }
        .table-controls button { padding: 0.75rem 1rem; border: none; border-radius: 8px; background: #2e7d32; color: white; cursor: pointer; }
        .table-controls button:hover { opacity: 0.95; }
        .users-section { margin-top: 2rem; }
        .users-section h2 { margin-bottom: 1rem; }
        .users-table th, .users-table td { padding: 0.8rem 0.75rem; border-bottom: 1px solid #e1e1e1; }
        .users-table th { background: #f8fafc; text-align: left; }
        .badge.active { background: #e8f5e9; color: #2e7d32; }
        .badge.inactive { background: #ffebee; color: #c62828; }
        .btn-primary { padding: 0.85rem 1.2rem; border-radius: 10px; color: #fff; background: #2e7d32; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
        .btn-primary:hover { opacity: 0.95; box-shadow: 0 4px 12px rgba(46,125,50,0.2); transform: translateY(-2px); }
        .btn-edit, .btn-delete { display: inline-block; padding: 0.4rem 0.7rem; border-radius: 6px; color: #fff; }
        .btn-edit { background: #1976d2; }
        .btn-delete { background: #d32f2f; }
        .sidebar .logo { font-size: 1.2rem; font-weight: 700; }
        .sidebar nav a { display: block; margin-bottom: 0.75rem; padding: 0.8rem 1rem; border-radius: 10px; color: #fff; text-decoration: none; }
        .sidebar nav a.active { background: #1b5e20; }
        .sidebar { background: #2e7d32; color: #fff; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="logo">NutriWise Admin</div>
            <nav>
                <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="<?= (strpos($page, 'admin_user') !== false || strpos($page, 'user') !== false && $page != 'profile') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments" class="<?= (strpos($page, 'aliment') !== false) ? 'active' : '' ?>">🥗 Aliments</a>
                <a href="index.php?page=admin_suivis" class="<?= (strpos($page, 'suivi') !== false) ? 'active' : '' ?>">📈 Suivis</a>
                <a href="#">📖 Recettes</a>
                <a href="#">📅 Plans</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <div class="header-actions">
                <div>
                    <h1>Gestion des Suivis</h1>
                    <p style="color: #6c757d;">Historique des suivis de tous les utilisateurs</p>
                </div>
                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
                    <a href="index.php?page=admin_add_suivi" class="btn-primary">Ajouter un suivi</a>
                </div>
            </div>

            <div class="table-controls">
                <input type="text" id="searchInput" placeholder="Rechercher par nom...">
                <button id="sortNameBtn" class="btn-primary" type="button">Trier par nom ↑</button>
                <button id="exportPdfBtn" class="btn-primary" type="button">Exporter PDF</button>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div style="padding: 1rem; background: #e8f5e9; color: #2e7d32; border-radius: 8px; margin-bottom: 2rem;">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div style="padding: 1rem; background: #fdecea; color: #b42318; border-radius: 8px; margin-bottom: 2rem;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid" style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom:1.5rem;">
                <div style="background:#fff; border:1px solid #e6e9ef; border-radius:16px; padding:1rem;">
                    <p style="margin:0; color:#6c757d;">Total des suivis</p>
                    <strong style="font-size:2rem;"><?= $totalSuivis ?? count($suivis) ?></strong>
                </div>
                <div style="background:#fff; border:1px solid #e6e9ef; border-radius:16px; padding:1rem;">
                    <p style="margin:0; color:#6c757d;">Utilisateurs suivis</p>
                    <strong style="font-size:2rem;"><?= $totalUsers ?? count($usersList) ?></strong>
                </div>
                <div style="background:#fff; border:1px solid #e6e9ef; border-radius:16px; padding:1rem;">
                    <p style="margin:0; color:#6c757d;">Calories moyennes</p>
                    <strong style="font-size:2rem;"><?= $averageCalories ?? 0 ?> kcal</strong>
                </div>
            </div>

            <div class="card">
                <table id="suiviTable" class="users-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Poids (kg)</th>
                            <th>État</th>
                            <th>Calories / jour</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($suivis as $s): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($s['date_suivi'])) ?></td>
                            <td><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?> <br><small style="color:#666;"><?= $s['email'] ?></small></td>
                            <td><?= $s['poids'] ?></td>
                            <td>
                                <?php 
                                    $class = "maintien";
                                    if(strpos($s['etat'], "Grandit") !== false) $class = "grandit";
                                    if(strpos($s['etat'], "affine") !== false) $class = "affine";
                                ?>
                                <span class="badge <?= $class ?>"><?= $s['etat'] ?></span>
                            </td>
                            <td><?= $s['calories_necessaires'] ?></td>
                            <td>
                                <a href="index.php?page=admin_edit_suivi&id=<?= $s['id'] ?>" class="btn-icon" style="text-decoration:none;">✏️</a>
                                <a href="index.php?page=admin_delete_suivi&id=<?= $s['id'] ?>" class="btn-icon" style="text-decoration:none;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce suivi ?');">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($suivis)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Aucun suivi trouvé.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script>
        const searchInput = document.getElementById('searchInput');
        const sortBtn = document.getElementById('sortNameBtn');
        const exportBtn = document.getElementById('exportPdfBtn');
        const tableBody = document.querySelector('#suiviTable tbody');

        let rows = Array.from(tableBody.querySelectorAll('tr')).map(tr => ({
            html: tr.innerHTML,
            name: tr.querySelector('td:nth-child(2)') ? tr.querySelector('td:nth-child(2)').innerText.trim().toLowerCase() : '',
            visible: true
        }));
        let sortAsc = true;

        function renderRows() {
            tableBody.innerHTML = '';
            const visibleRows = rows.filter(r => r.visible);
            if (visibleRows.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="6" style="text-align:center; padding:2rem;">Aucun suivi trouvé.</td>';
                tableBody.appendChild(tr);
                return;
            }
            visibleRows.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = r.html;
                tableBody.appendChild(tr);
            });
        }

        function filterRows() {
            const query = searchInput.value.trim().toLowerCase();
            rows.forEach(r => {
                r.visible = query === '' || r.name.includes(query);
            });
            renderRows();
        }

        function sortRows() {
            rows.sort((a, b) => a.name.localeCompare(b.name, 'fr', { sensitivity: 'base' }) * (sortAsc ? 1 : -1));
            sortAsc = !sortAsc;
            sortBtn.textContent = sortAsc ? 'Trier par nom ↑' : 'Trier par nom ↓';
            renderRows();
        }

        function exportPdf() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape' });
            doc.setFontSize(12);
            doc.text('Liste des Suivis', 14, 15);
            doc.autoTable({ html: '#suiviTable', startY: 22, headStyles: { fillColor: [56, 81, 255] }, styles: { fontSize: 8, cellPadding: 3 }, margin: { horizontal: 14 } });
            doc.save('suivis.pdf');
        }

        searchInput.addEventListener('input', filterRows);
        sortBtn.addEventListener('click', sortRows);
        exportBtn.addEventListener('click', exportPdf);
    </script>
</body>
</html>
