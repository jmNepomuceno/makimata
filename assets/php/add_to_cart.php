<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');
session_start();

try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) $data = $_POST;

    $user_id = $_SESSION['user']['user_ID'];
    $product_code = $data['product_code'] ?? null;
    $name = $data['name'] ?? null;
    $attributes = $data['attributes'] ?? '';
    $customization_json = $data['customization_json'] ?? null;
    $quantity = intval($data['quantity'] ?? 1);
    $base_price = floatval($data['base_price'] ?? 0);
    $extra_cost = floatval($data['extra_cost'] ?? 0);
    $final_price = floatval($data['final_price'] ?? 0);

    if (!$user_id || !$product_code || !$name) {
        throw new Exception("Missing required fields.");
    }

    // ✅ Check if item already exists in the user's cart (same product + customization)
    $check = $pdo->prepare("
        SELECT id, quantity FROM cart_items 
        WHERE user_id = :user_id 
        AND product_code = :product_code 
        AND (customization_json = :customization_json OR (:customization_json IS NULL AND customization_json IS NULL))
    ");
    $check->execute([
        ':user_id' => $user_id,
        ':product_code' => $product_code,
        ':customization_json' => $customization_json
    ]);

    if ($check->rowCount() > 0) {
        // Update quantity if already exists
        $existing = $check->fetch(PDO::FETCH_ASSOC);
        $newQty = $existing['quantity'] + $quantity;

        $update = $pdo->prepare("
            UPDATE cart_items 
            SET quantity = :quantity, updated_at = NOW() 
            WHERE id = :id
        ");
        $update->execute([
            ':quantity' => $newQty,
            ':id' => $existing['id']
        ]);

        echo json_encode(["status" => "success", "message" => "Cart item updated", "new_quantity" => $newQty]);
        exit;
    }

    // ✅ Insert new cart item
    $sql = "
        INSERT INTO cart_items (
            user_id, product_code, name, attributes, customization_json,
            quantity, base_price, extra_cost, final_price
        ) VALUES (
            :user_id, :product_code, :name, :attributes, :customization_json,
            :quantity, :base_price, :extra_cost, :final_price
        )
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':product_code' => $product_code,
        ':name' => $name,
        ':attributes' => $attributes,
        ':customization_json' => $customization_json,
        ':quantity' => $quantity,
        ':base_price' => $base_price,
        ':extra_cost' => $extra_cost,
        ':final_price' => $final_price
    ]);

    echo json_encode(["status" => "success", "message" => "Item added to cart"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
