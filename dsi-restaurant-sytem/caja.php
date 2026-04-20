<?php
include 'config/database.php';

if($_SESSION['role'] != 'cajero'){
    die("Solo caja");
}

/* ÓRDENES LISTAS */
$orders = $conn->query("
SELECT o.*, t.name as mesa
FROM orders o
LEFT JOIN tables t ON t.id = o.table_id
WHERE o.status = 'lista'
ORDER BY o.id ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Caja PRO</title>

<style>
body{
    background:#0f172a;
    color:white;
    font-family:Arial;
    margin:0;
}

h1{
    text-align:center;
    padding:15px;
    background:#020617;
}

.grid{
    display:grid;
    grid-template-columns: repeat(auto-fill,minmax(320px,1fr));
    gap:15px;
    padding:15px;
}

.card{
    background:#111827;
    padding:15px;
    border-radius:15px;
}

.grupo{
    background:#020617;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
}

h4{
    margin:5px 0;
    color:#22c55e;
}

button{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
}

.cobrar{background:#22c55e;color:black;}
</style>
</head>

<body>

<h1>💰 Caja PRO</h1>

<div class="grid">

<?php while($o = $orders->fetch_assoc()){ ?>

<div class="card">

<h2>
<?php 
$type = $o['type'] ?? 'mesa';

if($type == 'mesa'){
    echo "🍽 Mesa: " . ($o['mesa'] ?? 'N/A');
}elseif($type == 'llevar'){
    echo "🥡 Para llevar";
}else{
    echo "🛵 Delivery";
}
?>
</h2>

<?php

/* 🔥 OBTENER GRUPOS */
$grupos = $conn->query("
SELECT DISTINCT group_id 
FROM order_details 
WHERE order_id = {$o['id']}
");

$totalGeneral = 0;

/* 🔁 RECORRER GRUPOS */
while($g = $grupos->fetch_assoc()){

$gid = $g['group_id'];

echo "<div class='grupo'>";
echo "<h4>Cuenta #$gid</h4>";

$totalGrupo = 0;

$items = $conn->query("
SELECT od.*, p.name, p.price
FROM order_details od
JOIN products p ON p.id = od.product_id
WHERE od.order_id = {$o['id']} 
AND od.group_id = $gid
");

while($d = $items->fetch_assoc()){
    $sub = $d['price'] * $d['quantity'];
    $totalGrupo += $sub;

    echo "<p>{$d['name']} x{$d['quantity']} - RD$ ".number_format($sub,2)."</p>";
}

echo "<strong>Total grupo: RD$ ".number_format($totalGrupo,2)."</strong>";
echo "</div>";

$totalGeneral += $totalGrupo;
}

/* 💰 PROPINA */
$propina = $totalGeneral * 0.10;
$totalFinal = $totalGeneral + $propina;

?>

<hr>

<p>Subtotal: RD$ <?= number_format($totalGeneral,2) ?></p>
<p>Propina (10%): RD$ <?= number_format($propina,2) ?></p>

<h3>Total Final: RD$ <?= number_format($totalFinal,2) ?></h3>

<button class="cobrar"
onclick="cobrar(<?= $o['id'] ?>)">
💵 Cobrar
</button>

</div>

<?php } ?>

</div>

<script>

function cobrar(id){

    if(confirm("¿Cobrar esta orden?")){

        fetch("pagar_orden.php", {
            method:"POST",
            headers:{"Content-Type":"application/x-www-form-urlencoded"},
            body:"id="+id
        })
        .then(res => res.text())
        .then(data => {
            console.log(data);
            location.reload();
        });
    }
}

</script>

</body>
</html>