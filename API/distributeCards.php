<?php

require 'login.php';

// Create connection
$sql = new mysqli($serverName, $username, $password, $dbname);
// Check connection
if ($sql->connect_error) {
    http_response_code(502);
    die("Connection failed: " . $sql->connect_error);
}

// Load the whole deck of cards
$query = "SELECT ID FROM deck";
if ($result = $sql->query($query)) {
    // Convert result to integer array
    $nCards = $result->num_rows;
    $cardIDs = new SplFixedArray($nCards);
    $i = 0;
    while ($row = $result->fetch_row()) {
        $cardIDs[$i] = (int) $row[0];
        $i++;
    }
} else {
    http_response_code(410);
    echo "Could not get cards. Error: " . $query . " " . $sql->error . "\n";;
}

// From player ID get its fellow players IDs and check
// he is a dealer
$dealerID = $_GET['dealerID'];
$query = "SELECT roomID, dealer FROM players WHERE ID = $dealerID";
if (!$result = $sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = (int) $resArr['roomID'];
    $isDealer = (int) $resArr['dealer'];
} else {
    http_response_code(403);
    die("dealerID not recognized. " . $sql->error);
}

// Check if is dealer
if ($isDealer === 0) {
    http_response_code(401);
    die("This player is not a dealer, he cannot distribute cards");
}

// Game is ON
$query = "UPDATE rooms SET gameOn = 1 WHERE ID = $roomID";
if (!$sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

// Get other players IDs
$query = "SELECT ID, nCards FROM players WHERE roomID = $roomID";
if (!$result = $sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

// Distribute cards to each player
$takenID = new SplFixedArray($nCards);
while ($resArr = $result->fetch_assoc()) {
    $nCards = $resArr['nCards'];
    $playerID = (int) $resArr['ID'];

    // Delete possible previous entries
    $query = "DELETE FROM hands WHERE playerID = $playerID";
    if (!$sql->query($query)) {
        http_response_code(506);
        die("Error: " . $query . " " . $sql->error . "\n");
    }

    for ($c = 0; $c < $nCards; $c++) {
        // Find available card
        $iter = 0;
        do {
            $r = random_int(0, 51);

            // Avoid infite loops
            if ($iter > 52) {
                http_response_code(503);
                die("Somehow we are trying distribute more than 52 cards.");
            }
            $iter++;
        } while ($takenID[$r] !== null);

        //Assign card to this player
        $takenID[$r] = true;
        $query = "INSERT INTO hands (ID, playerID, cardID)
                VALUES(NULL, $playerID, $r + 1)";
        if (!$sql->query($query)) {
            http_response_code(506);
            die("Error: " . $query . " " . $sql->error . "\n");
        }
    }
}



echo "DONE";
$sql->close();
