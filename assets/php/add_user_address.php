<?php
include("../connection/connection.php");
session_start();
header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user']['user_ID'])) {
        throw new Exception("User not logged in.");
    }

    $userID = $_SESSION['user']['user_ID'];
    $full_name = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['lastname'];
    $house_no = trim($_POST['house_no']);
    $barangay = trim($_POST['barangay']);
    $city = trim($_POST['city']);
    $province = trim($_POST['province']);
    $mobile_number = trim($_POST['mobile_number']);

    if (!$house_no || !$barangay || !$city || !$province || !$mobile_number) {
        throw new Exception("All fields are required.");
    }

    // ✅ Insert new record
    $stmt = $pdo->prepare("INSERT INTO user_addresses 
        (user_id, full_name, house_no, barangay, city, province, mobile_number, is_default)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->execute([$userID, $full_name, $house_no, $barangay, $city, $province, $mobile_number]);

    // ✅ Get the last inserted ID
    $newId = $pdo->lastInsertId();

    // ✅ Fetch the inserted record
    $fetchStmt = $pdo->prepare("SELECT * FROM user_addresses WHERE id = ?");
    $fetchStmt->execute([$newId]);
    $newAddress = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    if (!$newAddress) {
        throw new Exception("Failed to retrieve inserted address.");
    }

    echo json_encode([
        "status" => "success",
        "data" => $newAddress
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
