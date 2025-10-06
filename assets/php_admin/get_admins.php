<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    $sql = "SELECT 
                admin_ID AS id,
                firstname,
                lastname,
                email,
                username,
                created_at,
                last_login
            FROM admin";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $admins
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
