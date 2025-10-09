<?php
include("../connection/connection.php");
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    // ✅ Ensure user is logged in
    if (!isset($_SESSION['admin'])) {
        throw new Exception("User not logged in.");
    }

    // ✅ Validate parameters
    if (!isset($_POST['productId'], $_POST['status'])) {
        throw new Exception("Missing parameters.");
    }

    $productId = (int) $_POST['productId'];
    $status = trim($_POST['status']);

    // ✅ Validate status values
    $validStatuses = ['old', 'new'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception("Invalid stock status value.");
    }

    // ✅ Update the product stock_status
    $stmt = $pdo->prepare("
        UPDATE products
        SET stock_status = :status
        WHERE product_ID = :id
    ");
    $stmt->execute([
        ':status' => $status,
        ':id' => $productId
    ]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Product not found or stock status unchanged.");
    }

    echo json_encode([
        "status" => "success",
        "message" => "Stock status updated successfully.",
        "productId" => $productId,
        "newStatus" => $status
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
