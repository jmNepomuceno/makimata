<?php
include("../connection/connection.php");
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    // ✅ Check if user is logged in
    if (!isset($_SESSION['user'])) {
        throw new Exception("User not logged in.");
    }

    if (!isset($_POST['reviewId'], $_POST['status'])) {
        throw new Exception("Missing parameters.");
    }

    $reviewId = (int) $_POST['reviewId'];
    $status   = trim($_POST['status']);

    // ✅ Validate status
    $validStatuses = ["pending", "approved", "rejected"];
    if (!in_array($status, $validStatuses)) {
        throw new Exception("Invalid status value.");
    }

    // ✅ Update review status
    $stmt = $pdo->prepare("
        UPDATE order_reviews
        SET status = :status,
            updated_at = NOW()
        WHERE id = :id
    ");
    $stmt->execute([
        ":status" => $status,
        ":id"     => $reviewId
    ]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Review not found or status unchanged.");
    }

    echo json_encode([
        "status"  => "success",
        "message" => "Review status updated successfully.",
        "reviewId" => $reviewId,
        "newStatus" => $status
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
