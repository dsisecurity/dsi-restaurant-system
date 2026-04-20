<?php
include 'config/database.php';

$id = $_POST['id'];

/* MARCAR ORDEN COMO PAGADA */
$conn->query("
UPDATE orders 
SET status = 'pagada'
WHERE id = $id
");

/* LIBERAR MESA */
$conn->query("
UPDATE tables t
JOIN orders o ON o.table_id = t.id
SET t.status = 'libre'
WHERE o.id = $id
");

/* REDIRIGIR A FACTURA */
header("Location: factura.php?order_id=".$id);