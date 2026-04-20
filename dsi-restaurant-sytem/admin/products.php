<?php
include __DIR__ . '/../config/database.php';
session_start();

/* SEGURIDAD */
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: ../menu.php");
    exit;
}

/* CATEGORIAS */
$categorias = $conn->query("SELECT * FROM categories");

/* =========================
   📦 GUARDAR PRODUCTO
========================= */
if(isset($_POST['guardar'])){

    $name = $_POST['name'];
    $category = $_POST['category'];
    $purchase = $_POST['purchase_price'];
    $sale = $_POST['price'];
    $stock = $_POST['stock'];

    $profit = (($sale - $purchase) / $purchase) * 100;

    /* IMAGEN */
    $image = "images/default.jpg";

    if(!empty($_FILES['image']['name'])){
        $ruta = "../images/";
        $nombre = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $ruta . $nombre);
        $image = "images/" . $nombre;
    }

    $conn->query("
    INSERT INTO products (name, category_id, purchase_price, price, profit, stock, image)
    VALUES ('$name', '$category', '$purchase', '$sale', '$profit', '$stock', '$image')
    ");
}

/* =========================
   ✏ EDITAR PRODUCTO
========================= */
if(isset($_POST['editar'])){

    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "UPDATE products SET name='$name', price='$price', stock='$stock'";

    /* SI SUBE NUEVA IMAGEN */
    if(!empty($_FILES['image']['name'])){
        $ruta = "../images/";
        $nombre = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $ruta . $nombre);

        $sql .= ", image='images/$nombre'";
    }

    $sql .= " WHERE id=$id";

    $conn->query($sql);
}

/* =========================
   ❌ ELIMINAR
========================= */
if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM products WHERE id = $id");
}

/* LISTAR */
$productos = $conn->query("
SELECT p.*, c.name as categoria
FROM products p
LEFT JOIN categories c ON c.id = p.category_id
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos</title>

<link rel="stylesheet" href="/dsi-restaurant-sytem/css/style.css">

<style>
.admin{padding:20px;color:white;}

.form-box{
    background:#1e1e2f;
    padding:15px;
    border-radius:12px;
    margin-bottom:20px;
}

input, select{
    padding:10px;
    margin:5px;
    border-radius:8px;
    border:none;
}

button{
    padding:10px 15px;
    border:none;
    border-radius:8px;
    background:#2ecc71;
    color:white;
    cursor:pointer;
}

/* 🔥 TARJETAS */
.grid{
    display:grid;
    grid-template-columns: repeat(auto-fill,minmax(200px,1fr));
    gap:15px;
}

.card{
    background:#111827;
    border-radius:15px;
    padding:10px;
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.3);
}

.card img{
    width:100%;
    height:130px;
    object-fit:cover;
    border-radius:10px;
}

.card h4{margin:5px 0;}
.card p{margin:3px;}

.actions a{
    color:red;
    text-decoration:none;
    font-size:18px;
}

.edit-form{
    margin-top:10px;
}
</style>

</head>

<body>

<div class="admin">

<a href="../menu.php" class="btn-back">⬅ Volver</a>

<h2>📦 Productos (Modo PRO)</h2>

<!-- =========================
     ➕ FORMULARIO
========================= -->
<div class="form-box">
<form method="POST" enctype="multipart/form-data">

<input name="name" placeholder="Nombre" required>

<select name="category" required>
<option value="">Categoría</option>
<?php 
$categorias->data_seek(0);
while($c = $categorias->fetch_assoc()){ ?>
<option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
<?php } ?>
</select>

<input name="purchase_price" type="number" step="0.01" placeholder="Compra" required>
<input name="price" type="number" step="0.01" placeholder="Venta" required>
<input name="stock" type="number" placeholder="Stock" required>

<input type="file" name="image">

<button name="guardar">💾 Guardar</button>

</form>
</div>

<!-- =========================
     🧠 TARJETAS
========================= -->
<div class="grid">

<?php while($p = $productos->fetch_assoc()){ ?>

<div class="card">

<img src="../<?= file_exists("../".$p['image']) ? $p['image'] : 'images/default.jpg' ?>">

<h4><?= $p['name'] ?></h4>
<p><?= $p['categoria'] ?></p>
<p>RD$ <?= number_format($p['price'],2) ?></p>
<p>Stock: <?= $p['stock'] ?></p>

<div class="actions">
<a href="?eliminar=<?= $p['id'] ?>" onclick="return confirm('Eliminar?')">❌</a>
</div>

<!-- EDITAR -->
<form method="POST" enctype="multipart/form-data" class="edit-form">

<input type="hidden" name="id" value="<?= $p['id'] ?>">

<input name="name" value="<?= $p['name'] ?>">
<input name="price" type="number" step="0.01" value="<?= $p['price'] ?>">
<input name="stock" type="number" value="<?= $p['stock'] ?>">

<input type="file" name="image">

<button name="editar">✏ Guardar</button>

</form>

</div>

<?php } ?>

</div>

</div>

</body>
</html>