<?php
session_start();
require_once 'db.php';

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
    
    try {
        $pdo->beginTransaction();
        
        // Create the final order record using parameterized query
        $stmt = $pdo->prepare("INSERT INTO order_info (user_id, total_price, address, payment_method, status) VALUES (?, ?, ?, ?, 'Success')");
        $stmt->execute([$user_id, $data['total_price'], $data['full_address'], 'Stripe Card (Verified)']);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .success-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 2rem;
        }
        .success-container {
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: var(--neon-glow);
            border-radius: 16px;
            padding: 4rem 2rem;
            width: 100%;
            max-width: 750px;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }
        .success-icon {
            font-size: 5rem;
            color: var(--neon);
            margin-bottom: 2rem;
            filter: drop-shadow(0 0 15px var(--neon));
        }
        .success-h1 {
            font-family: var(--font-display);
            color: var(--text-white);
            margin-bottom: 1.5rem;
            letter-spacing: 0.1em;
            font-weight: 900;
        }
        .success-h1 span { color: var(--neon); }
        .success-text {
            color: var(--text-grey);
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }
        .feedback-card {
            background: rgba(0, 229, 255, 0.05);
            border: 1px solid rgba(0, 229, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
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
        .btn-home {
            background: var(--neon);
            color: var(--bg-black);
            border: none;
        }
        .btn-home:hover {
            box-shadow: var(--neon-glow);
            transform: translateY(-2px);
        }
        .btn-orders {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-white);
            border: 1px solid var(--border);
            margin-left: 1rem;
        }
        .btn-orders:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--neon);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main class="success-wrapper">
    <div class="success-container">
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
             <a href="home.php" class="btn btn-success-page btn-home">Return Home</a>
            <a href="orders.php" class="btn btn-success-page btn-orders">View My Orders</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>