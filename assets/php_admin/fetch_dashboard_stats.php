<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    // --- Sales Today ---
    $sql = "SELECT COALESCE(SUM(total), 0) AS sales_today 
            FROM orders 
            WHERE DATE(created_at) = CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $salesToday = $stmt->fetch(PDO::FETCH_ASSOC)['sales_today'];

    // --- Orders This Week ---
    $sql = "SELECT COUNT(*) AS orders_week 
            FROM orders 
            WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ordersWeek = $stmt->fetch(PDO::FETCH_ASSOC)['orders_week'];

    // --- New Users This Month ---
    $sql = "SELECT COUNT(*) AS new_users_month 
            FROM users 
            WHERE YEAR(created_at) = YEAR(CURDATE()) 
              AND MONTH(created_at) = MONTH(CURDATE())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $newUsersMonth = $stmt->fetch(PDO::FETCH_ASSOC)['new_users_month'];

    // --- Total Products ---
    $sql = "SELECT COUNT(*) AS total_products FROM products";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

    // --- Final JSON Response ---
    echo json_encode([
        "status" => "success",
        "stats" => [
            "sales-today"     => "₱" . number_format((float)$salesToday, 2),
            "orders-week"     => (int)$ordersWeek,
            "new-users-month" => (int)$newUsersMonth,
            "total-products"  => (int)$totalProducts
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
