<?php
include("../connection/connection.php");
header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $type = $_POST['type'] ?? '';
        $video_url = $_POST['videoUrl'] ?? null;
        $article_url = $_POST['articleUrl'] ?? null;
        $views = $_POST['views'] ?? 0;
        $last_updated = $_POST['lastUpdated'] ?? null;

        if (!$id) {
            echo json_encode(["status" => "error", "message" => "Tutorial ID is required"]);
            exit;
        }

        $sql = "UPDATE tutorials 
                SET title = :title,
                    description = :description,
                    type = :type,
                    video_url = :video_url,
                    article_url = :article_url,
                    views = :views,
                    last_updated = :last_updated
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':description' => $description,
            ':type' => $type,
            ':video_url' => $video_url,
            ':article_url' => $article_url,
            ':views' => $views,
            ':last_updated' => $last_updated,
        ]);

        echo json_encode(["status" => "success", "message" => "Tutorial updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

?>