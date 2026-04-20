<?php

$host = "localhost";
$user = "demostech";
$password = "dsi123456.";
$dbname = "dsi_restaurant";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>