<?php
session_start();
$activePage = 'about';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Learn about OVERCLOCK/TECH, our mission, values, and the team behind our premium gaming hardware experience.">
  <title>OVERCLOCK/TECH — About Us</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content" class="about-page">
  <section class="about-hero" aria-labelledby="about-hero-heading">
    <div class="about-hero-inner">
      <div class="about-hero-copy">
        <div class="about-eyebrow">Who We Are</div>
        <h1 id="about-hero-heading">
          Built by <span class="hi">Gamers</span>,
          Tuned for Winners.
        </h1>
        <p>
          OVERCLOCK/TECH started with a simple standard: gaming hardware should feel uncompromising from the first click to the final frame.
          We design curated setups, high-performance systems, and premium peripherals for players who care about speed, reliability, and style.
        </p>
        <div class="about-hero-actions">
          <a href="home.php" class="btn-discover">
            <span>Explore Home</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </a>
          <a href="index.php" class="btn-secondary">Back to Landing</a>
        </div>
      </div>

      <aside class="about-hero-panel" aria-label="Company highlights">
        <div class="about-panel-label">Performance Snapshot</div>
        <div class="about-panel-stat">
          <strong>50K+</strong>
          <span>Gamers Served</span>
        </div>
        <div class="about-panel-stat">
          <strong>500+</strong>
          <span>Hardware Options</span>
        </div>
        <div class="about-panel-stat">
          <strong>12+</strong>
          <span>Industry Awards</span>
        </div>
      </aside>
    </div>
  </section>

  <section class="section" aria-labelledby="story-heading">
    <div class="section-header">
      <div class="section-kicker">Our Story</div>
      <h2 class="section-title" id="story-heading">How We <span class="hi">Started</span></h2>
      <p class="section-sub">A hardware brand shaped by competitive play, obsessive tuning, and a refusal to ship average gear.</p>
    </div>

    <div class="story-grid">
      <div class="story-copy">
        <p>
          What began as a passion project between PC enthusiasts evolved into a full gaming hardware brand focused on practical performance.
          We saw too many setups that looked impressive on paper but failed under real use, so we built a company around testing, tuning, and delivering systems that hold up under pressure.
        </p>
        <p>
          Every OVERCLOCK/TECH product is selected with the same mindset: stable thermals, clean aesthetics, fast response, and a premium feel from unboxing to long-session endurance.
          Whether someone is building their first battlestation or upgrading for tournament play, the goal stays the same: make the hardware disappear so performance can take over.
        </p>
      </div>

      <div class="story-card">
        <h3>What defines us</h3>
        <p>We combine enthusiast-grade standards with a storefront experience that feels clear, fast, and built for people who know what matters.</p>
        <ul class="story-list">
          <li><strong>01</strong> Curated hardware chosen for real-world performance</li>
          <li><strong>02</strong> Design language inspired by esports and custom battlestations</li>
          <li><strong>03</strong> Quality control focused on reliability, not just specifications</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="section-full" aria-labelledby="values-heading">
    <div class="section-inner">
      <div class="section-header">
        <div class="section-kicker">Core Values</div>
        <h2 class="section-title" id="values-heading">What We <span class="hi">Stand For</span></h2>
        <p class="section-sub">The operating principles behind every machine, peripheral, and customer experience decision.</p>
      </div>

      <div class="values-grid">
        <article class="value-card">
          <div class="value-icon" aria-hidden="true">⚡</div>
          <h3>Performance First</h3>
          <p>We prioritize components and accessories that deliver measurable speed, consistency, and smooth gameplay in actual sessions.</p>
        </article>
        <article class="value-card">
          <div class="value-icon" aria-hidden="true">🛠️</div>
          <h3>Built With Care</h3>
          <p>Every build and product selection is approached with the same attention to detail enthusiasts expect from their own setups.</p>
        </article>
        <article class="value-card">
          <div class="value-icon" aria-hidden="true">🎯</div>
          <h3>Clarity Over Hype</h3>
          <p>We believe good hardware should be explained honestly, sold clearly, and matched to the right player without unnecessary noise.</p>
        </article>
        <article class="value-card">
          <div class="value-icon" aria-hidden="true">🌍</div>
          <h3>Community Driven</h3>
          <p>Feedback from competitive players, creators, and everyday customers directly influences how we improve our catalogue.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section" aria-labelledby="milestones-heading">
    <div class="section-header">
      <div class="section-kicker">Milestones</div>
      <h2 class="section-title" id="milestones-heading">Our Growth <span class="hi">Timeline</span></h2>
      <p class="section-sub">A short look at the stages that turned OVERCLOCK/TECH into a premium gaming hardware destination.</p>
    </div>

    <div class="milestones-grid">
      <article class="milestone-card">
        <div class="milestone-year">2021</div>
        <h3>First custom builds shipped</h3>
        <p>We launched with enthusiast-level gaming PCs assembled around stability, airflow, and high-refresh-rate performance.</p>
      </article>
      <article class="milestone-card">
        <div class="milestone-year">2023</div>
        <h3>Peripheral lineup expanded</h3>
        <p>Our catalogue grew to include keyboards, mice, and audio gear chosen to complete the full battlestation experience.</p>
      </article>
      <article class="milestone-card">
        <div class="milestone-year">2025</div>
        <h3>Regional recognition earned</h3>
        <p>Industry awards and esports partnerships validated our focus on dependable hardware and customer-first service.</p>
      </article>
    </div>
  </section>

  <section class="section about-cta" aria-labelledby="about-cta-heading">
    <div class="about-cta-box">
      <div class="section-kicker">Next Move</div>
      <h2 class="section-title" id="about-cta-heading">Ready to upgrade your <span class="hi">setup</span>?</h2>
      <p>
        Explore the main site, discover our featured gear, and see how OVERCLOCK/TECH approaches gaming hardware with performance and polish.
      </p>
      <div class="about-cta-actions">
        <a href="home.php" class="btn-discover">
          <span>Go to Home</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </a>
        <a href="index.php" class="btn-secondary">View Welcome Page</a>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>