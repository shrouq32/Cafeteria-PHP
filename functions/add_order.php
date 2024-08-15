<?php
require 'connect.php'; 
require 'db_class.php'; 
session_start();

$db = new Database();
$db->connect($db_host, $db_user, $db_pass, $db_name);

$users = $db->select('users');
$products = $db->select('products');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id']; 
    $date = date('Y-m-d H:i:s');
    if($_POST['room']== 0){
        $old_room = $db->getRow('users', 'id', $user_id);
        $room = $old_room['room_id'];
    }else{
        $room = $_POST['room'];
    }

    if(empty($_POST['ext'])){
        $old_ext = $db->getRow('users', 'id', $user_id);
        $ext = $old_ext['ext'];
    }else{
        $ext = $_POST['ext'];
    }

    if(empty($_POST['notes'])){
        $comment = "Without any notes";
    }else{
        $comment = $_POST['notes'];
    }
    $total = $_POST['total'];
    $status = 1;

    try {
        $columns = "user_id, date, room, ext, comment, total, status";
        $values = "'$user_id', '$date', '$room','$ext', '$comment', '$total', $status";
        $db->insert('orders', $columns, $values);
    
        $order_id = $db->lastInsertId();

        foreach ($_POST['products'] as $product_id => $product_data) {
            $quantity = $product_data['quantity'];
            $price = $product_data['price'];
            $productColumns = "order_id, user_id, product_id, quantity, price";
            $productValues = "'$order_id', '$user_id', '$product_id', '$quantity', '$price'";
            $db->insert('orders_products', $productColumns, $productValues);
        }

        header("location :../my_orders.php;");
    } catch (Exception $e) {
        header("location: ../home.php?error=The Cart Is empty");
    }
} else {
    echo "Invalid request method.";
}
?>
