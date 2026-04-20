<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/database.php';

/* LISTAR VENTAS CON CLIENTE */
$ventas = $conn->query("
SELECT s.*, c.name AS cliente
FROM sales s
LEFT JOIN customers c ON c.id = s.customer_id
ORDER BY s.id DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ventas</title>

<link rel="stylesheet" href="/dsi-restaurant-sytem/css/style.css">

<style>
.admin{
    padding:20px;
    color:white;
}

/* TABLA */
table{
    width:100%;
    background:white;
    color:black;
    border-radius:10px;
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

/* BOTONES */
a.btn{
    padding:5px 10px;
    background:#3498db;
    color:white;
    border-radius:5px;
    text-decoration:none;
}

/* TAGS DE PAGO */
.tag{
    padding:5px 8px;
    border-radius:5px;
    color:white;
    font-weight:bold;
}

.efectivo{background:#2ecc71;}
.tarjeta{background:#3498db;}
.transferencia{background:#9b59b6;}
.credito{background:#e74c3c;}

.ref{
    font-size:12px;
    color:#555;
}
</style>

</head>

<body>

<div class="admin">

<a href="../admin/dashboard.php" class="btn">⬅ Volver</a>

<h2>💰 Historial de Ventas</h2>

<table>

<tr>
<th>ID</th>
<th>Cliente</th>
<th>Total</th>
<th>Pago</th>
<th>Referencia</th>
<th>Fecha</th>
<th>Acción</th>
</tr>

<?php while($v = $ventas->fetch_assoc()){ ?>

<tr>

<td><?= $v['id'] ?></td>

<td>
<?= $v['cliente'] ? $v['cliente'] : 'Público General' ?>
</td>

<td>RD$ <?= number_format($v['total'],2) ?></td>

<td>
<span class="tag <?= $v['payment_type'] ?>">
<?= strtoupper($v['payment_type']) ?>
</span>
</td>

<td>
<?php if(!empty($v['reference'])){ ?>
    <span class="ref"><?= $v['reference'] ?></span>
<?php } else { ?>
    -
<?php } ?>
</td>

<td><?= date("d/m/Y H:i", strtotime($v['date'] ?? 'now')) ?></td>

<td>
<a class="btn" href="detalle.php?id=<?= $v['id'] ?>">
👁 Ver
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>