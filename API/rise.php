<?php

require 'login.php';

// Create connection
$sql = new mysqli($serverName, $username, $password, $dbname);
// Check connection
if ($sql->connect_error) {
    http_response_code(502);
    die("Connection failed: " . $sql->connect_error);
}

// Get input args
$playerID = $_GET['playerID'];

// Get player info
$query = "SELECT roomID FROM players WHERE ID = $playerID";
if (!$result = $sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = (int) $resArr['roomID'];
} else {
    http_response_code(403);
    die("playerID not recognized. " . $sql->error);
}

// Get current turn value
$query = "SELECT currentTurn FROM rooms WHERE ID = $roomID";
if (!$result = $sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}
if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $currentTurn = (int) $resArr['currentTurn'];
} else {
    die("Could not get current turn value. " . $sql->error);
}

// Get all the player card and position
$query = "SELECT nCards, position FROM players WHERE roomID = $roomID ORDER BY position ASC";
if (!$result = $sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

$posArr = array();
$currPlayer = 0;
$i = 0;
while ($resArr = $result->fetch_assoc()) {
    array_push($posArr, $resArr);
    // Find current player in table (a player could exit the game)
    if ($resArr['position'] == $currentTurn) {
        $currPlayer = $i;
    }
    $i++;
}
echo $currPlayer . '#';
// Find next with card in hand
$newTurn = -1;
for ($i = $currPlayer + 1; $i < count($posArr); $i++) {
    if ($posArr[$i]['nCards'] != 0) {
        $newTurn = (int) $posArr[$i]['position'];
        break;
    }
}
echo $newTurn . '#';
// Could be that we need to search from beginning
if ($newTurn === -1) {
    for ($i = 0; $i < count($posArr); $i++) {
        if ($posArr[$i]['nCards'] != 0) {
            $newTurn = (int) $posArr[$i]['position'];
            break;
        }
    }
}
echo $newTurn . '#';

// Update turn
$query = "UPDATE rooms SET currentTurn = $newTurn WHERE ID = $roomID";
if (!$sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

echo "DONE";
$sql->close();
