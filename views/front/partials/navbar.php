<?php
// views/front/partials/navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['user_role'] ?? 'user';

if (!defined('ROLE_USER')) define('ROLE_USER', 'user');
if (!defined('ROLE_NUTRITIONIST')) define('ROLE_NUTRITIONIST', 'nutritionist');
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 'admin');

if (!function_exists('role_rank')) {
    function role_rank($role) {
        $ranks = ['user' => 1, 'nutritionist' => 2, 'admin' => 3];
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
            <a href="index.php?page=nutrition_plans" class="nav-link <?= in_array($currentPage, ['nutrition_plans', 'nutrition_plan_details', 'nutrition_plans_calendar', 'my_schedules'], true) ? 'active' : '' ?>">📅 Plannings</a>
            <a href="index.php?page=suivi" class="nav-link <?= $currentPage == 'suivi' ? 'active' : '' ?>">Suivi</a>
        <?php endif; ?>

        <?php if($isAdmin): ?>
            <a href="index.php?page=admin_dashboard" class="nav-link <?= $currentPage == 'admin_dashboard' ? 'active' : '' ?>">Admin</a>
        <?php endif; ?>

        <?php if($isNutritionist && !$isAdmin): ?>
            <a href="index.php?page=nutritionist_dashboard" class="nav-link <?= $currentPage == 'nutritionist_dashboard' ? 'active' : '' ?>">Nutritionniste</a>
        <?php endif; ?>
    </div>

    <div class="auth-buttons">
        <?php if($isLoggedIn): ?>
            <div class="user-menu">
                <div class="notification-box">
                    <span class="notif-icon" onclick="toggleNotif()">🔔</span>
                    <span id="notif-count" class="notif-count">0</span>
                    <div id="notif-dropdown" class="notif-dropdown">
                        <ul id="notif-list"><li>Aucune notification</li></ul>
                    </div>
                </div>
                <a href="index.php?page=profile" class="profile-link">
                    <img src="views/uploads/<?= htmlspecialchars($userImage) ?>" class="nav-avatar" onerror="this.src='views/uploads/default-avatar.png'">
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
.navbar {
    background: white;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
    padding: 0.8rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    border-radius: 60px;
    margin: 1rem 0;
}
.logo { display: flex; align-items: center; gap: 8px; font-size: 1.4rem; font-weight: 700; color: #2e7d32; }
.logo-text { background: linear-gradient(135deg, #2e7d32, #4caf50); -webkit-background-clip: text; background-clip: text; color: transparent; }
.nav-links { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
.nav-link { text-decoration: none; color: #4a6741; font-weight: 500; padding: 0.5rem 1rem; border-radius: 50px; transition: all 0.3s; }
.nav-link:hover { background: #e8f5e9; color: #2e7d32; }
.nav-link.active { background: #2e7d32; color: white; }
.user-menu { display: flex; align-items: center; gap: 1rem; }
.profile-link { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: #2e7d32; font-weight: 500; }
.nav-avatar { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #4caf50; }
.btn-logout { background: transparent; border: 2px solid #dc3545; color: #dc3545; padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; font-weight: 500; }
.btn-logout:hover { background: #dc3545; color: white; }
.btn-login { color: #2e7d32; border: 2px solid #2e7d32; padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; }
.btn-login:hover { background: #2e7d32; color: white; }
.btn-register { background: linear-gradient(135deg, #2e7d32, #4caf50); color: white; padding: 0.4rem 1rem; border-radius: 50px; text-decoration: none; }
.notification-box { position: relative; cursor: pointer; }
.notif-count { position: absolute; top: -8px; right: -8px; background: #ef4444; color: white; font-size: 0.65rem; padding: 2px 5px; border-radius: 50%; }
.notif-dropdown { display: none; position: absolute; right: 0; top: 35px; background: white; width: 280px; max-height: 350px; overflow-y: auto; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 999; }
.notif-dropdown li { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; cursor: pointer; }
.notif-dropdown li:hover { background: #e8f5e9; }
@media (max-width: 768px) { 
    .navbar { flex-direction: column; text-align: center; }
    .nav-links { justify-content: center; }
    .profile-link span { display: none; }
}
</style>

<script>
// Gestion des notifications
async function fetchNotifications() {
    try {
        const response = await fetch('index.php?page=get_notifications');
        const data = await response.json();
        
        const list = document.getElementById("notif-list");
        const countSpan = document.getElementById("notif-count");
        
        if (!list) return;
        
        list.innerHTML = "";
        
        if (!data || data.length === 0) {
            list.innerHTML = "<li style='text-align:center; color:#7c8e7a;'>📭 Aucune notification</li>";
            if (countSpan) countSpan.innerText = "0";
            return;
        }
        
        if (countSpan) countSpan.innerText = data.length;
        
        data.forEach(notif => {
            const li = document.createElement("li");
            li.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>${notif.message}</span>
                    <button onclick="markAsRead(${notif.id})" style="background:#4caf50; color:white; border:none; border-radius:20px; padding:2px 10px; cursor:pointer;">✓</button>
                </div>
                <small style="color:#999;">${notif.created_at}</small>
            `;
            li.style.padding = "12px";
            li.style.borderBottom = "1px solid #e2e8f0";
            li.style.cursor = "pointer";
            li.onclick = (e) => {
                if (e.target.tagName !== 'BUTTON') {
                    window.location.href = `index.php?page=nutrition_plan_details&id=${notif.planning_id}`;
                }
            };
            list.appendChild(li);
        });
    } catch (err) {
        console.error("Fetch error:", err);
    }
}

async function markAsRead(id) {
    try {
        const response = await fetch('index.php?page=mark_notification', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        });
        const result = await response.json();
        if (result.success) {
            fetchNotifications();
            updateNotificationCount();
        }
    } catch (err) {
        console.error("Mark error:", err);
    }
}

async function updateNotificationCount() {
    try {
        const response = await fetch('index.php?page=get_notification_count');
        const data = await response.json();
        const countSpan = document.getElementById("notif-count");
        if (countSpan && data.count > 0) {
            countSpan.innerText = data.count;
            countSpan.style.display = 'inline-block';
        } else if (countSpan) {
            countSpan.innerText = '0';
            countSpan.style.display = 'none';
        }
    } catch (err) {
        console.error("Count error:", err);
    }
}

function toggleNotif() {
    const dropdown = document.getElementById("notif-dropdown");
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        if (dropdown.style.display === "block") {
            fetchNotifications();
        }
    }
}

document.addEventListener("click", function(event) {
    const notifBox = document.querySelector(".notification-box");
    const dropdown = document.getElementById("notif-dropdown");
    if (notifBox && !notifBox.contains(event.target) && dropdown) {
        dropdown.style.display = "none";
    }
});

document.addEventListener("DOMContentLoaded", function() {
    if (document.getElementById("notif-list")) {
        updateNotificationCount();
        setInterval(updateNotificationCount, 30000);
    }
});
</script>