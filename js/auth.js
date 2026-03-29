/**
 * Client-side form validation & interactivity.
 */

document.addEventListener('DOMContentLoaded', function () {

  // Validate provided email domain against widely used email domains for misspellings

  var VALID_DOMAINS = [
    'gmail.com', 'yahoo.com', 'yahoo.com.sg', 'yahoo.co.uk',
    'hotmail.com', 'outlook.com', 'live.com', 'msn.com',
    'icloud.com', 'me.com', 'mac.com',
    'aol.com', 'protonmail.com', 'proton.me',
    'zoho.com', 'ymail.com', 'mail.com',
    'gmx.com', 'gmx.net',
    'singnet.com.sg', 'starhub.net.sg', 'myrepublic.net',
    'ntu.edu.sg', 'nus.edu.sg', 'sit.singaporetech.edu.sg',
    'overclocktech.com'
  ];

  function levenshtein(a, b) {
    var matrix = [];
    for (var i = 0; i <= b.length; i++) { 
      matrix[i] = [i]; 
    }
    for (var j = 0; j <= a.length; j++) { 
      matrix[0][j] = j; 
    }
    for (var i = 1; i <= b.length; i++) {
      for (var j = 1; j <= a.length; j++) {
        if (b.charAt(i - 1) === a.charAt(j - 1)) {
          matrix[i][j] = matrix[i - 1][j - 1];
        } else {
          matrix[i][j] = Math.min(
            matrix[i - 1][j - 1] + 1,
            matrix[i][j - 1] + 1,
            matrix[i - 1][j] + 1
          );
        }
      }
    }
    return matrix[b.length][a.length];
  }

  function checkEmailDomain(email) {
    var parts = email.split('@');
    if (parts.length !== 2) return { 
      valid: false, suggestion: '' 
    };
    var domain = parts[1].toLowerCase();
    if (VALID_DOMAINS.indexOf(domain) !== -1) {
      return { valid: true };
    }
    var closest = null;
    var closestDist = 999;
    for (var i = 0; i < VALID_DOMAINS.length; i++) {
      var dist = levenshtein(domain, VALID_DOMAINS[i]);
      if (dist < closestDist) { 
        closestDist = dist; closest = VALID_DOMAINS[i]; 
      }
    }
    if (closestDist <= 2 && closest !== domain) {
      return { valid: false, suggestion: 'Did you mean ' + parts[0] + '@' + closest + '?' };
    }
    // Domain completely not recognised
    return { valid: false, suggestion: 'Please use a valid email domain (e.g. gmail.com, yahoo.com, outlook.com).' };
  }

  // Password Validation Checklist for Registration Form, Change Password Form, and Forget Password Form

  function isStrongPassword(pwd) {
    return /[a-z]/.test(pwd) && /[A-Z]/.test(pwd) && /[0-9]/.test(pwd) && pwd.length >= 8;
  }

  var pwdField = document.getElementById('pwd') || document.getElementById('new_pwd');
  var checklist = document.getElementById('pwdChecklist');

  if (pwdField && checklist) {
    var checkLower = document.getElementById('checkLower');
    var checkUpper = document.getElementById('checkUpper');
    var checkNumber = document.getElementById('checkNumber');
    var checkLength = document.getElementById('checkLength');

    // Dynamic Password Validation Checklist
    pwdField.addEventListener('focus', function () {
      checklist.style.display = 'block';
    });

    // Update each requirement in real-time
    pwdField.addEventListener('input', function () {
      var val = pwdField.value;
      setReqStatus(checkLower, /[a-z]/.test(val));
      setReqStatus(checkUpper, /[A-Z]/.test(val));
      setReqStatus(checkNumber, /[0-9]/.test(val));
      setReqStatus(checkLength, val.length >= 8);
    });
  }

  function setReqStatus(el, met) {
    if (!el) {
      return;
    }
    if (met) {
      el.classList.add('pwd-req-met');
      el.classList.remove('pwd-req-fail');
    } else {
      el.classList.remove('pwd-req-met');
      el.classList.add('pwd-req-fail');
    }
  }

  // Role Selector Toggle

  var roleBtns = document.querySelectorAll('.role-btn');
  var roleInput = document.getElementById('roleInput');
  var emailInput = document.getElementById('email');

  if (roleBtns.length && roleInput) {
    roleBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        roleBtns.forEach(function (b) {
          b.classList.remove('active');
          b.setAttribute('aria-pressed', 'false');
        });
        btn.classList.add('active');
        btn.setAttribute('aria-pressed', 'true');
        roleInput.value = btn.getAttribute('data-role');
        if (emailInput) {
          var selectedRole = btn.getAttribute('data-role');
          var emailPlaceholder = 'Enter your email';

          if (selectedRole === 'staff') {
            emailPlaceholder = 'yourname@overclocktech.com';
          }

          emailInput.setAttribute('placeholder', emailPlaceholder);
        }
        clearFieldError(emailInput);
      });
    });
  }

  // Password Visibility Toggle

  document.querySelectorAll('.toggle-pwd').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.getAttribute('data-target'));
      if (!input) return;
      var eyeOpen = btn.querySelector('.eye-open');
      var eyeClosed = btn.querySelector('.eye-closed');
      if (input.type === 'password') {
        input.type = 'text';
        if (eyeOpen) {
          eyeOpen.style.display = 'none';
        }
        if (eyeClosed) {
          eyeClosed.style.display = 'block';
        }
      } else {
        input.type = 'password';
        if (eyeOpen) {
          eyeOpen.style.display = 'block';
        }
        if (eyeClosed) {
          eyeClosed.style.display = 'none';
        }
      }
    });
  });

  // Login Form for Customers and Staffs

  var loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      var valid = true;
      var email = document.getElementById('email');
      var emailError = document.getElementById('emailError');
      if (!email.value.trim()) {
        showFieldError(email, emailError, 'Email is required.'); valid = false;
      } else if (!isValidEmail(email.value.trim())) {
        showFieldError(email, emailError, 'Please enter a valid email address.'); valid = false;
      } else if (roleInput && roleInput.value === 'staff' && !isOverclockEmail(email.value.trim())) {
        showFieldError(email, emailError, 'Staff must use an @overclocktech.com email.'); valid = false;
      } else if (roleInput && roleInput.value === 'customer' && isOverclockEmail(email.value.trim())) {
        showFieldError(email, emailError, 'This is a staff email. Please select Staff to log in.'); valid = false;
      } else { 
        clearFieldError(email, emailError); 
      }

      var pwd = document.getElementById('pwd');
      var pwdError = document.getElementById('pwdError');
      if (!pwd.value) {
        showFieldError(pwd, pwdError, 'Password is required.'); valid = false;
      } else { 
        clearFieldError(pwd, pwdError); 
      }

      if (!valid) {
        e.preventDefault();
      }
    });
  }

  // Registration Form

  var registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
      var valid = true;

      var lname = document.getElementById('lname');
      var lnameError = document.getElementById('lnameError');
      if (!lname.value.trim()) {
        showFieldError(lname, lnameError, 'Last Name is required.'); valid = false;
      } else if (!/^[a-zA-Z\s'\-]+$/.test(lname.value.trim())) {
        showFieldError(lname, lnameError, 'Only letters, spaces, hyphens, and apostrophes allowed.'); valid = false;
      } else { clearFieldError(lname, lnameError); }

      var email = document.getElementById('email');
      var emailError = document.getElementById('emailError');
      if (!email.value.trim()) {
        showFieldError(email, emailError, 'Email is required.'); valid = false;
      } else if (!isValidEmail(email.value.trim())) {
        showFieldError(email, emailError, 'Please enter a valid email address.'); valid = false;
      } else {
        var domainCheck = checkEmailDomain(email.value.trim());
        if (!domainCheck.valid && domainCheck.suggestion) {
          showFieldError(email, emailError, domainCheck.suggestion); valid = false;
        } else { 
          clearFieldError(email, emailError); 
        }
      }

      var pwd = document.getElementById('pwd');
      var pwdError = document.getElementById('pwdError');
      if (!pwd.value) {
        showFieldError(pwd, pwdError, 'Password is required.'); valid = false;
      } else if (!isStrongPassword(pwd.value)) {
        showFieldError(pwd, pwdError, 'Password does not meet the requirements.'); valid = false;
      } else { 
        clearFieldError(pwd, pwdError); 
      }

      var pwdConfirm = document.getElementById('pwd_confirm');
      var pwdConfirmError = document.getElementById('pwdConfirmError');
      if (!pwdConfirm.value) {
        showFieldError(pwdConfirm, pwdConfirmError, 'Please confirm your password.'); valid = false;
      } else if (pwd.value !== pwdConfirm.value) {
        showFieldError(pwdConfirm, pwdConfirmError, 'Passwords do not match.'); valid = false;
      } else { 
        clearFieldError(pwdConfirm, pwdConfirmError); 
      }

      var agree = document.getElementById('agree');
      var agreeError = document.getElementById('agreeError');
      if (!agree.checked) {
        agree.classList.add('input-error');
        if (agreeError) agreeError.textContent = 'You must agree to the Terms & Conditions.';
        valid = false;
      } else { 
        agree.classList.remove('input-error'); if (agreeError) agreeError.textContent = ''; 
      }

      if (!valid) {
        e.preventDefault();
      }
    });
  }

  // Forget Password Form for Customers and Staffs
  var forgotForm = document.getElementById('forgotForm');
  if (forgotForm) {
    forgotForm.addEventListener('submit', function (e) {
      var valid = true;

      var email = document.getElementById('email');
      var emailError = document.getElementById('emailError');
      if (!email.value.trim()) {
        showFieldError(email, emailError, 'Email is required.'); valid = false;
      } else if (!isValidEmail(email.value.trim())) {
        showFieldError(email, emailError, 'Please enter a valid email address.'); valid = false;
      } else if (roleInput && roleInput.value === 'staff' && !isOverclockEmail(email.value.trim())) {
        showFieldError(email, emailError, 'Staff accounts use @overclocktech.com email.'); valid = false;
      } else if (roleInput && roleInput.value === 'customer' && isOverclockEmail(email.value.trim())) {
        showFieldError(email, emailError, 'This is a staff email. Please select Staff to reset your password.'); valid = false;
      } else {
        var domainCheck = checkEmailDomain(email.value.trim());
        if (!domainCheck.valid && domainCheck.suggestion) {
          showFieldError(email, emailError, domainCheck.suggestion); valid = false;
        } else { 
          clearFieldError(email, emailError); 
        }
      }

      var newPwd = document.getElementById('new_pwd');
      var newPwdErr = document.getElementById('newPwdError');
      if (!newPwd.value) {
        showFieldError(newPwd, newPwdErr, 'New password is required.'); valid = false;
      } else if (!isStrongPassword(newPwd.value)) {
        showFieldError(newPwd, newPwdErr, 'Password does not meet the requirements.'); valid = false;
      } else { 
        clearFieldError(newPwd, newPwdErr); 
      }

      var confirmPwd = document.getElementById('confirm_pwd');
      var confirmPwdErr = document.getElementById('confirmPwdError');
      if (!confirmPwd.value) {
        showFieldError(confirmPwd, confirmPwdErr, 'Please confirm your new password.'); valid = false;
      } else if (newPwd.value !== confirmPwd.value) {
        showFieldError(confirmPwd, confirmPwdErr, 'Passwords do not match.'); valid = false;
      } else { 
        clearFieldError(confirmPwd, confirmPwdErr); 
      }

      if (!valid) {
        e.preventDefault();
      }
    });
  }

  // Change Password Form

  var changeForm = document.getElementById('changePasswordForm');
  if (changeForm) {
    changeForm.addEventListener('submit', function (e) {
      var valid = true;

      var newPwd = document.getElementById('new_pwd');
      var newPwdErr = document.getElementById('newPwdError');
      if (!newPwd.value) {
        showFieldError(newPwd, newPwdErr, 'New password is required.'); valid = false;
      } else if (!isStrongPassword(newPwd.value)) {
        showFieldError(newPwd, newPwdErr, 'Password does not meet the requirements.'); valid = false;
      } else { 
        clearFieldError(newPwd, newPwdErr); 
      }

      var confirmPwd = document.getElementById('confirm_pwd');
      var confirmPwdErr = document.getElementById('confirmPwdError');
      if (!confirmPwd.value) {
        showFieldError(confirmPwd, confirmPwdErr, 'Please confirm your new password.'); valid = false;
      } else if (newPwd.value !== confirmPwd.value) {
        showFieldError(confirmPwd, confirmPwdErr, 'Passwords do not match.'); valid = false;
      } else { 
        clearFieldError(confirmPwd, confirmPwdErr); 
      }

      if (!valid) {
        e.preventDefault();
      }
    });
  }

  // Defined Helper functions

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function isOverclockEmail(email) {
    var domain = email.split('@')[1];
    return domain && domain.toLowerCase() === 'overclocktech.com';
  }

  function showFieldError(input, errorEl, message) {
    if (input) {
      input.classList.add('input-error');
    }
    if (errorEl) {
      errorEl.textContent = message;
    }
  }

  function clearFieldError(input, errorEl) {
    if (input) {
      input.classList.remove('input-error');
    }
    if (errorEl) {
      errorEl.textContent = '';
    }
  }

  // Clear errors as user types
  document.querySelectorAll('.auth-form-input').forEach(function (input) {
    input.addEventListener('input', function () {
      input.classList.remove('input-error');
      var errorEl = document.getElementById(input.id + 'Error');
      if (errorEl) {
        errorEl.textContent = '';
      }
    });
  });

  var agreeBox = document.getElementById('agree');
  if (agreeBox) {
    agreeBox.addEventListener('change', function () {
      agreeBox.classList.remove('input-error');
      var agreeError = document.getElementById('agreeError');
      if (agreeError) {
        agreeError.textContent = '';
      }
    });
  }
});
