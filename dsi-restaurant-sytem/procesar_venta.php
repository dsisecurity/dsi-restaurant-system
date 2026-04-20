<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();
include 'config/database.php';

/* VALIDAR CARRITO */
if(empty($_SESSION['cart'])){
    die("Carrito vacío");
}

/* DATOS */
$payment = $_POST['payment_type'] ?? '';
$reference = $_POST['reference'] ?? null;
$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
$table_id = isset($_POST['table_id']) ? (int)$_POST['table_id'] : 0;
$turn_id = $_SESSION['turn_id'] ?? 0;

/* VALIDACIONES */
if(empty($payment)){
    die("Debe seleccionar tipo de pago");
}

if($turn_id == 0){
    die("Turno no válido");
}

if($table_id == 0){
    die("⚠️ Debes seleccionar una mesa");
}

/* VALIDAR REFERENCIA */
if(($payment == 'tarjeta' || $payment == 'transferencia') && empty($reference)){
    die("⚠️ Debes ingresar el comprobante del pago");
}

/* TOTAL */
$total = 0;
foreach($_SESSION['cart'] as $item){
    $total += $item['price'] * $item['quantity'];
}

/* =========================
   🔔 VALIDAR CRÉDITO
========================= */
if($payment == 'credito'){

    if($customer_id == 0){
        die("⚠️ Debes seleccionar un cliente para crédito");
    }

    $cliente = $conn->query("
        SELECT balance, credit_limit 
        FROM customers 
        WHERE id = $customer_id
    ")->fetch_assoc();

    if(!$cliente){
        die("Cliente no encontrado");
    }

    if(($cliente['balance'] + $total) > $cliente['credit_limit']){
        die("⚠️ Límite de crédito excedido");
    }
}

/* =========================
   🍽 CREAR ORDEN (COMANDA)
========================= */
$conn->query("
INSERT INTO orders (table_id, status, type)
VALUES ($table_id, 'abierta', 'mesa')
");

$order_id = $conn->insert_id;

/* =========================
   💾 INSERTAR VENTA
========================= */
$customer_sql = ($customer_id == 0) ? "NULL" : $customer_id;
$reference_sql = $reference ? "'$reference'" : "NULL";

$conn->query("
INSERT INTO sales (total, payment_type, customer_id, turn_id, reference)
VALUES ($total, '$payment', $customer_sql, $turn_id, $reference_sql)
");

$sale_id = $conn->insert_id;

/* =========================
   🧾 DETALLES + COCINA + DIVISIÓN
========================= */
foreach($_SESSION['cart'] as $item){

    $product_id = $item['id'];
    $qty = $item['quantity'];
    $price = $item['price'];

    /* 🔥 GROUP ID (SI NO EXISTE, USA 0) */
    $group_id = isset($item['group_id']) ? (int)$item['group_id'] : 0;

    /* venta */
    $conn->query("
    INSERT INTO sale_details (sale_id, product_id, quantity, price)
    VALUES ($sale_id, $product_id, $qty, $price)
    ");

    /* cocina */
    $conn->query("
    INSERT INTO order_details (order_id, product_id, quantity, status, group_id)
    VALUES ($order_id, $product_id, $qty, 'pendiente', $group_id)
    ");
}

/* =========================
   📉 STOCK
========================= */
foreach($_SESSION['cart'] as $item){

    $conn->query("
    UPDATE products 
    SET stock = stock - {$item['quantity']}
    WHERE id = {$item['id']}
    ");
}

/* =========================
   📒 CRÉDITO
========================= */
if($payment == 'credito' && $customer_id > 0){

    $conn->query("
    UPDATE customers 
    SET balance = balance + $total
    WHERE id = $customer_id
    ");
}

/* =========================
   🍽 MESA OCUPADA
========================= */
$conn->query("
UPDATE tables 
SET status = 'ocupada'
WHERE id = $table_id
");

/* =========================
   🧹 LIMPIAR CARRITO
========================= */
unset($_SESSION['cart']);

/* =========================
   🖨 IMPRIMIR + REDIRIGIR
========================= */
echo "<script>
window.open('factura.php?id=$sale_id','_blank');
window.location='pos.php';
</script>";

exit;