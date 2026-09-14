<?php
$pageTitle = 'Contact Optimizers | Digital Marketing Company Dubai';
$pageDescription = 'Contact Optimizers in Dubai to discuss your website, digital solutions, digital marketing requirements, or UAE business growth goals.';
$currentPage = 'contact-us';
$isCompanyPage = true;
$formStatus = (string) ($_GET['form'] ?? '');
$formMessages = [
  'success' => ['success', 'Thank you. Your message has been sent successfully.'],
  'invalid' => ['error', 'Please check the form and complete every required field.'],
  'config' => ['error', 'Email setup is incomplete. Please contact us on WhatsApp.'],
  'error' => ['error', 'We could not send your message. Please try again or contact us on WhatsApp.'],
];
require __DIR__ . '/header/header.php';
?>

<main class="op-company-page op-contact-page">
  <section class="cp-contact-hero" aria-labelledby="contact-page-title">
    <div class="cp-grid" aria-hidden="true"></div>
    <div class="cp-contact-orbit" aria-hidden="true"><span>24H</span></div>
    <div class="cp-shell cp-contact-layout">
      <div class="cp-contact-copy">
        <span class="cp-eyebrow" data-reveal>Get in Touch</span>
        <h1 id="contact-page-title" data-reveal>Every successful partnership starts with<span>a conversation.</span></h1>
        <p class="cp-contact-intro" data-reveal>We’d love to hear about your project, ideas, or business goals. Whether you need a website, digital solutions, or expert guidance, our team is here to help you every step of the way.</p>

        <div class="cp-contact-list" data-reveal aria-label="Contact information">
          <a class="cp-contact-item" href="mailto:contact@optimizers.ae"><span class="cp-detail-index">01</span><span class="cp-detail-text"><small>Email</small><strong>contact@optimizers.ae</strong></span><span class="cp-detail-arrow" aria-hidden="true">↗</span></a>
          <a class="cp-contact-item" href="tel:+971 50 781 8373"><span class="cp-detail-index">02</span><span class="cp-detail-text"><small>Phone</small><strong>+971 50 781 83733</strong></span><span class="cp-detail-arrow" aria-hidden="true">↗</span></a>
          <a class="cp-contact-item" href="https://maps.google.com/?q=JVC+Dubai" target="_blank" rel="noopener"><span class="cp-detail-index">03</span><span class="cp-detail-text"><small>Office</small><strong>Office JVC Dubai</strong></span><span class="cp-detail-arrow" aria-hidden="true">↗</span></a>
        </div>

        <div class="cp-social-row" data-reveal aria-label="Social platforms"><span>Follow us</span><div><a href="https://www.facebook.com/optimizersuae/" target="_blank" rel="noopener">Facebook</a><a href="https://www.instagram.com/optimizersae" target="_blank" rel="noopener">Instagram</a><a href="https://www.linkedin.com/company/optimizers-uae/" target="_blank" rel="noopener">LinkedIn</a></div></div>
      </div>

      <div class="cp-form-wrap" id="contact-form" data-reveal>
        <div class="cp-form-glow" aria-hidden="true"></div>
        <div class="cp-form-head"><span class="cp-form-kicker">Send a Message</span><h2>Let&apos;s start your project</h2><span class="cp-form-number" aria-hidden="true">01</span></div>

        <form class="cp-contact-form" action="send-request.php" method="post">
          <input type="hidden" name="form_source" value="contact">
          <div class="op-form-trap" aria-hidden="true"><label for="contact-website-company">Company website</label><input type="text" id="contact-website-company" name="website_company" tabindex="-1" autocomplete="off"></div>
          <?php if (isset($formMessages[$formStatus])): [$statusClass, $statusMessage] = $formMessages[$formStatus]; ?>
            <div class="op-form-status op-form-status--<?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>" role="status"><?= htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>

          <div class="cp-field-row">
            <label class="cp-field" for="contact-name"><span>Full Name</span><input id="contact-name" type="text" name="name" placeholder="Enter your full name" autocomplete="name" maxlength="100" required></label>
            <label class="cp-field" for="contact-email"><span>Email Address</span><input id="contact-email" type="email" name="email" placeholder="your@email.com" autocomplete="email" required></label>
          </div>
          <label class="cp-field" for="contact-subject"><span>Subject</span><input id="contact-subject" type="text" name="subject" placeholder="Tell us briefly about your project" maxlength="180" required></label>
          <label class="cp-field" for="contact-message"><span>Message</span><textarea id="contact-message" name="message" rows="5" placeholder="Share details about your requirements, goals, or ideas..." maxlength="5000" required></textarea></label>
          <div class="cp-form-actions"><button type="submit">Send Message <span aria-hidden="true">→</span></button><p><span aria-hidden="true"></span>We typically respond within 24 hours.</p></div>
        </form>
      </div>
    </div>
  </section>

  <section class="cp-assurance-section" aria-label="Why contact Optimizers">
    <div class="cp-shell cp-assurance-grid">
      <article class="cp-assurance-card" data-reveal><span class="cp-assurance-number">01</span><div><h2>Quick Response</h2><p>We respond to all inquiries within 24 hours to keep your project moving forward without delays.</p></div></article>
      <article class="cp-assurance-card" data-reveal><span class="cp-assurance-number">02</span><div><h2>Free Consultation</h2><p>Book a free consultation with our experts and get clear guidance for your business needs.</p></div></article>
      <article class="cp-assurance-card" data-reveal><span class="cp-assurance-number">03</span><div><h2>Dedicated Support</h2><p>Enjoy reliable support throughout your project journey, from planning to final delivery.</p></div></article>
    </div>
  </section>
</main>

<?php require __DIR__ . '/footer/footer.php'; ?>
