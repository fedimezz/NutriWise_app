<?php
// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&error=Vous devez être connecté pour accéder aux aliments");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Nos Aliments - NutriWise</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        .page-header {
            text-align: center;
            padding: 2rem 2rem 1rem;
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
            color: #6c757d;
        }
        .search-section {
            margin: 2rem auto;
            max-width: 800px;
        }
        .search-box {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .search-input {
            flex: 1;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            font-size: 1rem;
        }
        .filter-container {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .filter-btn {
            padding: 0.5rem 1.2rem;
            border: none;
            background: #f8f9fa;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .filter-btn.active, .filter-btn:hover {
            background: #2e7d32;
            color: white;
        }
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem auto;
            padding: 1rem;
            background: white;
            border-radius: 60px;
            max-width: 400px;
        }
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2e7d32;
        }
        .aliments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .aliment-card {
            background: white;
            border-radius: 20px;
            padding: 1.25rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .aliment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .aliment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .aliment-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e2f;
        }
        .eco-badge {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .eco-badge.high {
            background: #c8e6c9;
        }
        .category-badge {
            display: inline-block;
            background: #f0f0f0;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .nutrition-grid {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }
        .nutrition-item {
            flex: 1;
            text-align: center;
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 12px;
        }
        .nutrition-value {
            font-weight: 700;
            color: #2e7d32;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 20px;
        }
        .eco-score {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #ffc107;
            color: #856404;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

        <div class="page-header">
            <h1 class="page-title">Nos Aliments</h1>
            <p class="page-subtitle">Découvrez notre sélection d'aliments sains, nutritifs et durables</p>
        </div>

        <div class="search-section">
            <div class="search-box">
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher un aliment...">
            </div>
            <div class="filter-container" id="filterContainer">
                <button class="filter-btn active" data-category="all">Tous</button>
                <?php foreach($categories as $cat): ?>
                    <button class="filter-btn" data-category="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></button>
                <?php endforeach; ?>
                <button class="filter-btn" data-category="durable">🌱 Durable</button>
            </div>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number" id="alimentCount">0</div>
                <div>Aliments</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="durableCount">0</div>
                <div>Durables</div>
            </div>
        </div>

        <div id="alimentsContainer" class="aliments-grid">
            <div class="loading-spinner" style="text-align:center; padding:3rem;">
                <div style="width:50px; height:50px; border:4px solid #e9ecef; border-top-color:#4caf50; border-radius:50%; animation:spin 0.8s linear infinite; margin:0 auto 1rem;"></div>
                <p>Chargement des aliments...</p>
            </div>
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

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        const alimentsData = <?php echo json_encode($aliments ?? []); ?>;
        let currentCategory = 'all';
        let currentSearch = '';

        function getEcoScoreClass(score) {
            if(score >= 8) return 'high';
            if(score >= 6) return 'medium';
            return 'low';
        }

        function getEcoScoreColor(score) {
            if(score >= 8) return '#2e7d32';
            if(score >= 6) return '#ff9800';
            return '#f44336';
        }

        function renderAliments(aliments) {
            const container = document.getElementById('alimentsContainer');
            
            if(!aliments || aliments.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div style="font-size:3rem; margin-bottom:1rem;">🥗</div>
                        <h3>Aucun aliment trouvé</h3>
                        <p>Essayez de modifier votre recherche</p>
                    </div>
                `;
                document.getElementById('alimentCount').textContent = '0';
                document.getElementById('durableCount').textContent = '0';
                return;
            }

            const durableCount = aliments.filter(a => a.eco_score >= 7).length;
            document.getElementById('alimentCount').textContent = aliments.length;
            document.getElementById('durableCount').textContent = durableCount;

            container.innerHTML = aliments.map(aliment => `
                <div class="aliment-card" onclick="location.href='index.php?page=aliment_details&id=${aliment.id}'">
                    <div class="aliment-header">
                        <h3 class="aliment-name">${escapeHtml(aliment.nom)}</h3>
                        <div class="eco-score" style="background: ${getEcoScoreColor(aliment.eco_score)}20; color: ${getEcoScoreColor(aliment.eco_score)};">
                            ${aliment.eco_score}/10
                        </div>
                    </div>
                    <div class="category-badge">${escapeHtml(aliment.category_name || aliment.categorie || 'Aliment')}</div>
                    <div class="nutrition-grid">
                        <div class="nutrition-item">
                            <div class="nutrition-value">${aliment.calories || 0}</div>
                            <div>kcal</div>
                        </div>
                        <div class="nutrition-item">
                            <div class="nutrition-value">${aliment.proteins || 0}g</div>
                            <div>Protéines</div>
                        </div>
                        <div class="nutrition-item">
                            <div class="nutrition-value">${aliment.glucides || 0}g</div>
                            <div>Glucides</div>
                        </div>
                        <div class="nutrition-item">
                            <div class="nutrition-value">${aliment.lipids || 0}g</div>
                            <div>Lipides</div>
                        </div>
                    </div>
                    ${aliment.eco_score >= 7 ? '<div class="eco-badge high">🌱 Aliment durable</div>' : ''}
                </div>
            `).join('');
        }

        function escapeHtml(text) {
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function filterAliments() {
            let filtered = [...alimentsData];
            
            if(currentSearch) {
                filtered = filtered.filter(a => a.nom.toLowerCase().includes(currentSearch.toLowerCase()));
            }
            
            if(currentCategory === 'durable') {
                filtered = filtered.filter(a => a.eco_score >= 7);
            } else if(currentCategory !== 'all') {
                filtered = filtered.filter(a => a.category_id == currentCategory);
            }
            
            renderAliments(filtered);
        }

        document.getElementById('searchInput').addEventListener('input', (e) => {
            currentSearch = e.target.value;
            filterAliments();
        });

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentCategory = btn.dataset.category;
                filterAliments();
            });
        });

        if(alimentsData.length > 0) {
            renderAliments(alimentsData);
        }
    </script>
</body>
</html>