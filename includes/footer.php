<?php
// Include at the bottom of every page EXCEPT the landing page
$basePath = $basePath ?? '';
?>

<footer class="site-footer" role="contentinfo">
  <div class="footer-grid">

    <!-- Brand column -->
    <div class="footer-brand">
      <div class="navbar-brand" style="font-size:1.1rem;">
        OVERCLOCK<span class="brand-slash" style="color:var(--neon2);">/</span><span>TECH</span>
      </div>
      <p>Your one-stop destination for premium gaming hardware. Built for champions, designed for performance.</p>
    </div>

    <!-- Products column -->
    <div class="footer-col">
      <h4>Products</h4>
      <ul>
        <li><a href="<?= $basePath ?>products.php?cat=gaming-pc">Gaming PCs</a></li>
        <li><a href="<?= $basePath ?>products.php?cat=laptop">Laptops</a></li>
        <li><a href="<?= $basePath ?>products.php?cat=keyboard">Keyboards</a></li>
        <li><a href="<?= $basePath ?>products.php?cat=mouse">Mouse</a></li>
      </ul>
    </div>

    <!-- Company column -->
    <div class="footer-col">
      <h4>Company</h4>
      <ul>
        <li><a href="<?= $basePath ?>about.php">About Us</a></li>
        <li><a href="<?= $basePath ?>find-us.php">Find Us</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Support</h4>
      <ul>
        <li><a href="<?= $basePath ?>faq.php">FAQ</a></li>
      </ul>
    </div>

  </div>

  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> OVERCLOCK/TECH — All rights reserved</span>
    <span>Designed for gamers &bull; Built with passion</span>
  </div>
</footer>
