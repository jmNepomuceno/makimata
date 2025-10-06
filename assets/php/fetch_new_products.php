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
                images
            FROM products WHERE stock_status='new'
            ORDER BY product_ID ASC
            LIMIT 8"; // get first 8 products for featured section
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$product) {
        if (!empty($product['images'])) {
            $product['images'] = json_decode($product['images'], true);
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

?>