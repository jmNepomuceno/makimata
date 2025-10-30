<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');
session_start();

try {
    // Decode JSON or fallback to POST
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) $data = $_POST;

    // Validate session and input
    $user_id = $_SESSION['user']['user_ID'] ?? null;
    $product_IDs = $data['product_IDs'] ?? [];

    if (!$user_id) {
        throw new Exception("User not logged in.");
    }

    if (empty($product_IDs) || !is_array($product_IDs)) {
        throw new Exception("No product IDs provided.");
    }

    // Build dynamic placeholders for IN clause
    $placeholders = implode(',', array_fill(0, count($product_IDs), '?'));

    // 🧩 Step 1: Fetch corresponding product_codes from the `products` table
    $fetchCodes = $pdo->prepare("SELECT product_code FROM products WHERE product_ID IN ($placeholders)");
    $fetchCodes->execute($product_IDs);
    $productCodes = $fetchCodes->fetchAll(PDO::FETCH_COLUMN);

    if (empty($productCodes)) {
        throw new Exception("No matching product codes found for the provided IDs.");
    }

    // 🧩 Step 2: Build placeholders for product codes
    $codePlaceholders = implode(',', array_fill(0, count($productCodes), '?'));

    // 🧩 Step 3: Delete matching items in cart_items table by user and product_code
    $delete = $pdo->prepare("
        DELETE FROM cart_items 
        WHERE user_id = ? 
        AND product_code IN ($codePlaceholders)
    ");
    $params = array_merge([$user_id], $productCodes);
    $delete->execute($params);

    echo json_encode([
        "status" => "success",
        "message" => count($productCodes) . " item(s) removed from cart.",
        "removed_codes" => $productCodes
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
