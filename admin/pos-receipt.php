<?php 
require_once '../common/config.php';
require_once '../common/functions.php';
checkAdminAuth();

$order_id = (int)$_GET['id'];
$o = $conn->query("SELECT o.*, u.full_name as customer FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.id = $order_id")->fetch_assoc();
$items = $conn->query("SELECT * FROM order_items WHERE order_id = $order_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt_<?php echo $o['order_number']; ?></title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body { width: 80mm; font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #000; padding: 10px; background: #fff; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        .footer { margin-top: 20px; font-size: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">PRINT INVOICE</button>
        <a href="pos.php" style="margin-left: 10px;">Back to POS</a>
    </div>

    <div class="center">
        <h1 style="margin:0; font-size: 20px; letter-spacing: -1px;">LOCKINGSTYLE</h1>
        <p style="margin:2px 0;">Premium Streetwear Node 01</p>
        <p style="margin:2px 0; font-size: 10px;"><?php echo $site_settings['address_line']; ?></p>
    </div>

    <div class="dashed"></div>

    <p>Reg No: #<?php echo $o['order_number']; ?></p>
    <p>Date: <?php echo date('d-m-Y H:i', strtotime($o['created_at'])); ?></p>
    <p>Cashier: <?php echo $_SESSION['admin_name']; ?></p>
    <p>Customer: <?php echo $o['customer'] ?? 'Guest Node'; ?></p>

    <div class="dashed"></div>

    <table>
        <thead>
            <tr class="bold">
                <th class="text-left">ITEM</th>
                <th class="right">QTY</th>
                <th class="right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php while($item = $items->fetch_assoc()): ?>
            <tr>
                <td><?php echo substr($item['product_name'], 0, 15); ?></td>
                <td class="right"><?php echo $item['quantity']; ?></td>
                <td class="right"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="dashed"></div>

    <table class="bold" style="font-size: 14px;">
        <tr>
            <td>GRAND TOTAL</td>
            <td class="right"><?php echo formatPrice($o['total_amount']); ?></td>
        </tr>
    </table>

    <div class="dashed"></div>

    <div class="center footer">
        <p>THANK YOU FOR YOUR PATRONAGE</p>
        <p>Vault Access: lockingstyle.com</p>
        <p>No Refund Without Original Node Logic</p>
    </div>

</body>
</html>