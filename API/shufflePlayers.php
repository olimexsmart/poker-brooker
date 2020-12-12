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
$dealerID = $_GET['dealerID'];

// Check if is really a dealer
$query = "SELECT roomID, dealer FROM players WHERE ID = $dealerID";
if (!$result = $sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $isDealer = (int) $resArr['dealer'];
    $roomID = (int) $resArr['roomID'];
} else {
    http_response_code(403);
    die("dealerID not recognized. " . $sql->error);
}

// Check if is dealer
if ($isDealer === 0) {
    http_response_code(401);
    die("This player is not a dealer, he cannot change number of cards");
}


// We need a transaction in case a player enters just when we are updating the positions
$sql->begin_transaction();

try {
    // Get IDs of the players in this room
    $query = "SELECT ID FROM players WHERE roomid = $roomID";
    if (!$result = $sql->query($query)) {
        http_response_code(506);
        die("Error: " . $query . " " . $sql->error . "\n");
    }
    $playersIDs = $result->fetch_all();


    // Overwrite old position with big numbers to avoid uniqueness constrain error
    $numbers = range(100, 100 + count($playersIDs) - 1);
    for ($i = 0; $i < count($playersIDs); $i++) {
        $pos = $numbers[$i];
        $ID = (int) $playersIDs[$i][0];
        $query = "UPDATE players SET position = $pos WHERE ID = $ID";
        if (!$sql->query($query)) {
            http_response_code(506);
            die("Error: " . $query . " " . $sql->error . "\n");
        }
    }

    // Generate array with sequence of possible positions
    $numbers = range(0, count($playersIDs) - 1);
    shuffle($numbers);

    // Assign new positions
    for ($i = 0; $i < count($playersIDs); $i++) {
        $pos = $numbers[$i];
        $ID = (int) $playersIDs[$i][0];
        $query = "UPDATE players SET position = $pos WHERE ID = $ID";
        if (!$sql->query($query)) {
            http_response_code(506);
            die("Error: " . $query . " " . $sql->error . "\n");
        }
    }

    $sql->commit();
} catch (mysqli_sql_exception $exception) {
    $sql->rollback();

    die($exception);
}

echo "DONE";
$sql->close();
