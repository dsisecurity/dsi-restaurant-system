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

/* LISTAR CRÉDITOS CON CLIENTE */
$creditos = $conn->query("
SELECT cr.*, c.name 
FROM credits cr
JOIN customers c ON c.id = cr.customer_id
ORDER BY cr.id DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Créditos</title>

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

/* FILAS */
tr:nth-child(even){
    background:#1f2937;
}

/* BOTONES */
.btn{
    padding:6px 12px;
    border-radius:8px;
    text-decoration:none;
    color:white;
    font-size:14px;
}

.btn-abonar{
    background:#22c55e;
}

.btn-abonar:hover{
    background:#16a34a;
}

/* ESTADOS */
.pendiente{
    color:#facc15;
    font-weight:bold;
}

.pagado{
    color:#22c55e;
    font-weight:bold;
}

.moroso{
    color:#ef4444;
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

<h2>💳 Panel de Créditos</h2>

<table>

<tr>
<th>ID</th>
<th>Cliente</th>
<th>Total</th>
<th>Balance</th>
<th>Estado</th>
<th>Acción</th>
</tr>

<?php while($c = $creditos->fetch_assoc()){ 

/* CLASE DE ESTADO */
$estado_class = 'pendiente';

if($c['status'] == 'pagado'){
    $estado_class = 'pagado';
}elseif($c['balance'] > 0 && $c['balance'] > ($c['total'] * 0.5)){
    $estado_class = 'moroso';
}
?>

<tr>

<td><?= $c['id'] ?></td>

<td><?= $c['name'] ?></td>

<td>RD$ <?= number_format($c['total'],2) ?></td>

<td>RD$ <?= number_format($c['balance'],2) ?></td>

<td class="<?= $estado_class ?>">
<?= strtoupper($c['status']) ?>
</td>

<td>
<?php if($c['status'] != 'pagado'){ ?>
<a class="btn btn-abonar" href="abonar.php?id=<?= $c['id'] ?>">
💰 Abonar
</a>
<?php } else { ?>
✔
<?php } ?>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>