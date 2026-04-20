<?php
session_start();

/* SI YA ESTÁ LOGUEADO */
if(isset($_SESSION['user_id'])){
    header("Location: menu.php");
    exit;
}

/* SI NO → LOGIN */
header("Location: admin/login.php");
exit;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>DSI Restaurant System</title>
<link rel="stylesheet" href="/dsi-restaurant-sytem/css/style.css">

</head>

<body class="home">

<div class="home-container">

<h1>🍔 DSI Restaurant System</h1>

<p class="subtitle">
Ordena tu comida favorita rápido y fácil
</p>

<div class="home-buttons">

<a href="menu.php" class="btn-home">🍽 Ver Menú</a>

<a href="admin/dashboard.php" class="btn-home secondary">⚙ Panel Admin</a>

</div>

<div class="features">

<div class="feature">
📱 Pedido rápido<br>
<span>Desde cualquier dispositivo</span>
</div>

<div class="feature">
🚚 Delivery<br>
<span>Recibe en casa</span>
</div>

<div class="feature">
💳 Pago fácil<br>
<span>Efectivo o crédito</span>
</div>

</div>

</div>

</body>
</html>