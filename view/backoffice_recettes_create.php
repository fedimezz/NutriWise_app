<?php
// Au début du fichier, avant le HTML, vérifiez que $aliments existe
if(!isset($aliments)) {
    // Si la variable n'existe pas, essayez de la charger
    require_once __DIR__ . '/../model/Database.php';
    require_once __DIR__ . '/../model/Aliment.php';
    $database = new Database();
    $db = $database->getConnection();
    $alimentObj = new Aliment($db);
    $aliments = $alimentObj->getAllForSelect();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une recette - NutriWise</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .dashboard { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: #1B4D1B; color: white; position: fixed; height: 100vh; }
        .sidebar .logo { padding: 25px 20px; font-size: 24px; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar nav a { display: block; padding: 12px 20px; color: rgba(255,255,255,0.8); text-decoration: none; transition: background 0.3s; }
        .sidebar nav a:hover { background: #2E7D32; }
        .sidebar nav a.active { background: #4CAF50; }
        .main-content { flex: 1; margin-left: 280px; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: #1B4D1B; }
        .admin-badge { background: #4CAF50; color: white; padding: 8px 16px; border-radius: 20px; }
        .container { background: white; border-radius: 10px; padding: 30px; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #333; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; }
        button { background: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 5px; margin-top: 20px; cursor: pointer; font-size: 16px; }
        .btn-back { background: #666; text-decoration: none; color: white; padding: 12px 20px; border-radius: 5px; display: inline-block; margin-top: 20px; margin-left: 10px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .aliments-list { max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 10px; margin-top: 5px; }
        .aliment-checkbox { margin: 5px 0; }
        .aliment-checkbox label { margin-left: 8px; font-weight: normal; display: inline; }
        .required:after { content: " *"; color: red; }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo"><span>🌿</span><span>NutriWise</span></div>
            <nav>
                <a href="../controller/index.php?area=back">📊 Dashboard</a>
                <a href="../controller/index.php?controller=aliment&action=index&area=back">🥗 Aliments</a>
                <a href="../controller/index.php?controller=recette&action=index&area=back" class="active">📖 Recettes</a>
                <a href="#">👥 Utilisateurs</a>
                <a href="#">📅 Plans</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>➕ Ajouter une recette</h1>
                <div class="admin-badge">Admin</div>
            </div>
            
            <div class="container">
                <?php if(isset($_SESSION['errors'])): ?>
                    <div class="error">
                        <strong>Erreurs :</strong><br>
                        <?php foreach($_SESSION['errors'] as $e): ?>• <?= htmlspecialchars($e) ?><br><?php endforeach; ?>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>
                
                <form method="POST" action="../controller/index.php?controller=recette&action=store&area=back">
                    <input type="hidden" name="area" value="back">
                    
                    <label class="required">Titre</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                    
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Décrivez brièvement votre recette..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    
                    <label class="required">Instructions</label>
                    <textarea name="instructions" rows="6" placeholder="1. Préparation des ingrédients&#10;2. Cuisson&#10;3. Dressage..." required><?= htmlspecialchars($_POST['instructions'] ?? '') ?></textarea>
                    
                    <label class="required">Durée (minutes)</label>
                    <input type="number" name="duree" value="<?= htmlspecialchars($_POST['duree'] ?? '30') ?>" min="1" max="1440" required>
                    
                    <label>Difficulté</label>
                    <select name="difficulte">
                        <option value="Facile" <?= ($_POST['difficulte'] ?? '') == 'Facile' ? 'selected' : '' ?>>Facile</option>
                        <option value="Moyen" <?= ($_POST['difficulte'] ?? '') == 'Moyen' ? 'selected' : '' ?>>Moyen</option>
                        <option value="Difficile" <?= ($_POST['difficulte'] ?? '') == 'Difficile' ? 'selected' : '' ?>>Difficile</option>
                    </select>
                    
                    <label>Saison</label>
                    <select name="saison">
                        <option value="Printemps" <?= ($_POST['saison'] ?? '') == 'Printemps' ? 'selected' : '' ?>>Printemps</option>
                        <option value="Été" <?= ($_POST['saison'] ?? '') == 'Été' ? 'selected' : '' ?>>Été</option>
                        <option value="Automne" <?= ($_POST['saison'] ?? '') == 'Automne' ? 'selected' : '' ?>>Automne</option>
                        <option value="Hiver" <?= ($_POST['saison'] ?? '') == 'Hiver' ? 'selected' : '' ?>>Hiver</option>
                        <option value="Toute l'année" <?= ($_POST['saison'] ?? '') == 'Toute l\'année' ? 'selected' : '' ?>>Toute l'année</option>
                    </select>
                    
                    <label>Statut</label>
                    <select name="statut" id="statut-select" onchange="toggleDatePublication()">
                        <option value="Brouillon" <?= ($_POST['statut'] ?? '') == 'Brouillon' ? 'selected' : '' ?>>Brouillon</option>
                        <option value="Programmée" <?= ($_POST['statut'] ?? '') == 'Programmée' ? 'selected' : '' ?>>Programmée</option>
                    </select>
                    
                    <!-- Calendrier de publication -->
                    <div id="date-publication-wrapper" style="display:none; margin-top:10px; padding:15px; background:#f0f8e8; border-radius:8px; border:1px solid #4CAF50;">
                        <label>📅 <b>Date et heure de publication</b> <small>(Heure Tunisie UTC+1)</small></label>
                        <input type="datetime-local" name="date_publication" id="date_publication">
                        <small style="color:#888;">🕐 Le calendrier est réglé sur le fuseau horaire de la Tunisie (Africa/Tunis)</small>
                    </div>
                    
                    <script>
                    function toggleDatePublication() {
                        var statut = document.getElementById('statut-select').value;
                        var wrapper = document.getElementById('date-publication-wrapper');
                        if(statut === 'Programmée') {
                            wrapper.style.display = 'block';
                            // Pré-remplir avec la date/heure actuelle en Tunisie
                            var now = new Date();
                            var tunisiaTime = new Date(now.toLocaleString('en-US', { timeZone: 'Africa/Tunis' }));
                            var y = tunisiaTime.getFullYear();
                            var m = String(tunisiaTime.getMonth()+1).padStart(2,'0');
                            var d = String(tunisiaTime.getDate()).padStart(2,'0');
                            var h = String(tunisiaTime.getHours()).padStart(2,'0');
                            var min = String(tunisiaTime.getMinutes()).padStart(2,'0');
                            document.getElementById('date_publication').value = y+'-'+m+'-'+d+'T'+h+':'+min;
                        } else {
                            wrapper.style.display = 'none';
                        }
                    }
                    document.addEventListener('DOMContentLoaded', function() { toggleDatePublication(); });
                    </script>
                    
                    <label>Score durabilité (⭐/5)</label>
                    <select name="score_durabilite">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <option value="<?= $i ?>" <?= ($_POST['score_durabilite'] ?? '3') == $i ? 'selected' : '' ?>><?= $i ?> ⭐</option>
                        <?php endfor; ?>
                    </select>
                    
                    <label class="required">Ingrédients (Aliments dans la recette)</label>
                    <div class="aliments-list">
                        <?php if(isset($aliments) && is_array($aliments) && count($aliments) > 0): ?>
                            <?php foreach($aliments as $aliment): ?>
                                <div class="aliment-checkbox">
                                    <input type="checkbox" name="aliments[]" value="<?= $aliment['id'] ?>" id="aliment_<?= $aliment['id'] ?>"
                                        <?= (isset($_POST['aliments']) && in_array($aliment['id'], $_POST['aliments'])) ? 'checked' : '' ?>>
                                    <label for="aliment_<?= $aliment['id'] ?>"><?= htmlspecialchars($aliment['nom']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: red;">⚠️ Aucun aliment trouvé. Veuillez d'abord <a href="../controller/index.php?controller=aliment&action=create&area=back">ajouter des aliments</a>.</p>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" <?= (!isset($aliments) || count($aliments) == 0) ? 'disabled' : '' ?>>Créer la recette</button>
                    <a href="../controller/index.php?controller=recette&action=index&area=back" class="btn-back">Annuler</a>
                </form>
            </div>
        </main>
    </div>
</body>
</html>