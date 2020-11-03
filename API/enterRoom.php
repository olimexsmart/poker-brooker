<?php

require 'login.php';

// Create connection
$sql = new mysqli($serverName, $username, $password, $dbname);
// Check connection
if ($sql->connect_error) {
    die("Connection failed: " . $sql->connect_error);
}

// Retrieve inputs
$roomCode = $_GET['roomCode'];
$playerName = $_GET['playerName'];

// Get ID of the room with that code
$query = "SELECT ID, nStartCards FROM rooms where code = '$roomCode'";
$result = $sql->query($query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = $resArr['ID'];
    $nStartCards = $resArr['nStartCards'];
} else {
    http_response_code(404);
    echo "Room code not valid";
}

// Create new user
$position = 0;
do {
    // If position 0 is valid this player will be the dealer
    if ($position === 0) {
        $dealer = true;
    }
    $query = "INSERT INTO players (ID, roomID, playerName, position, nCards, dealer)
    VALUES(NULL, $roomID, '$playerName', $position, $nStartCards, $dealer)";
    $status = $sql->query($query);
    $position++;

    // If the error does not contain 'theOrder' break cycle
    if ($status !== true && strpos($sql->error, 'theOrder') === false) {
        echo "Error: " . $query . " " . $sql->error . "\n";
        break;
    } else if ($status !== true) {
        echo "Incremented position to: " . $position . "\n";
    }
    // Increment position until is valid
} while ($status !== true);

echo "Player created";

$conn->close();
