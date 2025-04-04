function synchronizeQuests() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "./synchronize.php");
    xhr.send();
    return xhr.responseText; 
}
