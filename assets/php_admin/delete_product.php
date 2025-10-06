<?php
include("../connection/connection.php");

header('Content-Type: application/json');

try {
    if (!isset($_POST['product_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID missing.']);
        exit;
    }

    $product_id = intval($_POST['product_id']);

    // ✅ Delete product record from database
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_ID = :id");
    $stmt->execute(['id' => $product_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found or already deleted.']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}

// -- 31	PROD0031	Market Tote Basket	Open basket with side handles, convenient for shopping or carrying goods.	350	15	basket	mik/products/basket/b10.png	{"dark": "mik/products/basket/bd10.png", "default": "mik/products/basket/basket10.png", "natural": "mik/products/basket/bn10.png", "premium": "mik/products/basket/bp10.png"}	-- 
?>