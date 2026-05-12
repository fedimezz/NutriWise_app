<?php
// views/front/recettes.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Nos recettes - NutriWise</title>
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
        .recettes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; margin: 2rem 0; }
        .recette-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            cursor: pointer;
        }
        .recette-card:hover { transform: translateY(-6px); box-shadow: 0 20px 35px rgba(0,0,0,0.12); }
        .recette-image { height: 200px; overflow: hidden; position: relative; }
        .recette-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .recette-card:hover .recette-image img { transform: scale(1.03); }
        .difficulte-badge { position: absolute; bottom: 1rem; left: 1rem; background: rgba(46,125,50,0.9); color: white; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.7rem; }
        .recette-content { padding: 1.5rem; }
        .recette-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #1a3a1a; }
        .recette-description { color: #6b8a66; font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.5; }
        .recette-meta { display: flex; gap: 1rem; font-size: 0.85rem; color: #7c9a76; margin-bottom: 1rem; }
        .recette-meta i { margin-right: 0.3rem; color: #4caf50; }
        .tag { background: #eef5ec; padding: 0.2rem 0.7rem; border-radius: 15px; font-size: 0.7rem; color: #2e7d32; display: inline-block; margin-right: 0.5rem; }
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
        @keyframes spin { to { transform: rotate(360deg); } }
        .loading-spinner { text-align:center; padding:3rem; }
        .spinner { width:50px; height:50px; border:4px solid #e9ecef; border-top-color:#4caf50; border-radius:50%; animation:spin 0.8s linear infinite; margin:0 auto 1rem; }
        .empty-state { text-align: center; padding: 4rem; background: white; border-radius: 32px; }
        .empty-state i { font-size: 4rem; color: #c8e6c9; margin-bottom: 1rem; }
        .pagination { display: flex; justify-content: center; gap: 0.75rem; margin: 2rem 0; flex-wrap: wrap; }
        .pagination button {
            border: none;
            background: #f1f8ee;
            color: #2e7d32;
            padding: 0.75rem 1.2rem;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 600;
        }
        .pagination button.active, .pagination button:hover:not(:disabled) { background: #2e7d32; color: white; }
        .pagination button:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>
    <?php include 'views/back/chatbot.php'; ?>
    <div class="container">
        <?php include_once 'partials/navbar.php'; ?>

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
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Chargement des recettes...</p>
            </div>
        </div>
        <div id="paginationContainer" class="pagination"></div>

        <footer class="footer">
            <div class="footer-logo">🌿 NutriWise</div>
            <p>© 2024 NutriWise - Nutrition intelligente et durable</p>
        </footer>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        const recettesData = <?php echo json_encode($recettes ?? []); ?>;
        let currentCategorie = 'all', currentSearch = '', currentPage = 1;
        const pageSize = 6;

        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.background = isError ? '#e74c3c' : '#27ae60';
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        function getDifficulteIcon(d) { return { 'Facile':'😊 Facile', 'Moyen':'👍 Moyen', 'Difficile':'🔥 Difficile' }[d] || '📖 '+d; }
        function getDefaultImage() { return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop'; }
        function escapeHtml(t){ if(!t) return ''; const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }

        function renderRecettes(recettes) {
            const container = document.getElementById('recettesContainer');
            if(!recettes || recettes.length===0){
                container.innerHTML = '<div class="empty-state"><i class="fas fa-search"></i><h3>Aucune recette trouvée</h3><p>Essayez une autre recherche ou catégorie</p></div>';
                document.getElementById('paginationContainer').innerHTML = '';
                return;
            }
            const pages = Math.max(1, Math.ceil(recettes.length / pageSize));
            if(currentPage>pages) currentPage=pages;
            const start = (currentPage-1)*pageSize;
            const pageItems = recettes.slice(start, start+pageSize);
            container.innerHTML = pageItems.map(r => {
                const tags = r.tags ? r.tags.split(',') : [];
                const img = r.image && r.image.startsWith('http') ? r.image : (r.image ? 'views/assets/uploads/recettes/' + r.image : getDefaultImage());
                return `<div class="recette-card" onclick="window.location.href='index.php?page=recette_details&id=${r.id}'">
                    <div class="recette-image"><img src="${img}" alt="${escapeHtml(r.nom)}" onerror="this.src='${getDefaultImage()}'"><div class="difficulte-badge">${getDifficulteIcon(r.difficulte)}</div></div>
                    <div class="recette-content"><h3 class="recette-title">${escapeHtml(r.nom)}</h3>
                    <p class="recette-description">${escapeHtml(r.description || 'Une délicieuse recette à découvrir')}</p>
                    <div class="recette-meta"><span><i class="fas fa-user-friends"></i> ${r.portions || 4} parts</span><span><i class="fas fa-utensils"></i> ${r.categorie || 'Plat'}</span></div>
                    <div>${tags.slice(0,3).map(t=>`<span class="tag">#${t.trim()}</span>`).join('')}</div>
                </div></div>`;
            }).join('');
            const pagination = document.getElementById('paginationContainer');
            if(pages<=1){ pagination.innerHTML=''; return; }
            let btns = `<button ${currentPage===1?'disabled':''} onclick="changePage(${currentPage-1})"><i class="fas fa-chevron-left"></i> Précédent</button>`;
            for(let i=1;i<=pages;i++) btns += `<button class="${currentPage===i?'active':''}" onclick="changePage(${i})">${i}</button>`;
            btns += `<button ${currentPage===pages?'disabled':''} onclick="changePage(${currentPage+1})">Suivant <i class="fas fa-chevron-right"></i></button>`;
            pagination.innerHTML = btns;
        }

        function changePage(p){ currentPage=p; filterRecettes(); }
        function filterRecettes(){
            currentPage=1;
            let filtered = [...recettesData];
            if(currentSearch) filtered = filtered.filter(r => r.nom && r.nom.toLowerCase().includes(currentSearch.toLowerCase()));
            if(currentCategorie !== 'all') filtered = filtered.filter(r => r.categorie === currentCategorie);
            renderRecettes(filtered);
        }

        document.getElementById('searchBtn')?.addEventListener('click',()=>{ currentSearch=document.getElementById('searchInput').value; filterRecettes(); });
        document.getElementById('searchInput')?.addEventListener('keypress',(e)=>{ if(e.key==='Enter'){ currentSearch=e.target.value; filterRecettes(); } });
        document.querySelectorAll('.filter-btn').forEach(btn=>{
            btn.addEventListener('click',()=>{
                document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
                btn.classList.add('active');
                currentCategorie=btn.dataset.categorie;
                filterRecettes();
            });
        });
        
        if(recettesData && recettesData.length>0) {
            renderRecettes(recettesData);
        } else {
            document.getElementById('recettesContainer').innerHTML = '<div class="empty-state"><i class="fas fa-utensils"></i><h3>Bientôt disponible</h3><p>De délicieuses recettes arrivent bientôt !</p></div>';
        }
    </script>
</body>
</html>