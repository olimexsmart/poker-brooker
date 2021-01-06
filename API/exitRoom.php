<?php

require 'commonCode.php';

$sql = initSQLConnection();

// Get input args
$playerID = $_GET['playerID'];

// Delete possible cards in hand
$query = "DELETE FROM hands WHERE playerID = $playerID";
queryWithoutResult($sql, $query);

// If player was the dealer, we need to assign a new one
$query = "SELECT roomID, dealer FROM players WHERE ID = $playerID";
$result = queryWithResult($sql, $query);

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = (int) $resArr['roomID'];
    $isDealer = (int) $resArr['dealer'];

    // We can safely delete player now
    $query = "DELETE FROM players WHERE ID = $playerID";
    $result = queryWithResult($sql, $query);

    if ($isDealer) { // Need to assign a new one
        $query = "SELECT ID FROM players WHERE roomID = $roomID 
                    ORDER BY position LIMIT 1";

        $result = queryWithResult($sql, $query);

        if ($result->num_rows > 0) {
            $resArr = $result->fetch_assoc();
            $newAdminID = $resArr['ID'];

            // Confirm changes
            $query = "UPDATE players SET dealer = 1 WHERE ID = $newAdminID";
            $result = queryWithResult($sql, $query);
        } else {
            http_response_code(506);
            die("Something stupid and wrong happened in DB\n");
        }
    }
}










echo "DONE";
$sql->close();
