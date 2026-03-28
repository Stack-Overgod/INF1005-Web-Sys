<?php
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
            <button class="add-to-cart">Add to Cart</button>
          </div>
        </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>