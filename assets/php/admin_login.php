<?php
    include("../connection/connection.php");
    date_default_timezone_set('Asia/Manila');
    header('Content-Type: application/json; charset=utf-8');
    session_start();

    try {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($password)) {
            echo json_encode(["status" => "error", "message" => "Username and password are required"]);
            exit;
        }

        $sql = "SELECT id, firstname, lastname, username, password 
                FROM admin_users 
                WHERE username = :username 
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // Verify password
            if ($password === $admin['password']) {
                // Save session
                $_SESSION['admin'] = [
                    "id"        => $admin['id'],
                    "firstname" => $admin['firstname'],
                    "lastname"  => $admin['lastname'],
                    "username"  => $admin['username']
                ];

                echo json_encode(["status" => "success"]);

                $sqlNotif = "INSERT INTO notifications (type, icon, title, message, recipient)
                VALUES ('user', 'fa-sign-in-alt', 'Admin Login', :message, 'All Admins')";
                $stmtNotif = $pdo->prepare($sqlNotif);
                $stmtNotif->execute([
                    ':message' => $admin['firstname'] . ' ' . $admin['lastname'] . ' logged in at ' . date('Y-m-d H:i:s')
                ]);

            } else {
                echo json_encode(["status" => "error", "message" => "Invalid password"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid username"]);
        }

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
?>