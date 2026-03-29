<?php
// includes/nav.php — Shared Navigation
// Set $activePage and $basePath before including.
// Root pages: $basePath = '';  |  Forms pages: $basePath = '../';

$activePage = $activePage ?? '';
$basePath   = $basePath ?? '';

$navItems = [
  ['href' => $basePath . 'products.php',                'label' => 'Products',   'key' => 'products'],
  ['href' => $basePath . 'products.php?category=1',     'label' => 'Gaming PC',  'key' => 'gaming-pc'],
  ['href' => $basePath . 'products.php?category=2',     'label' => 'Laptop',     'key' => 'laptop'],
  ['href' => $basePath . 'products.php?category=3',     'label' => 'Keyboard',   'key' => 'keyboard'],
  ['href' => $basePath . 'products.php?category=4',     'label' => 'Mouse',      'key' => 'mouse'],
  ['href' => $basePath . 'find-us.php',                 'label' => 'Find Us',    'key' => 'find-us'],
  ['href' => 'partnership.php',             'label' => 'Partnership','key' => 'partnership'],

];

// Cart item count (from session)
session_start_if_not_started();
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0;

function session_start_if_not_started() {
  if (session_status() === PHP_SESSION_NONE) session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $isLoggedIn ? htmlspecialchars($_SESSION['username'] ?? 'Account') : null;
?>

<nav class="navbar" role="navigation" aria-label="Main navigation">
  <!-- Brand -->
  <div class="nav-top">
    <a href="<?= $basePath ?>home.php" class="navbar-brand" aria-label="OVERCLOCK TECH Home">
      OVERCLOCK<span class="brand-slash">/</span><span>TECH</span>
    </a>

    <!-- Icons -->
    <div class="nav-icons">
      <!-- Search -->
      
      <div class="search" id="searchBar">
      <form action="search.php" method="GET" id="searchForm">
        <input class="search-input" type="search" name="q" placeholder="Search Products" />
      </form>
      <button class="nav-icon-btn" id="searchBtn" aria-label="Search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
      </button>
      </div>

      <!-- User / Login -->
      <?php if ($isLoggedIn): ?>
        <a href="<?= $basePath ?>profile.php" class="nav-icon-btn" aria-label="My Account: <?= $userName ?>" title="My Profile">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
        </a>
        <a href="<?= $basePath ?>forms/logout.php" class="nav-icon-btn nav-logout-btn" aria-label="Log out" title="Log Out">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </a>
      <?php else: ?>
        <a href="<?= $basePath ?>forms/login.php" class="nav-icon-btn" aria-label="Login">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
        </a>
      <?php endif; ?>

      <!-- Cart -->
      <a href="<?= $basePath ?>cart.php" class="nav-icon-btn" aria-label="Shopping cart, <?= $cartCount ?> items">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 01-8 0"/>
        </svg>
        <?php if ($cartCount > 0): ?>
          <span class="cart-badge" aria-hidden="true"><?= $cartCount > 9 ? '9+' : $cartCount ?></span>
        <?php endif; ?>
      </a>

      <!-- Mobile toggle -->
      <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="navLinks">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>


  <!-- Nav links -->
  <ul class="nav-links" id="navLinks" role="menubar">
    <?php foreach ($navItems as $item): ?>
      <li role="none">
        <a href="<?= $item['href'] ?>"
           class="<?= $activePage === $item['key'] ? 'active' : '' ?>"
           role="menuitem"
           aria-current="<?= $activePage === $item['key'] ? 'page' : 'false' ?>">
          <?= $item['label'] ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

</nav>
<!-- <script src="js/nav.js"></script> -->
<script src="<?= $basePath ?>js/nav.js" defer></script>
