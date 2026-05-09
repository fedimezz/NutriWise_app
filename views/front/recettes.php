<?php // Access control is handled in controller/router (PHP), not in the view. ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Nos recettes - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8faf8;
            color: #1a2e1a;
        }

        /* Styles de la navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 60px;
            margin: 1rem 0 2rem 0;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2e7d32;
        }

        .logo-icon {
            font-size: 1.8rem;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: #5a7a55;
            font-weight: 500;
            transition: all 0.3s;
            padding: 0.5rem 0;
        }

        .nav-link:hover, .nav-link.active {
            color: #2e7d32;
            border-bottom: 2px solid #4caf50;
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
            transition: all 0.3s;
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
            transition: all 0.3s;
        }

        .btn-login {
            color: #2e7d32;
            border: 2px solid #2e7d32;
        }

        .btn-login:hover {
            background: #2e7d32;
            color: white;
        }

        .btn-register {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46,125,50,0.3);
        }

        /* Notification styles */
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

        /* Reste des styles pour la page recette */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .page-header {
            text-align: center;
            padding: 2rem 2rem 1rem;
            background: linear-gradient(135deg, #f0f7ed, #e8f3e4);
            border-radius: 32px;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #5a7a55;
            font-size: 1.1rem;
        }

        .search-section {
            margin: 2rem auto;
            max-width: 900px;
        }

        .search-box {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-input {
            flex: 1;
            padding: 1rem 1.2rem;
            border: 2px solid #e2e8e0;
            border-radius: 60px;
            font-size: 1rem;
            background: white;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
        }

        .search-btn {
            padding: 0 1.8rem;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 60px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .search-btn:hover {
            background: #1b5e20;
            transform: translateY(-2px);
        }

        .filter-container {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .filter-btn {
            padding: 0.6rem 1.3rem;
            border: none;
            background: #f8f9fa;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .filter-btn.active, .filter-btn:hover {
            background: #2e7d32;
            color: white;
            transform: translateY(-2px);
        }

        .recettes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .recette-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            cursor: pointer;
        }

        .recette-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px rgba(0,0,0,0.12);
        }

        .recette-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .recette-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .recette-card:hover .recette-image img {
            transform: scale(1.03);
        }

        .difficulte-badge {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            background: rgba(46,125,50,0.9);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .recette-content {
            padding: 1.5rem;
        }

        .recette-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1a3a1a;
        }

        .recette-description {
            color: #6b8a66;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .recette-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #7c9a76;
            margin-bottom: 1rem;
        }

        .recette-meta i {
            margin-right: 0.3rem;
            color: #4caf50;
        }

        .recette-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .tag {
            background: #eef5ec;
            padding: 0.2rem 0.7rem;
            border-radius: 15px;
            font-size: 0.7rem;
            color: #2e7d32;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 2rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .pagination button {
            border: none;
            background: #f1f8ee;
            color: #2e7d32;
            padding: 0.75rem 1.2rem;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .pagination button.active,
        .pagination button:hover:not(:disabled) {
            background: #2e7d32;
            color: white;
        }

        .pagination button:disabled {
            opacity: 0.4;
            cursor: default;
        }

        .empty-state {
            text-align: center;
            padding: 4rem;
            background: white;
            border-radius: 32px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #c8e6c9;
            margin-bottom: 1rem;
        }

        .footer {
            margin-top: 4rem;
            padding: 2rem 0;
            border-top: 1px solid #e0e8dc;
            text-align: center;
        }

        .footer-logo {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .recettes-grid {
                grid-template-columns: 1fr;
            }
            .page-title {
                font-size: 2rem;
            }
            .navbar {
                flex-direction: column;
                text-align: center;
            }
            .nav-links {
                justify-content: center;
            }
            .auth-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Include Chatbot -->
<?php include 'views/back/chatbot.php'; ?>
    <div class="container">
        <?php 
        // Inclusion correcte de la navbar
        if (file_exists(__DIR__ . '/partials/navbar.php')) {
            include_once __DIR__ . '/partials/navbar.php';
        } elseif (file_exists(__DIR__ . '/../partials/navbar.php')) {
            include_once __DIR__ . '/../partials/navbar.php';
        } else {
            include_once 'partials/navbar.php';
        }
        ?>

        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-utensils"></i> Nos recettes</h1>
            <p class="page-subtitle">Des recettes saines, équilibrées et délicieuses pour toute la famille</p>
        </div>

        <div class="search-section">
            <div class="search-box">
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher une recette...">
                <button id="searchBtn" class="search-btn"><i class="fas fa-search"></i> Rechercher</button>
            </div>
            <div class="filter-container" id="filterContainer">
                <button class="filter-btn active" data-categorie="all">Toutes</button>
                <button class="filter-btn" data-categorie="Petit-déjeuner">🍳 Petit-déjeuner</button>
                <button class="filter-btn" data-categorie="Entrée">🥗 Entrée</button>
                <button class="filter-btn" data-categorie="Plat principal">🍽️ Plat principal</button>
                <button class="filter-btn" data-categorie="Dessert">🍰 Dessert</button>
            </div>
        </div>

        <div id="recettesContainer" class="recettes-grid">
            <div class="loading-spinner" style="text-align:center; padding:3rem;">
                <div style="width:50px; height:50px; border:4px solid #e9ecef; border-top-color:#4caf50; border-radius:50%; animation:spin 0.8s linear infinite; margin:0 auto 1rem;"></div>
                <p>Chargement des recettes...</p>
            </div>
        </div>
        <div id="paginationContainer" class="pagination"></div>

        <footer class="footer">
            <div class="footer-logo">
                <span>🌿 NutriWise</span>
            </div>
            <p class="footer-copyright">© 2024 NutriWise - Nutrition intelligente et durable</p>
        </footer>
    </div>

    <script>
        const recettesData = <?php echo json_encode($recettes ?? []); ?>;
        let currentCategorie = 'all';
        let currentSearch = '';
        let currentPage = 1;
        const pageSize = 6;

        function getDifficulteIcon(difficulte) {
            const map = { 'Facile': '😊 Facile', 'Moyen': '👍 Moyen', 'Difficile': '🔥 Difficile' };
            return map[difficulte] || '📖 ' + difficulte;
        }

        function getDefaultImage() {
            return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop';
        }

        function renderRecettes(recettes) {
            const container = document.getElementById('recettesContainer');
            
            if (!recettes || recettes.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>Aucune recette trouvée</h3>
                        <p>Essayez une autre recherche ou catégorie</p>
                    </div>
                `;
                document.getElementById('paginationContainer').innerHTML = '';
                return;
            }

            const pages = Math.max(1, Math.ceil(recettes.length / pageSize));
            if (currentPage > pages) currentPage = pages;
            const start = (currentPage - 1) * pageSize;
            const pageItems = recettes.slice(start, start + pageSize);

            container.innerHTML = pageItems.map(recette => {
                const tags = recette.tags ? recette.tags.split(',') : [];
                const imageUrl = recette.image && recette.image !== '' && recette.image.startsWith('http') 
                    ? recette.image 
                    : getDefaultImage();
                
                return `
                    <div class="recette-card" onclick="window.location.href='index.php?page=recette_details&id=${recette.id}'">
                        <div class="recette-image">
                            <img src="${imageUrl}" alt="${escapeHtml(recette.nom)}" loading="lazy" onerror="this.src='${getDefaultImage()}'">
                            <div class="difficulte-badge">${getDifficulteIcon(recette.difficulte)}</div>
                        </div>
                        <div class="recette-content">
                            <h3 class="recette-title">${escapeHtml(recette.nom)}</h3>
                            <p class="recette-description">${escapeHtml(recette.description || 'Une délicieuse recette à découvrir')}</p>
                            <div class="recette-meta">
                                <span><i class="fas fa-user-friends"></i> ${recette.portions || 4} parts</span>
                                <span><i class="fas fa-utensils"></i> ${recette.categorie || 'Plat'}</span>
                            </div>
                            <div class="recette-tags">
                                ${tags.slice(0, 3).map(tag => `<span class="tag">#${tag.trim()}</span>`).join('')}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            renderPagination(pages);
        }

        function renderPagination(pageCount) {
            const pagination = document.getElementById('paginationContainer');
            if (pageCount <= 1) {
                pagination.innerHTML = '';
                return;
            }
            
            let buttons = `<button ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                            <i class="fas fa-chevron-left"></i> Précédent
                           </button>`;
            
            for (let i = 1; i <= pageCount; i++) {
                buttons += `<button class="${currentPage === i ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
            }
            
            buttons += `<button ${currentPage === pageCount ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                            Suivant <i class="fas fa-chevron-right"></i>
                        </button>`;
            
            pagination.innerHTML = buttons;
        }

        function changePage(page) {
            currentPage = page;
            filterRecettes();
        }

        function escapeHtml(text) {
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function filterRecettes() {
            currentPage = 1;
            let filtered = [...recettesData];
            
            if (currentSearch) {
                filtered = filtered.filter(r => r.nom && r.nom.toLowerCase().includes(currentSearch.toLowerCase()));
            }
            
            if (currentCategorie !== 'all') {
                filtered = filtered.filter(r => r.categorie === currentCategorie);
            }
            
            renderRecettes(filtered);
        }

        // Événements
        document.getElementById('searchBtn')?.addEventListener('click', () => {
            currentSearch = document.getElementById('searchInput').value;
            filterRecettes();
        });
        
        document.getElementById('searchInput')?.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') {
                currentSearch = e.target.value;
                filterRecettes();
            }
        });

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentCategorie = btn.dataset.categorie;
                filterRecettes();
            });
        });

        // Initialisation
        if(recettesData && recettesData.length > 0) {
            renderRecettes(recettesData);
        }
    </script>
</body>
</html>