<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: /dsi-restaurant-sytem/admin/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>DSI Panel</title>

<link rel="stylesheet" href="/dsi-restaurant-sytem/css/style.css">
<link rel="manifest" href="/dsi-restaurant-sytem/manifest.json">

<meta name="theme-color" content="#0f172a">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body class="dashboard">

<!-- TOPBAR -->
<div class="topbar">

    <div class="logo">🔵 DSI POS</div>

    <div class="user">
        👤 <?= $_SESSION['user_name'] ?>

        <?php if(isset($_SESSION['turn_id'])): ?>
            <a href="/dsi-restaurant-sytem/admin/closed_turn.php" class="btn-nav danger">
                🔴 Cerrar Turno
            </a>
        <?php endif; ?>

        <a href="/dsi-restaurant-sytem/admin/logout.php" class="btn-nav">
            🚪 Salir
        </a>
    </div>

</div>

<!-- CONTENIDO -->
<div class="dashboard-container">

<h2>Panel Principal</h2>

<div class="cards">

<?php if(isset($_SESSION['turn_id'])){ ?>
    <a href="/dsi-restaurant-sytem/pos.php" class="card">
<?php } else { ?>
    <a href="/dsi-restaurant-sytem/admin/open_turn.php" class="card">
<?php } ?>

    <div class="icon">🛒</div>
    <h3>Ventas</h3>
    <p>
        <?php if(isset($_SESSION['turn_id'])) echo "Ir a caja"; else echo "Abrir turno"; ?>
    </p>
</a>

<a href="/dsi-restaurant-sytem/sales/sales.php" class="card">
    <div class="icon">📊</div>
    <h3>Historial</h3>
</a>

<a href="/dsi-restaurant-sytem/admin/products.php" class="card">
    <div class="icon">📦</div>
    <h3>Productos</h3>
</a>

<a href="/dsi-restaurant-sytem/kitchen.php" class="card">
    <div class="icon">👨‍🍳</div>
    <h3>Cocina</h3>
</a>

<a href="/dsi-restaurant-sytem/admin/clientes.php" class="card">
    <div class="icon">👥</div>
    <h3>Clientes</h3>
</a>

<a href="/dsi-restaurant-sytem/caja.php" class="card">
    <div class="icon">💰</div>
    <h3>Caja</h3>
</a>

<a href="/dsi-restaurant-sytem/admin/cierre_caja.php" class="card">
    <div class="icon">🔒</div>
    <h3>Cerrar Caja</h3>
</a>

<?php if($_SESSION['role'] == 'admin'){ ?>
<a href="/dsi-restaurant-sytem/admin/dashboard.php" class="card">
    <div class="icon">⚙</div>
    <h3>Administración</h3>
</a>
<?php } ?>

</div>

</div>

<!-- 🔥 SERVICE WORKER -->
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/dsi-restaurant-sytem/service-worker.js')
    .then(() => console.log("✅ Service Worker registrado"))
    .catch(err => console.log("❌ Error SW:", err));
}
</script>

<!-- 🔥 BOTÓN INSTALAR APP (MEJORADO) -->
<script>
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    console.log("🔥 Evento instalación detectado");

    e.preventDefault();
    deferredPrompt = e;

    let btn = document.createElement("button");
    btn.innerText = "📲 Instalar DSI";
    btn.style = `
        position:fixed;
        bottom:20px;
        right:20px;
        padding:15px;
        background:#22c55e;
        color:white;
        border:none;
        border-radius:10px;
        z-index:999;
        font-weight:bold;
        box-shadow:0 5px 15px rgba(0,0,0,0.3);
    `;

    btn.onclick = async () => {
        if(!deferredPrompt){
            alert("No disponible aún");
            return;
        }

        deferredPrompt.prompt();

        const { outcome } = await deferredPrompt.userChoice;

        console.log("Resultado instalación:", outcome);

        if(outcome === 'accepted'){
            btn.remove();
        }
    };

    document.body.appendChild(btn);
});
</script>
<script>
document.addEventListener("click", function () {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
    }
});
</script>
</body>
</html>