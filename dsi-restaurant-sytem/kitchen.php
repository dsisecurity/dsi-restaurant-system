<?php
include 'config/database.php';

/* ÓRDENES ABIERTAS */
$orders = $conn->query("
SELECT o.*, t.name as mesa
FROM orders o
LEFT JOIN tables t ON t.id = o.table_id
WHERE o.status = 'abierta'
ORDER BY o.id ASC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Cocina PRO</title>

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

/* GRID */
.grid{
    display:grid;
    grid-template-columns: repeat(auto-fill,minmax(300px,1fr));
    gap:15px;
    padding:15px;
}

/* CARD */
.card{
    background:#111827;
    padding:15px;
    border-radius:15px;
    box-shadow:0 0 10px rgba(0,0,0,0.3);
}

/* ITEMS */
.item{
    display:flex;
    justify-content:space-between;
    margin-bottom:8px;
    font-size:15px;
}

/* ESTADOS */
.pendiente{color:#ef4444;}
.preparando{color:#f59e0b;}
.listo{color:#22c55e;}

/* BOTONES */
button{
    width:100%;
    padding:10px;
    margin-top:5px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
}

.btn-preparar{background:#f59e0b;color:black;}
.btn-listo{background:#22c55e;color:black;}
.btn-cerrar{background:#3b82f6;color:white;}

hr{
    border:0;
    height:1px;
    background:#1f2937;
    margin:10px 0;
}
</style>
</head>

<body>

<h1>👨‍🍳 Cocina en Tiempo Real</h1>

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
$detalles = $conn->query("
SELECT od.*, p.name 
FROM order_details od
JOIN products p ON p.id = od.product_id
WHERE od.order_id = {$o['id']}
");

while($d = $detalles->fetch_assoc()){
$status = $d['status'] ?? 'pendiente';
?>

<div class="item">
    <span><?= $d['name'] ?> x<?= $d['quantity'] ?></span>
    <span class="<?= $status ?>">
        <?= strtoupper($status) ?>
    </span>
</div>

<button class="btn-preparar"
onclick="cambiarEstado(<?= $d['id'] ?>,'preparando')">
🔥 Preparar
</button>

<button class="btn-listo"
onclick="cambiarEstado(<?= $d['id'] ?>,'listo')">
✅ Listo
</button>

<hr>

<?php } ?>

<button class="btn-cerrar"
onclick="cerrarOrden(<?= $o['id'] ?>)">
🧾 Enviar a Caja
</button>

</div>

<?php } ?>

</div>

<script>

/* CAMBIAR ESTADO ITEM */
function cambiarEstado(id, estado){
    fetch("update_order_item.php", {
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"id="+id+"&status="+estado
    })
    .then(()=> location.reload());
}

/* CERRAR ORDEN → PASA A CAJA */
function cerrarOrden(id){
    if(confirm("¿Enviar orden a caja?")){
        fetch("update_order.php", {
            method:"POST",
            headers:{"Content-Type":"application/x-www-form-urlencoded"},
            body:"id="+id+"&status=lista"
        })
        .then(()=> location.reload());
    }
}

/* AUTO REFRESH */
setInterval(()=>{
    location.reload();
}, 5000);

</script>

</body>
</html>