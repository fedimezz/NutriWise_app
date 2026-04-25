<?php
$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['user_role'] ?? ROLE_USER;
$isAdmin = role_rank((string)$role) >= role_rank(ROLE_ADMIN); // admin or owner
$isNutritionist = role_rank((string)$role) >= role_rank(ROLE_NUTRITIONIST); // nutritionist/admin/owner
$currentPage = $_GET['page'] ?? 'home';
$userImage = $_SESSION['user_image'] ?? 'default-avatar.png';
?>

<nav class="navbar">
    <div class="logo">
        <span class="logo-icon">🌿</span>
        <span class="logo-text">NutriWise</span>
    </div>
    <div class="nav-links">
        <a href="index.php?page=home" class="nav-link <?= $currentPage == 'home' ? 'active' : '' ?>">Accueil</a>
        
        <?php if($isLoggedIn): ?>
            <a href="index.php?page=aliments" class="nav-link <?= $currentPage == 'aliments' ? 'active' : '' ?>">Aliments</a>
            <a href="index.php?page=recettes" class="nav-link <?= $currentPage == 'recettes' ? 'active' : '' ?>">Recettes</a>
            <a href="index.php?page=suivi" class="nav-link <?= $currentPage == 'suivi' ? 'active' : '' ?>">Suivi</a>
        <?php endif; ?>
        
        <?php if($isAdmin): ?>
            <a href="index.php?page=admin_dashboard" class="nav-link">Admin</a>
        <?php endif; ?>

        <?php if($isNutritionist): ?>
            <a href="index.php?page=nutritionist_dashboard" class="nav-link <?= $currentPage == 'nutritionist_dashboard' ? 'active' : '' ?>">Nutritionniste</a>
        <?php endif; ?>
    </div>
    
    <div class="auth-buttons">
        <?php if($isLoggedIn): ?>
            <div class="user-menu">
                <a href="index.php?page=profile" class="profile-link">
                    <img src="views/assets/uploads/<?= $userImage ?>" alt="Photo" class="nav-avatar" 
                         onerror="this.src='views/assets/uploads/default-avatar.png'">
                    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></span>
                </a>
                <a href="index.php?page=logout" class="btn-logout">Déconnexion</a>
            </div>
        <?php else: ?>
            <a href="index.php?page=login" class="btn-login">Connexion</a>
            <a href="index.php?page=register" class="btn-register">Inscription</a>
        <?php endif; ?>
    </div>
</nav>

<style>
.user-menu {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.profile-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: #2e7d32;
    font-weight: 500;
}
.nav-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #4caf50;
}
.btn-logout {
    background: transparent;
    border: 2px solid #dc3545;
    color: #dc3545;
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
}
.btn-logout:hover {
    background: #dc3545;
    color: white;
}
.btn-login, .btn-register {
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
}
.btn-login {
    color: #2e7d32;
    border: 2px solid #2e7d32;
}
.btn-register {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
    color: white;
}
</style>