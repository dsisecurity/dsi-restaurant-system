<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include 'config/database.php';

/* VALIDAR ID */
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Venta no válida");
}

$sale_id = (int) $_GET['id'];

/* VENTA + CLIENTE */
$sale = $conn->query("
SELECT s.*, c.name AS cliente, c.balance
FROM sales s
LEFT JOIN customers c ON c.id = s.customer_id
WHERE s.id = $sale_id
")->fetch_assoc();

if(!$sale){
    die("Venta no encontrada");
}

/* DETALLES */
$details = $conn->query("
SELECT sd.*, p.name 
FROM sale_details sd
JOIN products p ON p.id = sd.product_id
WHERE sd.sale_id = $sale_id
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Factura</title>

<style>

/* 🔥 RESET */
body{
    margin:0;
    font-family: 'Courier New', monospace;
    background:#f1f1f1;
}

/* 🧾 TICKET */
.ticket{
    width:300px;
    margin:auto;
    background:#fff;
    padding:15px;
}

/* LOGO */
.logo{
    text-align:center;
}
.logo img{
    width:70px;
}

/* TITULO */
.title{
    text-align:center;
    font-weight:bold;
    font-size:18px;
}

/* INFO */
.info{
    text-align:center;
    font-size:12px;
}

/* LINEA */
.line{
    border-top:1px dashed #000;
    margin:8px 0;
}

/* ITEM */
.item{
    display:flex;
    justify-content:space-between;
    font-size:13px;
}

.item-name{
    width:60%;
}

.item-price{
    width:40%;
    text-align:right;
}

/* TOTAL */
.total{
    font-size:15px;
    font-weight:bold;
    text-align:right;
}

/* TAG PAGO */
.tag{
    text-align:center;
    font-weight:bold;
    padding:4px;
    border-radius:5px;
    margin:5px 0;
    font-size:12px;
}

.efectivo{background:#2ecc71;color:white;}
.tarjeta{background:#3498db;color:white;}
.transferencia{background:#9b59b6;color:white;}
.credito{background:#e74c3c;color:white;}

/* FOOTER */
.footer{
    text-align:center;
    font-size:11px;
}

/* BOTONES (solo pantalla) */
.btn{
    width:100%;
    padding:10px;
    margin-top:10px;
    border:none;
    cursor:pointer;
}

.print{background:#2ecc71;color:white;}
.back{background:#3498db;color:white;}

/* 🖨 MODO IMPRESIÓN */
@media print{
    body{
        background:white;
    }
    .btn{
        display:none;
    }
}

</style>

</head>
<body onload="window.print()">

<div class="ticket">

<!-- LOGO -->
<div class="logo">
    <img src="images/logo.jpeg">
</div>

<div class="title">DSI RESTAURANT</div>

<div class="info">
DSI Security & IT<br>
Cutupú, La Vega<br>
829-812-1617
</div>

<div class="line"></div>

<div class="info">
Factura #: <?= $sale_id ?><br>
Fecha: <?= date("d/m/Y H:i", strtotime($sale['date'] ?? 'now')) ?><br>
Cliente: <?= $sale['cliente'] ?? 'Público General' ?>
</div>

<!-- TIPO DE PAGO -->
<div class="tag <?= $sale['payment_type'] ?>">
<?= strtoupper($sale['payment_type']) ?>
</div>

<!-- REFERENCIAS -->
<?php if(!empty($sale['reference'])){ ?>
<div class="info">
Ref: <?= $sale['reference'] ?>
</div>
<?php } ?>

<div class="line"></div>

<!-- PRODUCTOS -->
<?php if($details->num_rows > 0){ ?>
<?php while($d = $details->fetch_assoc()){ ?>

<div class="item">
    <div class="item-name">
        <?= $d['name'] ?> x<?= $d['quantity'] ?>
    </div>
    <div class="item-price">
        <?= number_format($d['price'] * $d['quantity'],2) ?>
    </div>
</div>

<?php } ?>
<?php } ?>

<div class="line"></div>

<div class="total">
TOTAL: RD$ <?= number_format($sale['total'],2) ?>
</div>

<!-- CRÉDITO -->
<?php if($sale['payment_type'] == 'credito'){ ?>
<div class="line"></div>

<div class="info">
📒 VENTA A CRÉDITO
</div>

<div class="info">
Deuda actual: RD$ <?= number_format($sale['balance'],2) ?>
</div>
<?php } ?>

<div class="line"></div>

<div class="footer">
Gracias por su compra 🙌<br>
Síguenos @dsi_security
</div>

<!-- BOTONES -->
<button class="btn print" onclick="window.print()">🖨 Imprimir</button>
<button class="btn back" onclick="window.location='menu.php'">⬅ Volver</button>

</div>

</body>
</html>