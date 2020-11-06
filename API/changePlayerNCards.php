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
$up = (int) $_GET['up'];

// Check if is really a dealer
$query = "SELECT roomID, dealer FROM players WHERE ID = $dealerID";
if (!$result = $sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}

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
    die("This player is not a dealer, he cannot change number of cards");
}

// Get player info
$query = "SELECT roomID, nCards FROM players WHERE ID = $playerID";
if (!$result = $sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $nCards = (int) $resArr['nCards'];
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

// Calc new value
if ($up)
    $nCards++;
else
    $nCards--;

if ($nCards < 0)
    $nCards = 0;

// Apply changes
$query = "UPDATE players SET nCards = $nCards WHERE ID = $playerID";
if (!$sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}

echo "DONE";
$sql->close();
