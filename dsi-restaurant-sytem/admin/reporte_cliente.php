<?php
session_start();
include '../config/database.php';

$clientes = $conn->query("SELECT * FROM customers");
?>

<h2>📊 Reporte por Cliente</h2>

<?php while($c = $clientes->fetch_assoc()){ 

$compras = $conn->query("
SELECT IFNULL(SUM(total),0) as total 
FROM sales 
WHERE customer_id = {$c['id']}
")->fetch_assoc()['total'];

$pagos = $conn->query("
SELECT IFNULL(SUM(amount),0) as total 
FROM payments 
WHERE customer_id = {$c['id']}
")->fetch_assoc()['total'];

?>

<div style="border:1px solid #ccc; padding:10px; margin:10px;">
<h3><?= $c['name'] ?></h3>

<p>🛒 Compras: RD$ <?= number_format($compras,2) ?></p>
<p>💰 Pagado: RD$ <?= number_format($pagos,2) ?></p>
<p>📒 Deuda actual: RD$ <?= number_format($c['balance'],2) ?></p>

</div>

<?php } ?>