<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // adjust path to PHPMailer autoload.php

try {
    $firstname   = $_POST['firstname'];
    $lastname    = $_POST['lastname'];
    $email       = $_POST['email'];
    $mobile      = $_POST['contact'];
    $region      = $_POST['region'];
    $province    = $_POST['province'];
    $city        = $_POST['city'];
    $barangay    = $_POST['barangay'];
    $house_no    = $_POST['address'];
    $password    = $_POST['password'];

    // Check if email already exists
    $check = $pdo->prepare("SELECT user_ID FROM users WHERE email = :email");
    $check->execute([':email' => $email]);
    if ($check->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        exit;
    }

    // Generate OTP
    $otp = rand(100000, 999999);
    $otpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Insert user (unverified)
    $stmt = $pdo->prepare("INSERT INTO users 
        (firstname, lastname, email, mobile_number, region, province, city, barangay, house_no, password, created_at, is_verified, otp_code, otp_expires_at)
        VALUES (:firstname, :lastname, :email, :mobile, :region, :province, :city, :barangay, :house_no, :password, :created_at, 0, :otp, :otp_expires)");
    $stmt->execute([
        ':firstname'   => $firstname,
        ':lastname'    => $lastname,
        ':email'       => $email,
        ':mobile'      => $mobile,
        ':region'      => $region,
        ':province'    => $province,
        ':city'        => $city,
        ':barangay'    => $barangay,
        ':house_no'    => $house_no,
        ':password'    => $password, // TODO: hash later with password_hash()
        ':created_at'  => date('Y-m-d H:i:s'),
        ':otp'         => $otp,
        ':otp_expires' => $otpExpiry
    ]);
    $user_id = $pdo->lastInsertId();

    // --- Send OTP via Email ---
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'makimatamakimata@gmail.com';
        $mail->Password   = 'bcvn hegt gryv ubav'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('makimatamakimata@gmail.com', 'makimata');
        $mail->addAddress($email, $firstname . ' ' . $lastname);
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Code";
        $mail->Body    = "Hello <b>$firstname</b>,<br><br>Your OTP is: <b>$otp</b><br>This code will expire in 5 minutes.";
        $mail->AltBody = "Your OTP is: $otp";
        $mail->send();
    } catch (Exception $e) {
        // Continue even if email fails, but log the error
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }

    // --- Send OTP via Semaphore SMS ---
    // --- Send OTP via Semaphore SMS ---
    // try {
    //     $apiKey = '65ca60177cd2cc0e2f5184e3fa2d6d81';
    //     $message = "Hello $firstname, your OTP is $otp. It will expire in 5 minutes.";
    //     $number = preg_replace('/[^0-9]/', '', $mobile);

    //     // Ensure proper PH format
    //     if (strpos($number, '0') === 0) {
    //         $number = '63' . substr($number, 1);
    //     } elseif (strpos($number, '63') !== 0) {
    //         $number = '63' . $number;
    //     }

    //     $ch = curl_init('https://api.semaphore.co/api/v4/messages');
    //     curl_setopt_array($ch, [
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => http_build_query([
    //             'apikey' => $apiKey,
    //             'number' => $number,
    //             'message' => $message,
    //             'sendername' => 'mikamata'
    //         ]),
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_SSL_VERIFYPEER => false,
    //         CURLOPT_SSL_VERIFYHOST => false
    //     ]);

    //     $response = curl_exec($ch);
    //     $curlError = curl_error($ch);
    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     curl_close($ch);

    //     if ($curlError) {
    //         error_log("Semaphore CURL Error: " . $curlError);
    //         echo json_encode(['status' => 'warning', 'message' => 'SMS sent but with warning']);
    //     } elseif ($httpCode >= 400) {
    //         error_log("Semaphore HTTP Error: " . $httpCode . " | Response: " . $response);
    //         echo json_encode(['status' => 'error', 'message' => 'Failed to send SMS']);
    //     } else {
    //         error_log("Semaphore Response: " . $response);
    //         echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully']);
    //     }

    // } catch (Throwable $e) {
    //     error_log("Semaphore Exception: " . $e->getMessage());
    //     echo json_encode(['status' => 'error', 'message' => 'An internal error occurred']);
    // }


    // Insert admin notification
    $stmtNotif = $pdo->prepare("INSERT INTO notifications (type, icon, title, message, recipient, target_id, link)
        VALUES ('user','fa-user-plus','New User Registration',:message,'Admin',:user_id,'customers.html')");
    $stmtNotif->execute([
        ':message' => "$firstname $lastname started signup, pending OTP verification at " . date('Y-m-d H:i:s'),
        ':user_id' => $user_id
    ]);

    echo json_encode([
        "status"  => "pending",
        "message" => "OTP sent to your email and mobile number",
        "email"   => $email
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
