<?php
session_start();
require_once 'db.php';
require_once 'lib/stripe-php/init.php';
$config = require 'config.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit;
}

// Ensure the user is coming from the cart page
if (!isset($_SESSION['can_checkout']) || $_SESSION['can_checkout'] !== true) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items and calculate total from DB
$stmt = $pdo->prepare("SELECT p.product_id, p.name, p.price, ci.quantity FROM cartitems ci JOIN products p ON ci.product_id = p.product_id WHERE ci.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user info for prefilling the form based on role
$user_role = $_SESSION['role'] ?? 'customer';
if ($user_role === 'staff') {
    $stmt = $pdo->prepare("SELECT fname, lname, email FROM staff WHERE staff_id = ?");
} else {
    $stmt = $pdo->prepare("SELECT fname, lname, email FROM customers WHERE customer_id = ?");
}
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['fname' => '', 'lname' => '', 'email' => ''];

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping_fee = 10.00;
$total_amt = $subtotal + $shipping_fee;

$error = '';

// Handle Checkout Trigger
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if ($total_amt >= 0.50) {
        try {
            // --- PREPARE PENDING ORDER in Session ---
            // Instead of saving now, we save it on ordersuccess.php after Stripe redirect
            $_SESSION['pending_order_data'] = [
                'total_price'  => $total_amt,
                'full_address' => $_POST['address'] . ", " . $_POST['unit_no'] . " SG " . $_POST['zip'],
                'items'        => $cart_items
            ];

            // Clear the checkout flag
            unset($_SESSION['can_checkout']);

            // --- STRIPE PREPARATION ---
            Stripe::setApiKey($config['STRIPE_API_KEY']); // Intentionally left out api key - the repo is still public btw
            // Build Line Items for Stripe
            $stripe_line_items = [];
            foreach ($cart_items as $item) {
                $stripe_line_items[] = [
                    'price_data' => [
                        'currency' => 'sgd',
                        'unit_amount' => (int)round($item['price'] * 100),
                        'product_data' => [
                            'name' => $item['name'],
                        ],
                    ],
                    'quantity' => $item['quantity'],
                ];
            }
            
            // Add Shipping Fee as a line item
            $stripe_line_items[] = [
                'price_data' => [
                    'currency' => 'sgd',
                    'unit_amount' => (int)round($shipping_fee * 100),
                    'product_data' => [
                        'name' => 'Shipping Fee',
                    ],
                ],
                'quantity' => 1,
            ];

            $session = Session::create([
                'payment_method_types' => ['card', 'grabpay', 'paynow', 'alipay'],
                'line_items' => $stripe_line_items,
                'mode' => 'payment',
                'success_url' => 'http://localhost/ordersuccess.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost/checkout.php',
            ]);

            header("Location: " . $session->url);
            exit();
        } catch (Exception $e) {
            $error = 'Stripe Error: ' . $e->getMessage();
        }
    } else {
        $error = 'Your cart is empty or the amount is too low.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'includes/nav.php'; ?>


<main id="main-content" class="page-wrapper">    
    <div class="page-container page-container-medium">
        <h1 class="section-title text-center mb-5"><span class="hi">CHECK</span>OUT</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-5 order-md-2 mb-4">
                <h2 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Your cart</span>
                    <span class="badge badge-secondary badge-pill"><?php echo count($cart_items); ?></span>
                </h2>
                <div class="table-responsive">
                    <table class="table table-striped text-white mb-3">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $product): ?>
                            <tr>
                                <td>
                                    <div><?php echo htmlspecialchars($product['name']); ?></div>
                                    <small class="text-grey">Quantity: <?php echo $product['quantity']; ?></small>
                                </td>
                                <td class="text-right align-middle text-grey">$<?php echo number_format($product['price'] * $product['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td>Subtotal (SGD)</td>
                                <td class="text-right"><strong class="text-white">$<?php echo number_format($subtotal, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Shipping (SGD)</td>
                                <td class="text-right"><strong class="text-white">$<?php echo number_format($shipping_fee, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Total (SGD)</td>
                                <td class="text-right"><strong class="text-neon">$<?php echo number_format($total_amt, 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-7 order-md-1">
                <h2 class="mb-4">Shipping Information</h2>
                <form action="checkout.php" method="POST" class="needs-validation">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName">First name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" value="<?= htmlspecialchars($user_info['fname']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName">Last name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" value="<?= htmlspecialchars($user_info['lname']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_info['email']) ?>" placeholder="you@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="unit_no">Unit No.</label>
                            <input type="text" class="form-control" id="unit_no" name="unit_no" placeholder="#01-1234" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="zip">Zip Code</label>
                            <input type="text" class="form-control" id="zip" name="zip" placeholder="123456" required pattern="[0-9]{6}">
                        </div>
                    </div>

                    <hr class="mb-4" style="border-top: 1px solid rgba(0,229,255,0.1);">
                    
                    <h2 class="mb-3">Payment</h2>
                    <p class="text-muted small mb-4">Alipay, Credit Card, PayNow and GrabPay are supported. Click the button below to enter to Stripe payment gateway.</p>
                    
                    <input type="hidden" name="place_order" value="1">
                    <button class="btn btn-success btn-block text-uppercase" type="submit">
                        <i class="fa-brands fa-stripe mr-2" style="font-size:1.5rem; vertical-align:middle;"></i> 
                        Pay $<?php echo number_format($total_amt, 2); ?> Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>