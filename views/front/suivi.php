<?php
// views/front/suivi.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Suivi - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/front-global.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            border-radius: 32px;
            padding: 45px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .score-box {
            width: 190px;
            height: 190px;
            border-radius: 35px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .score-box h2 { font-size: 4rem; font-weight: 700; }
        .actions { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 35px; }
        .action-card {
            background: white;
            border-radius: 28px;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .action-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .action-left { display: flex; align-items: center; gap: 18px; }
        .action-icon { width: 70px; height: 70px; border-radius: 22px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; }
        .green { background: #4caf50; }
        .red { background: #ef4444; }
        .blue { background: #3b82f6; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 35px; }
        .card {
            background: white;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .card-value { font-size: 2.5rem; font-weight: 700; color: #2e7d32; }
        .progress { width: 100%; height: 10px; background: #e2e8f0; border-radius: 50px; overflow: hidden; margin-top: 20px; }
        .progress div { height: 100%; background: #4caf50; border-radius: 50px; transition: width 0.5s ease; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .meal-item, .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .meal-item:last-child, .activity-item:last-child { border-bottom: none; }
        .delete-btn {
            border: none;
            background: #fee2e2;
            color: #ef4444;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
        }
        .delete-btn:hover { background: #ef4444; color: white; transform: scale(1.05); }
        .section-title { font-size: 1.3rem; margin-bottom: 20px; color: #1a3a1a; }
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .modal-content {
            width: 95%;
            max-width: 500px;
            background: white;
            border-radius: 30px;
            padding: 30px;
            animation: modalSlideIn 0.3s ease;
        }
        @keyframes modalSlideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .close {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #64748b;
            transition: all 0.3s;
        }
        .close:hover { color: #ef4444; transform: rotate(90deg); }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a3a1a;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 14px;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
        }
        .submit-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: #2e7d32;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .submit-btn:hover { background: #1b5e20; transform: translateY(-2px); }
        .toast {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #4caf50;
            color: white;
            padding: 15px 25px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 99999;
        }
        @media (max-width: 1100px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .actions { grid-template-columns: 1fr; }
            .grid { grid-template-columns: 1fr; }
            .hero { flex-direction: column; text-align: center; }
        }
        @media (max-width: 768px) {
            .stats { grid-template-columns: 1fr; }
            .score-box { width: 150px; height: 150px; }
            .score-box h2 { font-size: 3rem; }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/partials/navbar.php'; ?>

    <div class="container">
        <!-- HERO SECTION -->
        <div class="hero">
            <div>
                <h1>👋 Bonjour <?= htmlspecialchars($user['prenom'] ?? $_SESSION['user_name'] ?? 'Utilisateur') ?></h1>
                <p>Suivez votre santé aujourd'hui</p>
            </div>
            <div class="score-box">
                <span>Score</span>
                <h2 id="dailyScore"><?= $dailyScore ?? 0 ?></h2>
                <span>/100</span>
            </div>
        </div>

        <!-- ACTIONS CARDS -->
        <div class="actions">
            <div class="action-card" onclick="openModal('mealModal')">
                <div class="action-left">
                    <div class="action-icon green"><i class="fas fa-utensils"></i></div>
                    <div><h3>Ajouter repas</h3><p>Ajoutez vos aliments</p></div>
                </div>
                <i class="fas fa-plus"></i>
            </div>

            <div class="action-card" onclick="openModal('activityModal')">
                <div class="action-left">
                    <div class="action-icon red"><i class="fas fa-running"></i></div>
                    <div><h3>Ajouter activité</h3><p>Ajoutez votre sport</p></div>
                </div>
                <i class="fas fa-plus"></i>
            </div>

            <div class="action-card" onclick="addWater()">
                <div class="action-left">
                    <div class="action-icon blue"><i class="fas fa-tint"></i></div>
                    <div><h3>Ajouter eau</h3><p>+1 verre d'eau</p></div>
                </div>
                <i class="fas fa-plus"></i>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="stats">
            <div class="card">
                <div class="card-value" id="calories"><?= $totals['calories_consumed'] ?? 0 ?></div>
                <div>Calories consommées</div>
                <div class="progress">
                    <div style="width: <?= min(100, (($totals['calories_consumed'] ?? 0) / ($user['daily_calories_needs'] ?? 2000)) * 100) ?>%"></div>
                </div>
                <small>Objectif: <?= $user['daily_calories_needs'] ?? 2000 ?> kcal</small>
            </div>

            <div class="card">
                <div class="card-value" id="burned"><?= $totals['calories_burned'] ?? 0 ?></div>
                <div>Calories brûlées</div>
                <div class="progress">
                    <div style="width: <?= min(100, (($totals['calories_burned'] ?? 0) / 500) * 100) ?>%"></div>
                </div>
                <small>Objectif: 500 kcal</small>
            </div>

            <div class="card">
                <div class="card-value" id="water"><?= $totals['water'] ?? 0 ?></div>
                <div>Hydratation</div>
                <div class="progress">
                    <div style="width: <?= min(100, (($totals['water'] ?? 0) / ($user['water_goal'] ?? 8)) * 100) ?>%"></div>
                </div>
                <small>Objectif: <?= $user['water_goal'] ?? 8 ?> verres</small>
            </div>

            <div class="card">
                <div class="card-value"><?= $totals['sleep'] ?? 0 ?>h</div>
                <div>Sommeil</div>
                <div class="progress">
                    <div style="width: <?= min(100, (($totals['sleep'] ?? 0) / 8) * 100) ?>%"></div>
                </div>
                <small>Objectif: 8h</small>
            </div>
        </div>

        <!-- MEALS & ACTIVITIES GRID -->
        <div class="grid">
            <!-- MEALS SECTION -->
            <div class="card">
                <h2 class="section-title">🍽️ Repas</h2>
                <div id="mealsContainer">
                    <?php if(empty($meals)): ?>
                        <p style="color: #6b8a66; text-align: center; padding: 20px;">Aucun repas ajouté aujourd'hui</p>
                    <?php else: ?>
                        <?php foreach($meals as $meal): ?>
                            <div class="meal-item">
                                <div>
                                    <strong><?= htmlspecialchars($meal['food_name']) ?></strong>
                                    <p><?= $meal['calories'] ?> kcal</p>
                                </div>
                                <button class="delete-btn" onclick="deleteMeal(<?= $meal['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ACTIVITIES SECTION -->
            <div class="card">
                <h2 class="section-title">🏃 Activités</h2>
                <div id="activitiesContainer">
                    <?php if(empty($activities)): ?>
                        <p style="color: #6b8a66; text-align: center; padding: 20px;">Aucune activité ajoutée aujourd'hui</p>
                    <?php else: ?>
                        <?php foreach($activities as $activity): ?>
                            <div class="activity-item">
                                <div>
                                    <strong><?= htmlspecialchars($activity['activity_type']) ?></strong>
                                    <p><?= $activity['duration'] ?> min - <?= $activity['calories_burned'] ?> kcal</p>
                                </div>
                                <button class="delete-btn" onclick="deleteActivity(<?= $activity['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUT REPAS -->
    <div class="modal" id="mealModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-utensils"></i> Ajouter un repas</h2>
                <button class="close" onclick="closeModal('mealModal')">&times;</button>
            </div>
            <form id="mealForm" novalidate>
                <div class="form-group">
                    <label>🍽️ Nom de l'aliment</label>
                    <input type="text" name="food_name" placeholder="Ex: Poulet grillé, Riz complet..." required>
                </div>
                <div class="form-group">
                    <label>🔥 Calories</label>
                    <input type="number" name="calories" placeholder="Ex: 450" required>
                </div>
                <div class="form-group">
                    <label>💪 Protéines (g)</label>
                    <input type="number" name="protein_g" step="0.1" value="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label>🍞 Glucides (g)</label>
                    <input type="number" name="carbs_g" step="0.1" value="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label>🧈 Lipides (g)</label>
                    <input type="number" name="fat_g" step="0.1" value="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label>⏰ Type de repas</label>
                    <select name="meal_type">
                        <option>Petit-déjeuner</option>
                        <option>Déjeuner</option>
                        <option>Dîner</option>
                        <option>Collation</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">✅ Ajouter le repas</button>
            </form>
        </div>
    </div>

    <!-- MODAL AJOUT ACTIVITE -->
    <div class="modal" id="activityModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-running"></i> Ajouter une activité</h2>
                <button class="close" onclick="closeModal('activityModal')">&times;</button>
            </div>
            <form id="activityForm" novalidate>
                <div class="form-group">
                    <label>🏃 Type d'activité</label>
                    <select name="activity_type">
                        <option>Marche</option>
                        <option>Course</option>
                        <option>Vélo</option>
                        <option>Natation</option>
                        <option>Musculation</option>
                        <option>Yoga</option>
                        <option>Cardio</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>⏱️ Durée (minutes)</label>
                    <input type="number" name="duration" placeholder="Ex: 30" required>
                </div>
                <div class="form-group">
                    <label>🔥 Calories brûlées</label>
                    <input type="number" name="calories_burned" placeholder="Ex: 200" required>
                </div>
                <button type="submit" class="submit-btn">✅ Ajouter l'activité</button>
            </form>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div class="toast" id="toast"></div>

    <script>
        // Modal functions
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Close modal on outside click
        window.onclick = function(event) {
            document.querySelectorAll('.modal').forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Toast notification
        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<i class="fas fa-${isError ? 'exclamation-triangle' : 'check-circle'}"></i> ${message}`;
            toast.style.background = isError ? '#ef4444' : '#4caf50';
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // Add Meal
        document.getElementById('mealForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('index.php?page=add_meal_suivi', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Repas ajouté avec succès !');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('Erreur lors de l\'ajout', true);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Erreur de connexion', true);
            }
        });

        // Add Activity
        document.getElementById('activityForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('index.php?page=add_activity_suivi', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Activité ajoutée avec succès !');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('Erreur lors de l\'ajout', true);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Erreur de connexion', true);
            }
        });

        // Add Water
        async function addWater() {
            try {
                const response = await fetch('index.php?page=add_water_suivi', {
                    method: 'POST'
                });
                const result = await response.json();
                if (result.success) {
                    showToast('+1 verre d\'eau ajouté !');
                    document.getElementById('water').innerText = result.data.totals.water;
                    document.getElementById('dailyScore').innerText = result.data.dailyScore;
                    const waterGoal = <?= $user['water_goal'] ?? 8 ?>;
                    const waterValue = result.data.totals.water;
                    const waterPercent = Math.min(100, (waterValue / waterGoal) * 100);
                    const progressBar = document.querySelector('.stats .card:last-child .progress div');
                    if (progressBar) progressBar.style.width = waterPercent + '%';
                } else {
                    showToast('Erreur lors de l\'ajout', true);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Erreur de connexion', true);
            }
        }

        // Delete Meal
        async function deleteMeal(id) {
            if (!confirm('Supprimer ce repas ?')) return;
            try {
                const response = await fetch('index.php?page=delete_meal_suivi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Repas supprimé');
                    location.reload();
                } else {
                    showToast('Erreur lors de la suppression', true);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Erreur de connexion', true);
            }
        }

        // Delete Activity
        async function deleteActivity(id) {
            if (!confirm('Supprimer cette activité ?')) return;
            try {
                const response = await fetch('index.php?page=delete_activity_suivi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id
                });
                const result = await response.json();
                if (result.success) {
                    showToast('Activité supprimée');
                    location.reload();
                } else {
                    showToast('Erreur lors de la suppression', true);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Erreur de connexion', true);
            }
        }
    </script>
</body>
</html>