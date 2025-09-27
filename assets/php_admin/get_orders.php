<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT * FROM orders ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orders = [];

    foreach ($ordersData as $row) {
        $order_code = $row['order_code'];

        // --- Fetch items ---
        $sqlItems = "SELECT product_code AS sku, name, attributes, quantity AS qty, price 
                     FROM order_items 
                     WHERE order_code = :order_code";
        $stmtItems = $pdo->prepare($sqlItems);
        $stmtItems->execute([':order_code' => $order_code]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // --- Fetch history (if you track it) ---
        $sqlHistory = "SELECT status, created_at as date 
                       FROM order_history 
                       WHERE order_code = :order_code 
                       ORDER BY created_at ASC";
        $stmtHist = $pdo->prepare($sqlHistory);
        $stmtHist->execute([':order_code' => $order_code]);
        $history = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

        // --- Format shipping address ---
        $shippingAddress = trim($row['house_no'] . " " . $row['barangay'] . ", " . $row['city'] . ", " . $row['province']);

        // --- Final order object ---
        $orders[] = [
            "id" => $row['order_code'],
            "customer" => [
                "name"  => $row['user_name'],
                "email" => $row['email'] ?? "", // optional if not stored
                "phone" => $row['mobile']
            ],
            "date" => $row['created_at'],
            "items" => $items,
            "total" => floatval($row['total']),
            "paymentMethod" => $row['payment_method'],
            "status" => $row['status'] ?? "pending",
            "tracking" => !empty($row['tracking_number']) ? [
                "number"  => $row['tracking_number'],
                "carrier" => $row['tracking_carrier']
            ] : null,
            "shippingAddress" => $shippingAddress,
            "history" => $history
        ];
    }

    echo json_encode([
        "status" => "success",
        "orders" => $orders
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
