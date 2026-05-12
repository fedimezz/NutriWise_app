<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - NutriWise</title>
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
            background: linear-gradient(135deg, #f0f7ed 0%, #e8f3e4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 32px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo-icon {
            font-size: 3rem;
            display: inline-block;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            padding: 0.8rem;
            border-radius: 50%;
            color: white;
        }

        h1 {
            font-size: 1.8rem;
            color: #1a472a;
            margin-bottom: 0.5rem;
            text-align: center;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            color: #6b8a66;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        /* Messages d'alerte */
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border-left: 4px solid #4caf50;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border-left: 4px solid #ef5350;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success i, .alert-error i {
            font-size: 1.2rem;
        }

        /* Formulaire */
        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: #2c3e2c;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        label i {
            color: #4caf50;
            width: 18px;
        }

        input {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 2px solid #e2e8dc;
            border-radius: 16px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            transition: all 0.3s;
            background: white;
        }

        input:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        input::placeholder {
            color: #b8d4b0;
        }

        button {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            border: none;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }

        /* Lien retour */
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #eef2ec;
            text-decoration: none;
            color: #6b8a66;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .back-link:hover {
            color: #2e7d32;
            gap: 0.8rem;
        }

        /* Code input spécifique */
        input#code {
            text-align: center;
            letter-spacing: 4px;
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 500px) {
            .card {
                padding: 1.5rem;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="logo">
            <i class="fas fa-leaf logo-icon"></i>
        </div>

        <h1>Mot de passe oublié</h1>
        <p class="subtitle">Ne vous inquiétez pas, ça arrive à tout le monde !</p>

        <!-- Messages -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php $step = $_GET['step'] ?? 'request'; ?>

        <!-- ===================== -->
        <!-- STEP 1: Demande email -->
        <!-- ===================== -->
        <?php if($step === 'request'): ?>
            <form action="index.php?page=motpasse" method="POST" novalidate>
                <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="request_reset">

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" 
                           placeholder="exemple@domaine.com" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required autofocus>
                </div>

                <button type="submit">
                    <i class="fas fa-paper-plane"></i> Envoyer le code
                </button>
            </form>

        <!-- ===================== -->
        <!-- STEP 2: Vérification -->
        <!-- ===================== -->
        <?php elseif($step === 'verify' && isset($_SESSION['reset_verify'])): ?>
            <form action="index.php?page=motpasse&step=verify" method="POST" novalidate>
                <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="reset_password">

                <div class="form-group">
                    <label><i class="fas fa-key"></i> Code de vérification</label>
                    <input type="text" id="code" name="code" 
                           maxlength="6" placeholder="123456" 
                           required autofocus>
                    <small style="color:#8aa08a; font-size:0.7rem; margin-top:0.3rem; display:block;">
                        <i class="fas fa-info-circle"></i> Entrez le code à 6 chiffres reçu par email
                    </small>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" 
                           placeholder="••••••••" required>
                </div>

                <button type="submit">
                    <i class="fas fa-check-circle"></i> Changer le mot de passe
                </button>
            </form>
        <?php endif; ?>

        <a href="index.php?page=login" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à la connexion
        </a>
    </div>
</div>
</body>
</html>