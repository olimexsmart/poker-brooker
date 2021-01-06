<?php

require 'commonCode.php';

$sql = initSQLConnection();

// Get input args
$playerID = $_GET['playerID'];

// Get player info
$query = "SELECT roomID FROM players WHERE ID = $playerID";
$result = queryWithResult($sql, $query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = (int) $resArr['roomID'];
} else {
    http_response_code(403);
    die("playerID not recognized. " . $sql->error);
}

// Get current turn value
$query = "SELECT currentTurn FROM rooms WHERE ID = $roomID";
$result = queryWithResult($sql, $query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $currentTurn = (int) $resArr['currentTurn'];
} else {
    die("Could not get current turn value. " . $sql->error);
}

// Get all the player card and position
$query = "SELECT nCards, position FROM players WHERE roomID = $roomID ORDER BY position ASC";
$result = queryWithResult($sql, $query);

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
queryWithoutResult($sql, $query);

echo "DONE";
$sql->close();
