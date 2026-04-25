<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="views/assets/css/motpasse.css">
    <style>
        .alert-success {background:#d4edda;color:#155724;padding:10px;margin:10px 0;}
        .alert-error {background:#f8d7da;color:#721c24;padding:10px;margin:10px 0;}
        .card {padding:20px;border:1px solid #ccc;border-radius:5px;}
        .container {max-width:400px;margin:50px auto;}
        label {display:block;margin-top:10px;}
        input {width:100%;padding:8px;margin-top:5px;}
        button {margin-top:15px;padding:10px;width:100%;}
    </style>
</head>
<body>
<div class="container">
<div class="card">

<h1>Mot de passe oublié</h1>

<!-- Messages -->
<?php if(isset($_SESSION['success'])): ?>
<div class="alert-success">
    <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
<div class="alert-error">
    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<?php $step = $_GET['step'] ?? 'request'; ?>

<!-- ===================== -->
<!-- STEP 1: Demande email -->
<!-- ===================== -->
<?php if($step === 'request'): ?>
<form action="index.php?page=motpasse" method="POST">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="action" value="request_reset">

    <label for="email">Votre email :</label>
    <input type="email" id="email" name="email" placeholder="exemple@domaine.com" required>

    <button type="submit">Envoyer le code</button>
</form>

<!-- ===================== -->
<!-- STEP 2: Vérification -->
<!-- ===================== -->
<?php elseif($step === 'verify' && isset($_SESSION['reset_verify'])): ?>
<form action="index.php?page=motpasse&step=verify" method="POST" novalidate>
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="action" value="reset_password">

    <label for="code">Code reçu :</label>
    <input type="text" id="code" name="code" maxlength="6" placeholder="123456" required>

    <label for="new_password">Nouveau mot de passe :</label>
    <input type="password" id="new_password" name="new_password" placeholder="********" required>

    <button type="submit">Changer le mot de passe</button>
</form>
<?php endif; ?>

<a href="index.php?page=login">Retour</a>

</div>
</div>
</body>
</html>
