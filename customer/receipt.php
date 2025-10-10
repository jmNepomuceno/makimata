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

// Helper: Format attributes into readable blocks
function parseAttributes($attributes) {
    if (!$attributes) return "<em>No customizations</em>";

    $parts = explode('|', $attributes);
    $html = '<div class="attr-list">';
    foreach ($parts as $p) {
        $p = trim($p);
        if (!$p) continue;
        // Split key-value pair (e.g., "Size: Large +10%")
        $kv = explode(':', $p, 2);
        $key = trim($kv[0] ?? '');
        $value = trim($kv[1] ?? '');
        $html .= "<div class='attr-item'><strong>{$key}</strong>: {$value}</div>";
    }
    $html .= '</div>';
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Billing Invoice - <?= htmlspecialchars($order['order_code']) ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f3f4f6;
        padding: 2rem;
        color: #1a202c;
    }
    .receipt-container {
        max-width: 750px;
        margin: auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .receipt-header {
        background: #2563eb;
        color: white;
        padding: 1rem;
        text-align: center;
        position: relative;
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
    .print-btn:hover { background: #2563eb; color: #fff; }
    .section {
        padding: 1rem 1.5rem;
        border-bottom: 1px dashed #d1d5db;
    }
    .section:last-child { border-bottom: none; }
    h3 {
        margin-bottom: .5rem;
        font-size: 1rem;
        text-transform: uppercase;
        color: #374151;
        border-bottom: 2px solid #2563eb;
        display: inline-block;
        padding-bottom: .2rem;
    }
    .detail-row { margin: .3rem 0; }
    .detail-row strong { width: 140px; display: inline-block; color: #111827; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: .5rem;
    }
    th, td {
        border-bottom: 1px dashed #d1d5db;
        padding: .6rem;
        text-align: left;
        vertical-align: top;
    }
    th { background: #f9fafb; text-transform: uppercase; font-size: .85rem; }
    .attr-list {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .attr-item {
        background: #f3f4f6;
        border-radius: 4px;
        padding: 3px 6px;
        font-size: 0.85rem;
    }
    .attr-item strong {
        color: #1e40af;
    }
    .total-section {
        padding: 1rem 1.5rem;
        text-align: right;
    }
    .total-section strong {
        font-size: 1.1rem;
        color: #2563eb;
    }
    @media print { .print-btn { display: none; } body { background: #fff; } }
</style>
</head>
<body>
<div class="receipt-container" id="receipt">
    <div class="receipt-header">
        <h2>Billing Invoice</h2>
        <p>Order Code: <?= htmlspecialchars($order['order_code']) ?></p>
        <button class="print-btn" onclick="printReceipt()">🖨 Print</button>
    </div>

    <!-- Customer Info -->
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
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <?php
                        $attributesHTML = parseAttributes($item['attributes']);
                        $itemTotal = $item['quantity'] * $item['price'];
                        $extraCost = floatval($item['extra_cost'] ?? 0);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $attributesHTML ?>
                            <?php if ($extraCost > 0): ?>
                                <div style="margin-top:4px;color:#2563eb;font-size:0.85rem;">
                                    <em>Customization +₱<?= number_format($extraCost,2) ?></em>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td>₱<?= number_format($item['price'], 2) ?></td>
                        <td>₱<?= number_format($itemTotal + $extraCost, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Totals -->
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
function printReceipt() { window.print(); }
</script>
</body>
</html>
