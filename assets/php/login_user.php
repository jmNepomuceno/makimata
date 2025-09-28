<?php

include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        exit;
    }

    $sql = "SELECT user_ID, firstname, lastname, email, password 
            FROM users 
            WHERE email = :email 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify password
        if ($password === $user['password']) {
            // Store user info in session
            $_SESSION['user'] = [
                "user_ID"   => $user['user_ID'],
                "firstname" => $user['firstname'],
                "lastname"  => $user['lastname'],
                "email"     => $user['email']
            ];

            $sqlNotif = "INSERT INTO notifications (type, icon, title, message, recipient, target_id)
             VALUES ('user', 'fa-user', 'User Login', :message, 'Admin', :user_id)";
            $stmtNotif = $pdo->prepare($sqlNotif);
            $stmtNotif->execute([
                ':message' => $user['firstname'] . ' ' . $user['lastname'] . ' logged in at ' . date('Y-m-d H:i:s'),
                ':user_id' => $user['user_ID']
            ]);

            echo json_encode(["status" => "success"]);
            
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No account found with this email"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
