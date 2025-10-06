<?php
    include("../assets/connection/connection.php");
    if (!isset($_GET['orderId'])) {
        die("Order code is required.");
    }

    $order_code = $_GET['orderId'];

    try {
        // Fetch order details
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = :order_code");
        $stmt->execute(['order_code' => $order_code]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            die("Order not found.");
        }

        // Fetch user details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE mobile_number = :mobile LIMIT 1");
        $stmt->execute(['mobile' => $order['mobile']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch order items
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_code = :order_code");
        $stmt->execute(['order_code' => $order_code]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("DB Error: " . $e->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Receipt - <?= htmlspecialchars($order['order_code']) ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f3f4f6;
        padding: 2rem;
        color: #1a202c;
    }
    .receipt-container {
        max-width: 700px;
        margin: auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        overflow: hidden;
        position: relative;
    }
    .receipt-header {
        background: #2563eb;
        color: white;
        padding: 1rem;
        text-align: center;
        position: relative;
    }
    .receipt-header h2 {
        margin: 0;
    }
    .print-btn {
        position: absolute;
        right: 15px;
        top: 15px;
        background: #fff;
        color: #2563eb;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.9rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        transition: all 0.2s ease-in-out;
    }
    .print-btn:hover {
        background: #2563eb;
        color: #fff;
    }
    .section {
        padding: 1rem 1.5rem;
        border-bottom: 1px dashed #d1d5db;
    }
    .section:last-child {
        border-bottom: none;
    }
    h3 {
        margin-bottom: .5rem;
        font-size: 1rem;
        text-transform: uppercase;
        color: #374151;
        border-bottom: 2px solid #2563eb;
        display: inline-block;
        padding-bottom: .2rem;
    }
    .detail-row {
        margin: .3rem 0;
    }
    .detail-row strong {
        display: inline-block;
        width: 140px;
        color: #111827;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: .5rem;
    }
    table th, table td {
        border-bottom: 1px dashed #d1d5db;
        padding: .5rem;
        text-align: left;
    }
    table th {
        background: #f9fafb;
        text-transform: uppercase;
        font-size: .85rem;
    }
    .total-section {
        padding: 1rem 1.5rem;
        text-align: right;
    }
    .total-section div {
        margin: .3rem 0;
    }
    .total-section strong {
        font-size: 1.1rem;
        color: #2563eb;
    }

    /* Print Styling */
    @media print {
        body {
            background: #fff;
        }
        .print-btn {
            display: none;
        }
    }
</style>
</head>
<body>
<div class="receipt-container" id="receipt">
    <div class="receipt-header">
        <h2>Order Receipt</h2>
        <p>Order Code: <?= htmlspecialchars($order['order_code']) ?></p>
        <button class="print-btn" onclick="printReceipt()">🖨 Print</button>
    </div>

    <!-- Customer Details -->
    <div class="section">
        <h3>Customer Info</h3>
        <div class="detail-row"><strong>Name:</strong> <?= htmlspecialchars($user['firstname'] . " " . $user['lastname']) ?></div>
        <div class="detail-row"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>
        <div class="detail-row"><strong>Mobile:</strong> <?= htmlspecialchars($user['mobile_number']) ?></div>
        <div class="detail-row"><strong>Address:</strong> 
            <?= htmlspecialchars($user['house_no']) ?>, <?= htmlspecialchars($user['barangay']) ?>, 
            <?= htmlspecialchars($user['city']) ?>, <?= htmlspecialchars($user['province']) ?>, 
            <?= htmlspecialchars($user['region']) ?>
        </div>
    </div>

    <!-- Order Items -->
    <div class="section">
        <h3>Order Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Attributes</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= htmlspecialchars($item['attributes']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td>₱<?= number_format($item['price'], 2) ?></td>
                    <td>₱<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Payment & Totals -->
    <div class="section total-section">
        <div><strong>Subtotal:</strong> ₱<?= number_format($order['subtotal'], 2) ?></div>
        <div><strong>Shipping:</strong> ₱<?= number_format($order['shipping'], 2) ?></div>
        <div><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></div>
        <div>Payment Method: <?= strtoupper(htmlspecialchars($order['payment_method'])) ?></div>
        <div>Status: <b><?= ucfirst(htmlspecialchars($order['status'])) ?></b></div>
        <div>Order Date: <?= date("F j, Y g:i A", strtotime($order['created_at'])) ?></div>
    </div>
</div>

<script>
function printReceipt() {
    window.print();
}
</script>
</body>
</html>
