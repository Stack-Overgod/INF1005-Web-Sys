<?php
session_start();
$activePage = 'profile';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: forms/login.php");
    exit();
}

$role = $_SESSION['role'] ?? 'customer';
$userId = $_SESSION['user_id'];
$fname = $lname = $email = "";
$errorMsg = "";
$successMsg = "";

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$roleConfig = [
  'customer' => ['table' => 'customers', 'id_col' => 'customer_id'],
  'staff' => ['table' => 'staff', 'id_col' => 'staff_id'],
];

if (!array_key_exists($role, $roleConfig)) {
  $role = 'customer';
}

// Fetch current user data from DB
try {
    require_once 'db.php';

    $table = $roleConfig[$role]['table'];
    $idCol = $roleConfig[$role]['id_col'];

    $stmt = $pdo->prepare("SELECT fname, lname, email FROM $table WHERE $idCol = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $fname = $user['fname'] ?? '';
        $lname = $user['lname'];
        $email = $user['email'];
    } else {
        $errorMsg = "Account not found.";
    }
} catch (PDOException $e) {
    $errorMsg = "Unable to load your profile. Please try again later.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errorMsg)) {
    $newFname = sanitize_input($_POST['fname'] ?? '');
    $newLname = sanitize_input($_POST['lname'] ?? '');
    $updateError = false;

    // Validate last name
    if (empty($newLname)) {
        $errorMsg = "Last Name is required.";
        $updateError = true;
    } else if (!preg_match("/^[a-zA-Z\s'-]+$/", $newLname)) {
        $errorMsg = "Last name must contain only letters, spaces, hyphens, and apostrophes.";
        $updateError = true;
    }

    // Validate first name if provided
    if (!$updateError && !empty($newFname) && !preg_match("/^[a-zA-Z\s'-]+$/", $newFname)) {
        $errorMsg = "First name must contain only letters, spaces, hyphens, and apostrophes.";
        $updateError = true;
    }

    if (!$updateError) {
        try {
            $updateStmt = $pdo->prepare("UPDATE $table SET fname = :fname, lname = :lname WHERE $idCol = :id");
            $updateStmt->execute([
                ':fname' => $newFname ?: null,
                ':lname' => $newLname,
                ':id' => $userId,
            ]);

            // Update session
            $_SESSION['username'] = trim(($newFname ? $newFname . ' ' : '') . $newLname);

            $fname = $newFname;
            $lname = $newLname;
            $successMsg = "Your profile has been updated.";
        } catch (PDOException $e) {
            $errorMsg = "Failed to update profile. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Manage your OVERCLOCK/TECH account profile.">
  <title>OVERCLOCK/TECH — My Profile</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="auth-page">
  <div class="auth-card profile-card">

    <h1 class="auth-heading">My Profile</h1>
    <p class="auth-subtext">Manage and protect your account</p>

    <?php if (!empty($successMsg)): ?>
      <div class="success-box" role="status"><?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
      <div class="error-box" role="alert"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <form action="profile.php" method="post" novalidate>
      <div class="auth-form-group">
        <label for="fname" class="auth-form-label">First Name <span class="optional">(optional)</span></label>
        <input class="auth-form-input" maxlength="45" type="text" id="fname" name="fname"
          value="<?php echo htmlspecialchars($fname); ?>" placeholder="Enter first name" autocomplete="given-name">
      </div>

      <div class="auth-form-group">
        <label for="lname" class="auth-form-label">Last Name</label>
        <input required class="auth-form-input" maxlength="45" type="text" id="lname" name="lname"
          value="<?php echo htmlspecialchars($lname); ?>" placeholder="Enter last name" autocomplete="family-name">
      </div>

      <div class="auth-form-group">
        <label for="email" class="auth-form-label">Email</label>
        <input disabled class="auth-form-input" type="email" id="email"
          value="<?php echo htmlspecialchars($email); ?>" autocomplete="email">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
      </div>

      <div class="auth-form-group">
        <label for="accountType" class="auth-form-label">Account Type</label>
        <input id="accountType" disabled class="auth-form-input" type="text"
          value="<?php echo ucfirst(htmlspecialchars($role)); ?>">
      </div>

      <button type="submit" class="btn-auth">
        <span>Save Changes</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
        </svg>
      </button>
    </form>

    <div class="profile-links">
      <a href="forms/verify_password.php" class="auth-link profile-link-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        Change Password
      </a>
      <a href="forms/logout.php" class="auth-link profile-link-item profile-link-logout">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Log Out
      </a>
    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>