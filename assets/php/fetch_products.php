<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    // 🟢 Fetch products
    $sqlProducts = "SELECT 
                        product_ID AS id, 
                        product_code,
                        name, 
                        description, 
                        price, 
                        stock, 
                        category, 
                        image, 
                        images,
                        stock_status,
                        styling
                    FROM products";
    $stmt = $pdo->prepare($sqlProducts);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 🟢 Decode JSON columns
    foreach ($products as &$product) {
        $product['images'] = !empty($product['images']) ? (json_decode($product['images'], true) ?: []) : [];
        $product['styling'] = !empty($product['styling']) ? (json_decode($product['styling'], true) ?: []) : [];
    }

    // 🟣 Fetch tutorials
    $sqlTutorials = "SELECT 
                        id, 
                        title, 
                        description, 
                        type, 
                        video_url, 
                        article_url, 
                        views, 
                        last_updated, 
                        icon, 
                        created_at, 
                        status, 
                        order_code
                     FROM tutorials";
    $stmtTut = $pdo->prepare($sqlTutorials);
    $stmtTut->execute();
    $tutorials = $stmtTut->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "products" => $products,
        "tutorials" => $tutorials
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
