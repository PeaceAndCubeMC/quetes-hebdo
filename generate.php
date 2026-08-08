<?php

require __DIR__ . "/vendor/autoload.php";
include(__DIR__ . "/db.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$rewardFunction = "peaceandcube:quetes/ticket";
$advancementTemplate = array(
    "display" => array(
        "icon" => array(
            "id" => ""
        ),
        "title" => "",
        "description" => ""
    ),
    "parent" => "peaceandcube:quetes/root",
    "criteria" => array(),
    "rewards" => array(
        "function" => $rewardFunction
    )
);

if (isset($_POST)) {
    $week = $_POST["week"];
    $year = $_POST["year"];
    $icon = $_POST["icon"];
    $description = $_POST["description"];
    $trigger = $_POST["trigger"];
    $recipe = $_POST["recipe"];
    $potion = $_POST["potion"];
    $entity = $_POST["entity"];
    $item = $_POST["item"];
    $biome = $_POST["biome"];
    $amount = $_POST["amount"];

    $questsPath = $_ENV["QUESTS_PATH"];
    if (!str_ends_with($questsPath, "/")) {
        $questsPath .= "/";
    }

    $advancementFileName = $year . "-" . str_pad($week, 2, "0", STR_PAD_LEFT);

    // count existing quests starting with the same name in the folder
    $existingQuests = count(glob($questsPath . $advancementFileName . "*"));
    $advancementFileName .= "-" . ($existingQuests + 1);

    $advancementPath = "peaceandcube:quetes/" . $advancementFileName;

    $advancement = $advancementTemplate;

    $advancement["display"]["icon"]["id"] = "minecraft:" . $icon;
    $advancement["display"]["title"] = getAdvancementTitle($week, $year);
    $advancement["display"]["description"] = $description;

    switch ($trigger) {
        case "minecraft:recipe_crafted":
        case "minecraft:crafter_recipe_crafted":
            $value = $recipe;
            break;
        case "minecraft:brewed_potion":
            $value = $potion;
            break;
        case "minecraft:player_killed_entity":
        case "minecraft:bred_animals":
        case "minecraft:tame_animal":
            $value = $entity;
            break;
        case "minecraft:enchanted_item":
        case "minecraft:consume_item":
        case "minecraft:villager_trade":
            $value = $item;
            break;
        case "minecraft:voluntary_exile":
            $value = $biome;
            break;
    }

    for ($i = $amount; $i > 0; $i--) {
        $criterion = array(
            "trigger" => $trigger,
            "conditions" => array()
        );

        switch ($trigger) {
            case "minecraft:recipe_crafted":
            case "minecraft:crafter_recipe_crafted":
                $criterion["conditions"]["recipe_id"] = $value;
                break;
            case "minecraft:brewed_potion":
                $criterion["conditions"]["potion"] = $value;
                break;
            case "minecraft:player_killed_entity":
            case "minecraft:tame_animal":
                $criterion["conditions"]["entity"] = array(
                    "entity_type" => withMinecraftPrefix($value)
                );
                break;
            case "minecraft:bred_animals":
                $criterion["conditions"]["child"] = array(
                    "entity_type" => withMinecraftPrefix($value)
                );
                break;
            case "minecraft:enchanted_item":
            case "minecraft:consume_item":
            case "minecraft:villager_trade":
                $criterion["conditions"]["item"] = array(
                    "items" => isTag($value)
                        ? withMinecraftPrefix($value)
                        : array(withMinecraftPrefix($value))
                );
                break;
            case "minecraft:voluntary_exile":
                $criterion["conditions"]["player"] = [
                    array(
                        "condition" => "minecraft:entity_properties",
                        "entity" => "this",
                        "predicate" => array(
                            "location" => array(
                                "biomes" => isTag($value)
                                    ? withMinecraftPrefix($value)
                                    : array(withMinecraftPrefix($value))
                            )
                        )
                    )
                ];
                break;
        }

        if ($i > 1) {
            if (array_key_exists("player", $criterion["conditions"])) {
                $criterion["conditions"]["player"][0]["predicate"] += addPlayerAdvancementCheck($advancementPath, $i - 1);
            } else {
                $criterion["conditions"]["player"] = addPlayerAdvancementCheck($advancementPath, $i - 1);
            }
        }

        $advancement["criteria"][$i] = $criterion;
    }

    if (!file_exists("advancements")) {
        mkdir("advancements", 0777, true);
    }

    // create advancement file
    $advancementFile = fopen("advancements/" . $advancementFileName . ".json", "w");
    fwrite($advancementFile, json_encode($advancement, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    fclose($advancementFile);

    // copy file to quests path
    copy("advancements/" . $advancementFileName . ".json", $questsPath . $advancementFileName . ".json");
    // save to sqlite
    addQuest($advancementFileName, $trigger, $value, $amount);
}

function isTag(string $value): bool
{
    return str_starts_with($value, "#");
}

function withMinecraftPrefix(string $value): string
{
    if (isTag($value)) {
        return "#minecraft:" . substr($value, 1);
    }
    return "minecraft:" . $value;
}

function getAdvancementTitle($week, $year): string
{
    $date = new DateTime();
    $date->setISODate($year, $week);
    $startDay = date("d", strtotime($date->format('Y-m-d') . " +6 day"));
    $startMonth = date("m", strtotime($date->format('Y-m-d') . " +6 day"));
    $endDay = date("d", strtotime($date->format('Y-m-d') . " +12 day"));
    $endMonth = date("m", strtotime($date->format('Y-m-d') . " +12 day"));
    return ltrim($startDay, "0") . " " . translateMonthToFrench($startMonth) . " - " . ltrim($endDay, "0") . " " . translateMonthToFrench($endMonth);
}

function translateMonthToFrench($month): string
{
    $map = array(
        "01" => "janvier",
        "02" => "février",
        "03" => "mars",
        "04" => "avril",
        "05" => "mai",
        "06" => "juin",
        "07" => "juillet",
        "08" => "août",
        "09" => "septembre",
        "10" => "octobre",
        "11" => "novembre",
        "12" => "décembre"
    );
    return $map[$month];
}

function addPlayerAdvancementCheck($advancementPath, $index): array
{
    return array(
        "type_specific/player" => array(
            "advancements" => array(
                $advancementPath => array(
                    $index => true
                )
            )
        )
    );
}

?>

<!DOCTYPE html>
<html>
    <script>
        function downloadFile() {
            var link = document.createElement("a");
            link.style.display = "none";
            link.download = "<?php echo $advancementFileName; ?>.json";
            link.href = "advancements/<?php echo $advancementFileName; ?>.json";
            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);
        }
    </script>

    <body>
        <button onclick="downloadFile()">Télécharger le fichier</button>
        <button onclick="window.location.href = 'index.php'">Retour</button>
    </body>
</html>
