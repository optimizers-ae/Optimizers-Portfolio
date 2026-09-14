<?php
if (!isset($servicePage) || !is_array($servicePage)) {
  http_response_code(500);
  exit('Service page configuration is missing.');
}

$pageTitle = $servicePage['pageTitle'];
$pageDescription = $servicePage['pageDescription'];
$currentPage = $servicePage['slug'];
$isServicePage = true;
$e = static function ($value) {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$faqSchema = [
  '@context' => 'https://schema.org',
  '@type' => 'FAQPage',
  'mainEntity' => array_map(static function (array $faq): array {
    return [
      '@type' => 'Question',
      'name' => $faq['question'],
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text' => $faq['answer'],
      ],
    ];
  }, $servicePage['faqs']),
];

require __DIR__ . '/header/header.php';
?>

<main class="op-service-page op-service-page--<?= $e($servicePage['slug']) ?>">
  <section class="sp-hero<?= !empty($servicePage['heroArtwork']) ? ' sp-hero--artwork' : '' ?>" aria-labelledby="service-page-title">
    <?php if (empty($servicePage['heroArtwork'])): ?>
      <div class="sp-hero__rings" aria-hidden="true"><img src="<?= $e($servicePage['heroIcon']) ?>" alt=""></div>
    <?php endif; ?>
    <div class="sp-hero__inner">
      <div class="sp-hero__layout">
        <div class="sp-hero__content">
          <nav class="sp-breadcrumbs" aria-label="Breadcrumb" data-reveal>
            <a href="index.php">Home</a><i>/</i><span>Services</span><i>/</i><span><?= $e($servicePage['categoryLabel'] ?? 'Web Development') ?></span><i>/</i><span><?= $e($servicePage['heroEyebrow']) ?></span>
          </nav>
          <div class="sp-hero__icon" data-reveal>
            <img src="<?= $e($servicePage['heroIcon']) ?>" alt="" aria-hidden="true">
          </div>
          <div class="sp-eyebrow" data-reveal><?= $e($servicePage['heroEyebrow']) ?></div>
          <h1 id="service-page-title" data-reveal><?= $e($servicePage['heroHeading']) ?><span><?= $e($servicePage['heroHeadingMuted']) ?></span></h1>
          <p class="sp-hero__description" data-reveal><?= $e($servicePage['heroDescription']) ?></p>
          <div class="sp-actions" data-reveal>
            <a class="op-btn op-btn--light" href="contact-us.php"><?= $e($servicePage['auditLabel']) ?></a>
            <a class="op-btn op-btn--outline" href="#included">What’s Included</a>
          </div>
          <div class="sp-hero__meta" data-reveal>
            <span><?= $e($servicePage['heroMeta'][0]) ?></span>
            <span><?= $e($servicePage['heroMeta'][1]) ?></span>
          </div>
        </div>
        <?php if (!empty($servicePage['heroArtwork'])): ?>
          <figure class="sp-hero__artwork" data-reveal aria-label="<?= $e($servicePage['heroArtworkAlt'] ?? $servicePage['heroEyebrow']) ?> preview">
            <img src="<?= $e($servicePage['heroArtwork']) ?>" alt="<?= $e($servicePage['heroArtworkAlt'] ?? '') ?>">
          </figure>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="sp-section sp-overview" aria-labelledby="overview-title">
    <div class="sp-inner">
      <div class="sp-section-head" data-reveal>
        <div>
          <span class="sp-eyebrow"><?= $e($servicePage['overviewEyebrow']) ?></span>
          <h2 id="overview-title"><?= $e($servicePage['overviewHeading']) ?><span><?= $e($servicePage['overviewHeadingMuted']) ?></span></h2>
        </div>
        <p><?= $e($servicePage['overviewCopy']) ?></p>
      </div>
      <div class="sp-stats" data-reveal>
        <?php foreach ($servicePage['stats'] as $stat): ?>
          <div class="sp-stat"><strong><?= $e($stat['value']) ?></strong><span><?= $e($stat['label']) ?></span></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="sp-section sp-included" id="included" aria-labelledby="included-title">
    <div class="sp-inner">
      <div class="sp-section-head" data-reveal>
        <div>
          <span class="sp-eyebrow">What’s Included</span>
          <h2 id="included-title"><?= $e($servicePage['includedHeading']) ?><span><?= $e($servicePage['includedHeadingMuted']) ?></span></h2>
        </div>
        <p><?= $e($servicePage['includedCopy']) ?></p>
      </div>
      <div class="sp-service-grid">
        <?php foreach ($servicePage['included'] as $index => $item): ?>
          <article class="sp-service-card<?= !empty($item['copy']) ? ' has-copy' : '' ?>" data-reveal>
            <div class="sp-service-card__top">
              <span class="sp-service-card__icon"><img src="<?= $e($item['icon']) ?>" alt="" aria-hidden="true"></span>
              <span class="sp-card-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
            </div>
            <h3><?= $e($item['title']) ?></h3>
            <?php if (!empty($item['copy'])): ?><p><?= $e($item['copy']) ?></p><?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="sp-section sp-why" aria-labelledby="why-title">
    <div class="sp-inner">
      <div class="sp-section-head sp-section-head--single" data-reveal>
        <div>
          <span class="sp-eyebrow">Why It Matters</span>
          <h2 id="why-title"><?= $e($servicePage['whyHeading']) ?><span><?= $e($servicePage['whyHeadingMuted']) ?></span></h2>
        </div>
      </div>
      <div class="sp-reason-grid">
        <?php foreach ($servicePage['why'] as $index => $item): ?>
          <article class="sp-reason" data-reveal>
            <span class="sp-card-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h3><?= $e($item['title']) ?></h3>
            <p><?= $e($item['copy']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="sp-section sp-process" aria-labelledby="process-title">
    <div class="sp-inner">
      <div class="sp-section-head sp-section-head--single" data-reveal>
        <div>
          <span class="sp-eyebrow">Our Process</span>
          <h2 id="process-title"><?= $e($servicePage['processHeading']) ?><span><?= $e($servicePage['processHeadingMuted']) ?></span></h2>
        </div>
      </div>
      <div class="sp-process-grid">
        <?php foreach ($servicePage['process'] as $index => $item): ?>
          <article class="sp-process-step" data-reveal>
            <span class="sp-card-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h3><?= $e($item['title']) ?></h3>
            <p><?= $e($item['copy']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="op-faq" id="faq" aria-labelledby="service-faq-title">
    <div class="op-faq__shell">
      <aside class="op-faq__aside" data-reveal>
        <span class="op-faq__eyebrow">FAQs</span>
        <h2 id="service-faq-title"><?= $e($servicePage['faqHeading']) ?><span><?= $e($servicePage['faqHeadingMuted']) ?></span></h2>
        <div class="op-faq__contact">
          <strong>Have a specific question about your project?</strong>
          <p>Talk to our UAE specialists. Free consultation, no commitment and no sales pressure.</p>
          <a class="op-faq__cta wa-unified-button" href="https://wa.me/971507818373" target="_blank" rel="noopener"><img src="assets/icons/brand/whatsapp.svg" alt="" aria-hidden="true">Chat on WhatsApp</a>
          <ul><li>Free consultation</li><li>No commitment required</li></ul>
        </div>
      </aside>

      <div class="op-faq__list" data-reveal>
        <?php foreach ($servicePage['faqs'] as $index => $faq): ?>
          <article class="op-faq-item<?= $index === 0 ? ' is-open' : '' ?>">
            <button class="op-faq-item__question" type="button" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
              <span><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
              <strong><?= $e($faq['question']) ?></strong>
              <i aria-hidden="true"></i>
            </button>
            <div class="op-faq-item__answer"><div><p><?= $e($faq['answer']) ?></p></div></div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php if (!empty($servicePage['related'])): ?>
    <section class="sp-section sp-related" aria-labelledby="related-services-title">
      <div class="sp-inner">
        <div class="sp-section-head sp-section-head--single" data-reveal>
          <div>
            <span class="sp-eyebrow">Related Services</span>
            <h2 id="related-services-title"><?= $e($servicePage['relatedHeading'] ?? 'Other Services') ?><span><?= $e($servicePage['relatedHeadingMuted'] ?? 'You May Need.') ?></span></h2>
          </div>
        </div>
        <div class="sp-related-grid">
          <?php foreach ($servicePage['related'] as $item): ?>
            <a class="sp-related-card" href="<?= $e($item['url']) ?>" data-reveal><strong><?= $e($item['title']) ?></strong></a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="op-growth-cta" id="consultation" aria-labelledby="service-cta-title">
    <div class="op-growth-cta__orb" aria-hidden="true"></div>
    <div class="op-growth-cta__inner" data-reveal>
      <span class="op-growth-cta__eyebrow"><?= $e($servicePage['auditLabel']) ?></span>
      <h2 id="service-cta-title"><?= $e($servicePage['ctaHeading']) ?><span><?= $e($servicePage['ctaHeadingMuted']) ?></span></h2>
      <p><?= $e($servicePage['ctaCopy']) ?></p>
      <a class="op-btn op-btn--light sp-cta-button" href="contact-us.php">
        <span><?= $e($servicePage['auditLabel']) ?></span>
      </a>
      <div class="op-growth-cta__trust" aria-label="Optimizers credentials">
        <span><b>✓</b> 200+ UAE Clients</span>
        <span><b>✓</b> All 7 UAE Emirates</span>
        <span><b class="op-growth-cta__star">★</b> 5.0 Google Rating</span>
      </div>
    </div>
  </section>
</main>

<script type="application/ld+json"><?= json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

<?php require __DIR__ . '/footer/footer.php'; ?>
