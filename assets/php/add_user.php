<?php 
    include("../connection/connection.php");
    date_default_timezone_set('Asia/Manila');
    header('Content-Type: application/json; charset=utf-8');

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
        $check->execute([ ':email' => $email ]);

        if ($check->rowCount() > 0) {
            echo json_encode([
                "status"  => "error",
                "message" => "Email already registered"
            ]);
            exit;
        }

        // Generate OTP (6-digit random number)
        $otp = rand(100000, 999999);
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Insert new user (set is_verified = 0 by default)
        $sql = "INSERT INTO users 
                (firstname, lastname, email, mobile_number, region, province, city, barangay, house_no, password, created_at, is_verified, otp_code, otp_expires_at) 
                VALUES (:firstname, :lastname, :email, :mobile, :region, :province, :city, :barangay, :house_no, :password, :created_at, 0, :otp, :otp_expires)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':firstname' => $firstname,
            ':lastname'  => $lastname,
            ':email'     => $email,
            ':mobile'    => $mobile,
            ':region'    => $region,
            ':province'  => $province,
            ':city'      => $city,
            ':barangay'  => $barangay,
            ':house_no'  => $house_no,
            ':password'  => $password, // ⚠️ later replace with password_hash()
            ':created_at'=> date('Y-m-d H:i:s'),
            ':otp'       => $otp,
            ':otp_expires'=> $otpExpiry
        ]);

        $user_id = $pdo->lastInsertId();

        // TODO: integrate actual SMS sending here with your SMS provider API
        // For now, just log OTP to test
        // e.g., sendSMS($mobile, "Your verification code is $otp");

        // Insert notification for Admin
        $sqlNotif = "INSERT INTO notifications (type, icon, title, message, recipient, target_id, link)
             VALUES ('user', 'fa-user-plus', 'New User Registration', :message, 'Admin', :user_id, 'customers.html')";
        $stmtNotif = $pdo->prepare($sqlNotif);
        $stmtNotif->execute([
            ':message' => $firstname . ' ' . $lastname . ' started signup, pending OTP verification at ' . date('Y-m-d H:i:s'),
            ':user_id' => $user_id
        ]);

        // Response to frontend (pending verification)
        echo json_encode([
            "status" => "pending",
            "message" => "OTP sent to your mobile number",
            "mobile" => $mobile
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status"  => "error",
            "message" => $e->getMessage()
        ]);
    }
?>
