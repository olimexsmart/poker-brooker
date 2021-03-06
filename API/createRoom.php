<?php

require 'commonCode.php';

$sql = initSQLConnection();

// Retrieve inputs
$nStartCards = $_GET['nStartCards'];

// Generate room code - length of 5
$characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
$charactersLength = strlen($characters);
$randomString = '';
for ($i = 0; $i < 5; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
}
// FIXME check if code already exists and generate again if yes

$query = "INSERT INTO rooms (ID, code, nStartCards, currentTurn, gameOn)
            VALUES(NULL, '$randomString', $nStartCards, 0, 0)";

if ($sql->query($query)) {
    echo $randomString;
} else {
    http_response_code(506);
    die("Error: " . $query . " " . $sql->error . "\n");
}

$sql->close();
