<?php
// views/front/my_schedules.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes plannings planifiés - NutriWise</title>
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
            background: #f8faf8;
            color: #1a2e1a;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 24px;
        }
        
        .page-header {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f0f7ed, #e8f3e4);
            border-radius: 32px;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .schedules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        
        .schedule-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }
        
        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .schedule-header {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            padding: 1.2rem;
        }
        
        .schedule-header h3 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .status-scheduled { background: #f39c12; color: white; }
        .status-in_progress { background: #3498db; color: white; }
        .status-completed { background: #27ae60; color: white; }
        .status-cancelled { background: #e74c3c; color: white; }
        
        .schedule-body {
            padding: 1.2rem;
        }
        
        .schedule-dates {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: #f8faf8;
            border-radius: 12px;
        }
        
        .date-item {
            flex: 1;
            text-align: center;
        }
        
        .date-label {
            font-size: 0.7rem;
            color: #7c8e7a;
        }
        
        .date-value {
            font-weight: 700;
            color: #2e7d32;
        }
        
        .schedule-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .btn-view, .btn-complete, .btn-delete {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-view {
            background: #3498db;
            color: white;
        }
        
        .btn-complete {
            background: #27ae60;
            color: white;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn-view:hover, .btn-complete:hover, .btn-delete:hover {
            transform: translateY(-2px);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem;
            background: white;
            border-radius: 32px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #c8e6c9;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .schedules-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include_once 'partials/navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-calendar-check"></i> Mes plannings planifiés</h1>
            <p>Retrouvez tous vos plannings personnalisés</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert" style="background:#d4edda; color:#155724; padding:12px; border-radius:12px; margin-bottom:20px;">
                <?= e($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert" style="background:#f8d7da; color:#721c24; padding:12px; border-radius:12px; margin-bottom:20px;">
                <?= e($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($schedules)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Aucun planning planifié</h3>
                <p>Planifiez vos premiers plannings depuis la liste</p>
                <a href="index.php?page=nutrition_plans" style="display:inline-block; margin-top:1rem; padding:0.75rem 1.5rem; background:#2e7d32; color:white; border-radius:50px; text-decoration:none;">
                    <i class="fas fa-plus"></i> Voir les plannings
                </a>
            </div>
        <?php else: ?>
            <div class="schedules-grid">
                <?php foreach ($schedules as $schedule): ?>
                    <div class="schedule-card">
                        <div class="schedule-header">
                            <h3><i class="fas fa-clipboard-list"></i> <?= e($schedule['name']) ?></h3>
                            <span class="status-badge status-<?= e($schedule['status']) ?>">
                                <?php
                                $statusText = [
                                    'scheduled' => '📅 Planifié',
                                    'in_progress' => '🚀 En cours',
                                    'completed' => '✅ Terminé',
                                    'cancelled' => '❌ Annulé'
                                ];
                                echo $statusText[$schedule['status']] ?? $schedule['status'];
                                ?>
                            </span>
                        </div>
                        <div class="schedule-body">
                            <div class="schedule-dates">
                                <div class="date-item">
                                    <div class="date-label">Début</div>
                                    <div class="date-value"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></div>
                                </div>
                                <div class="date-item">
                                    <div class="date-label">Fin</div>
                                    <div class="date-value"><?= $schedule['end_date'] ? date('d/m/Y', strtotime($schedule['end_date'])) : 'À définir' ?></div>
                                </div>
                            </div>
                            
                            <div class="schedule-actions">
                                <a href="index.php?page=nutrition_plan_details&id=<?= $schedule['planning_id'] ?>" class="btn-view">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <?php if ($schedule['status'] !== 'completed' && $schedule['status'] !== 'cancelled'): ?>
                                    <a href="index.php?page=complete_schedule&id=<?= $schedule['id'] ?>" class="btn-complete" onclick="return confirm('Marquer ce planning comme terminé ?')">
                                        <i class="fas fa-check"></i> Terminer
                                    </a>
                                <?php endif; ?>
                                <a href="index.php?page=delete_schedule&id=<?= $schedule['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cette planification ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>