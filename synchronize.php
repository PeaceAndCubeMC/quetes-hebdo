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

$synchronizationScript = $_ENV["SYNCHRONIZATION_SCRIPT"];

if (empty($synchronizationScript)) {
    sendResponse(503, "Synchronization script is not configured properly.");
}

executeScript($synchronizationScript);

function executeScript($script) {
    $output = exec("sh " . $script);
    sendResponse(200, $output);
}

function sendResponse($code, $reason = null) {
    $code = intval($code);
    header(trim("HTTP/1.1 $code $reason"));
    exit();
}

?>
