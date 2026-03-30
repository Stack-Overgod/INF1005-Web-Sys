<?php
session_start();
$activePage = 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Log in to your OVERCLOCK/TECH account.">
  <title>OVERCLOCK/TECH — Login</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php $basePath = '../';
include '../includes/nav.php'; ?>

<main id="main-content" class="auth-page">
  <div class="auth-card">

    <!-- Tab Toggle -->
    <nav class="auth-tabs" aria-label="Account actions">
      <a href="login.php" class="auth-tab active" aria-current="page">Log In</a>
      <a href="register.php" class="auth-tab">Register</a>
    </nav>

    <h1 class="auth-heading">Welcome Back</h1>
    <p class="auth-subtext">
      New here? <a href="register.php" class="auth-link">Create an account</a>
    </p>

    <!-- Role Selector -->
    <div class="role-selector" role="radiogroup" aria-label="Account type">
      <button type="button" class="role-btn active" data-role="customer" aria-pressed="true">Customer</button>
      <button type="button" class="role-btn" data-role="staff" aria-pressed="false">Staff</button>
    </div>

    <form id="loginForm" action="process_login.php" method="post" novalidate>
      <input type="hidden" name="role" id="roleInput" value="customer">

      <div class="auth-form-group">
        <label for="email" class="auth-form-label">Email</label>
        <input required class="auth-form-input" maxlength="100" type="email" id="email" name="email"
          placeholder="Enter your email" autocomplete="email" aria-describedby="emailError">
        <span class="field-error" id="emailError" role="alert"></span>
      </div>

      <div class="auth-form-group">
        <label for="pwd" class="auth-form-label">Password</label>
        <div class="password-wrapper">
          <input required class="auth-form-input" type="password" id="pwd" name="pwd"
            placeholder="Enter your password" autocomplete="current-password" aria-describedby="pwdError">
          <button type="button" class="toggle-pwd" aria-label="Toggle password visibility" data-target="pwd">
            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
              <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </button>
        </div>
        <span class="field-error" id="pwdError" role="alert"></span>
      </div>

      <button type="submit" class="btn-auth">
        <span>Log In</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      </button>

      <p class="auth-forgot-link">
        <a href="forgot_password.php" class="auth-link">Forgot your password?</a>
      </p>
    </form>

  </div>
</main>

<?php include '../includes/footer.php'; ?>

<script src="../js/auth.js?v=5" defer></script>
</body>
</html>
