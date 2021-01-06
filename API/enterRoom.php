<?php

require 'commonCode.php';

$sql = initSQLConnection();

// Retrieve inputs
$roomCode = $_GET['roomCode'];
$playerName = $_GET['playerName'];
$spectator = (int) $_GET['spectator'];

// Get ID of the room with that code
$query = "SELECT ID, nStartCards FROM rooms where code = '$roomCode'";
$result = queryWithResult($sql, $query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = $resArr['ID'];
    if ($spectator)
        $nStartCards = 0;
    else
        $nStartCards = $resArr['nStartCards'];
} else {
    http_response_code(404);
    die("Room code not valid");
}

// Get current max position
$query = "SELECT position FROM players WHERE roomID = $roomID ORDER BY position DESC LIMIT 1";
$result = queryWithResult($sql, $query);

// Interested in just one result so no while necessary
if ($resArr = $result->fetch_assoc()) {
    $position = (int) $resArr['position'];
} else {
    $position = 0; // If it is the first player to enter
}

// Just a precaution we try to increment if data not valid
$maxIter = 100;
$iter = 0;
$status = true;
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
        //die("Error: " . $query . " " . $sql->error . "\n");
        die("Name already taken.");
    } /*else if ($status !== true) {
        echo "Incremented position to: " . $position . "\n";
    }*/

    $iter++;
    if ($iter > $maxIter)
        break;

    // Increment position until is valid
} while ($status !== true);

if (!$status) {
    http_response_code(500);
    die("Could not find suitable position for player. 
        Possible max players reached.");
}

// Communicating player ID to front end
echo $sql->insert_id;

// Insert player into master record
$query = "INSERT INTO record (playerName) VALUES('$playerName')";
queryWithoutResult($sql, $query);

$sql->close();
