<?php
// cron/remind.php
// ============================================================
// Script cron — Rappels intelligents NutriWise
//
// XAMPP  : Planificateur de tâches Windows (Task Scheduler)
//   → Déclencheur : tous les jours à 20h00
//   → Action      : C:\xampp\php\php.exe C:\xampp\htdocs\NutriWise\cron\remind.php
//
// Linux/Mac (crontab -e) :
//   0 20 * * * /usr/bin/php /var/www/html/NutriWise/cron/remind.php >> /var/log/nutriwise_cron.log 2>&1
//
// Test manuel en navigateur (décommenter la ligne CLI check) :
//   http://localhost/NutriWise/cron/remind.php?cron_key=NW_SECRET_2024
// ============================================================

define('NUTRIWISE_ROOT', dirname(__DIR__));

// Sécurité : autoriser uniquement CLI ou requête avec clé secrète
$isCli = (php_sapi_name() === 'cli');
$isWeb = isset($_GET['cron_key']) && $_GET['cron_key'] === 'NW_SECRET_2024';

if (!$isCli && !$isWeb) {
    http_response_code(403);
    exit("Accès refusé.\n");
}

require_once NUTRIWISE_ROOT . '/models/Database.php';
require_once NUTRIWISE_ROOT . '/models/NotificationModel.php';

// ── Connexion directe PDO pour les requêtes complexes du cron ──
$database = new Database();
$pdo      = $database->getConnection();
$notifModel = new NotificationModel();

$totalNotified = 0;
$log = [];

echo date('[Y-m-d H:i:s]') . " Démarrage du cron NutriWise\n";

// ══════════════════════════════════════════════════════════════
// RÈGLE 1 — Rappel suivi manquant (pas de suivi depuis 48h)
// ══════════════════════════════════════════════════════════════
$rule = 'rappel_suivi';
$users = $pdo->query(
    "SELECT u.id, u.prenom, up.notif_rappel_suivi
     FROM users u
     LEFT JOIN user_preferences up ON up.user_id = u.id
     WHERE u.statut = 'actif'
       AND u.role   = 'user'
       AND (up.notif_rappel_suivi IS NULL OR up.notif_rappel_suivi = 1)
       AND NOT EXISTS (
           SELECT 1 FROM suivis s
           WHERE s.user_id = u.id
             AND s.date_suivi >= CURDATE() - INTERVAL 2 DAY
       )"
)->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($users as $u) {
    $sent = $notifModel->create(
        $u['id'],
        $rule,
        "N'oubliez pas votre suivi !",
        "Bonjour {$u['prenom']}, vous n'avez pas enregistré de suivi depuis plus de 48h. "
        . "Quelques secondes suffisent pour noter votre poids et votre état du jour !"
    );
    if ($sent) { $count++; }
}
$notifModel->logCron($rule, $count);
$totalNotified += $count;
echo "  ✓ [$rule] → $count notification(s)\n";

// ══════════════════════════════════════════════════════════════
// RÈGLE 2 — Hydratation insuffisante (eau < objectif personnalisé)
// ══════════════════════════════════════════════════════════════
$rule = 'eau_insuffisante';
$users = $pdo->query(
    "SELECT u.id, u.prenom, s.eau_bue_du_jour,
            COALESCE(up.objectif_eau_journalier, 1.5) AS objectif_eau
     FROM users u
     JOIN suivis s ON s.user_id = u.id
     LEFT JOIN user_preferences up ON up.user_id = u.id
     WHERE u.statut = 'actif'
       AND u.role   = 'user'
       AND (up.notif_eau IS NULL OR up.notif_eau = 1)
       AND s.date_suivi = CURDATE()
       AND s.eau_bue_du_jour < COALESCE(up.objectif_eau_journalier, 1.5)
     GROUP BY u.id"
)->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($users as $u) {
    $eau      = round($u['eau_bue_du_jour'], 1);
    $objectif = round($u['objectif_eau'], 1);
    $reste    = round($objectif - $eau, 1);

    // Déterminer le niveau d'urgence selon le pourcentage atteint
    $pourcentage = ($eau / $objectif) * 100;
    if ($pourcentage < 50) {
        $urgence = "urgent";
        $titre = "🚨 Hydratation très insuffisante !";
        $message = "Bonjour {$u['prenom']}, vous n'avez bu que {$eau} L sur votre objectif de {$objectif} L ! " .
                  "Il vous reste {$reste} L à boire. Votre santé en dépend, hydratez-vous maintenant ! 💧";
    } elseif ($pourcentage < 75) {
        $urgence = "modere";
        $titre = "⚠️ Hydratation insuffisante";
        $message = "Bonjour {$u['prenom']}, vous avez bu {$eau} L aujourd'hui. " .
                  "Il vous reste {$reste} L pour atteindre votre objectif de {$objectif} L. Pensez à votre carafe !";
    } else {
        $urgence = "leger";
        $titre = "💧 Petit rappel d'hydratation";
        $message = "Bonjour {$u['prenom']}, vous approchez de votre objectif ! " .
                  "Vous avez bu {$eau} L, il ne vous reste que {$reste} L pour atteindre les {$objectif} L recommandés.";
    }

    $sent = $notifModel->create(
        $u['id'],
        $rule . '_' . $urgence,
        $titre,
        $message
    );
    if ($sent) { $count++; }
}
$notifModel->logCron($rule, $count);
$totalNotified += $count;
echo "  ✓ [$rule] → $count notification(s)\n";

// ══════════════════════════════════════════════════════════════
// RÈGLE 2.1 — Aucun suivi d'eau aujourd'hui (rappel urgent)
// ══════════════════════════════════════════════════════════════
$rule = 'aucun_suivi_eau';
$users = $pdo->query(
    "SELECT u.id, u.prenom, COALESCE(up.objectif_eau_journalier, 1.5) AS objectif_eau
     FROM users u
     LEFT JOIN user_preferences up ON up.user_id = u.id
     WHERE u.statut = 'actif'
       AND u.role   = 'user'
       AND (up.notif_eau IS NULL OR up.notif_eau = 1)
       AND NOT EXISTS (
           SELECT 1 FROM suivis s
           WHERE s.user_id = u.id
             AND s.date_suivi = CURDATE()
             AND s.eau_bue_du_jour > 0
       )"
)->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($users as $u) {
    $objectif = round($u['objectif_eau'], 1);
    $sent = $notifModel->create(
        $u['id'],
        $rule,
        "🚨 N'oubliez pas de boire de l'eau !",
        "Bonjour {$u['prenom']}, vous n'avez pas encore enregistré votre consommation d'eau aujourd'hui. " .
        "Votre objectif journalier est de {$objectif} L. Pensez à noter vos verres d'eau dans votre suivi ! 💧"
    );
    if ($sent) { $count++; }
}
$notifModel->logCron($rule, $count);
$totalNotified += $count;
echo "  ✓ [$rule] → $count notification(s)\n";

// ══════════════════════════════════════════════════════════════
// RÈGLE 3 — Objectif atteint (jour_reussi = 1 aujourd'hui)
// ══════════════════════════════════════════════════════════════
$rule = 'objectif_atteint';
$users = $pdo->query(
    "SELECT u.id, u.prenom
     FROM users u
     JOIN suivis s ON s.user_id = u.id AND s.date_suivi = CURDATE()
     LEFT JOIN user_preferences up ON up.user_id = u.id
     WHERE u.statut = 'actif'
       AND u.role   = 'user'
       AND (up.notif_objectif IS NULL OR up.notif_objectif = 1)
       AND s.jour_reussi = 1"
)->fetchAll(PDO::FETCH_ASSOC);

$bravo = [
    "Incroyable, continuez comme ça !",
    "Vous êtes sur la bonne voie !",
    "La constance, c'est la clé du succès !",
    "Votre corps vous remercie !",
];
$count = 0;
foreach ($users as $u) {
    $msg   = $bravo[array_rand($bravo)];
    $sent  = $notifModel->create(
        $u['id'],
        $rule,
        "Bravo, objectif atteint !",
        "Félicitations {$u['prenom']} ! Vous avez coché « journée réussie » aujourd'hui. $msg"
    );
    if ($sent) { $count++; }
}
$notifModel->logCron($rule, $count);
$totalNotified += $count;
echo "  ✓ [$rule] → $count notification(s)\n";

// ══════════════════════════════════════════════════════════════
// RÈGLE 4 — Objectif manqué (3 jours sans jour_reussi)
// ══════════════════════════════════════════════════════════════
$rule = 'objectif_manque';
$users = $pdo->query(
    "SELECT u.id, u.prenom,
            SUM(s.jour_reussi) AS reussites,
            COUNT(s.id)        AS nb_suivis
     FROM users u
     JOIN suivis s ON s.user_id = u.id
     LEFT JOIN user_preferences up ON up.user_id = u.id
     WHERE u.statut = 'actif'
       AND u.role   = 'user'
       AND (up.notif_objectif IS NULL OR up.notif_objectif = 1)
       AND s.date_suivi >= CURDATE() - INTERVAL 3 DAY
     GROUP BY u.id
     HAVING nb_suivis >= 3 AND reussites = 0"
)->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($users as $u) {
    $sent = $notifModel->create(
        $u['id'],
        $rule,
        "Vous pouvez faire mieux !",
        "Bonjour {$u['prenom']}, vous n'avez pas atteint votre objectif depuis 3 jours. "
        . "Pas de panique, chaque jour est une nouvelle chance ! Consultez votre plan nutritionnel."
    );
    if ($sent) { $count++; }
}
$notifModel->logCron($rule, $count);
$totalNotified += $count;
echo "  ✓ [$rule] → $count notification(s)\n";

// ══════════════════════════════════════════════════════════════
// RÈGLE 5 — Alerte poids (variation > 2 kg en 7 jours)
// ══════════════════════════════════════════════════════════════
$rule = 'poids_alerte';
$users = $pdo->query(
    "SELECT u.id, u.prenom,
            MIN(s.poids) AS poids_min,
            MAX(s.poids) AS poids_max,
            (MAX(s.poids) - MIN(s.poids)) AS variation
     FROM users u
     JOIN suivis s ON s.user_id = u.id
     LEFT JOIN user_preferences up ON up.user_id = u.id
     WHERE u.statut = 'actif'
       AND u.role   = 'user'
       AND (up.notif_poids_alerte IS NULL OR up.notif_poids_alerte = 1)
       AND s.date_suivi >= CURDATE() - INTERVAL 7 DAY
     GROUP BY u.id
     HAVING variation > 2"
)->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($users as $u) {
    $variation = round($u['variation'], 1);
    $sent = $notifModel->create(
        $u['id'],
        $rule,
        "Variation de poids inhabituell",
        "Bonjour {$u['prenom']}, votre poids a varié de {$variation} kg cette semaine "
        . "(de {$u['poids_min']} à {$u['poids_max']} kg). "
        . "Pensez à en parler lors de votre prochaine consultation."
    );
    if ($sent) { $count++; }
}
$notifModel->logCron($rule, $count);
$totalNotified += $count;
echo "  ✓ [$rule] → $count notification(s)\n";

// ══════════════════════════════════════════════════════════════
// RÈGLE 6 — Encouragement (7 jours consécutifs de suivi)
// ══════════════════════════════════════════════════════════════
$rule = 'encouragement';
$users = $pdo->query(
    "SELECT u.id, u.prenom, COUNT(DISTINCT s.date_suivi) AS streak
     FROM users u
     JOIN suivis s ON s.user_id = u.id
     LEFT JOIN user_preferences up ON up.user_id = u.id
     WHERE u.statut = 'actif'
       AND u.role   = 'user'
       AND (up.notif_encouragement IS NULL OR up.notif_encouragement = 1)
       AND s.date_suivi >= CURDATE() - INTERVAL 6 DAY
     GROUP BY u.id
     HAVING streak = 7"
)->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($users as $u) {
    $sent = $notifModel->create(
        $u['id'],
        $rule,
        "7 jours de suite, bravo !",
        "Waouh {$u['prenom']} ! Vous avez enregistré votre suivi 7 jours d'affilée. "
        . "Cette régularité est votre meilleur allié pour atteindre vos objectifs. Continuez !"
    );
    if ($sent) { $count++; }
}
$notifModel->logCron($rule, $count);
$totalNotified += $count;
echo "  ✓ [$rule] → $count notification(s)\n";

// ══════════════════════════════════════════════════════════════
// RÈGLE 7 — Consultation à venir dans 2 jours
// ══════════════════════════════════════════════════════════════
$rule = 'consultation_a_venir';
$users = $pdo->query(
    "SELECT u.id, u.prenom, c.date_consultation
     FROM users u
     JOIN consultations c ON c.user_id = u.id
     LEFT JOIN user_preferences up ON up.user_id = u.id
     WHERE u.statut = 'actif'
       AND u.role   = 'user'
       AND (up.notif_consultation IS NULL OR up.notif_consultation = 1)
       AND c.date_consultation = CURDATE() + INTERVAL 2 DAY"
)->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($users as $u) {
    $dateFormatted = date('d/m/Y', strtotime($u['date_consultation']));
    $sent = $notifModel->create(
        $u['id'],
        $rule,
        "Consultation dans 2 jours",
        "Rappel {$u['prenom']} : vous avez une consultation prévue le {$dateFormatted}. "
        . "Pensez à noter vos ressentis et questions pour votre nutritionniste !"
    );
    if ($sent) { $count++; }
}
$notifModel->logCron($rule, $count);
$totalNotified += $count;
echo "  ✓ [$rule] → $count notification(s)\n";

// ──────────────────────────────────────────────
echo date('[Y-m-d H:i:s]') . " Cron terminé — $totalNotified notification(s) envoyée(s)\n";