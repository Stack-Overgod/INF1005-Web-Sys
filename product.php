<?php
session_start();
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

$can_review = false;
$already_reviewed = false;

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
  $customer_id = $_SESSION['user_id'];

  // check if customer purchased this product
  $stmt = $pdo->prepare("
    SELECT COUNT(*) FROM order_items oi
    JOIN order_info o ON oi.order_id = o.order_id
    WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered'
  ");
  $stmt->execute([$customer_id, $product_id]);
  $can_review = $stmt->fetchColumn() > 0;

  // check if already reviewed
  $stmt = $pdo->prepare("
    SELECT COUNT(*) FROM reviews 
    WHERE customer_id = ? AND product_id = ?
  ");
  $stmt->execute([$customer_id, $product_id]);
  $already_reviewed = $stmt->fetchColumn() > 0;
}


// handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
  $rating = (int)$_POST['rating'];
  $comment = trim($_POST['comment']);
  $customer_id = $_SESSION['user_id'];

  if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
    $stmt = $pdo->prepare("
      INSERT INTO reviews (product_id, customer_id, rating, comment)
      VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$product_id, $customer_id, $rating, htmlspecialchars($comment)]);
    header("Location: product.php?id=$product_id");
    exit;
  }
}

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

        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if ($can_review && !$already_reviewed): ?>
            <div class="review-form">
              <h3>Leave a Review</h3>
              <form action="product.php?id=<?= $product_id ?>" method="POST">
                
                <!-- Star Rating -->
                <div class="rating-input">
                  <label>Rating</label>
                  <div class="star-select">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                      <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
                      <label for="star<?= $i ?>">★</label>
                    <?php endfor; ?>
                  </div>
                </div>

                <!-- Comment -->
                <div class="review-input">
                  <label for="comment">Comment</label>
                  <textarea id="comment" name="comment" rows="4" 
                    placeholder="Share your experience..." required
                    maxlength="500"></textarea>
                </div>

                <input type="hidden" name="submit_review" value="1">
                <button type="submit" class="btn-submit-review">Submit Review</button>
              </form>
            </div>
          <?php elseif ($already_reviewed): ?>
            <p class="text-grey">You have already reviewed this product.</p>
          <?php else: ?>
            <p class="text-grey">Only customers who have purchased this product can leave a review.</p>
          <?php endif; ?>
        <?php else: ?>
          <p class="text-grey">Please <a href="<?= $basePath ?>forms/login.php">log in</a> to leave a review.</p>
        <?php endif; ?>


      </div>
    </div>

  </main>

</body>
</html>