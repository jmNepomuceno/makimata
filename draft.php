<?php
    include("./assets/connection/connection.php");
    session_start();

    $sql = "SELECT 
                *
            FROM order_items";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // $stmtUpdate = $pdo->prepare("UPDATE products SET stock_status = 'old'");
    // $stmtUpdate->execute();

    // $orderId = "ORD000004";

    // // fetch order + customer + items
    // $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ?");
    // $stmt->execute([$orderId]);
    // $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_code = ?");
    // $stmt->execute([$orderId]);
    // $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // echo "<pre>"; print_r($order); echo "</pre>";
    echo "<pre>"; print_r($data); echo "</pre>";
    echo "<pre>"; print_r($_SESSION); echo "</pre>";
    
?>