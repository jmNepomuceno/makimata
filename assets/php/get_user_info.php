<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
// header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    // Check if user is logged in
    // if (!isset($_SESSION['user']) || empty($_SESSION['user']['user_ID'])) {
    //     echo json_encode(["status" => "error", "message" => "Not logged in"]);
    //     exit;
    // }

    $userId = $_SESSION['user']['user_ID'];

    $sql = "SELECT user_ID, firstname, lastname, email, mobile_number 
            FROM users 
            WHERE user_ID = :user_ID 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_ID' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            "status" => "success",
            "data" => [
                "user_ID"   => $user['user_ID'],
                "firstname" => $user['firstname'],
                "lastname"  => $user['lastname'],
                "email"     => $user['email'],
                "mobile"    => $user['mobile_number']
            ]
        ]);
    } 
    else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

?>
