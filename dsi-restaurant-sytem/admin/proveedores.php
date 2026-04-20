<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/database.php';

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: admin/login.php");
    exit;
}


/* Guardar proveedor */
if(isset($_POST['guardar'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $conn->query("INSERT INTO suppliers (name,email,phone,address) VALUES ('$name','$email','$phone','$address')");

    header("Location: proveedores.php");
}

/* Cargar proveedores */
$result = $conn->query("SELECT * FROM suppliers");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proveedores</title>
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
<h1>Proveedores</h1>

<form method="POST">
<label>Nombre</label>
<input type="text" name="name" required>

<label>Email</label>
<input type="email" name="email">

<label>Teléfono</label>
<input type="text" name="phone">

<label>Dirección</label>
<input type="text" name="address">

<button type="submit" name="guardar">💾 Guardar Proveedor</button>
</form>

<h2>Lista de proveedores</h2>
<table>
<tr>
<th>ID</th>
<th>Nombre</th>
<th>Email</th>
<th>Teléfono</th>
<th>Dirección</th>
<th>Fecha Registro</th>
</tr>

<?php while($row=$result->fetch_assoc()){ ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['email']; ?></td>
<td><?php echo $row['phone']; ?></td>
<td><?php echo $row['address']; ?></td>
<td><?php echo $row['created_at']; ?></td>
</tr>
<?php } ?>

</table>

</div>
</body>
</html>