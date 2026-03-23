<?php
// Include at the bottom of every page EXCEPT the landing page??
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
        <li><a href="products.php?cat=gaming-pc">Gaming PCs</a></li>
        <li><a href="products.php?cat=laptop">Laptops</a></li>
        <li><a href="products.php?cat=keyboard">Keyboards</a></li>
        <li><a href="products.php?cat=mouse">Mice</a></li>
        <li><a href="products.php?cat=headset">Headsets</a></li>
      </ul>
    </div>

    <!-- Company column -->
    <div class="footer-col">
      <h4>Company</h4>
      <ul>
        <li><a href="about.php">About Us</a></li>
        <li><a href="find-us.php">Find Us</a></li>
        <li><a href="partnership.php">Partnerships</a></li>
        <li><a href="careers.php">Careers</a></li>
      </ul>
    </div>

    <!-- Support column -->
    <div class="footer-col">
      <h4>Support</h4>
      <ul>
        <li><a href="faq.php">FAQ</a></li>
        <li><a href="shipping.php">Shipping</a></li>
        <li><a href="returns.php">Returns</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </div>

  </div>

  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> OVERCLOCK/TECH — All rights reserved</span>
    <span>Designed for gamers &bull; Built with passion</span>
  </div>
</footer>
