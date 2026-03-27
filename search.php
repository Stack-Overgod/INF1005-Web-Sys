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
      <?php include 'nav.php'; ?>

  <!-- Then display results inside body -->
  <?php if (empty($query)): ?>
    <p>Enter a search term above.</p>

  <?php elseif (empty($results)): ?>
    <p>No results found for "<?= htmlspecialchars($query) ?>"</p>

  <?php else: ?>
    <?php foreach ($results as $product): ?>
      <div class="product-card">
        <?= htmlspecialchars($product['name']) ?>
      </div>
    <?php endforeach; ?>

  <?php endif; ?>

</body>
</html>
