<?php
include 'config/database.php';

$order_id = $_GET['order_id'];

$items = $conn->query("
SELECT od.*, p.name, p.price
FROM order_details od
JOIN products p ON p.id = od.product_id
WHERE od.order_id = $order_id
");

$total = 0;
?>

<h1>💵 Cobrar</h1>

<?php while($i = $items->fetch_assoc()){ 
$sub = $i['price'] * $i['quantity'];
$total += $sub;
?>

<div>
<?= $i['name'] ?> x<?= $i['quantity'] ?> - RD$ <?= $sub ?>
</div>

<?php } ?>

<h2>Total: RD$ <?= $total ?></h2>

<form action="finalizar_pago.php" method="POST">
<input type="hidden" name="order_id" value="<?= $order_id ?>">

<select name="payment_type">
<option value="efectivo">Efectivo</option>
<option value="tarjeta">Tarjeta</option>
<option value="transferencia">Transferencia</option>
</select>

<button type="submit">💵 Confirmar pago</button>
</form>