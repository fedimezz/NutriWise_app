<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Consultations - NutriWise</title>
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
        .consultation-layout {
            display: grid;
            grid-template-columns: minmax(320px, 420px) 1fr;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 3rem;
        }
        .panel {
            background: #fff;
            border-radius: 22px;
            padding: 2rem;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
        }
        .panel h2 { color: #2e7d32; margin-bottom: 1.5rem; font-size: 1.5rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.45rem;
            font-weight: 600;
            color: #203127;
        }
        .form-control {
            width: 100%;
            padding: 0.95rem 1rem;
            border: 1px solid #d9e3d7;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
        }
        .form-control:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.12);
        }
        textarea.form-control { min-height: 110px; resize: vertical; }
        .btn-submit {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 1rem;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
        }
        .alert { padding: 0.95rem 1rem; border-radius: 12px; margin-bottom: 1rem; }
        .alert-success { background: #e8f5e9; color: #1b5e20; }
        .alert-error { background: #fdecea; color: #b42318; }
        .consultation-table { width: 100%; border-collapse: collapse; }
        .consultation-table th, .consultation-table td {
            padding: 1rem 0.8rem;
            border-bottom: 1px solid #edf2eb;
            vertical-align: top;
            text-align: left;
        }
        .consultation-table th { color: #2e7d32; background: #f7faf7; white-space: nowrap; }
        .small-note { color: #5f6f63; font-size: 0.92rem; }
        .action-link { color: #c62828; text-decoration: none; font-weight: 600; }
        .action-link:hover { text-decoration: underline; }
        .action-btn {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 0.3rem;
        }
        .action-btn.edit { background: #e8f5e9; color: #2e7d32; }
        .action-btn.delete { background: #ffebee; color: #c62828; }
        @media (max-width: 960px) {
            .consultation-layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="page-header">
            <h1 class="page-title">Mes Consultations</h1>
            <p class="page-subtitle">Ajoutez vos consultations et liez-les à vos suivis quotidiens.</p>

            <?php if (!empty($consultations)): ?>
                <?php
                $latestConsultation = $consultations[0];
                $daysSinceLast = ceil((time() - strtotime($latestConsultation['date_consultation'])) / (60*60*24));
                ?>
                <div style="background: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 12px; padding: 1rem; margin-top: 1rem; max-width: 500px;">
                    <div style="font-size: 0.9rem; color: #2e7d32; font-weight: 600;">Dernière consultation</div>
                    <div style="font-size: 1.1rem; color: #1b5e20; margin: 0.25rem 0;">
                        <?= date('d/m/Y', strtotime($latestConsultation['date_consultation'])) ?>
                        <?php if ($daysSinceLast <= 7): ?>
                            <span style="color: #4caf50; font-size: 0.85rem;">(il y a <?= $daysSinceLast ?> jour<?= $daysSinceLast > 1 ? 's' : '' ?>)</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($latestConsultation['poids_cible']): ?>
                        <div style="font-size: 0.85rem; color: #558b2f;">Poids cible: <?= htmlspecialchars($latestConsultation['poids_cible']) ?> kg</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="consultation-layout">
            <div class="panel">
                <h2>Nouvelle consultation</h2>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>

                <?php if (empty($suivis)): ?>
                    <div class="alert alert-error">Ajoutez d abord un suivi pour pouvoir enregistrer une consultation.</div>
                <?php else: ?>
                    <form action="index.php?page=add_consultation" method="POST">
                        <div class="form-group">
                            <label for="suivi_id">Suivi associe</label>
                            <select name="suivi_id" id="suivi_id" class="form-control" required>
                                <option value="">Selectionner un suivi</option>
                                <?php foreach ($suivis as $suivi): ?>
                                    <option value="<?= $suivi['id'] ?>">
                                        <?= date('d/m/Y', strtotime($suivi['date_suivi'])) ?> - <?= htmlspecialchars($suivi['poids']) ?> kg<?= !empty($suivi['etat_du_jour']) ? ' - ' . htmlspecialchars($suivi['etat_du_jour']) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date_consultation">Date de consultation</label>
                            <input type="date" name="date_consultation" id="date_consultation" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="poids_cible">Poids cible (kg)</label>
                            <input type="number" step="0.1" name="poids_cible" id="poids_cible" class="form-control" placeholder="Ex: 68.5">
                        </div>

                        <div class="form-group">
                            <label for="remarque">Remarque</label>
                            <textarea name="remarque" id="remarque" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="conseil">Conseil du nutritionniste</label>
                            <textarea name="conseil" id="conseil" class="form-control" placeholder="Conseils et recommandations..." required></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Enregistrer la consultation</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
