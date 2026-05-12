<?php
// views/back/edit_user.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier utilisateur - NutriWise</title>
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
            <h1><i class="fas fa-user-edit"></i> Modifier l'utilisateur #<?= $userToEdit['id'] ?></h1>
            <a href="index.php?page=admin_users" class="btn-cancel" style="padding:8px 16px;">← Retour</a>
        </header>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-success">✓ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="index.php?page=admin_edit_user&id=<?= $userToEdit['id'] ?>" method="POST" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($userToEdit['prenom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($userToEdit['nom']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($userToEdit['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($userToEdit['telephone'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Statut</label>
                        <select name="statut">
                            <option value="actif" <?= strtolower($userToEdit['statut'] ?? '') == 'actif' ? 'selected' : '' ?>>✅ Actif</option>
                            <option value="inactif" <?= strtolower($userToEdit['statut'] ?? '') == 'inactif' ? 'selected' : '' ?>>❌ Inactif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rôle</label>
                        <select name="role">
                            <option value="user" <?= $userToEdit['role'] == 'user' ? 'selected' : '' ?>>👤 Utilisateur</option>
                            <option value="nutritionist" <?= $userToEdit['role'] == 'nutritionist' ? 'selected' : '' ?>>🍎 Nutritionniste</option>
                            <?php if(($_SESSION['user_role'] ?? '') === 'owner'): ?>
                                <option value="admin" <?= $userToEdit['role'] == 'admin' ? 'selected' : '' ?>>⚙️ Administrateur</option>
                                <option value="owner" <?= $userToEdit['role'] == 'owner' ? 'selected' : '' ?>>👑 Owner</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="index.php?page=admin_users" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>