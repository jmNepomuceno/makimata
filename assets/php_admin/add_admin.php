<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($firstname) || empty($lastname) || empty($email) || empty($username) || empty($password)) {
        throw new Exception("All fields are required.");
    }

    // Hash password before saving (important for security)

    // Insert query
    $sql = "INSERT INTO admin (firstname, lastname, email, username, password, created_at)
            VALUES (:firstname, :lastname, :email, :username, :password, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':firstname' => $firstname,
        ':lastname' => $lastname,
        ':email' => $email,
        ':username' => $username,
        ':password' => $password
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Admin added successfully."
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
