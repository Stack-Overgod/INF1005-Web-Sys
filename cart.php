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

$total_amt = 0;
foreach ($cart_items as $product) {
    $total_amt += $product['price'] * $product['quantity'];
}
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
    .cart-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 2rem;
    }
    .cart-container {
        background: var(--bg-card);
        border: 1px solid var(--border);
        box-shadow: var(--neon-glow);
        border-radius: 16px;
        padding: 2.5rem;
        width: 100%;
        max-width: 1100px;
        animation: fadeIn 0.8s ease-out;
        position: relative;
        z-index: 10;
    }
    .table { color: var(--text-white); }
    .table thead th { border-top: none; color: var(--neon); font-family: var(--font-display); font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase; }
    .table td { border-top: 1px solid rgba(0, 229, 255, 0.1); vertical-align: middle; }
    .custom-select { background: var(--bg-card2); border: 1px solid var(--border); color: var(--text-white); }
    .custom-select:focus { background: var(--bg-card2); border-color: var(--neon); color: var(--text-white); box-shadow: var(--neon-glow); }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="cart-wrapper">
    <div class="cart-container">
        <h2 class="section-title text-center mb-5"><span class="hi">YOUR</span> CART</h2>
<?php if (!empty($cart_items)): ?>
<div class="row">
    <div class="col-12">
        <table class="table table-striped table-responsive" name="cart">
            <thead>
                <tr>
                    <th scope="col">Product</th>
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
                    $subtotal = $product['price'] * $product['quantity'];
                ?>
                <tr id="<?php echo $product['product_id']; ?>_row">
                    <td class="col-lg-1"><img style="width: 64px;height: 64px;" class="img-fluid"
                            src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"/>
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td class="d-none"><?php echo $product['quantity']; ?></td>

                    <td class="d-none"><?php echo $product['product_id']; ?></td>

                    <td class="text-right">
                        <span>$<?php echo number_format($product['price'], 2); ?></span>
                    </td>

                    <td class="justify-content-center">
                        <div class="input-group mb-12">
                            <select class="custom-select" id="<?php echo $product['product_id']; ?>" onchange="change_quantity(this)">
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
                            onclick="delete_item(this)"><i class="fa fa-trash"></i>
                        </button> 
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"></td>
                    <td class="d-none"></td>
                    <td class="d-none"></td>
                    <td class="text-right">Total:</td>
                    <td class="text-right">$<?php echo number_format($total_amt, 2); ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
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