<?php
session_start();
include 'config/database.php';

$order_id = $_POST['order_id'];
$payment = $_POST['payment_type'];

/* CALCULAR TOTAL */
$items = $conn->query("
SELECT od.*, p.price
FROM order_details od
JOIN products p ON p.id = od.product_id
WHERE od.order_id = $order_id
");

$total = 0;

while($i = $items->fetch_assoc()){
    $total += $i['price'] * $i['quantity'];
}

/* INSERTAR VENTA */
$conn->query("
INSERT INTO sales (total, payment_type)
VALUES ($total, '$payment')
");

$sale_id = $conn->insert_id;

/* MARCAR ORDEN COMO CERRADA */
$conn->query("
UPDATE orders 
SET status='cerrada'
WHERE id = $order_id
");

/* LIBERAR MESA */
$conn->query("
UPDATE tables 
SET status='libre'
WHERE id = (SELECT table_id FROM orders WHERE id=$order_id)
");

header("Location: factura.php?id=".$sale_id);