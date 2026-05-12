<?php
// views/front/nutrition_plans_calendar.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Calendrier des plannings - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8faf8; color: #1a2e1a; }
        .container { max-width: 1400px; margin: 0 auto; padding: 0 24px; }
        
        .calendar-header {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            padding: 2rem;
            border-radius: 24px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .calendar-header h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        
        .view-toggle {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .view-btn {
            padding: 0.75rem 1.8rem;
            border: 2px solid #e2e8e0;
            background: white;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .view-btn.active {
            background: #2e7d32;
            color: white;
            border-color: #2e7d32;
        }
        
        .legend {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; }
        .legend-color { width: 20px; height: 20px; border-radius: 4px; }
        .legend-color.planning { background: #4caf50; }
        .legend-color.liked { background: #ff4757; }
        .legend-color.scheduled { background: #ffa502; }
        .legend-color.favorite { background: #e056fd; }
        
        .month-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1rem 2rem;
            border-radius: 60px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .month-nav-btn {
            background: #f0f7ed;
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
            color: #2e7d32;
        }
        .month-nav-btn:hover { background: #2e7d32; color: white; }
        .current-month { font-size: 1.5rem; font-weight: 700; color: #2e7d32; }
        
        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }
        .weekday {
            text-align: center;
            padding: 12px;
            font-weight: 700;
            background: #e8f5e9;
            border-radius: 12px;
            color: #2e7d32;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        .calendar-day {
            background: white;
            border-radius: 16px;
            padding: 12px;
            min-height: 130px;
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .calendar-day:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .calendar-day.empty { background: #fafbf9; opacity: 0.6; }
        .day-number { font-weight: 700; color: #2e7d32; margin-bottom: 8px; font-size: 1rem; }
        .day-today { background: #2e7d32; color: white; display: inline-block; width: 28px; height: 28px; line-height: 28px; text-align: center; border-radius: 50%; }
        
        .day-events { display: flex; flex-direction: column; gap: 4px; }
        .event-badge {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 20px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        .event-badge:hover { transform: scale(1.02); }
        .event-planning { background: #e8f5e9; color: #2e7d32; border-left: 3px solid #4caf50; }
        .event-liked { background: #ffeaea; color: #ff4757; border-left: 3px solid #ff4757; }
        .event-scheduled { background: #fff8e8; color: #ffa502; border-left: 3px solid #ffa502; }
        .event-favorite { background: #f3e8ff; color: #e056fd; border-left: 3px solid #e056fd; }
        .more-events { font-size: 0.65rem; color: #7c8e7a; text-align: center; padding: 2px; }
        
        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
            background: white;
            color: #4a6741;
        }
        .filter-btn.active, .filter-btn:hover { background: #2e7d32; color: white; }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            border-radius: 24px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        .event-list { list-style: none; }
        .event-list li {
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 12px;
            cursor: pointer;
            background: #f8faf8;
        }
        .event-list li:hover { transform: translateX(5px); background: #e8f5e9; }
        .event-title { font-weight: 700; margin-bottom: 4px; }
        .btn-close {
            background: #2e7d32;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
            font-weight: 600;
        }
        
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #27ae60;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            z-index: 1100;
            display: none;
        }
        
        .btn-back {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            text-decoration: none;
            margin-top: 15px;
        }
        
        @media (max-width: 768px) {
            .calendar-day { min-height: 90px; padding: 6px; }
            .event-badge { font-size: 0.6rem; padding: 2px 4px; }
        }
    </style>
</head>
<body>
    <?php include 'views/back/chatbot.php'; ?>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="calendar-header">
            <h1><i class="fas fa-calendar-alt"></i> Calendrier des plannings</h1>
            <p>Visualisez tous vos plannings, likes et planifications</p>
            <a href="index.php?page=nutrition_plans" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <div class="view-toggle">
            <button class="view-btn" onclick="window.location.href='index.php?page=nutrition_plans'">
                <i class="fas fa-list"></i> Vue Liste
            </button>
            <button class="view-btn active" onclick="window.location.href='index.php?page=nutrition_plans_calendar'">
                <i class="fas fa-calendar-alt"></i> Vue Calendrier
            </button>
        </div>

        <div class="filter-bar">
            <button class="filter-btn active" data-filter="all">📅 Tous</button>
            <button class="filter-btn" data-filter="planning">📋 Plannings</button>
            <button class="filter-btn" data-filter="liked">❤️ Likés</button>
            <button class="filter-btn" data-filter="scheduled">📆 Planifiés</button>
            <button class="filter-btn" data-filter="favorite">⭐ Favoris</button>
        </div>

        <div class="legend">
            <div class="legend-item"><div class="legend-color planning"></div><span>Planning actif</span></div>
            <div class="legend-item"><div class="legend-color liked"></div><span>Planning liké</span></div>
            <div class="legend-item"><div class="legend-color scheduled"></div><span>Planning planifié</span></div>
            <div class="legend-item"><div class="legend-color favorite"></div><span>Planning favori</span></div>
        </div>

        <div class="month-navigation">
            <button class="month-nav-btn" onclick="changeMonth(-1)">
                <i class="fas fa-chevron-left"></i> Mois précédent
            </button>
            <div class="current-month" id="currentMonth"></div>
            <button class="month-nav-btn" onclick="changeMonth(1)">
                Mois suivant <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="calendar-weekdays" id="weekdays"></div>
        <div class="calendar-grid" id="calendarGrid"></div>
    </div>

    <!-- Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <h3 style="color: #2e7d32; margin-bottom: 20px;">
                <i class="fas fa-calendar-day"></i> Événements du <span id="modalDate"></span>
            </h3>
            <div id="modalEvents"></div>
            <button class="btn-close" onclick="closeModal()">Fermer</button>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        // Données PHP passées au JavaScript
        const plannings = <?php echo json_encode($plannings ?? []); ?>;
        const likedPlannings = <?php echo json_encode($likedPlannings ?? []); ?>;
        const favoritePlannings = <?php echo json_encode($favoritePlannings ?? []); ?>;
        const scheduledPlannings = <?php echo json_encode($scheduledPlannings ?? []); ?>;

        let currentDate = new Date();
        let currentFilter = 'all';

        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.background = isError ? '#e74c3c' : '#27ae60';
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function getEventsForDay(dateStr) {
            const events = [];
            const date = new Date(dateStr);
            
            // Plannings
            if (currentFilter === 'all' || currentFilter === 'planning') {
                plannings.forEach(planning => {
                    const start = new Date(planning.start_date);
                    const end = new Date(planning.end_date);
                    if (date >= start && date <= end) {
                        events.push({
                            type: 'planning',
                            id: planning.id,
                            title: planning.name,
                            icon: '📋',
                            status: planning.status
                        });
                    }
                });
            }

            // Likes
            if ((currentFilter === 'all' || currentFilter === 'liked') && likedPlannings.length > 0) {
                likedPlannings.forEach(planning => {
                    const start = new Date(planning.start_date);
                    const end = new Date(planning.end_date);
                    if (date >= start && date <= end) {
                        events.push({
                            type: 'liked',
                            id: planning.id,
                            title: '❤️ ' + planning.name,
                            icon: '❤️'
                        });
                    }
                });
            }

            // Favoris
            if ((currentFilter === 'all' || currentFilter === 'favorite') && favoritePlannings.length > 0) {
                favoritePlannings.forEach(planning => {
                    const start = new Date(planning.start_date);
                    const end = new Date(planning.end_date);
                    if (date >= start && date <= end) {
                        events.push({
                            type: 'favorite',
                            id: planning.id,
                            title: '⭐ ' + planning.name,
                            icon: '⭐'
                        });
                    }
                });
            }

            // Planifications
            if ((currentFilter === 'all' || currentFilter === 'scheduled') && scheduledPlannings.length > 0) {
                scheduledPlannings.forEach(schedule => {
                    const start = new Date(schedule.start_date);
                    const end = new Date(schedule.end_date);
                    if (date >= start && date <= end) {
                        events.push({
                            type: 'scheduled',
                            id: schedule.id,
                            title: '📅 ' + schedule.name,
                            icon: '📅',
                            scheduleId: schedule.id
                        });
                    }
                });
            }

            return events;
        }

        function getEventClass(type) {
            switch(type) {
                case 'planning': return 'event-planning';
                case 'liked': return 'event-liked';
                case 'favorite': return 'event-favorite';
                case 'scheduled': return 'event-scheduled';
                default: return 'event-planning';
            }
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            const firstDay = new Date(year, month, 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - startDate.getDay());
            
            document.getElementById('currentMonth').textContent = 
                `${firstDay.toLocaleString('fr-FR', { month: 'long' })} ${year}`.toUpperCase();
            
            const weekdays = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
            document.getElementById('weekdays').innerHTML = weekdays.map(day => `<div class="weekday">${day}</div>`).join('');
            
            let gridHTML = '';
            const currentDateObj = new Date(startDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            for (let i = 0; i < 42; i++) {
                const isCurrentMonth = currentDateObj.getMonth() === month;
                const dateStr = formatDate(currentDateObj);
                const events = getEventsForDay(dateStr);
                const isToday = formatDate(currentDateObj) === formatDate(today);
                
                gridHTML += `
                    <div class="calendar-day ${!isCurrentMonth ? 'empty' : ''}" 
                         onclick="showDayEvents('${dateStr}', ${JSON.stringify(events).replace(/"/g, '&quot;')})">
                        <div class="day-number">
                            ${isToday ? `<span class="day-today">${currentDateObj.getDate()}</span>` : currentDateObj.getDate()}
                        </div>
                        <div class="day-events">
                            ${events.slice(0, 3).map(event => `
                                <div class="event-badge ${getEventClass(event.type)}" 
                                     onclick="event.stopPropagation(); goToPlanning(${event.id}, '${event.type}')"
                                     title="${event.title}">
                                    ${event.icon} ${event.title.length > 25 ? event.title.substring(0, 22) + '...' : event.title}
                                </div>
                            `).join('')}
                            ${events.length > 3 ? `<div class="more-events">+${events.length - 3} autre(s)</div>` : ''}
                        </div>
                    </div>
                `;
                currentDateObj.setDate(currentDateObj.getDate() + 1);
            }
            
            document.getElementById('calendarGrid').innerHTML = gridHTML;
        }

        function showDayEvents(dateStr, events) {
            const modal = document.getElementById('eventModal');
            document.getElementById('modalDate').innerHTML = dateStr;
            
            const eventsContainer = document.getElementById('modalEvents');
            
            if (events.length === 0) {
                eventsContainer.innerHTML = '<p style="text-align:center; color:#7c8e7a;">Aucun événement ce jour</p>';
            } else {
                eventsContainer.innerHTML = `
                    <ul class="event-list">
                        ${events.map(event => `
                            <li onclick="goToPlanning(${event.id}, '${event.type}')">
                                <div class="event-title">${event.icon} ${event.title}</div>
                                <div class="event-date">${event.type === 'scheduled' ? '📆 Planifié personnalisé' : '📋 Planning officiel'}</div>
                            </li>
                        `).join('')}
                    </ul>
                `;
            }
            
            modal.style.display = 'flex';
        }

        function goToPlanning(id, type) {
            if (type === 'scheduled') {
                window.location.href = `index.php?page=my_schedules`;
            } else {
                window.location.href = `index.php?page=nutrition_plan_details&id=${id}`;
            }
        }

        function closeModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        function changeMonth(delta) {
            currentDate.setMonth(currentDate.getMonth() + delta);
            renderCalendar();
        }

        // Filtres
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentFilter = btn.dataset.filter;
                renderCalendar();
                showToast(`📋 Filtre appliqué : ${btn.textContent}`);
            });
        });

        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target === modal) closeModal();
        }

        // Initialisation
        renderCalendar();
        
        // Notification pour aujourd'hui
        const today = formatDate(new Date());
        const todayEvents = getEventsForDay(today);
        if (todayEvents.length > 0) {
            showToast(`🔔 Aujourd'hui : ${todayEvents.length} événement(s)`);
        }
    </script>
</body>
</html>