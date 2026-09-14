<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$slug = trim((string)($_GET['slug'] ?? ''));

if ($slug === '' || $slug === 'case-studies.php' || $slug === 'case-study.php' || $slug === 'index.php') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    if (preg_match('~/case-studies/([a-zA-Z0-9_-]+)/?$~', $requestPath, $matches)) {
        $slug = rawurldecode($matches[1]);
    } elseif (preg_match('~/case-study/([a-zA-Z0-9_-]+)/?$~', $requestPath, $matches)) {
        $slug = rawurldecode($matches[1]);
    }
}

if ($slug === 'case-studies.php' || $slug === 'case-study.php' || $slug === 'index.php') {
    $slug = '';
}

if ($slug === '') {
    header('HTTP/1.0 404 Not Found');
    $pageTitle = 'Case Study Not Found | Optimizers UAE';
    require __DIR__ . '/header/header.php';
    echo '<main class="op-case-detail-page"><div class="op-case-detail-container" style="padding: 120px 20px; text-align:center;"><h1>Case Study Not Found</h1><p>The requested case study could not be found or is not published yet.</p><br><a href="' . optimizers_url('case-studies.php') . '" class="op-btn op-btn--light">Back to All Case Studies</a></div></main>';
    require __DIR__ . '/footer/footer.php';
    exit;
}

try {
    $db = OptimizersDB::getConnection();
    
    // Fetch target case study by slug
    $stmt = $db->prepare("SELECT * FROM case_studies WHERE slug = ? AND status = 'published'");
    $stmt->execute([$slug]);
    $cs = $stmt->fetch();

    if (!$cs) {
        // Fallback check for admin preview if draft
        $stmtAdmin = $db->prepare("SELECT * FROM case_studies WHERE slug = ?");
        $stmtAdmin->execute([$slug]);
        $cs = $stmtAdmin->fetch();
    }

    if (!$cs) {
        header("HTTP/1.0 404 Not Found");
        $pageTitle = 'Case Study Not Found | Optimizers UAE';
        require __DIR__ . '/header/header.php';
        echo '<main class="op-case-detail-page"><div class="op-case-detail-container" style="padding: 120px 20px; text-align:center;"><h1>Case Study Not Found</h1><p>The requested case study could not be found or is not published yet.</p><br><a href="' . optimizers_url('case-studies.php') . '" class="op-btn op-btn--light">Back to All Case Studies</a></div></main>';
        require __DIR__ . '/footer/footer.php';
        exit;
    }

    // Decode JSON fields safely
    $gallery = json_decode($cs['gallery_images'] ?? '[]', true) ?: [];
    $services = json_decode($cs['services'] ?? '[]', true) ?: [];
    $technologies = json_decode($cs['technologies'] ?? '[]', true) ?: [];
    $benefits = json_decode($cs['benefits'] ?? '[]', true) ?: [];
    $tags = json_decode($cs['tags'] ?? '[]', true) ?: [];
    $statistics = json_decode($cs['statistics'] ?? '[]', true) ?: [];

    // Fetch statistics from relational table (with JSON fallback)
    try {
        $statsStmt = $db->prepare("SELECT * FROM case_study_statistics WHERE case_study_id = ? ORDER BY display_order ASC, id ASC");
        $statsStmt->execute([$cs['id']]);
        $dbStatistics = $statsStmt->fetchAll();
        if (!empty($dbStatistics)) {
            $statistics = array_map(function($s) {
                return [
                    // Real DB columns: label, before_value, after_value, description
                    'label'  => $s['label']       ?? ($s['metric_label']       ?? ''),
                    'after'  => $s['after_value']  ?? ($s['metric_value']       ?? ($s['value'] ?? '')),
                    'before' => $s['before_value'] ?? ($s['before']             ?? ''),
                    'note'   => $s['description']  ?? ($s['metric_description'] ?? ($s['note'] ?? ''))
                ];
            }, $dbStatistics);
        }
    } catch (Throwable $e) { /* table might be empty or migrating */ }

    // Fetch custom sections from relational table
    $customSections = [];
    try {
        $sectionsStmt = $db->prepare("SELECT * FROM case_study_sections WHERE case_study_id = ? ORDER BY display_order ASC, id ASC");
        $sectionsStmt->execute([$cs['id']]);
        $customSections = $sectionsStmt->fetchAll();
    } catch (Throwable $e) { /* table empty */ }

    // Fetch custom tables from relational table
    $customTables = [];
    try {
        $tablesStmt = $db->prepare("SELECT * FROM case_study_tables WHERE case_study_id = ? ORDER BY display_order ASC, id ASC");
        $tablesStmt->execute([$cs['id']]);
        $rawTbls = $tablesStmt->fetchAll();
        foreach ($rawTbls as $rt) {
            $customTables[] = [
                'title'   => $rt['title'],
                'headers' => json_decode($rt['headers'] ?? '[]', true) ?: [],
                'rows'    => json_decode($rt['rows'] ?? '[]', true) ?: []
            ];
        }
    } catch (Throwable $e) { /* table empty */ }

    if (empty($customTables) && !empty($cs['tables'])) {
        $customTables = json_decode($cs['tables'] ?? '[]', true) ?: [];
    }

    // New saves retain the exact admin order in the existing JSON column.
    $storedContentBlocks = json_decode($cs['tables'] ?? '[]', true);
    $contentBlocks = is_array($storedContentBlocks) && isset($storedContentBlocks[0]['type'])
      ? $storedContentBlocks
      : array_merge(
        array_map(static function ($section) {
          $section['type'] = 'section';
          return $section;
        }, $customSections),
        array_map(static function ($table) {
          $table['type'] = 'table';
          return $table;
        }, $customTables)
      );

    // Fetch relational tags if available
    try {
        $tagsStmt = $db->prepare("SELECT t.name FROM tags t JOIN case_study_tags cst ON t.id = cst.tag_id WHERE cst.case_study_id = ? ORDER BY t.name ASC");
        $tagsStmt->execute([$cs['id']]);
        $relTags = $tagsStmt->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($relTags)) {
            $tags = $relTags;
        }
    } catch (Throwable $e) {}

    // Fetch relational technologies if available
    try {
        $techStmt = $db->prepare("SELECT t.name FROM technologies t JOIN case_study_technologies cst ON t.id = cst.technology_id WHERE cst.case_study_id = ? ORDER BY t.name ASC");
        $techStmt->execute([$cs['id']]);
        $relTechs = $techStmt->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($relTechs)) {
            $technologies = $relTechs;
        }
    } catch (Throwable $e) {}

    // Fetch related case studies (matching category or recent, excluding current)
    $relStmt = $db->prepare("SELECT * FROM case_studies WHERE status = 'published' AND id != ? AND (category = ? OR 1=1) ORDER BY (category = ?) DESC, display_order ASC, created_at DESC LIMIT 3");
    $relStmt->execute([$cs['id'], $cs['category'], $cs['category']]);
    $relatedCases = $relStmt->fetchAll();

} catch (Throwable $e) {
    header("Location: " . optimizers_url('case-studies.php'));
    exit;
}

$pageTitle = htmlspecialchars($cs['title']) . ' | Optimizers UAE Case Study';
$detailDescription = trim((string)($cs['detail_description'] ?? '')) ?: (string)($cs['short_description'] ?? '');
$pageDescription = htmlspecialchars($detailDescription);
$currentPage = 'case-studies';

require __DIR__ . '/header/header.php';
?>

<main class="op-case-detail-page">

  <!-- Case Study Hero Header -->
  <header class="op-cs-detail-hero">
    <div class="op-cs-detail-hero__glow" aria-hidden="true"></div>
    <div class="op-cs-detail-hero__inner">
      
      <div class="op-cs-detail-hero__breadcrumbs">
        <a href="<?= optimizers_url('index.php') ?>">Home</a>
        <span>/</span>
        <a href="<?= optimizers_url('case-studies.php') ?>">Case Studies</a>
        <span>/</span>
        <span class="is-current"><?= htmlspecialchars($cs['category'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>

      <div class="op-cs-detail-hero__layout">
        <div class="op-cs-detail-hero__head">
          <span class="op-cs-detail-hero__category"><?= htmlspecialchars($cs['category'], ENT_QUOTES, 'UTF-8') ?></span>
          <h1><?= htmlspecialchars($cs['title'], ENT_QUOTES, 'UTF-8') ?></h1>
          <p class="op-cs-detail-hero__intro"><?= nl2br(htmlspecialchars($detailDescription, ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
        <?php if (!empty($cs['featured_image'])): ?>
          <div class="op-cs-detail-hero__image">
            <img src="<?= optimizers_url($cs['featured_image']) ?>" alt="<?= htmlspecialchars($cs['title'], ENT_QUOTES, 'UTF-8') ?>" loading="eager" onerror="this.closest('.op-cs-detail-hero__image').remove()">
          </div>
        <?php endif; ?>
      </div>

    </div>
  </header>

  <!-- Statistics and metadata stay out of the detail hero by design. -->

  <!-- MAIN CASE STUDY NARRATIVE CONTENT -->
  <section class="op-cs-narrative">
    <div class="op-cs-narrative__inner">

      <?php if (!empty($cs['challenge']) || !empty($cs['objectives'])): ?>
        <div class="op-cs-priority-grid">
          <?php if (!empty($cs['challenge'])): ?>
            <div class="op-cs-block op-cs-block--challenge">
              <div class="op-cs-block__header"><span class="op-cs-block__num">01</span><h3>The Challenge</h3></div>
              <p><?= nl2br(htmlspecialchars($cs['challenge'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
          <?php endif; ?>
          <?php if (!empty($cs['objectives'])): ?>
            <div class="op-cs-block">
              <div class="op-cs-block__header"><span class="op-cs-block__num">02</span><h3>Campaign Objectives</h3></div>
              <p><?= nl2br(htmlspecialchars($cs['objectives'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      
      <div class="op-cs-narrative__grid">
        
        <!-- Left Side: Main Story Sections -->
        <div class="op-cs-narrative__main">
          
          <?php if (!empty($cs['full_description'])): ?>
            <div class="op-cs-block">
              <h3>Overview & Background</h3>
              <p><?= nl2br(htmlspecialchars($cs['full_description'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
          <?php endif; ?>

          <?php if (!empty($cs['approach'])): ?>
            <div class="op-cs-block op-cs-block--approach">
              <div class="op-cs-block__header">
                <span class="op-cs-block__num">03</span>
                <h3>Strategy & Approach</h3>
              </div>
              <p><?= nl2br(htmlspecialchars($cs['approach'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
          <?php endif; ?>

          <?php if (!empty($cs['solution'])): ?>
            <div class="op-cs-block op-cs-block--solution">
              <div class="op-cs-block__header">
                <span class="op-cs-block__num">04</span>
                <h3>The Solution & Implementation</h3>
              </div>
              <p><?= nl2br(htmlspecialchars($cs['solution'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
          <?php endif; ?>

          <?php if (!empty($cs['results'])): ?>
            <div class="op-cs-block op-cs-block--results">
              <div class="op-cs-block__header">
                <span class="op-cs-block__num">05</span>
                <h3>Final Business Impact & Results</h3>
              </div>
              <p><?= nl2br(htmlspecialchars($cs['results'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
          <?php endif; ?>

          <!-- Custom Sections from Database -->
          <?php if (empty($contentBlocks) && !empty($customSections)): 
            $secCounter = 6;
            foreach ($customSections as $section):
              $secTitle = $section['title'] ?? '';
              $secContent = $section['content'] ?? '';
              $secImg = $section['image'] ?? ($section['image_url'] ?? '');
              if (empty(trim($secTitle)) && empty(trim($secContent))) continue;
          ?>
            <div class="op-cs-block">
              <div class="op-cs-block__header">
                <span class="op-cs-block__num"><?= str_pad((string)$secCounter, 2, '0', STR_PAD_LEFT) ?></span>
                <h3><?= htmlspecialchars($secTitle, ENT_QUOTES, 'UTF-8') ?></h3>
              </div>
              <?php if (!empty($secContent)): ?>
                <p><?= nl2br(htmlspecialchars($secContent, ENT_QUOTES, 'UTF-8')) ?></p>
              <?php endif; ?>
              <?php if (!empty($secImg)): ?>
                <div class="op-cs-block__image" style="margin-top: 1.5rem; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08);">
                  <img src="<?= optimizers_url($secImg) ?>" alt="<?= htmlspecialchars($secTitle, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" style="width: 100%; height: auto; display: block;">
                </div>
              <?php endif; ?>
            </div>
          <?php 
              $secCounter++;
            endforeach; 
          endif; ?>

          <!-- Custom Tables from Database -->
          <?php if (empty($contentBlocks) && !empty($customTables)): ?>
            <?php foreach ($customTables as $tbl): 
              $tTitle = trim((string)($tbl['title'] ?? ''));
              $tHeaders = is_array($tbl['headers'] ?? null) ? $tbl['headers'] : [];
              $tRows = is_array($tbl['rows'] ?? null) ? $tbl['rows'] : [];
              if (empty($tTitle) && empty($tHeaders) && empty($tRows)) continue;
            ?>
              <div class="op-cs-block op-cs-table-block">
                <?php if (!empty($tTitle)): ?>
                  <h3 class="op-cs-table-title" style="color: #F8FAFC; font-size: 1.15rem; font-weight: 700; margin-bottom: 0.75rem;"><?= htmlspecialchars($tTitle, ENT_QUOTES, 'UTF-8') ?></h3>
                <?php endif; ?>
                <div class="op-table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 12px; border: 1px solid rgba(255,255,255,0.08); background: rgba(18,18,18,0.6);">
                  <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <?php if (!empty($tHeaders)): ?>
                      <thead>
                        <tr style="background: rgba(0, 229, 255, 0.08); border-bottom: 1px solid rgba(255, 255, 255, 0.12);">
                          <?php foreach ($tHeaders as $h): ?>
                            <th style="padding: 12px 16px; font-weight: 700; color: #00E5FF; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; white-space: nowrap;">
                              <?= htmlspecialchars((string)$h, ENT_QUOTES, 'UTF-8') ?>
                            </th>
                          <?php endforeach; ?>
                        </tr>
                      </thead>
                    <?php endif; ?>
                    <?php if (!empty($tRows)): ?>
                      <tbody>
                        <?php foreach ($tRows as $rIndex => $row): ?>
                          <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.04);">
                            <?php 
                              $rowCells = is_array($row) ? $row : [];
                              foreach ($tHeaders as $cIndex => $h): 
                                $cellVal = $rowCells[$cIndex] ?? ($rowCells[(string)$cIndex] ?? '');
                            ?>
                              <td style="padding: 12px 16px; color: #CBD5E1; vertical-align: middle;">
                                <?= nl2br(htmlspecialchars((string)$cellVal, ENT_QUOTES, 'UTF-8')) ?>
                              </td>
                            <?php endforeach; ?>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    <?php endif; ?>
                  </table>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php foreach ($contentBlocks as $block):
            $blockType = $block['type'] ?? '';
            if ($blockType === 'section'):
              $secTitle = trim((string)($block['title'] ?? ''));
              $secContent = (string)($block['content'] ?? '');
              $secImg = trim((string)($block['image'] ?? ($block['image_url'] ?? '')));
              if ($secTitle === '' && trim($secContent) === '') continue;
          ?>
            <article class="op-cs-block op-cs-flow-section<?= $secImg !== '' ? ' op-cs-flow-section--image' : '' ?>">
              <div class="op-cs-flow-section__content">
                <?php if ($secTitle !== ''): ?><h3><?= htmlspecialchars($secTitle, ENT_QUOTES, 'UTF-8') ?></h3><?php endif; ?>
                <?php if (trim($secContent) !== ''): ?><p><?= nl2br(htmlspecialchars($secContent, ENT_QUOTES, 'UTF-8')) ?></p><?php endif; ?>
              </div>
              <?php if ($secImg !== ''): ?><img class="op-cs-flow-section__image" src="<?= optimizers_url($secImg) ?>" alt="<?= htmlspecialchars($secTitle, ENT_QUOTES, 'UTF-8') ?>" loading="lazy"><?php endif; ?>
            </article>
          <?php elseif ($blockType === 'table'):
            $tTitle = trim((string)($block['title'] ?? ''));
            $tHeaders = is_array($block['headers'] ?? null) ? $block['headers'] : [];
            $tRows = is_array($block['rows'] ?? null) ? $block['rows'] : [];
            if ($tTitle === '' && empty($tHeaders) && empty($tRows)) continue;
          ?>
            <article class="op-cs-block op-cs-table-block">
              <?php if ($tTitle !== ''): ?><h3 class="op-cs-table-title"><?= htmlspecialchars($tTitle, ENT_QUOTES, 'UTF-8') ?></h3><?php endif; ?>
              <div class="op-table-responsive">
                <table>
                  <?php if (!empty($tHeaders)): ?><thead><tr><?php foreach ($tHeaders as $header): ?><th><?= htmlspecialchars((string)$header, ENT_QUOTES, 'UTF-8') ?></th><?php endforeach; ?></tr></thead><?php endif; ?>
                  <?php if (!empty($tRows)): ?><tbody><?php foreach ($tRows as $row): ?><tr><?php foreach ($tHeaders as $columnIndex => $header): ?><td><?= nl2br(htmlspecialchars((string)($row[$columnIndex] ?? ($row[(string)$columnIndex] ?? '')), ENT_QUOTES, 'UTF-8')) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody><?php endif; ?>
                </table>
              </div>
            </article>
          <?php endif; endforeach; ?>

          <!-- Additional Gallery Images if provided -->
          <?php if (!empty($gallery)): ?>
            <div class="op-cs-block op-cs-gallery-block">
              <h3>Project Visual Highlights</h3>
              <div class="op-cs-gallery-grid">
                <?php foreach ($gallery as $gImg): ?>
                  <div class="op-cs-gallery-item">
                    <img src="<?= optimizers_url((string)$gImg) ?>" alt="Gallery Image" loading="lazy">
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

        </div>

        <!-- Right Side: Sidebar Info (Services, Tech Stack, Benefits, Tags) -->
        <aside class="op-cs-narrative__sidebar">
          
          <?php if (!empty($benefits)): ?>
            <div class="op-cs-side-box">
              <h4 class="op-cs-side-box__title">Key Benefits Delivered</h4>
              <ul class="op-cs-benefits-list">
                <?php foreach ($benefits as $b): ?>
                  <li>
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <span><?= htmlspecialchars((string)$b, ENT_QUOTES, 'UTF-8') ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (!empty($services)): ?>
            <div class="op-cs-side-box">
              <h4 class="op-cs-side-box__title">Services Executed</h4>
              <div class="op-cs-tags-cloud">
                <?php foreach ($services as $srv): ?>
                  <span class="op-cs-tag-chip"><?= htmlspecialchars((string)$srv, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($technologies)): ?>
            <div class="op-cs-side-box">
              <h4 class="op-cs-side-box__title">Technologies & Tools</h4>
              <div class="op-cs-tags-cloud">
                <?php foreach ($technologies as $tech): ?>
                  <span class="op-cs-tag-chip op-cs-tag-chip--tech"><?= htmlspecialchars((string)$tech, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($tags)): ?>
            <div class="op-cs-side-box">
              <h4 class="op-cs-side-box__title">Keywords & Industry Tags</h4>
              <div class="op-cs-tags-cloud">
                <?php foreach ($tags as $tg): ?>
                  <span class="op-cs-tag-chip op-cs-tag-chip--muted"><?= htmlspecialchars((string)$tg, ENT_QUOTES, 'UTF-8') ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Sidebar Consultation Quick Action -->
          <div class="op-cs-side-cta">
            <h4>Ready for Similar Results?</h4>
            <p>Schedule a 30-minute growth strategy session tailored for your UAE business.</p>
            <a href="<?= optimizers_url('index.php#consultation') ?>" class="op-btn op-btn--light">
              <span>Book Strategy Call</span>
            </a>
          </div>

        </aside>

      </div>

    </div>
  </section>

  <!-- CTA Banner Bottom -->
  <section class="op-cs-cta-banner">
    <div class="op-cs-cta-banner__inner">
      <h2><?= htmlspecialchars($cs['call_to_action_title'] ?: 'Ready to Scale Your Digital Acquisition in Dubai?') ?></h2>
      <p><?= htmlspecialchars($cs['call_to_action_description'] ?: 'Get an actionable growth audit and learn how Optimizers can accelerate your revenue pipeline.') ?></p>
      <a href="<?= optimizers_url('index.php#consultation') ?>" class="op-btn op-btn--light">
        <span>Request Free Consultation</span>
        <svg class="op-ui-arrow op-ui-arrow--diagonal" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
      </a>
    </div>
  </section>

  <!-- Related Case Studies -->
  <?php if (!empty($relatedCases)): ?>
    <section class="op-cs-related">
      <div class="op-cs-related__inner">
        <div class="op-cs-related__head">
          <span>More Success Stories</span>
          <h2>Explore Related Case Studies</h2>
        </div>

        <div class="op-case-grid">
          <?php foreach ($relatedCases as $relCs): 
            $relTags = json_decode($relCs['tags'] ?? '[]', true) ?: [];
            $relStats = json_decode($relCs['statistics'] ?? '[]', true) ?: [];
            $relCover = !empty($relCs['featured_image']) ? optimizers_url($relCs['featured_image']) : optimizers_url('assets/images/service-pages/google-ads-hero.svg');
            $relDate = !empty($relCs['project_date']) ? $relCs['project_date'] : date('M Y', strtotime($relCs['created_at'] ?? 'now'));
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
                    <span class="op-case-card__dot">&bull;</span>
                    <span class="op-case-card__date"><?= htmlspecialchars($relDate, ENT_QUOTES, 'UTF-8') ?></span>
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
      </div>
    </section>
  <?php endif; ?>

</main>

<?php require __DIR__ . '/footer/footer.php'; ?>
