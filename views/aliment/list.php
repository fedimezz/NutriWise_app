<?php // Access control is handled in controller/router (PHP), not in the view. ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des aliments - NutriWise</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/assets/css/users.css">

    <style>
        .aliment-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
        }

        .image-placeholder {
            display: inline-flex;
            width: 60px;
            height: 60px;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #f1f8ee;
            color: #4caf50;
            font-size: 1.5rem;
        }
    </style>
</head>

<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>

        <nav>
            <a href="index.php?page=admin_dashboard">📊 Dashboard</a>
            <a href="index.php?page=admin_users">👥 Users</a>
            <a href="index.php?page=admin_aliments" class="active">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes">📖 Recettes</a>
            <a href="index.php?page=admin_plans">📅 Plans</a>
        </nav>

        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <header>
            <h1>Gestion des aliments</h1>

            <button onclick="location.href='index.php?page=admin_add_aliment'">
                + Ajouter un aliment
            </button>
        </header>

        <!-- ALERTS -->
        <?php if(isset($_SESSION['success'])): ?>
            <div style="color:green; padding:10px;">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div style="color:red; padding:10px;">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- TABLE -->
        <table class="users-table">

            <thead>
            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Calories</th>
                <th>Macros</th>
                <th>Eco</th>
                <th>Durable</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>

            <?php if(empty($alimentsList)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:30px;">
                        Aucun aliment trouvé
                    </td>
                </tr>
            <?php else: ?>

                <?php foreach($alimentsList as $aliment): ?>

                    <?php
                    // ✅ IMAGE FIX (NO FUNCTION)
                    $imageUrl = null;

                    if (!empty($aliment['image'])) {
                        if (filter_var($aliment['image'], FILTER_VALIDATE_URL)) {
                            $imageUrl = $aliment['image'];
                        } else {
                            $imageUrl = 'views/uploads/aliments/' . $aliment['image'];
                        }
                    }

                    $ecoScore = (float)($aliment['eco_score'] ?? 0);
                    ?>

                    <tr>

                        <!-- IMAGE -->
                        <td>
                            <?php if($imageUrl): ?>
                                <img src="<?= $imageUrl ?>"
                                     class="aliment-image"
                                     alt="<?= htmlspecialchars($aliment['nom']) ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                <div class="image-placeholder" style="display:none;">🥗</div>
                            <?php else: ?>
                                <div class="image-placeholder">🥗</div>
                            <?php endif; ?>
                        </td>

                        <!-- NAME -->
                        <td>
                            <strong><?= htmlspecialchars($aliment['nom']) ?></strong>
                        </td>

                        <!-- CATEGORY -->
                        <td>
                            <?= htmlspecialchars($aliment['category_name'] ?? 'N/A') ?>
                        </td>

                        <!-- CALORIES -->
                        <td>
                            <?= (int)($aliment['calories'] ?? 0) ?> kcal
                        </td>

                        <!-- MACROS -->
                        <td style="font-size:13px;color:#666;">
                            P: <?= (float)($aliment['proteines'] ?? 0) ?>g <br>
                            G: <?= (float)($aliment['glucides'] ?? 0) ?>g <br>
                            L: <?= (float)($aliment['lipides'] ?? 0) ?>g
                        </td>

                        <!-- ECO SCORE -->
                        <td>
                            🌱 <?= number_format($ecoScore, 1) ?>/10
                        </td>

                        <!-- DURABLE -->
                        <td>
                            <?= ($aliment['durable'] ?? 0)
                                ? '🌍 Oui'
                                : '❌ Non' ?>
                        </td>

                        <!-- ACTIONS -->
                        <td>

                            <a href="index.php?page=admin_edit_aliment&id=<?= $aliment['id'] ?>"
                               style="background:#2196f3;color:white;padding:6px 10px;border-radius:6px;text-decoration:none;">
                                ✏️ Edit
                            </a>

                            <a href="index.php?page=admin_delete_aliment&id=<?= $aliment['id'] ?>"
                               onclick="return confirm('Delete this aliment?')"
                               style="background:#f44336;color:white;padding:6px 10px;border-radius:6px;text-decoration:none;margin-left:5px;">
                                🗑️ Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </main>

</div>

</body>
</html>