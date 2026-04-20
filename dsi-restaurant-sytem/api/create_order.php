<?php

include '../config/database.php';

$name = $_POST['name'];
$phone = $_POST['phone'];
$address = $_POST['address'];

$sql = "INSERT INTO orders(customer_name,phone,address,total,status)
VALUES('$name','$phone','$address',0,'nuevo')";

$conn->query($sql);

$order_id = $conn->insert_id;

header("Location: ../menu.php");

?>