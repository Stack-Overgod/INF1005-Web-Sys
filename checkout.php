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

$total_amt = 0;
foreach ($cart_items as $item) {
    $total_amt += $item['price'] * $item['quantity'];
}

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
            Stripe::setApiKey(base64_decode('c2tfdGVzdF81MVRGNjBiM1FnNExnNjcxNnYyQnBoNWVWanVXSkFvWGhEYTh4MkQ2bmRUcG1Lbzd6czU2UjFHSHpPMnJCTTVKTzlvY1ZnY1hBWlQ1QXJtdzYyRGRDMGdTWDAwdmtwUzhNQXc=')); // Intentionally left out api key - the repo is still public btw
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
    <style>
        .checkout-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 80vh;
            padding: 4rem 2rem;
        }
        .checkout-container {
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: var(--neon-glow);
            border-radius: 16px;
            padding: 3rem;
            width: 100%;
            max-width: 1000px;
            position: relative;
            z-index: 10;
        }
        .form-control {
            background: var(--bg-card2);
            border: 1px solid var(--border);
            color: var(--text-white);
        }
        .form-control:focus {
            background: var(--bg-card2);
            border-color: var(--neon);
            color: var(--text-white);
            box-shadow: var(--neon-glow);
        }
        .list-group-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(0, 229, 255, 0.1);
            color: var(--text-white);
        }
        .text-muted { color: var(--text-grey) !important; }
        label { color: var(--neon); font-family: var(--font-display); font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase; }
        .btn-order {
            background: var(--neon);
            color: var(--bg-black);
            font-family: var(--font-display);
            font-weight: 900;
            letter-spacing: 0.1em;
            padding: 1rem;
            transition: all 0.3s;
        }
        .btn-order:hover {
            box-shadow: var(--neon-glow);
            transform: translateY(-2px);
            background: var(--text-white);
        }
    </style>
</head>
<body>

<?php include 'includes/nav.php'; ?>


<main id="main-content" class="checkout-wrapper">    
    <div class="checkout-container">
        <h2 class="section-title text-center mb-5"><span class="hi">CHECK</span>OUT</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-5 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Your cart</span>
                    <span class="badge badge-secondary badge-pill"><?php echo count($cart_items); ?></span>
                </h4>
                <ul class="list-group mb-3">
                    <?php foreach ($cart_items as $product): ?>
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                            <h6 class="my-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                            <small class="text-muted">Quantity: <?php echo $product['quantity']; ?></small>
                        </div>
                        <span class="text-muted">$<?php echo number_format($product['price'] * $product['quantity'], 2); ?></span>
                    </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total (SGD)</span>
                        <strong class="text-neon">$<?php echo number_format($total_amt, 2); ?></strong>
                    </li>
                </ul>
            </div>

            <div class="col-md-7 order-md-1">
                <h4 class="mb-4">Shipping Information</h4>
                <form action="checkout.php" method="POST" class="needs-validation">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstName">First name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastName">Last name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
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
                    
                    <h4 class="mb-3">Payment</h4>
                    <p class="text-muted small mb-4">Alipay, Credit Card, PayNow and GrabPay are supported. Click the button below to enter to Stripe payment gateway.</p>
                    
                    <input type="hidden" name="place_order" value="1">
                    <button class="btn btn-order btn-block text-uppercase" type="submit">
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