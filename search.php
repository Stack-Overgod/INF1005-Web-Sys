


<?php
require_once 'db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : null;
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;

// fetch categories for filter
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// build query dynamically
$sql = "SELECT * FROM products WHERE name LIKE ?";
$params = ["%" . $query . "%"];

if ($selected_category) {
  $sql .= " AND category_id = ?";
  $params[] = $selected_category;
}

if ($min_price !== null) {
  $sql .= " AND price >= ?";
  $params[] = $min_price;
}

if ($max_price !== null) {
  $sql .= " AND price <= ?";
  $params[] = $max_price;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Results</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/nav.php'; ?>

  <main class="search-page">

    <!-- Sidebar -->
    <aside class="filter-sidebar">
      <form action="search.php" method="GET">
        <!-- preserve search query -->
        <input type="hidden" name="q" value="<?= htmlspecialchars($query) ?>">

        <h3>Filters</h3>

        <!-- Category filter -->
        <div class="filter-section">
          <h4>Category</h4>
          <?php foreach ($categories as $category): ?>
            <label>
              <input type="radio" name="category" value="<?= $category['category_id'] ?>"
                <?= $selected_category == $category['category_id'] ? 'checked' : '' ?>>
              <?= htmlspecialchars($category['name']) ?>
            </label>
          <?php endforeach; ?>
          <label>
            <input type="radio" name="category" value="" <?= !$selected_category ? 'checked' : '' ?>>
            All
          </label>
        </div>

        <!-- Price filter -->
        <div class="filter-section">
          <h4>Price</h4>
          <input type="number" name="min_price" placeholder="Min" value="<?= $min_price ?>">
          <input type="number" name="max_price" placeholder="Max" value="<?= $max_price ?>">
        </div>

        <button type="submit">Apply Filters</button>
        <a href="search.php?q=<?= htmlspecialchars($query) ?>">Clear Filters</a>

      </form>
    </aside>

    <!-- Results -->
    <div class="search-results">
      <h2>Search results for "<?= htmlspecialchars($query) ?>"</h2>

      <div class="product-grid">
        <?php if (empty($query)): ?>
          <p>Enter a search term above.</p>
        <?php elseif (empty($results)): ?>
          <p>No results found.</p>
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
    </div>

  </main>

</body>
</html>