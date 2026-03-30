<?php
session_start();
$activePage = 'faq';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="OVERCLOCK/TECH — FAQ and Support. Find answers about shipping, returns, and custom PC builds, or contact our support team.">
  <title>OVERCLOCK/TECH — FAQ & Support</title>
  <link rel="stylesheet" href="css/style.css">
  
  <style>
    /* Scoped styles for the FAQ Accordion */
    .faq-list {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
      max-width: 800px;
      margin: 0 auto 4rem;
    }

    .faq-item {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 12px;
      transition: all 0.3s ease;
      overflow: hidden;
    }

    .faq-item:hover {
      border-color: var(--neon);
      box-shadow: var(--neon-glow);
      transform: translateY(-2px);
    }

    .faq-item summary {
      padding: 1.5rem;
      font-family: var(--font-display);
      font-size: 1rem;
      font-weight: 700;
      color: var(--text-white);
      letter-spacing: 0.08em;
      cursor: pointer;
      list-style: none; /* Removes default arrow */
      display: flex;
      justify-content: space-between;
      align-items: center;
      text-transform: uppercase;
    }

    .faq-item summary::-webkit-details-marker {
      display: none; /* Removes Safari default arrow */
    }

    .faq-item summary::after {
      content: '+';
      color: var(--neon);
      font-size: 1.8rem;
      line-height: 1;
      font-family: var(--font-mono);
      transition: transform 0.3s ease;
    }

    .faq-item[open] summary::after {
      content: '−';
      color: var(--neon2);
      transform: rotate(180deg);
    }

    .faq-item[open] {
      border-color: var(--border-hover);
    }

    .faq-answer {
      padding: 0 1.5rem 1.5rem;
      color: var(--text-grey);
      font-size: 0.95rem;
      line-height: 1.8;
    }

    /* Wrapper to center the contact form */
    .contact-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }
  </style>
</head>
<body>

<?php include 'includes/nav.php'; ?>

<main id="main-content">

  <section class="section" aria-labelledby="faq-heading">
    <div class="section-header">
      <div class="section-kicker">Knowledge Base</div>
      <h1 class="section-title" id="faq-heading">
        FAQ <span class="hi">&</span> Support
      </h1>
      <p class="section-sub">Everything you need to know about our gear, shipping, and warranties.</p>
    </div>

    <div class="faq-list">
      
      <details class="faq-item">
        <summary>What payment methods are accepted?</summary>
        <div class="faq-answer">
          We accept all major credit cards (Visa, MasterCard, Amex), PayPal, and select crypto wallets. All transactions are secured with military-grade 256-bit encryption.
        </div>
      </details>

      <details class="faq-item">
        <summary>How long does shipping take?</summary>
        <div class="faq-answer">
          Standard shipping typically takes 3-5 business days within the region. Pre-built PCs ship within 24–48 hours. Custom rigs require an additional 3 days for assembly and stress-testing.
        </div>
      </details>

      <details class="faq-item">
        <summary>What is your return policy?</summary>
        <div class="faq-answer">
          We offer a 30-day money-back guarantee. If your hardware doesn't meet your expectations, return it in its original packaging. Return shipping costs apply unless the hardware arrived defective.
        </div>
      </details>

      <details class="faq-item">
        <summary>Do custom builds come with a warranty?</summary>
        <div class="faq-answer">
          Yes! All OVERCLOCK custom builds and pre-builts come with a comprehensive 2-year warranty covering parts and labor. We also provide lifetime technical support for all our systems.
        </div>
      </details>

    </div>
  </section>

  <section class="section-full" aria-labelledby="contact-heading">
    <div class="section-inner contact-wrapper">
      
      <div class="auth-card" style="max-width: 600px; width: 100%;">
        <h2 class="auth-heading" id="contact-heading" style="text-align: center;">Still Need Help?</h2>
        <p class="auth-subtext" style="text-align: center;">Transmit a message to our support deck. We usually reply within 24 hours.</p>

        <form action="process_contact.php" method="POST">
          
          <div class="auth-form-row">
            <div class="auth-form-group">
              <label class="auth-form-label" for="name">Name</label>
              <input type="text" id="name" name="name" class="auth-form-input" placeholder="Enter your handle" required>
            </div>
            <div class="auth-form-group">
              <label class="auth-form-label" for="email">Email Address</label>
              <input type="email" id="email" name="email" class="auth-form-input" placeholder="Enter your email" required>
            </div>
          </div>

          <div class="auth-form-group">
            <label class="auth-form-label" for="question">Your Question</label>
            <textarea id="question" name="question" class="auth-form-input" rows="5" placeholder="Describe your issue..." style="resize: vertical;" required></textarea>
          </div>

          <button type="submit" class="btn-auth">
            <span>Send Transmission</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left: 8px;"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>

        </form>
      </div>

    </div>
  </section>

</main><?php include 'includes/footer.php'; ?>

<script src="js/faq.js" defer>
</script>

</body>
</html>