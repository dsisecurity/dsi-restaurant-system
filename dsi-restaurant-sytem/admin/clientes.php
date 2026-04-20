    <?php
include __DIR__ . '/../config/database.php';


session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: admin/login.php");
    exit;
}

$name = $_POST['name'];
$phone = $_POST['phone'];
$address = $_POST['address'];

$conn->query("
INSERT INTO customers (name, phone, address)
VALUES ('$name','$phone','$address')
");

header("Location: clientes.php");