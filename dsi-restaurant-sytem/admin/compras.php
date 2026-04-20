<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
include '../config/database.php';


session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: admin/login.php");
    exit;
}
/* Registrar compra */
if(isset($_POST['guardar'])){
    $id = $_POST['id'];
    $supplier_id = $_POST['supplier_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $total = $quantity * $price;

    // Insertar en purchases
    $conn->query("INSERT INTO purchases (supplier_id, id, quantity, price, total)
                  VALUES ($supplier_id, $id, $quantity, $price, $total)");

    // Actualizar inventario
    $conn->query("INSERT INTO inventory (id, quantity, type) VALUES ($id, $quantity, 'entrada')");
    $conn->query("UPDATE products SET stock = stock + $quantity WHERE id = $id");

    header("Location: compras.php");
}

/* Cargar productos y proveedores */
$products = $conn->query("SELECT * FROM products");
$suppliers = $conn->query("SELECT * FROM suppliers");

/* Cargar compras recientes */
$purchases = $conn->query("
SELECT pu.id, s.name AS supplier, p.name AS product, pu.quantity, pu.price, pu.total, pu.date
FROM purchases pu
JOIN products p ON pu.id = p.id
JOIN suppliers s ON pu.supplier_id = s.id
ORDER BY pu.date DESC
LIMIT 20
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Compras</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="sidebar">
<h2>DSI POS</h2>
<a href="dashboard.php">📊 Dashboard</a>
<a href="products.php">🍔 Productos</a>
<a href="categorias.php">📂 Categorías</a>
<a href="inventario.php">📦 Inventario</a>
<a href="compras.php">🧾 Compras</a>
<a href="proveedores.php">🏢 Proveedores</a>
<a href="../menu.php">🖥 POS</a>
</div>

<div class="content">
<h1>Registrar Compra</h1>

<form method="POST">
<label>Proveedor</label>
<select name="supplier_id" required>
<option value="">Selecciona un proveedor</option>
<?php while($s = $suppliers->fetch_assoc()){ ?>
<option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
<?php } ?>
</select>

<label>Producto</label>
<select name="id" required>
<option value="">Selecciona un producto</option>
<?php
// Reset de resultado para reutilizar
$products->data_seek(0);
while($p = $products->fetch_assoc()){ ?>
<option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?> (Stock: <?php echo $p['stock']; ?>)</option>
<?php } ?>
</select>

<label>Cantidad</label>
<input type="number" name="quantity" required>

<label>Precio unitario</label>
<input type="number" step="0.01" name="price" required>

<button type="submit" name="guardar">💾 Registrar Compra</button>
</form>

<h2>Compras recientes</h2>
<table>
<tr>
<th>ID</th>
<th>Proveedor</th>
<th>Producto</th>
<th>Cantidad</th>
<th>Precio</th>
<th>Total</th>
<th>Fecha</th>
</tr>
<?php while($c = $purchases->fetch_assoc()){ ?>
<tr>
<td><?php echo $c['id']; ?></td>
<td><?php echo $c['supplier']; ?></td>
<td><?php echo $c['product']; ?></td>
<td><?php echo $c['quantity']; ?></td>
<td>RD$ <?php echo $c['price']; ?></td>
<td>RD$ <?php echo $c['total']; ?></td>
<td><?php echo $c['date']; ?></td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>