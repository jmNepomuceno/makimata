<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        throw new Exception("Required parameters not received");
    }

    $id = $_POST['id'];
    $newStatus = trim($_POST['status']);

    $validStatuses = ['sent', 'delivered', 'opened', 'failed'];
    if (!in_array($newStatus, $validStatuses)) {
        throw new Exception("Invalid status");
    }

    $stmt = $pdo->prepare("UPDATE notifications SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $newStatus,
        ':id'     => $id
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Notification status updated successfully"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>
