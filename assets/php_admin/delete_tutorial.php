<?php
include("../connection/connection.php");
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Tutorial ID required"]);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM tutorials WHERE id = :id");
        $stmt->execute([":id" => $id]);

        echo json_encode(["status" => "success", "message" => "Tutorial deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

?>