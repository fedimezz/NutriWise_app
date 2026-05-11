<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Calendrier des plannings - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .calendar-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .calendar-header button {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .calendar-header button:hover {
            background: #1b5e20;
        }

        .calendar-month {
            font-size: 24px;
            font-weight: 700;
            color: #1B4D1B;
        }

        .weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }

        .weekday {
            text-align: center;
            font-weight: 600;
            color: #666;
            padding: 12px;
            font-size: 14px;
        }

        .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .day {
            aspect-ratio: 1;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 8px;
            text-align: center;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            min-height: 100px;
            display: flex;
            flex-direction: column;
        }

        .day:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
        }

        .day.other-month {
            background: #f5f5f5;
            color: #ccc;
        }

        .day.today {
            background: #c8e6c9;
            border-color: #2e7d32;
        }

        .day-number {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 4px;
            color: #333;
        }

        .day-plannings {
            font-size: 11px;
            overflow: hidden;
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            align-content: flex-start;
        }

        .planning-badge {
            background: #2e7d32;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .planning-badge.active {
            background: #43a047;
        }

        .planning-badge.completed {
            background: #80cbc4;
        }

        .notifications-panel {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }

        .notifications-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .notifications-header h2 {
            margin: 0;
            color: #1B4D1B;
            font-size: 20px;
        }

        .notification-badge {
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }

        .notification-item {
            background: #f5f5f5;
            border-left: 4px solid #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-item.urgent {
            border-left-color: #ff6b6b;
            background: #ffebee;
        }

        .notification-content {
            flex: 1;
        }

        .notification-time {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }

        .notification-actions button {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 8px;
            transition: background 0.3s;
        }

        .notification-actions button:hover {
            background: #1b5e20;
        }

        .empty-state {
            text-align: center;
            padding: 24px;
            color: #999;
        }

        .view-toggle {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }

        .view-toggle button {
            background: #f0f0f0;
            border: 2px solid #f0f0f0;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .view-toggle button.active {
            background: #2e7d32;
            color: white;
            border-color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <section style="padding: 2rem 0 1rem 0;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div>
                    <h1 class="hero-title" style="font-size:2.4rem; margin-bottom:0.5rem;">Mes <span class="highlight">plannings</span></h1>
                    <p class="hero-subtitle" style="margin-bottom:0;">Vue calendrier et notifications de vos programmes nutritionnels.</p>
                </div>
            </div>
        </section>

        <!-- Vue toggle -->
        <div class="view-toggle">
            <button class="active" onclick="switchView('calendar')">📅 Calendrier</button>
            <button onclick="switchView('list')">📋 Liste</button>
        </div>

        <!-- Notifications Panel -->
        <div class="notifications-panel" id="notificationsPanel">
            <div class="notifications-header">
                <span>🔔</span>
                <h2>Notifications d'aujourd'hui</h2>
                <span class="notification-badge" id="notificationCount">0</span>
            </div>
            <div id="notificationsList"></div>
        </div>

        <!-- Calendar View -->
        <div id="calendarView" class="calendar-container">
            <div class="calendar-header">
                <button onclick="previousMonth()">← Précédent</button>
                <span class="calendar-month" id="monthYear"></span>
                <button onclick="nextMonth()">Suivant →</button>
            </div>

            <div class="weekdays">
                <div class="weekday">Lun</div>
                <div class="weekday">Mar</div>
                <div class="weekday">Mer</div>
                <div class="weekday">Jeu</div>
                <div class="weekday">Ven</div>
                <div class="weekday">Sam</div>
                <div class="weekday">Dim</div>
            </div>

            <div class="days" id="calendarDays"></div>
        </div>

        <!-- List View -->
        <div id="listView" style="display:none;">
            <section class="features" style="align-items:stretch;">
                <?php if(empty($plannings)): ?>
                    <div class="feature-card" style="max-width:700px; width:100%;">
                        <div class="feature-icon">📅</div>
                        <h3 class="feature-title">Aucun planning disponible</h3>
                        <p class="feature-desc">Votre nutritionniste n'a pas encore créé de planning pour votre compte.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($plannings as $planning): ?>
                        <div class="feature-card" style="text-align:left; max-width:360px;">
                            <div class="feature-icon">🗓️</div>
                            <h3 class="feature-title"><?= htmlspecialchars($planning['name']) ?></h3>
                            <p class="feature-desc" style="margin-bottom:0.5rem;"><strong>Période :</strong> <?= htmlspecialchars($planning['start_date']) ?> → <?= htmlspecialchars($planning['end_date']) ?></p>
                            <p class="feature-desc" style="margin-bottom:0.5rem;"><strong>Menus :</strong> <?= intval($planning['menu_count']) ?></p>
                            <p class="feature-desc" style="margin-bottom:1.2rem;"><strong>Statut :</strong> <?= htmlspecialchars($planning['status']) ?></p>
                            <a href="index.php?page=nutrition_plan_details&id=<?= $planning['id'] ?>" class="btn-primary" style="display:inline-block; text-decoration:none; padding:0.8rem 1.5rem; font-size:0.95rem;">Voir le planning</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-logo">
                    <span class="logo-icon">🌿</span>
                    <span>NutriWise</span>
                </div>
                <p class="footer-copyright">© 2024 NutriWise - Nutrition intelligente et durable</p>
            </div>
        </footer>
    </div>

    <script>
        // Données des plannings
        const planningsData = <?= json_encode($plannings) ?>;
        let currentDate = new Date();

        // Initialiser
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendar();
            loadNotifications();
            checkDailyNotifications();
        });

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // Mettre à jour le titre
            const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            document.getElementById('monthYear').textContent = `${monthNames[month]} ${year}`;
            
            // Récupérer le premier jour du mois
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const prevLastDay = new Date(year, month, 0);
            
            const firstDayOfWeek = firstDay.getDay() || 7; // 1-7 (lun-dim)
            const lastDateOfMonth = lastDay.getDate();
            const prevDate = prevLastDay.getDate();
            let html = '';

            // Jours du mois précédent
            for(let i = firstDayOfWeek - 1; i > 0; i--) {
                html += `<div class="day other-month"><div class="day-number">${prevDate - i + 1}</div></div>`;
            }

            // Jours du mois actuel
            for(let day = 1; day <= lastDateOfMonth; day++) {
                const date = new Date(year, month, day);
                const dateStr = date.toISOString().split('T')[0];
                const isToday = dateStr === new Date().toISOString().split('T')[0];
                
                // Trouver les plannings pour ce jour
                const dayPlannings = planningsData.filter(p => {
                    const start = new Date(p.start_date);
                    const end = new Date(p.end_date);
                    return date >= start && date <= end;
                });

                let planningsHtml = dayPlannings.map(p => 
                    `<span class="planning-badge ${p.status}">${p.name}</span>`
                ).join('');

                html += `
                    <div class="day ${isToday ? 'today' : ''}" onclick="goToDate('${dateStr}')">
                        <div class="day-number">${day}</div>
                        <div class="day-plannings">${planningsHtml}</div>
                    </div>
                `;
            }

            // Jours du mois suivant
            const remainingDays = 42 - (firstDayOfWeek - 1 + lastDateOfMonth);
            for(let day = 1; day <= remainingDays; day++) {
                html += `<div class="day other-month"><div class="day-number">${day}</div></div>`;
            }

            document.getElementById('calendarDays').innerHTML = html;
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        }

        function switchView(view) {
            document.getElementById('calendarView').style.display = view === 'calendar' ? 'block' : 'none';
            document.getElementById('listView').style.display = view === 'list' ? 'block' : 'none';
            
            document.querySelectorAll('.view-toggle button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        function goToDate(dateStr) {
            const date = new Date(dateStr);
            currentDate = date;
            renderCalendar();
            loadNotifications();
        }

        // Gestion des notifications
        function loadNotifications() {
            const today = new Date().toISOString().split('T')[0];
            const todayPlannings = planningsData.filter(p => {
                const start = new Date(p.start_date);
                const end = new Date(p.end_date);
                const today_date = new Date(today);
                return today_date >= start && today_date <= end;
            });

            let notificationsHtml = '';
            if(todayPlannings.length === 0) {
                notificationsHtml = '<div class="empty-state">Aucun planning pour aujourd\'hui</div>';
            } else {
                todayPlannings.forEach(planning => {
                    const mealsCount = planning.menu_count || 0;
                    const isUrgent = isPlanningSoonEnding(planning);
                    notificationsHtml += `
                        <div class="notification-item ${isUrgent ? 'urgent' : ''}">
                            <div class="notification-content">
                                <strong>${planning.name}</strong>
                                <div class="notification-time">${mealsCount} repas à suivre - Du ${planning.start_date} au ${planning.end_date}</div>
                            </div>
                            <div class="notification-actions">
                                <button onclick="goToPlanning(${planning.id})">Voir</button>
                            </div>
                        </div>
                    `;
                });
            }

            document.getElementById('notificationsList').innerHTML = notificationsHtml;
            document.getElementById('notificationCount').textContent = todayPlannings.length;
        }

        function isPlanningSoonEnding(planning) {
            const endDate = new Date(planning.end_date);
            const today = new Date();
            const daysLeft = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
            return daysLeft <= 3 && daysLeft > 0;
        }

        function goToPlanning(planningId) {
            window.location.href = `index.php?page=nutrition_plan_details&id=${planningId}`;
        }

        function checkDailyNotifications() {
            // Vérifier s'il y a une notification pour aujourd'hui
            const today = new Date().toISOString().split('T')[0];
            const lastNotified = localStorage.getItem('lastNotified');
            
            if(lastNotified !== today) {
                // Créer une notification navigateur si possible
                if('Notification' in window && Notification.permission === 'granted') {
                    new Notification('🔔 NutriWise', {
                        body: 'Vos plannings d\'aujourd\'hui sont prêts!',
                        icon: '🌿'
                    });
                }
                localStorage.setItem('lastNotified', today);
            }
        }

        // Demander la permission pour les notifications
        if('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    </script>
</body>
</html>
