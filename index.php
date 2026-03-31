<?php
// No navigation. "Find Out More" leads to index.php (Home Page)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OVERCLOCK/TECH — Welcome</title>
    <meta name="description" content="OVERCLOCK/TECH — Premium gaming hardware. Ready-to-ship gaming PCs, laptops, keyboards, mice and more.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">  
  <style>
    /* Extra styles specific to landing page only */
    .landing-scroll-hint {
      position: fixed;
      bottom: 2rem;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.35rem;
      font-family: var(--font-mono);
      font-size: 0.6rem;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      animation: fadeSlideDown 1s 1s ease both, bob 2s 2s ease-in-out infinite;
      z-index: 5;
    }
    .landing-scroll-hint svg { opacity: 0.4; }

    @keyframes bob {
      0%, 100% { transform: translateX(-50%) translateY(0); }
      50%       { transform: translateX(-50%) translateY(6px); }
    }

    .stat-row {
      display: flex;
      gap: 3rem;
      justify-content: center;
      margin-top: 3rem;
      animation: fadeSlideDown 0.8s 0.6s ease both;
    }

    .mini-stat { text-align: center; }
    .mini-stat .num {
      font-family: var(--font-display);
      font-size: 1.4rem;
      font-weight: 900;
      color: var(--neon);
      text-shadow: var(--neon-glow);
      display: block;
    }
    .mini-stat .lbl {
      font-family: var(--font-mono);
      font-size: 0.6rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
    }

    .mini-stat-div {
      width: 1px;
      background: var(--border);
      align-self: stretch;
    }

    .landing-about-link {
      display: block;
      margin-top: 0.25rem;
      border: 1px solid transparent;
      border-radius: 14px;
      padding: 1rem 1.25rem;
      transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
    }

    .landing-about-link:hover,
    .landing-about-link:focus-visible {
      transform: translateY(-3px);
      border-color: var(--border-hover);
      background: rgba(15, 15, 24, 0.55);
      outline: none;
    }

    .landing-about-link .about-link-text {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      margin-top: 1rem;
      font-family: var(--font-mono);
      font-size: 0.72rem;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: var(--neon);
    }
  </style>
</head>
<body>

<!-- Top loading bar -->
<div class="landing-loader" aria-hidden="true"></div>

<!-- Corner decorations -->
<div class="corner-deco tl" aria-hidden="true"></div>
<div class="corner-deco tr" aria-hidden="true"></div>
<div class="corner-deco bl" aria-hidden="true"></div>
<div class="corner-deco br" aria-hidden="true"></div>

<main class="landing-page" id="main-content">
  <div class="landing-inner">

    <!-- Company Name / Logo -->
    <h1 class="landing-logo">
      OVERCLOCK<span class="accent2">/</span><span class="accent">TECH</span>
    </h1>

    <div class="landing-tagline">
      &#x25b6;&nbsp; Elite Gaming Hardware &nbsp;&#x25b6;
    </div>

    <div class="landing-divider" aria-hidden="true"></div>

    <!-- About Us -->
    <div class="landing-about">
      <a href="about.php" class="landing-about-link" aria-label="Read more about OVERCLOCK TECH">
        <h2>About Us</h2>
        <p>
          OVERCLOCK/TECH is a premium gaming hardware company dedicated to delivering
          cutting-edge performance machines to competitive players and enthusiasts alike.
          We engineer every product with one goal — giving you the edge you deserve.
        </p>
        <span class="about-link-text">
          Read More
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </span>
      </a>
    </div>

    <!-- Speciality -->
    <div class="landing-specialty">
      <h2>Our Speciality</h2>
      <div class="specialty-pills">
        <a href="products.php?cat=gaming-pc" class="pill">
          <span class="icon" aria-hidden="true">🖥️</span> Gaming PCs
        </a>
        <a href="products.php?cat=laptop" class="pill">
          <span class="icon" aria-hidden="true">💻</span> Laptops
        </a>
        <a href="products.php?cat=keyboard" class="pill">
          <span class="icon" aria-hidden="true">⌨️</span> Keyboards
        </a>
        <a href="products.php?cat=mouse" class="pill">
          <span class="icon" aria-hidden="true">🖱️</span> Mice
        </a>
        <a href="products.php?cat=headset" class="pill">
          <span class="icon" aria-hidden="true">🎧</span> Headsets
        </a>
      </div>
    </div>

    <!-- Mini stats -->
    <div class="stat-row" role="group" aria-label="Company highlights">
      <div class="mini-stat">
        <span class="num">500+</span>
        <span class="lbl">Products</span>
      </div>
      <div class="mini-stat-div" aria-hidden="true"></div>
      <div class="mini-stat">
        <span class="num">50K+</span>
        <span class="lbl">Happy Gamers</span>
      </div>
      <div class="mini-stat-div" aria-hidden="true"></div>
      <div class="mini-stat">
        <span class="num">12+</span>
        <span class="lbl">Awards Won</span>
      </div>
    </div>

    <!-- CTA Button -->
    <a href="home.php" class="btn-discover" role="button">
      <span>Find Out More</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M5 12h14M12 5l7 7-7 7"/>
      </svg>
    </a>

  </div><!-- /.landing-inner -->
</main>

<!-- Scroll hint -->
<div class="landing-scroll-hint" aria-hidden="true">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M12 5v14M5 12l7 7 7-7"/>
  </svg>
  scroll
</div>

</body>
</html>
