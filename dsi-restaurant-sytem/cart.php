<?php
session_start();

if(!isset($_SESSION['cart'])){
$_SESSION['cart'] = [];
}

/* ELIMINAR PRODUCTO */

if(isset($_GET['remove'])){

$id = $_GET['remove'];

unset($_SESSION['cart'][$id]);

}

/* CALCULAR TOTAL */

$total = 0;

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<title>Carrito de Compras</title>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<header>

<h1>Carrito de Compras</h1>

<nav>

<a href="index.php">Inicio</a>
<a href="menu.php">Menú</a>

</nav>

</header>

<div class="cart-container">

<?php if(empty($_SESSION['cart'])){ ?>

<h2 style="text-align:center;">El carrito está vacío</h2>

<?php }else{ ?>

<table class="cart-table">

<tr>

<th>Producto</th>
<th>Precio</th>
<th>Cantidad</th>
<th>Total</th>
<th>Acción</th>

</tr>

<?php foreach($_SESSION['cart'] as $id => $item){

$subtotal = $item['price'] * $item['qty'];

$total += $subtotal;

?>

<tr>

<td><?php echo $item['name']; ?></td>

<td>RD$ <?php echo $item['price']; ?></td>

<td><?php echo $item['qty']; ?></td>

<td>RD$ <?php echo $subtotal; ?></td>

<td>

<a href="cart.php?remove=<?php echo $id; ?>">

<button class="btn-delete">

Eliminar

</button>

</a>

</td>

</tr>

<?php } ?>

</table>

<div class="cart-total">

Total: RD$ <?php echo $total; ?>

</div>

<br>

<button class="checkout-btn" onclick="sendOrder()">

Realizar Pedido

</button>

<?php } ?>

</div>

<footer>

<p>© Sistema de pedidos | DSI Restaurant System</p>

</footer>

<script>

function sendOrder(){

let order = "Hola, quiero hacer el siguiente pedido:%0A%0A";

<?php if(!empty($_SESSION['cart'])){ foreach($_SESSION['cart'] as $item){ ?>

order += "🍔 <?php echo $item['name']; ?> ";
order += "x<?php echo $item['qty']; ?> ";
order += "- RD$<?php echo $item['price']; ?>%0A";

<?php }} ?>

order += "%0ATotal: RD$<?php echo $total; ?>";

let phone = "18298121617";

let url = "https://wa.me/"+phone+"?text="+order;

window.open(url,"_blank");

}

</script>

</body>
</html>