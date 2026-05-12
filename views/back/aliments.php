<?php
function getAdminImageUrl($image) {
    if (empty($image)) return null;

    if (filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    }

    return 'views/assets/uploads/aliments/' . $image;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion aliments</title>

    <style>
        .aliment-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
        }

        .image-placeholder {
            width: 60px;
            height: 60px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#eee;
            border-radius:10px;
        }
    </style>
</head>

<body>

<h2>Gestion des aliments</h2>

<a href="index.php?page=admin_add_aliment">+ Ajouter aliment</a>

<table border="1" width="100%" cellpadding="10">
    <thead>
        <tr>
            <th>Image</th>
            <th>Nom</th>
            <th>Calories</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($alimentsList as $aliment): ?>

        <?php $img = getAdminImageUrl($aliment['image'] ?? null); ?>

        <tr>
            <td>
                <?php if ($img): ?>
                    <img src="<?= htmlspecialchars($img) ?>" class="aliment-image"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="image-placeholder" style="display:none;">🥗</div>
                <?php else: ?>
                    <div class="image-placeholder">🥗</div>
                <?php endif; ?>
            </td>

            <td><?= htmlspecialchars($aliment['nom']) ?></td>
            <td><?= (int)$aliment['calories'] ?> kcal</td>

            <td>
                <a href="index.php?page=admin_edit_aliment&id=<?= $aliment['id'] ?>">Edit</a>
                |
                <a href="index.php?page=admin_delete_aliment&id=<?= $aliment['id'] ?>">Delete</a>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>