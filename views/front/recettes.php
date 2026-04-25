<?php // Access control is handled in controller/router (PHP), not in the view. ?>
<?php if(($page ?? '') === 'admin_recettes'): ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recettes - Admin | NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo"><span>🌿</span><span>NutriWise</span></div>
            <nav>
                <a href="index.php?page=admin_dashboard" class="<?= ($page=='admin_dashboard') ? 'active' : '' ?>">📊 Dashboard</a>
                <a href="index.php?page=admin_users" class="<?= ($page=='admin_users') ? 'active' : '' ?>">👥 Utilisateurs</a>
                <a href="index.php?page=admin_aliments" class="<?= ($page=='admin_aliments') ? 'active' : '' ?>">🥗 Aliments</a>
                <a href="index.php?page=admin_recettes" class="<?= ($page=='admin_recettes') ? 'active' : '' ?>">📖 Recettes</a>
                <a href="index.php?page=admin_plans" class="<?= ($page=='admin_plans') ? 'active' : '' ?>">📅 Plans</a>
            </nav>
            <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
        </aside>

        <main class="main-content">
            <header>
                <h1>Recettes</h1>
                <a href="index.php?page=home" class="view-link" style="text-decoration:none;">← Retour à l’accueil</a>
            </header>

            <div class="recent-users">
                <div class="section-header">
                    <h2>Liste des recettes</h2>
                    <form method="GET" action="index.php" style="display:flex; gap:10px; align-items:center;" novalidate>
                        <input type="hidden" name="page" value="admin_recettes">
                        <input name="q" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="Rechercher par titre..." style="padding:10px 12px; border:1px solid #c8e6c9; border-radius:12px; min-width:260px;">
                        <button type="submit" class="view-link" style="border:0; background:transparent; font-weight:600; cursor:pointer;">Rechercher</button>
                    </form>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <p style="color:#1b5e20; margin: 10px 0; font-weight:600;"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?></p>
                <?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?>
                    <p style="color:#b71c1c; margin: 10px 0; font-weight:600;"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?></p>
                <?php endif; ?>

                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Calories</th>
                            <th>Macros (P/G/L)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($recipes)): ?>
                            <tr><td colspan="5" style="padding:16px;">Aucune recette.</td></tr>
                        <?php else: ?>
                            <?php foreach($recipes as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(trim(($r['prenom'] ?? '').' '.($r['nom'] ?? '')) ?: '-', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($r['calories'] ?? '-'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($r['protein_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> / <?= htmlspecialchars((string)($r['carbs_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> / <?= htmlspecialchars((string)($r['fat_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td>
                                        <a class="btn-icon" href="index.php?page=admin_recettes&action=delete_recipe&id=<?= (int)$r['id'] ?>" onclick="return confirm('Supprimer cette recette ?');">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
<?php else: ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Recettes - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>
        
        <div class="page-header">
            <h1 class="page-title">Mes Recettes</h1>
            <p class="page-subtitle">Créez, modifiez et retrouvez vos recettes équilibrées.</p>
        </div>

        <div class="coming-soon" style="max-width: 980px;">
            <div class="coming-soon-icon">🍳</div>
            <h2 style="margin-bottom: 12px;">Créer / modifier une recette</h2>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="success-message" style="background:#d4edda; color:#155724; padding:12px; border-radius:12px; margin-bottom:12px;">
                    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                <div class="error-message" style="background:#f8d7da; color:#721c24; padding:12px; border-radius:12px; margin-bottom:12px;">
                    <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=recettes" style="text-align:left; margin-top:10px;" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                <?php if(!empty($editRecipe)): ?>
                    <input type="hidden" name="action" value="update_recipe">
                    <input type="hidden" name="recipe_id" value="<?= (int)$editRecipe['id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="action" value="create_recipe">
                <?php endif; ?>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div>
                        <label style="font-weight:600;">Titre</label>
                        <input name="title" required value="<?= htmlspecialchars($editRecipe['title'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:600;">Calories (optionnel)</label>
                        <input type="number" name="calories" value="<?= htmlspecialchars((string)($editRecipe['calories'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <label style="font-weight:600;">Description</label>
                    <textarea name="description" rows="3" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px; resize:vertical;"><?= htmlspecialchars($editRecipe['description'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; margin-top:12px;">
                    <div>
                        <label style="font-weight:600;">Protéines (g)</label>
                        <input type="number" step="0.01" name="protein_g" value="<?= htmlspecialchars((string)($editRecipe['protein_g'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:600;">Glucides (g)</label>
                        <input type="number" step="0.01" name="carbs_g" value="<?= htmlspecialchars((string)($editRecipe['carbs_g'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:600;">Lipides (g)</label>
                        <input type="number" step="0.01" name="fat_g" value="<?= htmlspecialchars((string)($editRecipe['fat_g'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:14px;">
                    <button type="submit" class="btn-submit" style="background:linear-gradient(135deg,#2e7d32,#4caf50); color:#fff; border:0; padding:12px 16px; border-radius:12px; font-weight:700; cursor:pointer;">
                        <?= !empty($editRecipe) ? 'Enregistrer' : 'Créer la recette' ?>
                    </button>
                    <?php if(!empty($editRecipe)): ?>
                        <a href="index.php?page=recettes" class="btn-submit" style="background:#e9ecef; color:#2c3e2f; border:0; padding:12px 16px; border-radius:12px; font-weight:700; text-decoration:none;">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="coming-soon" style="max-width: 980px; margin-top: 18px;">
            <div class="section-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                <h2 style="margin:0;">Toutes les recettes</h2>
                <form method="GET" action="index.php" style="display:flex; gap:10px; align-items:center;" novalidate>
                    <input type="hidden" name="page" value="recettes">
                    <input name="q" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" placeholder="Rechercher..." style="padding:10px 12px; border:1px solid #e9ecef; border-radius:12px;">
                    <button type="submit" class="btn-submit" style="background:#2e7d32; color:#fff; border:0; padding:10px 14px; border-radius:12px; font-weight:700; cursor:pointer;">OK</button>
                </form>
            </div>

            <?php if(empty($recipes)): ?>
                <p style="margin-top:10px; color:#6c757d;">Aucune recette pour le moment.</p>
            <?php else: ?>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:12px; margin-top:12px;">
                    <?php foreach($recipes as $r): ?>
                        <div style="background:#fff; border:1px solid #e9ecef; border-radius:16px; padding:14px; text-align:left;">
                            <div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">
                                <div>
                                    <div style="font-weight:800; color:#2c3e2f;"><?= htmlspecialchars($r['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
                                    <div style="font-size:0.9rem; color:#6c757d;">
                                        Par <?= htmlspecialchars(trim(($r['prenom'] ?? '').' '.($r['nom'] ?? '')) ?: 'Utilisateur', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                                    </div>
                                </div>
                                <div style="font-weight:800; color:#2e7d32;"><?= htmlspecialchars((string)($r['calories'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> kcal</div>
                            </div>

                            <?php if(!empty($r['description'])): ?>
                                <p style="margin-top:10px; color:#2c3e2f;"><?= nl2br(htmlspecialchars($r['description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) ?></p>
                            <?php endif; ?>

                            <div style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap; color:#2c3e2f;">
                                <span style="background:#f8f9fa; padding:6px 10px; border-radius:999px;">P <?= htmlspecialchars((string)($r['protein_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>g</span>
                                <span style="background:#f8f9fa; padding:6px 10px; border-radius:999px;">G <?= htmlspecialchars((string)($r['carbs_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>g</span>
                                <span style="background:#f8f9fa; padding:6px 10px; border-radius:999px;">L <?= htmlspecialchars((string)($r['fat_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>g</span>
                            </div>

                            <div style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap;">
                                <a href="index.php?page=recettes&action=edit_recipe&id=<?= (int)$r['id'] ?>" style="text-decoration:none; font-weight:700; color:#1976d2;">✏️ Modifier</a>
                                <a href="index.php?page=recettes&action=delete_recipe&id=<?= (int)$r['id'] ?>" onclick="return confirm('Supprimer cette recette ?');" style="text-decoration:none; font-weight:700; color:#d32f2f;">🗑️ Supprimer</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
        
        .coming-soon {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 32px;
            margin: 2rem auto;
            max-width: 600px;
        }
        
        .coming-soon-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .coming-soon h2 {
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }
        
        .coming-soon p {
            color: #6c757d;
        }
    </style>
</body>
</html>
<?php endif; ?>