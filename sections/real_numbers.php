<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

try {
    $db = OptimizersDB::getConnection();
    
    // Real Numbers: 3 latest published case studies for Real Numbers category
    $realNumbersStmt = $db->query("SELECT * FROM case_studies WHERE status = 'published' AND (category = 'Real Numbers. Real Clients. Real Growth.' OR category = 'real-numbers-client-results' OR (category NOT LIKE '%Tips%' AND category NOT LIKE '%TikTok%')) ORDER BY created_at DESC, display_order ASC LIMIT 3");
    $realNumbersCaseStudies = $realNumbersStmt->fetchAll();

} catch (Throwable $e) {
    $realNumbersCaseStudies = [];
}
?>

<!-- SECTION 4: REAL NUMBERS. REAL CLIENTS. REAL GROWTH. -->
<section class="op-case-section svg-arrow-container" id="real-numbers-section" aria-labelledby="real-numbers-title">
  <div class="op-case-section__glow" aria-hidden="true"></div>
  <div class="op-case-section__inner">
    <header class="op-case-section__head" data-reveal>
      <div class="op-case-section__head-content">
        <span class="op-case-section__eyebrow">VERIFIED OUTCOMES</span>
        <h2 id="real-numbers-title">Proof Over Promises.<span>Results You Can Measure.</span></h2>
      </div>
      <div class="op-case-section__head-action">
        <p class="op-case-section__intro">Our case studies speak louder than any promise. Here’s what we’ve achieved for UAE businesses like yours.</p>
      </div>
    </header>

    <?php if (!empty($realNumbersCaseStudies)): ?>
      <div class="op-case-grid">
        <?php foreach ($realNumbersCaseStudies as $index => $cs): 
          $tags = json_decode($cs['tags'] ?? '[]', true) ?: [];
          $coverImg = !empty($cs['featured_image']) ? optimizers_url($cs['featured_image']) : optimizers_url('assets/images/service-pages/google-ads-hero.svg');
          $displayTitle = !empty($cs['card_title']) ? $cs['card_title'] : $cs['title'];
          $detailUrl = optimizers_url('case-studies/' . rawurlencode($cs['slug']));
          $delayStr = '.' . str_pad((string)(($index % 3) * 5 + 4), 2, '0', STR_PAD_LEFT) . 's';
        ?>
          <article class="op-case-card" data-reveal style="--reveal-delay: <?= $delayStr ?>">
            <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>" class="op-case-card__link" aria-label="Read case study: <?= htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') ?>">
              
              <div class="op-case-card__media">
                <img 
                  src="<?= $coverImg ?>" 
                  alt="<?= htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') ?>" 
                  loading="lazy"
                  onerror="this.onerror=null; this.src='<?= optimizers_url('assets/images/service-pages/google-ads-hero.svg') ?>';"
                >
                <div class="op-case-card__badge-wrap">
                  <span class="op-case-card__category">Real Numbers</span>
                </div>
              </div>

              <div class="op-case-card__body">
                <h3 class="op-case-card__title"><?= htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="op-case-card__excerpt"><?= htmlspecialchars($cs['short_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                <div class="op-case-card__footer">
                  <?php if (!empty($tags)): ?>
                    <div class="op-case-card__tags">
                      <?php foreach (array_slice($tags, 0, 3) as $tg): ?>
                        <span class="op-case-card__tag"><?= htmlspecialchars((string)$tg, ENT_QUOTES, 'UTF-8') ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>

                  <span class="op-case-card__cta ">
                    <span>View Case Study</span>
                    <svg class="op-ui-arrow op-ui-arrow--diagonal" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
                  </span>
                </div>
              </div>

            </a>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- View More Button -->
      <div style="text-align: center; margin-top: 40px;" data-reveal>
        <a href="<?= optimizers_url('case-studies.php?category=real-numbers-client-results') ?>" class="op-btn op-btn--outline">
          <span>View More</span>
          <svg class="op-ui-arrow" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>
