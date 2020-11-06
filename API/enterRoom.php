<?php

require 'login.php';

// Create connection
$sql = new mysqli($serverName, $username, $password, $dbname);
// Check connection
if ($sql->connect_error) {
    http_response_code(502);
    die("Connection failed: " . $sql->connect_error);
}

// Retrieve inputs
$roomCode = $_GET['roomCode'];
$playerName = $_GET['playerName'];

// Get ID of the room with that code
$query = "SELECT ID, nStartCards FROM rooms where code = '$roomCode'";
if(!$result = $sql->query($query)){
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = $resArr['ID'];
    $nStartCards = $resArr['nStartCards'];
} else {
    http_response_code(404);
    die("Room code not valid");
}

// Create new user
$position = 0;
do {
    // If position 0 is valid this player will be the dealer
    $dealer = $position === 0 ? 1 : 0;

    $query = "INSERT INTO players (ID, roomID, playerName, position, nCards, dealer)
    VALUES(NULL, $roomID, '$playerName', $position, $nStartCards, $dealer)";
    $status = $sql->query($query);
    $position++;

    // If the error does not contain 'theOrder' break cycle
    if ($status !== true && strpos($sql->error, 'theOrder') === false) {
        http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
    } /*else if ($status !== true) {
        echo "Incremented position to: " . $position . "\n";
    }*/
    // Increment position until is valid
} while ($status !== true);

echo $sql->insert_id;;
$sql->close();
