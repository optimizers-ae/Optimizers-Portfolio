<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$slug = trim((string)($_GET['slug'] ?? ''));

if ($slug === '') {
    header('Location: ' . optimizers_url('index.php'));
    exit;
}

try {
    $db = OptimizersDB::getConnection();
    
    // Fetch service by slug
    $stmt = $db->prepare("SELECT * FROM services WHERE slug = ? AND is_active = 1");
    $stmt->execute([$slug]);
    $service = $stmt->fetch();

    if (!$service) {
        header("HTTP/1.0 404 Not Found");
        $pageTitle = 'Service Not Found | Optimizers UAE';
        require __DIR__ . '/header/header.php';
        echo '<main style="padding: 120px 20px; text-align:center;"><h1>Service Not Found</h1><p>The requested service could not be found.</p><br><a href="' . optimizers_url('index.php') . '" class="op-btn op-btn--light">Back to Home</a></main>';
        require __DIR__ . '/footer/footer.php';
        exit;
    }

    // Decode JSON fields
    $subServices = json_decode($service['sub_services'] ?? '[]', true) ?: [];
    $features = json_decode($service['features'] ?? '[]', true) ?: [];
    $processSteps = json_decode($service['process_steps'] ?? '[]', true) ?: [];
    $benefits = json_decode($service['benefits'] ?? '[]', true) ?: [];

    // Fetch related case studies for this service
    $relatedStmt = $db->prepare("SELECT * FROM case_studies WHERE status = 'published' AND (category LIKE ? OR services LIKE ? OR title LIKE ?) ORDER BY display_order ASC, created_at DESC LIMIT 3");
    $relatedStmt->execute(["%{$service['category']}%", "%{$service['title']}%", "%{$service['title']}%"]);
    $relatedCases = $relatedStmt->fetchAll();

} catch (Throwable $e) {
    header("Location: " . optimizers_url('index.php'));
    exit;
}

$pageTitle = htmlspecialchars($service['meta_title'] ?: $service['title']) . ' | Optimizers UAE';
$pageDescription = htmlspecialchars($service['meta_description'] ?: $service['description']);
$currentPage = 'services';

require __DIR__ . '/header/header.php';
?>

<main class="op-service-detail-page">

  <!-- Service Hero Header -->
  <header class="op-service-detail-hero">
    <div class="op-service-hero__glow" aria-hidden="true" style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 800px; height: 500px; background: radial-gradient(circle, rgba(0, 176, 213, 0.08) 0%, transparent 70%); pointer-events: none;"></div>
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; position: relative; z-index: 1;">
      
      <div class="op-service-breadcrumb">
        <a href="<?= optimizers_url('index.php') ?>">Home</a>
        <span class="sep">/</span>
        <span class="sep">Services</span>
        <span class="sep">/</span>
        <span class="current"><?= htmlspecialchars($service['category'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>

      <div class="op-service-detail-hero__inner">
        <span class="op-service-detail-hero__category"><?= htmlspecialchars($service['category'], ENT_QUOTES, 'UTF-8') ?></span>
        <h1 class="op-service-detail-hero__title"><?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="op-service-detail-hero__desc"><?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?></p>
        
        <?php if (!empty($subServices)): ?>
          <div class="op-service-detail-hero__sub-tags">
            <?php foreach ($subServices as $sub): ?>
              <span class="op-service-detail-hero__sub-tag"><?= htmlspecialchars((string)$sub, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div style="display: flex; gap: 14px; flex-wrap: wrap;">
          <a href="<?= optimizers_url('index.php#consultation') ?>" class="op-btn op-btn--light">
            <span>Schedule Strategy Consultation</span>
            <svg class="op-ui-arrow op-ui-arrow--diagonal" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
          </a>
          <a href="<?= optimizers_url('case-studies.php?category=' . urlencode($service['category'])) ?>" class="op-btn" style="border: 1px solid rgba(255,255,255,0.15); color: #fff; background: rgba(255,255,255,0.03);">
            <span>View Related Case Studies</span>
          </a>
        </div>
      </div>

    </div>
  </header>

  <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

    <!-- Featured Image if provided -->
    <?php if (!empty($service['featured_image'])): ?>
      <div class="op-service-detail-image">
        <img src="<?= optimizers_url($service['featured_image']) ?>" alt="<?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
      </div>
    <?php endif; ?>

    <!-- Full Description / Overview if provided -->
    <?php if (!empty($service['full_description'])): ?>
      <section class="op-service-full-desc">
        <div class="op-service-full-desc__inner">
          <span class="op-service-section-eyebrow">Service Overview</span>
          <h2 class="op-service-section-title">Strategic Approach for UAE Growth</h2>
          <div style="color: rgba(255,255,255,0.7); line-height: 1.8; font-size: 1rem;">
            <?= nl2br(htmlspecialchars($service['full_description'], ENT_QUOTES, 'UTF-8')) ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- Features / Deliverables Grid -->
    <?php if (!empty($features)): ?>
      <section class="op-service-features">
        <span class="op-service-section-eyebrow">Core Features</span>
        <h2 class="op-service-section-title">What's Included in This Service</h2>
        <div class="op-service-features__grid">
          <?php foreach ($features as $f): 
            $fText = is_array($f) ? ($f['title'] ?? '') : (string)$f;
            if (empty(trim($fText))) continue;
          ?>
            <div class="op-service-feature-card">
              <div class="op-service-feature-card__icon">
                <i class="fa-solid fa-check"></i>
              </div>
              <h3 class="op-service-feature-card__title"><?= htmlspecialchars($fText, ENT_QUOTES, 'UTF-8') ?></h3>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- Process Steps -->
    <?php if (!empty($processSteps)): ?>
      <section class="op-service-process">
        <span class="op-service-section-eyebrow">Execution Methodology</span>
        <h2 class="op-service-section-title">Our Step-by-Step Process</h2>
        <div class="op-service-process__list">
          <?php foreach ($processSteps as $pIdx => $step): 
            $stepTitle = is_array($step) ? ($step['title'] ?? '') : (string)$step;
            $stepDesc = is_array($step) ? ($step['desc'] ?? '') : '';
            if (empty(trim($stepTitle))) continue;
          ?>
            <div class="op-service-process-step">
              <div class="op-service-process-step__num"><?= str_pad((string)($pIdx + 1), 2, '0', STR_PAD_LEFT) ?></div>
              <div class="op-service-process-step__content">
                <h3 class="op-service-process-step__title"><?= htmlspecialchars($stepTitle, ENT_QUOTES, 'UTF-8') ?></h3>
                <?php if (!empty($stepDesc)): ?>
                  <p class="op-service-process-step__desc"><?= htmlspecialchars($stepDesc, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- Benefits Grid -->
    <?php if (!empty($benefits)): ?>
      <section class="op-service-benefits">
        <span class="op-service-section-eyebrow">Measurable Value</span>
        <h2 class="op-service-section-title">Key Business Benefits</h2>
        <div class="op-service-benefits__grid">
          <?php foreach ($benefits as $b): 
            $bText = is_array($b) ? ($b['text'] ?? '') : (string)$b;
            if (empty(trim($bText))) continue;
          ?>
            <div class="op-service-benefit-item">
              <div class="op-service-benefit-item__icon">
                <i class="fa-solid fa-arrow-trend-up"></i>
              </div>
              <span class="op-service-benefit-item__text"><?= htmlspecialchars($bText, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- Related Case Studies Section -->
    <?php if (!empty($relatedCases)): ?>
      <section class="op-service-related-cases">
        <div style="margin-bottom: 36px;">
          <span class="op-service-section-eyebrow">Proven Results</span>
          <h2 class="op-service-section-title" style="margin-bottom: 0;">Case Studies in <?= htmlspecialchars($service['category'], ENT_QUOTES, 'UTF-8') ?></h2>
        </div>

        <div class="op-case-grid">
          <?php foreach ($relatedCases as $relCs): 
            $relTags = json_decode($relCs['tags'] ?? '[]', true) ?: [];
            $relCover = !empty($relCs['featured_image']) ? optimizers_url($relCs['featured_image']) : optimizers_url('assets/images/service-pages/google-ads-hero.svg');
            $relAuthor = !empty($relCs['author']) ? $relCs['author'] : (!empty($relCs['client_name']) ? $relCs['client_name'] : 'Optimizers Strategy Team');
            $relUrl = optimizers_url('case-studies/' . htmlspecialchars($relCs['slug'], ENT_QUOTES, 'UTF-8'));
          ?>
            <article class="op-case-card">
              <a href="<?= $relUrl ?>" class="op-case-card__link">
                <div class="op-case-card__media">
                  <img src="<?= $relCover ?>" alt="<?= htmlspecialchars($relCs['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                  <div class="op-case-card__badge-wrap">
                    <span class="op-case-card__category"><?= htmlspecialchars($relCs['category'], ENT_QUOTES, 'UTF-8') ?></span>
                  </div>
                </div>

                <div class="op-case-card__body">
                  <h3 class="op-case-card__title"><?= htmlspecialchars($relCs['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                  <div class="op-case-card__meta">
                    <span class="op-case-card__client"><?= htmlspecialchars($relAuthor, ENT_QUOTES, 'UTF-8') ?></span>
                  </div>

                  <p class="op-case-card__excerpt"><?= htmlspecialchars($relCs['short_description'], ENT_QUOTES, 'UTF-8') ?></p>

                  <div class="op-case-card__footer">
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
      </section>
    <?php endif; ?>

  </div>

  <!-- Service CTA Section -->
  <section style="border-top: 1px solid rgba(255,255,255,0.06); padding: 80px 0; background: radial-gradient(circle at 50% 50%, rgba(0,176,213,0.05) 0%, transparent 60%); text-align: center; margin-top: 60px;">
    <div style="max-width: 720px; margin: 0 auto; padding: 0 20px;">
      <span style="display: inline-block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em; color: #00b0d5; margin-bottom: 12px;">Ready to Elevate Your Business?</span>
      <h2 style="font-size: clamp(1.8rem, 4vw, 2.4rem); font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 16px;">Drive Results with Professional <?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?></h2>
      <p style="color: rgba(255,255,255,0.6); font-size: 1rem; line-height: 1.7; margin-bottom: 32px;">Partner with Optimizers UAE for bespoke strategies designed specifically for market leadership in Dubai and the GCC.</p>
      <a href="<?= optimizers_url('index.php#consultation') ?>" class="op-btn op-btn--light">
        <span>Request Free Strategy Consultation</span>
        <svg class="op-ui-arrow op-ui-arrow--diagonal" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
      </a>
    </div>
  </section>

</main>

<?php require __DIR__ . '/footer/footer.php'; ?>
