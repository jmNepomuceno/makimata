<?php 
    include("../connection/connection.php");

    try{
        $sql = "SELECT id, region_code, region_description 
            FROM region 
            ORDER BY CAST(region_code AS UNSIGNED) ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
        
    }catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
?>