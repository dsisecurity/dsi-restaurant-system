<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id']) || !isset($_SESSION['turn_id'])){
    header("Location: login.php");
    exit;
}

$turn_id = $_SESSION['turn_id'];

/* 🔥 CALCULAR TOTALES */
$totales = $conn->query("
SELECT 
SUM(total) as total_general,

SUM(CASE WHEN payment_type='efectivo' THEN total ELSE 0 END) as efectivo,
SUM(CASE WHEN payment_type='tarjeta' THEN total ELSE 0 END) as tarjeta,
SUM(CASE WHEN payment_type='transferencia' THEN total ELSE 0 END) as transferencia,
SUM(CASE WHEN payment_type='credito' THEN total ELSE 0 END) as credito

FROM sales
WHERE turn_id = $turn_id
")->fetch_assoc();

/* CERRAR */
if(isset($_POST['cerrar'])){

    $conn->query("
    UPDATE turns 
    SET closing_date = NOW(),
        total = {$totales['total_general']}
    WHERE id = $turn_id
    ");

    unset($_SESSION['turn_id']);

    header("Location: ../menu.php");
    exit;
}
?>

<h2>🔒 Cierre de Caja</h2>

<p>Total General: RD$ <?= number_format($totales['total_general'],2) ?></p>
<p>Efectivo: RD$ <?= number_format($totales['efectivo'],2) ?></p>
<p>Tarjeta: RD$ <?= number_format($totales['tarjeta'],2) ?></p>
<p>Transferencia: RD$ <?= number_format($totales['transferencia'],2) ?></p>
<p>Crédito: RD$ <?= number_format($totales['credito'],2) ?></p>

<form method="POST">
<button name="cerrar">🔒 Cerrar Caja</button>
</form>