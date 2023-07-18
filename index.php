<?php

include(__DIR__ . "/data_fetcher.php");

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/ico" href="./favicon.ico" />
    <link rel="stylesheet" href="styles.css" />
    <script src="js/conditions.js"></script>
    <title>Quêtes hebdo - PeaceAndCube</title>
</head>
<body>
    <h1>Quêtes hebdo</h1>

    <h2>Créer une quête</h2>
    <form action="generate.php" method="post">

        <fieldset>
            <legend>Affichage</legend>
            <div>
                <label for="week">Semaine</label>
                <input type="number" id="week" name="week" min="1" max="52" value="<?php echo date("W"); ?>" required>
            </div>
            <div>
                <label for="year">Année</label>
                <input type="number" id="year" name="year" min="<?php echo date("Y"); ?>" max="2100" value="<?php echo date("Y"); ?>" required>
            </div>
            <div>
                <label for="icon">Icône</label>
                <select id="icon" name="icon">
                    <?php
                        $items = getItems();
                        foreach ($items as $item) {
                            echo "<option value='" . $item . "'>" . $item . "</option>";
                        }
                    ?>
                </select>
            </div>
            <div>
                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Description" required>
            </div>
        </fieldset>

        <fieldset>
            <legend>Conditions</legend>
            <div>
                <label for="trigger">Type de condition</label>
                <select id="trigger" name="trigger">
                    <option value="minecraft:recipe_crafted">Craft d'une recette</option>
                    <option value="minecraft:player_killed_entity">Kill d'une entité</option>
                    <option value="minecraft:bred_animals">Reproduction d'animaux</option>
                    <option value="minecraft:enchanted_item">Item enchanté</option>
                    <option value="minecraft:consume_item">Item consommé</option>
                    <option value="minecraft:villager_trade">Trade avec un villageois</option>
                </select>
            </div>
            <div class="target" id="target-recipe">
                <label for="recipe">Recette</label>
                <select id="recipe" name="recipe">
                    <?php
                        $recipes = getRecipes();
                        foreach ($recipes as $recipe) {
                            echo "<option value='" . $recipe . "'>" . $recipe . "</option>";
                        }
                    ?>
                </select>
                <input type="text" id="filter-recipe" placeholder="Filtre" onkeyup="filterElements('filter-recipe', 'recipe')">
            </div>
            <div class="target" id="target-entity">
                <label for="entity">Entité</label>
                <select id="entity" name="entity">
                    <?php
                        $entityTypes = getEntityTypes();
                        foreach ($entityTypes as $entityType) {
                            echo "<option value='" . $entityType . "'>" . $entityType . "</option>";
                        }
                    ?>
                </select>
                <input type="text" id="filter-entity" placeholder="Filtre" onkeyup="filterElements('filter-entity', 'entity')">
            </div>
            <div class="target" id="target-item">
                <label for="item">Item</label>
                <select id="item" name="item">
                    <?php
                        $items = getItems();
                        foreach ($items as $item) {
                            echo "<option value='" . $item . "'>" . $item . "</option>";
                        }
                    ?>
                </select>
                <input type="text" id="filter-item" placeholder="Filtre" onkeyup="filterElements('filter-item', 'item')">
            </div>
            <div id="note"></div>
            <div>
                <label for="amount">Nombre de fois</label>
                <input type="number" id="amount" name="amount" min="1" value="1" required>
            </div>
        </fieldset>

        <fieldset>
            <legend>Options avancées</legend>
            <div>
                <input type="checkbox" id="copy-quests-path" name="copy-quests-path" checked>
                <label for="copy-quests-path">Copier le fichier dans le dossier des quêtes</label>
            </div>
            <div>
                <input type="checkbox" id="replace-existing" name="replace-existing">
                <label for="replace-existing">Remplacer le fichier de la semaine s'il existe déjà. Sinon, le nom du fichier se terminera par "-2", "-3", etc.</label>
            </div>
        </fieldset>

        <input type="submit" value="Submit">
    </form>

</body>
</html>
