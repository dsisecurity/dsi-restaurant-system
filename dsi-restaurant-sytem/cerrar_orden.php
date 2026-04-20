<?php
include 'config/database.php';

$id = $_POST['id'];

/* cerrar orden */
$conn->query("
UPDATE orders
SET status = 'cerrada'
WHERE id = $id
");

/* liberar mesa */
$conn->query("
UPDATE tables
SET status = 'libre'
WHERE id = (SELECT table_id FROM orders WHERE id = $id)
");