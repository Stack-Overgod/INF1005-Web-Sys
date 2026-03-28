<?php
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// get product id from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// redirect if no id provided
if (!$product_id) {
  header('Location: products.php');
  exit;
}

// fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// redirect if product not found
if (!$product) {
  header('Location: products.php');
  exit;
}

// fetch product specs
$stmt = $pdo->prepare("SELECT * FROM product_specs WHERE product_id = ?");
$stmt->execute([$product_id]);
$specs = $stmt->fetchAll();

// fetch category name
$stmt = $pdo->prepare("SELECT name FROM categories WHERE category_id = ?");
$stmt->execute([$product['category_id']]);
$category = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['name']) ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/nav.php'; ?>

  <main class="product-page">

    <div class="product-details">

      <!-- Product Image -->
      <div class="product-image">
        <img src="images/<?= htmlspecialchars($product['image']) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>">
      </div>

      <!-- Product Info -->
      <div class="product-info">
        <p class="product-category"><?= htmlspecialchars($category['name']) ?></p>
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
        <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
        <p class="product-stock">In Stock: <?= $product['stock'] ?></p>

        <button class="add-to-cart">Add to Cart</button>

        <!-- Specifications -->
        <?php if (!empty($specs)): ?>
          <div class="product-specs">
            <h2>Specifications</h2>
            <table class="specs-table">
              <?php foreach ($specs as $spec): ?>
                <tr>
                  <td class="spec-name"><?= htmlspecialchars($spec['spec_name']) ?></td>
                  <td class="spec-value"><?= htmlspecialchars($spec['spec_value']) ?></td>
                </tr>
              <?php endforeach; ?>
            </table>
          </div>
        <?php endif; ?>

      </div>
    </div>

  </main>

</body>
</html>