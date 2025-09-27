<?php 
    include("../connection/connection.php");

    if (isset($_GET['municipality_code'])) {
        $municipality_code = $_GET['municipality_code'];
        
        try {
            $sql = "SELECT barangay_code, barangay_description 
                    FROM barangay 
                    WHERE bgymuncod = :municipality_code 
                    ORDER BY barangay_description ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':municipality_code', $municipality_code, PDO::PARAM_STR);
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
?>