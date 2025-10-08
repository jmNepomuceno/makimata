<?php
include("../connection/connection.php");
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    // ✅ Check if user is logged in
    if (!isset($_SESSION['admin'])) {
        throw new Exception("User not logged in.");
    }

    // ✅ Fetch reviews joined with users + order_items
    $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.order_code,
            r.user_id,
            r.rating,
            r.comment,
            r.status,
            r.created_at,
            r.updated_at,
            CONCAT(u.firstname, ' ', u.lastname) AS customerName,

            -- From order_items
            oi.product_code,
            oi.name AS productName,
            oi.attributes,
            oi.quantity,
            oi.price

        FROM order_reviews r
        JOIN users u ON r.user_id = u.user_ID
        LEFT JOIN order_items oi ON r.order_code = oi.order_code
        ORDER BY r.created_at DESC
    ");

    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status"  => "success",
        "reviews" => $reviews
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>
