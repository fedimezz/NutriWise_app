<?php
// views/back/add_user.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="views/assets/css/admin-global.css">

</head>
<body>
<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>
        <nav>
            <a href="index.php?page=admin_dashboard">📊 Dashboard</a>
            <a href="index.php?page=admin_users" class="active">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes">📖 Recettes</a>
            <a href="index.php?page=admin_plannings">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1><i class="fas fa-user-plus"></i> Ajouter un utilisateur</h1>
            <a href="index.php?page=admin_users" class="btn-cancel" style="padding:8px 16px;">← Retour</a>
        </header>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="index.php?page=admin_add_user" method="POST" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom <span class="required">*</span></label>
                        <input type="text" name="prenom" required>
                    </div>
                    <div class="form-group">
                        <label>Nom <span class="required">*</span></label>
                        <input type="text" name="nom" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email <small>(optionnel)</small></label>
                        <input type="email" name="email" placeholder="Laisser vide pour générer automatiquement">
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Mot de passe <small>(optionnel)</small></label>
                        <input type="text" name="password" placeholder="Laisser vide pour générer un mot de passe (8 caractères)">
                    </div>
                    <div class="form-group">
                        <label>Rôle</label>
                        <select name="role">
                            <option value="user">👤 Utilisateur</option>
                            <option value="nutritionist">🍎 Nutritionniste</option>
                            <?php if(($_SESSION['user_role'] ?? '') === 'owner'): ?>
                                <option value="admin">⚙️ Administrateur</option>
                                <option value="owner">👑 Owner</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-user-plus"></i> Créer l'utilisateur</button>
                    <a href="index.php?page=admin_users" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>