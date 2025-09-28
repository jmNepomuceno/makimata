<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    $required = ['name', 'price', 'category', 'stock'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    $productId   = $_POST['product_id'] ?? null;
    $name        = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');
    $price       = floatval($_POST['price']);
    $category    = trim($_POST['category']);
    $stock       = intval($_POST['stock']);

    // Upload base folder
    $uploadDir = __DIR__ . "/../../customer/mik/products/" . $category . "/";
    $publicPath = "/customer/mik/products/" . $category . "/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle main image
    $mainImage = $_POST['main_image'] ?? '';
    if (!empty($_FILES['main_image_file']['name'])) {
        $fileName = basename($_FILES['main_image_file']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['main_image_file']['tmp_name'], $targetPath)) {
            $mainImage = $publicPath . $fileName; // ✅ web-accessible path
        }
    }

    // Handle color images
    $colorImages = ['burly' => '', 'coffee' => '', 'rust_brown' => ''];
    if (!empty($_FILES['color_image_files'])) {
        foreach ($_FILES['color_image_files']['name'] as $colorKey => $fileName) {
            if (!empty($fileName)) {
                $safeName = basename($fileName);
                $targetPath = $uploadDir . $safeName;
                if (move_uploaded_file($_FILES['color_image_files']['tmp_name'][$colorKey], $targetPath)) {
                    $colorImages[$colorKey] = $publicPath . $safeName; // ✅ web path
                }
            }
        }
    }

    // Rebuild images JSON
    $imagesJson = json_encode([
        'dark'    => $colorImages['burly'] ?? '',
        'default' => $mainImage,
        'natural' => $colorImages['coffee'] ?? '',
        'premium' => $colorImages['rust_brown'] ?? ''
    ]);

    // Product code
    $lastIdStmt = $pdo->query("SELECT MAX(product_ID) as last_id FROM products");
    $lastIdRow  = $lastIdStmt->fetch(PDO::FETCH_ASSOC);
    $nextId     = ($lastIdRow['last_id'] ?? 0) + 1;
    $productCode = 'PROD' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

    if ($productId) {
        // $stmt = $pdo->prepare("
        //     UPDATE products 
        //     SET name=:name, description=:desc, price=:price, category=:cat, stock=:stock, images=:images 
        //     WHERE product_ID=:id
        // ");
        // $stmt->execute([
        //     ':name'   => $name,
        //     ':desc'   => $description,
        //     ':price'  => $price,
        //     ':cat'    => $category,
        //     ':stock'  => $stock,
        //     ':images' => $imagesJson,
        //     ':id'     => $productId
        // ]);

        $stmt = $pdo->prepare("
            UPDATE products 
            SET name=:name, description=:desc, price=:price, category=:cat, stock=:stock
            WHERE product_ID=:id
        ");
        $stmt->execute([
            ':name'   => $name,
            ':desc'   => $description,
            ':price'  => $price,
            ':cat'    => $category,
            ':stock'  => $stock,
            ':id'     => $productId
        ]);

        $message = "Product updated successfully";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO products (product_code, name, description, price, category, stock, image, images, created_at) 
            VALUES (:code, :name, :desc, :price, :cat, :stock, :image, :images, NOW())
        ");
        $stmt->execute([
            ':code'   => $productCode,
            ':name'   => $name,
            ':desc'   => $description,
            ':price'  => $price,
            ':cat'    => $category,
            ':stock'  => $stock,
            ':image'  => $mainImage,
            ':images' => $imagesJson
        ]);
        $message = "Product added successfully";
    }

    echo json_encode(['status' => 'success', 'message' => $message]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
