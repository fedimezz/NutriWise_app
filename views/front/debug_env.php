<?php
function mask_value(string $v): string {
    $v = trim($v);
    if ($v === '') return '(empty)';
    if (strlen($v) <= 8) return str_repeat('*', strlen($v));
    return substr($v, 0, 4) . str_repeat('*', max(0, strlen($v) - 8)) . substr($v, -4);
}

$googleId = (string)env_value('GOOGLE_CLIENT_ID', '');
$googleSecret = (string)env_value('GOOGLE_CLIENT_SECRET', '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug env - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>
<div class="container" style="max-width:900px; margin:2rem auto;">
    <h1>Debug env (Owner)</h1>
    <p>Cette page masque les valeurs. Elle sert  PHP lit bien `.env` / variables Apache.</p>

    <div style="background:#fff; border:1px solid #e9ecef; border-radius:16px; padding:16px;">
        <div><strong>GOOGLE_CLIENT_ID</strong>: <?= htmlspecialchars(mask_value($googleId), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
        <div style="margin-top:10px;"><strong>GOOGLE_CLIENT_SECRET</strong>: <?= htmlspecialchars(mask_value($googleSecret), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
    </div>

    <div style="margin-top:16px;">
        <a href="index.php?page=login" style="text-decoration:none;">← Retour login</a>
    </div>
</div>
</body>
</html>

