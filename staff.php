<?php
session_start();
$activePage = 'staff';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'staff') {
    header('Location: forms/login.php');
    exit();
}

$basePath = '';
$accountRows = [];
$productRows = [];
$errorMsg = '';

require_once 'db.php';

try {
    $accountStmt = $pdo->query(
        "SELECT 'customer' AS account_type, customer_id AS account_id, fname, lname, email, password, created_at
         FROM customers
         UNION ALL
         SELECT 'staff' AS account_type, staff_id AS account_id, fname, lname, email, password, created_at
         FROM staff
         ORDER BY account_type, account_id"
    );
    $accountRows = $accountStmt->fetchAll(PDO::FETCH_ASSOC);

    $productStmt = $pdo->query(
        "SELECT p.product_id, c.name AS category_name, p.name, p.description, p.price, p.stock, p.image
         FROM products p
         LEFT JOIN categories c ON c.category_id = p.category_id
         ORDER BY p.product_id"
    );
    $productRows = $productStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMsg = 'Unable to load staff data right now.';
}

$displayName = trim($_SESSION['username'] ?? 'Staff');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Staff area for OVERCLOCK/TECH.">
  <title>OVERCLOCK/TECH — Staff</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="auth-page staff-page">
  <div class="auth-card staff-card">
    <div class="result-icon result-success" aria-hidden="true">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 6L9 17l-5-5"/>
      </svg>
    </div>
    <h1 class="auth-heading">Welcome Back Staff</h1>
    <p class="auth-subtext">Signed in as <strong><?php echo htmlspecialchars($displayName); ?></strong>.</p>

    <?php if (!empty($errorMsg)): ?>
      <div class="error-box" role="alert"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php else: ?>
      <section class="staff-section" aria-labelledby="accounts-heading">
        <div class="staff-section-head">
          <h2 id="accounts-heading" class="staff-section-title">All Account Info</h2>
          <p class="staff-section-note">Stored passwords are shown as hashes from the database.</p>
        </div>
        <div class="staff-table-wrap">
          <table class="staff-table">
            <thead>
              <tr>
                <th>Type</th>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Password</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($accountRows as $account): ?>
                <tr>
                  <td><span class="staff-badge staff-badge-<?php echo htmlspecialchars($account['account_type']); ?>"><?php echo htmlspecialchars(ucfirst($account['account_type'])); ?></span></td>
                  <td><?php echo (int) $account['account_id']; ?></td>
                  <td><?php echo htmlspecialchars($account['fname'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($account['lname']); ?></td>
                  <td><?php echo htmlspecialchars($account['email']); ?></td>
                  <td class="staff-mono"><?php echo htmlspecialchars($account['password']); ?></td>
                  <td><?php echo htmlspecialchars($account['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="staff-section" aria-labelledby="products-heading">
        <div class="staff-section-head">
          <h2 id="products-heading" class="staff-section-title">All Product Info</h2>
          <p class="staff-section-note">Live product data from the products table and linked category names.</p>
        </div>
        <div class="staff-table-wrap">
          <table class="staff-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Image</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($productRows as $product): ?>
                <tr>
                  <td><?php echo (int) $product['product_id']; ?></td>
                  <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                  <td><?php echo htmlspecialchars($product['name']); ?></td>
                  <td><?php echo htmlspecialchars($product['description'] ?? ''); ?></td>
                  <td>$<?php echo number_format((float) $product['price'], 2); ?></td>
                  <td><?php echo (int) $product['stock']; ?></td>
                  <td class="staff-mono"><?php echo htmlspecialchars($product['image'] ?? ''); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>