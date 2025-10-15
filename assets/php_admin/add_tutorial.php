<?php
include("../connection/connection.php");
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $article_url = $_POST['article_url'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        $type = 'video';
        $last_updated = date("Y-m-d H:i:s");
        $video_url = null;

        // ✅ Correct relative and absolute paths
        $uploadDirRelative = "assets/upload/tutorials/";
        $uploadDirAbsolute = realpath(__DIR__ . "/../upload/tutorials");

        // If realpath fails (folder not yet created), fallback
        if ($uploadDirAbsolute === false) {
            $uploadDirAbsolute = __DIR__ . "/../upload/tutorials";
        }

        // Ensure folder exists
        if (!is_dir($uploadDirAbsolute)) {
            mkdir($uploadDirAbsolute, 0777, true);
        }

        // ✅ File handling
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['video_file']['tmp_name'];
            $fileName = basename($_FILES['video_file']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid("vid_") . "." . $fileExtension;

            $destPathAbsolute = $uploadDirAbsolute . DIRECTORY_SEPARATOR . $newFileName;
            $destPathRelative = $uploadDirRelative . $newFileName;

            // ✅ Extra debug before moving
            if (!file_exists($fileTmpPath)) {
                echo json_encode(["success" => false, "message" => "Temp file not found.", "tmp" => $fileTmpPath]);
                exit;
            }

            if (!is_writable($uploadDirAbsolute)) {
                echo json_encode(["success" => false, "message" => "Upload folder not writable.", "folder" => $uploadDirAbsolute]);
                exit;
            }

            if (move_uploaded_file($fileTmpPath, $destPathAbsolute)) {
                $video_url = $destPathRelative;
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Failed to move uploaded file.",
                    "debug" => [
                        "tmp" => $fileTmpPath,
                        "dest" => $destPathAbsolute,
                        "existsTmp" => file_exists($fileTmpPath),
                        "isWritable" => is_writable(dirname($destPathAbsolute))
                    ]
                ]);
                exit;
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "File upload error or missing file.",
                "error_code" => $_FILES['video_file']['error'] ?? 'no_file',
                "files" => $_FILES
            ]);
            exit;
        }

        // ✅ Insert into database
        $sql = "INSERT INTO tutorials (title, description, type, video_url, article_url, last_updated, status)
                VALUES (:title, :description, :type, :video_url, :article_url, :last_updated, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':type' => $type,
            ':video_url' => $video_url,
            ':article_url' => $article_url,
            ':last_updated' => $last_updated,
            ':status' => $status
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Tutorial uploaded successfully.",
            "saved_path" => $video_url
        ]);

    } else {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
