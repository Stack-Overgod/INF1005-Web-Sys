<?php
require_once 'db.php';
// PHP logic at the very top, before any HTML
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($query)) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
  $searchTerm = "%" . $query . "%";
  $stmt->execute([$searchTerm, $searchTerm]);
  $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="OVERCLOCK/TECH — Premium gaming hardware. Ready-to-ship gaming PCs, laptops, keyboards, mice and more.">
  <title>OVERCLOCK/TECH — Welcome</title>
  <link rel="stylesheet" href="css/style.css">

</head>
<body>
      <?php include 'includes/nav.php'; ?>

  <main class="products-page">
  <h2>Search results for "<?= htmlspecialchars($query) ?>"</h2>

  <div class="product-grid">
    <?php if (empty($query)): ?>
      <p>Enter a search term above.</p>

    <?php elseif (empty($results)): ?>
      <p>No results found for "<?= htmlspecialchars($query) ?>"</p>

    <?php else: ?>
      <?php foreach ($results as $product): ?>
        <a href="product.php?id=<?= $product['product_id'] ?>" class="product-card-link">
          <div class="product-card">
            <img src="images/<?= htmlspecialchars($product['image']) ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <p class="price">$<?= number_format($product['price'], 2) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
