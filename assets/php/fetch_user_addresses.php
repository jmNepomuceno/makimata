<?php
include("../connection/connection.php");
session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION['user'])) {
        throw new Exception("User not logged in.");
    }

    $userID = $_SESSION['user']['user_ID'];

    // ✅ Fetch all addresses for this user
    $stmt = $pdo->prepare("
        SELECT 
            id,
            full_name,
            mobile_number,
            house_no,
            barangay,
            city,
            province,
            is_default
        FROM user_addresses
        WHERE user_id = ?
        ORDER BY is_default DESC, created_at DESC
    ");
    $stmt->execute([$userID]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$addresses) {
        echo json_encode([
            "status" => "empty",
            "message" => "No addresses found for this user."
        ]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "data" => $addresses
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
