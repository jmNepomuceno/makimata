<?php 
    include("../connection/connection.php");
    date_default_timezone_set('Asia/Manila');
    header('Content-Type: application/json; charset=utf-8');

    try {
        $firstname   = $_POST['firstname'];
        $lastname    = $_POST['lastname'];
        $email       = $_POST['email'];
        $mobile      = $_POST['contact'];
        $region      = $_POST['region'];
        $province    = $_POST['province'];
        $city        = $_POST['city'];
        $barangay    = $_POST['barangay'];
        $house_no    = $_POST['address'];
        $password    = $_POST['password'];

        // Check if email already exists
        $check = $pdo->prepare("SELECT user_ID FROM users WHERE email = :email");
        $check->execute([ ':email' => $email ]);

        if ($check->rowCount() > 0) {
            echo json_encode([
                "status"  => "error",
                "message" => "Email already registered"
            ]);
            exit;
        }

        // Hash password
        // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $sql = "INSERT INTO users 
                (firstname, lastname, email, mobile_number, region, province, city, barangay, house_no, password) 
                VALUES (:firstname, :lastname, :email, :mobile, :region, :province, :city, :barangay, :house_no, :password)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':firstname' => $firstname,
            ':lastname'  => $lastname,
            ':email'     => $email,
            ':mobile'    => $mobile,
            ':region'  => $region,
            ':province'  => $province,
            ':city'      => $city,
            ':barangay'  => $barangay,
            ':house_no'  => $house_no,
            ':password'  => $password
        ]);

        $user_id = $pdo->lastInsertId();

        echo json_encode([
            "status" => "success",
            "data" => [
                "id"        => $user_id,
                "fullname"  => $firstname . " " . $lastname,
                "email"     => $email,
                "mobile"    => $mobile,
                "region"    => $region,
                "province"  => $province,
                "city"      => $city,
                "barangay"  => $barangay,
                "house_no"  => $house_no,
                "password" => $password
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status"  => "error",
            "message" => $e->getMessage()
        ]);
    }
?>
