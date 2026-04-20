<?php
session_start();
include '../config/database.php';

if(isset($_SESSION['turn_id'])){

    $turn_id = $_SESSION['turn_id'];

    $result = $conn->query("SELECT SUM(total) as total FROM sales WHERE turn_id=$turn_id");
    $total = $result->fetch_assoc()['total'] ?? 0;

    $conn->query("
    UPDATE turns 
    SET closing_amount=$total, status='cerrado', closed_at=NOW()
    WHERE id=$turn_id
    ");
}

session_destroy();

header("Location: login.php");
exit;