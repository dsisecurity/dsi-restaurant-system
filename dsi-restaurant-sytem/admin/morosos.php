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

/* CLIENTES CON DEUDA */
$clientes = $conn->query("
SELECT * FROM customers 
WHERE balance > 0 
ORDER BY balance DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Clientes Morosos</title>

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

/* NIVELES */
.bajo{
    color:#22c55e;
    font-weight:bold;
}

.medio{
    color:#facc15;
    font-weight:bold;
}

.alto{
    color:#ef4444;
    font-weight:bold;
}

/* BOTONES */
.btn{
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
    color:white;
    font-size:14px;
}

.btn-cobrar{
    background:#22c55e;
}

.btn-reporte{
    background:#3b82f6;
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

<h2>📊 Clientes Morosos</h2>

<table>

<tr>
<th>Cliente</th>
<th>Deuda</th>
<th>Nivel</th>
<th>Acciones</th>
</tr>

<?php while($c = $clientes->fetch_assoc()){ 

/* CLASIFICAR NIVEL */
$nivel = "bajo";
$clase = "bajo";

if($c['balance'] > 5000){
    $nivel = "ALTO";
    $clase = "alto";
}elseif($c['balance'] > 1000){
    $nivel = "MEDIO";
    $clase = "medio";
}else{
    $nivel = "BAJO";
}
?>

<tr>

<td><?= $c['name'] ?></td>

<td class="<?= $clase ?>">
RD$ <?= number_format($c['balance'],2) ?>
</td>

<td class="<?= $clase ?>">
<?= $nivel ?>
</td>

<td>
<a class="btn btn-cobrar" href="cobrar_deuda.php?id=<?= $c['id'] ?>">
💰 Cobrar
</a>

<a class="btn btn-reporte" href="reporte_cliente.php?id=<?= $c['id'] ?>">
📊 Ver
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>