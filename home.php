<?php

session_start();
$activePage = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="OVERCLOCK/TECH — Shop the latest gaming PCs, laptops, keyboards, mouse and peripherals. Built for champions.">
  <title>OVERCLOCK/TECH — Home</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content">

  <!--AD BANNER-->
  <section class="hero-carousel" aria-label="Featured products">

    <div class="carousel-track" id="carouselTrack">

      <!-- Slide 1: Gaming PC -->

      <div class="carousel-slide slide-1" role="group" aria-label="Slide 1 of 3">
        <div class="slide-bg-glow" aria-hidden="true"></div>
        <div class="slide-content">
          <div class="slide-tag">New Arrival</div>
          <h2 class="slide-title">
            OVERCLOCK <span class="hi">PHANTOM</span><br>PRO X
          </h2>
          <p class="slide-subtitle">RTX 5090 &bull; Intel Core i9 &bull; 64GB DDR5 &bull; 4TB NVMe</p>
          <div class="slide-price">$4,999</div>
          <a href="products.php?cat=gaming-pc" class="btn-slide">
            Shop Now
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
        <div class="slide-image" aria-hidden="true">
          <img src="images/hero-gaming-pc.jpg" alt="Overclock Phantom Pro X Gaming PC" loading="eager">
        </div>
      </div>
      <div class="carousel-slide slide-2" role="group" aria-label="Slide 2 of 3">
        <div class="slide-bg-glow" aria-hidden="true"></div>
        <div class="slide-content">
          <div class="slide-tag">Best Seller</div>
          <h2 class="slide-title">
            VIPER <span class="hi">EDGE</span><br>KEYBOARD
          </h2>
          <p class="slide-subtitle">Optical Switches &bull; Per-Key RGB &bull; Aluminum Frame &bull; NKRO</p>
          <div class="slide-price" style="color:var(--neon2);">$189 <span>free shipping</span></div>
          <a href="products.php?cat=keyboard" class="btn-slide">
            View Details
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
        <div class="slide-image" aria-hidden="true">
          <img src="images/hero-keyboard.jpg" alt="Viper Edge Gaming Keyboard" loading="eager">
        </div>
      </div>
      <div class="carousel-slide slide-3" role="group" aria-label="Slide 3 of 3">
        <div class="slide-bg-glow" aria-hidden="true"></div>
        <div class="slide-content">
          <div class="slide-tag" style="color:#00ff96">Limited Deal</div>
          <h2 class="slide-title">
            APEX <span class="hi" style="color:#00ff96;text-shadow:0 0 24px rgba(0,255,150,0.5)">BLADE</span><br>LAPTOP
          </h2>
          <p class="slide-subtitle">QHD 240Hz &bull; RTX 4080 &bull; AMD Ryzen 9 &bull; 2kg Ultra-Light</p>
          <div class="slide-price" style="color:#00ff96">$2,299</div>
          <a href="products.php?cat=laptop" class="btn-slide" style="background:#00ff96">
            Grab the Deal
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
        <div class="slide-image" aria-hidden="true">
          <img src="images/hero-laptop.jpg" alt="Apex Blade Gaming Laptop" loading="eager">
        </div>
      </div>

    </div><!-- /.carousel-track -->

    <!-- Controls -->
    <button class="carousel-btn prev" id="prevBtn" aria-label="Previous slide">&#8592;</button>
    <button class="carousel-btn next" id="nextBtn" aria-label="Next slide">&#8594;</button>

    <!-- Dots -->
    <div class="carousel-nav" role="tablist" aria-label="Slide indicators">
      <button class="carousel-dot active" data-slide="0" role="tab" aria-selected="true"  aria-label="Go to slide 1"></button>
      <button class="carousel-dot"        data-slide="1" role="tab" aria-selected="false" aria-label="Go to slide 2"></button>
      <button class="carousel-dot"        data-slide="2" role="tab" aria-selected="false" aria-label="Go to slide 3"></button>
    </div>

  </section>

 
      <!-- What we do -->
 
  <section class="section" aria-labelledby="what-heading">
    <div class="section-header">
      <div class="section-kicker">Our Expertise</div>
      <h2 class="section-title" id="what-heading">
        What We <span class="hi">Do?</span>
      </h2>
      <p class="section-sub">From ready-to-ship battle stations to bespoke custom builds — we've got you covered.</p>
    </div>

    <div class="what-grid">
      <div class="what-card">
        <span class="what-icon" aria-hidden="true">🖥️</span>
        <h3>Ready-to-Ship PCs</h3>
        <p>Pre-built, rigorously tested gaming rigs optimised for maximum FPS. Ships in 24–48 hours.</p>
      </div>
      <div class="what-card">
        <span class="what-icon" aria-hidden="true">🔧</span>
        <h3>Custom Builds</h3>
        <p>Configure your dream machine with our PC builder tool. Assembled and tested by our experts.</p>
      </div>
      <div class="what-card">
        <span class="what-icon" aria-hidden="true">💻</span>
        <h3>Gaming Laptops</h3>
        <p>High-refresh-rate portable powerhouses for gaming on the go without compromise.</p>
      </div>
      <div class="what-card">
        <span class="what-icon" aria-hidden="true">⌨️</span>
        <h3>Peripherals</h3>
        <p>Pro-grade keyboards and mouse.</p>
      </div>
    </div>
  </section>

  <!-- ══════════════════════════════════════════
       AWARDS
  ═══════════════════════════════════════════ -->
  <section class="section-full" aria-labelledby="awards-heading">
    <div class="section-inner">
      <div class="section-header">
        <div class="section-kicker">Recognition</div>
        <h2 class="section-title" id="awards-heading">
          Our <span class="hi">Awards</span>
        </h2>
        <p class="section-sub">Industry recognition for engineering excellence and customer satisfaction.</p>
      </div>

      <div class="awards-grid">
        <div class="award-card">
          <span class="award-icon" aria-hidden="true">🏆</span>
          <h3>Best Gaming PC 2024</h3>
          <p>TechRadar Hardware Awards</p>
        </div>
        <div class="award-card">
          <span class="award-icon" aria-hidden="true">⭐</span>
          <h3>Editor's Choice</h3>
          <p>PC Gamer Magazine</p>
        </div>
        <div class="award-card">
          <span class="award-icon" aria-hidden="true">🎮</span>
          <h3>Esports Partner</h3>
          <p>Southeast Asia Gaming League</p>
        </div>
        <div class="award-card">
          <span class="award-icon" aria-hidden="true">🔥</span>
          <h3>Top Rated Seller</h3>
          <p>HardwareBenchmark.sg — 4.9★</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ══════════════════════════════════════════
       PRO GAMER REVIEWS
  ═══════════════════════════════════════════ -->
  <section class="section" aria-labelledby="reviews-heading">
    <div class="section-header">
      <div class="section-kicker">Community</div>
      <h2 class="section-title" id="reviews-heading">
        Pro Gamer <span class="hi">Reviews</span>
      </h2>
      <p class="section-sub">Trusted by professional esports athletes and content creators worldwide.</p>
    </div>

    <div class="reviews-grid">

      <article class="review-card">
        <div class="review-stars" aria-label="5 out of 5 stars">★★★★★</div>
        <p class="review-text">
          The OVERCLOCK PHANTOM is insane. No throttling during 12-hour tournament sessions, 
          temps stay perfect. This is genuinely the best rig I've ever played on.
        </p>
        <div class="reviewer">
          <div class="reviewer-avatar" aria-hidden="true">🎮</div>
          <div>
            <div class="reviewer-name">ZeroX Striker</div>
            <div class="reviewer-handle">@zerox_fps &mdash; Valorant Pro</div>
          </div>
        </div>
      </article>

      <article class="review-card">
        <div class="review-stars" aria-label="5 out of 5 stars">★★★★★</div>
        <p class="review-text">
          Switched my entire streaming setup to OVERCLOCK peripherals. The keyboard response 
          is unreal — the clicks feel absolutely crisp and precise every single time.
        </p>
        <div class="reviewer">
          <div class="reviewer-avatar" aria-hidden="true">🖱️</div>
          <div>
            <div class="reviewer-name">Lumina Plays</div>
            <div class="reviewer-handle">@luminaplays &mdash; Twitch Partner</div>
          </div>
        </div>
      </article>

      <article class="review-card">
        <div class="review-stars" aria-label="4 out of 5 stars">★★★★★</div>
        <p class="review-text">
          Custom built mine through their site — the PC builder is super intuitive. 
          Arrived early, packed flawlessly. Frames are through the roof on every title.
        </p>
        <div class="reviewer">
          <div class="reviewer-avatar" aria-hidden="true">💻</div>
          <div>
            <div class="reviewer-name">NightOwl Gaming</div>
            <div class="reviewer-handle">@nightowl_gg &mdash; CS2 IEM Champion</div>
          </div>
        </div>
      </article>

    </div>
  </section>

  <!-- ══════════════════════════════════════════
       GROWING PRESENCE
  ═══════════════════════════════════════════ -->
  <section class="presence-section" aria-labelledby="presence-heading">
    <div class="section-header" style="margin-bottom:2.5rem">
      <div class="section-kicker">Our Scale</div>
      <h2 class="section-title" id="presence-heading">
        Growing <span class="hi">Presence</span>
      </h2>
    </div>

    <div class="stats-grid" role="list">
      <div class="stat-item" role="listitem">
        <span class="stat-number" aria-label="50 thousand plus customers">50K+</span>
        <div class="stat-label">Happy Customers</div>
      </div>
      <div class="stat-item" role="listitem">
        <span class="stat-number" aria-label="500 plus products">500+</span>
        <div class="stat-label">Products Listed</div>
      </div>
      <div class="stat-item" role="listitem">
        <span class="stat-number" aria-label="12 countries">12</span>
        <div class="stat-label">Countries Shipped</div>
      </div>
      <div class="stat-item" role="listitem">
        <span class="stat-number" aria-label="99 percent satisfaction">99%</span>
        <div class="stat-label">Satisfaction Rate</div>
      </div>
      <div class="stat-item" role="listitem">
        <span class="stat-number" aria-label="24 slash 7 support">24/7</span>
        <div class="stat-label">Support Available</div>
      </div>
    </div>

    <p class="presence-desc">
      From a small team of gaming enthusiasts in Singapore, OVERCLOCK/TECH has grown into 
      a leading gaming hardware brand across Southeast Asia. Our commitment to quality 
      and our community continues to drive everything we build.
    </p>
  </section>

</main><!-- /#main-content -->

<?php include 'includes/footer.php'; ?>

<!-- Carousel Script -->
<script>
(function () {
  const track  = document.getElementById('carouselTrack');
  const dots   = document.querySelectorAll('.carousel-dot');
  const prev   = document.getElementById('prevBtn');
  const next   = document.getElementById('nextBtn');
  const total  = dots.length;
  let current  = 0;
  let timer;

  function goTo(idx) {
    current = (idx + total) % total;
    track.style.transform = `translateX(-${current * 100}%)`;
    dots.forEach((d, i) => {
      d.classList.toggle('active', i === current);
      d.setAttribute('aria-selected', i === current);
    });
  }

  function startAuto() {
    timer = setInterval(() => goTo(current + 1), 9000);
  }

  function stopAuto() { clearInterval(timer); }

  prev.addEventListener('click', () => { stopAuto(); goTo(current - 1); startAuto(); });
  next.addEventListener('click', () => { stopAuto(); goTo(current + 1); startAuto(); });

  dots.forEach(d => {
    d.addEventListener('click', () => {
      stopAuto();
      goTo(parseInt(d.dataset.slide));
      startAuto();
    });
  });

  // Touch / swipe support
  let startX = 0;
  track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
  track.addEventListener('touchend', e => {
    const diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) { stopAuto(); goTo(diff > 0 ? current + 1 : current - 1); startAuto(); }
  });

  // Pause on hover
  track.parentElement.addEventListener('mouseenter', stopAuto);
  track.parentElement.addEventListener('mouseleave', startAuto);

  startAuto();

  // Scroll reveal for cards
  const reveals = document.querySelectorAll('.what-card, .award-card, .review-card, .stat-item');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.style.opacity = '1';
        e.target.style.transform = 'translateY(0)';
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });

  reveals.forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(24px)';
    el.style.transition = `opacity 0.5s ${i * 0.07}s ease, transform 0.5s ${i * 0.07}s ease`;
    observer.observe(el);
  });

  //  Counter animation for stats 
  const counters = document.querySelectorAll('.stat-number');
  const countObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        animateCounter(e.target);
        countObserver.unobserve(e.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(c => countObserver.observe(c));

  function animateCounter(el) {
    const raw = el.textContent.trim();
    const num = parseFloat(raw.replace(/[^0-9.]/g, ''));
    const suffix = raw.replace(/[0-9.]/g, '');
    if (isNaN(num)) return;
    let start = 0;
    const duration = 1600;
    const step = timestamp => {
      if (!start) start = timestamp;
      const progress = Math.min((timestamp - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = (num < 10 ? (num * eased).toFixed(1) : Math.floor(num * eased)) + suffix;
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = raw;
    };
    requestAnimationFrame(step);
  }
})();
</script>

</body>
</html>