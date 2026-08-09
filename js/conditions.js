document.addEventListener("DOMContentLoaded", function() {
    hideTargetDivs();
    showTargetDiv(document.getElementById("trigger").value);

    document.getElementById("trigger").addEventListener("change", function() {
        hideTargetDivs();
        showTargetDiv(this.value);
    });

    let table = new DataTable('#previousQuests', {
        paging: false,
        order: [[0, 'desc']]
    });
});

function hideTargetDivs() {
    const targetDivs = document.getElementsByClassName("target");
    for (let i = 0; i < targetDivs.length; i++) {
        targetDivs[i].style.display = "none";
    }
    document.getElementById("note").innerHTML = "";
}

function showTargetDiv(value) {
    switch (value) {
        case "minecraft:recipe_crafted":
        case "minecraft:crafter_recipe_crafted":
            document.getElementById("target-recipe").style.display = "block";
            break;
        case "minecraft:brewed_potion":
            document.getElementById("target-potion").style.display = "block";
            break;
        case "minecraft:player_killed_entity":
            document.getElementById("target-entity").style.display = "block";
            document.getElementById("target-biome").style.display = "block";
            break;
        case "minecraft:bred_animals":
        case "minecraft:summoned_entity":
        case "minecraft:tame_animal":
            document.getElementById("target-entity").style.display = "block";
            break;
        case "minecraft:enchanted_item":
            document.getElementById("target-item").style.display = "block";
            document.getElementById("target-enchantment").style.display = "block";
            document.getElementById("note").innerHTML = "Note : Il s'agit de l'item après avoir été enchanté.";
            break;
        case "minecraft:consume_item":
        case "minecraft:villager_trade":
            document.getElementById("target-item").style.display = "block";
            break;
        case "minecraft:voluntary_exile":
            document.getElementById("target-biome").style.display = "block";
            break;
    }
}

function filterElements(filterId, selectId) {
    const filter = document.getElementById(filterId).value;
    const select = document.getElementById(selectId);
    // hide select options that don't match the filter
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].text.toLowerCase().indexOf(filter.toLowerCase()) === -1) {
            select.options[i].style.display = "none";
        } else {
            select.options[i].style.display = "block";
        }
    }
    // if the selected option is hidden, select the first visible option
    if (select.selectedIndex !== -1 && select.options[select.selectedIndex].style.display === "none") {
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].style.display === "block") {
                select.selectedIndex = i;
                break;
            }
        }
    }
}

function updateIconSelect(selectId) {
    const select = document.getElementById(selectId);
    const value = select.options[select.selectedIndex].value;
    if (value) {
        const iconSelect = document.getElementById("icon");
        for (let i = 0; i < iconSelect.options.length; i++) {
            if (iconSelect.options[i].value === value) {
                iconSelect.selectedIndex = i;
                break;
            }
        }
    }
}

function chooseRandomly(selectId) {
    const select = document.getElementById(selectId);
    const item = select.options[Math.floor(Math.random() * select.options.length)];
    select.value = item.value;
}
