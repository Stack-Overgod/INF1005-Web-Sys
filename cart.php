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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Shopping Cart</title>
    <meta name="description" content="OVERCLOCK/TECH — Shop the latest gaming PCs, laptops, keyboards, mice and peripherals. Built for champions.">
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

<main id="main-content" class="page-wrapper">
    <div class="page-container page-container-wide">
        <h1 class="section-title text-center mb-5"><span class="hi">YOUR</span> CART</h1>
<?php if (!empty($cart_items)): ?>
<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-striped table-cyber text-white">
                <thead>
                    <tr>
                        <th scope="col" style="width: 80px;">Product</th>
                        <th scope="col">Name</th>
                        <th class="d-none"></th>
                        <th class="d-none"></th>
                        <th scope="col" class="text-right">Price</th>
                        <th scope="col" class="text-center">Quantity</th>
                        <th scope="col" class="text-right">Sub-Total</th>
                        <th scope="col" class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $product): 
                        $item_subtotal = $product['price'] * $product['quantity'];
                    ?>
                    <tr id="<?php echo $product['product_id']; ?>_row">
                        <td><img style="width: 64px;height: 64px; border-radius: 4px;" class="img-thumbnail"
                                src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </td>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td class="d-none"><?php echo $product['quantity']; ?></td>
                        <td class="d-none"><?php echo $product['product_id']; ?></td>

                        <td class="text-right">
                            <span class="text-neon">$<?php echo number_format($product['price'], 2); ?></span>
                        </td>

                        <td class="text-center">
                            <div style="max-width: 100px; margin: 0 auto;">
                                <select title="Quantity" aria-label="Quantity" class="custom-select" id="<?php echo $product['product_id']; ?>" onchange="change_quantity(this)">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ($product['quantity'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo $i; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </td>
                        <td class="text-right"><strong class="text-white">$<?php echo number_format($item_subtotal, 2); ?></strong></td>
                        <td class="text-right">
                            <button class="btn btn-sm btn-danger" id="<?php echo $product['product_id']; ?>_"
                                onclick="delete_item(this)"><i class="fa fa-trash"></i>
                            </button> 
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4"></td>
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <td class="text-right text-muted">Sub-Total:</td>
                        <td class="text-right">$<?php echo number_format($subtotal, 2); ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <td class="text-right text-muted">Shipping:</td>
                        <td class="text-right">$<?php echo number_format($shipping_fee, 2); ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td class="d-none"></td>
                        <td class="d-none"></td>
                        <td class="text-right"><strong class="text-white">TOTAL:</strong></td>
                        <td class="text-right"><strong class="text-neon" style="font-size: 1.2rem;">$<?php echo number_format($total_amt, 2); ?></strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col mb-2">
        <div class="row">
            <div class="col-sm-12 col-md-3 mr-auto">
                <a href="products.php" class="btn btn-block btn-light">Continue Shopping</a>
            </div>
            <div class="col-sm-12 col-md-3 ml-auto">
                <a href="checkout.php" class="btn btn-md btn-block btn-success text-uppercase">Checkout</a>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<h6 class="text-center mb-4">There is nothing in your cart.</h6>
<div class="text-center">
    <a href="products.php" class="btn btn-md btn-primary">Shop</a>
</div>
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
    const productId = select.id;
    const qty = select.value;
    window.location.href = `cart.php?action=update&product_id=${productId}&qty=${qty}`;
}
</script>

</body>
</html>