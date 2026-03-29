<?php
session_start();
require_once 'db.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the user
$stmt = $pdo->prepare("SELECT * FROM order_info WHERE user_id = ? ORDER BY timestamp DESC");
$stmt->execute([$user_id]);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .orders-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 80vh;
            padding: 4rem 2rem;
        }
        .btn-success-page {
            font-family: var(--font-display);
            padding: 0.8rem 2rem;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.1em;
            transition: all 0.3s;
        }
        .btn-orders {
            background: var(--neon);
            color: var(--bg-black);
            border: none;
        }
        .btn-orders:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--neon);
        }
        .orders-container {
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: var(--neon-glow);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 1100px;
            position: relative;
            z-index: 10;
            animation: fadeIn 0.8s ease-out;
        }
        .order-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(0, 229, 255, 0.1);
            border-radius: 12px;
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s, border-color 0.3s;
        }
        .order-card:hover {
            transform: translateY(-5px);
            border-color: var(--neon);
        }
        .order-header {
            background: rgba(0, 229, 255, 0.05);
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 229, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .order-id { font-family: var(--font-display); color: var(--neon); font-size: 1.1rem; }
        .order-date { color: var(--text-grey); font-size: 0.9rem; }
        .order-status {
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.05em;
        }
        .status-pending { background: rgba(255, 193, 7, 0.2); color: #ffc107; border: 1px solid #ffc107; }
        .status-shipped { background: rgba(0, 123, 255, 0.2); color: #007bff; border: 1px solid #007bff; }
        .status-delivered { background: rgba(40, 167, 69, 0.2); color: #28a745; border: 1px solid #28a745; }
        
        .order-body { padding: 1.5rem; }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .order-item:last-child { border-bottom: none; }
        .item-name { color: var(--text-white); font-weight: 500; }
        .item-details { color: var(--text-grey); font-size: 0.85rem; }
        .item-price { color: var(--text-white); font-family: var(--font-display); }

        .order-footer {
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total-label { color: var(--text-grey); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em; }
        .total-amount { color: var(--neon); font-size: 1.4rem; font-family: var(--font-display); font-weight: 900; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .no-orders {
            text-align: center;
            padding: 4rem 0;
        }
        .empty-icon {
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.1);
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="orders-wrapper">
    <div class="orders-container">
        <h1 class="section-title text-center mb-5"><span class="hi">ORDER</span> HISTORY</h1>

        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
                <h2 class="text-white">No orders found.</h2>
                <p class="text-white">You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-success-page btn-orders mt-3">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">#ORD-<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></span>
                            <div class="order-date"><i class="far fa-calendar-alt mr-2"></i><?= date('F j, Y, g:i a', strtotime($order['timestamp'])) ?></div>
                        </div>
                        <span class="order-status status-<?= strtolower($order['status']) ?>">
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
                        <div class="text-right">
                            <span class="total-label d-block">Total Amount</span>
                            <span class="total-amount">$<?= number_format($order['total_price'], 2) ?></span>
                            <a href="orderdetail.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-info mt-2">View Order</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
