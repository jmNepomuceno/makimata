<?php
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$fullname = trim($_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['lastname']);

try {
    // 1️⃣ Get all order codes owned by this user
    $orderQuery = "SELECT order_code FROM orders WHERE user_name = :fullname";
    $stmt = $pdo->prepare($orderQuery);
    $stmt->execute([':fullname' => $fullname]);
    $userOrders = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($userOrders)) {
        echo json_encode(['status' => 'success', 'notifications' => []]);
        exit;
    }

    // 2️⃣ Get all notifications whose target_id matches user's order codes
    $inQuery = implode(',', array_fill(0, count($userOrders), '?'));

    $query = "
        SELECT 
            n.id,
            n.type,
            n.icon,
            n.title,
            n.message,
            n.recipient,
            n.user_id,
            n.target_id,
            n.link,
            n.status,
            n.created_at,
            oi.name AS product_name
        FROM notifications AS n
        LEFT JOIN order_items AS oi 
            ON n.target_id = oi.order_code
        WHERE n.type = 'order'
          AND n.target_id IN ($inQuery)
        ORDER BY n.created_at DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute($userOrders);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3️⃣ Replace order code with product name in message if available
    foreach ($notifications as &$notif) {
        if (!empty($notif['product_name'])) {
            $notif['message'] = preg_replace(
                '/#' . preg_quote($notif['target_id'], '/') . '/',
                $notif['product_name'],
                $notif['message']
            );
        }
    }

    echo json_encode([
        'status' => 'success',
        'notifications' => $notifications
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
