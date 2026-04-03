<?php
session_start();
require_once 'db.php';

// fetch all categories for filter buttons
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// check if a category filter is selected
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : null;

// fetch products based on selected category
if ($selected_category) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
  $stmt->execute([$selected_category]);
} else {
  $stmt = $pdo->query("SELECT * FROM products");
}

$products = $stmt->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
  // Authentication check before adding to cart
  if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit;
  }
  $product_id = (int)$_POST['product_id'];
  $user_id = $_SESSION['user_id'];

  // Check if product already in cart
  $stmt = $pdo->prepare("SELECT quantity FROM cartitems WHERE product_id = ? AND user_id = ?");
  $stmt->execute([$product_id, $user_id]);
  $item = $stmt->fetch();

  if ($item) {
    // Increment quantity
    $stmt = $pdo->prepare("UPDATE cartitems SET quantity = quantity + 1 WHERE product_id = ? AND user_id = ?");
    $stmt->execute([$product_id, $user_id]);
  } else {
    // Insert new item
    $stmt = $pdo->prepare("INSERT INTO cartitems (product_id, user_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$product_id, $user_id]);
  }
  $_SESSION['cart_success'] = true;
  // Redirect to avoid form resubmission
  $redirect_url = "products.php" . ($selected_category ? "?category=$selected_category" : "");
  header("Location: $redirect_url");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products — OVERCLOCK/TECH</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
    <style>
    .add-to-cart {
      margin-top: auto;
      padding: 8px;
      border-radius: 8px;
      border: 2px solid var(--neon);
      background: transparent;
      color: var(--neon);
      font-family: var(--font-display);
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      cursor: pointer;
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
    }

    .add-to-cart::before {
      content: '';
      position: absolute;
      inset: 0;
      background: var(--neon);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.3s ease;
      z-index: 0;
    }

    .add-to-cart:hover::before { transform: scaleX(1); }
    .add-to-cart:hover { color: var(--bg-black); box-shadow: var(--neon-glow); }
    .add-to-cart span, .add-to-cart svg { position: relative; z-index: 1; }
  </style>
</head>
<body>
  <?php include 'includes/nav.php'; ?>

  <main class="products-page">

    <?php if (isset($_SESSION['cart_success'])): ?>
      <div class="success-box" style="margin-bottom: 2rem;">
        <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i>
        <span><strong>Success!</strong> Item added to cart successfully.</span>
      </div>
      <?php unset($_SESSION['cart_success']); ?>
    <?php endif; ?>

    <h1>Browse Our Products</h1>
    <!-- Category filter buttons -->
    <div class="category-filters">
      <a href="products.php" class="filter-btn <?= !$selected_category ? 'active' : '' ?>">All</a>
      <?php foreach ($categories as $category): ?>
        <a href="products.php?category=<?= $category['category_id'] ?>" 
           class="filter-btn <?= $selected_category == $category['category_id'] ? 'active' : '' ?>">
          <?= htmlspecialchars($category['name']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Product grid -->
    <div class="product-grid">
      <?php if (empty($products)): ?>
        <p>No products found.</p>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
        <div class="product-card">
          <a href="product.php?id=<?= $product['product_id'] ?>" style="text-decoration: none;">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <p class="price">$<?= number_format($product['price'], 2) ?></p>
          </a>
          <form method="POST" action="products.php<?= $selected_category ? '?category=' . $selected_category : '' ?>">
            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
            <button type="submit" name="add_to_cart" class="btn-secondary" style="width: 100%; border-radius: 8px;"><span>Add to Cart</span></button>
          </form>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </main>

  <?php include 'includes/footer.php'; ?>
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>