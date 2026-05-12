<?php
// views/front/aliments.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Nos Aliments - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/front-global.css">
    <style>
        .search-section { margin: 2rem auto; max-width: 900px; }
        .search-box { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
        .search-input {
            flex: 1;
            padding: 1rem 1.2rem;
            border: 2px solid #e2e8e0;
            border-radius: 60px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .search-input:focus { outline: none; border-color: #4caf50; box-shadow: 0 0 0 3px rgba(76,175,80,0.1); }
        .search-btn { padding: 0 1.8rem; background: #2e7d32; color: white; border: none; border-radius: 60px; cursor: pointer; font-weight: 600; }
        .filter-container { display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center; margin-bottom: 2rem; }
        .filter-btn {
            padding: 0.6rem 1.3rem;
            border: none;
            background: #f8f9fa;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        .filter-btn.active, .filter-btn:hover { background: #2e7d32; color: white; transform: translateY(-2px); }
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
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem auto;
            padding: 1rem 2rem;
            background: white;
            border-radius: 60px;
            max-width: 450px;
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
        }
        .aliment-card:hover { transform: translateY(-6px); box-shadow: 0 20px 35px rgba(0,0,0,0.12); }
        .aliment-image { height: 200px; border-radius: 20px; overflow: hidden; }
        .aliment-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .aliment-card:hover .aliment-image img { transform: scale(1.03); }
        .aliment-name { font-size: 1.25rem; font-weight: 700; color: #1a3a1a; }
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
            background: #f0f3ef;
            padding: 0.25rem 0.9rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #4a6741;
        }
        .nutrition-grid { display: flex; gap: 0.75rem; flex-wrap: wrap; margin: 0.5rem 0; }
        .nutrition-item {
            flex: 1;
            text-align: center;
            background: #fafbf9;
            padding: 0.6rem 0.3rem;
            border-radius: 16px;
        }
        .nutrition-value { font-weight: 800; color: #2e7d32; font-size: 1rem; display: block; }
        .nutrition-label { font-size: 0.7rem; color: #7c8e7a; font-weight: 500; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <?php include_once 'partials/navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-apple-alt"></i> Nos Aliments</h1>
            <p class="page-subtitle">Découvrez notre sélection d'aliments sains, nutritifs et durables</p>
        </div>

        <div class="search-section">
            <div class="search-box">
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher un aliment...">
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
            <div><div class="stat-number" id="alimentCount">0</div><div>Aliments</div></div>
            <div><div class="stat-number" id="durableCount">0</div><div>Durables</div></div>
        </div>

        <div id="alimentsContainer" class="aliments-grid">
            <div class="loading-spinner" style="text-align:center; padding:3rem;">
                <div style="width:50px; height:50px; border:4px solid #e9ecef; border-top-color:#4caf50; border-radius:50%; animation:spin 0.8s linear infinite; margin:0 auto 1rem;"></div>
                <p>Chargement des aliments...</p>
            </div>
        </div>
        <div id="paginationContainer" class="pagination"></div>

        <footer class="footer">
            <div class="footer-logo">🌿 NutriWise</div>
            <p>© 2024 NutriWise - Nutrition intelligente et durable</p>
        </footer>
    </div>

    <script>
        const alimentsData = <?php echo json_encode($aliments ?? []); ?>;
        let currentCategory = 'all', currentSearch = '', currentSort = 'name', currentPage = 1;
        const pageSize = 6;

        function getDefaultImage() { return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop'; }
        function getSpecificImage(name, cat) {
            const map = {
                'amande': 'https://images.unsplash.com/photo-1525706616307-9301b5c3ad4f',
                'avocat': 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578',
                'banane': 'https://images.unsplash.com/photo-1603833665858-e61d17a86224',
                'poulet': 'https://images.unsplash.com/photo-1604503468506-a8da13d82791',
                'saumon': 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2',
                'oeuf': 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f',
                'pomme': 'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce',
                'carotte': 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37'
            };
            for (let [key, url] of Object.entries(map)) {
                if (name.toLowerCase().includes(key)) return url + '?w=400&h=300&fit=crop';
            }
            return getDefaultImage();
        }

        function getEcoStyle(score) {
            if(score >= 8) return { color: '#2e7d32', bg: '#e8f5e9' };
            if(score >= 6) return { color: '#f39c12', bg: '#fff3e0' };
            return { color: '#e74c3c', bg: '#ffebee' };
        }

        function renderAliments(aliments) {
            const container = document.getElementById('alimentsContainer');
            if(!aliments || aliments.length === 0) {
                document.getElementById('alimentCount').textContent = '0';
                document.getElementById('durableCount').textContent = '0';
                container.innerHTML = '<div class="empty-state"><i class="fas fa-search"></i><h3>Aucun aliment trouvé</h3></div>';
                return;
            }

            const sorted = [...aliments].sort((a,b) => {
                if(currentSort === 'calories') return (a.calories||0) - (b.calories||0);
                if(currentSort === 'eco_score') return (b.eco_score||0) - (a.eco_score||0);
                return (a.nom||'').localeCompare(b.nom||'');
            });

            const durable = sorted.filter(a => (a.eco_score||0) >= 7).length;
            document.getElementById('alimentCount').textContent = sorted.length;
            document.getElementById('durableCount').textContent = durable;

            const pages = Math.ceil(sorted.length / pageSize);
            if(currentPage > pages) currentPage = pages;
            const start = (currentPage-1)*pageSize;
            const pageItems = sorted.slice(start, start+pageSize);

            container.innerHTML = pageItems.map(a => {
                const cat = a.category_name || 'Aliment';
                let img = a.image;
                if(!img) img = getSpecificImage(a.nom, cat);
                else if(!img.startsWith('http')) img = 'views/uploads/aliments/' + img;
                const eco = (a.eco_score||0);
                const style = getEcoStyle(eco);
                return `<div class="aliment-card" onclick="location.href='index.php?page=aliment_details&id=${a.id}'">
                    <div class="aliment-image"><img src="${img}" alt="${escapeHtml(a.nom)}" onerror="this.src='${getDefaultImage()}'"></div>
                    <div><div style="display:flex;justify-content:space-between;align-items:center"><h3 class="aliment-name">${escapeHtml(a.nom)}</h3><div class="eco-score" style="background:${style.bg};color:${style.color}">${eco}/10</div></div><div class="category-badge">${escapeHtml(cat)}</div></div>
                    <div class="nutrition-grid"><div class="nutrition-item"><span class="nutrition-value">${a.calories||0}</span><span class="nutrition-label">kcal</span></div>
                    <div class="nutrition-item"><span class="nutrition-value">${(a.proteines||0).toFixed(1)}g</span><span class="nutrition-label">Protéines</span></div>
                    <div class="nutrition-item"><span class="nutrition-value">${(a.glucides||0).toFixed(1)}g</span><span class="nutrition-label">Glucides</span></div>
                    <div class="nutrition-item"><span class="nutrition-value">${(a.lipides||0).toFixed(1)}g</span><span class="nutrition-label">Lipides</span></div></div>
                    ${eco>=7?'<div class="eco-badge high"><i class="fas fa-leaf"></i> Aliment durable</div>':''}</div>`;
            }).join('');
            
            const pagesDiv = document.getElementById('paginationContainer');
            if(pages<=1){ pagesDiv.innerHTML=''; return; }
            let btns = `<button ${currentPage===1?'disabled':''} onclick="changePage(${currentPage-1})"><i class="fas fa-chevron-left"></i> Précédent</button>`;
            for(let i=1;i<=pages;i++) btns += `<button class="${currentPage===i?'active':''}" onclick="changePage(${i})">${i}</button>`;
            btns += `<button ${currentPage===pages?'disabled':''} onclick="changePage(${currentPage+1})">Suivant <i class="fas fa-chevron-right"></i></button>`;
            pagesDiv.innerHTML = btns;
        }

        function changePage(p){ currentPage=p; filterAliments(); }
        function escapeHtml(t){ if(!t) return ''; const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }
        function filterAliments(){
            currentPage=1;
            let filtered = [...alimentsData];
            if(currentSearch) filtered = filtered.filter(a => a.nom && a.nom.toLowerCase().includes(currentSearch.toLowerCase()));
            if(currentCategory === 'durable') filtered = filtered.filter(a => (a.eco_score||0) >= 7);
            else if(currentCategory !== 'all') filtered = filtered.filter(a => Number(a.category_id) === Number(currentCategory));
            renderAliments(filtered);
        }

        document.getElementById('searchBtn').addEventListener('click',()=>{ currentSearch=document.getElementById('searchInput').value; filterAliments(); });
        document.getElementById('searchInput').addEventListener('keypress',e=>{ if(e.key==='Enter'){ currentSearch=e.target.value; filterAliments(); } });
        document.getElementById('sortSelect').addEventListener('change',e=>{ currentSort=e.target.value; filterAliments(); });
        document.querySelectorAll('.filter-btn').forEach(btn=>{
            btn.addEventListener('click',()=>{
                document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
                btn.classList.add('active');
                currentCategory=btn.dataset.category;
                filterAliments();
            });
        });
        if(alimentsData.length) renderAliments(alimentsData);
    </script>
</body>
</html>