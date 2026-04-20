<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include 'config/database.php';

/* ACEPTAR POST O GET */
$id = $_POST['id'] ?? $_GET['id'] ?? null;
$status = $_POST['status'] ?? $_GET['status'] ?? null;

if(!$id || !$status){
    echo "Faltan datos";
    exit;
}

$id = intval($id);

$sql = "UPDATE orders SET status='$status' WHERE id=$id";

if($conn->query($sql)){
    echo "OK";
}else{
    echo "Error: " . $conn->error;
}