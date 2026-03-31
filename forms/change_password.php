<?php
session_start();
$activePage = 'profile';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['pwd_verified'])) {
    header("Location: verify_password.php");
    exit();
}

$errorMsg = "";
$successMsg = "";
$roleConfig = [
  'customer' => ['table' => 'customers', 'id_col' => 'customer_id'],
  'staff' => ['table' => 'staff', 'id_col' => 'staff_id'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPwd = $_POST['new_pwd'] ?? '';
    $confirmPwd = $_POST['confirm_pwd'] ?? '';

    if (empty($newPwd)) {
        $errorMsg = "New password is required.";
    } else if (empty($confirmPwd)) {
        $errorMsg = "Please confirm your new password.";
    } else if ($newPwd !== $confirmPwd) {
        $errorMsg = "Passwords do not match.";
    } else if (!preg_match('/[a-z]/', $newPwd) || !preg_match('/[A-Z]/', $newPwd) ||
               !preg_match('/[0-9]/', $newPwd) || strlen($newPwd) < 8) {
        $errorMsg = "Password does not meet the required strength.";
    } else {
        try {
            require_once '../db.php';

            $role   = $_SESSION['role'] ?? 'customer';
            $userId = $_SESSION['user_id'];
            if (!array_key_exists($role, $roleConfig)) {
              $role = 'customer';
            }

            $table  = $roleConfig[$role]['table'];
            $idCol  = $roleConfig[$role]['id_col'];

            $hashedPwd = password_hash($newPwd, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE $table SET password = :pwd WHERE $idCol = :id");
            $stmt->execute([':pwd' => $hashedPwd, ':id' => $userId]);

            unset($_SESSION['pwd_verified']);
            $successMsg = "Your password has been changed successfully.";
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
      <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Change Password</title>
    <meta name="description" content="Change your OVERCLOCK/TECH account password.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">  
</head>
<body>

<?php $basePath = '../';
include '../includes/nav.php'; ?>

<main id="main-content" class="auth-page">
  <div class="auth-card">

    <a href="../profile.php" class="auth-back-link" aria-label="Back to profile">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>

    <h1 class="auth-heading">Change Password</h1>
    <p class="auth-subtext">For your account's security, do not share your password with anyone else</p>

    <?php if (!empty($successMsg)): ?>
      <div class="success-box" role="status">
        <?php echo htmlspecialchars($successMsg); ?>
        <a href="../profile.php" class="auth-link" style="display:block; margin-top:0.75rem;">Return to Profile</a>
      </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
      <div class="error-box" role="alert"><?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <?php if (empty($successMsg)): ?>
    <form id="changePasswordForm" action="change_password.php" method="post" novalidate>

      <div class="auth-form-group">
        <label for="new_pwd" class="auth-form-label">New Password</label>
        <div class="password-wrapper">
          <input required class="auth-form-input" type="password" id="new_pwd" name="new_pwd"
            placeholder="Enter new password" autocomplete="new-password" aria-describedby="newPwdError pwdChecklist">
          <button type="button" class="toggle-pwd" aria-label="Toggle password visibility" data-target="new_pwd">
            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        <span class="field-error" id="newPwdError" role="alert"></span>
        <div class="pwd-checklist" id="pwdChecklist" style="display:none;" aria-live="polite">
          <p class="pwd-checklist-title">Password must contain:</p>
          <ul>
            <li id="checkLower" class="pwd-req"><span class="pwd-req-icon" aria-hidden="true"></span> At least one <strong>lowercase</strong> letter</li>
            <li id="checkUpper" class="pwd-req"><span class="pwd-req-icon" aria-hidden="true"></span> At least one <strong>uppercase</strong> letter</li>
            <li id="checkNumber" class="pwd-req"><span class="pwd-req-icon" aria-hidden="true"></span> At least one <strong>number</strong></li>
            <li id="checkLength" class="pwd-req"><span class="pwd-req-icon" aria-hidden="true"></span> Minimum <strong>8 characters</strong></li>
          </ul>
        </div>
      </div>

      <div class="auth-form-group">
        <label for="confirm_pwd" class="auth-form-label">Confirm Password</label>
        <div class="password-wrapper">
          <input required class="auth-form-input" type="password" id="confirm_pwd" name="confirm_pwd"
            placeholder="Confirm new password" autocomplete="new-password" aria-describedby="confirmPwdError">
          <button type="button" class="toggle-pwd" aria-label="Toggle confirm password visibility" data-target="confirm_pwd">
            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        <span class="field-error" id="confirmPwdError" role="alert"></span>
      </div>

      <button type="submit" class="btn-auth">
        <span>Confirm</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </form>
    <?php endif; ?>

  </div>
</main>

<?php include '../includes/footer.php'; ?>
<script src="../js/auth.js?v=5" defer></script>
</body>
</html>