<?php

require 'commonCode.php';

$sql = initSQLConnection();

// Get input args
$dealerID = $_GET['dealerID'];
$playerID = $_GET['playerID'];

// Check if is really a dealer
$query = "SELECT roomID, dealer FROM players WHERE ID = $dealerID";
$result = queryWithResult($sql, $query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $isDealer = (int) $resArr['dealer'];
    $roomID1 = (int) $resArr['roomID'];
} else {
    http_response_code(403);
    die("dealerID not recognized. " . $sql->error);
}

// Check if is dealer
if ($isDealer === 0) {
    http_response_code(401);
    die("This player is not a dealer, he cannot change dealer");
}

// Get player info
$query = "SELECT roomID FROM players WHERE ID = $playerID";
$result = queryWithResult($sql, $query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID2 = (int) $resArr['roomID'];
} else {
    http_response_code(403);
    die("playerID not recognized. " . $sql->error);
}

// Check if players are in the same room
if ($roomID1 !== $roomID2) {
    http_response_code(409);
    die("Players not in the same room");
}

// Set player cards to zero
$query = "UPDATE players SET dealer = 1 WHERE ID = $playerID";
queryWithoutResult($sql, $query);

$query = "UPDATE players SET dealer = 0 WHERE ID = $dealerID";
queryWithoutResult($sql, $query);

echo "DONE";
$sql->close();
