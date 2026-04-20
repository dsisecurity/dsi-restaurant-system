<?php
session_start();
include 'config/database.php';

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

$id = (int)$_POST['id'];
$group_id = (int)($_POST['group_id'] ?? 1);

$p = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if($p){

    $found = false;

    foreach($_SESSION['cart'] as &$item){

        // 🔥 IMPORTANTE: mismo producto Y mismo grupo
        if($item['id'] == $id && $item['group_id'] == $group_id){
            $item['quantity']++;
            $found = true;
            break;
        }
    }

    if(!$found){
        $_SESSION['cart'][] = [
            'id'=>$p['id'],
            'name'=>$p['name'],
            'price'=>$p['price'],
            'quantity'=>1,
            'group_id'=>$group_id
        ];
    }
}

echo json_encode($_SESSION['cart']);