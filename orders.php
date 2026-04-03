<?php
session_start();
require_once 'db.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin_view = false;
$view_user_id = $user_id;

if (isset($_GET['client_id']) && ($_SESSION['role'] ?? '') === 'staff') {
    $view_user_id = (int)$_GET['client_id'];
    $is_admin_view = true;
}

// Fetch user info for admin view banner
$view_user_name = '';
if ($is_admin_view) {
    $userStmt = $pdo->prepare("SELECT fname, lname FROM customers WHERE customer_id = ?");
    $userStmt->execute([$view_user_id]);
    $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
    if ($userRow) {
        $view_user_name = trim($userRow['fname'] . ' ' . $userRow['lname']);
    } else {
        $view_user_name = 'Unknown User';
    }
}

// Fetch orders for the user
$stmt = $pdo->prepare("SELECT * FROM order_info WHERE user_id = ? ORDER BY timestamp DESC");
$stmt->execute([$view_user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For each order, fetch its items
foreach ($orders as &$order) {
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order['order_id']]);
    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="OVERCLOCK/TECH — View your order history and track your latest gaming gear.">
    <title>OVERCLOCK/TECH — My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
    .order-footer .text-muted {
        color: #ffffff !important;
    }
    .btn-success {
        color: #000000 !important;
    }
</style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="page-wrapper">
    <div class="page-container page-container-wide">
        <?php if ($is_admin_view): ?>
            <div class="alert alert-warning text-center" role="alert" style="margin-bottom: 2rem; border-radius: 8px;">
                <strong>Reminder:</strong> You are currently viewing orders for customer <strong><?= htmlspecialchars($view_user_name) ?></strong>.
                <a href="staff.php" class="btn btn-sm btn-dark ml-3" role="button">Return to Staff Page</a>
            </div>
        <?php endif; ?>

        <h1 class="section-title text-center mb-5"><span class="hi">ORDER</span> HISTORY</h1>

        <?php if (empty($orders)): ?>
            <section class="no-orders" aria-label="No orders" role="status">
                <div class="empty-icon"><i class="fa-solid fa-box-open" aria-hidden="true"></i></div>
                <h2 class="text-white">No orders found.</h2>
                <p class="text-white">You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-success mt-3" role="button">Start Shopping</a>
            </section>
        <?php else: ?>
            <section aria-label="Order list">
            <?php foreach ($orders as $order): ?>
                <article class="order-card" aria-label="Order #ORD-<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?>">
                    <div class="order-header">
                        <div>
                            <span class="order-id">#ORD-<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></span>
                            <div class="order-date"><i class="far fa-calendar-alt mr-2" aria-hidden="true"></i><time datetime="<?= date('c', strtotime($order['timestamp'])) ?>"><?= date('F j, Y, g:i a', strtotime($order['timestamp'])) ?></time></div>
                        </div>
                        <span class="order-status status-<?= strtolower($order['status']) ?>" role="status" aria-label="Order status: <?= htmlspecialchars($order['status']) ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </div>
                    <div class="order-body">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <div>
                                    <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                    <div class="item-details">Quantity: <?= $item['quantity'] ?> × $<?= number_format($item['purchased_price'], 2) ?></div>
                                </div>
                                <div class="item-price">$<?= number_format($item['purchased_price'] * $item['quantity'], 2) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-footer">
                        <div>
                            <div class="text-muted small">Paid via <?= htmlspecialchars($order['payment_method']) ?></div>
                            <div class="text-muted small">Shipping to: <?= htmlspecialchars($order['address']) ?></div>
                        </div>
                        <div class="text-right d-flex flex-column align-items-end">
                            <span class="total-label">Total Amount</span>
                            <span class="total-amount mb-2">$<?= number_format($order['total_price'], 2) ?></span>
                            <a href="orderdetail.php?order_id=<?= $order['order_id'] ?><?= $is_admin_view ? '&client_id=' . $view_user_id : '' ?>" class="btn btn-outline-info" aria-label="View details for order #ORD-<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?>"> View Order</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
