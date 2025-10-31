<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_POST['orderId']) || !isset($_POST['status'])) {
        throw new Exception("Missing parameters");
    }

    $orderId = $_POST['orderId'];
    $status  = $_POST['status'];

    // Update order status
    $sql = "UPDATE orders SET status = :status WHERE order_code = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':status' => $status, ':id' => $orderId]);

    // Insert into order_history
    $sqlHistory = "INSERT INTO order_history (order_code, status, remarks, created_at)
                SELECT order_code, :status, :remarks, NOW() FROM orders WHERE order_code = :order_code";
    $stmtHist = $pdo->prepare($sqlHistory);
    $stmtHist->execute([
        ':status'  => $status,
        ':remarks' => 'Order status updated to ' . ucfirst($status),
        ':order_code' => $orderId
    ]);

    echo json_encode(["status" => "success", "message" => "Status updated to $status"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
