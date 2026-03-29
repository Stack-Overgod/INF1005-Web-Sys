<?php
session_start();
$activePage = 'admin';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: forms/login.php');
    exit();
}

$displayName = trim($_SESSION['username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Admin area for OVERCLOCK/TECH.">
  <title>OVERCLOCK/TECH — Admin</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="auth-page">
  <div class="auth-card auth-result-card">
    <div class="result-icon result-success" aria-hidden="true">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 6L9 17l-5-5"/>
      </svg>
    </div>
    <h1 class="auth-heading">Welcome Back Admin</h1>
    <p class="auth-subtext">Signed in as <strong><?php echo htmlspecialchars($displayName); ?></strong>.</p>
  </div>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>