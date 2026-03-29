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
  <title>Products</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/nav.php'; ?>

  <?php if (isset($_SESSION['cart_success'])): ?>
    <div class="container mt-3">
      <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(0, 255, 150, 0.1); border: 1px solid var(--neon-green); color: var(--neon-green);">
        <strong>Success!</strong> Item added to cart.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
    <?php unset($_SESSION['cart_success']); ?>
  <?php endif; ?>

  <main class="products-page">

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
        <a href="product.php?id=<?= $product['product_id'] ?>" class="product-card-link">
          <div class="product-card">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <p class="price">$<?= number_format($product['price'], 2) ?></p>
            <form method="POST" action="products.php<?= $selected_category ? '?category=' . $selected_category : '' ?>">
              <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
              <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
            </form>
          </div>
        </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </main>

  <?php include 'includes/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>