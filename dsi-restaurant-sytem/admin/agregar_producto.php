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

/* GUARDAR PRODUCTO */
if(isset($_POST['guardar'])){

    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    /* VALIDACIONES */
    if($price <= 0){
        die("Precio inválido");
    }

    if($stock < 0){
        die("Stock inválido");
    }

    /* IMAGEN */
    $image = null;

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

        $ruta = "../images/";
        $nombre = time() . "_" . basename($_FILES['image']['name']);
        $destino = $ruta . $nombre;

        if(move_uploaded_file($_FILES['image']['tmp_name'], $destino)){
            $image = "images/" . $nombre;
        }
    }

    /* INSERT */
    $sql = "
    INSERT INTO products (name, description, price, stock, image)
    VALUES ('$name','$description',$price,$stock," . ($image ? "'$image'" : "NULL") . ")
    ";

    if(!$conn->query($sql)){
        die("Error: " . $conn->error);
    }

    header("Location: products.php?ok=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Agregar Producto</title>

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
    max-width:600px;
    margin:50px auto;
    background:#111827;
    padding:25px;
    border-radius:15px;
}

/* TITULO */
h2{
    text-align:center;
}

/* INPUT */
input, textarea{
    width:100%;
    padding:12px;
    margin-top:10px;
    border-radius:10px;
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

/* VOLVER */
.back{
    color:#38bdf8;
    text-decoration:none;
}

</style>

</head>

<body>

<div class="container">

<a href="products.php" class="back">⬅ Volver</a>

<h2>➕ Agregar Producto</h2>

<form method="POST" enctype="multipart/form-data">

<label>Nombre</label>
<input type="text" name="name" required>

<label>Descripción</label>
<textarea name="description"></textarea>

<label>Precio</label>
<input type="number" step="0.01" name="price" required>

<label>Stock inicial</label>
<input type="number" name="stock" required>

<label>Imagen</label>
<input type="file" name="image">

<button type="submit" name="guardar">
💾 Guardar Producto
</button>

</form>

</div>

</body>
</html>