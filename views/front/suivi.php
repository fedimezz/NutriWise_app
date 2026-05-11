<?php
// Vérifier si l'utilisateur est connecté -> géré par le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Suivi - NutriWise</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        .page-header {
            text-align: center;
            padding: 3rem 2rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }

        .tracking-container {
            display: flex;
            justify-content: center;
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
        }

        .tracking-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .tracking-card h2 {
            color: #2e7d32;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4caf50;
            outline: none;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .checkbox-container input {
            width: 20px;
            height: 20px;
            accent-color: #4caf50;
        }

        .btn-submit {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.4);
        }

        /* History Table */
        .history-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            color: #2e7d32;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            color: #555;
        }

        .badge-success { background: #d4edda; color: #155724; padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.8rem;}
        .badge-danger { background: #f8d7da; color: #721c24; padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.8rem;}
        
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }

        @media (max-width: 900px) {
            .tracking-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>
        
        <div class="page-header">
            <h1 class="page-title">Suivi Quotidien</h1>
            <p class="page-subtitle">Suivez votre progression jour après jour</p>
        </div>

        <div class="tracking-container">
            <!-- Formulaire d'ajout -->
            <div class="tracking-card">
                <h2>Renseigner la journée</h2>
                
                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                <?php endif; ?>
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <form action="index.php?page=add_suivi" method="POST">
                    <div class="form-group">
                        <label for="poids">Poids actuel (kg)</label>
                        <input type="number" step="0.1" name="poids" id="poids" class="form-control" value="<?php echo isset($user_profile['poids']) ? $user_profile['poids'] : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="eau_bue_du_jour">Eau bue (Litres)</label>
                        <input type="number" step="0.1" name="eau_bue_du_jour" id="eau_bue_du_jour" class="form-control" placeholder="ex: 1.5" required>
                    </div>

                    <div class="form-group">
                        <label for="etat_du_jour">État du jour</label>
                        <select name="etat_du_jour" id="etat_du_jour" class="form-control" required>
                            <option value="">Sélectionner un état...</option>
                            <option value="en forme">En pleine forme</option>
                            <option value="normal">Normal</option>
                            <option value="fatigué">Fatigué(e)</option>
                            <option value="malade">Malade</option>
                            <option value="stressé">Stressé(e)</option>
                        </select>
                    </div>

                    <div class="form-group" style="padding: 10px 0;">
                        <label class="checkbox-container">
                            <input type="checkbox" name="jour_reussi" value="1">
                            <span style="font-weight: 500; color: #333;">Journée réussie ? (Objectifs atteints)</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Enregistrer</button>
                </form>
            </div>
        </div>

        <footer class="footer" style="margin-top: 4rem;">
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