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

/* PROCESAR PAGO */
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $cliente_id = (int) $_POST['cliente_id'];
    $monto = (float) $_POST['monto'];

    if($monto <= 0){
        die("Monto inválido");
    }

    /* VALIDAR CLIENTE */
    $cliente = $conn->query("
        SELECT balance, name 
        FROM customers 
        WHERE id = $cliente_id
    ")->fetch_assoc();

    if(!$cliente){
        die("Cliente no encontrado");
    }

    if($cliente['balance'] <= 0){
        die("Este cliente no tiene deuda");
    }

    /* 🔥 GUARDAR PAGO */
    $conn->query("
        INSERT INTO payments (customer_id, amount, created_at)
        VALUES ($cliente_id, $monto, NOW())
    ");

    /* 🔥 RESTAR DEUDA (NO NEGATIVO) */
    $conn->query("
        UPDATE customers 
        SET balance = GREATEST(balance - $monto, 0)
        WHERE id = $cliente_id
    ");

    header("Location: cobrar_deuda.php?ok=1");
    exit;
}

/* CLIENTES CON DEUDA */
$clientes = $conn->query("
SELECT * FROM customers 
WHERE balance > 0 
ORDER BY balance DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cobrar Deuda</title>

<link rel="stylesheet" href="/dsi-restaurant-sytem/css/style.css">

<style>

/* FONDO */
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

/* SELECT + INPUT */
select, input{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:none;
    margin-top:10px;
    font-size:15px;
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

/* MENSAJE */
.success{
    background:#22c55e;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
    text-align:center;
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

<a href="dashboard.php" class="back">⬅ Volver</a>

<h2>💰 Cobrar Deuda</h2>

<?php if(isset($_GET['ok'])){ ?>
<div class="success">✅ Pago registrado correctamente</div>
<?php } ?>

<form method="POST" onsubmit="return confirmarPago()">

<select name="cliente_id" required>
<option value="">Seleccionar cliente</option>

<?php while($c = $clientes->fetch_assoc()){ ?>
<option value="<?= $c['id'] ?>">
<?= $c['name'] ?> - Debe RD$ <?= number_format($c['balance'],2) ?>
</option>
<?php } ?>

</select>

<input type="number" name="monto" step="0.01" placeholder="Monto a cobrar" required>

<button type="submit">💵 Cobrar</button>

</form>

</div>

<script>
function confirmarPago(){
    return confirm("¿Confirmar cobro de deuda?");
}
</script>

</body>
</html>