<?php
session_start();
$activePage = 'login';

$roleConfig = [
  'customer' => ['table' => 'customers', 'id_col' => 'customer_id'],
  'staff' => ['table' => 'staff', 'id_col' => 'staff_id'],
];

$email = $pwd = $role = $errorMsg = "";
$fname = $lname = "";
$success = true;

// Check the role of user
if (empty($_POST["role"]) || !array_key_exists($_POST["role"], $roleConfig)) {
    $role = "customer";
} else {
    $role = $_POST["role"];
}

// Validate and Sanitize email
if (empty($_POST["email"])) {
    $errorMsg .= "Email is required.<br>";            // .= concatenates the error messages
    $success = false;
} else {
    $email = sanitize_input($_POST["email"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg .= "Invalid email format.<br>";
        $success = false;
    }

    // Staff must use company email
    if ($success && $role === 'staff') {
        $domain = substr(strrchr($email, "@"), 1);
        if (strtolower($domain) !== 'overclocktech.com') {
            $errorMsg .= "Staff must log in with their @overclocktech.com email.<br>";
            $success = false;
        }
    }

    // Customers cannot use staff email domain
    if ($success && $role === 'customer') {
        $domain = substr(strrchr($email, "@"), 1);
        if (strtolower($domain) === 'overclocktech.com') {
            $errorMsg .= "This is a staff email. Please select Staff to log in.<br>";
            $success = false;
        }
    }
}

// Validate password
if (empty($_POST["pwd"])) {
    $errorMsg .= "Password is required.<br>";
    $success = false;
} else {
    $pwd = $_POST["pwd"];
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Verify provided information with the stored data in MySQL
function authenticateUser() {
  global $fname, $lname, $email, $pwd, $role, $errorMsg, $success, $roleConfig;

    try {
        require_once '../db.php';

        // Determine which table to query based on role
        $table = $roleConfig[$role]['table'];
        $idCol = $roleConfig[$role]['id_col'];

        // Use prepared statement to prevent SQL injection
        $stmt = $pdo->prepare(
            "SELECT $idCol AS user_id, fname, lname, password FROM $table WHERE email = :email"
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($pwd, $row['password'])) {
            $fname = $row['fname'];
            $lname = $row['lname'];

            // Set session variables
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = trim(($row['fname'] ?? '') . ' ' . $row['lname']);
            $_SESSION['role'] = $role;
            $_SESSION['email'] = $email;
        } else {
            // Generic error message for security
            $errorMsg = "Wrong email or password provided.";
            $success = false;
        }
    } catch (PDOException $e) {
        $errorMsg = "A system error occurred. Please try again later.";
        $success = false;
    }
}

if ($success) {
    authenticateUser();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Login</title>
    <meta name="description" content="Log in to your OVERCLOCK/TECH account.">
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
  <div class="auth-card auth-result-card">
    <?php if ($success): ?>
      <?php
        $displayName = trim($lname);
        if (!empty(trim($fname ?? ''))) {
            $displayName = trim($fname) . ' ' . trim($lname);
        }
      ?>
      <div class="result-icon result-success" aria-hidden="true">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <h1 class="auth-heading">Login Successful!</h1>
      <p class="auth-subtext">Welcome back, <strong><?php echo htmlspecialchars($displayName); ?></strong>.</p>
      <a href="../home.php" class="btn-auth">
        <span>Go to Home</span>
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
      <h1 class="auth-heading">Oops!</h1>
      <p class="auth-subtext">The following errors were detected:</p>
      <div class="error-box" role="alert">
        <?php echo $errorMsg; ?>
      </div>
      <a href="login.php" class="btn-auth btn-auth-warning">
        <span>Return to Login</span>
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