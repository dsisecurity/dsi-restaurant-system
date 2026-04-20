<?php
session_start();

header('Content-Type: application/json');

$index = (int)$_POST['index'];

if(isset($_SESSION['cart'][$index])){
    array_splice($_SESSION['cart'], $index, 1);
}

echo json_encode(array_values($_SESSION['cart']));