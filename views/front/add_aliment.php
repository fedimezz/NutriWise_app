<?php
// This legacy view should not handle POST/business logic.
// Use controllers/models for validation and persistence.
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Aliment</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Ajouter un aliment</h2>

    <form method="POST" action="index.php?page=admin_add_aliment" novalidate>
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

        <input type="text" name="nom" placeholder="Nom" required><br><br>
        <input type="text" name="categorie" placeholder="Catégorie" required><br><br>
        <input type="number" name="calories" placeholder="Calories"><br><br>
        <input type="number" name="proteines" placeholder="Protéines"><br><br>
        <input type="number" name="glucides" placeholder="Glucides"><br><br>
        <input type="number" name="lipides" placeholder="Lipides"><br><br>

        <label>
            <input type="checkbox" name="durable"> Durable
        </label><br><br>

        <button class="btn-primary">Ajouter</button>

    </form>
</div>

</body>
</html>