<?php 
    include("../connection/connection.php");

    if (isset($_GET['province_code'])) {
        $province_code = $_GET['province_code'];

        try {
            $sql = "SELECT municipality_code, municipality_description 
                    FROM city
                    WHERE province_code = :province_code 
                    ORDER BY municipality_description ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':province_code', $province_code, PDO::PARAM_STR);
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
?>