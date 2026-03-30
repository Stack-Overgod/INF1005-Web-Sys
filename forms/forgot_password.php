<?php
session_start();
$activePage = 'login';

$errorMsg = "";
$successMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'customer';
    $newPwd = $_POST['new_pwd'] ?? '';
    $confirmPwd = $_POST['confirm_pwd'] ?? '';
    $valid = true;

    if (!in_array($role, ['customer', 'staff'])) { $role = 'customer'; }

    $email = htmlspecialchars(stripslashes($email));

    if (empty($email)) {
        $errorMsg .= "Email is required.<br>"; $valid = false;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg .= "Invalid email format.<br>"; $valid = false;
    }

    if ($valid && $role === 'staff') {
        $domain = substr(strrchr($email, "@"), 1);
        if (strtolower($domain) !== 'overclocktech.com') {
            $errorMsg .= "Staff accounts use @overclocktech.com email.<br>"; $valid = false;
        }
    }

    if ($valid && $role === 'customer') {
        $domain = substr(strrchr($email, "@"), 1);
        if (strtolower($domain) === 'overclocktech.com') {
            $errorMsg .= "This is a staff email. Please select Staff to reset your password.<br>"; $valid = false;
        }
    }

    if (empty($newPwd)) {
        $errorMsg .= "New password is required.<br>"; $valid = false;
    } else if (empty($confirmPwd)) {
        $errorMsg .= "Please confirm your new password.<br>"; $valid = false;
    } else if ($newPwd !== $confirmPwd) {
        $errorMsg .= "Passwords do not match.<br>"; $valid = false;
    } else if (!preg_match('/[a-z]/', $newPwd) || !preg_match('/[A-Z]/', $newPwd) ||
               !preg_match('/[0-9]/', $newPwd) || strlen($newPwd) < 8) {
        $errorMsg .= "Password does not meet the required strength.<br>"; $valid = false;
    }

    if ($valid) {
        try {
            require_once '../db.php';

            $table = ($role === 'staff') ? 'staff' : 'customers';

            $checkStmt = $pdo->prepare("SELECT 1 FROM $table WHERE email = :email");
            $checkStmt->execute([':email' => $email]);

            if (!$checkStmt->fetch()) {
                $errorMsg = "No account found with this email address.";
            } else {
                $hashedPwd = password_hash($newPwd, PASSWORD_BCRYPT);
                $updateStmt = $pdo->prepare("UPDATE $table SET password = :pwd WHERE email = :email");
                $updateStmt->execute([':pwd' => $hashedPwd, ':email' => $email]);
                $successMsg = "Your password has been reset.";
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
  <meta name="description" content="Reset your OVERCLOCK/TECH account password.">
  <title>OVERCLOCK/TECH — Forgot Password</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php $basePath = '../';
include '../includes/nav.php'; ?>

<main id="main-content" class="auth-page">
  <div class="auth-card">

    <a href="login.php" class="auth-back-link" aria-label="Back to login">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>

    <h1 class="auth-heading">Reset Password</h1>
    <p class="auth-subtext">Enter your email and choose a new password</p>

    <?php if (!empty($successMsg)): ?>
      <div class="success-box" role="status">
        <?php echo htmlspecialchars($successMsg); ?>
        <a href="login.php" class="auth-link" style="display:block; margin-top:0.75rem;">Go to Login</a>
      </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
      <div class="error-box" role="alert"><?php echo $errorMsg; ?></div>
    <?php endif; ?>

    <?php if (empty($successMsg)): ?>
    <div class="role-selector" role="radiogroup" aria-label="Account type">
      <button type="button" class="role-btn active" data-role="customer" aria-pressed="true">Customer</button>
      <button type="button" class="role-btn" data-role="staff" aria-pressed="false">Staff</button>
    </div>

    <form id="forgotForm" action="forgot_password.php" method="post" novalidate>
      <input type="hidden" name="role" id="roleInput" value="customer">

      <div class="auth-form-group">
        <label for="email" class="auth-form-label">Email</label>
        <input required class="auth-form-input" maxlength="100" type="email" id="email" name="email"
          placeholder="Enter your registered email" autocomplete="email" aria-describedby="emailError">
        <span class="field-error" id="emailError" role="alert"></span>
      </div>

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
        <span>Reset Password</span>
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