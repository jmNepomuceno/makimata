<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

try {
    $mobile = $_POST['mobile'] ?? '';
    if (!$mobile) {
        echo json_encode(["status"=>"error","message"=>"Mobile/email required."]);
        exit;
    }

    // Normalize mobile if using phone, otherwise fetch email
    $stmt = $pdo->prepare("SELECT user_ID, firstname, lastname, email FROM users WHERE mobile_number = :mobile");
    $stmt->execute([':mobile'=>$mobile]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status"=>"error","message"=>"User not found."]);
        exit;
    }

    // Generate new OTP
    $otp = rand(100000,999999);
    $otpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $stmtUpdate = $pdo->prepare("UPDATE users SET otp_code = :otp, otp_expires_at = :exp WHERE user_ID = :id");
    $stmtUpdate->execute([
        ':otp'=>$otp,
        ':exp'=>$otpExpiry,
        ':id'=>$user['user_ID']
    ]);

    // --- Send OTP via Email ---
    // Uncomment when ready
    /*
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'yourgmail@gmail.com';
    $mail->Password   = 'your_app_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('yourgmail@gmail.com', 'Your App Name');
    $mail->addAddress($user['email'], $user['firstname'].' '.$user['lastname']);

    $mail->isHTML(true);
    $mail->Subject = "Your New OTP Code";
    $mail->Body    = "Hello <b>{$user['firstname']}</b>,<br><br>Your new OTP is: <b>$otp</b><br>This code will expire in 5 minutes.";
    $mail->AltBody = "Your new OTP is: $otp";

    $mail->send();
    */

    echo json_encode(["status"=>"success","message"=>"A new OTP has been sent."]);

} catch(Exception $e) {
    echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
}
?>
