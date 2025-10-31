<?php
include("../connection/connection.php");
session_start();
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 1; // fallback for now

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $product_id = $_POST['product_id'] ?? null;

        if (!$product_id) {
            echo json_encode(["status" => "error", "message" => "Missing product ID"]);
            exit;
        }

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $product_id]);
            echo json_encode(["status" => "success", "message" => "Added to wishlist"]);
        } elseif ($action === 'remove') {
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            echo json_encode(["status" => "success", "message" => "Removed from wishlist"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid action"]);
        }
    } else {
        // GET request: fetch wishlist
        $stmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "wishlist" => $wishlist]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
