<?php
session_start();
include '../config/database.php';

/* PROTEGER */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

/* PROCESAR */
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $monto = (float) $_POST['opening_amount'];
    $user_id = $_SESSION['user_id'];

    $conn->query("
    INSERT INTO turns (user_id, opening_amount, opened_at)
    VALUES ($user_id, $monto, NOW())
    ");

    $_SESSION['turn_id'] = $conn->insert_id;

    /* REDIRIGIR AL POS */
    header("Location: ../pos.php");
    exit;
}

/* DATOS */
$fecha = date("d/m/Y");
$hora = date("h:i A");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Abrir Turno</title>

<style>
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(135deg,#0f172a,#1e293b);
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    background:#111827;
    padding:30px;
    border-radius:15px;
    width:350px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.5);
}

h2{
    margin-bottom:10px;
}

.info{
    font-size:14px;
    opacity:0.8;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    margin-bottom:15px;
    font-size:16px;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#2563eb;
    color:white;
    font-size:16px;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}
</style>

</head>

<body>

<div class="card">

<h2>🟢 Abrir Turno</h2>

<div class="info">
👤 <?= $_SESSION['user_name'] ?><br>
📅 <?= $fecha ?><br>
🕒 <?= $hora ?>
</div>

<form method="POST">

<input 
type="number" 
name="opening_amount" 
placeholder="💵 Monto inicial en caja" 
required
>

<button type="submit">
Abrir Turno
</button>

</form>

</div>

</body>
</html>