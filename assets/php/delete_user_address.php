<?php
include("../connection/connection.php");
session_start();
header('Content-Type: application/json');

try {
    // ✅ Check if user is logged in
    if (!isset($_SESSION['user']['user_ID'])) {
        throw new Exception("User not logged in.");
    }

    // ✅ Get user and input
    $userID = $_SESSION['user']['user_ID'];
    $addressID = isset($_POST['address_id']) ? trim($_POST['address_id']) : null;

    if (!$addressID) {
        throw new Exception("Address ID is required.");
    }

    // ✅ Check if address belongs to the user
    $checkStmt = $pdo->prepare("SELECT * FROM user_addresses WHERE id = ? AND user_id = ?");
    $checkStmt->execute([$addressID, $userID]);
    $address = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$address) {
        throw new Exception("Address not found or unauthorized.");
    }

    // ✅ Proceed to delete
    $deleteStmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
    $deleteStmt->execute([$addressID, $userID]);

    if ($deleteStmt->rowCount() === 0) {
        throw new Exception("Failed to delete address.");
    }

    echo json_encode([
        "status" => "success",
        "message" => "Address deleted successfully.",
        "deleted_id" => $addressID
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
