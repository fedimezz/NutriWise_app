<?php
// views/back/aliments.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des aliments - NutriWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="views/assets/css/admin-global.css">
    <style>
        .aliment-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #c8e6c9;
        }
        .image-placeholder {
            display: inline-flex;
            width: 60px;
            height: 60px;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #e8f5e9;
            color: #2e7d32;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
<div class="dashboard-container">

    <aside class="sidebar">
        <div class="logo">🌿 NutriWise</div>
        <nav>
            <a href="index.php?page=admin_dashboard">📊 Dashboard</a>
            <a href="index.php?page=admin_users">👥 Utilisateurs</a>
            <a href="index.php?page=admin_aliments" class="active">🥗 Aliments</a>
            <a href="index.php?page=admin_recettes">📖 Recettes</a>
            <a href="index.php?page=admin_plannings">📋 Plannings</a>
        </nav>
        <a href="index.php?page=logout" class="logout">🚪 Déconnexion</a>
    </aside>

    <main class="main-content">
        <header>
            <h1>Gestion des aliments</h1>
            <button class="btn-add" onclick="location.href='index.php?page=admin_add_aliment'">
                + Ajouter un aliment
            </button>
        </header>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert-success">✓ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert-error">⚠️ <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Calories</th>
                        <th>Protéines</th>
                        <th>Glucides</th>
                        <th>Lipides</th>
                        <th>Eco</th>
                        <th>Durable</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($alimentsList)): ?>
                        <tr><td colspan="10" style="text-align:center;padding:40px;">Aucun aliment trouvé</td></tr>
                    <?php else: ?>
                        <?php foreach($alimentsList as $aliment): ?>
                            <?php
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
                                <td>
                                    <?php if($imageUrl): ?>
                                        <img src="<?= $imageUrl ?>" class="aliment-image" alt="<?= htmlspecialchars($aliment['nom']) ?>">
                                    <?php else: ?>
                                        <div class="image-placeholder">🥗</div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($aliment['nom']) ?></strong></td>
                                <td><?= htmlspecialchars($aliment['category_name'] ?? 'N/A') ?></td>
                                <td><?= (int)($aliment['calories'] ?? 0) ?> kcal</td>
                                <td><?= (float)($aliment['proteines'] ?? 0) ?>g</td>
                                <td><?= (float)($aliment['glucides'] ?? 0) ?>g</td>
                                <td><?= (float)($aliment['lipides'] ?? 0) ?>g</td>
                                <td>⭐ <?= number_format($ecoScore, 1) ?>/10</td>
                                <td><?= ($aliment['durable'] ?? 0) ? '🌍 Oui' : '❌ Non' ?></td>
                                <td class="actions">
                                    <a href="index.php?page=admin_edit_aliment&id=<?= $aliment['id'] ?>" class="btn-edit">✏️</a>
                                    <a href="index.php?page=admin_delete_aliment&id=<?= $aliment['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet aliment ?')">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>