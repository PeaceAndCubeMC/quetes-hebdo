<?php

include(__DIR__ . "/data_fetcher.php");
include(__DIR__ . "/db.php");

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
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-1.13.6/datatables.min.js"></script>
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
                <input type="text" id="filter-icon" placeholder="Filtre" onkeyup="filterElements('filter-icon', 'icon')">
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
                    <option value="minecraft:tame_animal">Animal apprivoisé</option>
                    <option value="minecraft:enchanted_item">Item enchanté</option>
                    <option value="minecraft:consume_item">Item consommé</option>
                    <option value="minecraft:villager_trade">Trade avec un villageois</option>
                    <option value="minecraft:voluntary_exile">Déclenchement d'un raid</option>
                </select>
            </div>
            <div class="target" id="target-recipe">
                <label for="recipe">Recette</label>
                <select id="recipe" name="recipe" onchange="updateIconSelect('recipe')">
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
                <select id="item" name="item" onchange="updateIconSelect('item')">
                    <?php
                        $items = getItems();
                        foreach ($items as $item) {
                            echo "<option value='" . $item . "'>" . $item . "</option>";
                        }
                    ?>
                </select>
                <input type="text" id="filter-item" placeholder="Filtre" onkeyup="filterElements('filter-item', 'item')">
            </div>

            <div class="target" id="target-biome">
                <label for="biome">Biome</label>
                <select id="biome" name="biome">
                    <?php
                        $biomes = getBiomes();
                        foreach ($biomes as $biome) {
                            echo "<option value='" . $biome . "'>" . $biome . "</option>";
                        }
                    ?>
                </select>
                <input type="text" id="filter-biome" placeholder="Filtre" onkeyup="filterElements('filter-biome', 'biome')">
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
                <input type="checkbox" id="save-quest" name="save-quest" checked>
                <label for="save-quest">Enregistrer la quête. Le fichier sera copié dans le dossier des quêtes, et la quête sera ajoutée dans la base de données.</label>
            </div>
            <div>
                <input type="checkbox" id="replace-existing" name="replace-existing">
                <label for="replace-existing">Remplacer le fichier de la semaine s'il existe déjà. Sinon, le nom du fichier se terminera par "-1", "-2", etc.</label>
            </div>
        </fieldset>

        <input type="submit" value="Submit">
    </form>

    <h2>Liste des quêtes précédentes</h2>
    <table id="previousQuests">
        <thead>
            <th>Nom</th>
            <th>Type de condition</th>
            <th>Valeur</th>
            <th>Nombre de fois</th>
        </thead>
        <tbody>
            <?php getQuests() ?>
        </tbody>
    </table>

</body>
</html>
