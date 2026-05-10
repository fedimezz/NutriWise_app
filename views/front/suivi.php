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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8faf8;
            color: #1a2e1a;
        }

        /* ================= PAGE STYLES ================= */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 24px;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            border-radius: 32px;
            padding: 45px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            box-shadow: 0 10px 30px rgba(46,125,50,0.2);
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.95;
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

        .score-box span {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .score-box h2 {
            font-size: 4rem;
            font-weight: 700;
        }

        /* ACTIONS */
        .actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 35px;
        }

        .action-card {
            background: white;
            border-radius: 28px;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .action-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .action-icon {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .green { background: #4caf50; }
        .red { background: #ef4444; }
        .blue { background: #3b82f6; }

        /* STATS */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 35px;
        }

        .card {
            background: white;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-title {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2e7d32;
        }

        .progress {
            width: 100%;
            height: 10px;
            background: #e2e8f0;
            border-radius: 50px;
            overflow: hidden;
            margin-top: 20px;
        }

        .progress div {
            height: 100%;
            border-radius: 50px;
            transition: width 0.5s ease;
        }

        /* TABLES */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .meal-item, .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .meal-item:last-child, .activity-item:last-child {
            border-bottom: none;
        }

        .delete-btn {
            border: none;
            background: #fee2e2;
            color: #ef4444;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.05);
        }

        .section-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #1a3a1a;
        }

        /* MODAL */
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
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
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

        .close:hover {
            color: #ef4444;
            transform: rotate(90deg);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a3a1a;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
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
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #1b5e20;
            transform: translateY(-2px);
        }

        /* TOAST */
        .toast {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: white;
            padding: 15px 25px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 99999;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
            .actions {
                grid-template-columns: 1fr;
            }
            .grid {
                grid-template-columns: 1fr;
            }
            .hero {
                flex-direction: column;
                gap: 30px;
                text-align: center;
            }
        }

        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
            }
            .hero h1 {
                font-size: 1.8rem;
            }
            .score-box {
                width: 150px;
                height: 150px;
            }
            .score-box h2 {
                font-size: 3rem;
            }
        }
    </style>
</head>

<body>

<!-- ================= INCLUDE NAVBAR ================= -->
<?php include_once __DIR__ . '/partials/navbar.php'; ?>

<!-- ================= PAGE CONTENT ================= -->
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
                <div class="action-icon green">
                    <i class="fas fa-utensils"></i>
                </div>
                <div>
                    <h3>Ajouter repas</h3>
                    <p>Ajoutez vos aliments</p>
                </div>
            </div>
            <i class="fas fa-plus"></i>
        </div>

        <div class="action-card" onclick="openModal('activityModal')">
            <div class="action-left">
                <div class="action-icon red">
                    <i class="fas fa-running"></i>
                </div>
                <div>
                    <h3>Ajouter activité</h3>
                    <p>Ajoutez votre sport</p>
                </div>
            </div>
            <i class="fas fa-plus"></i>
        </div>

        <div class="action-card" onclick="addWater()">
            <div class="action-left">
                <div class="action-icon blue">
                    <i class="fas fa-tint"></i>
                </div>
                <div>
                    <h3>Ajouter eau</h3>
                    <p>+1 verre d'eau</p>
                </div>
            </div>
            <i class="fas fa-plus"></i>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="stats">
        <div class="card">
            <div class="card-top">
                <div>
                    <div class="card-title">Calories</div>
                    <div class="card-value" id="calories">
                        <?= $totals['calories_consumed'] ?? 0 ?>
                    </div>
                </div>
                <div class="action-icon green">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
            <p>Objectif <?= $user['daily_calories_needs'] ?? 2000 ?> kcal</p>
            <div class="progress">
                <div class="green"
                    style="width:<?= min(100, (($totals['calories_consumed'] ?? 0) / ($user['daily_calories_needs'] ?? 2000)) * 100) ?>%">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-top">
                <div>
                    <div class="card-title">Activité</div>
                    <div class="card-value" id="burned">
                        <?= $totals['calories_burned'] ?? 0 ?>
                    </div>
                </div>
                <div class="action-icon red">
                    <i class="fas fa-dumbbell"></i>
                </div>
            </div>
            <p>Calories brûlées</p>
        </div>

        <div class="card">
            <div class="card-top">
                <div>
                    <div class="card-title">Hydratation</div>
                    <div class="card-value" id="water">
                        <?= $totals['water'] ?? 0 ?>
                    </div>
                </div>
                <div class="action-icon blue">
                    <i class="fas fa-glass-water"></i>
                </div>
            </div>
            <p><?= $user['water_goal'] ?? 8 ?> verres</p>
        </div>

        <div class="card">
            <div class="card-top">
                <div>
                    <div class="card-title">Sommeil</div>
                    <div class="card-value">
                        <?= $totals['sleep'] ?? 0 ?>h
                    </div>
                </div>
                <div class="action-icon" style="background:#9333ea;">
                    <i class="fas fa-moon"></i>
                </div>
            </div>
            <p>Objectif 8h</p>
        </div>
    </div>

    <!-- MEALS & ACTIVITIES GRID -->
    <div class="grid">
        <!-- MEALS -->
        <div class="card">
            <h2 class="section-title">🍽️ Repas</h2>
            <div id="mealsContainer">
                <?php if(empty($meals)): ?>
                    <p>Aucun repas ajouté.</p>
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

        <!-- ACTIVITIES -->
        <div class="card">
            <h2 class="section-title">🏃 Activités</h2>
            <div id="activitiesContainer">
                <?php if(empty($activities)): ?>
                    <p>Aucune activité.</p>
                <?php else: ?>
                    <?php foreach($activities as $activity): ?>
                        <div class="activity-item">
                            <div>
                                <strong><?= htmlspecialchars($activity['activity_type']) ?></strong>
                                <p><?= $activity['duration'] ?> min</p>
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

<!-- MODAL REPAS -->
<div class="modal" id="mealModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Ajouter repas</h2>
            <button class="close" onclick="closeModal('mealModal')">&times;</button>
        </div>
        <form id="mealForm" novalidate>
            <div class="form-group">
                <label>Nom aliment</label>
                <input type="text" name="food_name" required>
            </div>
            <div class="form-group">
                <label>Calories</label>
                <input type="number" name="calories" required>
            </div>
            <div class="form-group">
                <label>Quantité</label>
                <input type="number" name="quantity">
            </div>
            <div class="form-group">
                <label>Type repas</label>
                <select name="meal_type">
                    <option>Petit-déjeuner</option>
                    <option>Déjeuner</option>
                    <option>Dîner</option>
                    <option>Collation</option>
                </select>
            </div>
            <button class="submit-btn">Ajouter</button>
        </form>
    </div>
</div>

<!-- MODAL ACTIVITE -->
<div class="modal" id="activityModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Ajouter activité</h2>
            <button class="close" onclick="closeModal('activityModal')">&times;</button>
        </div>
        <form id="activityForm" novalidate>
            <div class="form-group">
                <label>Activité</label>
                <select name="activity_type">
                    <option>Marche</option>
                    <option>Course</option>
                    <option>Vélo</option>
                    <option>Musculation</option>
                </select>
            </div>
            <div class="form-group">
                <label>Durée (minutes)</label>
                <input type="number" name="duration" required>
            </div>
            <div class="form-group">
                <label>Calories brûlées</label>
                <input type="number" name="calories_burned" required>
            </div>
            <button class="submit-btn">Ajouter</button>
        </form>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
function openModal(id){
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id){
    document.getElementById(id).style.display = 'none';
}

window.onclick = function(e){
    document.querySelectorAll('.modal').forEach(modal=>{
        if(e.target === modal){
            modal.style.display = 'none';
        }
    });
}

function showToast(message){
    const toast = document.getElementById('toast');
    toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    toast.style.display = 'flex';
    setTimeout(()=>{
        toast.style.display = 'none';
    }, 3000);
}

/* ADD MEAL */
document.getElementById('mealForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    try{
        const response = await fetch('index.php?page=add_meal_suivi', {
            method:'POST',
            body:formData
        });
        const result = await response.json();
        if(result.success){
            showToast('Repas ajouté');
            location.reload();
        }
    }catch(error){
        console.log(error);
    }
});

/* ADD ACTIVITY */
document.getElementById('activityForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    try{
        const response = await fetch('index.php?page=add_activity_suivi', {
            method:'POST',
            body:formData
        });
        const result = await response.json();
        if(result.success){
            showToast('Activité ajoutée');
            location.reload();
        }
    }catch(error){
        console.log(error);
    }
});

/* ADD WATER */
async function addWater(){
    try{
        const response = await fetch('index.php?page=add_water_suivi', {
            method:'POST'
        });
        const result = await response.json();
        if(result.success){
            showToast('Eau ajoutée');
            document.getElementById('water').innerText = result.data.totals.water;
            document.getElementById('dailyScore').innerText = result.data.dailyScore;
        }
    }catch(error){
        console.log(error);
    }
}

/* DELETE MEAL */
async function deleteMeal(id){
    if(!confirm('Supprimer ce repas ?')) return;
    try{
        const response = await fetch('index.php?page=delete_meal_suivi', {
            method:'POST',
            body:new URLSearchParams({id:id})
        });
        const result = await response.json();
        if(result.success){
            showToast('Repas supprimé');
            location.reload();
        }
    }catch(error){
        console.log(error);
    }
}

/* DELETE ACTIVITY */
async function deleteActivity(id){
    if(!confirm('Supprimer cette activité ?')) return;
    try{
        const response = await fetch('index.php?page=delete_activity_suivi', {
            method:'POST',
            body:new URLSearchParams({id:id})
        });
        const result = await response.json();
        if(result.success){
            showToast('Activité supprimée');
            location.reload();
        }
    }catch(error){
        console.log(error);
    }
}
</script>

</body>
</html>