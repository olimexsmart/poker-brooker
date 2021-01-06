<?php

require 'commonCode.php';

$sql = initSQLConnection();

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
$result = queryWithResult($sql, $query);

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
queryWithoutResult($sql, $query);

// Get other players IDs
$query = "SELECT ID, nCards FROM players WHERE roomID = $roomID";
$result = queryWithResult($sql, $query);

// Distribute cards to each player
$takenID = new SplFixedArray($nCards);
while ($resArr = $result->fetch_assoc()) {
    $nCards = $resArr['nCards'];
    $playerID = (int) $resArr['ID'];

    // Delete possible previous entries
    $query = "DELETE FROM hands WHERE playerID = $playerID";
    queryWithoutResult($sql, $query);

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
        queryWithoutResult($sql, $query);
    }
}



echo "DONE";
$sql->close();
