<?php

require 'login.php';

// Create connection
$sql = new mysqli($serverName, $username, $password, $dbname);
// Check connection
if ($sql->connect_error) {
    die("Connection failed: " . $sql->connect_error);
}

// Get input args
$dealerID = $_GET['dealerID'];
$playerID = $_GET['playerID'];
$nCards = $_GET['nCards'];

// Check if is really a dealer
$query = "SELECT ID, roomID, dealer FROM players WHERE ID IN ($dealerID, $playerID)";
if (!$result = $sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}

$isDealer = 0;
if ($result->num_rows > 0) {
    // First row
    $resArr = $result->fetch_assoc();
    $roomID1 = (int) $resArr['roomID'];
    if ($resArr['ID'] == $dealerID)
        $isDealer = (int) $resArr['dealer'];

    // Second row, fetch only if different players
    if ($dealerID != $playerID)
        $resArr = $result->fetch_assoc();

    $roomID2 = (int) $resArr['roomID'];
    if ($resArr['ID'] == $dealerID)
        $isDealer = (int) $resArr['dealer'];
} else {
    http_response_code(403);
    die("dealerID not recognized. " . $sql->error);
}

// Check if is dealer
if ($isDealer === 0) {
    http_response_code(401);
    die("This player is not a dealer, he cannot change number of cards");
}

// Check if players are in the same room
if ($roomID1 !== $roomID2) {
    http_response_code(409);
    die("Players not in the same room");
}

// Apply changes
$query = "UPDATE players SET nCards = $nCards WHERE ID = $playerID";
if (!$sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}

echo "DONE";
$sql->close();
