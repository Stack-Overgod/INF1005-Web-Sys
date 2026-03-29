<?php
session_start();
require_once 'db.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if (!$order_id) {
    header("Location: orders.php");
    exit;
}

// Fetch order info and verify ownership using parameterized query
$stmt = $pdo->prepare("SELECT * FROM order_info WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    // If order doesn't exist or doesn't belong to the user
    header("Location: orders.php");
    exit;
}

// Fetch order items using parameterized query
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Invoice Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .invoice-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 80vh;
            padding: 4rem 2rem;
        }
        .invoice-container {
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: var(--neon-glow);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 800px;
            position: relative;
            z-index: 10;
        }
        .invoice-header {
            border-bottom: 2px solid var(--neon);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .invoice-title {
            font-family: var(--font-display);
            color: var(--text-white);
            margin: 0;
            font-size: 2.5rem;
            letter-spacing: 0.1em;
        }
        .invoice-meta {
            text-align: right;
            color: var(--text-grey);
            font-size: 0.9rem;
        }
        .invoice-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .info-section h2 {
            color: var(--neon);
            font-family: var(--font-display);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.1em;
            margin-bottom: 0.75rem;
        }
        .info-content {
            color: var(--text-white);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        .invoice-table th {
            text-align: left;
            padding: 1rem;
            background: rgba(0, 229, 255, 0.1);
            color: var(--neon);
            font-family: var(--font-display);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
        }
        .invoice-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-white);
        }
        .invoice-table tr:last-child td {
            border-bottom: none;
        }
        .invoice-total-section {
            margin-left: auto;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }
        .total-row.grand-total {
            border-top: 1px solid var(--neon);
            margin-top: 1rem;
            padding-top: 1rem;
            color: var(--neon);
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 900;
        }
        .footer-note {
            margin-top: 4rem;
            text-align: center;
            color: var(--text-grey);
            font-size: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
        }
        @media print {
            .navbar, .footer, .btn-print { display: none !important; }
            body { background: white; color: black; }
            .invoice-container { box-shadow: none; border: 1px solid #ddd; background: white; }
            .invoice-title, .info-section h4, .invoice-table th, .total-row.grand-total { color: black; border-color: black; }
            .invoice-table td, .info-content { color: black; }
        }
    </style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main class="invoice-wrapper">
    <div class="invoice-container">
        <div class="text-right mb-4">
            <button onclick="window.print()" class="btn btn-outline-info btn-sm btn-print">
                <i class="fa fa-print mr-2"></i>Print Invoice
            </button>
        </div>

        <div class="invoice-header">
            <div>
                <h1 class="invoice-title">INVOICE</h1>
                <div class="info-content mt-2">OVERCLOCK<span style="color:var(--neon)">/</span>TECH</div>
            </div>
            <div class="invoice-meta">
                <div>Order #ORD-<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></div>
                <div>Date: <?= date('d M Y, g:i a', strtotime($order['timestamp'])) ?></div>
                <div>Payment: <?= htmlspecialchars($order['payment_method']) ?></div>
                <div>Status: <span style="color:var(--neon)"><?= strtoupper($order['status']) ?></span></div>
            </div>
        </div>

        <div class="invoice-info-grid">
            <div class="info-section">
                <h2>Customer Details</h2>
                <div class="info-content">
                    <?= htmlspecialchars($_SESSION['username']) ?><br>
                    <?= htmlspecialchars($_SESSION['email']) ?>
                </div>
            </div>
            <div class="info-section">
                <h2>Shipping Address</h2>
                <div class="info-content">
                    <?= nl2br(htmlspecialchars($order['address'])) ?>
                </div>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): 
                    $subtotal = $item['purchased_price'] * $item['quantity'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-right">$<?= number_format($item['purchased_price'], 2) ?></td>
                    <td class="text-right">$<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php 
            $calc_subtotal = 0;
            foreach ($order_items as $item) {
                $calc_subtotal += $item['purchased_price'] * $item['quantity'];
            }
            $calc_shipping = $order['total_price'] - $calc_subtotal;
        ?>
        <div class="invoice-total-section">
            <div class="total-row">
                <span>Subtotal</span>
                <span>$<?= number_format($calc_subtotal, 2) ?></span>
            </div>
            <div class="total-row">
                <span>Shipping</span>
                <span><?= $calc_shipping > 0 ? '$' . number_format($calc_shipping, 2) : 'FREE' ?></span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL</span>
                <span>$<?= number_format($order['total_price'], 2) ?></span>
            </div>
        </div>

        <div class="footer-note">
            <p>Thank you for choosing OVERCLOCK/TECH. For any questions regarding this invoice, please contact support@overclocktech.com</p>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>