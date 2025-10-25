<?php

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

$startScript = $_ENV["BINGO_SERVER_START_SCRIPT"];
$stopScript = $_ENV["BINGO_SERVER_STOP_SCRIPT"];
if (empty($startScript) || empty($stopScript)) {
    sendResponse(503, "Bingo server screen is not configured properly.");
}

$action = $_POST["action"];
if ($action === "start") {
    execute($startScript);
} elseif ($action === "stop") {
    execute($stopScript);
}

function execute(string $script) {
    $output = exec("sh " . $script);
    sendResponse(empty($output) ? 200 : 409, $output);
}

function sendResponse($code, $reason = null) {
    $code = intval($code);
    header(trim("HTTP/1.1 $code $reason"));
    exit();
}

?>
