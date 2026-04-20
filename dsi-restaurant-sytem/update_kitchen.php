<?php
include 'config/database.php';

$id = $_POST['id'];
$status = $_POST['status'];

$conn->query("
UPDATE order_details
SET status = '$status'
WHERE id = $id
");