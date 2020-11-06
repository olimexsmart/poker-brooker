<?php
// TODO could have sense to distinguish between exit and kick
// TODO when admin exits? Possible problems also in GUI if admin changes
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

// Delete player
$query = "DELETE FROM players WHERE ID = $playerID";
if (!$sql->query($query)) {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

echo "DONE";
$sql->close();
