<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');
session_start();

try {
    $sql = "SELECT 
                id, 
                title, 
                description, 
                type, 
                video_url, 
                article_url, 
                views, 
                last_updated, 
                icon
            FROM tutorials
            ORDER BY last_updated DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tutorials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $tutorials
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error", 
        "message" => $e->getMessage()
    ]);
}

?>