<?php

require 'commonCode.php';

$sql = initSQLConnection();

// Get input args
$playerID = $_GET['playerID'];

// Get player info
$query = "SELECT roomID, dealer, nCards FROM players WHERE ID = $playerID";
$result = queryWithResult($sql, $query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = (int) $resArr['roomID'];
    // Used to show all cards to players who lost 
    $nCards = (int) $resArr['nCards'];
    $IAmTheDealer = (int) $resArr['dealer'];
} else {
    http_response_code(403);
    die("playerID not recognized. It's possible you've been kicked out by the dealer." . $sql->error);
}

// Get if game is on
$query = "SELECT gameOn, currentTurn FROM rooms WHERE ID = $roomID";
$result = queryWithResult($sql, $query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $gameOn = (int) $resArr['gameOn'];
    $currentTurn = (int) $resArr['currentTurn'];
} else {
    http_response_code(403);
    die("Could not understand if game is on. " . $sql->error);
}

// Get information about all the players
$query = "SELECT * FROM players WHERE roomID = $roomID ORDER BY position";
$result = queryWithResult($sql, $query);

$players = array();
while ($resArr = $result->fetch_assoc()) {
    // General info
    $info = array();
    $info['name'] = $resArr['playerName'];
    $info['nCards'] = (int) $resArr['nCards'];
    $info['isDealer'] = $resArr['dealer'] == 1 ? true : false;
    $info['position'] = (int) $resArr['position'];

    // If it's me
    if ($resArr['ID'] == $playerID) {
        $info['itsMe'] = true;
    } else { // If it's NOT me
        $info['itsMe'] = false;
    }

    // Which is it
    if ($resArr['position'] == $currentTurn) {
        $info['hisTurn'] = true;
    } else {
        $info['hisTurn'] = false;
    }

    /*
    // If I am the dealer, I need to know other players IDs
    if ($IAmTheDealer == 1) {
        $info['ID'] = $resArr['ID'];
    } else {
        $info['ID'] = null;
    }
    */
    //Actually need to know ID of every player to match info with GUI
    // IDEA instead of database ID, it could be a hash of something
    $info['ID'] = (int) $resArr['ID'];

    // Load cards
    if ($gameOn == 0 || $nCards == 0) { // Of all players if game is off
        $thisPlayerID = $resArr['ID'];  // Or this request was sent by a player who lost
        $query = "SELECT d.cardHexCode, d.cardName FROM deck AS d 
                JOIN hands AS h 
                ON d.ID = h.cardID 
                WHERE h.playerID = $thisPlayerID";

        $resultC = queryWithResult($sql, $query);

        $cards = array();
        while ($resArrC = $resultC->fetch_row()) {
            array_push($cards, $resArrC);
        }
        $info['cards'] = $cards;
    } else if ($resArr['ID'] == $playerID) { // Just me if game is on
        $query = "SELECT d.cardHexCode, d.cardName FROM deck AS d 
                JOIN hands AS h 
                ON d.ID = h.cardID 
                WHERE h.playerID = $playerID";

        $resultC = queryWithResult($sql, $query);

        $cards = array();
        while ($resArrC = $resultC->fetch_row()) {
            array_push($cards, $resArrC);
        }
        $info['cards'] = $cards;
    } else { // No cards if game is on and it's not me
        $info['cards'] = null;
    }

    // Save info for this player
    array_push($players, $info);
}


// Formulate complete response and conclude
header('Content-Type: application/json');
echo json_encode(array("gameOn" => $gameOn == 0 ? false : true, "players" => $players));
$sql->close();
