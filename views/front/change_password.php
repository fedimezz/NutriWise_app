<?php
// views/front/change_password.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Changer le mot de passe - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/front-global.css">
    <style>
        .password-card {
            max-width: 520px;
            margin: 2.5rem auto;
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            padding: 2rem;
        }
        .password-card h1 {
            font-size: 1.8rem;
            color: #2e7d32;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e2f;
        }
        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
        }
        .note {
            background: #f8faf8;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e2f;
        }
        .note i {
            color: #f39c12;
            font-size: 1.2rem;
        }
        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46,125,50,0.3);
        }
        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #ef4444;
        }
        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 1rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #22c55e;
        }
        .text-center {
            text-align: center;
            margin-top: 1.5rem;
        }
        .text-center a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 500;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="password-card">
            <h1>
                <i class="fas fa-lock" style="color: #2e7d32;"></i> 
                Changer le mot de passe
            </h1>

            <?php if (!empty($_SESSION['must_change_password'])): ?>
                <div class="note">
                    <i class="fas fa-shield-alt"></i>
                    <span>Pour des raisons de sécurité, vous devez changer votre mot de passe avant de continuer.</span>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=change_password" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-group">
                    <label><i class="fas fa-key"></i> Mot de passe actuel</label>
                    <input type="password" name="current_password" required placeholder="Entrez votre mot de passe actuel">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-pen"></i> Nouveau mot de passe</label>
                    <input type="password" name="new_password" required placeholder="Minimum 8 caractères">
                    <small style="color: #6b8a66;">Doit contenir au moins 8 caractères</small>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-check-double"></i> Confirmer le nouveau mot de passe</label>
                    <input type="password" name="confirm_password" required placeholder="Retapez le nouveau mot de passe">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>

                <div class="text-center">
                    <a href="index.php?page=profile">
                        <i class="fas fa-arrow-left"></i> Retour au profil
                    </a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-logo">🌿 NutriWise</div>
        <p>© 2024 NutriWise - Nutrition intelligente et durable</p>
    </footer>
</body>
</html>