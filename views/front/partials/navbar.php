<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['user_role'] ?? ROLE_USER;

$isAdmin = function_exists('role_rank') && defined('ROLE_ADMIN')
    ? role_rank((string)$role) >= role_rank(ROLE_ADMIN)
    : false;

$isNutritionist = function_exists('role_rank') && defined('ROLE_NUTRITIONIST')
    ? role_rank((string)$role) >= role_rank(ROLE_NUTRITIONIST)
    : false;

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
            <a href="index.php?page=recettes" class="nav-link <?= in_array($currentPage, ['recettes', 'recette_details'], true) ? 'active' : '' ?>">Recettes</a>
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

            <!-- 🔔 Notification -->
            <div class="notification-box">
                <span class="notif-icon" onclick="toggleNotif()">🔔</span>
                <span id="notif-count" class="notif-count">0</span>

                <div id="notif-dropdown" class="notif-dropdown">
                    <ul id="notif-list"></ul>
                </div>
            </div>

            <div class="user-menu">
                <a href="index.php?page=profile" class="profile-link">
                    <img src="views/uploads/<?= $userImage ?>" 
                         class="nav-avatar"
                         onerror="this.src='views/uploads/default-avatar.png'">
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

/* 🔔 Notifications */
.notification-box {
    position: relative;
    margin-right: 1rem;
    cursor: pointer;
}

.notif-icon {
    font-size: 20px;
}

.notif-count {
    position: absolute;
    top: -5px;
    right: -8px;
    background: red;
    color: white;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 50%;
}

.notif-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 30px;
    background: white;
    width: 250px;
    max-height: 300px;
    overflow-y: auto;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    z-index: 999;
}

.notif-dropdown ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notif-dropdown li {
    padding: 10px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.notif-dropdown li:hover {
    background: #f5f5f5;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const notifIcon = document.querySelector(".notif-icon");
    const dropdown = document.getElementById("notif-dropdown");

    notifIcon.addEventListener("click", function () {
        dropdown.style.display =
            dropdown.style.display === "block" ? "none" : "block";
    });

    function fetchNotifications() {
        fetch("index.php?page=get_notifications")
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById("notif-list");
                const count = document.getElementById("notif-count");

                list.innerHTML = "";

                if (!data || data.length === 0) {
                    list.innerHTML = "<li>Aucune notification</li>";
                    count.innerText = "0";
                    return;
                }

                count.innerText = data.length;

                data.forEach(notif => {
                    const li = document.createElement("li");
                    li.innerText = notif.message;

                    li.addEventListener("click", function () {
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

    setInterval(fetchNotifications, 5000);
    fetchNotifications();

});
</script>