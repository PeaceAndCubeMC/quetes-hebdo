<?php

function getData($type) {
    $url = "https://raw.githubusercontent.com/misode/mcmeta/registries/" . $type . "/data.min.json";
    $data = file_get_contents($url);
    $data = json_decode($data, true);
    return $data;
}

function getItems() {
    return getData("item");
}

function getEntityTypes() {
    return getData("entity_type");
}

function getRecipes() {
    return getData("recipe");
}

?>
