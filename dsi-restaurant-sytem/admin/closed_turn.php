<?php
session_start();
include __DIR__ . '/../config/database.php';

/* VALIDAR LOGIN */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* VALIDAR TURNO */
if(!isset($_SESSION['turn_id'])){
    die("No hay turno abierto");
}

$turn_id = $_SESSION['turn_id'];

/* CALCULAR TOTALES */
$totales = $conn->query("
SELECT 
SUM(total) as total,
SUM(CASE WHEN payment_type='efectivo' THEN total ELSE 0 END) as efectivo,
SUM(CASE WHEN payment_type='tarjeta' THEN total ELSE 0 END) as tarjeta,
SUM(CASE WHEN payment_type='transferencia' THEN total ELSE 0 END) as transferencia,
SUM(CASE WHEN payment_type='credito' THEN total ELSE 0 END) as credito
FROM sales
WHERE turn_id = $turn_id
")->fetch_assoc();

/* CERRAR TURNO */
$conn->query("
UPDATE turns 
SET closing_time = NOW(),
closing_amount = {$totales['efectivo']}
WHERE id = $turn_id
");

/* LIMPIAR TURNO */
unset($_SESSION['turn_id']);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cierre de Caja</title>
<style>
body{font-family:Arial;background:#0f172a;color:white;text-align:center;padding:40px;}
.box{background:#111827;padding:20px;border-radius:10px;display:inline-block;}
h2{margin-bottom:20px;}
p{margin:8px 0;}
.btn{display:inline-block;margin-top:20px;padding:10px 20px;background:#22c55e;color:white;border-radius:8px;text-decoration:none;}
</style>
</head>

<body onload="window.print()">

<div class="box">
<h2>🧾 Cierre de Caja</h2>

<p>Total ventas: RD$ <?= number_format($totales['total'],2) ?></p>
<p>💵 Efectivo: RD$ <?= number_format($totales['efectivo'],2) ?></p>
<p>💳 Tarjeta: RD$ <?= number_format($totales['tarjeta'],2) ?></p>
<p>🏦 Transferencia: RD$ <?= number_format($totales['transferencia'],2) ?></p>
<p>📒 Crédito: RD$ <?= number_format($totales['credito'],2) ?></p>

<a href="../menu.php" class="btn">Volver</a>
</div>

</body>
</html>