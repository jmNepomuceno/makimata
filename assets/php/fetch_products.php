<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    $sql = "SELECT 
                product_ID AS id, 
                product_code,
                name, 
                description, 
                price, 
                stock, 
                category, 
                image, 
                images,
                stock_status
            FROM products";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode JSON column "images" into array
    foreach ($products as &$product) {
        if (!empty($product['images'])) {
            $decoded = json_decode($product['images'], true);
            $product['images'] = $decoded ?: [];
        } else {
            $product['images'] = [];
        }
    }

    echo json_encode([
        "status" => "success",
        "data" => $products
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error", 
        "message" => $e->getMessage()
    ]);
}
