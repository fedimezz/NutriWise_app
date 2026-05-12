<?php
// views/front/schedule_planning.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planifier - <?= e($planning['name']) ?> - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .card-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .card-header h1 {
            font-size: 1.8rem;
            color: #2e7d32;
            margin-bottom: 10px;
        }
        
        .card-header p {
            color: #7c8e7a;
        }
        
        .planning-info {
            background: #f8faf8;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .planning-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 10px;
        }
        
        .planning-dates {
            color: #6b8a66;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        input[type="date"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e8dc;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input[type="date"]:focus {
            outline: none;
            border-color: #2e7d32;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            flex: 1;
            background: #e0e8dc;
            color: #4a6741;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background: #c8d6c0;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1><i class="fas fa-calendar-plus"></i> Planifier</h1>
                <p>Choisissez la date de début pour ce planning</p>
            </div>
            
            <div class="planning-info">
                <div class="planning-name">
                    <i class="fas fa-clipboard-list"></i> <?= e($planning['name']) ?>
                </div>
                <div class="planning-dates">
                    <i class="fas fa-calendar-alt"></i> Durée originale: 
                    <?= date('d/m/Y', strtotime($planning['start_date'])) ?> - 
                    <?= date('d/m/Y', strtotime($planning['end_date'])) ?>
                </div>
            </div>
            
            <form id="scheduleForm" novalidate>
                <div class="form-group">
                    <label for="start_date"><i class="fas fa-calendar-day"></i> Date de début</label>
                    <input type="date" id="start_date" name="start_date" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label for="end_date"><i class="fas fa-calendar-week"></i> Date de fin (optionnelle)</label>
                    <input type="date" id="end_date" name="end_date">
                    <small style="color: #7c8e7a;">Laissez vide pour utiliser la durée du planning original</small>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check"></i> Planifier
                    </button>
                    <a href="index.php?page=nutrition_plans" class="btn-cancel">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('scheduleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (!startDate) {
                alert('Veuillez sélectionner une date de début');
                return;
            }
            
            const response = await fetch('index.php?page=schedule_planning', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    planning_id: <?= $planning['id'] ?>,
                    start_date: startDate,
                    end_date: endDate
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('✅ ' + result.message);
                window.location.href = 'index.php?page=my_schedules';
            } else {
                alert('❌ ' + result.message);
            }
        });
    </script>
</body>
</html>