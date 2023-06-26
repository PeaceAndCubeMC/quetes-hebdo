<?php

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$advancementTemplate = array(
    "display" => array(
        "icon" => array(
            "item" => ""
        ),
        "title" => "",
        "description" => ""
    ),
    "parent" => "peaceandcube:quetes/root",
    "criteria" => array(),
    "rewards" => array(
        "function" => "peaceandcube:quetes/ticket"
    )
);

if (isset($_POST)) {
    $week = $_POST["week"];
    $year = $_POST["year"];
    $icon = $_POST["icon"];
    $description = $_POST["description"];
    $trigger = $_POST["trigger"];
    $recipe = $_POST["recipe"];
    $entity = $_POST["entity"];
    $item = $_POST["item"];
    $amount = $_POST["amount"];
    $copyQuestsPath = $_POST["copy-quests-path"] ?? false;

    $advancementFileName = $year . "-" . str_pad($week, 2, "0", STR_PAD_LEFT);
    $advancementPath = "peaceandcube:quetes/" . $advancementFileName;

    $advancement = $advancementTemplate;

    $advancement["display"]["icon"]["item"] = "minecraft:" . $icon;
    $advancement["display"]["title"] = getAdvancementTitle($week, $year);
    $advancement["display"]["description"] = $description;

    for ($i = $amount; $i > 0; $i--) {
        $criterion = array(
            "trigger" => $trigger,
            "conditions" => array()
        );

        switch ($trigger) {
            case "minecraft:recipe_crafted":
                $criterion["conditions"]["recipe"] = $recipe;
                break;
            case "minecraft:player_killed_entity":
                $criterion["conditions"]["entity"] = array(
                    "type" => "minecraft:" . $entity
                );
                break;
            case "minecraft:bred_animals":
                $criterion["conditions"]["child"] = array(
                    "type" => "minecraft:" . $entity
                );
                break;
            case "minecraft:enchanted_item":
            case "minecraft:consume_item":
            case "minecraft:villager_trade":
                $criterion["conditions"]["item"] = array(
                    "items" => array(
                        "minecraft:" . $item
                    )
                );
                break;
        }

        if ($i > 1) {
            $criterion["conditions"]["player"] = addPlayerAdvancementCheck($advancementPath, $i - 1);
        }

        $advancement["criteria"][$i] = $criterion;
    }

    if (!file_exists("advancements")) {
        mkdir("advancements", 0777, true);
    }

    // create advancement file
    $advancementFile = fopen("advancements/" . $advancementFileName . ".json", "w");
    fwrite($advancementFile, json_encode($advancement, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fclose($advancementFile);

    if ($copyQuestsPath) {
        // copy file to quests path
        $questsPath = $_ENV["QUESTS_PATH"];
        if (!str_ends_with($questsPath, "/")) {
            $questsPath .= "/";
        }
        copy("advancements/" . $advancementFileName . ".json", $questsPath . $advancementFileName . ".json");
    }
}

function getAdvancementTitle($week, $year) {
    $date = new DateTime();
    $date->setISODate($year, $week);
    $startDay = date("d", strtotime($date->format('Y-m-d') . " +6 day"));
    $startMonth = date("m", strtotime($date->format('Y-m-d') . " +6 day"));
    $endDay = date("d", strtotime($date->format('Y-m-d') . " +12 day"));
    $endMonth = date("m", strtotime($date->format('Y-m-d') . " +12 day"));
    return ltrim($startDay, "0") . " " . translateMonthToFrench($startMonth) . " - " . ltrim($endDay, "0") . " " . translateMonthToFrench($endMonth);
}

function translateMonthToFrench($month) {
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

function addPlayerAdvancementCheck($advancementPath, $index) {
    return array(
        "type_specific" => array(
            "type" => "player",
            "advancements" => array(
                $advancementPath => array(
                    $index => true
                )
            )
        )
    );
}

?>

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
    </body>
</html>