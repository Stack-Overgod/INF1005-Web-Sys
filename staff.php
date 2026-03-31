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
$categoryRows = [];
$errorMsg = '';
$successMsg = '';
$categorySpecMap = [
  '1' => ['CPU', 'RAM', 'GPU', 'Storage'],
  '2' => ['CPU', 'RAM', 'GPU', 'Display'],
  '3' => ['Switch Type', 'Polling Rate', 'Backlight'],
  '4' => ['DPI', 'Polling Rate', 'Buttons'],
];
$accountFormData = [
  'account_type' => 'customer',
  'fname' => '',
  'lname' => '',
  'email' => '',
];
$productFormData = [
  'category_id' => '',
  'name' => '',
  'description' => '',
  'spec_values' => [],
  'price' => '',
  'stock' => '',
  'image' => '',
];

require_once 'db.php';

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'create_account') {
      $accountType = $_POST['account_type'] ?? 'customer';
      $fname = trim($_POST['fname'] ?? '');
      $lname = trim($_POST['lname'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $password = $_POST['password'] ?? '';

      $accountFormData = [
        'account_type' => $accountType,
        'fname' => $fname,
        'lname' => $lname,
        'email' => $email,
      ];

      if (!in_array($accountType, ['customer', 'staff'], true)) {
        $errorMsg = 'Invalid account type.';
      } elseif ($lname === '') {
        $errorMsg = 'Last name is required for new accounts.';
      } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $lname)) {
        $errorMsg = 'Last name contains invalid characters.';
      } elseif ($fname !== '' && !preg_match("/^[a-zA-Z\s'-]+$/", $fname)) {
        $errorMsg = 'First name contains invalid characters.';
      } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'A valid email is required for new accounts.';
      } elseif ($password === '') {
        $errorMsg = 'Password is required for new accounts.';
      } elseif (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || strlen($password) < 8) {
        $errorMsg = 'New account password must include uppercase, lowercase, number, and at least 8 characters.';
      } else {
        $checkStmt = $pdo->prepare(
          "SELECT 'customers' AS source FROM customers WHERE email = :email
           UNION ALL
           SELECT 'staff' AS source FROM staff WHERE email = :email
           LIMIT 1"
        );
        $checkStmt->execute([':email' => $email]);

        if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
          $errorMsg = 'An account with this email already exists.';
        } else {
          $table = $accountType === 'staff' ? 'staff' : 'customers';
          $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
          $insertAccountStmt = $pdo->prepare(
            "INSERT INTO $table (fname, lname, email, password) VALUES (:fname, :lname, :email, :password)"
          );
          $insertAccountStmt->execute([
            ':fname' => $fname !== '' ? $fname : null,
            ':lname' => $lname,
            ':email' => $email,
            ':password' => $hashedPassword,
          ]);
          $successMsg = ucfirst($accountType) . ' account created successfully.';
          $accountFormData = [
            'account_type' => 'customer',
            'fname' => '',
            'lname' => '',
            'email' => '',
          ];
        }
      }
    } elseif ($formAction === 'create_product') {
      $categoryId = (int) ($_POST['category_id'] ?? 0);
      $name = trim($_POST['name'] ?? '');
      $description = trim($_POST['description'] ?? '');
      $specNames = $_POST['spec_name'] ?? [];
      $specValues = $_POST['spec_value'] ?? [];
      $price = trim($_POST['price'] ?? '');
      $stock = trim($_POST['stock'] ?? '');
      $uploadedImage = $_FILES['image_file'] ?? null;
      $parsedSpecs = [];
      $currentSpecValues = [];

      if (is_array($specNames) && is_array($specValues)) {
        foreach ($specNames as $index => $specName) {
          $specName = trim((string) $specName);
          $specValue = trim((string) ($specValues[$index] ?? ''));
          if ($specName !== '') {
            $currentSpecValues[$specName] = $specValue;
          }
        }
      }

      $productFormData = [
        'category_id' => $categoryId > 0 ? (string) $categoryId : '',
        'name' => $name,
        'description' => $description,
        'spec_values' => $currentSpecValues,
        'price' => $price,
        'stock' => $stock,
        'image' => '',
      ];

      if ($categoryId <= 0) {
        $errorMsg = 'Category is required for new products.';
      } elseif ($name === '') {
        $errorMsg = 'Product name is required.';
      } elseif ($price === '' || !is_numeric($price) || (float) $price < 0) {
        $errorMsg = 'Price must be a valid non-negative number.';
      } elseif ($stock === '' || filter_var($stock, FILTER_VALIDATE_INT) === false || (int) $stock < 0) {
        $errorMsg = 'Stock must be a valid non-negative integer.';
      } elseif ($uploadedImage === null || ($uploadedImage['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        $errorMsg = 'Product image is required.';
      } elseif (($uploadedImage['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $errorMsg = 'Image upload failed. Please try again.';
      } else {
        $categoryKey = (string) $categoryId;
        if (!array_key_exists($categoryKey, $categorySpecMap)) {
          $errorMsg = 'No fixed spec template is configured for this category.';
        } else {
          foreach ($categorySpecMap[$categoryKey] as $requiredSpecName) {
            $requiredSpecValue = trim((string) ($currentSpecValues[$requiredSpecName] ?? ''));
            if ($requiredSpecValue === '') {
              $errorMsg = $requiredSpecName . ' is required for this category.';
              break;
            }

            $parsedSpecs[] = [
              'name' => $requiredSpecName,
              'value' => $requiredSpecValue,
            ];
          }
        }
      }

      if ($errorMsg === '') {
        $categoryStmt = $pdo->prepare('SELECT 1 FROM categories WHERE category_id = :category_id');
        $categoryStmt->execute([':category_id' => $categoryId]);

        if (!$categoryStmt->fetchColumn()) {
          $errorMsg = 'Selected category does not exist.';
        } else {
          $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
          $originalName = $uploadedImage['name'] ?? '';
          $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

          if (!in_array($extension, $allowedExtensions, true)) {
            $errorMsg = 'Only JPG, JPEG, PNG, GIF, or WEBP images are allowed.';
          } else {
            $safeBaseName = preg_replace('/[^a-zA-Z0-9_-]+/', '-', strtolower($name));
            $safeBaseName = trim($safeBaseName, '-');
            if ($safeBaseName === '') {
              $safeBaseName = 'product';
            }

            $imageFileName = $safeBaseName . '-' . uniqid('', true) . '.' . $extension;
            $imageDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'images';
            $imagePath = $imageDirectory . DIRECTORY_SEPARATOR . $imageFileName;

            if (!is_dir($imageDirectory)) {
              $errorMsg = 'Images directory is missing.';
            } elseif (!move_uploaded_file($uploadedImage['tmp_name'], $imagePath)) {
              $errorMsg = 'Unable to save the uploaded image.';
            } else {
              try {
                $pdo->beginTransaction();

                $insertProductStmt = $pdo->prepare(
                  'INSERT INTO products (category_id, name, description, price, stock, image) VALUES (:category_id, :name, :description, :price, :stock, :image)'
                );
                $insertProductStmt->execute([
                  ':category_id' => $categoryId,
                  ':name' => $name,
                  ':description' => $description !== '' ? $description : null,
                  ':price' => number_format((float) $price, 2, '.', ''),
                  ':stock' => (int) $stock,
                  ':image' => $imageFileName,
                ]);

                $productId = (int) $pdo->lastInsertId();
                $insertSpecStmt = $pdo->prepare(
                  'INSERT INTO product_specs (product_id, spec_name, spec_value) VALUES (:product_id, :spec_name, :spec_value)'
                );

                foreach ($parsedSpecs as $spec) {
                  $insertSpecStmt->execute([
                    ':product_id' => $productId,
                    ':spec_name' => $spec['name'],
                    ':spec_value' => $spec['value'],
                  ]);
                }

                $pdo->commit();
                $successMsg = 'Product created successfully.';
                $productFormData = [
                  'category_id' => '',
                  'name' => '',
                  'description' => '',
                  'spec_values' => [],
                  'price' => '',
                  'stock' => '',
                  'image' => '',
                ];
              } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                  $pdo->rollBack();
                }
                if (file_exists($imagePath)) {
                  unlink($imagePath);
                }
                throw $e;
              }
            }
          }
        }
      }
    }
  }

  $categoryStmt = $pdo->query('SELECT category_id, name FROM categories ORDER BY name');
  $categoryRows = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

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
$categorySpecMapJson = json_encode($categorySpecMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
$currentProductSpecValuesJson = json_encode($productFormData['spec_values'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Staff</title>
    <meta name="description" content="Staff page for OVERCLOCK/TECH.">    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Bootstrap 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">  
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="page-wrapper staff-page">
  <div class="page-container page-container-wide">
    <div class="auth-card staff-card">

    <div class="result-icon result-success" aria-hidden="true">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 6L9 17l-5-5"/>
      </svg>
    </div>
    <h1 class="auth-heading">Welcome Back Staff</h1>
    <p class="auth-subtext">Signed in as <strong><?php echo htmlspecialchars($displayName); ?></strong>.</p>

    <?php if (!empty($successMsg)): ?>
      <div class="success-box" role="status"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
      <div class="error-box" role="alert"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

      <section class="staff-section" aria-labelledby="create-tools-heading">
        <div class="staff-section-head">
          <h2 id="create-tools-heading" class="staff-section-title">Create New Records</h2>
          <p class="staff-section-note">Staff can add new accounts and products from here.</p>
        </div>
        <div class="staff-form-grid">
          <form action="staff.php" method="post" class="staff-create-card">
            <input type="hidden" name="form_action" value="create_account">
            <h3 class="staff-create-title">New Account</h3>

            <div class="auth-form-group">
              <label for="account_type" class="auth-form-label">Account Type</label>
              <select id="account_type" name="account_type" class="auth-form-input staff-select">
                <option value="customer"<?php echo $accountFormData['account_type'] === 'customer' ? ' selected' : ''; ?>>Customer</option>
                <option value="staff"<?php echo $accountFormData['account_type'] === 'staff' ? ' selected' : ''; ?>>Staff</option>
              </select>
            </div>

            <div class="auth-form-row">
              <div class="auth-form-group">
                <label for="staff-fname" class="auth-form-label">First Name</label>
                <input class="auth-form-input" type="text" id="staff-fname" name="fname" maxlength="45" placeholder="Enter first name" value="<?php echo htmlspecialchars($accountFormData['fname']); ?>">
              </div>
              <div class="auth-form-group">
                <label for="staff-lname" class="auth-form-label">Last Name</label>
                <input class="auth-form-input" type="text" id="staff-lname" name="lname" maxlength="45" placeholder="Enter last name" value="<?php echo htmlspecialchars($accountFormData['lname']); ?>" required>
              </div>
            </div>

            <div class="auth-form-group">
              <label for="staff-email" class="auth-form-label">Email</label>
              <input class="auth-form-input" type="email" id="staff-email" name="email" maxlength="100" placeholder="Enter email" value="<?php echo htmlspecialchars($accountFormData['email']); ?>" required>
            </div>

            <div class="auth-form-group">
              <label for="staff-password" class="auth-form-label">Password</label>
              <input class="auth-form-input" type="password" id="staff-password" name="password" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn-auth">
              <span>Create Account</span>
            </button>
          </form>

          <form action="staff.php" method="post" enctype="multipart/form-data" class="staff-create-card">
            <input type="hidden" name="form_action" value="create_product">
            <h3 class="staff-create-title">New Product</h3>

            <div class="auth-form-group">
              <label for="category_id" class="auth-form-label">Category</label>
              <select id="category_id" name="category_id" class="auth-form-input staff-select" required>
                <option value="">Select category</option>
                <?php foreach ($categoryRows as $category): ?>
                  <option value="<?php echo (int) $category['category_id']; ?>"<?php echo $productFormData['category_id'] === (string) $category['category_id'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="auth-form-group">
              <label for="product-name" class="auth-form-label">Name</label>
              <input class="auth-form-input" type="text" id="product-name" name="name" maxlength="100" placeholder="Enter product name" value="<?php echo htmlspecialchars($productFormData['name']); ?>" required>
            </div>

            <div class="auth-form-group">
              <label for="product-description" class="auth-form-label">Description</label>
              <textarea class="auth-form-input staff-textarea" id="product-description" name="description" placeholder="Enter product description"><?php echo htmlspecialchars($productFormData['description']); ?></textarea>
            </div>

            <div class="auth-form-group">
              <label class="auth-form-label">Product Specs</label>
              <div id="product-spec-fields" class="staff-spec-list"></div>
              <p class="staff-field-note">Specs are fixed automatically based on the selected category.</p>
            </div>

            <div class="auth-form-row">
              <div class="auth-form-group">
                <label for="product-price" class="auth-form-label">Price</label>
                <input class="auth-form-input" type="number" id="product-price" name="price" min="0" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars($productFormData['price']); ?>" required>
              </div>
              <div class="auth-form-group">
                <label for="product-stock" class="auth-form-label">Stock</label>
                <input class="auth-form-input" type="number" id="product-stock" name="stock" min="0" step="1" placeholder="0" value="<?php echo htmlspecialchars($productFormData['stock']); ?>" required>
              </div>
            </div>

            <div class="auth-form-group">
              <label for="product-image-file" class="auth-form-label">Product Image</label>
              <input class="auth-form-input" type="file" id="product-image-file" name="image_file" accept=".jpg,.jpeg,.png,.gif,.webp" required>
            </div>

            <button type="submit" class="btn-auth">
              <span>Create Product</span>
            </button>
          </form>
        </div>
      </section>

      <section class="staff-section" aria-labelledby="accounts-heading">
        <div class="staff-section-head">
          <h2 id="accounts-heading" class="staff-section-title">All Account Info</h2>
        </div>
        <div class="staff-table-wrap">
          <table class="staff-table">
            <thead>
              <tr>
                <th>Type</th>
                <th class="col-id">ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th class="col-date">Created</th>
                <th class="col-actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($accountRows as $account): ?>
                <tr>
                  <td><span class="staff-badge staff-badge-<?php echo htmlspecialchars($account['account_type']); ?>"><?php echo htmlspecialchars(ucfirst($account['account_type'])); ?></span></td>
                  <td class="col-id"><?php echo (int) $account['account_id']; ?></td>
                  <td><?php echo htmlspecialchars($account['fname'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($account['lname']); ?></td>
                  <td><?php echo htmlspecialchars($account['email']); ?></td>
                  <td class="col-date"><?php echo htmlspecialchars($account['created_at']); ?></td>
                  <td class="col-actions">
                    <?php if ($account['account_type'] === 'customer'): ?>
                      <a href="orders.php?client_id=<?php echo (int)$account['account_id']; ?>" class="btn btn-primary btn-sm"> <i class="fa fa-shopping-basket" aria-hidden="true"></i> Orders</a>
                    <?php endif; ?>
                  </td>
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
                <th class="col-id">ID</th>
                <th>Category</th>
                <th>Name</th>
                <th class="col-desc">Description</th>
                <th class="col-price">Price</th>
                <th class="col-stock">Stock</th>
                <th>Image</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($productRows as $product): ?>
                <tr>
                  <td class="col-id"><?php echo (int) $product['product_id']; ?></td>
                  <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                  <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                  <td class="col-desc" title="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"><?php echo htmlspecialchars($product['description'] ?? ''); ?></td>
                  <td class="col-price">$<?php echo number_format((float) $product['price'], 2); ?></td>
                  <td class="col-stock"><?php echo (int) $product['stock']; ?></td>
                  <td class="staff-mono"><?php echo htmlspecialchars($product['image'] ?? ''); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>

          </table>
        </div>
      </section>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
  (function () {
    var categorySpecMap = <?php echo $categorySpecMapJson ?: '{}'; ?>;
    var currentSpecValues = <?php echo $currentProductSpecValuesJson ?: '{}'; ?>;
    var categorySelect = document.getElementById('category_id');
    var specFieldsContainer = document.getElementById('product-spec-fields');

    if (!categorySelect || !specFieldsContainer) {
      return;
    }

    function renderSpecFields() {
      var categoryId = categorySelect.value;
      var specNames = categorySpecMap[categoryId] || [];
      specFieldsContainer.innerHTML = '';

      if (!specNames.length) {
        specFieldsContainer.innerHTML = '<p class="staff-field-note">Select a category to show its required specs.</p>';
        return;
      }

      specNames.forEach(function (specName) {
        var wrapper = document.createElement('div');
        wrapper.className = 'auth-form-group';

        var label = document.createElement('label');
        label.className = 'auth-form-label';
        label.textContent = specName;

        var hiddenName = document.createElement('input');
        hiddenName.type = 'hidden';
        hiddenName.name = 'spec_name[]';
        hiddenName.value = specName;

        var input = document.createElement('input');
        input.className = 'auth-form-input';
        input.type = 'text';
        input.name = 'spec_value[]';
        input.placeholder = 'Enter ' + specName;
        input.value = currentSpecValues[specName] || '';
        input.required = true;

        wrapper.appendChild(label);
        wrapper.appendChild(hiddenName);
        wrapper.appendChild(input);
        specFieldsContainer.appendChild(wrapper);
      });
    }

    categorySelect.addEventListener('change', function () {
      currentSpecValues = {};
      renderSpecFields();
    });

    renderSpecFields();
  })();
</script>

</body>
</html>
