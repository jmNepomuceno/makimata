<?php 
    include("../connection/connection.php");

    if (isset($_GET['region_code'])) {
        $region_code = $_GET['region_code'];

        try {
            $sql = "SELECT province_code, province_description 
                    FROM provinces
                    WHERE region_code = :region_code 
                    ORDER BY province_description ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':region_code', $region_code, PDO::PARAM_STR);
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
?>