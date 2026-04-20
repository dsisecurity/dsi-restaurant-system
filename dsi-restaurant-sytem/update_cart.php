<?php
session_start();

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

$index = (int)$_POST['index'];
$change = (int)$_POST['change'];

if(isset($_SESSION['cart'][$index])){

    $_SESSION['cart'][$index]['quantity'] += $change;

    // 🔥 eliminar si llega a 0
    if($_SESSION['cart'][$index]['quantity'] <= 0){
        array_splice($_SESSION['cart'], $index, 1);
    }
}

echo json_encode($_SESSION['cart']);