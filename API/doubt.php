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

// Get player info
$query = "SELECT roomID FROM players WHERE ID = $playerID";
if (!$result = $sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

if ($result->num_rows > 0) {
    $resArr = $result->fetch_assoc();
    $roomID = (int) $resArr['roomID'];
} else {
    http_response_code(403);
    die("playerID not recognized. " . $sql->error);
}

$query = "UPDATE rooms SET gameOn = 0 WHERE ID = $roomID";
if (!$sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

echo "DONE";
$sql->close();
