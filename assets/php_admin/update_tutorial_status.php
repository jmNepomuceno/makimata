<?php
include("../connection/connection.php");
session_start();
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    // ✅ Check if admin is logged in
    if (!isset($_SESSION['admin'])) {
        throw new Exception("User not logged in.");
    }

    // ✅ Validate required parameters
    if (!isset($_POST['tutorialId'], $_POST['status'])) {
        throw new Exception("Missing parameters.");
    }

    $tutorialId = (int) $_POST['tutorialId'];
    $status = trim($_POST['status']);

    // ✅ Validate status value
    $validStatuses = ["pending", "approved", "rejected"];
    if (!in_array($status, $validStatuses)) {
        throw new Exception("Invalid status value.");
    }

    // ✅ Update tutorial status
    $stmt = $pdo->prepare("
        UPDATE tutorials
        SET status = :status,
            last_updated = NOW()
        WHERE id = :id
    ");
    $stmt->execute([
        ":status" => $status,
        ":id" => $tutorialId
    ]);

    // ✅ Check if update occurred
    if ($stmt->rowCount() === 0) {
        throw new Exception("Tutorial not found or status unchanged.");
    }

    // ✅ Success response
    echo json_encode([
        "status" => "success",
        "message" => "Tutorial status updated successfully.",
        "tutorialId" => $tutorialId,
        "newStatus" => $status
    ]);

} catch (Exception $e) {
    // ❌ Error response
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
