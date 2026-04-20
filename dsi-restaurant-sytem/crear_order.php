<?php
session_start();
include 'config/database.php';

if(empty($_SESSION['cart'])){
    die("Carrito vacío");
}

$order_type = $_POST['order_type'];
$table_id = (int)$_POST['table_id'];

/* VALIDAR */
if($order_type == 'mesa' && $table_id == 0){
    die("Selecciona mesa");
}

/* CREAR ORDEN */
$conn->query("
INSERT INTO orders (table_id, type, status)
VALUES ($table_id, '$order_type', 'abierta')
");

$order_id = $conn->insert_id;

/* DETALLES */
foreach($_SESSION['cart'] as $item){

    $conn->query("
    INSERT INTO order_details (order_id, product_id, quantity)
    VALUES ($order_id, {$item['id']}, {$item['quantity']})
    ");
}

/* LIMPIAR */
unset($_SESSION['cart']);

header("Location: pos.php");