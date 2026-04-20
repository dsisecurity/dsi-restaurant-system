<?php
session_start();
include 'config/database.php';

/* SEGURIDAD */
if(!isset($_SESSION['user_id'])){
    header("Location: admin/login.php");
    exit;
}

if(!isset($_SESSION['turn_id'])){
    header("Location: admin/open_turn.php");
    exit;
}

/* ROLES */
if($_SESSION['role'] != 'mesero' && $_SESSION['role'] != 'admin'){
    die("Acceso denegado");
}

/* CARRITO */
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

/* DATA */
$productos = $conn->query("
SELECT p.*, c.name as categoria
FROM products p
LEFT JOIN categories c ON c.id = p.category_id
");

$categorias = $conn->query("SELECT * FROM categories");
$mesas = $conn->query("SELECT * FROM tables WHERE status='libre'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>POS DSI PRO</title>

<style>
body{margin:0;font-family:Arial;background:#0f172a;color:white;}
.pos-topbar{display:flex;justify-content:space-between;padding:10px;background:#020617;}
.pos-container{display:flex;height:100vh;}
.pos-products{width:70%;padding:15px;}
.pos-cart{width:30%;background:#020617;padding:15px;display:flex;flex-direction:column;}

.search{width:100%;padding:10px;border:none;border-radius:10px;background:#1f2937;color:white;margin-bottom:10px;}

.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:15px;}

.card{background:#111827;padding:10px;border-radius:10px;text-align:center;cursor:pointer;transition:.2s;}
.card:hover{transform:scale(1.05);}
.card img{width:100%;height:100px;object-fit:cover;border-radius:8px;}

.added{background:#22c55e;transform:scale(1.1);}

#cart-items{flex:1;overflow-y:auto;margin-bottom:10px;}

.cart-item{
    background:#111827;
    padding:10px;
    margin-bottom:8px;
    border-radius:10px;
}

.qty{display:flex;gap:5px;margin-top:5px;}
.qty button{background:#1f2937;border:none;color:white;padding:5px;border-radius:5px;}

select,button{
    width:100%;
    padding:10px;
    margin-top:10px;
    border:none;
    border-radius:10px;
}

.btn-send{background:#22c55e;font-weight:bold;}
</style>
</head>

<body>

<div class="pos-topbar">
    <div>👤 <?= $_SESSION['user_name'] ?></div>
    <div>
        <a href="menu.php" style="color:white;">🏠</a>
        <a href="admin/logout.php" style="color:white;">🚪</a>
    </div>
</div>

<div class="pos-container">

<!-- PRODUCTOS -->
<div class="pos-products">

<input type="text" id="buscador" class="search" placeholder="🔍 Buscar producto...">

<select id="grupoActivo">
    <option value="1">👤 Cliente 1</option>
</select>

<button onclick="nuevoGrupo()">➕ Nuevo Cliente</button>

<div class="grid" id="productos">

<?php while($p = $productos->fetch_assoc()){ ?>
<div class="card"
onclick="agregarProducto(<?= $p['id'] ?>, this)"
data-name="<?= strtolower($p['name']) ?>">

<img src="<?= file_exists($p['image']) ? $p['image'] : 'images/default.jpg' ?>">
<h4><?= $p['name'] ?></h4>
<p>RD$ <?= number_format($p['price'],2) ?></p>

</div>
<?php } ?>

</div>

</div>

<!-- CARRITO -->
<div class="pos-cart">

<h3>🛒 Carrito</h3>

<div id="cart-items"></div>
<h3 id="total">Total: RD$ 0</h3>

<form action="crear_orden.php" method="POST">

<select name="order_type" required>
<option value="">Tipo de pedido</option>
<option value="mesa">🍽 Mesa</option>
<option value="llevar">🥡 Para llevar</option>
<option value="delivery">🛵 Delivery</option>
</select>

<select name="table_id">
<option value="0">Seleccionar mesa</option>
<?php while($m = $mesas->fetch_assoc()){ ?>
<option value="<?= $m['id'] ?>"><?= $m['name'] ?></option>
<?php } ?>
</select>

<button type="submit" class="btn-send">
📤 Enviar a cocina
</button>

</form>

</div>

</div>

<script>

let cart = [];
let grupoActual = 1;

/* BUSCADOR */
document.getElementById("buscador").addEventListener("keyup", function(){
    let val = this.value.toLowerCase();
    document.querySelectorAll(".card").forEach(c=>{
        c.style.display = c.dataset.name.includes(val) ? "block" : "none";
    });
});

/* AGREGAR PRODUCTO */
function agregarProducto(id, el){

    fetch("add_to_cart.php", {
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"id="+id+"&group_id="+grupoActual
    })
    .then(res=>res.json())
    .then(data=>{
        actualizarCarrito(data);

        el.classList.add("added");
        setTimeout(()=>el.classList.remove("added"),300);
    });
}

/* ACTUALIZAR CARRITO */
function actualizarCarrito(data){
    cart = data;
    renderCart();
}

/* RENDER */
function renderCart(){

    let html="";
    let total=0;

    cart.forEach((item,index)=>{

        let sub=item.price*item.quantity;
        total+=sub;

        html+=`
<div class="cart-item">
    ${item.name} x${item.quantity}

    <div class="qty">
        <button onclick="cambiarCantidad(${index},-1)">-</button>
        <button onclick="cambiarCantidad(${index},1)">+</button>
        <button onclick="eliminarItem(${index})">🗑</button>
    </div>

    RD$ ${sub.toFixed(2)}
</div>`;
    });

    document.getElementById("cart-items").innerHTML=html;
    document.getElementById("total").innerText="Total: RD$ "+total.toFixed(2);
}

/* CAMBIAR CANTIDAD */
function cambiarCantidad(index,cambio){

    fetch("update_cart_ajax.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"index="+index+"&change="+cambio
    })
    .then(res=>res.json())
    .then(data=>actualizarCarrito(data));
}

/* GRUPOS */
function nuevoGrupo(){
    let select=document.getElementById("grupoActivo");
    let nuevo=select.length+1;

    let option=document.createElement("option");
    option.value=nuevo;
    option.text="👤 Cliente "+nuevo;

    select.appendChild(option);
    select.value=nuevo;

    grupoActual=nuevo;
}

document.getElementById("grupoActivo").addEventListener("change",function(){
    grupoActual=this.value;
});

/* INICIAL */
window.onload=()=>{
    fetch("get_cart.php")
    .then(res=>res.json())
    .then(data=>actualizarCarrito(data));
}

</script>

</body>
</html>