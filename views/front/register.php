<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Inscription - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <script src="views/assets/js/auth.js"></script>

    <style>
        .auth-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .auth-card {
            background: white;
            border-radius: 32px;
            padding: 2.5rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .auth-title {
            font-size: 1.8rem;
            color: #2e7d32;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .auth-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2c3e2f;
        }
        .form-group input {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
        }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            padding: 0.9rem;
            border: none;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(46,125,50,0.3);
        }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #6c757d;
        }
        .auth-footer a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 600;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .google-btn {
        display: inline-block;
        width: 100%;
        padding: 0.9rem;
        border-radius: 16px;
        background: white;
        border: 2px solid #e9ecef;
        font-weight: 600;
        color: #444;
        text-decoration: none;
        transition: all 0.3s;
}

    .google-btn:hover {
    border-color: #4caf50;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .auth-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="auth-container">
            <div class="auth-card">
                <h1 class="auth-title">Inscription</h1>
                <p class="auth-subtitle">Créez votre compte gratuitement</p>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="success-message"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="error-message"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?></div>
                <?php endif; ?>
    

                <form method="POST" action="index.php?page=register" novalidate>
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Prénom</label>
                            <input type="text" name="prenom"  placeholder="Jean">
                        </div>
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="nom"  placeholder="Dupont">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"  placeholder="jean.dupont@email.com">
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password"  placeholder="••••••••">
                    </div>
                    <button type="submit" class="btn-submit">Créer mon compte</button>
                </form>
                <div style="margin-bottom: 1.5rem; text-align:center;">
    <a href="index.php?page=google_signup" class="google-btn">
        <img src="https://developers.google.com/identity/images/g-logo.png" width="20" style="vertical-align:middle; margin-right:10px;">
        S'inscrire avec Google
    </a>
</div>

                <div class="auth-footer">
                    Déjà un compte ? <a href="index.php?page=login">Connectez-vous</a>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-logo">
                    <span class="logo-icon">🌿</span>
                    <span>NutriWise</span>
                </div>
                <p class="footer-copyright">© 2024 NutriWise - Nutrition intelligente et durable</p>
            </div>
        </footer>
    </div>
</body>
</html>