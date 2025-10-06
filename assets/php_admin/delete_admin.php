<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    $admin_id = $_POST['admin_id'] ?? '';

    if (empty($admin_id)) {
        throw new Exception("Admin ID is required.");
    }

    $sql = "DELETE FROM admin WHERE admin_ID = :admin_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':admin_id' => $admin_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Admin deleted successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No admin found with the provided ID."
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
