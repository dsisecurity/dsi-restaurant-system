<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();
include __DIR__ . '/../config/database.php';

/* PROTEGER */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* VALIDAR ID */
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Crédito no válido");
}

$id = (int) $_GET['id'];

/* OBTENER CRÉDITO + CLIENTE */
$credito = $conn->query("
SELECT cr.*, c.name 
FROM credits cr
JOIN customers c ON c.id = cr.customer_id
WHERE cr.id = $id
")->fetch_assoc();

if(!$credito){
    die("Crédito no encontrado");
}

/* PROCESAR ABONO */
if(isset($_POST['abonar'])){

    $monto = floatval($_POST['monto']);

    if($monto <= 0){
        die("Monto inválido");
    }

    if($monto > $credito['balance']){
        die("⚠️ No puedes abonar más de lo que debe");
    }

    /* RESTAR BALANCE */
    $conn->query("
    UPDATE credits 
    SET balance = balance - $monto
    WHERE id = $id
    ");

    /* MARCAR COMO PAGADO */
    $conn->query("
    UPDATE credits
    SET status = 'pagado'
    WHERE id = $id AND balance <= 0
    ");

    /* ACTUALIZAR BALANCE CLIENTE */
    $conn->query("
    UPDATE customers
    SET balance = balance - $monto
    WHERE id = {$credito['customer_id']}
    ");

    header("Location: creditos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Abonar Deuda</title>

<link rel="stylesheet" href="/dsi-restaurant-sytem/css/style.css">

<style>

/* FONDO AKASIA */
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(135deg,#0f172a,#1e293b);
    color:white;
}

/* CONTENEDOR */
.container{
    max-width:500px;
    margin:60px auto;
    background:#111827;
    padding:25px;
    border-radius:15px;
    box-shadow:0 0 20px rgba(0,0,0,0.5);
}

/* TITULO */
h2{
    text-align:center;
    margin-bottom:20px;
}

/* INFO */
.info{
    background:#1f2937;
    padding:15px;
    border-radius:10px;
    margin-bottom:15px;
}

/* INPUT */
input{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:none;
    margin-top:10px;
    font-size:16px;
}

/* BOTON */
button{
    width:100%;
    padding:12px;
    margin-top:15px;
    background:#22c55e;
    border:none;
    border-radius:10px;
    color:white;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#16a34a;
}

/* VOLVER */
.back{
    display:inline-block;
    margin-bottom:15px;
    color:#38bdf8;
    text-decoration:none;
}

.back:hover{
    text-decoration:underline;
}

</style>

</head>

<body>

<div class="container">

<a href="creditos.php" class="back">⬅ Volver</a>

<h2>💰 Abonar Deuda</h2>

<div class="info">
<strong>Cliente:</strong> <?= $credito['name'] ?><br>
<strong>Deuda actual:</strong> RD$ <?= number_format($credito['balance'],2) ?>
</div>

<form method="POST">

<input type="number" step="0.01" name="monto" placeholder="Ingrese monto a abonar" required>

<button name="abonar">💵 Confirmar Abono</button>

</form>

</div>

</body>
</html>