<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/database.php';

/* VALIDAR ID */
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Venta no válida");
}

$sale_id = (int) $_GET['id'];

/* VENTA + CLIENTE */
$sale = $conn->query("
SELECT s.*, c.name AS cliente
FROM sales s
LEFT JOIN customers c ON c.id = s.customer_id
WHERE s.id = $sale_id
")->fetch_assoc();

if(!$sale){
    die("Venta no encontrada");
}

/* DETALLES */
$detalles = $conn->query("
SELECT sd.*, p.name 
FROM sale_details sd
JOIN products p ON p.id = sd.product_id
WHERE sd.sale_id = $sale_id
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle de Venta</title>

<link rel="stylesheet" href="/dsi-restaurant-sytem/css/style.css">

<style>
.admin{
    padding:20px;
    color:white;
}

.card{
    background:white;
    color:black;
    border-radius:10px;
    padding:20px;
}

table{
    width:100%;
    margin-top:15px;
    border-collapse: collapse;
}

td, th{
    padding:10px;
    text-align:center;
}

th{
    background:#111;
    color:white;
}

/* TAGS */
.tag{
    padding:5px 10px;
    border-radius:5px;
    color:white;
    font-weight:bold;
}

.efectivo{background:#2ecc71;}
.tarjeta{background:#3498db;}
.transferencia{background:#9b59b6;}
.credito{background:#e74c3c;}

.ref{
    font-size:13px;
    color:#555;
}
</style>

</head>

<body>

<div class="admin">

<a href="sales.php" class="btn">⬅ Volver</a>

<h2>🧾 Detalle de Venta #<?= $sale_id ?></h2>

<div class="card">

<p><strong>Cliente:</strong> <?= $sale['cliente'] ?? 'Público General' ?></p>

<p>
<strong>Pago:</strong>
<span class="tag <?= $sale['payment_type'] ?>">
<?= strtoupper($sale['payment_type']) ?>
</span>
</p>

<?php if(!empty($sale['reference'])){ ?>
<p class="ref">
<strong>Referencia:</strong> <?= $sale['reference'] ?>
</p>
<?php } ?>

<p><strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($sale['date'] ?? 'now')) ?></p>

<table>

<tr>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Subtotal</th>
</tr>

<?php
$total = 0;

while($d = $detalles->fetch_assoc()){

$sub = $d['price'] * $d['quantity'];
$total += $sub;
?>

<tr>
<td><?= $d['name'] ?></td>
<td><?= $d['quantity'] ?></td>
<td>RD$ <?= number_format($d['price'],2) ?></td>
<td>RD$ <?= number_format($sub,2) ?></td>
</tr>

<?php } ?>

</table>

<h3 style="text-align:right;">
TOTAL: RD$ <?= number_format($sale['total'],2) ?>
</h3>

<?php if($sale['payment_type'] == 'credito'){ ?>
<p style="color:red;">
📒 Esta venta fue realizada a crédito
</p>
<?php } ?>

</div>

</div>

</body>
</html>