<?php

session_start();
$activePage = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Home</title>
    <meta name="description" content="OVERCLOCK/TECH — Shop the latest gaming PCs, laptops, keyboards, mouse and peripherals. Built for champions.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">  
  <style>
    .visually-hidden {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }
  </style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content">

  <h1 class="visually-hidden">OVERCLOCK/TECH — Gaming Hardware Store</h1>

  <section class="hero-carousel" aria-label="Featured products" aria-live="polite" aria-atomic="false">

    <div class="carousel-track" id="carouselTrack">

      <div class="carousel-slide slide-1" role="tabpanel" id="slide-1" aria-label="Slide 1 of 3">
        <div class="slide-bg-glow" aria-hidden="true"></div>
        <div class="slide-content">
          <p class="slide-tag" aria-hidden="true">Top Pick</p>
          <h2 class="slide-title">
            GAMING <span class="hi">PC</span>
          </h2>
          <p class="slide-subtitle">RTX 4090 &bull; Intel Core i9-13900K &bull; 32GB DDR5 &bull; 2TB NVMe</p>
          <p class="slide-price"><span class="visually-hidden">Price: </span>$1,499.99</p>
          <a href="product.php?id=1" class="btn-slide" aria-label="Shop Now — Gaming PC at $1,499.99">
            Shop Now
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" focusable="false"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
        <div class="slide-image" aria-hidden="true">
          <img src="images/gamingpc.jpg" alt="" loading="eager">
        </div>
      </div>

      <div class="carousel-slide slide-2" role="tabpanel" id="slide-2" aria-label="Slide 2 of 3">
        <div class="slide-bg-glow" aria-hidden="true"></div>
        <div class="slide-content">
          <p class="slide-tag" aria-hidden="true">Best Seller</p>
          <h2 class="slide-title">
            MECHANICAL <span class="hi">KEYBOARD</span>
          </h2>
          <p class="slide-subtitle">Cherry MX Red &bull; RGB Backlit &bull; 1000Hz Polling &bull; Anti-Ghosting</p>
          <p class="slide-price" style="color:var(--neon2);"><span class="visually-hidden">Price: </span>$79.99 <span>free shipping</span></p>
          <a href="product.php?id=3" class="btn-slide" aria-label="View Details — Mechanical Keyboard at $79.99">
            View Details
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" focusable="false"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
        <div class="slide-image" aria-hidden="true">
          <img src="images/keyboard.jpg" alt="" loading="eager">
        </div>
      </div>

      <div class="carousel-slide slide-3" role="tabpanel" id="slide-3" aria-label="Slide 3 of 3">
        <div class="slide-bg-glow" aria-hidden="true"></div>
        <div class="slide-content">
          <p class="slide-tag" style="color:#00ff96" aria-hidden="true">Limited Deal</p>
          <h2 class="slide-title">
            GAMING <span class="hi" style="color:#00ff96;text-shadow:0 0 24px rgba(0,255,150,0.5)">LAPTOP</span>
          </h2>
          <p class="slide-subtitle">RTX 4070 &bull; Intel Core i7-13700H &bull; 16GB DDR5 &bull; 15.6" 144Hz</p>
          <p class="slide-price" style="color:#00ff96"><span class="visually-hidden">Price: </span>$999.99</p>
          <a href="product.php?id=2" class="btn-slide" style="background:#00ff96" aria-label="Grab the Deal — Gaming Laptop at $999.99">
            Grab the Deal
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" focusable="false"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
        <div class="slide-image" aria-hidden="true">
          <img src="images/laptop.jpg" alt="" loading="eager">
        </div>
      </div>

    </div>

    <button class="carousel-btn prev" id="prevBtn" aria-label="Previous slide">&#8592;</button>
    <button class="carousel-btn next" id="nextBtn" aria-label="Next slide">&#8594;</button>

    <div class="carousel-nav" role="tablist" aria-label="Slide indicators">
      <button class="carousel-dot active" data-slide="0" role="tab" aria-selected="true"  aria-controls="slide-1" aria-label="Go to slide 1"></button>
      <button class="carousel-dot"        data-slide="1" role="tab" aria-selected="false" aria-controls="slide-2" aria-label="Go to slide 2"></button>
      <button class="carousel-dot"        data-slide="2" role="tab" aria-selected="false" aria-controls="slide-3" aria-label="Go to slide 3"></button>
    </div>

  </section>

  <section class="section" aria-labelledby="what-heading">
    <div class="section-header">
      <p class="section-kicker">Our Expertise</p>
      <h2 class="section-title" id="what-heading">
        What We <span class="hi">Do?</span>
      </h2>
      <p class="section-sub">From ready-to-ship battle stations to bespoke custom builds — we've got you covered.</p>
    </div>

    <div class="what-grid">
      <article class="what-card">
        <span class="what-icon" aria-hidden="true">🖥️</span>
        <h3>Ready-to-Ship PCs</h3>
        <p>Pre-built, rigorously tested gaming rigs optimised for maximum FPS. Ships in 24–48 hours.</p>
      </article>
      <article class="what-card">
        <span class="what-icon" aria-hidden="true">🔧</span>
        <h3>Custom Builds</h3>
        <p>Configure your dream machine with our PC builder tool. Assembled and tested by our experts.</p>
      </article>
      <article class="what-card">
        <span class="what-icon" aria-hidden="true">💻</span>
        <h3>Gaming Laptops</h3>
        <p>High-refresh-rate portable powerhouses for gaming on the go without compromise.</p>
      </article>
      <article class="what-card">
        <span class="what-icon" aria-hidden="true">⌨️</span>
        <h3>Peripherals</h3>
        <p>Pro-grade keyboards and mice.</p>
      </article>
    </div>
  </section>

  <section class="section-full" aria-labelledby="awards-heading">
    <div class="section-inner">
      <div class="section-header">
        <p class="section-kicker">Recognition</p>
        <h2 class="section-title" id="awards-heading">
          Our <span class="hi">Awards</span>
        </h2>
        <p class="section-sub">Industry recognition for engineering excellence and customer satisfaction.</p>
      </div>

      <div class="awards-grid">
        <article class="award-card">
          <span class="award-icon" aria-hidden="true">🏆</span>
          <h3>Best Gaming PC 2024</h3>
          <p>TechRadar Hardware Awards</p>
        </article>
        <article class="award-card">
          <span class="award-icon" aria-hidden="true">⭐</span>
          <h3>Editor's Choice</h3>
          <p>PC Gamer Magazine</p>
        </article>
        <article class="award-card">
          <span class="award-icon" aria-hidden="true">🎮</span>
          <h3>Esports Partner</h3>
          <p>Southeast Asia Gaming League</p>
        </article>
        <article class="award-card">
          <span class="award-icon" aria-hidden="true">🔥</span>
          <h3>Top Rated Seller</h3>
          <p>HardwareBenchmark.sg — 4.9&#9733;</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section" aria-labelledby="reviews-heading">
    <div class="section-header">
      <p class="section-kicker">Community</p>
      <h2 class="section-title" id="reviews-heading">
        Pro Gamer <span class="hi">Reviews</span>
      </h2>
      <p class="section-sub">Trusted by professional esports athletes and content creators worldwide.</p>
    </div>

    <div class="reviews-grid">

      <article class="review-card">
        <div role="img" aria-label="5 out of 5 stars" class="review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <h3 class="visually-hidden">Review by ZeroX Striker</h3>
        <p class="review-text">
          The OVERCLOCK PHANTOM is insane. No throttling during 12-hour tournament sessions,
          temps stay perfect. This is genuinely the best rig I've ever played on.
        </p>
        <div class="reviewer">
          <div class="reviewer-avatar" aria-hidden="true">🎮</div>
          <div>
            <p class="reviewer-name">ZeroX Striker</p>
            <p class="reviewer-handle">@zerox_fps &mdash; Valorant Pro</p>
          </div>
        </div>
      </article>

      <article class="review-card">
        <div role="img" aria-label="5 out of 5 stars" class="review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <h3 class="visually-hidden">Review by Lumina Plays</h3>
        <p class="review-text">
          Switched my entire streaming setup to OVERCLOCK peripherals. The keyboard response
          is unreal — the clicks feel absolutely crisp and precise every single time.
        </p>
        <div class="reviewer">
          <div class="reviewer-avatar" aria-hidden="true">🖱️</div>
          <div>
            <p class="reviewer-name">Lumina Plays</p>
            <p class="reviewer-handle">@luminaplays &mdash; Twitch Partner</p>
          </div>
        </div>
      </article>

      <article class="review-card">
        <div role="img" aria-label="5 out of 5 stars" class="review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <h3 class="visually-hidden">Review by NightOwl Gaming</h3>
        <p class="review-text">
          Custom built mine through their site — the PC builder is super intuitive.
          Arrived early, packed flawlessly. Frames are through the roof on every title.
        </p>
        <div class="reviewer">
          <div class="reviewer-avatar" aria-hidden="true">💻</div>
          <div>
            <p class="reviewer-name">NightOwl Gaming</p>
            <p class="reviewer-handle">@nightowl_gg &mdash; CS2 IEM Champion</p>
          </div>
        </div>
      </article>

    </div>
  </section>

  <section class="presence-section" aria-labelledby="presence-heading">
    <div class="section-header" style="margin-bottom:2.5rem">
      <p class="section-kicker">Our Scale</p>
      <h2 class="section-title" id="presence-heading">
        Growing <span class="hi">Presence</span>
      </h2>
    </div>

    <div class="stats-grid" role="list">
      <div class="stat-item" role="listitem">
        <span class="stat-number" role="img" aria-label="50 thousand plus customers">50K+</span>
        <p class="stat-label">Happy Customers</p>
      </div>
      <div class="stat-item" role="listitem">
        <span class="stat-number" role="img" aria-label="500 plus products">500+</span>
        <p class="stat-label">Products Listed</p>
      </div>
      <div class="stat-item" role="listitem">
        <span class="stat-number" role="img" aria-label="12 countries shipped">12</span>
        <p class="stat-label">Countries Shipped</p>
      </div>
      <div class="stat-item" role="listitem">
        <span class="stat-number" role="img" aria-label="99 percent satisfaction rate">99%</span>
        <p class="stat-label">Satisfaction Rate</p>
      </div>
      <div class="stat-item" role="listitem">
        <span class="stat-number" role="img" aria-label="24 hours a day, 7 days a week support">24/7</span>
        <p class="stat-label">Support Available</p>
      </div>
    </div>

    <p class="presence-desc">
      From a small team of gaming enthusiasts in Singapore, OVERCLOCK/TECH has grown into
      a leading gaming hardware brand across Southeast Asia. Our commitment to quality
      and our community continues to drive everything we build.
    </p>
  </section>

</main>

<?php include 'includes/footer.php'; ?>

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

  let startX = 0;
  track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
  track.addEventListener('touchend', e => {
    const diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) { stopAuto(); goTo(diff > 0 ? current + 1 : current - 1); startAuto(); }
  });

  track.parentElement.addEventListener('mouseenter', stopAuto);
  track.parentElement.addEventListener('mouseleave', startAuto);

  startAuto();

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
