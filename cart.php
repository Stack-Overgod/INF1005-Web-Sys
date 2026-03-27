<?php
// 1. Path setup - using __DIR__ ensures PHP finds the folder
require_once('lib/stripe-php/init.php');

// 2. Import the Stripe classes so you don't get "Undefined type" errors
use Stripe\Stripe;
use Stripe\Checkout\Session;

const STRIPE_API_KEY = '';

// 3. Set your API Key
Stripe::setApiKey(base64_decode(STRIPE_API_KEY));
//Intentionally left out api key - the repo is still public btw, if u need to try, ask jr for the api key

$error = '';

// 4. CHECK: Was the form submitted?
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    
    $amount_dollars = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $amount_cents = (int)round($amount_dollars * 100);

    // Stripe minimum is usually 50 cents
    if ($amount_cents >= 50) {
        try {
            $session = Session::create([
                'payment_method_types' => ['card','grabpay','paynow'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'sgd',
                        'unit_amount' => $amount_cents,
                        'product_data' => [
                            'name' => 'Overclock Tech',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://localhost:80',
                'cancel_url' => 'https://yourwebsite.com/cancel.html',
            ]);

            header("Location: " . $session->url);
            exit();
        } catch (Exception $e) {
            $error = 'Stripe Error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please enter an amount of at least $0.50.';
    }
}
?>

<!DOCTYPE html>
<html>
<?php

session_start();
$activePage = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="OVERCLOCK/TECH — Shop the latest gaming PCs, laptops, keyboards, mice and peripherals. Built for champions.">
  <title>OVERCLOCK/TECH — Home</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content">    
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <section class="section" aria-labelledby="what-heading">
        <form method="POST">
            <h2>Make a Payment</h2>
            <label>Amount (USD):</label>
            <input type="number" name="amount" step="0.01" min="0.50" placeholder="0.00" required>
            <button type="submit">Proceed to Checkout</button>
        </form>
    </section>

</main><!-- /#main-content -->

<?php include 'includes/footer.php'; ?>

</body>
</html>