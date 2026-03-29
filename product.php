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

// fetch reviews
$stmt = $pdo->prepare("
  SELECT r.*, c.fname, c.lname 
  FROM reviews r
  JOIN customers c ON r.customer_id = c.customer_id
  WHERE r.product_id = ?
  ORDER BY r.created_at DESC
");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll();

// calculate average rating
$avg_rating = 0;
if (!empty($reviews)) {
  $avg_rating = array_sum(array_column($reviews, 'rating')) / count($reviews);
}
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

        <!-- Reviews Section -->
        <div class="reviews-section">
          <h2>Customer Reviews 
            <?php if (!empty($reviews)): ?>
              <span class="review-count">(<?= count($reviews) ?>)</span>
              <span class="avg-rating">
                <?= number_format($avg_rating, 1) ?> / 5
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <span class="star <?= $i <= round($avg_rating) ? 'filled' : '' ?>">★</span>
                <?php endfor; ?>
              </span>
            <?php endif; ?>
          </h2>

          <?php if (empty($reviews)): ?>
            <p class="no-reviews">No reviews yet. Be the first to review!</p>
          <?php else: ?>
            <div class="reviews-list">
              <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                  <div class="review-header">
                    <span class="reviewer-name">
                      <?= htmlspecialchars($review['fname'] . ' ' . $review['lname']) ?>
                    </span>
                    <span class="review-date">
                      <?= date('d M Y', strtotime($review['created_at'])) ?>
                    </span>
                  </div>
                  <div class="review-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                    <?php endfor; ?>
                  </div>
                  <p class="review-comment"><?= htmlspecialchars($review['comment']) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>


      </div>
    </div>

  </main>

</body>
</html>