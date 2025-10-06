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
        echo json_encode(["status"=>"error","message"=>"Email already registered"]);
        exit;
    }

    // Generate OTP
    $otp = rand(100000,999999);
    $otpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Insert user (with unverified status)
    $stmt = $pdo->prepare("INSERT INTO users 
        (firstname, lastname, email, mobile_number, region, province, city, barangay, house_no, password, created_at, is_verified, otp_code, otp_expires_at)
        VALUES (:firstname, :lastname, :email, :mobile, :region, :province, :city, :barangay, :house_no, :password, :created_at, 0, :otp, :otp_expires)");
    $stmt->execute([
        ':firstname'=>$firstname,
        ':lastname'=>$lastname,
        ':email'=>$email,
        ':mobile'=>$mobile,
        ':region'=>$region,
        ':province'=>$province,
        ':city'=>$city,
        ':barangay'=>$barangay,
        ':house_no'=>$house_no,
        ':password'=>$password,  // hash later with password_hash()
        ':created_at'=>date('Y-m-d H:i:s'),
        ':otp'=>$otp,
        ':otp_expires'=>$otpExpiry
    ]);
    $user_id = $pdo->lastInsertId();

    // --- Send OTP via Email (PHPMailer) ---
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'makimatamakimata@gmail.com';   // your email
        $mail->Password   = 'bcvn hegt gryv ubav';     // Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('makimatamakimata@gmail.com', 'makimata');
        $mail->addAddress($email, $firstname.' '.$lastname);

        $mail->isHTML(true);
        $mail->Subject = "Your OTP Code";
        $mail->Body    = "Hello <b>$firstname</b>,<br><br>Your OTP is: <b>$otp</b><br>This code will expire in 5 minutes.";
        $mail->AltBody = "Your OTP is: $otp";

        $mail->send();
    } catch (Exception $e) {
        echo json_encode(["status"=>"error","message"=>"Could not send OTP email. Mailer Error: {$mail->ErrorInfo}"]);
        exit;
    }

    // Insert notification for admin
    $stmtNotif = $pdo->prepare("INSERT INTO notifications (type, icon, title, message, recipient, target_id, link)
        VALUES ('user','fa-user-plus','New User Registration',:message,'Admin',:user_id,'customers.html')");
    $stmtNotif->execute([
        ':message'=> "$firstname $lastname started signup, pending OTP verification at ".date('Y-m-d H:i:s'),
        ':user_id'=> $user_id
    ]);

    echo json_encode([
        "status"=>"pending",
        "message"=>"OTP sent to your email",
        "email"=>$email
    ]);

} catch(Exception $e) {
    echo json_encode(["status"=>"error","message"=>$e->getMessage()]);
}
?>
