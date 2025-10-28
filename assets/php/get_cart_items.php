<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    if (!isset($_SESSION['user'])) {
        throw new Exception("User not logged in");
    }

    $user_id = $_SESSION['user']['user_ID'];

    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Transform each item so that `id` equals `product_code` for front-end consistency
    $cart = array_map(function($item) {
        return [
            'id' => $item['id'],
            'product_code' => $item['product_code'],
            'name' => $item['name'],
            'quantity' => intval($item['quantity']),
            'price' => floatval($item['final_price']),
            'attributes' => $item['attributes'] ?? '',
            'customization' => $item['customization_json'] ? json_decode($item['customization_json'], true) : null,
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
