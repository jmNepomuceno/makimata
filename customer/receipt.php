<?php
include("../assets/connection/connection.php");

if (!isset($_GET['orderId'])) {
    die("Order code is required.");
}
$order_code = $_GET['orderId'];

try {
    // Fetch order
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = :order_code");
    $stmt->execute(['order_code' => $order_code]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) die("Order not found.");

    // Fetch user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE mobile_number = :mobile LIMIT 1");
    $stmt->execute(['mobile' => $order['mobile']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch order_items
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_code = :order_code ORDER BY id ASC");
    $stmt->execute(['order_code' => $order_code]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Try fetch shipping fee from provinces table using province name (case-insensitive)
    $shippingFee = floatval($order['shipping'] ?? 0);
    if (!empty($order['province'])) {
        $stmt = $pdo->prepare("SELECT shipping_price_range FROM provinces WHERE LOWER(province_description) = LOWER(:prov) LIMIT 1");
        $stmt->execute(['prov' => $order['province']]);
        $provRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($provRow && !empty($provRow['shipping_price_range'])) {
            // shipping_price_range stored like "₱200" — extract numeric
            if (preg_match('/([\d,.]+)/', $provRow['shipping_price_range'], $m)) {
                $shippingFee = floatval(str_replace(',', '', $m[1]));
            }
        }
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

/**
 * Build breakdown HTML for a single order item.
 * - Uses item.price (already multiplied by qty) only for Subtotal display.
 * - Uses per-unit values (unit_base) to compute size % and finish flat amounts.
 */
function buildItemBreakdownHtml($item) {
    $qty = max(1, intval($item['quantity']));
    $storedSubtotal = floatval($item['price']); // DB stores already multiplied price
    // Determine a sensible unit price: prefer base_price column if present, else price/qty
    $unit_base = null;
    if (!empty($item['base_price']) && is_numeric($item['base_price'])) {
        $unit_base = floatval($item['base_price']);
    } else {
        // fallback
        $unit_base = $qty > 0 ? floatval($item['price']) / $qty : 0;
    }
    $unit_base = round($unit_base, 2);

    $lines = [];
    // show attributes raw line first (as user expects)
    if (!empty($item['attributes'])) {
        // show the attributes string as provided
        $lines[] = '<div class="attr-item"><strong>Attributes:</strong> ' . htmlspecialchars(trim($item['attributes'])) . '</div>';
    }

    // base price (unit)
    $lines[] = '<div><strong>Base Price:</strong> ₱' . number_format($unit_base, 2) . '</div>';

    // parse attributes for adjustments (size, finish)
    $size_add = 0.0;
    $finish_add = 0.0;
    $extra_add = 0.0;
    $engravingText = '';
    $messageCardText = '';

    // parse attributes entries like "Size: Large +10% | Finish: Rust Brown"
    if (!empty($item['attributes'])) {
        $attrs = explode('|', $item['attributes']);
        foreach ($attrs as $a) {
            $a = trim($a);
            if ($a === '') continue;

            // if contains "Size" detect large/small and percentage
            if (stripos($a, 'size:') !== false) {
                // look for +NN% in the value
                if (preg_match('/\+(\d+)%/', $a, $m)) {
                    $pct = floatval($m[1]);
                    $size_add = round($unit_base * ($pct / 100), 2);
                    $lines[] = ' <div>Size (' . htmlspecialchars($a) . '): +' . '₱' . number_format($size_add, 2) . '</div>';
                } else {
                    // no percent e.g., "Size: Large" — show it but no money change
                    $lines[] = '<div>' . htmlspecialchars($a) . '</div>';
                }
            } elseif (stripos($a, 'finish:') !== false) {
                // any premium / dark adds +40 (your logic)
                if (preg_match('/premium|dark/i', $a)) {
                    $finish_add = 40.00;
                    $lines[] = '<div>Finish (' . htmlspecialchars($a) . '): +' . '₱' . number_format($finish_add, 2) . '</div>';
                } else {
                    // finish but not premium/dark — show as text
                    $lines[] = '<div>' . htmlspecialchars($a) . '</div>';
                }
            } else {
                // fallback: display any other attribute as-is (e.g., color)
                $lines[] = '<div>' . htmlspecialchars($a) . '</div>';
            }
        }
    }

    // parse customization_json if exists
    if (!empty($item['customization_json'])) {
        $cj = json_decode($item['customization_json'], true);
        if (is_array($cj)) {
            // engraving
            if (!empty($cj['engraving']) && trim($cj['engraving']) !== '') {
                $engravingText = trim($cj['engraving']);
                // engraving cost in your site = +50 (based on previous code)
                $lines[] = '<div>Engraving (+₱50.00): "' . htmlspecialchars($engravingText) . '"</div>';
                $extra_add += 50.00;
            }
            // message card
            if (!empty($cj['message_card']) || !empty($cj['card']) || !empty($cj['message'])) {
                // accept several keys to be robust
                $msg = $cj['message_card'] ?? $cj['card'] ?? $cj['message'] ?? '';
                if (trim($msg) !== '') {
                    $messageCardText = trim($msg);
                    // earlier code used +20 for card
                    $lines[] = '<div>Message Card (+₱20.00): "' . htmlspecialchars($messageCardText) . '"</div>';
                    $extra_add += 20.00;
                }
            }
            // check for extra_cost field in customization_json
            if (!empty($cj['extra_cost']) && is_numeric($cj['extra_cost'])) {
                $extra_add += floatval($cj['extra_cost']);
                $lines[] = '<div>Extra Customization (+₱' . number_format(floatval($cj['extra_cost']), 2) . ')</div>';
            }
        }
    }

    // also check separate column extra_cost (DB)
    if (!empty($item['extra_cost']) && is_numeric($item['extra_cost'])) {
        $ec = floatval($item['extra_cost']);
        if ($ec > 0) {
            $extra_add += $ec;
            $lines[] = '<div>Extra Customization (+₱' . number_format($ec, 2) . ')</div>';
        }
    }

    // If size_add or finish_add were not listed above (example: attributes string didn't include text but we still want them),
    // we already pushed lines when parsed attributes; no duplication.

    // Show per-unit summary (optional) and finally the Subtotal (which is item.price — already qty * unit_final)
    // Compose final breakdown HTML (show unit calculations then subtotal)
    // If you want to show "Subtotal: ₱607.00 × 1" similar to your example, show qty too.
    $subtotal_display = floatval($item['price']); // DB value (already multiplied)
    $qty = intval($item['quantity']);
    $unit_final = $qty > 0 ? round($subtotal_display / $qty, 2) : $unit_base;

    // For display, show computed per-unit additions if we have them (size_add, finish_add, extra_add)
    // But avoid duplicating lines: we've already added per-line entries for size/finish/extra above.
    // Show Unit Final (optional)
    $lines[] = '<div><strong>Unit Final:</strong> ₱' . number_format($unit_final, 2) . '</div>';

    $lines[] = '<div><strong>Subtotal:</strong> ₱' . number_format($subtotal_display, 2) . ' × ' . $qty . '</div>';
    // you said not to multiply again — so subtotal_display is final

    return '<div class="price-breakdown">' . implode('', $lines) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Billing Invoice - <?= htmlspecialchars($order['order_code']) ?></title>
<style>
    body { font-family: Arial, sans-serif; background:#f3f4f6; padding:2rem; color:#111827; }
    .receipt-container { max-width:800px; margin:auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 8px 30px rgba(0,0,0,0.12); }
    .receipt-header { background:#2563eb; color:#fff; padding:1rem; text-align:center; position:relative; }
    .print-btn { position:absolute; right:16px; top:14px; background:#fff; color:#2563eb; border:none; padding:6px 10px; border-radius:6px; cursor:pointer; font-weight:600; }
    .section{ padding:1rem 1.5rem; border-bottom:1px dashed #e5e7eb; }
    h3{ margin:0 0 .5rem 0; font-size:.95rem; color:#374151; text-transform:uppercase; display:inline-block; padding-bottom:.2rem; border-bottom:2px solid #2563eb; }
    table { width:100%; border-collapse:collapse; margin-top:.5rem; }
    th, td { padding:.6rem; text-align:left; vertical-align:top; border-bottom:1px dashed #e5e7eb; }
    th { background:#f9fafb; font-size:.85rem; text-transform:uppercase; }
    .price-breakdown { font-size:.92rem; line-height:1.4; color:#111827; margin-left:6px; }
    .price-breakdown div { margin:4px 0; }
    .total-section { padding:1rem 1.5rem; text-align:right; }
    .total-section div{ margin:6px 0; }
    @media print { .print-btn{display:none;} body{background:#fff;} }
</style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>Billing Invoice</h2>
            <p>Order Code: <?= htmlspecialchars($order['order_code']) ?></p>
            <button class="print-btn" onclick="window.print()">🖨 Print</button>
        </div>

        <div class="section">
            <h3>Customer Info</h3>
            <div class="detail-row"><strong>Name:</strong> <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
            <div class="detail-row"><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></div>
            <div class="detail-row"><strong>Mobile:</strong> <?= htmlspecialchars($user['mobile_number'] ?? '') ?></div>
            <div class="detail-row"><strong>Address:</strong> <?= htmlspecialchars($user['house_no'] . ', ' . $user['barangay'] . ', ' . $user['city'] . ', ' . $user['province'] . ', ' . $user['region']) ?></div>
        </div>

        <div class="section">
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Customization & Breakdown</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $computedSubtotal = 0.0;
                    foreach ($order_items as $item):
                        $computedSubtotal += floatval($item['price']); // price already multiplied in DB
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                        <td><?= buildItemBreakdownHtml($item) ?></td>
                        <td><?= intval($item['quantity']) ?></td>
                        <td>₱<?= number_format(floatval($item['price']), 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section total-section">
            <div><strong>Subtotal:</strong> ₱<?= number_format($computedSubtotal, 2) ?></div>
            <div><strong>Shipping Fee:</strong> ₱<?= number_format($shippingFee, 2) ?></div>
            <div><strong>Total:</strong> ₱<?= number_format($computedSubtotal + floatval($shippingFee), 2) ?></div>
            <div>Payment Method: <?= strtoupper(htmlspecialchars($order['payment_method'])) ?></div>
            <div>Status: <b><?= ucfirst(htmlspecialchars($order['status'])) ?></b></div>
            <div>Order Date: <?= date("F j, Y g:i A", strtotime($order['created_at'])) ?></div>
        </div>
    </div>
</body>
</html>
