<?php
// Navigation dynamique - visible sur toutes les pages
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
$currentPage = $_GET['page'] ?? 'home';
?>

<nav class="navbar">
    <div class="logo">
        <span class="logo-icon">🌿</span>
        <span class="logo-text">NutriWise</span>
    </div>
    <div class="nav-links">
        <a href="index.php?page=home" class="nav-link <?php echo $currentPage == 'home' ? 'active' : ''; ?>">Accueil</a>
        
        <?php if($isLoggedIn): ?>
            <a href="index.php?page=aliments" class="nav-link <?php echo $currentPage == 'aliments' ? 'active' : ''; ?>">Aliments</a>
            <a href="index.php?page=recettes" class="nav-link <?php echo $currentPage == 'recettes' ? 'active' : ''; ?>">Recettes</a>
            <a href="index.php?page=nutrition_plans" class="nav-link <?php echo in_array($currentPage, ['nutrition_plans','nutrition_plan_details']) ? 'active' : ''; ?>">Plannings</a>
            <a href="index.php?page=suivi" class="nav-link <?php echo $currentPage == 'suivi' ? 'active' : ''; ?>">Suivi</a>
        <?php else: ?>
            <a href="#" class="nav-link disabled" style="opacity:0.5; cursor:not-allowed;" onclick="return false;">Aliments</a>
            <a href="#" class="nav-link disabled" style="opacity:0.5; cursor:not-allowed;" onclick="return false;">Recettes</a>
            <a href="#" class="nav-link disabled" style="opacity:0.5; cursor:not-allowed;" onclick="return false;">Plans</a>
            <a href="#" class="nav-link disabled" style="opacity:0.5; cursor:not-allowed;" onclick="return false;">Suivi</a>
        <?php endif; ?>
        
        <?php if($isAdmin): ?>
            <a href="index.php?page=admin_dashboard" class="nav-link">Admin</a>
        <?php endif; ?>
    </div>
    
    <div class="auth-buttons">
        <?php if($isLoggedIn): ?>
            <div class="user-menu">
                <span class="user-greeting">👋 <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur'); ?></span>
                <a href="index.php?page=profile" class="btn-profile">Mon Profil</a>
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

.user-greeting {
    color: #2e7d32;
    font-weight: 500;
    background: #e8f5e9;
    padding: 0.5rem 1rem;
    border-radius: 50px;
}

.btn-profile {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-profile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46,125,50,0.3);
}

.btn-logout {
    background: transparent;
    border: 2px solid #dc3545;
    color: #dc3545;
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-logout:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
}

.disabled {
    pointer-events: none;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .user-menu {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .user-greeting {
        font-size: 0.9rem;
        padding: 0.3rem 0.8rem;
    }
}
</style>