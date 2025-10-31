<?php 
include("../connection/connection.php");
date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_POST['orderData'])) {
        throw new Exception("No order data received");
    }

    $orderData = json_decode($_POST['orderData'], true);
    if (!$orderData) {
        throw new Exception("Invalid order data format");
    }

    $items   = $orderData['items'] ?? [];
    $user    = $orderData['user'] ?? [];
    $payment = $orderData['payment'] ?? 'cod';
    $totals  = $orderData['totals'] ?? [];

    if (empty($items)) {
        throw new Exception("No items in order");
    }
    if (empty($user['name']) || empty($user['mobile']) || empty($user['address'])) {
        throw new Exception("Incomplete user details");
    }

    // --- Generate new order_code ---
    $stmt = $pdo->query("SELECT order_code FROM orders ORDER BY id DESC LIMIT 1");
    $lastOrder = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastOrder && !empty($lastOrder['order_code'])) {
        $lastNum = (int) substr($lastOrder['order_code'], 3); // remove "ORD"
        $newNum  = str_pad($lastNum + 1, 6, "0", STR_PAD_LEFT);
        $order_code = "ORD" . $newNum;
    } else {
        $order_code = "ORD000001";
    }

    // Insert into orders table
    $sql = "INSERT INTO orders 
            (order_code, user_name, mobile, barangay, city, province, house_no, payment_method, subtotal, shipping, total, status, created_at) 
            VALUES (:order_code, :user_name, :mobile, :barangay, :city, :province, :house_no, :payment, :subtotal, :shipping, :total, :status, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':order_code' => $order_code,
        ':user_name'  => $user['name'],
        ':mobile'     => $user['mobile'],
        ':barangay'   => $user['address']['barangay'],
        ':city'       => $user['address']['city'],
        ':province'   => $user['address']['province'],
        ':house_no'   => $user['address']['house_no'],
        ':payment'    => $payment,
        ':subtotal'   => $totals['subtotal'],
        ':shipping'   => $totals['shipping'],
        ':total'      => $totals['total'],
        ':status'     => 'pending'
    ]);

    // Insert each order item
    $sqlItem = "INSERT INTO order_items 
                (order_code, product_code, name, attributes, quantity, price) 
                VALUES (:order_code, :product_code, :name, :attributes, :quantity, :price)";
    $stmtItem = $pdo->prepare($sqlItem);

    // Prepare update stock statement
    $sqlUpdateStock = "UPDATE products SET stock = stock - :qty WHERE product_code = :product_code";
    $stmtUpdateStock = $pdo->prepare($sqlUpdateStock);

    foreach ($items as $item) {
        $qty = (int) preg_replace('/\D/', '', $item['qty']);

        // Insert into order_items
        $stmtItem->execute([
            ':order_code'  => $order_code,
            ':product_code'=> $item['product_code'] ?? null,
            ':name'        => $item['name'],
            ':attributes'  => $item['attributes'] ?? '',
            ':quantity'    => $qty,
            ':price'       => preg_replace('/[^\d.]/', '', $item['price'])
        ]);

        // 🔹 Update product stock
        if (!empty($item['product_code'])) {
            $stmtUpdateStock->execute([
                ':qty' => $qty,
                ':product_code' => $item['product_code']
            ]);
        }
    }

    // Insert initial order history (Placed status)
    $sqlHistory = "INSERT INTO order_history (order_code, status, remarks, created_at) 
                   VALUES (:order_code, :status, :remarks, NOW())";
    $stmtHistory = $pdo->prepare($sqlHistory);
    $stmtHistory->execute([
        ':order_code' => $order_code,
        ':status'     => 'pending',
        ':remarks'    => 'Order placed successfully'
    ]);

    // Add notification
    $sqlNotif = "INSERT INTO notifications (type, icon, title, message, recipient, target_id, link)
                 VALUES ('order', 'fa-shopping-cart', 'New Order Received', :message, 'Sales Team', :order_id, 'orders.html')";
    $stmtNotif = $pdo->prepare($sqlNotif);
    $stmtNotif->execute([
        ':message' => 'New order #' . $order_code . ' from ' . $user['name'],
        ':order_id' => $order_code
    ]);

    echo json_encode([
        "status"  => "success",
        "message" => "Order placed successfully and stock updated",
        "data"    => [
            "order_code" => $order_code,
            "user"       => $user,
            "totals"     => $totals,
            "items"      => $items
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>
