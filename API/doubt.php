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

$query = "UPDATE rooms SET gameOn = 0 WHERE ID = $roomID";
queryWithoutResult($sql, $query);

echo "DONE";
$sql->close();
