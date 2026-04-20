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

/* SOLO ADMIN */
if($_SESSION['role'] != 'admin'){
    die("⛔ Acceso solo para administradores");
}

/* AGREGAR */
if(isset($_POST['guardar'])){

    $name = $conn->real_escape_string($_POST['name']);

    if(empty($name)){
        die("Nombre inválido");
    }

    $conn->query("INSERT INTO categories (name) VALUES ('$name')");

    header("Location: categorias.php?ok=1");
    exit;
}

/* ELIMINAR */
if(isset($_GET['eliminar'])){

    $id = (int) $_GET['eliminar'];

    /* VALIDAR SI TIENE PRODUCTOS */
    $check = $conn->query("
    SELECT COUNT(*) as total 
    FROM products 
    WHERE category_id = $id
    ")->fetch_assoc();

    if($check['total'] > 0){
        die("⚠️ No puedes eliminar, tiene productos asignados");
    }

    $conn->query("DELETE FROM categories WHERE id = $id");

    header("Location: categorias.php?del=1");
    exit;
}

/* LISTAR */
$categorias = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Categorías</title>

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
    padding:15px;
    border-radius:10px;
}

input{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:none;
    margin-top:10px;
}

/* BOTON */
button{
    margin-top:10px;
    padding:10px;
    background:#22c55e;
    border:none;
    border-radius:8px;
    color:white;
    cursor:pointer;
}

/* TABLA */
table{
    width:100%;
    background:#111827;
    margin-top:20px;
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

/* BOTON DELETE */
.delete{
    color:#ef4444;
    text-decoration:none;
    font-weight:bold;
}

/* MENSAJES */
.success{
    background:#22c55e;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
}

.warning{
    background:#facc15;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
    color:black;
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

<h2>📂 Categorías</h2>

<?php if(isset($_GET['ok'])){ ?>
<div class="success">✅ Categoría agregada</div>
<?php } ?>

<?php if(isset($_GET['del'])){ ?>
<div class="warning">🗑 Categoría eliminada</div>
<?php } ?>

<!-- FORM -->
<form method="POST">

<input name="name" placeholder="Nombre de categoría" required>

<button name="guardar">➕ Guardar</button>

</form>

<!-- TABLA -->
<table>

<tr>
<th>ID</th>
<th>Nombre</th>
<th>Acción</th>
</tr>

<?php while($c = $categorias->fetch_assoc()){ ?>

<tr>

<td><?= $c['id'] ?></td>

<td><?= $c['name'] ?></td>

<td>
<a class="delete" href="?eliminar=<?= $c['id'] ?>" onclick="return confirm('¿Eliminar categoría?')">
❌
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>