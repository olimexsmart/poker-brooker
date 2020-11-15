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

// Delete possible cards in hand
$query = "DELETE FROM hands WHERE playerID = $playerID";
if (!$sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

// If player was the dealer, we need to assign a new one
$query = "SELECT roomID, dealer FROM players WHERE ID = $playerID";
if (!$result = $sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = (int) $resArr['roomID'];
    $isDealer = (int) $resArr['dealer'];

    // We can safely delete player now
    $query = "DELETE FROM players WHERE ID = $playerID";
    if (!$sql->query($query)) {
        http_response_code(506);
        die("Error: " . $query . " " . $sql->error . "\n");
    }

    if ($isDealer) { // Need to assign a new one
        $query = "SELECT ID FROM players WHERE roomID = $roomID 
                    ORDER BY position LIMIT 1";

        if (!$result = $sql->query($query)) {
            http_response_code(506);
            die("Error: " . $query . " " . $sql->error . "\n");
        }

        if ($result->num_rows > 0) {
            $resArr = $result->fetch_assoc();
            $newAdminID = $resArr['ID'];

            // Confirm changes
            $query = "UPDATE players SET dealer = 1 WHERE ID = $newAdminID";
            if (!$sql->query($query)) {
                http_response_code(506);
                die("Error: " . $query . " " . $sql->error . "\n");
            }
        } else {
            http_response_code(506);
            die("Something stupid and wrong happened in DB\n");
        }
    }
}










echo "DONE";
$sql->close();
