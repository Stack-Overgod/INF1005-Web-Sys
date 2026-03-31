<?php
session_start();
require_once 'db.php';
require_once 'lib/stripe-php/init.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = false;
$error = '';

// Check if we have pending order data recorded during checkout
if (isset($_SESSION['pending_order_data']) && isset($_GET['session_id'])) {
    $data = $_SESSION['pending_order_data'];
    $session_id = $_GET['session_id'];
    
    try {
        // --- FETCH STRIPE SESSION TO GET ACTUAL PAYMENT METHOD ---
        Stripe::setApiKey('');
        // Expand payment_intent.payment_method to see the actual method used
        $checkout_session = Session::retrieve(['id' => $session_id, 'expand' => ['payment_intent.payment_method']]);
        
        $method_used = 'Stripe';
        if ($checkout_session->payment_intent && $checkout_session->payment_intent->payment_method) {
            $type = $checkout_session->payment_intent->payment_method->type;
            $map = [
                'card' => 'Credit Card',
                'paynow' => 'PayNow',
                'grabpay' => 'GrabPay',
                'alipay' => 'Alipay'
            ];
            $method_used = $map[$type] ?? ucfirst($type);
        }

        $pdo->beginTransaction();
        
        // Create the final order record using parameterized query
        $stmt = $pdo->prepare("INSERT INTO order_info (user_id, total_price, address, payment_method, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $data['total_price'], $data['full_address'], $method_used, 'Success']);
        $order_id = $pdo->lastInsertId();
        
        // Add items to the order record and update stock
        foreach ($data['items'] as $item) {
            // Insert into order_items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, purchased_price, quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['product_id'], $item['name'], $item['price'], $item['quantity']]);
            
            // DECREASE product stock in inventory
            $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
            $stmt_stock->execute([$item['quantity'], $item['product_id']]);
        }
        
        // CLEAR the user's cart in the database now that payment is successful
        $stmt_clear = $pdo->prepare("DELETE FROM cartitems WHERE user_id = ?");
        $stmt_clear->execute([$user_id]);
        
        $pdo->commit();
        
        // Clear the temporary session storage
        unset($_SESSION['pending_order_data']);
        $success = true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = "Error finalizing order: " . $e->getMessage();
    }
} else {
    // If no session data, check if they are just visiting (handled by view)
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Order Successful</title>
    <meta name="description" content="Learn about OVERCLOCK/TECH, our mission, values, and the team behind our premium gaming hardware experience.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main class="page-wrapper">
    <div class="page-container page-container-narrow text-center">
        <?php if ($success): ?>
            <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h1 class="success-h1">TRANSACTION <span>SUCCESSFUL</span></h1>
            <p class="success-text">A receipt has been recorded in your profile. You can track your order status in the orders history.</p>
        <?php elseif ($error): ?>
            <div class="success-icon" style="color:#ff4d4d; filter: drop-shadow(0 0 15px #ff4d4d);"><i class="fa-solid fa-circle-xmark"></i></div>
            <h1 class="success-h1">PROCESSING <span>ERROR</span></h1>
            <p class="success-text"><?= htmlspecialchars($error) ?></p>
        <?php else: ?>
            <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h1 class="success-h1">THANK <span>YOU</span></h1>
            <p class="success-text">Your order has been already processed.</p>
        <?php endif; ?>
             <a href="home.php" class="btn btn-primary">Return Home</a>
            <a href="orders.php" class="btn btn-outline-info">View My Orders</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>