<?php
session_start();
$activePage = 'register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Create a new OVERCLOCK/TECH account.">
  <title>OVERCLOCK/TECH — Register</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php $basePath = '../';
include '../includes/nav.php'; ?>

<main id="main-content" class="auth-page">
  <div class="auth-card">

    <div class="auth-tabs" role="tablist" aria-label="Account actions">
      <a href="login.php" class="auth-tab" role="tab" aria-selected="false">Log In</a>
      <a href="register.php" class="auth-tab active" role="tab" aria-selected="true">Register</a>
    </div>

    <h1 class="auth-heading">Create Account</h1>
    <p class="auth-subtext">
      Already have an account? <a href="login.php" class="auth-link">Sign in</a>
    </p>

    <form id="registerForm" action="process_register.php" method="post" novalidate>
      <input type="hidden" name="role" id="roleInput" value="customer">

      <div class="auth-form-row">
        <div class="auth-form-group">
          <label for="fname" class="auth-form-label">First Name <span class="optional">(optional)</span></label>
          <input class="auth-form-input" maxlength="45" type="text" id="fname" name="fname"
            placeholder="Enter first name" autocomplete="given-name">
        </div>
        <div class="auth-form-group">
          <label for="lname" class="auth-form-label">Last Name</label>
          <input required class="auth-form-input" maxlength="45" type="text" id="lname" name="lname"
            placeholder="Enter last name" autocomplete="family-name" aria-describedby="lnameError">
          <span class="field-error" id="lnameError" role="alert"></span>
        </div>
      </div>

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
            placeholder="Enter password" autocomplete="new-password" aria-describedby="pwdError pwdChecklist">
          <button type="button" class="toggle-pwd" aria-label="Toggle password visibility" data-target="pwd">
            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        <span class="field-error" id="pwdError" role="alert"></span>
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
        <label for="pwd_confirm" class="auth-form-label">Confirm Password</label>
        <div class="password-wrapper">
          <input required class="auth-form-input" type="password" id="pwd_confirm" name="pwd_confirm"
            placeholder="Confirm password" autocomplete="new-password" aria-describedby="pwdConfirmError">
          <button type="button" class="toggle-pwd" aria-label="Toggle confirm password visibility" data-target="pwd_confirm">
            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
        <span class="field-error" id="pwdConfirmError" role="alert"></span>
      </div>

      <div class="auth-form-group auth-check-group">
        <label class="auth-check-label">
          <input class="auth-check-input" type="checkbox" name="agree" id="agree" aria-describedby="agreeError">
          <span class="checkmark"></span>
          I agree to the Terms &amp; Conditions</a>
        </label>
        <span class="field-error" id="agreeError" role="alert"></span>
      </div>

      <button type="submit" class="btn-auth">
        <span>Register</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </form>

  </div>
</main>

<?php include '../includes/footer.php'; ?>
<script src="../js/auth.js?v=5" defer></script>
</body>
</html>
