<?php

function openDb() {
    try {
        $pdo = new PDO("sqlite:" . dirname(__FILE__) . "/quests.sqlite");
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->query("CREATE TABLE IF NOT EXISTS quests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created DATETIME,
            name VARCHAR(255),
            trigger VARCHAR(255),
            value VARCHAR(255),
            amount INTEGER
        )");
    } catch (Exception $e) {
        echo "Impossible d'accéder à la base de données SQLite : " . $e->getMessage();
        exit();
    }
    return $pdo;
}

function getQuests() {
    $pdo = openDb();
    $result = $pdo->query("SELECT * FROM quests");

    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        // todo: echo
    }
}

function addQuest(string $name, string $trigger, string $value, int $amount) {
    $pdo = openDb();
    $request = $pdo->prepare("INSERT INTO quests (created, name, trigger, value, amount) VALUES (DATETIME('NOW'), :name, :trigger, :value, :amount)");
    $request->bindValue(":name", $name);
    $request->bindValue(":trigger", $trigger);
    $request->bindValue(":value", $value);
    $request->bindValue(":amount", $amount);
    $request->execute();
}

?>
