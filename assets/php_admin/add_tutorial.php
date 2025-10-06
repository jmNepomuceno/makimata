<?php
include("../connection/connection.php");
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $type = $_POST['type'] ?? '';
        $video_url = $_POST['videoUrl'] ?? null;
        $article_url = $_POST['articleUrl'] ?? null;
        $last_updated = $_POST['lastUpdated'] ?? null;
        $image = $_POST['image'] ?? null;

        $sql = "INSERT INTO tutorials (title, description, type, video_url, article_url, last_updated)
                VALUES (:title, :description, :type, :video_url, :article_url, :last_updated)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':type' => $type,
            ':video_url' => $video_url,
            ':article_url' => $article_url,
            ':last_updated' => $last_updated,
        ]);

        echo json_encode(["status" => "success", "message" => "Tutorial added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

?>