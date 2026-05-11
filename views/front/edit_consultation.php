<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Modifier Consultation - NutriWise</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        .edit-wrap { max-width: 760px; margin: 0 auto; padding: 3rem 1.5rem 4rem; }
        .edit-card {
            background: #fff;
            border-radius: 22px;
            padding: 2rem;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
        }
        .edit-card h1 { color: #2e7d32; margin-bottom: 0.5rem; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.45rem; font-weight: 600; }
        .form-control {
            width: 100%;
            padding: 0.95rem 1rem;
            border: 1px solid #d9e3d7;
            border-radius: 12px;
            font-family: inherit;
        }
        textarea.form-control { min-height: 110px; resize: vertical; }
        .actions { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-primary, .btn-secondary {
            padding: 0.95rem 1.3rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background: linear-gradient(135deg, #2e7d32, #4caf50); color: #fff; }
        .btn-secondary { background: #fff; color: #526055; border: 1px solid #d9e3d7; }
        .alert-error { background: #fdecea; color: #b42318; padding: 1rem; border-radius: 12px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="edit-wrap">
            <div class="edit-card">
                <h1>Modifier la consultation</h1>
                <p style="color:#5f6f63; margin-bottom:1.5rem;">Mettez a jour la consultation liee a votre suivi.</p>

                <?php if (isset($error)): ?>
                    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="index.php?page=edit_consultation&id=<?= $consultation['id'] ?>" method="POST">
                    <div class="form-group">
                        <label for="suivi_id">Suivi associe</label>
                        <select name="suivi_id" id="suivi_id" class="form-control" required>
                            <option value="">Selectionner un suivi</option>
                            <?php foreach ($suivis as $suivi): ?>
                                <option value="<?= $suivi['id'] ?>" <?= intval($consultation['suivi_id']) === intval($suivi['id']) ? 'selected' : '' ?>>
                                    <?= date('d/m/Y', strtotime($suivi['date_suivi'])) ?> - <?= htmlspecialchars($suivi['poids']) ?> kg<?= !empty($suivi['etat_du_jour']) ? ' - ' . htmlspecialchars($suivi['etat_du_jour']) : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_consultation">Date de consultation</label>
                        <input type="date" name="date_consultation" id="date_consultation" class="form-control" value="<?= htmlspecialchars($consultation['date_consultation']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="poids_cible">Poids cible (kg)</label>
                        <input type="number" step="0.1" name="poids_cible" id="poids_cible" class="form-control" value="<?= htmlspecialchars((string) $consultation['poids_cible']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="remarque">Remarque</label>
                        <textarea name="remarque" id="remarque" class="form-control" required><?= htmlspecialchars($consultation['remarque']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="conseil">Conseil</label>
                        <textarea name="conseil" id="conseil" class="form-control" required><?= htmlspecialchars($consultation['conseil']) ?></textarea>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn-primary">Mettre a jour</button>
                        <a href="index.php?page=consultations" class="btn-secondary">Retour</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
