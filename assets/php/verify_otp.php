<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    $mobile = $_POST['mobile'] ?? '';
    $otp    = $_POST['otp'] ?? '';

    if (!$mobile || !$otp) {
        echo json_encode(["status" => "error", "message" => "Mobile number and OTP are required."]);
        exit;
    }

    // Normalize mobile number (e.g. 09123456789 → +639123456789)
    // if (substr($mobile, 0, 1) == "0") {
    //     $mobile = "+63" . substr($mobile, 1);
    // }

    // Find user with matching OTP and mobile
    $stmt = $pdo->prepare("SELECT user_ID, otp_expires_at 
                           FROM users 
                           WHERE mobile_number = :mobile 
                           AND otp_code = :otp 
                           AND is_verified = 0");
    $stmt->execute([':mobile' => $mobile, ':otp' => $otp]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "Invalid OTP or mobile number.",
        "data" => json_encode($_POST , TRUE)]);
        exit;
    }

    // Check expiry
    if (strtotime($user['otp_expires_at']) < time()) {
        echo json_encode(["status" => "expired", "message" => "OTP has expired. Please request a new one."]);
        exit;
    }

    // Mark user as verified and clear OTP
    $stmtUpdate = $pdo->prepare("UPDATE users 
                                 SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL 
                                 WHERE user_ID = :id");
    $stmtUpdate->execute([':id' => $user['user_ID']]);

    echo json_encode([
        "status" => "success", 
        "message" => "Your account has been successfully verified!",
        "status" => "success", 
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
