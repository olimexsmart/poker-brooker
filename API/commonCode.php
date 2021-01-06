<?php

function initSQLConnection()
{
    require 'login.php';
    // Create connection
    $sql = new mysqli($serverName, $username, $password, $dbname);
    // Check connection
    if ($sql->connect_error) {
        http_response_code(502);
        die("Connection failed: " . $sql->connect_error);
    }
    return $sql;
}

function queryWithResult($sql, $query)
{
    if (!$result = $sql->query($query)) {
        http_response_code(506);
        die("Error: " . $query . " " . $sql->error . "\n");
    }
    return $result;
}

function queryWithoutResult($sql, $query)
{
    if (!$result = $sql->query($query)) {
        http_response_code(506);
        die("Error: " . $query . " " . $sql->error . "\n");
    }
    return $result;
}
