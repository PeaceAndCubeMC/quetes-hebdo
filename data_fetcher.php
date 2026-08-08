<?php

include(__DIR__ . "/src/enums/RegistryType.php");

function getData(string $type, bool $tag = false): array
{
    $path = $tag ? "tag/" . $type : $type;
    $url = "https://raw.githubusercontent.com/misode/mcmeta/registries/" . $path . "/data.min.json";
    $data = file_get_contents($url);
    $data = json_decode($data, true);
    return $data;
}

function getElementsAndTags(string $type): array
{
    $elements = getData($type);
    $tags = getData($type, true);
    $tags = preg_filter("/^/", "#", array_values($tags));
    return array_merge($elements, $tags);
}

function getItems(bool $withTags = false): array
{
    if ($withTags) {
        return getElementsAndTags(RegistryType::ITEM);
    }
    return getData(RegistryType::ITEM);
}

function getEntityTypes(): array
{
    return getElementsAndTags(RegistryType::ENTITY_TYPE);
}

function getRecipes(): array
{
    return getData(RegistryType::RECIPE);
}

function getBiomes(): array
{
    return getElementsAndTags(RegistryType::BIOME);
}

function getPotions(): array
{
    return getData(RegistryType::POTION);
}
