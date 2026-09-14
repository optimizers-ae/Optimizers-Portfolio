  <footer class="op-footer" id="contact">
    <div class="op-footer__inner">
      <div class="op-footer__brand-zone">
        <a class="op-footer__brand" href="<?= optimizers_url('index.php#home') ?>" aria-label="Optimizers UAE home">
          <img class="op-footer__logo-file" src="<?= optimizers_url('assets/images/optimizers-uae-animated-logo.svg') ?>" alt="Optimizers United Arab Emirates">
        </a>

        <p class="op-footer__brand-note">Digital strategy, creative, media and technology—built around measurable business growth.</p>

        <div class="op-footer__social-row">
          <div class="op-footer__socials">
            <a class="op-footer__social" href="https://www.facebook.com/optimizersuae/" target="_blank" rel="noopener" aria-label="Facebook">
              <img src="<?= optimizers_url('assets/icons/brand/facebook.svg') ?>" alt="" aria-hidden="true">
            </a>
            <a class="op-footer__social" href="https://www.instagram.com/optimizersae" target="_blank" rel="noopener" aria-label="Instagram">
              <img src="<?= optimizers_url('assets/icons/brand/instagram.svg') ?>" alt="" aria-hidden="true">
            </a>
            <a class="op-footer__social" href="https://www.linkedin.com/company/optimizers-uae/" target="_blank" rel="noopener" aria-label="LinkedIn">
              <img src="<?= optimizers_url('assets/icons/brand/linkedin.svg') ?>" alt="" aria-hidden="true">
            </a>
          </div>
        </div>
      </div>

      <div class="op-footer__columns">
        <nav class="op-footer__column" aria-label="Footer overview navigation">
          <h2 class="op-footer__title">Overview</h2>
          <div class="op-footer__links">
            <a href="<?= optimizers_url('index.php#home') ?>">Home</a>
            <a href="<?= optimizers_url('about-us.php') ?>">About Us</a>
            <a href="<?= optimizers_url('case-studies.php') ?>">Case Studies</a>
            <a href="<?= optimizers_url('index.php#consultation') ?>">Free Seo Audit</a>
            <a href="<?= optimizers_url('index.php#clients') ?>">Testimonials</a>
            <a href="<?= optimizers_url('contact-us.php') ?>">Contact Us</a>
          </div>
        </nav>

        <nav class="op-footer__column" aria-label="Footer services navigation">
          <h2 class="op-footer__title">Services</h2>
          <div class="op-footer__services">
            <a href="<?= optimizers_url('index.php#seo') ?>">Search Engine Optimization</a>
            <a href="<?= optimizers_url('index.php#performance-marketing') ?>">Performance Marketing &amp; Google Ads</a>
            <a href="<?= optimizers_url('index.php#social-media-marketing') ?>">Social Media Marketing</a>
            <a href="<?= optimizers_url('business-branding-designing.php') ?>">Business Branding &amp; Designing</a>
            <a href="<?= optimizers_url('graphic-designing.php') ?>">Graphic Designing</a>
            <a href="<?= optimizers_url('content-creation-reels.php') ?>">Content Creation &amp; Reels</a>
            <a href="<?= optimizers_url('video-editing.php') ?>">Video Editing</a>
            <a href="<?= optimizers_url('whatsapp-marketing.php') ?>">WhatsApp Marketing</a>
            <a href="<?= optimizers_url('index.php#web-development') ?>">Web Development</a>
          </div>
        </nav>

        <div class="op-footer__column op-footer__column--contact">
          <h2 class="op-footer__title">Contact Us</h2>
          <div class="op-footer__contact-list">
            <a class="op-footer__contact" href="https://maps.google.com/?q=JVC+Dubai" target="_blank" rel="noopener">
              <span class="op-footer__contact-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg></span>
              <span class="op-footer__contact-copy"><span>Office</span><strong>JVC, Dubai, UAE</strong></span>
            </a>
            <a class="op-footer__contact" href="tel:+971 50 781 8373">
              <span class="op-footer__contact-icon"><img src="<?= optimizers_url('assets/icons/ui/telephone.svg') ?>" alt="" aria-hidden="true"></span>
              <span class="op-footer__contact-copy"><span>Call us</span><strong>+971 50 781 83733</strong></span>
            </a>
            <a class="op-footer__contact" href="mailto:contact@optimizers.ae">
              <span class="op-footer__contact-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
              <span class="op-footer__contact-copy"><span>Email</span><strong>contact@optimizers.ae</strong></span>
            </a>
          </div>
        </div>
      </div>

      <div class="op-footer__bottom">
        <div class="op-footer__legal">
          <a href="<?= optimizers_url('privacy-policy.php') ?>">Privacy Policy</a>
          <a href="<?= optimizers_url('terms-conditions.php') ?>">Terms &amp; Conditions</a>
        </div>
        <div class="op-footer__credit">© <?= date('Y') ?> <a href="<?= optimizers_url('index.php#home') ?>">Optimizers UAE</a>. All rights reserved.</div>
      </div>
    </div>
  </footer>
<script src="<?= optimizers_url('assets/js/glowing-effect.js?v=2') ?>" defer></script>
<script src="<?= optimizers_url('assets/js/eye-effect.js?v=eyes-centered-3') ?>" defer></script>
<script src="<?= optimizers_url('assets/js/main.js?v=eyes-centered-3') ?>" defer></script>

<style id="optimizers-sticky-whatsapp">
.opt-sticky-whatsapp{
 position:fixed;right:22px;bottom:22px;width:58px;height:58px;border-radius:50%;
 background:#25D366;display:flex;align-items:center;justify-content:center;
 z-index:99999;box-shadow:0 8px 24px rgba(0,0,0,.25);text-decoration:none;
 transition:transform .2s ease,box-shadow .2s ease;
}
.opt-sticky-whatsapp:hover{transform:translateY(-3px);box-shadow:0 12px 28px rgba(0,0,0,.3)}
.opt-sticky-whatsapp img{width:32px;height:32px;display:block}
@media(max-width:600px){.opt-sticky-whatsapp{right:15px;bottom:15px;width:54px;height:54px}.opt-sticky-whatsapp img{width:30px;height:30px}}
</style>
<a class="opt-sticky-whatsapp" href="https://wa.me/971507818373" target="_blank" rel="noopener" aria-label="Chat with Optimizers on WhatsApp">
  <img src="<?= optimizers_url('assets/icons/brand/whatsapp-sticky.svg') ?>" alt="" aria-hidden="true">
</a>

</body>
</html>
