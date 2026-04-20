<?php

include '../config/database.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;

   
}
$result = $conn->query("SELECT * FROM orders");

?>

<h1>Pedidos</h1>

<?php while($row = $result->fetch_assoc()){ ?>

<div>

Pedido #<?php echo $row['id']; ?>

Cliente: <?php echo $row['customer_name']; ?>

Estado: <?php echo $row['status']; ?>

</div>

<?php } ?>