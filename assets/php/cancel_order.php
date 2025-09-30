<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_POST['order_code'])) {
        throw new Exception("Order code not received");
    }

    $orderCode = trim($_POST['order_code']);

    // Validate order exists & is still pending
    $checkStmt = $pdo->prepare("SELECT status FROM orders WHERE order_code = :order_code");
    $checkStmt->execute([':order_code' => $orderCode]);
    $order = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("Order not found");
    }
    if ($order['status'] !== 'pending') {
        throw new Exception("Only pending orders can be cancelled");
    }

    // Update order status
    $updateOrderStmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE order_code = :order_code");
    $updateOrderStmt->execute([':order_code' => $orderCode]);

    // Insert into order history
    $insertHistoryStmt = $pdo->prepare("
        INSERT INTO order_history (order_code, status, remarks, created_at) 
        VALUES (:order_code, 'cancelled', 'Order cancelled by customer', NOW())
    ");
    $insertHistoryStmt->execute([':order_code' => $orderCode]);

    // Insert notification for Sales/Admin (optional)
    $insertNotifStmt = $pdo->prepare("
        INSERT INTO notifications 
        (type, icon, title, message, recipient, target_id, link, created_at) 
        VALUES 
        ('order', 'fa-ban', 'Order Cancelled', :message, 'Sales Team', :target_id, 'orders.html', NOW())
    ");
    $insertNotifStmt->execute([
        ':message'   => "Order #$orderCode has been cancelled by the customer",
        ':target_id' => $orderCode
    ]);

    echo json_encode([
        "status"  => "success",
        "message" => "Order #$orderCode has been cancelled successfully"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>
