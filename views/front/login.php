<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Connexion - NutriWise</title>
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
            max-width: 450px;
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
        }
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 0.9rem;
            border-radius: 16px;
            text-decoration: none;
            background: #fff;
            color: #2c3e2f;
            border: 2px solid #e9ecef;
            font-size: 1rem;
            font-weight: 600;
        }
        .btn-google:hover {
            background: #f8f9fa;
        }
        .google-icon {
            width: 20px;
            height: 20px;
            display: inline-block;
        }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
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
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 0.75rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="auth-container">
            <div class="auth-card">
                <h1 class="auth-title">Connexion</h1>
                <p class="auth-subtitle">Connectez-vous à votre compte NutriWise</p>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="success-message"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="error-message"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <?php if(($authStep ?? 'login') === 'verify_login'): ?>
                    <form method="POST" action="index.php?page=verify_login" novalidate>
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                        <div class="form-group">
                            <label>Code reçu par email</label>
                            <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" required placeholder="123456">
                        </div>
                        <button type="submit" class="btn-submit">Vérifier</button>
                    </form>

                    <form method="POST" action="index.php?page=verify_login" style="margin-top:12px;" novalidate>
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                        <input type="hidden" name="action" value="resend">
                        <button type="submit" class="btn-submit" style="background:#e9ecef; color:#2c3e2f;">Renvoyer le code</button>
                    </form>

                    <div class="auth-footer">
                        <a href="index.php?page=login">← Revenir au login</a>
                    </div>
                <?php else: ?>
                    <form method="POST" action="index.php?page=login" novalidate>
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" required placeholder="votre@email.com">
                        </div>
                        <div class="form-group">
                            <label>Mot de passe</label>
                            <input type="password" name="password" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn-submit">Se connecter</button>
                    </form>

                    <div style="margin-top:12px;">
                        <a href="index.php?page=google_login"
                           class="btn-google">
                            <svg class="google-icon" viewBox="0 0 48 48" aria-hidden="true">
                                <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.194 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.96 3.04l5.657-5.657C34.049 6.053 29.268 4 24 4 12.954 4 4 12.954 4 24s8.954 20 20 20 20-8.954 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                                <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 19.0 12 24 12c3.059 0 5.842 1.154 7.96 3.04l5.657-5.657C34.049 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                                <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.197l-6.19-5.238C29.27 35.091 26.757 36 24 36c-5.173 0-9.613-3.318-11.275-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                                <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.223-2.26 4.118-4.084 5.565l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                            </svg>
                            Continuer avec Google
                        </a>
                    </div>

                    <div class="auth-footer" style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                        <span>Pas encore de compte ? <a href="index.php?page=register">Inscrivez-vous</a></span>
                        <a href="index.php?page=motpasse">Mot de passe oublié ?</a>
                    </div>
                <?php endif; ?>

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