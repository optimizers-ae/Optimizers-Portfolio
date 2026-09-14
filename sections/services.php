<?php
require_once __DIR__ . '/../includes/db.php';
try {
    $db = OptimizersDB::getConnection();
    $activeServices = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC, id ASC")->fetchAll();
} catch (Throwable $e) {
    $activeServices = [];
}
?>
  <section class="op-service-suite" id="services" aria-labelledby="service-suite-title">
    <div class="op-service-suite__inner">
      <header class="op-service-suite__head" data-reveal>
        <div>
          <span class="op-service-suite__eyebrow">Our Services</span>
          <h2 id="service-suite-title">Digital marketing consultancy services <span>built for UAE market results.</span></h2>
        </div>
        <p class="op-service-suite__intro">We cover every channel that generates real business growth in the UAE. Each service is managed by dedicated in-house specialists, tracked against KPIs that reflect your actual business goals, and connected to a unified strategy rather than running in isolation.</p>
      </header>

      <div class="op-service-suite__grid">
        <?php if (!empty($activeServices)): ?>
          <?php foreach ($activeServices as $index => $srv): 
            $subServices = json_decode($srv['sub_services'] ?? '[]', true) ?: [];
            $boxClass = ($index === 0) ? 'op-service-box op-service-box--wide' : (($index === 5) ? 'op-service-box op-service-box--full' : 'op-service-box');
            $numStr = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) . ' / ' . str_pad((string)count($activeServices), 2, '0', STR_PAD_LEFT);
            $iconPath = !empty($srv['icon']) ? htmlspecialchars($srv['icon'], ENT_QUOTES, 'UTF-8') : 'assets/icons/brand/google.svg';
          ?>
            <article class="<?= $boxClass ?>" id="<?= htmlspecialchars($srv['slug'], ENT_QUOTES, 'UTF-8') ?>" data-reveal style="--delay:.04s">
              <div class="op-service-box__top">
                <span class="op-service-box__icon"><img src="<?= $iconPath ?>" alt="" aria-hidden="true"></span>
                <span class="op-service-box__number"><?= $numStr ?></span>
              </div>
              <span class="op-service-box__category"><?= htmlspecialchars($srv['category'], ENT_QUOTES, 'UTF-8') ?></span>
              <h2><?= htmlspecialchars($srv['title'], ENT_QUOTES, 'UTF-8') ?></h2>
              <p class="op-service-box__copy"><?= htmlspecialchars($srv['description'], ENT_QUOTES, 'UTF-8') ?></p>
              <div class="op-service-box__footer">
                <?php if (!empty($subServices)): ?>
                  <div class="op-service-box__tags">
                    <?php foreach ($subServices as $sub): ?>
                      <span class="op-service-box__tag"><?= htmlspecialchars((string)$sub, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
                <a class="op-service-box__cta" href="#consultation"><span>Book Now</span><svg class="op-ui-arrow op-ui-arrow--diagonal" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg></a>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <article class="op-service-box op-service-box--wide" id="seo" data-reveal style="--delay:.04s">
            <div class="op-service-box__top">
              <span class="op-service-box__icon"><img src="assets/icons/brand/google.svg" alt="" aria-hidden="true"></span>
              <span class="op-service-box__number">01 / 06</span>
            </div>
            <span class="op-service-box__category">SEO</span>
            <h2>Search engine optimization (SEO)</h2>
            <p class="op-service-box__copy">Rank on Page 1 of Google for high-intent UAE keywords that attract real buyers. Our SEO includes technical audits, on-page optimization, local SEO, and e-commerce SEO. We also optimize for Google AI Overviews, ChatGPT, and Perplexity visibility. Our focus is driving qualified leads and business growth—not vanity rankings.</p>
            <div class="op-service-box__footer">
              <div class="op-service-box__tags"><span class="op-service-box__tag">Technical SEO</span><span class="op-service-box__tag">On-Page SEO</span><span class="op-service-box__tag">Local SEO</span><span class="op-service-box__tag">E-Commerce SEO</span><span class="op-service-box__tag">AI / AEO / GEO</span></div>
              <a class="op-service-box__cta" href="#consultation"><span>Book Now</span><svg class="op-ui-arrow op-ui-arrow--diagonal" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg></a>
            </div>
          </article>
        <?php endif; ?>
      </div>
    </div>
  </section>
