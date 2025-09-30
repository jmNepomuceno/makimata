<?php
include("../connection/connection.php");
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    // Make sure user is logged in
    if (!isset($_SESSION['user'])) {
        echo json_encode([
            "status" => "error",
            "message" => "User not logged in."
        ]);
        exit;
    }

    $fullName = trim($_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['lastname']);

    // Base query
    $sql = "SELECT * 
            FROM orders 
            WHERE user_name = :user_name";
    $params = [':user_name' => $fullName];

    // ✅ Apply filters if provided
    if (!empty($_POST['startDate'])) {
        $sql .= " AND DATE(created_at) >= :startDate";
        $params[':startDate'] = $_POST['startDate'];
    }

    if (!empty($_POST['endDate'])) {
        $sql .= " AND DATE(created_at) <= :endDate";
        $params[':endDate'] = $_POST['endDate'];
    }

    if (!empty($_POST['status'])) {
        $sql .= " AND status = :status";
        $params[':status'] = $_POST['status'];
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orders = [];

    foreach ($ordersData as $row) {
        $order_code = $row['order_code'];

        // Fetch items
        $sqlItems = "SELECT product_code AS sku, name, attributes, quantity AS qty, price 
                     FROM order_items 
                     WHERE order_code = :order_code 
                     ORDER BY id DESC";
        $stmtItems = $pdo->prepare($sqlItems);
        $stmtItems->execute([':order_code' => $order_code]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // Fetch history
        $sqlHistory = "SELECT status, remarks, created_at 
                       FROM order_history 
                       WHERE order_code = :order_code 
                       ORDER BY created_at ASC";
        $stmtHist = $pdo->prepare($sqlHistory);
        $stmtHist->execute([':order_code' => $order_code]);
        $history = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

        // Shipping address
        $shippingAddress = trim($row['house_no'] . " " . $row['barangay'] . ", " . $row['city'] . ", " . $row['province']);

        $orders[] = [
            "id" => $row['order_code'],
            "customer" => [
                "name"  => $row['user_name'],
                "phone" => $row['mobile']
            ],
            "date" => $row['created_at'],
            "items" => $items,
            "total" => floatval($row['total']),
            "paymentMethod" => $row['payment_method'],
            "status" => $row['status'] ?? "pending",
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
