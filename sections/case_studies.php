<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

try {
    $db = OptimizersDB::getConnection();
    
    // 1. Proven Client Impact: 3 latest published case studies across all categories
    $provenCaseStudies = $db->query("SELECT * FROM case_studies WHERE status = 'published' ORDER BY created_at DESC, display_order ASC LIMIT 3")->fetchAll();

    // 2. Digital Marketing Tips: 3 latest published case studies for Digital Marketing Tips category
    $tipsStmt = $db->query("SELECT * FROM case_studies WHERE status = 'published' AND (category = 'Digital Marketing Tips for UAE Business Owners' OR category = 'digital-marketing-tips' OR category LIKE '%Tips%' OR category LIKE '%DIGITAL MARKETING%' OR category LIKE '%TikTok%') ORDER BY created_at DESC, display_order ASC LIMIT 3");
    $tipsCaseStudies = $tipsStmt->fetchAll();

} catch (Throwable $e) {
    $provenCaseStudies = [];
    $tipsCaseStudies = [];
}
?>

<!-- SECTION 1: PROVEN CLIENT IMPACT -->
<!-- <section class="op-case-section" id="case-studies" aria-labelledby="case-studies-title">
  <div class="op-case-section__glow" aria-hidden="true"></div>
  <div class="op-case-section__inner">
    <header class="op-case-section__head" data-reveal>
      <div class="op-case-section__head-content">
        <span class="op-case-section__eyebrow">PROVEN CLIENT IMPACT</span>
        <h2 id="case-studies-title">Transforming UAE businesses through <span>data-led digital execution.</span></h2>
      </div>
      <div class="op-case-section__head-action">
        <p class="op-case-section__intro">Explore real case studies detailing how we lowered lead acquisition costs, dominated organic search rankings, and scaled revenue for UAE brands.</p>
      </div>
    </header>

    <?php if (!empty($provenCaseStudies)): ?>
      <div class="op-case-grid">
        <?php foreach ($provenCaseStudies as $index => $cs): 
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
                  <span class="op-case-card__category"><?= htmlspecialchars($cs['category'], ENT_QUOTES, 'UTF-8') ?></span>
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

                  <span class="op-case-card__cta">
                    <span>View Case Study</span>
                    <svg class="op-ui-arrow op-ui-arrow--diagonal" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
                  </span>
                </div>
              </div>

            </a>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="op-case-empty" data-reveal>
        <h3>No Case Studies Published Yet</h3>
      </div>
    <?php endif; ?>
  </div>
</section> -->

<!-- SECTION 2: DIGITAL MARKETING TIPS FOR UAE BUSINESS OWNERS -->
<section class="op-case-section" id="digital-marketing-tips-section" aria-labelledby="tips-section-title" style="padding-top: 0;">
  <div class="op-case-section__glow" aria-hidden="true"></div>
  <div class="op-case-section__inner">
    <header class="op-case-section__head" data-reveal>
      <div class="op-case-section__head-content">
        <span class="op-case-section__eyebrow">STRATEGIC INSIGHTS</span>
        <h2 id="tips-section-title">Digital Marketing Tips<span>For UAE Business Owners.</span></h2>
      </div>
      <div class="op-case-section__head-action">
        <p class="op-case-section__intro">Practical digital marketing insights, strategies and proven approaches designed to help UAE businesses attract more customers and grow online.</p>
      </div>
    </header>

    <?php if (!empty($tipsCaseStudies)): ?>
      <div class="op-case-grid">
        <?php foreach ($tipsCaseStudies as $index => $cs): 
          $tags = json_decode($cs['tags'] ?? '[]', true) ?: [];
          $coverImg = !empty($cs['featured_image']) ? optimizers_url($cs['featured_image']) : optimizers_url('assets/images/service-pages/google-ads-hero.svg');
          $displayTitle = !empty($cs['card_title']) ? $cs['card_title'] : $cs['title'];
          $detailUrl = optimizers_url('case-studies/' . rawurlencode($cs['slug']));
          $delayStr = '.' . str_pad((string)(($index % 3) * 5 + 4), 2, '0', STR_PAD_LEFT) . 's';
        ?>
          <article class="op-case-card" data-reveal style="--reveal-delay: <?= $delayStr ?>">
            <a href="<?= htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8') ?>" class="op-case-card__link" aria-label="Read tip: <?= htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') ?>">
              
              <div class="op-case-card__media">
                <img 
                  src="<?= $coverImg ?>" 
                  alt="<?= htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8') ?>" 
                  loading="lazy"
                  onerror="this.onerror=null; this.src='<?= optimizers_url('assets/images/service-pages/google-ads-hero.svg') ?>';"
                >
                <div class="op-case-card__badge-wrap">
                  <span class="op-case-card__category">Digital Marketing Tips</span>
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

                  <span class="op-case-card__cta">
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
        <a href="<?= optimizers_url('case-studies.php?category=digital-marketing-tips') ?>" class="op-btn op-btn--outline">
          <span>View More</span>
          <svg class="op-ui-arrow" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>
