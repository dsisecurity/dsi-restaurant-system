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

/* REGISTRAR MOVIMIENTO */
if(isset($_POST['guardar'])){

    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];
    $type = $_POST['type'];

    if($quantity <= 0){
        die("Cantidad inválida");
    }

    /* VALIDAR PRODUCTO */
    $producto = $conn->query("
        SELECT stock, name 
        FROM products 
        WHERE id = $product_id
    ")->fetch_assoc();

    if(!$producto){
        die("Producto no encontrado");
    }

    /* VALIDAR STOCK */
    if($type == 'salida' && $producto['stock'] < $quantity){
        die("❌ Stock insuficiente para: " . $producto['name']);
    }

    /* INSERTAR MOVIMIENTO */
    $conn->query("
        INSERT INTO inventory (product_id, quantity, type, date)
        VALUES ($product_id, $quantity, '$type', NOW())
    ");

    /* ACTUALIZAR STOCK */
    if($type == 'entrada'){
        $conn->query("
            UPDATE products 
            SET stock = stock + $quantity 
            WHERE id = $product_id
        ");
    }else{
        $conn->query("
            UPDATE products 
            SET stock = stock - $quantity 
            WHERE id = $product_id
        ");
    }

    header("Location: inventario.php?ok=1");
    exit;
}

/* PRODUCTOS */
$products = $conn->query("SELECT * FROM products ORDER BY name ASC");

/* MOVIMIENTOS */
$movements = $conn->query("
SELECT i.*, p.name 
FROM inventory i
JOIN products p ON p.id = i.product_id
ORDER BY i.date DESC
LIMIT 20
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario</title>

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
    padding:20px;
}

/* FORM */
form{
    background:#111827;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

select, input{
    width:100%;
    padding:10px;
    margin-top:10px;
    border-radius:8px;
    border:none;
}

/* BOTON */
button{
    margin-top:15px;
    padding:12px;
    width:100%;
    background:#22c55e;
    border:none;
    border-radius:10px;
    color:white;
    cursor:pointer;
}

button:hover{
    background:#16a34a;
}

/* TABLA */
table{
    width:100%;
    background:#111827;
    border-radius:10px;
    border-collapse: collapse;
}

th, td{
    padding:10px;
    text-align:center;
}

th{
    background:#020617;
    color:#38bdf8;
}

tr:nth-child(even){
    background:#1f2937;
}

/* COLORES */
.entrada{
    color:#22c55e;
    font-weight:bold;
}

.salida{
    color:#ef4444;
    font-weight:bold;
}

/* STOCK BAJO */
.bajo{
    color:#ef4444;
    font-weight:bold;
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

</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="back">⬅ Volver</a>

<h2>📦 Inventario</h2>

<?php if(isset($_GET['ok'])){ ?>
<div class="success">✅ Movimiento registrado</div>
<?php } ?>

<!-- FORM -->
<form method="POST">

<label>Producto</label>
<select name="product_id" required>
<option value="">Selecciona un producto</option>

<?php while($p=$products->fetch_assoc()){ 

$stock_class = ($p['stock'] <= 5) ? 'bajo' : '';
?>

<option value="<?= $p['id'] ?>">
<?= $p['name'] ?> (Stock: <?= $p['stock'] ?>)
</option>

<?php } ?>

</select>

<label>Cantidad</label>
<input type="number" name="quantity" required>

<label>Tipo</label>
<select name="type" required>
<option value="entrada">Entrada 📥</option>
<option value="salida">Salida 📤</option>
</select>

<button type="submit" name="guardar">💾 Registrar Movimiento</button>

</form>

<!-- MOVIMIENTOS -->
<h3>📊 Movimientos Recientes</h3>

<table>

<tr>
<th>ID</th>
<th>Producto</th>
<th>Cantidad</th>
<th>Tipo</th>
<th>Fecha</th>
</tr>

<?php while($m=$movements->fetch_assoc()){ ?>

<tr>

<td><?= $m['id'] ?></td>

<td><?= $m['name'] ?></td>

<td><?= $m['quantity'] ?></td>

<td class="<?= $m['type'] ?>">
<?= strtoupper($m['type']) ?>
</td>

<td><?= date("d/m/Y H:i", strtotime($m['date'])) ?></td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>