<?php
session_start();
$activePage = 'register';

$fname = $lname = $email = $role = $password_hash = $errorMsg = "";
$success = true;

// Public registration is for customers only
if (empty($_POST["role"]) || $_POST["role"] !== 'customer') {
    $role = "customer";
} else {
    $role = $_POST["role"];
}

// Validate and sanitize first name (if provided)
if (!empty($_POST["fname"])) {
    $fname = sanitize_input($_POST["fname"]);

    if (!preg_match("/^[a-zA-Z\s'-]+$/", $fname)) {
        $errorMsg .= "First name must contain only letters, spaces, hyphens, and apostrophes.<br>";
        $success = false;
    }
}

// Validate and sanitize last name
if (empty($_POST["lname"])) {
    $errorMsg .= "Last Name is required.<br>";
    $success = false;
} else {
    $lname = sanitize_input($_POST["lname"]);

    if (!preg_match("/^[a-zA-Z\s'-]+$/", $lname)) {
        $errorMsg .= "Last name must contain only letters, spaces, hyphens, and apostrophes.<br>";
        $success = false;
    }
}

// Validate email
if (empty($_POST["email"])) {
    $errorMsg .= "Email is required.<br>";
    $success = false;
} else {
    $email = sanitize_input($_POST["email"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg .= "Invalid email format.<br>";
        $success = false;
    }

    // Check email domain for misspellings or unrecognised domains
    if ($success && $role === 'customer') {
        $suggestion = checkEmailDomain($email);
        if ($suggestion === 'unrecognised') {
            $errorMsg .= "Please use a valid email domain (e.g. gmail.com, yahoo.com, outlook.com).<br>";
            $success = false;
        } else if ($suggestion !== null) {
            $errorMsg .= "Invalid email domain. Did you mean " . htmlspecialchars($suggestion) . "?<br>";
            $success = false;
        }
    }
}

// Validate password
if (empty($_POST["pwd"])) {
    $errorMsg .= "Password is required.<br>";
    $success = false;
} else if (empty($_POST["pwd_confirm"])) {
    $errorMsg .= "Password Confirmation is required.<br>";
    $success = false;
} else if ($_POST["pwd"] !== $_POST["pwd_confirm"]) {
    $errorMsg .= "Passwords do not match.<br>";
    $success = false;
} else if (!preg_match('/[a-z]/', $_POST["pwd"]) || !preg_match('/[A-Z]/', $_POST["pwd"]) ||
           !preg_match('/[0-9]/', $_POST["pwd"]) || strlen($_POST["pwd"]) < 8) {
    $errorMsg .= "Password does not meet the required strength.<br>";
    $success = false;
} else {
    // Hash the password - never output plaintext password or hash to the webpage
    $password_hash = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
}

// Checks if the Terms and Conditions checkbox has been checked
if (!isset($_POST["agree"])) {
    $errorMsg .= "You must agree to the Terms &amp; Conditions.<br>";
    $success = false;
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/*
* Validate provided email domain against widely used email domains for misspellings
*/
function checkEmailDomain($email) {
    $validDomains = [
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

    $parts = explode('@', $email);
    if (count($parts) !== 2) return null;

    $domain = strtolower($parts[1]);

    // Exact match — domain is valid
    if (in_array($domain, $validDomains)) return null;

    // Find closest match using levenshtein
    $closest = null;
    $closestDist = 999;
    foreach ($validDomains as $valid) {
        $dist = levenshtein($domain, $valid);
        if ($dist < $closestDist) {
            $closestDist = $dist;
            $closest = $valid;
        }
    }

    if ($closestDist <= 2 && $closest !== $domain) {
        return $parts[0] . '@' . $closest;
    }

    // Domain completely not recognised
    return "unrecognised";
}

// Add record into MySQL database
function saveMember() {
    global $fname, $lname, $email, $role, $password_hash, $errorMsg, $success;

    try {
        // Create database connection directly
        $host = 'localhost';
        $dbname = 'overclock_tech';
        $dbuser = 'root';
        $dbpass = ''; // update this to match your MySQL password

        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $table = 'customers';

        // Check if email already exists in the relevant table
        $checkStmt = $pdo->prepare("SELECT 1 FROM $table WHERE email = :email");
        $checkStmt->execute([':email' => $email]);

        if ($checkStmt->fetch()) {
            $errorMsg = "An account with this email already exists.<br>";
            $success = false;
            return;
        }

        // Insert new record using prepared statement to prevent SQL injection
        $stmt = $pdo->prepare(
            "INSERT INTO $table (fname, lname, email, password) VALUES (:fname, :lname, :email, :password)"
        );
        $stmt->execute([
            ':fname' => $fname ?: null,
            ':lname' => $lname,
            ':email' => $email,
            ':password' => $password_hash,
        ]);
    } catch (PDOException $e) {
        $errorMsg = "A system error occurred. Please try again later.<br>";
        $success = false;
    }
}

if ($success) {
    saveMember();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OVERCLOCK/TECH — Registration</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php $basePath = '../';
include '../includes/nav.php'; ?>

<main id="main-content" class="auth-page">
  <div class="auth-card auth-result-card">
    <?php if ($success): ?>
      <?php
        $displayName = trim($lname);
        if (!empty(trim($fname))) {
            $displayName = trim($fname) . ' ' . trim($lname);
        }
      ?>
      <div class="result-icon result-success" aria-hidden="true">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <h2 class="auth-heading">Registration Successful!</h2>
      <p class="auth-subtext">Thank you for signing up, <strong><?php echo htmlspecialchars($displayName); ?></strong>.</p>
      <a href="login.php" class="btn-auth">
        <span>Log In</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      </a>
    <?php else: ?>
      <div class="result-icon result-error" aria-hidden="true">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
      </div>
      <h2 class="auth-heading">Oops!</h2>
      <p class="auth-subtext">The following errors were detected:</p>
      <div class="error-box" role="alert">
        <?php echo $errorMsg; ?>
      </div>
      <a href="register.php" class="btn-auth btn-auth-danger">
        <span>Return to Registration</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
      </a>
    <?php endif; ?>
  </div>
</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
