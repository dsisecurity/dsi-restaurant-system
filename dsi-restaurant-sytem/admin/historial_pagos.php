<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();
include __DIR__ . '/../config/database.php';

/* PROTEGER */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* PAGOS */
$pagos = $conn->query("
SELECT p.*, c.name 
FROM payments p
JOIN customers c ON c.id = p.customer_id
ORDER BY p.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial de Pagos</title>

<link rel="stylesheet" href="/dsi-restaurant-sytem/css/style.css">

<style>

/* FONDO */
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(135deg,#0f172a,#1e293b);
    color:white;
}

/* CONTENEDOR */
.container{
    padding:20px;
}

/* TABLA */
table{
    width:100%;
    background:#111827;
    border-radius:10px;
    margin-top:20px;
    border-collapse: collapse;
    overflow:hidden;
}

th, td{
    padding:12px;
    text-align:center;
}

th{
    background:#020617;
    color:#38bdf8;
}

tr:nth-child(even){
    background:#1f2937;
}

/* TAG MONTO */
.amount{
    color:#22c55e;
    font-weight:bold;
}

/* VOLVER */
.back{
    display:inline-block;
    margin-bottom:15px;
    color:#38bdf8;
    text-decoration:none;
}

.back:hover{
    text-decoration:underline;
}

</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="back">⬅ Volver</a>

<h2>🧾 Historial de Pagos</h2>

<table>

<tr>
<th>Cliente</th>
<th>Monto</th>
<th>Fecha</th>
</tr>

<?php while($p = $pagos->fetch_assoc()){ ?>

<tr>
<td><?= $p['name'] ?></td>

<td class="amount">
RD$ <?= number_format($p['amount'],2) ?>
</td>

<td>
<?= date("d/m/Y H:i", strtotime($p['created_at'])) ?>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>