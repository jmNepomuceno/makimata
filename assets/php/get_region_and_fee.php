<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    $province = isset($_GET['province']) ? trim($_GET['province']) : '';

    if (empty($province)) {
        echo json_encode([
            "status" => "error",
            "message" => "Province is required."
        ]);
        exit;
    }

    $sql = "SELECT region_code, shipping_price_range 
            FROM provinces 
            WHERE province_description = ?
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$province]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode([
            "status" => "success",
            "data" => [
                "region_code" => $data['region_code'],
                "shipping_price_range" => $data['shipping_price_range']
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Province not found."
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
