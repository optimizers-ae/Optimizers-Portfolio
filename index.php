<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

// Front Router for pretty URLs when requests hit index.php (e.g., PHP built-in server or Apache fallback)
$rawRequestUri = $_SERVER['REQUEST_URI'] ?? '';
$parsedPath = parse_url($rawRequestUri, PHP_URL_PATH) ?? '';

if ($parsedPath !== '') {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $relPath = $parsedPath;
    if ($baseDir !== '' && $baseDir !== '/' && strpos($relPath, $baseDir) === 0) {
        $relPath = substr($relPath, strlen($baseDir));
    }
    $relPath = '/' . ltrim($relPath, '/');

    // Route: /case-studies/{slug} or /case-study/{slug}
    if (preg_match('~^/case-studies/([a-zA-Z0-9_-]+)/?$~', $relPath, $m)) {
        $targetSlug = rawurldecode($m[1]);
        if ($targetSlug !== 'case-studies.php' && $targetSlug !== 'index.php') {
            $_GET['slug'] = $targetSlug;
            require __DIR__ . '/case-study.php';
            exit;
        }
    } elseif (preg_match('~^/case-study/([a-zA-Z0-9_-]+)/?$~', $relPath, $m)) {
        $targetSlug = rawurldecode($m[1]);
        if ($targetSlug !== 'case-study.php' && $targetSlug !== 'index.php') {
            $_GET['slug'] = $targetSlug;
            require __DIR__ . '/case-study.php';
            exit;
        }
    }

    // Route: /case-studies or /case-studies/
    if ($relPath === '/case-studies' || $relPath === '/case-studies/') {
        require __DIR__ . '/case-studies.php';
        exit;
    }

    // Route: /services/{slug} or /service/{slug}
    if (preg_match('~^/services/([a-zA-Z0-9_-]+)/?$~', $relPath, $m)) {
        $targetSlug = rawurldecode($m[1]);
        if ($targetSlug !== 'service.php' && $targetSlug !== 'service-detail.php' && $targetSlug !== 'index.php') {
            $_GET['slug'] = $targetSlug;
            require __DIR__ . '/service.php';
            exit;
        }
    } elseif (preg_match('~^/service/([a-zA-Z0-9_-]+)/?$~', $relPath, $m)) {
        $targetSlug = rawurldecode($m[1]);
        if ($targetSlug !== 'service.php' && $targetSlug !== 'index.php') {
            $_GET['slug'] = $targetSlug;
            require __DIR__ . '/service.php';
            exit;
        }
    }

    // Route: /services or /services/
    if ($relPath === '/services' || $relPath === '/services/') {
        header('Location: ' . optimizers_url('index.php#services'));
        exit;
    }
}

$pageTitle = 'Digital Marketing Consultancy in Dubai | Optimizers UAE';
$pageDescription = 'Optimizers helps UAE brands grow through SEO, paid advertising, social media and web development.';

try {
    $db = OptimizersDB::getConnection();
    $statsRows = $db->query("SELECT * FROM stats_footprint ORDER BY display_order ASC")->fetchAll();
    $statsMap = [];
    foreach ($statsRows as $r) {
        $statsMap[$r['stat_key']] = $r;
    }
} catch (Throwable $e) {
    $statsMap = [];
}
require __DIR__ . '/header/header.php';
?>

<main>
  <section class="op-hero" id="home">

    <div class="op-hero__media" aria-hidden="true">
      <img
        id="heroImage"
        src="assets/images/hero-main.webp?v=2"
        alt=""
        onerror="this.style.display='none'"
      >
    </div>

    <div class="op-hero__overlay" aria-hidden="true"></div>
    <div class="op-hero__glow-layer" id="heroGlowLayer" aria-hidden="true"></div>

    <div class="op-hero__content">
      <div class="op-hero__brand">Optimizers UAE</div>

      <h1>
        Digital Marketing<br>
        <span>Consultancy in Dubai.</span>
      </h1>

      <p class="op-hero__lead">
        Helping brands grow through smart digital marketing solutions in the UAE.
      </p>

      <p class="op-hero__description">
        From our JVC office, we combine local market knowledge with data-led execution.
        Our team connects strategy, creative, media and technology around one clear goal:
        helping your business attract the right audience and convert attention into revenue.
      </p>

      <p class="op-hero__micro">
        SEO · PAID ADVERTISING · SOCIAL MEDIA · WEB DEVELOPMENT
      </p>

      <div class="op-hero__buttons">
        <a href="#consultation" class="op-btn op-btn--light">
          Free Consultation
        </a>
        <a href="#services" class="op-btn op-btn--outline">
          Explore Services
        </a>
      </div>
    </div>

    <div class="op-hero__bottom">
      <span>JVC · DUBAI</span>
      <span>SERVING ALL 7 UAE EMIRATES</span>
    </div>

  </section>

  <section class="op-consult" id="about">
    <div class="op-consult__glow op-consult__glow--one" aria-hidden="true"></div>
    <div class="op-consult__glow op-consult__glow--two" aria-hidden="true"></div>

    <div class="op-consult__inner">
      <div class="op-consult__shell" data-reveal>
        <div class="op-consult__visual" aria-hidden="true">
          <div class="op-consult__visual-grid"></div>
          <div class="op-consult__orbit">
            <span></span>
            <span></span>
            <span></span>
            <img class="op-animated-logo op-animated-logo--visual" src="assets/images/optimizers-uae-animated-logo.svg" alt="">
          </div>
        </div>

        <div class="op-consult__form-panel" id="consultation">
          <div class="op-consult__form-head">
            <span>Start a conversation</span>
            <h3>Free consultation</h3>
            <p>Share your requirements and our team will contact you.</p>
          </div>

          <form class="op-consult__form" action="send-request.php" method="post">
            <?php
              $formStatus = (string) ($_GET['form'] ?? '');
              $formMessages = [
                'success' => ['success', 'Thank you. Your request has been sent successfully.'],
                'invalid' => ['error', 'Please check the form and complete every required field.'],
                'config' => ['error', 'Email setup is incomplete. Please contact us on WhatsApp.'],
                'error' => ['error', 'We could not send your request. Please try again or contact us on WhatsApp.'],
              ];
              if (isset($formMessages[$formStatus])):
                [$formClass, $formMessage] = $formMessages[$formStatus];
            ?>
              <div class="op-form-status op-form-status--<?= $formClass ?>" role="status"><?= htmlspecialchars($formMessage, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <div class="op-form-trap" aria-hidden="true"><label for="website-company">Company website</label><input type="text" id="website-company" name="website_company" tabindex="-1" autocomplete="off"></div>
            <div class="op-consult__form-row">
              <div class="op-consult__field">
                <label for="consult-name">Your Name</label>
                <input type="text" id="consult-name" name="name" placeholder="Enter your name" autocomplete="name" required>
              </div>

              <div class="op-consult__field">
                <label for="consult-email">Corporate Email</label>
                <input type="email" id="consult-email" name="email" placeholder="name@company.com" autocomplete="email" required>
              </div>
            </div>

            <div class="op-consult__form-row">
              <div class="op-consult__field">
                <label for="consult-phone">Phone Number</label>
                <input type="tel" id="consult-phone" name="phone" placeholder="+971" autocomplete="tel" required>
              </div>

              <div class="op-consult__field">
                <label for="consult-budget">Monthly Budget</label>
                <select id="consult-budget" name="budget" required>
                  <option value="" selected disabled>Select budget</option>
                  <option value="5k-10k">AED 5k – 10k</option>
                  <option value="10k-25k">AED 10k – 25k</option>
                  <option value="25k-50k">AED 25k – 50k</option>
                  <option value="50k-plus">AED 50k+</option>
                </select>
              </div>
            </div>

            <div class="op-consult__field">
              <label for="consult-message">Tell Us About Your Project</label>
              <textarea id="consult-message" name="message" placeholder="What would you like to achieve?" rows="4" required></textarea>
            </div>

            <button class="op-consult__submit" type="submit">Send Request</button>
            <p class="op-consult__privacy">No spam. Your information stays private.</p>
          </form>
        </div>
      </div>
    </div>
  </section>

  <?php require __DIR__ . '/sections/services.php'; ?>

  <?php require __DIR__ . '/sections/case_studies.php'; ?>

  <?php require __DIR__ . '/sections/industries.php'; ?>

  <?php require __DIR__ . '/sections/real_numbers.php'; ?>

  <?php require __DIR__ . '/sections/process.php'; ?>

  <section class="op-services op-services--footprint" id="impact" aria-labelledby="services-title">
    <div class="op-services__glow" aria-hidden="true"></div>

    <div class="op-services__inner">
      <header class="op-services__head" data-reveal>
        <span class="op-services__eyebrow">What we do</span>
        <h2 id="services-title">Full-stack digital growth.</h2>
        <p>From search to social. Strategy, media, creative and commerce under one roof.</p>
      </header>

      <div class="op-services__grid">
        <article class="op-service-card" data-reveal style="--reveal-delay: .04s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/google.svg" alt=""></span>
          <h3>SEO</h3>
          <p>Higher rankings, local visibility and qualified organic traffic.</p>
        </article>

        <article class="op-service-card" data-reveal style="--reveal-delay: .09s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/adobe-illustrator.svg" alt=""></span>
          <h3>Branding &amp; designing</h3>
          <p>Logos, visual systems and campaign-ready creative.</p>
        </article>

        <article class="op-service-card" id="paid-advertising" data-reveal style="--reveal-delay: .14s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/google.svg" alt=""></span>
          <h3>PPC / Google Ads</h3>
          <p>High-intent Google campaigns built for measurable returns.</p>
        </article>

        <article class="op-service-card" data-reveal style="--reveal-delay: .19s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/google.svg" alt=""></span>
          <h3>Performance marketing</h3>
          <p>Full-funnel campaigns focused on leads and conversions.</p>
        </article>

        <article class="op-service-card" id="content-marketing" data-reveal style="--reveal-delay: .24s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/medium.svg" alt=""></span>
          <h3>Content marketing</h3>
          <p>Strategic content that builds attention and trust.</p>
        </article>

        <article class="op-service-card" id="lead-generation" data-reveal style="--reveal-delay: .04s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/messenger.svg" alt=""></span>
          <h3>Lead generation</h3>
          <p>Targeted campaigns that attract better prospects.</p>
        </article>

        <article class="op-service-card" id="ecommerce-solutions" data-reveal style="--reveal-delay: .09s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/shopify.svg" alt=""></span>
          <h3>E-commerce solutions</h3>
          <p>Growth campaigns for stores, sales and retention.</p>
        </article>

        <article class="op-service-card" id="linkedin-marketing" data-reveal style="--reveal-delay: .14s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/linkedin.svg" alt=""></span>
          <h3>LinkedIn marketing</h3>
          <p>B2B content and campaigns for decision-makers.</p>
        </article>

        <article class="op-service-card" id="tiktok-marketing" data-reveal style="--reveal-delay: .19s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/tiktok.svg" alt=""></span>
          <h3>TikTok marketing</h3>
          <p>Platform-native creative built for reach and action.</p>
        </article>

        <article class="op-service-card" data-reveal style="--reveal-delay: .24s">
          <span class="op-service-card__icon" aria-hidden="true"><img src="assets/icons/brand/whatsapp.svg" alt=""></span>
          <h3>WhatsApp marketing</h3>
          <p>Direct campaigns, automation and customer conversations.</p>
        </article>
      </div>

      <div class="op-footprint__layout" data-reveal>
      <div class="op-stats" aria-label="Optimizers UAE achievements">
        <div class="op-stat">
          <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="10"/><circle cx="16" cy="16" r="5"/><circle cx="16" cy="16" r="1"/></svg>
          <strong><?= htmlspecialchars($statsMap['brands_grown']['value'] ?? '200+', ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($statsMap['brands_grown']['label'] ?? 'Brands Grow Across UAE', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="op-stat">
          <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M5 25V8M5 25h22M9 20l5-6 5 3 7-9M21 8h5v5"/></svg>
          <strong><?= htmlspecialchars($statsMap['projects_delivered']['value'] ?? '300+', ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($statsMap['projects_delivered']['label'] ?? 'Projects Successfully Delivered', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="op-stat">
          <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 28S7 19.5 7 12a9 9 0 1 1 18 0c0 7.5-9 16-9 16Z"/><circle cx="16" cy="12" r="3"/></svg>
          <strong><?= htmlspecialchars($statsMap['emirates_covered']['value'] ?? '7', ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($statsMap['emirates_covered']['label'] ?? 'UAE Emirates Covered', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="op-stat">
          <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="11"/><path d="M16 9v8l5 3"/></svg>
          <strong><?= htmlspecialchars($statsMap['years_expertise']['value'] ?? '14+', ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($statsMap['years_expertise']['label'] ?? 'Years Combined Expertise', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
      </div>

      <section class="op-uae-map" aria-labelledby="uae-map-title">
        <div class="op-uae-map__head">
          <div>
            <span class="op-services__eyebrow">Our UAE footprint</span>
            <h2 id="uae-map-title">Built in Dubai.<br><span>Growing across the Emirates.</span></h2>
          </div>
          <div class="op-uae-map__capacity">
            <span>Client network</span>
            <strong><?= htmlspecialchars($statsMap['client_network']['value'] ?? '200+', ENT_QUOTES, 'UTF-8') ?></strong>
          </div>
        </div>

        <div class="op-uae-map__canvas">
          <div class="op-uae-map__grid" aria-hidden="true"></div>
          <svg class="op-uae-map__svg" viewBox="250 35 650 350" role="img" aria-label="Complete UAE map with Dubai marked as the primary client hub">
            <defs>
              <pattern id="uaeDots" width="17" height="17" patternUnits="userSpaceOnUse">
                <circle cx="4" cy="4" r="3.3" fill="currentColor"/>
              </pattern>
              <filter id="dubaiGlow" x="-100%" y="-100%" width="300%" height="300%">
                <feGaussianBlur stdDeviation="16" result="blur"/>
                <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
              </filter>
            </defs>

            <path class="op-uae-map__country" d="M677.772 105.622 682.737 112.407 683.289 159.19 684.668 162.419 681.909 163.006 678.875 166.527 675.29 171.952 670.6 174.737 666.739 177.961 662.877 181.917 659.843 182.795 655.43 177.815 652.672 172.685 653.223 171.513 655.43 171.219 656.257 168.58 654.878 164.62 652.12 163.299 648.534 163.153 644.948 164.766 641.363 168.287 639.156 171.806 638.88 179.133 639.983 187.481 639.708 191.432 637.777 196.406 637.225 203.717 638.604 209.416 639.708 212.775 639.983 215.549 636.398 224.597 639.432 226.202 649.362 226.931 652.396 232.909 654.326 237.135 653.775 239.612 646.603 241.505 638.053 243.544 631.708 242.961 620.124 245.582 614.055 249.804 615.986 252.568 617.917 254.46 619.02 260.131 617.09 267.98 614.055 275.677 609.918 285.256 605.229 296.13 598.885 312.638 593.644 325.653 593.092 334.9 593.092 340.964 592.541 353.081 587.3 359.712 586.197 360 580.128 359.135 578.198 358.847 572.405 358.127 563.303 356.974 551.718 355.388 537.651 353.514 522.204 351.495 505.93 349.332 488.829 347.024 471.727 344.716 455.178 342.551 439.731 340.531 425.94 338.654 414.079 337.21 405.253 335.911 399.46 335.189 397.254 334.9 390.91 334.033 387.324 329.555 383.186 324.063 379.049 318.714 374.911 313.217 370.498 307.717 366.361 302.215 362.223 296.854 357.81 291.346 353.673 285.836 349.535 280.322 345.398 274.952 340.984 269.433 336.847 263.911 332.71 258.532 328.296 253.005 324.159 247.475 320.021 241.942 317.263 238.301 315.608 234.221 315.332 223.284 315.332 220.95 318.091 216.571 319.47 219.636 322.78 223.868 328.02 222.847 330.503 223.576 332.434 238.592 336.295 243.981 341.26 246.165 357.534 247.329 367.464 245.291 387.6 235.532 398.081 231.888 427.043 232.617 450.213 236.698 486.346 239.029 493.518 238.446 512.826 230.576 524.963 223.576 532.134 221.533 536.823 214.819 539.857 206.055 542.616 200.355 546.201 197.576 549.511 192.749 551.994 184.699 558.89 176.789 585.645 157.282 601.367 140.679 602.746 135.384 611.573 127.289 618.193 118.599 650.465 93.368 656.809 83.022 660.671 71.333 660.946 70.444 663.705 70 667.566 71.777 668.118 80.508 666.739 88.788 666.463 97.504 665.911 102.228 668.945 106.065 673.91 107.688 676.117 107.54ZM676.393 140.826 676.945 137.149 676.117 135.237 672.807 134.943 671.428 138.179 670.876 142.738 673.359 143.032ZM450.489 223.284 444.696 223.722 439.731 220.512 450.489 216.279 453.523 214.381 456.557 210.438 459.039 213.797 456.281 219.053 454.35 221.387ZM395.599 220.658 394.219 221.242 393.116 216.717 393.116 215.403 396.702 213.213 398.633 217.009ZM496 229.993 496 232.909 488.277 232.034 486.346 233.492 479.727 232.763 473.658 230.722 477.796 227.223 488.829 223.138 493.518 226.931ZM537.375 207.662 536.547 209.416 534.341 209.269 529.1 207.662 527.169 205.324 530.755 202.548 532.134 202.401 534.341 205.324Z"/>
            <path class="op-uae-map__dots" d="M677.772 105.622 682.737 112.407 683.289 159.19 684.668 162.419 681.909 163.006 678.875 166.527 675.29 171.952 670.6 174.737 666.739 177.961 662.877 181.917 659.843 182.795 655.43 177.815 652.672 172.685 653.223 171.513 655.43 171.219 656.257 168.58 654.878 164.62 652.12 163.299 648.534 163.153 644.948 164.766 641.363 168.287 639.156 171.806 638.88 179.133 639.983 187.481 639.708 191.432 637.777 196.406 637.225 203.717 638.604 209.416 639.708 212.775 639.983 215.549 636.398 224.597 639.432 226.202 649.362 226.931 652.396 232.909 654.326 237.135 653.775 239.612 646.603 241.505 638.053 243.544 631.708 242.961 620.124 245.582 614.055 249.804 615.986 252.568 617.917 254.46 619.02 260.131 617.09 267.98 614.055 275.677 609.918 285.256 605.229 296.13 598.885 312.638 593.644 325.653 593.092 334.9 593.092 340.964 592.541 353.081 587.3 359.712 586.197 360 580.128 359.135 578.198 358.847 572.405 358.127 563.303 356.974 551.718 355.388 537.651 353.514 522.204 351.495 505.93 349.332 488.829 347.024 471.727 344.716 455.178 342.551 439.731 340.531 425.94 338.654 414.079 337.21 405.253 335.911 399.46 335.189 397.254 334.9 390.91 334.033 387.324 329.555 383.186 324.063 379.049 318.714 374.911 313.217 370.498 307.717 366.361 302.215 362.223 296.854 357.81 291.346 353.673 285.836 349.535 280.322 345.398 274.952 340.984 269.433 336.847 263.911 332.71 258.532 328.296 253.005 324.159 247.475 320.021 241.942 317.263 238.301 315.608 234.221 315.332 223.284 315.332 220.95 318.091 216.571 319.47 219.636 322.78 223.868 328.02 222.847 330.503 223.576 332.434 238.592 336.295 243.981 341.26 246.165 357.534 247.329 367.464 245.291 387.6 235.532 398.081 231.888 427.043 232.617 450.213 236.698 486.346 239.029 493.518 238.446 512.826 230.576 524.963 223.576 532.134 221.533 536.823 214.819 539.857 206.055 542.616 200.355 546.201 197.576 549.511 192.749 551.994 184.699 558.89 176.789 585.645 157.282 601.367 140.679 602.746 135.384 611.573 127.289 618.193 118.599 650.465 93.368 656.809 83.022 660.671 71.333 660.946 70.444 663.705 70 667.566 71.777 668.118 80.508 666.739 88.788 666.463 97.504 665.911 102.228 668.945 106.065 673.91 107.688 676.117 107.54ZM676.393 140.826 676.945 137.149 676.117 135.237 672.807 134.943 671.428 138.179 670.876 142.738 673.359 143.032ZM450.489 223.284 444.696 223.722 439.731 220.512 450.489 216.279 453.523 214.381 456.557 210.438 459.039 213.797 456.281 219.053 454.35 221.387ZM395.599 220.658 394.219 221.242 393.116 216.717 393.116 215.403 396.702 213.213 398.633 217.009ZM496 229.993 496 232.909 488.277 232.034 486.346 233.492 479.727 232.763 473.658 230.722 477.796 227.223 488.829 223.138 493.518 226.931ZM537.375 207.662 536.547 209.416 534.341 209.269 529.1 207.662 527.169 205.324 530.755 202.548 532.134 202.401 534.341 205.324Z"/>

            <g class="op-uae-map__dubai" transform="translate(599 143.4)">
              <circle class="op-uae-map__pulse op-uae-map__pulse--two" r="39"/>
              <circle class="op-uae-map__pulse" r="25"/>
              <circle class="op-uae-map__marker" r="10" filter="url(#dubaiGlow)"/>
              <circle class="op-uae-map__core" r="3.5"/>
              <path class="op-uae-map__line" d="M13 0h44"/>
              <g class="op-uae-map__label" transform="translate(57 -24)">
                <rect width="178" height="49" rx="12"/>
                <text x="17" y="21">DUBAI</text>
                <text class="op-uae-map__label-sub" x="17" y="37">PRIMARY CLIENT HUB</text>
              </g>
            </g>
          </svg>

          <div class="op-uae-map__meta">
            <span>25.2048° N</span>
            <span>55.2708° E</span>
          </div>
        </div>
      </section>
      </div>
    </div>
  </section>

  <section class="op-clients" id="clients" aria-labelledby="clients-title">
    <div class="op-clients__inner">
      <header class="op-clients__head" data-reveal>
        <div>
          <span class="op-clients__eyebrow">The clients who trust us</span>
          <h2 id="clients-title">Trusted by 350+<em>businesses in UAE.</em></h2>
        </div>
        <p class="op-clients__intro">
          We work with brands from diverse sectors, helping them strengthen their digital presence and grow online with confidence.
        </p>
        <div class="op-clients__controls" aria-label="Client slider controls">
          <button class="op-clients__arrow" id="clientsPrev" type="button" aria-label="Previous clients">
            <svg class="op-ui-arrow op-ui-arrow--left" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
          </button>
          <button class="op-clients__arrow" id="clientsNext" type="button" aria-label="Next clients">
            <svg class="op-ui-arrow" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
          </button>
        </div>
      </header>

      <div class="op-clients__viewport" data-reveal>
        <div class="op-clients__track" id="clientsTrack" tabindex="0" aria-label="Trusted client logos">
          <!-- Client cards are generated from the editable logo list in assets/js/main.js. -->
        </div>
      </div>
      <div class="op-clients__progress" aria-hidden="true"><span id="clientsProgress"></span></div>
    </div>
  </section>

  <?php require __DIR__ . '/sections/technology.php'; ?>

  <?php require __DIR__ . '/sections/team.php'; ?>

  <?php require __DIR__ . '/sections/coverage.php'; ?>

  <?php require __DIR__ . '/sections/faq.php'; ?>

  <?php require __DIR__ . '/sections/cta.php'; ?>
</main>

<?php require __DIR__ . '/footer/footer.php'; ?>
