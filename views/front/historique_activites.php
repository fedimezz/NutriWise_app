<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Historique des Activités - NutriWise</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        .page-header { text-align: center; padding: 3rem 2rem; }
        .page-title {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }
        .page-subtitle { color: #6c757d; }

        .history-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem 3rem;
        }

        @media (max-width: 1024px) {
            .history-layout { grid-template-columns: 1fr; }
        }

        .filters-panel {
            background: #fff;
            border-radius: 22px;
            padding: 2rem;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
            height: fit-content;
        }

        .filters-panel h3 {
            color: #2e7d32;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .filter-group { margin-bottom: 1.5rem; }
        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e2f;
            font-size: 0.9rem;
        }

        .filter-select, .filter-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #d9e3d7;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.9rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid #c8e6c9;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #2e7d32;
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #558b2f;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .timeline-container {
            background: #fff;
            border-radius: 22px;
            padding: 2rem;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .timeline-title {
            color: #2e7d32;
            font-size: 1.5rem;
            margin: 0;
        }

        .timeline-controls {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-filter {
            padding: 0.6rem 1rem;
            border: 1px solid #4caf50;
            background: white;
            color: #4caf50;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-filter:hover, .btn-filter.active {
            background: #4caf50;
            color: white;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e0e0e0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 1.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: #4caf50;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #4caf50;
        }

        .timeline-content {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 1.5rem;
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }

        .timeline-content.suivi { border-left-color: #2196f3; }
        .timeline-content.consultation { border-left-color: #ff9800; }

        .timeline-date {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .timeline-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e2f;
            margin-bottom: 0.5rem;
        }

        .timeline-description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .timeline-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: #888;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }

        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 { margin-bottom: 0.5rem; color: #2c3e2f; }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .page-btn {
            padding: 0.5rem 0.8rem;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .page-btn:hover, .page-btn.active {
            background: #4caf50;
            color: white;
            border-color: #4caf50;
        }

        @media (max-width: 768px) {
            .timeline-header { flex-direction: column; align-items: stretch; }
            .timeline-controls { justify-content: center; }
            .timeline-meta { flex-direction: column; gap: 0.3rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="page-header">
            <h1 class="page-title">📚 Historique des Activités</h1>
            <p class="page-subtitle">Votre timeline complète des consultations et suivis</p>
        </div>

        <div class="history-layout">
            <!-- Filters and Stats Panel -->
            <div class="filters-panel">
                <h3>📊 Statistiques</h3>

                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-value"><?= $stats['total_activities'] ?? 0 ?></span>
                        <span class="stat-label">Activités totales</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-value"><?= $stats['total_suivis'] ?? 0 ?></span>
                        <span class="stat-label">Suivis quotidiens</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-value"><?= $stats['total_consultations'] ?? 0 ?></span>
                        <span class="stat-label">Consultations</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-value"><?= $stats['objectifs_atteints'] ?? 0 ?></span>
                        <span class="stat-label">Objectifs atteints</span>
                    </div>
                </div>

                <h3>🔍 Filtres</h3>

                <div class="filter-group">
                    <label for="type-filter">Type d'activité</label>
                    <select id="type-filter" class="filter-select">
                        <option value="all">Toutes les activités</option>
                        <option value="suivi">Suivis quotidiens</option>
                        <option value="consultation">Consultations</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date-start">Date de début</label>
                    <input type="date" id="date-start" class="filter-input"
                           value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                </div>

                <div class="filter-group">
                    <label for="date-end">Date de fin</label>
                    <input type="date" id="date-end" class="filter-input"
                           value="<?= date('Y-m-d') ?>">
                </div>

                <button id="apply-filters" class="btn-submit" style="width: 100%; margin-top: 1rem;">
                    Appliquer les filtres
                </button>
            </div>

            <!-- Timeline -->
            <div class="timeline-container">
                <div class="timeline-header">
                    <h2 class="timeline-title">🕐 Chronologie des activités</h2>
                    <div class="timeline-controls">
                        <button class="btn-filter active" data-type="all">Tout</button>
                        <button class="btn-filter" data-type="suivi">Suivis</button>
                        <button class="btn-filter" data-type="consultation">Consultations</button>
                    </div>
                </div>

                <div id="timeline-content">
                    <?php if (!empty($activities)): ?>
                        <div class="timeline">
                            <?php foreach ($activities as $activity): ?>
                                <div class="timeline-item" data-type="<?= $activity['type_activite'] ?>">
                                    <div class="timeline-content <?= $activity['type_activite'] ?>">
                                        <div class="timeline-date">
                                            <?= date('d/m/Y', strtotime($activity['date_activite'])) ?>
                                            <?php if ($activity['type_activite'] === 'consultation' && $activity['statut'] === 'scheduled'): ?>
                                                <span style="color: #ff9800; font-weight: bold;">(À venir)</span>
                                            <?php elseif ($activity['type_activite'] === 'consultation' && $activity['statut'] === 'today'): ?>
                                                <span style="color: #4caf50; font-weight: bold;">(Aujourd'hui)</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="timeline-title">
                                            <?php if ($activity['type_activite'] === 'suivi'): ?>
                                                📊 <?= htmlspecialchars($activity['titre']) ?>
                                            <?php else: ?>
                                                👨‍⚕️ <?= htmlspecialchars($activity['titre']) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="timeline-description">
                                            <?= htmlspecialchars($activity['description']) ?>
                                        </div>
                                        <div class="timeline-meta">
                                            <?php if ($activity['type_activite'] === 'suivi' && $activity['poids']): ?>
                                                <div class="meta-item">⚖️ <?= $activity['poids'] ?> kg</div>
                                            <?php endif; ?>
                                            <?php if ($activity['eau_bue']): ?>
                                                <div class="meta-item">💧 <?= $activity['eau_bue'] ?> L</div>
                                            <?php endif; ?>
                                            <?php if ($activity['poids_cible']): ?>
                                                <div class="meta-item">🎯 Cible: <?= $activity['poids_cible'] ?> kg</div>
                                            <?php endif; ?>
                                            <div class="meta-item">🕐 <?= date('H:i', strtotime($activity['created_at'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=historique_activites&page_num=<?= $i ?>"
                                       class="page-btn <?= $i == $page ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <div class="icon">📭</div>
                            <h3>Aucune activité trouvée</h3>
                            <p>Commencez par ajouter un suivi quotidien ou planifier une consultation.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter functionality
        document.getElementById('apply-filters').addEventListener('click', function() {
            const type = document.getElementById('type-filter').value;
            const startDate = document.getElementById('date-start').value;
            const endDate = document.getElementById('date-end').value;

            // Update URL with filters
            const params = new URLSearchParams(window.location.search);
            params.set('type', type);
            params.set('start_date', startDate);
            params.set('end_date', endDate);

            window.location.search = params.toString();
        });

        // Quick filter buttons
        document.querySelectorAll('.btn-filter').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const type = this.dataset.type;
                document.getElementById('type-filter').value = type;
                document.getElementById('apply-filters').click();
            });
        });

        // Auto-refresh timeline content via AJAX (optional enhancement)
        function refreshTimeline() {
            // This could be implemented to dynamically update the timeline
            // without full page reload
        }
    </script>
</body>
</html>