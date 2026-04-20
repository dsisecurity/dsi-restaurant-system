<?php
session_start();
include __DIR__ . '/../config/database.php';

$hoy = date("Y-m-d");

$data = $conn->query("
SELECT 
SUM(total) as total,
SUM(CASE WHEN payment_type='efectivo' THEN total ELSE 0 END) as efectivo,
SUM(CASE WHEN payment_type='tarjeta' THEN total ELSE 0 END) as tarjeta,
SUM(CASE WHEN payment_type='transferencia' THEN total ELSE 0 END) as transferencia,
SUM(CASE WHEN payment_type='credito' THEN total ELSE 0 END) as credito
FROM sales
WHERE DATE(date) = '$hoy'
")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Reporte Diario</title>

<style>
body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(135deg,#0f172a,#1e293b);
    color:white;
}

.container{
    padding:20px;
}

.card{
    background:#111827;
    padding:20px;
    margin:10px 0;
    border-radius:10px;
}

h2{text-align:center;}
.valor{font-size:22px;font-weight:bold;}
</style>

</head>

<body>

<div class="container">

<h2>📊 Reporte Diario</h2>

<div class="card">
Total: <div class="valor">RD$ <?= number_format($data['total'],2) ?></div>
</div>

<div class="card">
💵 Efectivo: <div class="valor">RD$ <?= number_format($data['efectivo'],2) ?></div>
</div>

<div class="card">
💳 Tarjeta: <div class="valor">RD$ <?= number_format($data['tarjeta'],2) ?></div>
</div>

<div class="card">
🏦 Transferencia: <div class="valor">RD$ <?= number_format($data['transferencia'],2) ?></div>
</div>

<div class="card">
📒 Crédito: <div class="valor">RD$ <?= number_format($data['credito'],2) ?></div>
</div>

</div>

</body>
</html>