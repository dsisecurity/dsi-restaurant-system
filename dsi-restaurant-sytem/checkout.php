<?php

session_start();

if(!isset($_SESSION['cart'])){
$_SESSION['cart']=[];
}

$total=0;

foreach($_SESSION['cart'] as $item){
$total += $item['price'];
}

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<title>Cobrar</title>
<link rel="stylesheet" href="...css/style.css">


<script>

function calcular(){

let total = <?php echo $total; ?>;

let descuento = document.getElementById("descuento").value;

let final = total - descuento;

if(final < 0){
final = 0;
}

document.getElementById("total_final").innerText = final;

}

</script>

</head>

<body>

<div class="container">

<h1>💰 Cobrar</h1>

<?php

foreach($_SESSION['cart'] as $item){

?>

<div class="item">

<span><?php echo $item['name']; ?></span>

<span>RD$ <?php echo $item['price']; ?></span>

</div>

<?php } ?>

<div class="total">

Total: RD$ <?php echo $total; ?>

</div>

<label>Descuento</label>

<input type="number" id="descuento" value="0" onkeyup="calcular()">

<div class="total">

Total Final: RD$ <span id="total_final"><?php echo $total; ?></span>

</div>

<label>Método de pago</label>

<select>

<option>Efectivo</option>
<option>Tarjeta</option>
<option>Transferencia</option>

</select>

<button>

Confirmar Venta

</button>

<a class="back" href="menu.php">

⬅ Volver al menú

</a>

</div>

</body>
</html>