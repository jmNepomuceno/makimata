<?php
    include("./assets/connection/connection.php");
    session_start();

    $sql = "SELECT 
                *
            FROM products";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>"; print_r($data); echo "</pre>";
?>