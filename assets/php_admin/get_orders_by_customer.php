<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    if (!isset($_GET['customerId'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing customerId parameter"
        ]);
        exit;
    }

    $customerId = intval($_GET['customerId']);

    $sql = "SELECT 
                o.id,
                o.order_code,
                o.user_name,
                o.mobile,
                o.barangay,
                o.city,
                o.province,
                o.house_no,
                o.payment_method,
                o.subtotal,
                o.shipping,
                o.total,
                o.status,
                o.created_at AS date,
                u.firstname,
                u.lastname
            FROM orders o
            INNER JOIN users u 
                ON o.mobile = u.mobile_number
            WHERE u.user_ID = :customerId
            ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['customerId' => $customerId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $orders
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
