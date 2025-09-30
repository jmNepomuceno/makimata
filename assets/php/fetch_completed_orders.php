<?php
include("../connection/connection.php");
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION['user'])) {
        throw new Exception("User not logged in.");
    }

    $fullName = trim($_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['lastname']);
    $params = [":user_name" => $fullName];
    $conditions = "user_name = :user_name AND status = 'completed'";

    if (!empty($_POST['startDate'])) {
        $conditions .= " AND DATE(created_at) >= :startDate";
        $params[":startDate"] = $_POST['startDate'];
    }
    if (!empty($_POST['endDate'])) {
        $conditions .= " AND DATE(created_at) <= :endDate";
        $params[":endDate"] = $_POST['endDate'];
    }

    $sql = "SELECT * FROM orders WHERE $conditions ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orders = [];
    foreach ($ordersData as $row) {
        // Fetch items
        $sqlItems = "SELECT name, quantity AS qty, price 
                     FROM order_items WHERE order_code = :order_code";
        $stmtItems = $pdo->prepare($sqlItems);
        $stmtItems->execute([":order_code" => $row['order_code']]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // Fetch review (if exists)
        $sqlReview = "SELECT rating, comment, created_at 
                      FROM order_reviews 
                      WHERE order_code = :order_code AND user_id = :user_id 
                      LIMIT 1";
        $stmtReview = $pdo->prepare($sqlReview);
        $stmtReview->execute([
            ":order_code" => $row['order_code'],
            ":user_id"    => $_SESSION['user']['user_ID']
        ]);
        $review = $stmtReview->fetch(PDO::FETCH_ASSOC);

        $orders[] = [
            "id"     => $row['order_code'],
            "date"   => $row['created_at'],
            "items"  => $items,
            "total"  => floatval($row['total']),
            "review" => $review ? $review : null
        ];
    }

    echo json_encode(["status" => "success", "orders" => $orders]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
