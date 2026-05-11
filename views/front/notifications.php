<?php
// views/front/notifications.php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Notifications - NutriWise</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        /* ── Layout ── */
        .page-header { text-align: center; padding: 2rem 2rem 1rem; }
        .page-title {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            -webkit-background-clip: text; background-clip: text; color: transparent;
            margin-bottom: .5rem;
        }
        .page-subtitle { color: #6c757d; }

        .notif-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 2rem;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1.5rem 3rem;
        }
        @media (max-width: 860px) { .notif-layout { grid-template-columns: 1fr; } }

        /* ── Toolbar ── */
        .notif-toolbar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.25rem; flex-wrap: wrap; gap: .75rem;
        }
        .notif-count { font-size: .9rem; color: #6c757d; }
        .notif-count strong { color: #2e7d32; }
        .btn-mark-all {
            background: none; border: 2px solid #2e7d32; color: #2e7d32;
            padding: .4rem 1rem; border-radius: 50px; font-size: .83rem;
            font-weight: 600; cursor: pointer; text-decoration: none; transition: all .2s;
        }
        .btn-mark-all:hover { background: #2e7d32; color: white; }

        /* ── Alert ── */
        .alert { padding: .9rem 1.2rem; border-radius: 14px; margin-bottom: 1.25rem; font-size: .9rem; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error   { background: #f8d7da; color: #721c24; }

        /* ── Carte notification ── */
        .notif-list { display: flex; flex-direction: column; gap: .75rem; }
        .notif-card {
            background: white; border-radius: 18px; padding: 1.1rem 1.3rem;
            display: flex; align-items: flex-start; gap: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
            transition: transform .2s, box-shadow .2s;
            position: relative; border-left: 4px solid transparent;
        }
        .notif-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.09); }
        .notif-card.unread { border-left-color: #4caf50; background: #f9fff9; }

        .unread-dot {
            position: absolute; top: 1rem; right: 1rem;
            width: 9px; height: 9px; border-radius: 50%; background: #4caf50;
        }
        .notif-icon {
            width: 44px; height: 44px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; flex-shrink: 0;
        }
        .icon-rappel_suivi       { background: #e8eaf6; }
        .icon-eau_insuffisante   { background: #e3f2fd; }
        .icon-objectif_atteint   { background: #e8f5e9; }
        .icon-objectif_manque    { background: #fff3e0; }
        .icon-poids_alerte       { background: #fce4ec; }
        .icon-encouragement      { background: #f3e5f5; }
        .icon-consultation_a_venir { background: #fff8e1; }

        .notif-body { flex: 1; min-width: 0; }
        .notif-title  { font-size: .95rem; font-weight: 600; color: #2c3e2f; margin-bottom: .2rem; }
        .notif-message{ font-size: .84rem; color: #6c757d; line-height: 1.5; margin-bottom: .4rem; }
        .notif-meta   { font-size: .75rem; color: #adb5bd; }

        .notif-actions { display: flex; gap: .4rem; flex-shrink: 0; align-items: center; }
        .btn-icon {
            background: none; border: none; cursor: pointer; padding: .3rem;
            border-radius: 8px; font-size: .95rem; transition: background .2s;
            text-decoration: none; display: inline-flex;
        }
        .btn-icon:hover { background: #f0f0f0; }
        .btn-read   { color: #2e7d32; }
        .btn-delete { color: #e53935; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 3.5rem 2rem; background: white; border-radius: 24px; }
        .empty-state .ei { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 { color: #2c3e2f; margin-bottom: .4rem; }
        .empty-state p  { color: #6c757d; }

        /* ── Panneau préférences ── */
        .prefs-panel {
            background: white; border-radius: 22px; padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,.06); align-self: start;
        }
        .prefs-panel h3 { color: #2e7d32; margin-bottom: 1.2rem; font-size: 1.1rem; }
        .pref-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: .65rem 0; border-bottom: 1px solid #f0f0f0; gap: .75rem;
        }
        .pref-row:last-of-type { border-bottom: none; }
        .pref-label { font-size: .88rem; color: #2c3e2f; display: flex; align-items: center; gap: .5rem; }
        /* Toggle switch */
        .toggle { position: relative; width: 40px; height: 22px; flex-shrink: 0; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; inset: 0; background: #ccc;
            border-radius: 22px; cursor: pointer; transition: .3s;
        }
        .toggle-slider:before {
            content: ''; position: absolute;
            width: 16px; height: 16px; left: 3px; bottom: 3px;
            background: white; border-radius: 50%; transition: .3s;
        }
        .toggle input:checked + .toggle-slider { background: #4caf50; }
        .toggle input:checked + .toggle-slider:before { transform: translateX(18px); }

        .pref-time { font-size: .85rem; }
        .pref-time label { display: block; color: #6c757d; margin-bottom: .3rem; }
        .pref-time input[type="time"] {
            width: 100%; padding: .5rem .75rem; border: 1px solid #e0e0e0;
            border-radius: 10px; font-family: inherit; font-size: .9rem;
        }
        .btn-save-prefs {
            width: 100%; margin-top: 1.2rem; padding: .75rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white; border: none; border-radius: 12px;
            font-weight: 600; font-size: .9rem; cursor: pointer;
        }

        /* ── Cron info box ── */
        .cron-info {
            margin-top: 1.2rem; padding: 1rem; background: #f8f9fa;
            border-radius: 14px; font-size: .8rem; color: #6c757d;
        }
        .cron-info code {
            display: block; margin-top: .5rem; padding: .4rem .6rem;
            background: #e9ecef; border-radius: 6px; word-break: break-all;
            font-size: .76rem;
        }
    </style>
</head>
<body>
<div class="container">
    <?php include_once 'partials/navbar.php'; ?>

    <div class="page-header">
        <h1 class="page-title">🔔 Notifications</h1>
        <p class="page-subtitle">Vos alertes et rappels NutriWise</p>
    </div>

    <div class="notif-layout">

        <!-- ── Colonne principale : liste ── -->
        <div>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <?php
                $unreadList  = array_filter($notifications ?? [], fn($n) => !$n['lu']);
                $total       = count($notifications ?? []);
                $unreadCount = count($unreadList);
            ?>

            <div class="notif-toolbar">
                <div class="notif-count">
                    <strong><?= $total ?></strong> notification<?= $total > 1 ? 's' : '' ?>
                    <?php if ($unreadCount > 0): ?>
                        · <strong><?= $unreadCount ?></strong> non lue<?= $unreadCount > 1 ? 's' : '' ?>
                    <?php endif; ?>
                </div>
                <?php if ($unreadCount > 0): ?>
                    <a href="index.php?page=notifications_read_all" class="btn-mark-all">✓ Tout marquer comme lu</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($notifications)): ?>
                <div class="notif-list">
                    <?php
                        $icons = [
                            'rappel_suivi'              => '📋',
                            'objectif_atteint'          => '🎉',
                            'objectif_manque'           => '📉',
                            'eau_insuffisante_leger'    => '💧',
                            'eau_insuffisante_modere'   => '⚠️',
                            'eau_insuffisante_urgent'   => '🚨',
                            'aucun_suivi_eau'           => '🚨',
                            'poids_alerte'              => '⚖️',
                            'encouragement'             => '💪',
                            'consultation_a_venir'      => '📅',
                        ];
                        foreach ($notifications as $n):
                            $isUnread = !$n['lu'];
                            $type     = htmlspecialchars($n['type']);
                            $icon     = $icons[$n['type']] ?? '🔔';
                            $date     = date('d/m/Y à H:i', strtotime($n['created_at']));
                    ?>
                    <div class="notif-card <?= $isUnread ? 'unread' : '' ?>">
                        <?php if ($isUnread): ?><div class="unread-dot"></div><?php endif; ?>

                        <div class="notif-icon icon-<?= $type ?>"><?= $icon ?></div>

                        <div class="notif-body">
                            <div class="notif-title"><?= htmlspecialchars($n['titre']) ?></div>
                            <div class="notif-message"><?= htmlspecialchars($n['message']) ?></div>
                            <div class="notif-meta">
                                <?= $date ?>
                                <?php if ($n['lu'] && $n['lu_le']): ?>
                                    · Lu le <?= date('d/m/Y', strtotime($n['lu_le'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="notif-actions">
                            <?php if ($isUnread): ?>
                                <a href="index.php?page=notification_read&id=<?= $n['id'] ?>"
                                   class="btn-icon btn-read" title="Marquer comme lu">✓</a>
                            <?php endif; ?>
                            <a href="index.php?page=notification_delete&id=<?= $n['id'] ?>"
                               class="btn-icon btn-delete" title="Supprimer"
                               onclick="return confirm('Supprimer cette notification ?')">🗑</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="empty-state">
                    <div class="ei">🔕</div>
                    <h3>Aucune notification</h3>
                    <p>Enregistrez votre suivi quotidien pour en recevoir !</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- ── Colonne droite : préférences ── -->
        <div>
            <div class="prefs-panel">
                <h3>⚙️ Mes préférences</h3>

                <?php $p = $preferences ?? []; ?>
                <form action="index.php?page=save_notif_preferences" method="POST">

                    <?php
                        $types = [
                            'notif_rappel_suivi'    => ['📋', 'Rappel suivi manquant'],
                            'notif_eau'             => ['💧', 'Hydratation insuffisante'],
                            'notif_objectif'        => ['🎯', 'Objectif atteint / manqué'],
                            'notif_poids_alerte'    => ['⚖️', 'Alerte variation poids'],
                            'notif_encouragement'   => ['💪', 'Encouragements streak'],
                            'notif_consultation'    => ['📅', 'Rappel consultation'],
                        ];
                        foreach ($types as $key => [$emoji, $label]):
                            $checked = ($p[$key] ?? 1) ? 'checked' : '';
                    ?>
                    <div class="pref-row">
                        <div class="pref-label">
                            <span style="font-size:14px;"><?= $emoji ?></span>
                            <?= $label ?>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" name="<?= $key ?>" <?= $checked ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <?php endforeach; ?>

                    <div class="pref-time" style="margin-top: .75rem;">
                        <label>Heure du rappel suivi</label>
                        <input type="time" name="heure_rappel_suivi"
                               value="<?= htmlspecialchars($p['heure_rappel_suivi'] ?? '20:00') ?>">
                    </div>

                    <div class="pref-time" style="margin-top: .75rem;">
                        <label>Objectif d'eau journalier (L)</label>
                        <input type="number" step="0.1" min="0.5" max="5.0" name="objectif_eau_journalier"
                               value="<?= htmlspecialchars($p['objectif_eau_journalier'] ?? '1.5') ?>"
                               placeholder="1.5">
                        <small style="color: #6c757d; font-size: 0.8rem; display: block; margin-top: 0.25rem;">
                            Recevez des rappels si vous n'atteignez pas cet objectif
                        </small>
                    </div>

                    <button type="submit" class="btn-save-prefs">Enregistrer</button>
                </form>

                <div class="cron-info">
                    <strong>Info cron</strong> — Le cron s'exécute chaque soir à 20h.<br>
                    Pour le déclencher manuellement :
                    <code>php cron/remind.php</code>
                    ou via :
                    <code>index.php?page=run_cron&cron_key=NW_SECRET_2024</code>
                </div>
            </div>
        </div>

    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <span class="logo-icon">🌿</span><span>NutriWise</span>
            </div>
            <p class="footer-copyright">© 2024 NutriWise - Nutrition intelligente et durable</p>
        </div>
    </footer>
</div>
</body>
</html>