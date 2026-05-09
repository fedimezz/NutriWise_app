<?php // Access control is handled in controller/router (PHP), not in the view. ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier aliment - NutriWise</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/edit_user.css">

    <style>
        .current-image {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #ddd;
        }

        .image-placeholder {
            width: 90px;
            height: 90px;
            border-radius: 12px;
            background: #f1f8ee;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="logo">🌿 NutriWise</div>

        <nav>

            <a href="index.php?page=admin_dashboard">
                📊 Tableau de bord
            </a>

            <a href="index.php?page=admin_users">
                👥 Utilisateurs
            </a>

            <a href="index.php?page=admin_aliments" class="active">
                🥗 Aliments
            </a>

            <a href="index.php?page=admin_recettes">
                📖 Recettes
            </a>

            <a href="index.php?page=admin_plans">
                📅 Plans
            </a>

        </nav>

        <a href="index.php?page=logout" class="logout">
            🚪 Déconnexion
        </a>

    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <header>

            <h1>
                Modifier aliment :
                <?= htmlspecialchars($aliment['nom']) ?>
            </h1>

        </header>

        <div class="form-container">

            <!-- ERROR -->
            <?php if(isset($_SESSION['error'])): ?>

                <div style="
                    background:#ffebee;
                    color:#c62828;
                    padding:12px;
                    border-radius:8px;
                    margin-bottom:20px;
                ">
                    <?= $_SESSION['error'] ?>
                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

            <!-- FORM -->
            <form
                action="index.php?page=admin_edit_aliment&id=<?= (int)$aliment['id'] ?>"
                method="POST"
                enctype="multipart/form-data"
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= htmlspecialchars(csrf_token()) ?>"
                >

                <!-- NOM + CATEGORY -->
                <div class="form-row">

                    <!-- NOM -->
                    <div class="form-group">

                        <label for="nom">
                            Nom de l'aliment *
                        </label>

                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            required
                            value="<?= htmlspecialchars($aliment['nom']) ?>"
                        >

                    </div>

                    <!-- CATEGORY -->
                    <div class="form-group">

                        <label for="category_id">
                            Catégorie *
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            required
                        >

                            <option value="">
                                -- Choisir une catégorie --
                            </option>

                            <?php foreach($categories as $category): ?>

                                <option
                                    value="<?= $category['id'] ?>"
                                    <?= ($category['id'] == ($aliment['category_id'] ?? 0))
                                        ? 'selected'
                                        : '' ?>
                                >

                                    <?= htmlspecialchars($category['name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <!-- NUTRITION -->
                <h3 style="
                    margin:25px 0 15px;
                    color:#333;
                    font-size:18px;
                ">
                    Valeurs nutritionnelles (pour 100g)
                </h3>

                <!-- ROW -->
                <div class="form-row">

                    <div class="form-group">

                        <label for="calories">
                            Calories (kcal)
                        </label>

                        <input
                            type="number"
                            id="calories"
                            name="calories"
                            min="0"
                            step="0.1"
                            value="<?= htmlspecialchars((string)$aliment['calories']) ?>"
                        >

                    </div>

                    <div class="form-group">

                        <label for="proteines">
                            Protéines (g)
                        </label>

                        <input
                            type="number"
                            id="proteines"
                            name="proteines"
                            min="0"
                            step="0.1"
                            value="<?= htmlspecialchars((string)$aliment['proteines']) ?>"
                        >

                    </div>

                </div>

                <!-- ROW -->
                <div class="form-row">

                    <div class="form-group">

                        <label for="glucides">
                            Glucides (g)
                        </label>

                        <input
                            type="number"
                            id="glucides"
                            name="glucides"
                            min="0"
                            step="0.1"
                            value="<?= htmlspecialchars((string)$aliment['glucides']) ?>"
                        >

                    </div>

                    <div class="form-group">

                        <label for="lipides">
                            Lipides (g)
                        </label>

                        <input
                            type="number"
                            id="lipides"
                            name="lipides"
                            min="0"
                            step="0.1"
                            value="<?= htmlspecialchars((string)$aliment['lipides']) ?>"
                        >

                    </div>

                </div>

                <!-- ECO + IMAGE -->
                <div class="form-row">

                    <!-- ECO -->
                    <div class="form-group">

                        <label for="eco_score">
                            Éco-score *
                        </label>

                        <input
                            type="number"
                            id="eco_score"
                            name="eco_score"
                            min="0"
                            max="10"
                            step="0.1"
                            required
                            value="<?= htmlspecialchars((string)($aliment['eco_score'] ?? 0)) ?>"
                        >

                        <small style="color:#666;">
                            Note de 0 à 10
                        </small>

                    </div>

                    <!-- IMAGE -->
                    <div class="form-group">

                        <label for="image">
                            Image
                        </label>

                        <input
                            type="file"
                            id="image"
                            name="image"
                            accept="image/png,image/jpeg,image/webp"
                        >

                        <small style="color:#666;">
                            JPG, PNG, WebP - max 3MB
                        </small>

                        <!-- CURRENT IMAGE -->
                        <?php if(!empty($aliment['image'])): ?>

                            <?php
                            $imagePath = 'views/uploads/aliments/' . $aliment['image'];
                            ?>

                            <div style="
                                margin-top:15px;
                                display:flex;
                                align-items:center;
                                gap:15px;
                                flex-wrap:wrap;
                            ">

                                <img
                                    src="<?= htmlspecialchars($imagePath) ?>"
                                    alt="Image actuelle"
                                    class="current-image"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >

                                <div
                                    class="image-placeholder"
                                    style="display:none;"
                                >
                                    🥗
                                </div>

                                <div>

                                    <label style="
                                        display:flex;
                                        align-items:center;
                                        gap:8px;
                                    ">

                                        <input
                                            type="checkbox"
                                            name="keep_image"
                                            checked
                                            style="width:auto;"
                                        >

                                        Garder image actuelle

                                    </label>

                                    <small style="color:#666;">
                                        Décochez pour supprimer l'image
                                    </small>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

                <!-- DURABLE -->
                <div class="form-row">

                    <div class="form-group" style="
                        flex-direction:row;
                        align-items:center;
                        gap:10px;
                    ">

                        <input
                            type="checkbox"
                            id="durable"
                            name="durable"
                            <?= !empty($aliment['durable']) ? 'checked' : '' ?>
                            style="width:auto;"
                        >

                        <label for="durable" style="margin:0;">
                            Aliment durable 🌍
                        </label>

                    </div>

                </div>

                <!-- ACTIONS -->
                <div class="form-actions" style="margin-top:30px;">

                    <button type="submit" class="btn-save">
                        💾 Enregistrer
                    </button>

                    <a
                        href="index.php?page=admin_aliments"
                        class="btn-cancel"
                        style="text-decoration:none;"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>