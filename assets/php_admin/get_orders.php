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

        // --- Fetch items with product details and styling ---
        $sqlItems = "
            SELECT 
                oi.product_code AS sku, 
                oi.name,
                oi.attributes,
                oi.quantity AS qty, 
                oi.price,
                p.image,
                p.styling
            FROM order_items oi
            LEFT JOIN products p ON oi.product_code = p.product_code
            WHERE oi.order_code = :order_code
            ORDER BY oi.id DESC
        ";
        $stmtItems = $pdo->prepare($sqlItems);
        $stmtItems->execute([':order_code' => $order_code]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // --- Fetch history ---
        $sqlHistory = "
            SELECT status, created_at as date 
            FROM order_history 
            WHERE order_code = :order_code 
            ORDER BY created_at ASC
        ";
        $stmtHist = $pdo->prepare($sqlHistory);
        $stmtHist->execute([':order_code' => $order_code]);
        $history = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

        // --- Fetch shipping fee from provinces table ---
        $sqlProv = "
            SELECT shipping_price_range 
            FROM provinces 
            WHERE province_description = :province
            LIMIT 1
        ";
        $stmtProv = $pdo->prepare($sqlProv);
        $stmtProv->execute([':province' => $row['province']]);
        $provData = $stmtProv->fetch(PDO::FETCH_ASSOC);
        $shippingFee = $provData ? $provData['shipping_price_range'] : '₱0';

        // --- Format shipping address ---
        $shippingAddress = trim(
            $row['house_no'] . ' ' . $row['barangay'] . ', ' . 
            $row['city'] . ', ' . $row['province']
        );

        // --- Final order object ---
        $orders[] = [
            "id" => $row['order_code'],
            "customer" => [
                "name"  => $row['user_name'],
                "email" => $row['email'] ?? "",
                "phone" => $row['mobile']
            ],
            "date" => $row['created_at'],
            "items" => array_map(function($item) {
                if (!empty($item['styling'])) {
                    $item['styling'] = json_decode($item['styling'], true);
                }
                return $item;
            }, $items),
            "total" => floatval($row['total']),
            "paymentMethod" => $row['payment_method'],
            "status" => $row['status'] ?? "pending",
            "tracking" => !empty($row['tracking_number']) ? [
                "number"  => $row['tracking_number'],
                "carrier" => $row['tracking_carrier']
            ] : null,
            "shippingAddress" => $shippingAddress,
            "shippingFee" => $shippingFee, // ✅ added shipping fee here
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
