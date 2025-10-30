<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');
session_start();

try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) $data = $_POST;

    $user_id = $_SESSION['user']['user_ID'] ?? null;
    $product_ID = $data['product_ID'] ?? null;

    if (!$user_id || !$product_ID) {
        throw new Exception("Missing required fields (user_id or product_ID).");
    }

    // 🟩 Step 1: Find product_code from products table using product_ID
    $productStmt = $pdo->prepare("
        SELECT product_code 
        FROM products 
        WHERE product_ID = :product_ID
    ");
    $productStmt->execute([':product_ID' => $product_ID]);
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("Product not found for the provided product_ID.");
    }

    $product_code = $product['product_code'];

    // 🟩 Step 2: Delete matching cart item for this user + product_code
    $delete = $pdo->prepare("
        DELETE FROM cart_items 
        WHERE user_id = :user_id AND product_code = :product_code
    ");
    $delete->execute([
        ':user_id' => $user_id,
        ':product_code' => $product_code
    ]);

    if ($delete->rowCount() === 0) {
        throw new Exception("No matching cart item found to delete.");
    }

    echo json_encode([
        "status" => "success",
        "message" => "Item removed from cart successfully.",
        "product_code" => $product_code
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
