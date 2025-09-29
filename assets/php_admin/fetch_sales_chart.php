<?php
    include("../connection/connection.php");
    date_default_timezone_set('Asia/Manila');
    header('Content-Type: application/json; charset=utf-8');

    try {
        // --- Fetch weekly sales for current month ---
        $sql = "SELECT WEEK(created_at, 1) AS week_number, 
                    SUM(total) AS weekly_sales
                FROM orders
                WHERE YEAR(created_at) = YEAR(CURDATE())
                AND MONTH(created_at) = MONTH(CURDATE())
                GROUP BY WEEK(created_at, 1)
                ORDER BY week_number ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $salesData = [];

        foreach ($rows as $row) {
            $labels[] = "Week " . $row['week_number'];
            $salesData[] = (float)$row['weekly_sales'];
        }

        echo json_encode([
            "status" => "success",
            "chart" => [
                "labels" => $labels,
                "data" => $salesData
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
?>
