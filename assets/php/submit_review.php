<?php
include("../connection/connection.php");
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION['user'])) {
        throw new Exception("User not logged in.");
    }

    if (!isset($_POST['orderId'], $_POST['stars'])) {
        throw new Exception("Missing required parameters.");
    }

    $orderId = trim($_POST['orderId']);         // order_code from orders
    $stars   = (int)$_POST['stars'];            // 1–5 rating
    $comment = trim($_POST['comment'] ?? "");   // optional comment
    $userId  = (int)$_SESSION['user']['user_ID'];

    if ($stars < 1 || $stars > 5) {
        throw new Exception("Invalid star rating.");
    }

    // Insert or update review
    $stmt = $pdo->prepare("
        INSERT INTO order_reviews (order_code, user_id, rating, comment, created_at, updated_at)
        VALUES (:order_code, :user_id, :rating, :comment, NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
            rating = VALUES(rating),
            comment = VALUES(comment),
            updated_at = NOW()
    ");
    $stmt->execute([
        ":order_code" => $orderId,
        ":user_id"    => $userId,
        ":rating"     => $stars,
        ":comment"    => $comment
    ]);

    echo json_encode([
        "status"  => "success",
        "message" => "Review submitted successfully."
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}

?>
