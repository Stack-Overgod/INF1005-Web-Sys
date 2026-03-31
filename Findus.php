<?php
session_start();
$activePage = 'find-us';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Find OVERCLOCK/TECH — Visit our showroom at Singapore Institute of Technology, Punggol. Get directions, opening hours and contact details.">
  <title>OVERCLOCK/TECH — Find Us</title>
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

    .findus-hero {
      padding: 4rem 2rem 2rem;
      text-align: center;
    }

    .findus-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem;
      align-items: start;
    }

    .findus-map-wrapper {
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid var(--border);
      position: sticky;
      top: 80px;
    }

    .findus-map-wrapper iframe {
      width: 100%;
      height: 640px;
      border: none;
      display: block;
    }

    .findus-info {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .info-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 1.75rem;
      transition: border-color 0.3s;
    }

    .info-card:hover {
      border-color: var(--border-hover);
    }

    .info-card-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }

    .info-card-icon {
      font-size: 1.4rem;
    }

    .info-card-header h2 {
      font-family: var(--font-display);
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.15em;
      color: var(--neon);
      text-transform: uppercase;
      margin: 0;
    }

    .info-card p,
    .info-card address {
      color: var(--text-grey);
      font-size: 0.95rem;
      line-height: 1.8;
      font-style: normal;
    }

    .info-card a {
      color: var(--neon);
      transition: opacity 0.2s;
      text-decoration: underline;
    }

    .info-card a:hover,
    .info-card a:focus {
      opacity: 0.75;
      outline: 2px solid var(--neon);
      outline-offset: 2px;
      border-radius: 2px;
    }

    .hours-table {
      width: 100%;
      border-collapse: collapse;
    }

    .hours-table caption {
      display: none;
    }

    .hours-table tr {
      border-bottom: 1px solid var(--border);
    }

    .hours-table tr:last-child {
      border-bottom: none;
    }

    .hours-table td {
      padding: 0.5rem 0;
      color: var(--text-grey);
      font-size: 0.9rem;
    }

    .hours-table td:last-child {
      text-align: right;
      color: var(--text-white);
      font-family: var(--font-mono);
      font-size: 0.8rem;
    }

    .transport-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      padding: 0;
      margin: 0;
    }

    .transport-list li {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      color: var(--text-grey);
      font-size: 0.9rem;
      line-height: 1.6;
    }

    .transport-badge {
      font-family: var(--font-display);
      font-size: 0.6rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      padding: 0.2rem 0.5rem;
      border-radius: 4px;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .badge-mrt { background: var(--neon); color: var(--bg-black); }
    .badge-bus { background: var(--neon2); color: #fff; }
    .badge-car { background: #ffd700; color: var(--bg-black); }

    .directions-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 1rem;
      padding: 0.6rem 1.25rem;
      background: transparent;
      border: 2px solid var(--neon);
      color: var(--neon);
      font-family: var(--font-display);
      font-size: 0.7rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      border-radius: 4px;
      transition: all 0.25s;
      text-decoration: none;
    }

    .directions-btn:hover,
    .directions-btn:focus {
      background: var(--neon);
      color: var(--bg-black);
      outline: 2px solid var(--neon);
      outline-offset: 2px;
    }

    @media (max-width: 768px) {
      .findus-grid {
        grid-template-columns: 1fr;
      }

      .findus-map-wrapper iframe {
        height: 300px;
      }

      .findus-map-wrapper {
        min-height: 300px;
      }
    }
  </style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content">

  <section class="findus-hero" aria-labelledby="findus-heading">
    <div class="section-kicker">Location</div>
    <h1 class="section-title" id="findus-heading">Find <span class="hi">Us</span></h1>
    <p class="section-sub">Come visit our showroom and experience our gaming hardware in person.</p>
  </section>

  <div class="findus-grid">

    <div class="findus-map-wrapper">
      <iframe
        src="https://maps.google.com/maps?q=1.41416,103.91075&z=17&output=embed"
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Google Map showing OVERCLOCK/TECH location at Singapore Institute of Technology, 1 Punggol Coast Road, Singapore 828608">
      </iframe>
    </div>

    <div class="findus-info">

      <article class="info-card">
        <div class="info-card-header">
          <span class="info-card-icon" aria-hidden="true">📍</span>
          <h2>Our Address</h2>
        </div>
        <address>
          OVERCLOCK/TECH Showroom<br>
          Singapore Institute of Technology<br>
          1 Punggol Coast Road<br>
          Singapore 828608
        </address>
        <a href="https://maps.google.com/?q=1+Punggol+Coast+Road+Singapore+828608"
           target="_blank"
           rel="noopener noreferrer"
           class="directions-btn"
           aria-label="Get directions to OVERCLOCK/TECH on Google Maps (opens in new tab)">
          Get Directions
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" focusable="false">
            <path d="M5 12h14M12 5l7 7-7 7"/>
          </svg>
        </a>
      </article>

      <article class="info-card">
        <div class="info-card-header">
          <span class="info-card-icon" aria-hidden="true">📞</span>
          <h2>Contact Us</h2>
        </div>
        <p>
          Phone: <a href="tel:+6567676767" aria-label="Call us at plus 65 6767 6767">+65 6767 6767</a><br>
          WhatsApp: <a href="https://wa.me/6576767676" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp us at plus 65 7676 7676 (opens in new tab)">+65 7676 7676</a><br>
          Email: <a href="mailto:support@overclocktech.sg" aria-label="Email us at support at overclocktech dot sg">support@overclocktech.sg</a>
        </p>
      </article>

      <article class="info-card">
        <div class="info-card-header">
          <span class="info-card-icon" aria-hidden="true">🕐</span>
          <h2>Opening Hours</h2>
        </div>
        <table class="hours-table">
          <caption>OVERCLOCK/TECH showroom opening hours</caption>
          <thead class="visually-hidden">
            <tr>
              <th scope="col">Day</th>
              <th scope="col">Hours</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">Monday – Friday</th>
              <td>10:00 AM – 9:00 PM</td>
            </tr>
            <tr>
              <th scope="row">Saturday</th>
              <td>10:00 AM – 8:00 PM</td>
            </tr>
            <tr>
              <th scope="row">Sunday</th>
              <td>11:00 AM – 6:00 PM</td>
            </tr>
            <tr>
              <th scope="row">Public Holidays</th>
              <td>Closed</td>
            </tr>
          </tbody>
        </table>
      </article>

      <article class="info-card">
        <div class="info-card-header">
          <span class="info-card-icon" aria-hidden="true">🚆</span>
          <h2>Getting Here</h2>
        </div>
        <ul class="transport-list" aria-label="Transport options to reach OVERCLOCK/TECH">
          <li>
            <span class="transport-badge badge-mrt" aria-hidden="true">MRT</span>
            <span><span class="visually-hidden">MRT: </span>Punggol Coast MRT Station — Exit 1, right at the doorstep</span>
          </li>
          <li>
            <span class="transport-badge badge-bus" aria-hidden="true">BUS</span>
            <span><span class="visually-hidden">Bus: </span>Bus 34, 117 or 117M — Alight at Punggol Coast Bus Interchange</span>
          </li>
          <li>
            <span class="transport-badge badge-car" aria-hidden="true">CAR</span>
            <span><span class="visually-hidden">Car: </span>Parking available at SIT Punggol Campus carpark — season and coupon parking available</span>
          </li>
        </ul>
      </article>

    </div>
  </div>

</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>