<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    if (!isset($_SESSION['user'])) {
        throw new Exception("User not logged in");
    }

    $user_id = $_SESSION['user']['user_ID'];

    // 🟩 Fetch cart items for this user
    $stmt = $pdo->prepare("
        SELECT c.*, p.product_ID AS product_id, p.price AS base_price, p.images
        FROM cart_items c
        LEFT JOIN products p ON c.product_code = p.product_code
        WHERE c.user_id = :user_id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 🟩 Transform each item for frontend
    $cart = array_map(function($item) {
        return [
            // id now uses the actual product_ID from `products`
            'id' => $item['product_id'] ?? null,
            'cart_item_id' => $item['id'], // still include cart id for deletion reference
            'product_code' => $item['product_code'],
            'name' => $item['name'],
            'quantity' => intval($item['quantity']),
            'price' => floatval($item['final_price']),
            'base_price' => floatval($item['base_price'] ?? 0),
            'attributes' => $item['attributes'] ?? '',
            'customization' => $item['customization_json'] 
                ? json_decode($item['customization_json'], true) 
                : null,
            'image' => $item['images'] 
                ? json_decode($item['images'], true)['default'] 
                : null,
            'selected' => true
        ];
    }, $cart);

    echo json_encode([
        "status" => "success",
        "cart" => $cart
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
