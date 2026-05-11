<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Consultations - NutriWise Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <style>
        .header-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .header-actions h1 { margin: 0; }
        .header-actions p { margin: 0.35rem 0 0; color: #6c757d; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card { background: #fff; border: 1px solid #e6e9ef; border-radius: 16px; padding: 1.2rem 1.4rem; }
        .stat-card h3 { margin: 0 0 0.5rem; font-size: 0.95rem; color: #6c757d; }
        .stat-card p { margin: 0; font-size: 2rem; font-weight: 700; }
        .table-controls { display: flex; flex-wrap: wrap; gap: 0.75rem; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .table-controls input { width: 240px; padding: 0.8rem 1rem; border: 1px solid #ced4da; border-radius: 10px; }
        .table-controls button { padding: 0.8rem 1.2rem; border: none; border-radius: 10px; background: #2e7d32; color: #fff; cursor: pointer; }
        .card { background: #fff; border: 1px solid #e6e9ef; border-radius: 16px; padding: 1.5rem; }
        .users-table th, .users-table td { padding: 0.95rem 0.85rem; border-bottom: 1px solid #f0f2f5; text-align: left; }
        .users-table thead th { background: #f8fafc; }
        .users-table tbody tr:hover { background: #f9fbfd; }
        .btn-primary { padding: 0.85rem 1.2rem; border-radius: 10px; color: #fff; background: #2e7d32; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .sidebar .logo { font-size: 1.2rem; font-weight: 700; }
        .sidebar nav a { display: block; margin-bottom: 0.75rem; padding: 0.8rem 1rem; border-radius: 10px; color: #fff; text-decoration: none; }
        .sidebar nav a.active { background: #2e7d32; }
        .sidebar { background: #2e7d32; color: #fff; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">NutriWise Admin</div>
            <nav>
                <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="<?= ($page=='admin_users') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_suivis" class="<?= ($page=='admin_suivis') ? 'active' : '' ?>">📈 Suivis</a>
                <a href="index.php?page=admin_consultations" class="<?= ($page=='admin_consultations') ? 'active' : '' ?>">🩺 Consultations</a>
                <a href="index.php?page=admin_aliments" class="<?= ($page=='admin_aliments') ? 'active' : '' ?>">🥗 Aliments</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <div class="header-actions">
                <div>
                    <h1>Gestion des consultations</h1>
                    <p>Vue complète des consultations et des utilisateurs suivis.</p>
                </div>
                <div style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
                    <a href="index.php?page=admin_add_consultation" class="btn-primary">Ajouter consultation</a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div style="padding:1rem; background:#e8f5e9; color:#1b5e20; border-radius:10px; margin-bottom:1.5rem;">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div style="padding:1rem; background:#fdecea; color:#b42318; border-radius:10px; margin-bottom:1.5rem;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total des consultations</h3>
                    <p><?= $totalConsultations ?></p>
                </div>
                <div class="stat-card">
                    <h3>Utilisateurs actifs</h3>
                    <p><?= $totalUsers ?></p>
                </div>
                <div class="stat-card">
                    <h3>Suivis associés</h3>
                    <p><?= $totalLinkedSuivis ?></p>
                </div>
            </div>

            <div class="table-controls">
                <input type="text" id="searchInput" placeholder="Rechercher par utilisateur...">
                <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <button id="sortNameBtn" type="button">Trier par nom ↑</button>
                    <button id="exportPdfBtn" type="button">Exporter PDF</button>
                </div>
            </div>

            <div class="card">
                <table id="consultationTable" class="users-table" style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Date consultation</th>
                            <th>Utilisateur</th>
                            <th>Suivi associé</th>
                            <th>Remarque</th>
                            <th>Conseil</th>
                            <th>Poids cible</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultations as $consultation): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($consultation['date_consultation'])) ?></td>
                                <td><?= htmlspecialchars($consultation['prenom'] . ' ' . $consultation['nom']) ?><br><small style="color:#666;"><?= htmlspecialchars($consultation['email']) ?></small></td>
                                <td>
                                    <?php if (!empty($consultation['date_suivi'])): ?>
                                        <?= date('d/m/Y', strtotime($consultation['date_suivi'])) ?><br>
                                        <small style="color:#666;"><?= htmlspecialchars($consultation['poids_suivi']) ?> kg</small>
                                    <?php else: ?>
                                        <span style="color:#666;">Aucun suivi</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= nl2br(htmlspecialchars($consultation['remarque'])) ?></td>
                                <td><?= nl2br(htmlspecialchars($consultation['conseil'])) ?></td>
                                <td><?= $consultation['poids_cible'] !== null ? htmlspecialchars($consultation['poids_cible']) . ' kg' : '-' ?></td>
                                <td>
                                    <a href="index.php?page=admin_edit_consultation&id=<?= $consultation['id'] ?>" style="text-decoration:none; margin-right:0.6rem;">✏️</a>
                                    <a href="index.php?page=admin_delete_consultation&id=<?= $consultation['id'] ?>" style="text-decoration:none;" onclick="return confirm('Supprimer cette consultation ?');">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($consultations)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:2rem;">Aucune consultation trouvée.</td>
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
        const tableBody = document.querySelector('#consultationTable tbody');

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
                tr.innerHTML = '<td colspan="7" style="text-align:center; padding:2rem;">Aucune consultation trouvée.</td>';
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
            doc.text('Consultations', 14, 15);
            doc.autoTable({ html: '#consultationTable', startY: 22, headStyles: { fillColor: [46, 125, 50] }, styles: { fontSize: 8, cellPadding: 3 }, margin: { horizontal: 14 } });
            doc.save('consultations.pdf');
        }

        searchInput.addEventListener('input', filterRows);
        sortBtn.addEventListener('click', sortRows);
        exportBtn.addEventListener('click', exportPdf);
    </script>
</body>
</html>
