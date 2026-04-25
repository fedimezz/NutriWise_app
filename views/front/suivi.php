<?php // Access control is handled in controller/router (PHP), not in the view. ?>
<?php if(($page ?? '') === 'admin_plans'): ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plans - Admin | NutriWise</title>
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
                <h1>Plans alimentaires</h1>
                <a href="index.php?page=home" class="view-link" style="text-decoration:none;">← Retour à l’accueil</a>
            </header>

            <div class="recent-users">
                <div class="section-header">
                    <h2>Créer un plan</h2>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <p style="color:#1b5e20; margin: 10px 0; font-weight:600;"><?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?></p>
                <?php endif; ?>
                <?php if(isset($_SESSION['error'])): ?>
                    <p style="color:#b71c1c; margin: 10px 0; font-weight:600;"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?></p>
                <?php endif; ?>

                <form method="POST" action="index.php?page=admin_plans" style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;" novalidate>
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="create_plan">
                    <div style="grid-column: 1 / -1;">
                        <label style="font-weight:700;">Titre</label>
                        <input name="title" required style="width:100%; padding:12px; border:1px solid #c8e6c9; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:700;">Assigner à (optionnel)</label>
                        <select name="assigned_to" style="width:100%; padding:12px; border:1px solid #c8e6c9; border-radius:12px;">
                            <option value="">— Aucun —</option>
                            <?php foreach(($usersList ?? []) as $u): ?>
                                <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars(($u['prenom'] ?? '').' '.($u['nom'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> (<?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="font-weight:700;">Objectif</label>
                        <input name="goal" placeholder="Perte / Maintien / Prise" style="width:100%; padding:12px; border:1px solid #c8e6c9; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:700;">Calories cible</label>
                        <input type="number" name="calories_target" placeholder="ex: 2200" style="width:100%; padding:12px; border:1px solid #c8e6c9; border-radius:12px;">
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="is_active" checked>
                        <label style="font-weight:700;">Actif</label>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <button type="submit" class="view-link" style="border:0; background:var(--vert-principal); color:#fff; padding:12px 16px; border-radius:12px; font-weight:800; cursor:pointer;">Créer</button>
                    </div>
                </form>
            </div>

            <div class="recent-users" style="margin-top:16px;">
                <div class="section-header">
                    <h2>Plans existants</h2>
                </div>
                <table class="users-table">
                    <thead>
                        <tr><th>Titre</th><th>Objectif</th><th>Calories</th><th>Assigné à</th><th>Actif</th></tr>
                    </thead>
                    <tbody>
                        <?php if(empty($plans ?? [])): ?>
                            <tr><td colspan="5" style="padding:16px;">Aucun plan.</td></tr>
                        <?php else: ?>
                            <?php foreach($plans as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($p['goal'] ?? '-', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string)($p['calories_target'] ?? '-'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(trim(($p['at_prenom'] ?? '').' '.($p['at_nom'] ?? '')) ?: '-', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td><?= ((int)($p['is_active'] ?? 0) === 1) ? '✅' : '—' ?></td>
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
    <title>Suivi - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>
        
        <?php if(($page ?? '') === 'nutritionist_dashboard'): ?>
            <div class="page-header">
                <h1 class="page-title">Espace Nutritionniste</h1>
                <p class="page-subtitle">Créez des plans alimentaires et accompagnez vos utilisateurs efficacement.</p>
            </div>

            <div class="coming-soon" style="max-width: 900px;">
                <div class="coming-soon-icon">🧑‍⚕️</div>
                <h2>Tableau de bord</h2>
                <p style="margin-bottom: 1.25rem;">
                    Ici, vous pourrez bientôt créer des plans personnalisés, suivre l’adhérence et ajuster les objectifs en temps réel.
                </p>

                <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                    <a href="index.php?page=profile" class="btn-primary" style="text-decoration:none; padding:0.8rem 1.2rem; border-radius:14px; display:inline-block;">
                        Voir mon profil
                    </a>
                    <a href="index.php?page=recettes" class="btn-primary" style="text-decoration:none; padding:0.8rem 1.2rem; border-radius:14px; display:inline-block;">
                        Parcourir les recettes
                    </a>
                    <a href="index.php?page=aliments" class="btn-primary" style="text-decoration:none; padding:0.8rem 1.2rem; border-radius:14px; display:inline-block;">
                        Base aliments
                    </a>
                </div>

                <div style="margin-top:1.5rem; text-align:left; background:#f8f9fa; padding:16px; border-radius:16px;">
                    <h3 style="margin:0 0 8px; color:#2e7d32;">À venir dans cet espace</h3>
                    <ul style="margin:0; padding-left: 18px; color:#2c3e2f;">
                        <li>Création de plans (par objectifs + calories/jour)</li>
                        <li>Bibliothèque de repas & recettes validées</li>
                        <li>Suivi des progrès (poids, IMC, apports)</li>
                        <li>Conseils automatiques basés sur les données</li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <div class="page-header">
                <h1 class="page-title">Suivi & progression</h1>
                <p class="page-subtitle">Enregistrez vos calories et vos macros chaque jour.</p>
            </div>

            <?php if(isset($_SESSION['success'])): ?>
                <div style="max-width: 900px; margin: 0 auto 12px; background:#d4edda; color:#155724; padding:12px; border-radius:12px;">
                    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                <div style="max-width: 900px; margin: 0 auto 12px; background:#f8d7da; color:#721c24; padding:12px; border-radius:12px;">
                    <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php
                $goal = (int)($userData['daily_calories_needs'] ?? 2000);
                $consumed = (int)($todayLog['calories_consumed'] ?? 0);
                $remaining = $goal - $consumed;
            ?>

            <div class="coming-soon" style="max-width: 900px; text-align:left;">
                <h2 style="margin-bottom: 10px;">Aujourd’hui (<?= htmlspecialchars(date('d/m/Y'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>)</h2>
                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom: 12px;">
                    <div style="flex:1; min-width:180px; background:#f8f9fa; padding:12px; border-radius:14px;">
                        <div style="font-weight:800; color:#2e7d32; font-size: 1.4rem;"><?= $goal ?></div>
                        <div style="color:#6c757d;">Objectif kcal</div>
                    </div>
                    <div style="flex:1; min-width:180px; background:#f8f9fa; padding:12px; border-radius:14px;">
                        <div style="font-weight:800; color:#2e7d32; font-size: 1.4rem;"><?= $consumed ?></div>
                        <div style="color:#6c757d;">Consommé</div>
                    </div>
                    <div style="flex:1; min-width:180px; background:#f8f9fa; padding:12px; border-radius:14px;">
                        <div style="font-weight:800; color:#2e7d32; font-size: 1.4rem;"><?= $remaining ?></div>
                        <div style="color:#6c757d;">Restant</div>
                    </div>
                </div>

                <form method="POST" action="index.php?page=suivi" style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;" novalidate>
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                    <input type="hidden" name="action" value="save_daily_log">
                    <input type="hidden" name="day" value="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

                    <div>
                        <label style="font-weight:700;">Poids (kg)</label>
                        <input type="number" step="0.1" name="weight_kg" value="<?= htmlspecialchars((string)($todayLog['weight_kg'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:700;">Calories consommées</label>
                        <input type="number" name="calories_consumed" value="<?= htmlspecialchars((string)($todayLog['calories_consumed'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:700;">Protéines (g)</label>
                        <input type="number" step="0.1" name="protein_g" value="<?= htmlspecialchars((string)($todayLog['protein_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:700;">Glucides (g)</label>
                        <input type="number" step="0.1" name="carbs_g" value="<?= htmlspecialchars((string)($todayLog['carbs_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div>
                        <label style="font-weight:700;">Lipides (g)</label>
                        <input type="number" step="0.1" name="fat_g" value="<?= htmlspecialchars((string)($todayLog['fat_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="font-weight:700;">Notes (optionnel)</label>
                        <input name="notes" value="<?= htmlspecialchars((string)($todayLog['notes'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" style="width:100%; padding:12px; border:2px solid #e9ecef; border-radius:12px;">
                    </div>
                    <div style="grid-column:1/-1;">
                        <button type="submit" class="btn-submit" style="background:linear-gradient(135deg,#2e7d32,#4caf50); color:#fff; border:0; padding:12px 16px; border-radius:12px; font-weight:800; cursor:pointer;">Enregistrer</button>
                    </div>
                </form>
            </div>

            <?php if(!empty($recommendations ?? [])): ?>
                <div class="coming-soon" style="max-width: 900px; text-align:left; margin-top:16px;">
                    <h2 style="margin-bottom: 10px;">Conseils du jour</h2>
                    <ul style="margin:0; padding-left:18px; color:#2c3e2f;">
                        <?php foreach(array_slice($recommendations, 0, 3) as $rec): ?>
                            <li style="margin-bottom:8px;"><?= htmlspecialchars((string)$rec, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="coming-soon" style="max-width: 900px; text-align:left; margin-top:16px;">
                <h2 style="margin-bottom: 10px;">Historique (7 jours)</h2>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; color:#2e7d32;">
                            <th style="padding:10px; border-bottom:1px solid #e9ecef;">Date</th>
                            <th style="padding:10px; border-bottom:1px solid #e9ecef;">Poids</th>
                            <th style="padding:10px; border-bottom:1px solid #e9ecef;">Kcal</th>
                            <th style="padding:10px; border-bottom:1px solid #e9ecef;">P/G/L</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($history ?? [])): ?>
                            <tr><td colspan="4" style="padding:10px;">Aucune donnée.</td></tr>
                        <?php else: ?>
                            <?php foreach($history as $h): ?>
                                <tr>
                                    <td style="padding:10px; border-bottom:1px solid #f1f3f5;"><?= htmlspecialchars($h['day'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td style="padding:10px; border-bottom:1px solid #f1f3f5;"><?= htmlspecialchars((string)($h['weight_kg'] ?? '-'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td style="padding:10px; border-bottom:1px solid #f1f3f5;"><?= htmlspecialchars((string)($h['calories_consumed'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                    <td style="padding:10px; border-bottom:1px solid #f1f3f5;"><?= htmlspecialchars((string)($h['protein_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> / <?= htmlspecialchars((string)($h['carbs_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?> / <?= htmlspecialchars((string)($h['fat_g'] ?? 0), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="coming-soon" style="max-width: 900px; text-align:left; margin-top:16px;">
                <h2 style="margin-bottom: 10px;">Évolution (7 jours)</h2>
                <canvas id="trendChart" width="860" height="220" style="width:100%; height:auto; background:#fff; border:1px solid #e9ecef; border-radius:16px;"></canvas>
                <p style="margin-top:10px; color:#6c757d; font-size:0.95rem;">
                    Astuce: enregistrez votre poids chaque jour pour voir une courbe plus précise.
                </p>
            </div>
        <?php endif; ?>

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

    <?php
        $chartPoints = [];
        foreach (array_reverse($history ?? []) as $h) {
            $chartPoints[] = [
                'day' => (string)($h['day'] ?? ''),
                'weight' => $h['weight_kg'] !== null ? (float)$h['weight_kg'] : null,
                'cal' => (int)($h['calories_consumed'] ?? 0),
            ];
        }
    ?>
    <script>
        (function () {
            const canvas = document.getElementById('trendChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            const points = <?= json_encode($chartPoints, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
            if (!Array.isArray(points) || points.length === 0) return;

            // Canvas helpers
            const w = canvas.width, h = canvas.height;
            const pad = 36;
            ctx.clearRect(0, 0, w, h);
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, w, h);

            // Extract series
            const weights = points.map(p => (p.weight == null ? null : Number(p.weight))).filter(v => Number.isFinite(v));
            const cals = points.map(p => Number(p.cal || 0));
            const minW = weights.length ? Math.min(...weights) : 0;
            const maxW = weights.length ? Math.max(...weights) : 0;
            const minC = Math.min(...cals);
            const maxC = Math.max(...cals);

            function xAt(i) {
                if (points.length === 1) return pad;
                return pad + (i * (w - pad * 2)) / (points.length - 1);
            }
            function yFor(val, min, max) {
                if (!Number.isFinite(val)) return null;
                const range = (max - min) || 1;
                const t = (val - min) / range;
                return (h - pad) - t * (h - pad * 2);
            }

            // Grid
            ctx.strokeStyle = '#eef2f7';
            ctx.lineWidth = 1;
            for (let i = 0; i < 4; i++) {
                const y = pad + (i * (h - pad * 2)) / 3;
                ctx.beginPath();
                ctx.moveTo(pad, y);
                ctx.lineTo(w - pad, y);
                ctx.stroke();
            }

            // Calories line (green, scaled independently)
            ctx.strokeStyle = '#2e7d32';
            ctx.lineWidth = 2;
            ctx.beginPath();
            points.forEach((p, i) => {
                const x = xAt(i);
                const y = yFor(Number(p.cal || 0), minC, maxC);
                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            });
            ctx.stroke();

            // Weight dots (blue)
            if (weights.length) {
                ctx.fillStyle = '#1976d2';
                points.forEach((p, i) => {
                    if (p.weight == null) return;
                    const x = xAt(i);
                    const y = yFor(Number(p.weight), minW, maxW);
                    ctx.beginPath();
                    ctx.arc(x, y, 3.5, 0, Math.PI * 2);
                    ctx.fill();
                });
            }

            // X labels
            ctx.fillStyle = '#6c757d';
            ctx.font = '12px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
            points.forEach((p, i) => {
                if (i === 0 || i === points.length - 1 || i === Math.floor(points.length / 2)) {
                    const x = xAt(i);
                    const label = String(p.day || '').slice(5); // MM-DD
                    ctx.fillText(label, x - 16, h - 12);
                }
            });

            // Legend
            ctx.fillStyle = '#2c3e2f';
            ctx.font = '13px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
            ctx.fillText('Calories', pad, 18);
            ctx.fillStyle = '#2e7d32';
            ctx.fillRect(pad + 70, 8, 18, 3);
            ctx.fillStyle = '#2c3e2f';
            ctx.fillText('Poids', pad + 110, 18);
            ctx.fillStyle = '#1976d2';
            ctx.beginPath();
            ctx.arc(pad + 156, 12, 4, 0, Math.PI * 2);
            ctx.fill();
        })();
    </script>
</body>
</html>
<?php endif; ?>