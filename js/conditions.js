document.addEventListener("DOMContentLoaded", function() {
    hideTargetDivs();
    showTargetDiv(document.getElementById("trigger").value);
    document.getElementById("trigger").addEventListener("change", function() {
        hideTargetDivs();
        showTargetDiv(this.value);
    });
});

function hideTargetDivs() {
    var targetDivs = document.getElementsByClassName("target");
    for (var i = 0; i < targetDivs.length; i++) {
        targetDivs[i].style.display = "none";
    }
    document.getElementById("note").innerHTML = "";
}

function showTargetDiv(value) {
    switch (value) {
        case "minecraft:recipe_crafted":
            document.getElementById("target-recipe").style.display = "block";
            break;
        case "minecraft:player_killed_entity":
        case "minecraft:bred_animals":
            document.getElementById("target-entity").style.display = "block";
            break;
        case "minecraft:enchanted_item":
            document.getElementById("target-item").style.display = "block";
            document.getElementById("note").innerHTML = "Note : Il s'agit de l'item après avoir été enchanté.";
            break;
        case "minecraft:consume_item":
        case "minecraft:villager_trade":
            document.getElementById("target-item").style.display = "block";
            break;
    }
}
