<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    // --- Top 5 Most Selling Products ---
    $sql = "SELECT p.name, SUM(oi.quantity) AS total_sold
            FROM order_items oi
            INNER JOIN products p ON oi.product_code = p.product_code
            GROUP BY oi.product_code
            ORDER BY total_sold DESC
            LIMIT 5";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];

    foreach ($rows as $row) {
        $labels[] = $row['name'];
        $data[] = (int)$row['total_sold'];
    }

    echo json_encode([
        "status" => "success",
        "chart" => [
            "labels" => $labels,
            "data" => $data
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
