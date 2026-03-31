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

$is_admin_view = false;
$view_user_id = $user_id;

if (isset($_GET['client_id']) && ($_SESSION['role'] ?? '') === 'staff') {
    $view_user_id = (int)$_GET['client_id'];
    $is_admin_view = true;
}

// Fetch order info and verify ownership using parameterized query
$stmt = $pdo->prepare("SELECT * FROM order_info WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $view_user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    // If order doesn't exist or doesn't belong to the user
    $redirect_url = $is_admin_view ? "orders.php?client_id=" . $view_user_id : "orders.php";
    header("Location: $redirect_url");
    exit;
}

// Fetch customer info if admin view
$view_user_name = '';
$view_user_email = '';
if ($is_admin_view) {
    $userStmt = $pdo->prepare("SELECT fname, lname, email FROM customers WHERE customer_id = ?");
    $userStmt->execute([$view_user_id]);
    $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
    if ($userRow) {
        $view_user_name = trim($userRow['fname'] . ' ' . $userRow['lname']);
        $view_user_email = $userRow['email'];
    }
} else {
    $view_user_name = $_SESSION['username'] ?? '';
    $view_user_email = $_SESSION['email'] ?? '';
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
    <meta name="description" content="OVERCLOCK/TECH — Premium gaming hardware. Ready-to-ship gaming PCs, laptops, keyboards, mice and more.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Bootstrap 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">-->
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">  
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main class="page-wrapper">
    <div class="page-container page-container-narrow">
        <?php if ($is_admin_view): ?>
            <div class="alert alert-warning text-center" style="margin-bottom: 2rem; border-radius: 8px;">
                <strong>Reminder:</strong> You are currently viewing an order for customer <strong><?= htmlspecialchars($view_user_name) ?></strong>.
            </div>
        <?php endif; ?>
        <div class="text-right mb-4">
            <button onclick="window.print()" class="btn btn-info btn-sm btn-print">
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
                    <?= htmlspecialchars($view_user_name) ?><br>
                    <?= htmlspecialchars($view_user_email) ?>
                </div>
            </div>
            <div class="info-section">
                <h2>Shipping Address</h2>
                <div class="info-content">
                    <?= nl2br(htmlspecialchars($order['address'])) ?>
                </div>
            </div>
        </div>

        <table class="table table-borderless text-white">
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

        <div>
            <p>Thank you for choosing OVERCLOCK/TECH. For any questions regarding this invoice, please contact support@overclocktech.com</p>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>