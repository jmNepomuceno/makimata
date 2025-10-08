<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email/Username and password are required"]);
        exit;
    }

    // --- 1️⃣ Check USER table first ---
    $sqlUser = "SELECT user_ID AS id, firstname, lastname, email, password, is_verified 
                FROM users 
                WHERE email = :email 
                LIMIT 1";
    $stmt = $pdo->prepare($sqlUser);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // ✅ Verify account
        if ((int)$user['is_verified'] === 0) {
            echo json_encode(["status" => "error", "message" => "Your account is not yet verified."]);
            exit;
        }

        // ✅ Check password (plain for now)
        if ($password === $user['password']) {
            // ✅ Set session for USER
            $_SESSION['user'] = [
                "user_ID"   => $user['id'],
                "firstname" => $user['firstname'],
                "lastname"  => $user['lastname'],
                "email"     => $user['email'],
                "role"      => "user"
            ];

            // ✅ Update last login
            $sql = "UPDATE users SET last_login = :last_login WHERE user_ID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':last_login' => date('Y-m-d H:i:s'),
                ':id'         => $user['id']
            ]);

            // ✅ Insert notification
            $sqlNotif = "INSERT INTO notifications (type, icon, title, message, recipient, target_id)
                         VALUES ('user', 'fa-user', 'User Login', :message, 'Admin', :target_id)";
            $stmtNotif = $pdo->prepare($sqlNotif);
            $stmtNotif->execute([
                ':message' => $user['firstname'] . ' ' . $user['lastname'] . ' logged in at ' . date('Y-m-d H:i:s'),
                ':target_id' => $user['id']
            ]);

            echo json_encode(["status" => "success", "role" => "user"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
            exit;
        }
    }

    // --- 2️⃣ If not a user, check ADMIN table ---
    $sqlAdmin = "SELECT admin_ID AS id, firstname, lastname, email, username, password
                 FROM admin
                 WHERE email = :email OR username = :email
                 LIMIT 1";
    $stmt = $pdo->prepare($sqlAdmin);
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        if ($password === $admin['password']) {
            // ✅ Set session for ADMIN
            $_SESSION['admin'] = [
                "admin_ID"  => $admin['id'],
                "firstname" => $admin['firstname'],
                "lastname"  => $admin['lastname'],
                "email"     => $admin['email'],
                "username"  => $admin['username'],
                "role"      => "admin"
            ];

            // ✅ Update last login
            $sql = "UPDATE admin SET last_login = :last_login WHERE admin_ID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':last_login' => date('Y-m-d H:i:s'),
                ':id'         => $admin['id']
            ]);

            // ✅ Insert notification (Admin Login)
            $sqlNotif = "INSERT INTO notifications (type, icon, title, message, recipient, target_id)
                         VALUES ('admin', 'fa-user-shield', 'Admin Login', :message, 'All Admins', :target_id)";
            $stmtNotif = $pdo->prepare($sqlNotif);
            $stmtNotif->execute([
                ':message' => $admin['firstname'] . ' ' . $admin['lastname'] . ' logged in at ' . date('Y-m-d H:i:s'),
                ':target_id' => $admin['id']
            ]);

            echo json_encode(["status" => "success", "role" => "admin"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
            exit;
        }
    }

    // --- 3️⃣ No match found at all ---
    echo json_encode(["status" => "error", "message" => "No account found with this email or username"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
