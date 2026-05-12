<?php
// views/front/nutrition_plans.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Mes Plannings Nutritionnels - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8faf8; color: #1a2e1a; }
        .container { max-width: 1400px; margin: 0 auto; padding: 0 24px; }
        
        .page-header {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f0f7ed, #e8f3e4);
            border-radius: 32px;
            margin-bottom: 2rem;
        }
        .page-title {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .page-subtitle { color: #5a7a55; font-size: 1.1rem; }
        
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background: white;
            border-radius: 60px;
            max-width: 350px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .stat-number { font-size: 1.8rem; font-weight: 800; color: #2e7d32; }
        
        .view-toggle {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .view-btn {
            padding: 0.75rem 1.8rem;
            border: 2px solid #e2e8e0;
            background: white;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .view-btn.active {
            background: #2e7d32;
            color: white;
            border-color: #2e7d32;
        }
        
        .plannings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        
        .planning-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            position: relative;
        }
        .planning-card:hover { transform: translateY(-6px); box-shadow: 0 20px 35px rgba(0,0,0,0.12); }
        
        .planning-header {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            padding: 1.2rem;
            position: relative;
        }
        .planning-header h3 { font-size: 1.2rem; margin-bottom: 0.5rem; padding-right: 70px; }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.9rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .status-active { background: #fff3cd; color: #856404; }
        .status-draft { background: #e2e8e0; color: #5a7a55; }
        .status-completed { background: #d4edda; color: #155724; }
        
        .action-icons {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 8px;
        }
        .icon-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            color: white;
            font-size: 14px;
        }
        .icon-btn:hover { background: rgba(255,255,255,0.4); transform: scale(1.1); }
        .icon-btn.liked { background: #ff4757; }
        .icon-btn.favorite { background: #ffa502; }
        
        .planning-body { padding: 1.2rem; }
        .planning-dates { display: flex; align-items: center; gap: 8px; color: #6b8a66; font-size: 0.85rem; margin-bottom: 0.8rem; }
        
        .planning-stats {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
            padding: 0.8rem 0;
            border-top: 1px solid #e8f0e5;
            border-bottom: 1px solid #e8f0e5;
        }
        .stat-item { flex: 1; text-align: center; }
        .stat-value { font-size: 1.3rem; font-weight: 800; color: #2e7d32; }
        .stat-label { font-size: 0.7rem; color: #7c8e7a; }
        
        .planning-actions { display: flex; gap: 0.75rem; margin-top: 1rem; }
        .btn-view, .btn-schedule {
            flex: 1;
            padding: 0.7rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .btn-view { background: #3498db; color: white; }
        .btn-schedule { background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; }
        .btn-view:hover, .btn-schedule:hover { transform: translateY(-2px); }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            border-radius: 24px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e8dc;
            border-radius: 12px;
        }
        .modal-buttons { display: flex; gap: 15px; margin-top: 25px; }
        .btn-primary { flex: 1; background: #2e7d32; color: white; padding: 12px; border: none; border-radius: 12px; cursor: pointer; }
        .btn-secondary { flex: 1; background: #e0e8dc; color: #4a6741; padding: 12px; border: none; border-radius: 12px; cursor: pointer; }
        
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #27ae60;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            z-index: 1100;
            display: none;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        .pagination button {
            border: none;
            background: #f1f8ee;
            color: #2e7d32;
            padding: 0.75rem 1.2rem;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 600;
        }
        .pagination button.active, .pagination button:hover { background: #2e7d32; color: white; }
        
        .empty-state { text-align: center; padding: 4rem; background: white; border-radius: 32px; }
        .empty-state i { font-size: 4rem; color: #c8e6c9; margin-bottom: 1rem; }
        
        @media (max-width: 768px) {
            .plannings-grid { grid-template-columns: 1fr; }
            .page-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <?php include 'views/back/chatbot.php'; ?>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-calendar-alt"></i> Mes Plannings Nutritionnels</h1>
            <p class="page-subtitle">Likez, sauvegardez et planifiez vos programmes alimentaires</p>
        </div>

        <div class="view-toggle">
            <button class="view-btn active" onclick="window.location.href='index.php?page=nutrition_plans'">
                <i class="fas fa-list"></i> Vue Liste
            </button>
            <button class="view-btn" onclick="window.location.href='index.php?page=nutrition_plans_calendar'">
                <i class="fas fa-calendar-alt"></i> Vue Calendrier
            </button>
        </div>

        <div class="stats-bar">
            <div>
                <div class="stat-number" id="planningCount"><?= count($planningsData['plannings'] ?? []) ?></div>
                <div>Plannings</div>
            </div>
        </div>

        <div id="planningsGrid" class="plannings-grid">
            <?php if (empty($planningsData['plannings'])): ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Aucun planning trouvé</h3>
                    <p>Vous n'avez pas encore de planning nutritionnel</p>
                </div>
            <?php else: ?>
                <?php foreach ($planningsData['plannings'] as $planning): 
                    $isLiked = in_array($planning['id'], $likedPlannings ?? []);
                    $isFav = in_array($planning['id'], $favoritePlannings ?? []);
                ?>
                <div class="planning-card" id="planning-<?= $planning['id'] ?>">
                    <div class="planning-header">
                        <h3><i class="fas fa-clipboard-list"></i> <?= htmlspecialchars($planning['name']) ?></h3>
                        <div class="status-badge status-<?= $planning['status'] ?>">
                            <?php
                            $statusText = ['active' => '✅ Actif', 'draft' => '📝 Brouillon', 'completed' => '🏆 Terminé'];
                            echo $statusText[$planning['status']] ?? $planning['status'];
                            ?>
                        </div>
                        <div class="action-icons">
                            <button class="icon-btn like-btn <?= $isLiked ? 'liked' : '' ?>" data-id="<?= $planning['id'] ?>">
                                <i class="<?= $isLiked ? 'fas' : 'far' ?> fa-heart"></i>
                            </button>
                            <button class="icon-btn fav-btn <?= $isFav ? 'favorite' : '' ?>" data-id="<?= $planning['id'] ?>">
                                <i class="<?= $isFav ? 'fas' : 'far' ?> fa-star"></i>
                            </button>
                        </div>
                    </div>
                    <div class="planning-body">
                        <div class="planning-dates">
                            <i class="fas fa-calendar-alt"></i>
                            Du <?= date('d/m/Y', strtotime($planning['start_date'])) ?> au <?= date('d/m/Y', strtotime($planning['end_date'])) ?>
                        </div>
                        <?php if ($planning['description']): ?>
                            <p style="color: #7c8e7a; font-size: 0.85rem; margin-bottom: 0.8rem;">
                                <?= htmlspecialchars(substr($planning['description'], 0, 100)) ?>
                                <?= strlen($planning['description']) > 100 ? '...' : '' ?>
                            </p>
                        <?php endif; ?>
                        <div class="planning-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?= $planning['menus_count'] ?? 0 ?></div>
                                <div class="stat-label">Menus</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?= ceil((strtotime($planning['end_date']) - strtotime($planning['start_date'])) / 86400) + 1 ?></div>
                                <div class="stat-label">Jours</div>
                            </div>
                        </div>
                        <div class="planning-actions">
                            <a href="index.php?page=nutrition_plan_details&id=<?= $planning['id'] ?>" class="btn-view">
                                <i class="fas fa-eye"></i> Voir détails
                            </a>
                            <button class="btn-schedule" onclick="openScheduleModal(<?= $planning['id'] ?>, '<?= htmlspecialchars(addslashes($planning['name'])) ?>')">
                                <i class="fas fa-calendar-plus"></i> Planifier
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (($planningsData['totalPages'] ?? 1) > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $planningsData['totalPages']; $i++): ?>
                <button class="<?= $i == $planningsData['page'] ? 'active' : '' ?>" onclick="window.location.href='?page=nutrition_plans&page_num=<?= $i ?>'">
                    <?= $i ?>
                </button>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal Planification -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <h3 style="color: #2e7d32; margin-bottom: 20px;"><i class="fas fa-calendar-plus"></i> Planifier un planning</h3>
            <form id="scheduleForm">
                <input type="hidden" id="schedulePlanningId" name="planning_id">
                <div class="form-group">
                    <label>Date de début</label>
                    <input type="date" id="scheduleStartDate" name="start_date" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>Date de fin (optionnelle)</label>
                    <input type="date" id="scheduleEndDate" name="end_date">
                    <small>Laissez vide pour utiliser la durée originale</small>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="btn-primary">✅ Planifier</button>
                    <button type="button" class="btn-secondary" onclick="closeModal()">❌ Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.background = isError ? '#e74c3c' : '#27ae60';
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        async function toggleLike(planningId, btn) {
            try {
                const response = await fetch('index.php?page=toggle_planning_like', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'planning_id=' + planningId
                });
                const result = await response.json();
                if (result.success) {
                    if (result.liked) {
                        btn.classList.add('liked');
                        btn.innerHTML = '<i class="fas fa-heart"></i>';
                        showToast('❤️ Planning ajouté à vos likes !');
                    } else {
                        btn.classList.remove('liked');
                        btn.innerHTML = '<i class="far fa-heart"></i>';
                        showToast('💔 Like retiré');
                    }
                }
            } catch (error) { console.error(error); }
        }

        async function toggleFavorite(planningId, btn) {
            try {
                const response = await fetch('index.php?page=toggle_favorite', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'planning_id=' + planningId
                });
                const result = await response.json();
                if (result.success) {
                    if (result.favorite) {
                        btn.classList.add('favorite');
                        btn.innerHTML = '<i class="fas fa-star"></i>';
                        showToast('⭐ Planning ajouté aux favoris !');
                    } else {
                        btn.classList.remove('favorite');
                        btn.innerHTML = '<i class="far fa-star"></i>';
                        showToast('⭐ Favori retiré');
                    }
                }
            } catch (error) { console.error(error); }
        }

        function openScheduleModal(planningId, planningName) {
            document.getElementById('schedulePlanningId').value = planningId;
            document.getElementById('scheduleStartDate').value = '';
            document.getElementById('scheduleEndDate').value = '';
            document.getElementById('scheduleModal').style.display = 'flex';
            showToast(`📅 Planification de : ${planningName}`);
        }

        function closeModal() {
            document.getElementById('scheduleModal').style.display = 'none';
        }

        document.getElementById('scheduleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const planningId = document.getElementById('schedulePlanningId').value;
            const startDate = document.getElementById('scheduleStartDate').value;
            const endDate = document.getElementById('scheduleEndDate').value;

            if (!startDate) {
                showToast('Veuillez sélectionner une date de début', true);
                return;
            }

            try {
                const response = await fetch('index.php?page=schedule_planning', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `planning_id=${planningId}&start_date=${startDate}&end_date=${endDate}`
                });
                const result = await response.json();
                if (result.success) {
                    showToast('✅ ' + result.message);
                    closeModal();
                } else {
                    showToast('❌ ' + result.message, true);
                }
            } catch (error) {
                showToast('Erreur lors de la planification', true);
            }
        });

        window.onclick = function(event) {
            const modal = document.getElementById('scheduleModal');
            if (event.target === modal) closeModal();
        }

        // Attacher les événements
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleLike(btn.dataset.id, btn);
            });
        });

        document.querySelectorAll('.fav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleFavorite(btn.dataset.id, btn);
            });
        });
    </script>
</body>
</html>