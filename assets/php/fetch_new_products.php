<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    $filter = $_GET['filter'] ?? 'new'; // default to "What's New"

    switch ($filter) {
        case 'bestsellers':
            // 🔥 Bestsellers based on order_items + orders (completed)
            $sql = "
                SELECT 
                    p.product_ID AS id,
                    p.product_code,
                    p.name,
                    p.description,
                    p.price,
                    p.stock,
                    p.category,
                    p.image,
                    p.images,
                    COALESCE(SUM(oi.quantity), 0) AS total_sold
                FROM products p
                LEFT JOIN order_items oi ON p.product_code = oi.product_code
                LEFT JOIN orders o ON oi.order_code = o.order_code AND o.status = 'completed'
                GROUP BY p.product_ID
                ORDER BY total_sold DESC, p.product_ID ASC
                LIMIT 8
            ";
            break;

        case 'favorites':
            // 💖 Favorites based on wishlist frequency
            $sql = "
                SELECT 
                    p.product_ID AS id,
                    p.product_code,
                    p.name,
                    p.description,
                    p.price,
                    p.stock,
                    p.category,
                    p.image,
                    p.images,
                    COUNT(w.product_id) AS wishlist_count
                FROM products p
                JOIN wishlist w ON p.product_ID = w.product_id
                GROUP BY p.product_ID
                ORDER BY wishlist_count DESC, p.product_ID ASC
                LIMIT 8
            ";
            break;

        case 'new':
        default:
            // 🆕 What's New (based on stock_status)
            $sql = "
                SELECT 
                    product_ID AS id, 
                    product_code,
                    name, 
                    description, 
                    price, 
                    stock, 
                    category, 
                    image, 
                    images
                FROM products 
                WHERE stock_status = 'new'
                ORDER BY product_ID ASC
                LIMIT 8
            ";
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$product) {
        // Decode JSON images if applicable
        if (!empty($product['images'])) {
            $decoded = json_decode($product['images'], true);
            $product['images'] = is_array($decoded) ? $decoded : [];
        } else {
            $product['images'] = [];
        }

        // Fallback image if none
        if (empty($product['image'])) {
            $product['image'] = '../assets/img/no-image.png';
        }
    }

    echo json_encode([
        "status" => "success",
        "filter" => $filter,
        "data" => $products
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
