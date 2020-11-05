<?php

require 'login.php';

// Create connection
$sql = new mysqli($serverName, $username, $password, $dbname);
// Check connection
if ($sql->connect_error) {
    die("Connection failed: " . $sql->connect_error);
}

// Get input args
$playerID = $_GET['playerID'];

// Get player info
$query = "SELECT roomID FROM players WHERE ID = $playerID";
if (!$result = $sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = (int) $resArr['roomID'];
} else {
    http_response_code(403);
    die("playerID not recognized. " . $sql->error);
}

// Get how many players are in the room
$query = "SELECT COUNT(*) FROM players WHERE roomID = $roomID";
if (!$result = $sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}
if ($result->num_rows > 0) {
    $resArr = $result->fetch_row();
    $nPlayers = (int) $resArr[0];
} else {
    die("Could not count players. " . $sql->error);
}

// Get current turn value
$query = "SELECT currentTurn FROM rooms WHERE ID = $roomID";
if (!$result = $sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}
if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $currentTurn = (int) $resArr['currentTurn'];
} else {
    die("Could not get current turn value. " . $sql->error);
}

// Update turn
// FIXME this is wrong because can also be out of the game
$newTurn = ($currentTurn + 1) % $nPlayers;
$query = "UPDATE rooms SET currentTurn = $newTurn WHERE ID = $roomID";
if (!$sql->query($query)) {
    die("Error: " . $query . " " . $sql->error . "\n");
}

echo "DONE";
$sql->close();
