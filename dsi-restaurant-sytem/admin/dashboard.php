<?php
session_start();
include __DIR__ . '/../config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: admin/login.php");
    exit;
}

/* 🔒 SOLO ADMIN */
if($_SESSION['role'] != 'admin'){
    die("Solo administrador");
}

/* VENTAS HOY */
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
<title>Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    margin:0;
    font-family:Arial;
    background:#0f172a;
    color:white;
}

.container{
    padding:20px;
}

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
}

.card{
    background:#111827;
    padding:20px;
    border-radius:12px;
    text-align:center;
}

canvas{
    background:white;
    border-radius:10px;
    padding:10px;
}
</style>

</head>

<body>

<div class="container">

<h2>📊 Dashboard</h2>

<div class="cards">

<div class="card">Total<br>RD$ <?= number_format($data['total'],2) ?></div>
<div class="card">💵 <?= number_format($data['efectivo'],2) ?></div>
<div class="card">💳 <?= number_format($data['tarjeta'],2) ?></div>
<div class="card">🏦 <?= number_format($data['transferencia'],2) ?></div>
<div class="card">📒 <?= number_format($data['credito'],2) ?></div>

</div>

<h3>Ventas por tipo</h3>

<canvas id="grafico"></canvas>

</div>

<script>
new Chart(document.getElementById('grafico'), {
    type: 'doughnut',
    data: {
        labels: ['Efectivo','Tarjeta','Transferencia','Crédito'],
        datasets: [{
            data: [
                <?= $data['efectivo'] ?? 0 ?>,
                <?= $data['tarjeta'] ?? 0 ?>,
                <?= $data['transferencia'] ?? 0 ?>,
                <?= $data['credito'] ?? 0 ?>
            ]
        }]
    }
});
</script>

</body>
</html>