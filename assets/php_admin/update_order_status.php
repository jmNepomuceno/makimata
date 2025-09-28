<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_POST['ids']) || !isset($_POST['status'])) {
        throw new Exception("Required parameters not received");
    }

    $ids = $_POST['ids'];
    $newStatus = trim($_POST['status']);
    $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    if (!is_array($ids) || empty($ids)) {
        throw new Exception("No orders selected");
    }
    if (!in_array($newStatus, $validStatuses)) {
        throw new Exception("Invalid status");
    }

    // Prepare statements
    $updateOrderStmt = $pdo->prepare("UPDATE orders SET status = :status WHERE order_code = :order_code");
    $insertHistoryStmt = $pdo->prepare("INSERT INTO order_history (order_code, status, remarks, created_at) VALUES (:order_code, :status, :remarks, NOW())");

    // Prepare notification statement
    $insertNotifStmt = $pdo->prepare("
        INSERT INTO notifications 
        (type, icon, title, message, recipient, target_id, link, created_at) 
        VALUES 
        ('order', 'fa-shopping-cart', 'Order Status Updated', :message, 'Sales Team', :target_id, 'orders.html', NOW())
    ");

    foreach ($ids as $orderCode) {
        // Update order status
        $updateOrderStmt->execute([
            ':status'     => $newStatus,
            ':order_code' => $orderCode
        ]);

        // Insert order history
        $insertHistoryStmt->execute([
            ':order_code' => $orderCode,
            ':status'     => $newStatus,
            ':remarks'    => "Status updated to {$newStatus}"
        ]);

        // Insert notification
        $insertNotifStmt->execute([
            ':message'   => "Order #$orderCode status updated to $newStatus",
            ':target_id' => $orderCode
        ]);
    }

    echo json_encode([
        "status" => "success",
        "message" => count($ids) . " orders updated successfully"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>
