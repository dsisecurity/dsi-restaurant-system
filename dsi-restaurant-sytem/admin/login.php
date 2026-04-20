<?php
session_start();
include '../config/database.php';

$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user && ($password == $user['password'] || password_verify($password, $user['password']))){
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'] ?? 'mesero';

        header("Location: ../menu.php");
        exit;
    }

    $error = "Credenciales incorrectas";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>DSI Restaurant System</title>

<style>

/* RESET */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Segoe UI', sans-serif;
}

/* FONDO ANIMADO */
body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(-45deg,#020617,#0f172a,#020617,#111827);
    background-size:400% 400%;
    animation: gradient 10s ease infinite;
    color:white;
}

@keyframes gradient{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

/* CONTENEDOR */
.container{
    display:flex;
    width:950px;
    height:520px;
    border-radius:20px;
    overflow:hidden;
    backdrop-filter: blur(20px);
    box-shadow:0 0 40px rgba(0,0,0,0.7);
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn{
    from{opacity:0; transform:scale(0.95);}
    to{opacity:1; transform:scale(1);}
}

/* IZQUIERDA */
.left{
    width:50%;
    background: linear-gradient(135deg,#22c55e,#16a34a);
    padding:40px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    color:black;
}

.logo{
    font-size:32px;
    font-weight:bold;
}

.slogan{
    margin-top:15px;
    font-size:16px;
    opacity:0.8;
}

/* DERECHA */
.right{
    width:50%;
    background:rgba(2,6,23,0.9);
    padding:40px;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

h2{
    margin-bottom:20px;
}

/* INPUTS */
.input-group{
    position:relative;
}

input{
    width:100%;
    padding:14px;
    margin-bottom:15px;
    border:none;
    border-radius:10px;
    background:#1f2937;
    color:white;
    outline:none;
    transition:0.2s;
}

input:focus{
    background:#374151;
}

/* ICONO PASSWORD */
.toggle-pass{
    position:absolute;
    right:15px;
    top:14px;
    cursor:pointer;
}

/* CHECKBOX */
.remember{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:14px;
    margin-bottom:10px;
}

/* BOTÓN */
button{
    padding:14px;
    border:none;
    border-radius:10px;
    background:#22c55e;
    color:black;
    font-weight:bold;
    cursor:pointer;
    transition:0.2s;
}

button:hover{
    background:#16a34a;
}

/* ERROR */
.error{
    background:#ef4444;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
    text-align:center;
}

/* FOOTER */
.footer{
    margin-top:15px;
    font-size:12px;
    opacity:0.5;
    text-align:center;
}

</style>
</head>

<body>

<div class="container">

    <!-- IZQUIERDA -->
    <div class="left">
        <div class="logo">🍽 DSI Restaurant System</div>
        <div class="slogan">
            Controla tus ventas mientras entregas sabor
        </div>
    </div>

    <!-- DERECHA -->
    <div class="right">

        <h2>Bienvenido</h2>

        <?php if($error){ ?>
            <div class="error"><?= $error ?></div>
        <?php } ?>

        <form method="POST">

            <div class="input-group">
                <input type="email" name="email" placeholder="Correo electrónico" required>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
                <span class="toggle-pass" onclick="togglePass()">👁</span>
            </div>

            <div class="remember">
                <input type="checkbox"> Recordar sesión
            </div>

            <button type="submit">Entrar al sistema</button>

        </form>

        <div class="footer">
            © <?= date("Y") ?> DSI Restaurant System
        </div>

    </div>

</div>

<script>

/* MOSTRAR/OCULTAR PASSWORD */
function togglePass(){
    let input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}

</script>

</body>
</html>