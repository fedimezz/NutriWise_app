<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'models/AlimentModel.php';
    $model = new AlimentModel();

    $model->addAliment(
        $_POST['nom'],
        $_POST['categorie'],
        $_POST['calories'],
        $_POST['proteines'],
        $_POST['glucides'],
        $_POST['lipides'],
        isset($_POST['durable']) ? 1 : 0
    );

    header("Location: index.php?page=aliments");
}
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

    <form method="POST">

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