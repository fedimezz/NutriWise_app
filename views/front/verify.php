<?php
// Pas besoin de session_start() ici, déjà ouvert dans index.php ou AuthController
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification Email - NutriWise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verify-card {
            margin-top: 80px;
            border-radius: 10px;
            overflow: hidden;
        }
        .verify-header {
            background: #28a745;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .verify-header h4 {
            margin: 0;
            font-weight: bold;
        }
        .verify-body {
            padding: 30px;
        }
        .verify-footer {
            background: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #666;
        }
        .btn-resend {
            background: #ffc107;
            color: #000;
            margin-top: 10px;
        }
        .btn-resend:hover {
            background: #e0a800;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow verify-card">
                <div class="verify-header">
                    <h4>Vérification de l'email</h4>
                    <p class="mb-0">Entrez le code reçu par email pour activer votre compte NutriWise</p>
                </div>
                <div class="verify-body">

                    <!-- ✅ Message succès -->
                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <!-- ❌ Message erreur -->
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Formulaire de vérification -->
                    <form method="POST" action="index.php?page=verify_email" novalidate>
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">

                        <div class="mb-3">
                            <label for="code" class="form-label">Code de vérification</label>
                            <input type="text" name="code" id="code" class="form-control" placeholder="Ex: 123456" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Vérifier</button>
                    </form>

                    <!-- Bouton Renvoyer le code -->
                    <form method="POST" action="index.php?page=resend_code">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                        <button type="submit" class="btn btn-resend w-100">Renvoyer le code</button>
                    </form>

                </div>
                <div class="verify-footer">
                    NutriWise © 2026
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
