<?php
session_start();
$activePage = 'profile';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errorMsg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPwd = $_POST['current_pwd'] ?? '';

    if (empty($currentPwd)) {
        $errorMsg = "Please enter your current password.";
    } else {
        try {
            $host = 'localhost';
            $dbname = 'overclock_tech';
            $dbuser = 'root';
            $dbpass = ''; // update this to match your MySQL password

            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $role = $_SESSION['role'] ?? 'customer';
            $userId = $_SESSION['user_id'];
            $table = ($role === 'staff') ? 'staff' : 'customers';
            $idCol = ($role === 'staff') ? 'staff_id' : 'customer_id';

            $stmt = $pdo->prepare("SELECT password FROM $table WHERE $idCol = :id");
            $stmt->execute([':id' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($currentPwd, $row['password'])) {
                // Password verified — users can change their password
                $_SESSION['pwd_verified'] = true;
                header("Location: change_password.php");
                exit();
            } else {
                $errorMsg = "Incorrect password. Please try again.";
            }
        } catch (PDOException $e) {
            $errorMsg = "A system error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Verify your identity before changing your password.">
  <title>OVERCLOCK/TECH — Verify Password</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php $basePath = '../';
include '../includes/nav.php'; ?>

<main id="main-content" class="auth-page">
  <div class="auth-card">

    <a href="../profile.php" class="auth-back-link" aria-label="Back to profile">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
    </a>

    <h1 class="auth-heading">Verify Your Identity</h1>
    <p class="auth-subtext">Enter your current password to continue</p>

    <?php if (!empty($errorMsg)): ?>
      <div class="error-box" role="alert"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <form action="verify_password.php" method="post" novalidate>
      <div class="auth-form-group">
        <label for="current_pwd" class="auth-form-label">Current Password</label>
        <div class="password-wrapper">
          <input required class="auth-form-input" type="password" id="current_pwd" name="current_pwd"
            placeholder="Enter your current password" autocomplete="current-password">
          <button type="button" class="toggle-pwd" aria-label="Toggle password visibility" data-target="current_pwd">
            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
              <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-auth">
        <span>Confirm</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      </button>
    </form>

  </div>
</main>

<?php include '../includes/footer.php'; ?>

<script src="../js/auth.js?v=5" defer></script>
</body>
</html>