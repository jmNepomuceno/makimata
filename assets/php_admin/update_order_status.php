<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

// ✅ Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // adjust path to PHPMailer autoload.php

try {
    if (!isset($_POST['ids']) || !isset($_POST['status'])) {
        throw new Exception("Required parameters not received");
    }

    $ids = $_POST['ids'];
    $newStatus = trim($_POST['status']);
    $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    if (!is_array($ids) || empty($ids)) {
        throw new Exception("No orders selected");
    }
    if (!in_array($newStatus, $validStatuses)) {
        throw new Exception("Invalid status");
    }

    // --- SQL Statements ---
    $updateOrderStmt = $pdo->prepare("UPDATE orders SET status = :status WHERE order_code = :order_code");
    $insertHistoryStmt = $pdo->prepare("INSERT INTO order_history (order_code, status, remarks, created_at) VALUES (:order_code, :status, :remarks, NOW())");
    $insertNotifStmt = $pdo->prepare("
        INSERT INTO notifications 
        (type, icon, title, message, recipient, target_id, link, created_at) 
        VALUES 
        ('order', 'fa-shopping-cart', 'Order Status Updated', :message, 'Sales Team', :target_id, 'orders.html', NOW())
    ");
    $fetchOrderStmt = $pdo->prepare("SELECT user_name, mobile, total FROM orders WHERE order_code = :order_code");
    $fetchUserStmt = $pdo->prepare("SELECT email FROM users WHERE mobile_number = :mobile LIMIT 1");

    $emails = [];

    foreach ($ids as $orderCode) {
        // --- Update records ---
        $updateOrderStmt->execute([':status' => $newStatus, ':order_code' => $orderCode]);
        $insertHistoryStmt->execute([':order_code' => $orderCode, ':status' => $newStatus, ':remarks' => "Status updated to {$newStatus}"]);
        $insertNotifStmt->execute([':message' => "Order #$orderCode status updated to $newStatus", ':target_id' => $orderCode]);

        // --- Fetch order info ---
        $fetchOrderStmt->execute([':order_code' => $orderCode]);
        $order = $fetchOrderStmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $fetchUserStmt->execute([':mobile' => $order['mobile']]);
            $user = $fetchUserStmt->fetch(PDO::FETCH_ASSOC);

            if ($user && !empty($user['email'])) {
                $email = $user['email'];
                $username = $order['user_name'];
                $total = $order['total'];

                // ✅ Record for JSON response
                $emails[] = [
                    'order_code' => $orderCode,
                    'user_name' => $username,
                    'email' => $email,
                    'total' => $total
                ];

                // ✅ Send email via PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'makimatamakimata@gmail.com';
                    $mail->Password   = 'bcvn hegt gryv ubav'; // Gmail App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('makimatamakimata@gmail.com', 'Makimata');
                    $mail->addAddress($email, $username);

                    $mail->isHTML(true);
                    $mail->Subject = "Your Order #{$orderCode} Status Update";
                    $mail->Body = "
                        <div style='font-family: Arial, sans-serif; line-height:1.6;'>
                            <h2 style='color:#007bff;'>Order Status Update</h2>
                            <p>Dear <strong>{$username}</strong>,</p>
                            <p>Your order <strong>{$orderCode}</strong> has been updated to:
                                <span style='color:#007bff; font-weight:bold;'>{$newStatus}</span>.
                            </p>
                            <p><strong>Total:</strong> ₱{$total}</p>
                            <br>
                            <p>Thank you for shopping with us! You can check your order details by logging into your account.</p>
                            <p style='margin-top: 30px;'>Best regards,<br><strong>Mikimata Team</strong></p>
                        </div>
                    ";
                    $mail->AltBody = "Hello {$username}, your order {$orderCode} has been updated to {$newStatus}. Total: ₱{$total}.";

                    $mail->send();
                } catch (Exception $e) {
                    // Don’t stop script if one email fails
                    error_log("Email failed for {$email}: {$mail->ErrorInfo}");
                }
            }
        }
    }

    echo json_encode([
        "status" => "success",
        "message" => count($ids) . " orders updated successfully and emails sent (if available).",
        "emails" => $emails
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>
