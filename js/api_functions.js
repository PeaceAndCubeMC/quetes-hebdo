function synchronizeQuests() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "./synchronize.php");
    xhr.send();
    return xhr.responseText; 
}

function startBingoServer() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "./bingo_server.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("action=start");
    return xhr.responseText; 
}

function stopBingoServer() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "./bingo_server.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("action=stop");
    return xhr.responseText; 
}
