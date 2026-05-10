<?php
// views/front/partials/navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['user_role'] ?? 'user';

// Définir les constantes de rôles si non définies
if (!defined('ROLE_USER')) define('ROLE_USER', 'user');
if (!defined('ROLE_NUTRITIONIST')) define('ROLE_NUTRITIONIST', 'nutritionist');
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 'admin');

// ✅ Vérifier si la fonction n'existe pas déjà avant de la déclarer
if (!function_exists('role_rank')) {
    function role_rank($role) {
        $ranks = [
            'user' => 1,
            'nutritionist' => 2,
            'admin' => 3
        ];
        return $ranks[$role] ?? 1;
    }
}

$isAdmin = role_rank((string)$role) >= role_rank(ROLE_ADMIN);
$isNutritionist = role_rank((string)$role) >= role_rank(ROLE_NUTRITIONIST);

$currentPage = $_GET['page'] ?? 'home';
$userImage = $_SESSION['user_image'] ?? 'default-avatar.png';
$userName = $_SESSION['user_name'] ?? $_SESSION['prenom'] ?? 'Utilisateur';
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
            <a href="index.php?page=recettes" class="nav-link <?= in_array($currentPage, ['recettes', 'recette_details'], true) ? 'active' : '' ?>">Recettes</a>
            <a href="index.php?page=suivi" class="nav-link <?= $currentPage == 'suivi' ? 'active' : '' ?>">Suivi</a>
        <?php endif; ?>

        <?php if($isAdmin): ?>
            <a href="index.php?page=admin_dashboard" class="nav-link <?= $currentPage == 'admin_dashboard' ? 'active' : '' ?>">
                <i class="fas fa-shield-alt"></i> Admin
            </a>
        <?php endif; ?>

        <?php if($isNutritionist && !$isAdmin): ?>
            <a href="index.php?page=nutritionist_dashboard" class="nav-link <?= $currentPage == 'nutritionist_dashboard' ? 'active' : '' ?>">
                <i class="fas fa-chalkboard-user"></i> Nutritionniste
            </a>
        <?php endif; ?>
    </div>

    <div class="auth-buttons">
        <?php if($isLoggedIn): ?>

            <!-- 🔔 Notification -->
            <div class="notification-box">
                <span class="notif-icon" onclick="toggleNotif()">🔔</span>
                <span id="notif-count" class="notif-count">0</span>
                <div id="notif-dropdown" class="notif-dropdown">
                    <ul id="notif-list">
                        <li>Aucune notification</li>
                    </ul>
                </div>
            </div>

            <div class="user-menu">
                <a href="index.php?page=profile" class="profile-link">
                    <img src="views/uploads/<?= htmlspecialchars($userImage) ?>" 
                         class="nav-avatar"
                         onerror="this.src='views/uploads/default-avatar.png'">
                    <span><?= htmlspecialchars($userName) ?></span>
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
/* ================= NAVBAR STYLES ================= */
.navbar {
    background: white;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.98);
    padding: 0.8rem 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.5rem;
    font-weight: 700;
    color: #2e7d32;
}

.logo-icon {
    font-size: 1.8rem;
}

.logo-text {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.nav-links {
    display: flex;
    gap: 2rem;
    align-items: center;
    flex-wrap: wrap;
}

.nav-link {
    text-decoration: none;
    color: #4a6741;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.nav-link i {
    margin-right: 5px;
}

.nav-link:hover {
    background: #e8f5e9;
    color: #2e7d32;
    transform: translateY(-2px);
}

.nav-link.active {
    background: #2e7d32;
    color: white;
}

.auth-buttons {
    display: flex;
    align-items: center;
    gap: 1rem;
}

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
    transition: all 0.3s ease;
}

.profile-link:hover {
    transform: translateY(-2px);
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
    transition: all 0.3s ease;
}

.btn-logout:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
}

.btn-login, .btn-register {
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-login {
    color: #2e7d32;
    border: 2px solid #2e7d32;
}

.btn-login:hover {
    background: #2e7d32;
    color: white;
    transform: translateY(-2px);
}

.btn-register {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
    color: white;
}

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46,125,50,0.3);
}

/* 🔔 Notifications */
.notification-box {
    position: relative;
    margin-right: 0.5rem;
    cursor: pointer;
}

.notif-icon {
    font-size: 1.3rem;
    color: #4a6741;
    transition: all 0.3s;
}

.notif-icon:hover {
    color: #2e7d32;
    transform: scale(1.1);
}

.notif-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 50%;
    font-weight: bold;
}

.notif-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 35px;
    background: white;
    width: 280px;
    max-height: 350px;
    overflow-y: auto;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    z-index: 999;
}

.notif-dropdown ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notif-dropdown li {
    padding: 12px 15px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.85rem;
    color: #1a3a1a;
    cursor: pointer;
    transition: all 0.2s;
}

.notif-dropdown li:hover {
    background: #e8f5e9;
}

.notif-dropdown li:last-child {
    border-bottom: none;
}

/* Responsive */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        gap: 15px;
        padding: 1rem;
    }
    
    .nav-links {
        justify-content: center;
        gap: 0.5rem;
    }
    
    .nav-link span {
        display: none;
    }
    
    .nav-link i {
        margin-right: 0;
    }
    
    .user-menu span {
        display: none;
    }
    
    .btn-logout, .btn-login, .btn-register {
        padding: 0.4rem 1rem;
        font-size: 0.9rem;
    }
}
</style>

<script>
function toggleNotif() {
    const dropdown = document.getElementById("notif-dropdown");
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }
}

// Fermer le dropdown en cliquant ailleurs
document.addEventListener("click", function(event) {
    const notifBox = document.querySelector(".notification-box");
    const dropdown = document.getElementById("notif-dropdown");
    
    if (notifBox && !notifBox.contains(event.target)) {
        if (dropdown) dropdown.style.display = "none";
    }
});

function fetchNotifications() {
    fetch("index.php?page=get_notifications")
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById("notif-list");
            const count = document.getElementById("notif-count");

            if (!list) return;
            list.innerHTML = "";

            if (!data || data.length === 0) {
                list.innerHTML = "<li>Aucune notification</li>";
                if (count) count.innerText = "0";
                return;
            }

            if (count) count.innerText = data.length;

            data.forEach(notif => {
                const li = document.createElement("li");
                li.innerText = notif.message;
                li.addEventListener("click", function() {
                    markAsRead(notif.id);
                });
                list.appendChild(li);
            });
        })
        .catch(err => console.error("Fetch error:", err));
}

function markAsRead(id) {
    fetch("index.php?page=mark_notification", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "id=" + id
    })
    .then(res => res.json())
    .then(() => fetchNotifications())
    .catch(err => console.error("Mark error:", err));
}

// Charger les notifications au chargement de la page
document.addEventListener("DOMContentLoaded", function() {
    if (document.getElementById("notif-list")) {
        fetchNotifications();
        setInterval(fetchNotifications, 30000); // Rafraîchir toutes les 30 secondes
    }
});
</script>