<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
// header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    // Check login session
    if (!isset($_SESSION['user']) || empty($_SESSION['user']['user_ID'])) {
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        exit;
    }

    $userId = $_SESSION['user']['user_ID'];
    $newPassword = trim($_POST['newPassword'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');

    if (empty($newPassword) || empty($confirmPassword)) {
        echo json_encode(["status" => "error", "message" => "Both password fields are required"]);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
        exit;
    }

    // Update password (⚠️ consider hashing with password_hash in real-world apps)
    $sql = "UPDATE users SET password = :password WHERE user_ID = :user_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':password' => $newPassword,
        ':user_ID'  => $userId
    ]);

    // Optional: insert into notifications
    $sqlNotif = "INSERT INTO notifications (type, icon, title, message, recipient, target_id) 
                 VALUES ('user', 'fa-lock', 'Password Change', :message, 'Admin', :user_id)";
    $stmtNotif = $pdo->prepare($sqlNotif);
    $stmtNotif->execute([
        ':message' => $_SESSION['user']['firstname'] . " " . $_SESSION['user']['lastname'] . " changed password at " . date('Y-m-d H:i:s'),
        ':user_id' => $userId
    ]);

    echo json_encode(["status" => "success", "message" => "Password updated successfully"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
