<?php
session_start();
require_once 'db.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Allow access to checkout only if coming from cart
$_SESSION['can_checkout'] = true;

// Handle Actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $product_id = $_GET['product_id'] ?? null;

    if ($action === 'delete' && $product_id) {
        $stmt = $pdo->prepare("DELETE FROM cartitems WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        header("Location: cart.php");
        exit;
    }

    if ($action === 'update' && $product_id && isset($_GET['qty'])) {
        $qty = (int)$_GET['qty'];
        if ($qty > 0) {
            $stmt = $pdo->prepare("UPDATE cartitems SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$qty, $user_id, $product_id]);
        }
        header("Location: cart.php");
        exit;
    }
}

// Fetch cart items for the user
$stmt = $pdo->prepare("SELECT ci.cartitems_id, p.product_id, p.name, p.price, p.image, ci.quantity 
                        FROM cartitems ci 
                        JOIN products p ON ci.product_id = p.product_id 
                        WHERE ci.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($cart_items as $product) {
    $subtotal += $product['price'] * $product['quantity'];
}
$shipping_fee = 10.00;
$total_amt = $subtotal + $shipping_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="OVERCLOCK/TECH — Shop the latest gaming PCs, laptops, keyboards, mice and peripherals. Built for champions.">
  <title>OVERCLOCK/TECH — Shopping Cart</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .btn-success {
        color: #000000 !important;
    }
  </style>

</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="page-wrapper" role="main">
    <div class="page-container page-container-wide">
        <h1 class="section-title text-center mb-5"><span class="hi">YOUR</span> CART</h1>
<?php if (!empty($cart_items)): ?>
<section aria-label="Cart items">
<div class="row">
    <div class="col-12">
        <table class="table table-striped table-responsive text-white" aria-label="Shopping cart items">
            <thead>
                <tr>
                    <th scope="col">Product</th>
                    <th scope="col">Name</th>
                    <th class="d-none" aria-hidden="true"></th>
                    <th class="d-none" aria-hidden="true"></th>
                    <th scope="col" class="text-right">Price</th>
                    <th scope="col" class="text-center">Quantity</th>
                    <th scope="col" class="text-right">Sub-Total</th>
                    <th scope="col" class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $product): 
                    $subtotal = $product['price'] * $product['quantity'];
                ?>
                <tr id="<?php echo $product['product_id']; ?>_row">
                    <td class="col-lg-1"><img style="width: 64px;height: 64px;" class="img-fluid"
                            src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td class="d-none"><?php echo $product['quantity']; ?></td>

                    <td class="d-none"><?php echo $product['product_id']; ?></td>

                    <td class="text-right">
                        <span>$<?php echo number_format($product['price'], 2); ?></span>
                    </td>

                    <td class="justify-content-center">
                        <div class="input-group mb-12">
                            <select title="Quantity for <?php echo htmlspecialchars($product['name']); ?>" aria-label="Quantity for <?php echo htmlspecialchars($product['name']); ?>" class="custom-select" id="qty_<?php echo $product['product_id']; ?>" onchange="change_quantity(this)">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($product['quantity'] == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </td>
                    <td id="subtotal" class="text-right">$<?php echo number_format($subtotal, 2); ?></td>
                    <td class="text-right">
                        <button class="btn btn-sm btn-danger" id="<?php echo $product['product_id']; ?>_"
                            onclick="delete_item(this)" aria-label="Remove <?php echo htmlspecialchars($product['name']); ?> from cart"><i class="fa fa-trash" aria-hidden="true"></i> Delete Item  
                        </button> 
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"></td>
                    <td class="d-none"></td>
                    <td class="d-none"></td>
                    <td class="text-right">Sub-Total:</td>
                    <td class="text-right">$<?php echo number_format($subtotal, 2); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td class="d-none"></td>
                    <td class="d-none"></td>
                    <td class="text-right">Shipping:</td>
                    <td class="text-right">$<?php echo number_format($shipping_fee, 2); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td class="d-none"></td>
                    <td class="d-none"></td>
                    <td class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>$<?php echo number_format($total_amt, 2); ?></strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <nav class="col mb-2" aria-label="Cart actions">
        <div class="row">
            <div class="col-sm-12 col-md-3 mr-auto">
                <a href="products.php" class="btn btn-block btn-light" role="button">Continue Shopping</a>
            </div>
            <div class="col-sm-12 col-md-3 ml-auto">
                <a href="checkout.php" class="btn btn-md btn-block btn-success text-uppercase" role="button">Checkout</a>
            </div>
        </div>
    </nav>
</div>
</section>
<?php else: ?>
<section aria-label="Empty cart" role="status">
<p class="text-center mb-4">There is nothing in your cart.</p>
<div class="text-center">
    <a href="products.php" class="btn btn-md btn-primary" role="button">Shop</a>
</div>
</section>
<?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
function delete_item(btn) {
    const productId = btn.id.replace('_', '');
    if (confirm("Are you sure you want to delete this product from your cart?")) {
        window.location.href = `cart.php?action=delete&product_id=${productId}`;
    }
}

function change_quantity(select) {
    const productId = select.id.replace('qty_', '');
    const qty = select.value;
    window.location.href = `cart.php?action=update&product_id=${productId}&qty=${qty}`;
}
</script>

</body>
</html>