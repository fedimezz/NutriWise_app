<?php // Access control is handled in controller/router (PHP), not in the view. ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Nos Aliments - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
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

        .sort-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 50px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .sort-label {
            font-size: 0.85rem;
            color: #6b8a66;
        }

        .sort-select {
            padding: 0.4rem 0.8rem;
            border: 1px solid #e2e8e0;
            border-radius: 30px;
            background: white;
            cursor: pointer;
        }

        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background: white;
            border-radius: 60px;
            max-width: 450px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2e7d32;
        }

        .aliments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .aliment-card {
            background: white;
            border-radius: 24px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .aliment-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px rgba(0,0,0,0.12);
        }

        .aliment-image {
            width: 100%;
            height: 200px;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(135deg, #f5f7f0, #e8f0e5);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .aliment-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .aliment-card:hover .aliment-image img {
            transform: scale(1.03);
        }

        .aliment-content {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .aliment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
        }

        .aliment-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a3a1a;
            margin: 0;
        }

        .eco-score {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 48px;
            height: 32px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0 8px;
        }

        .category-badge {
            display: inline-block;
            background: #f0f3ef;
            padding: 0.25rem 0.9rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #4a6741;
            letter-spacing: 0.3px;
        }

        .nutrition-grid {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin: 0.5rem 0;
        }

        .nutrition-item {
            flex: 1;
            text-align: center;
            background: #fafbf9;
            padding: 0.6rem 0.3rem;
            border-radius: 16px;
            transition: all 0.2s;
        }

        .nutrition-item:hover {
            background: #e8f5e9;
            transform: scale(1.02);
        }

        .nutrition-value {
            font-weight: 800;
            color: #2e7d32;
            font-size: 1rem;
            display: block;
        }

        .nutrition-label {
            font-size: 0.7rem;
            color: #7c8e7a;
            font-weight: 500;
        }

        .eco-badge {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 0.4rem 0.8rem;
            border-radius: 40px;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            width: fit-content;
        }

        .eco-badge.high {
            background: #c8e6c9;
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
            .aliments-grid {
                grid-template-columns: 1fr;
            }
            .page-title {
                font-size: 2rem;
            }
            .sort-bar {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <!-- Include Chatbot -->
<?php include 'views/back/chatbot.php'; ?>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-apple-alt"></i> Nos Aliments</h1>
            <p class="page-subtitle">Découvrez notre sélection d'aliments sains, nutritifs et durables</p>
        </div>

        <div class="search-section">
            <div class="search-box">
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher un aliment... ex: avocat, banane, amande">
                <button id="searchBtn" class="search-btn"><i class="fas fa-search"></i> Rechercher</button>
            </div>
            <div class="filter-container" id="filterContainer">
                <button class="filter-btn active" data-category="all">Tous</button>
                <?php foreach($categories as $cat): ?>
                    <button class="filter-btn" data-category="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['icon'] ?? '📦'); ?> <?php echo htmlspecialchars($cat['name']); ?>
                    </button>
                <?php endforeach; ?>
                <button class="filter-btn" data-category="durable">🌱 Durable</button>
            </div>
        </div>

        <div class="sort-bar">
            <span class="sort-label"><i class="fas fa-sort"></i> Trier par :</span>
            <select id="sortSelect" class="sort-select">
                <option value="name">Nom (A-Z)</option>
                <option value="calories">Calories (croissant)</option>
                <option value="eco_score">Éco-score (décroissant)</option>
            </select>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number" id="alimentCount">0</div>
                <div><i class="fas fa-utensils"></i> Aliments</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="durableCount">0</div>
                <div><i class="fas fa-leaf"></i> Durables</div>
            </div>
        </div>

        <div id="alimentsContainer" class="aliments-grid">
            <div class="loading-spinner" style="text-align:center; padding:3rem;">
                <div style="width:50px; height:50px; border:4px solid #e9ecef; border-top-color:#4caf50; border-radius:50%; animation:spin 0.8s linear infinite; margin:0 auto 1rem;"></div>
                <p>Chargement des aliments...</p>
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
    const alimentsData = <?php echo json_encode($aliments ?? []); ?>;
    let currentCategory = 'all';
    let currentSearch = '';
    let currentSort = 'name';
    const pageSize = 6;
    let currentPage = 1;

    function getDefaultImage(category) {
        const defaultImages = {
            'Fruits': 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?w=400&h=300&fit=crop',
            'Légumes': 'https://images.unsplash.com/photo-1566385101042-1a0aa0c1268c?w=400&h=300&fit=crop',
            'Protéines': 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=400&h=300&fit=crop',
            'Féculents': 'https://images.unsplash.com/photo-1586201375761-83865001e8ac?w=400&h=300&fit=crop',
            'Matières grasses': 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop',
            'Laitages': 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&h=300&fit=crop'
        };

        return defaultImages[category] ||
            'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop';
    }

    function getSpecificImage(alimentName, category) {

        const name = alimentName.toLowerCase();

        const imageMap = {
            'amande': 'https://images.unsplash.com/photo-1525706616307-9301b5c3ad4f?w=400&h=300&fit=crop',
            'avocat': 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?w=400&h=300&fit=crop',
            'banane': 'https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=400&h=300&fit=crop',
            'boeuf': 'https://images.unsplash.com/photo-1603048297172-c92544798d5a?w=400&h=300&fit=crop',
            'brocoli': 'https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?w=400&h=300&fit=crop',
            'poulet': 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=400&h=300&fit=crop',
            'saumon': 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&h=300&fit=crop',
            'oeuf': 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400&h=300&fit=crop',
            'pomme': 'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?w=400&h=300&fit=crop',
            'carotte': 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=400&h=300&fit=crop'
        };

        for (let [key, url] of Object.entries(imageMap)) {
            if (name.includes(key)) {
                return url;
            }
        }

        return getDefaultImage(category);
    }

    function getEcoScoreColor(score) {
        if(score >= 8) return '#2e7d32';
        if(score >= 6) return '#f39c12';
        return '#e74c3c';
    }

    function getEcoScoreBg(score) {
        if(score >= 8) return '#e8f5e9';
        if(score >= 6) return '#fff3e0';
        return '#ffebee';
    }

    function sortAliments(aliments) {

        return [...aliments].sort((a, b) => {

            if (currentSort === 'calories') {
                return (Number(a.calories) || 0) -
                       (Number(b.calories) || 0);
            }

            else if (currentSort === 'eco_score') {
                return (Number(b.eco_score) || 0) -
                       (Number(a.eco_score) || 0);
            }

            return (a.nom || '').localeCompare(b.nom || '');
        });
    }

    function escapeHtml(text) {

        if(!text) return '';

        const div = document.createElement('div');
        div.textContent = text;

        return div.innerHTML;
    }

    function renderAliments(aliments) {

        const container = document.getElementById('alimentsContainer');

        if(!aliments || aliments.length === 0) {

            document.getElementById('alimentCount').textContent = '0';
            document.getElementById('durableCount').textContent = '0';

            document.getElementById('paginationContainer').innerHTML = '';

            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>Aucun aliment trouvé</h3>
                    <p>Essayez une autre recherche ou catégorie</p>
                </div>
            `;

            return;
        }

        const sortedAliments = sortAliments(aliments);

        const durableCount =
            sortedAliments.filter(a => Number(a.eco_score) >= 7).length;

        document.getElementById('alimentCount').textContent =
            sortedAliments.length;

        document.getElementById('durableCount').textContent =
            durableCount;

        const pages =
            Math.max(1, Math.ceil(sortedAliments.length / pageSize));

        if (currentPage > pages) {
            currentPage = pages;
        }

        const start = (currentPage - 1) * pageSize;

        const pageItems =
            sortedAliments.slice(start, start + pageSize);

        container.innerHTML = pageItems.map(aliment => {

            const category =
                aliment.category_name ||
                aliment.categorie ||
                'Aliment';

            let imageUrl = '';

            if (aliment.image && aliment.image !== '') {

                if (aliment.image.startsWith('http')) {
                    imageUrl = aliment.image;
                } else {
                    imageUrl = 'views/uploads/aliments/' + aliment.image;
                }

            } else {
                imageUrl = getSpecificImage(aliment.nom, category);
            }

            const ecoScore = Number(aliment.eco_score) || 0;

            const scoreColor = getEcoScoreColor(ecoScore);

            const scoreBg = getEcoScoreBg(ecoScore);

            return `
            <div class="aliment-card"
                 onclick="location.href='index.php?page=aliment_details&id=${encodeURIComponent(aliment.id)}'">

                <div class="aliment-image">

                    <img
                        src="${imageUrl}"
                        alt="${escapeHtml(aliment.nom)}"
                        loading="lazy"
                        onerror="this.src='${getDefaultImage(category)}'">

                </div>

                <div class="aliment-content">

                    <div>

                        <div class="aliment-header">

                            <h3 class="aliment-name">
                                ${escapeHtml(aliment.nom)}
                            </h3>

                            <div class="eco-score"
                                 style="background:${scoreBg}; color:${scoreColor};">

                                ${ecoScore}/10

                            </div>

                        </div>

                        <div class="category-badge">
                            ${escapeHtml(category)}
                        </div>

                    </div>

                    <div class="nutrition-grid">

                        <div class="nutrition-item">
                            <span class="nutrition-value">
                                ${escapeHtml(String(aliment.calories || 0))}
                            </span>
                            <span class="nutrition-label">kcal</span>
                        </div>

                        <div class="nutrition-item">
                            <span class="nutrition-value">
                                ${escapeHtml(String(aliment.proteines || 0))}g
                            </span>
                            <span class="nutrition-label">Protéines</span>
                        </div>

                        <div class="nutrition-item">
                            <span class="nutrition-value">
                                ${escapeHtml(String(aliment.glucides || 0))}g
                            </span>
                            <span class="nutrition-label">Glucides</span>
                        </div>

                        <div class="nutrition-item">
                            <span class="nutrition-value">
                                ${escapeHtml(String(aliment.lipides || 0))}g
                            </span>
                            <span class="nutrition-label">Lipides</span>
                        </div>

                    </div>

                    ${ecoScore >= 7
                        ? '<div class="eco-badge high"><i class="fas fa-leaf"></i> Aliment durable</div>'
                        : ''}

                </div>

            </div>
            `;

        }).join('');

        renderPagination(pages);
    }

    function renderPagination(pageCount) {

        const pagination =
            document.getElementById('paginationContainer');

        if (pageCount <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let buttons = `
            <button
                ${currentPage === 1 ? 'disabled' : ''}
                onclick="changePage(${currentPage - 1})">

                <i class="fas fa-chevron-left"></i> Précédent

            </button>
        `;

        for (let i = 1; i <= pageCount; i++) {

            buttons += `
                <button
                    class="${currentPage === i ? 'active' : ''}"
                    onclick="changePage(${i})">

                    ${i}

                </button>
            `;
        }

        buttons += `
            <button
                ${currentPage === pageCount ? 'disabled' : ''}
                onclick="changePage(${currentPage + 1})">

                Suivant <i class="fas fa-chevron-right"></i>

            </button>
        `;

        pagination.innerHTML = buttons;
    }

    function filterAliments(resetPage = false) {

        if (resetPage) {
            currentPage = 1;
        }

        let filtered = [...alimentsData];

        if(currentSearch) {

            filtered = filtered.filter(a =>
                a.nom &&
                a.nom.toLowerCase().includes(currentSearch.toLowerCase())
            );
        }

        if(currentCategory === 'durable') {

            filtered = filtered.filter(a =>
                Number(a.eco_score) >= 7
            );

        } else if(currentCategory !== 'all') {

            filtered = filtered.filter(a =>
                Number(a.category_id) === Number(currentCategory)
            );
        }

        renderAliments(filtered);
    }

    function changePage(page) {

        currentPage = page;

        filterAliments(false);

        window.scrollTo({
            top: document.getElementById('alimentsContainer').offsetTop - 100,
            behavior: 'smooth'
        });
    }

    // Search button

    document.getElementById('searchBtn')
        .addEventListener('click', () => {

        currentSearch =
            document.getElementById('searchInput').value;

        filterAliments(true);
    });

    // Enter search

    document.getElementById('searchInput')
        .addEventListener('keypress', (e) => {

        if (e.key === 'Enter') {

            currentSearch =
                document.getElementById('searchInput').value;

            filterAliments(true);
        }
    });

    // Sort

    document.getElementById('sortSelect')
        .addEventListener('change', (e) => {

        currentSort = e.target.value;

        filterAliments(true);
    });

    // Filters

    document.querySelectorAll('.filter-btn')
        .forEach(btn => {

        btn.addEventListener('click', () => {

            document.querySelectorAll('.filter-btn')
                .forEach(b => b.classList.remove('active'));

            btn.classList.add('active');

            currentCategory = btn.dataset.category;

            filterAliments(true);
        });
    });

    // Initial render

    if(alimentsData.length > 0) {
        renderAliments(alimentsData);
    }
</script>
</body>
</html>