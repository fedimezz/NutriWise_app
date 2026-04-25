<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/style.css">
    <style>
        .card {
            max-width: 520px;
            margin: 2.5rem auto;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.10);
            padding: 1.75rem;
        }
        .row { margin-bottom: 1rem; }
        label { display:block; margin-bottom: .45rem; font-weight:600; color:#2c3e2f; }
        input {
            width:100%;
            padding:.85rem 1rem;
            border:2px solid #e9ecef;
            border-radius: 14px;
            font-size: 1rem;
        }
        .btn {
            width:100%;
            padding:.9rem 1rem;
            border:none;
            border-radius: 14px;
            font-weight:700;
            cursor:pointer;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color:#fff;
        }
        .note {
            background:#f8f9fa;
            border:1px solid #e9ecef;
            border-radius: 14px;
            padding: .85rem 1rem;
            margin-bottom: 1rem;
            color:#2c3e2f;
        }
        .error-message { background:#f8d7da; color:#721c24; padding:1rem; border-radius:12px; margin-bottom:1rem; }
        .success-message { background:#d4edda; color:#155724; padding:1rem; border-radius:12px; margin-bottom:1rem; }
    </style>
</head>
<body>
<div class="container">
    <?php include_once 'partials/navbar.php'; ?>

    <div class="card">
        <h1 style="margin-top:0;">Changer le mot de passe</h1>

        <?php if (!empty($_SESSION['must_change_password'])): ?>
            <div class="note">
                Pour des raisons de sécurité, vous devez changer votre mot de passe avant de continuer.
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-message"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=change_password" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

            <div class="row">
                <label>Mot de passe actuel</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="row">
                <label>Nouveau mot de passe (min 8 caractères)</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="row">
                <label>Confirmer le nouveau mot de passe</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button class="btn" type="submit">Mettre à jour</button>
        </form>
    </div>
</div>
</body>
</html>

