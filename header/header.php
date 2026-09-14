<?php
require_once __DIR__ . '/../includes/db.php';

$pageTitle = $pageTitle ?? 'Optimizers UAE';
$pageDescription = $pageDescription ?? 'Digital marketing consultancy in Dubai, UAE.';
$currentPage = $currentPage ?? '';
$baseUrl = optimizers_get_base_url();
$homeUrl = optimizers_url('index.php');

// Fetch dynamic navigation tree from database
$navItems = [];
try {
    $db = OptimizersDB::getConnection();
    $stmt = $db->query("SELECT * FROM navigation_items WHERE is_active = 1 ORDER BY parent_id ASC, display_order ASC, id ASC");
    $allNav = $stmt->fetchAll();
    
    // Group navigation items by parent_id
    $navTree = [];
    foreach ($allNav as $item) {
        $pId = $item['parent_id'] !== null ? (int)$item['parent_id'] : 0;
        $navTree[$pId][] = $item;
    }
} catch (Throwable $e) {
    $navTree = [];
}

// Helper function to test active page
if (!function_exists('isNavActive')) {
    function isNavActive(string $url, string $currentPage): bool {
        if ($currentPage === '') return false;
        $cleanUrl = ltrim($url, '/');
        $cleanUrlNoExt = preg_replace('/\.php$/', '', $cleanUrl);
        $cleanUrlNoHash = explode('#', $cleanUrlNoExt)[0];
        return ($cleanUrl === $currentPage || $cleanUrlNoExt === $currentPage || $cleanUrlNoHash === $currentPage);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#07080b">
  <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= optimizers_url('assets/images/Optimizers-logo-fevicon.png') ?>">
  <link rel="stylesheet" href="<?= optimizers_url('assets/css/style.css?v=packages-live-20260824') ?>">
  <link rel="stylesheet" href="<?= optimizers_url('assets/css/service-pages.css?v=4') ?>">
  <link rel="stylesheet" href="<?= optimizers_url('assets/css/cms-extensions.css?v=1') ?>">
  <?php if (!empty($isCompanyPage)): ?>
  <link rel="stylesheet" href="<?= optimizers_url('assets/css/company-pages.css?v=2') ?>">
  <?php endif; ?>
</head>
<body>

<header class="site-header" id="siteHeader">
  <div class="nav-pill">
    <a class="nav-logo" href="<?= optimizers_url('index.php#home') ?>" aria-label="Optimizers UAE home">
      <img class="op-animated-logo op-animated-logo--header" src="<?= optimizers_url('assets/images/optimizers-uae-animated-logo.svg') ?>" alt="Optimizers United Arab Emirates">
    </a>

    <nav class="nav-links" id="primaryNav" aria-label="Primary navigation">
      <?php 
      $topItems = $navTree[0] ?? [];
      if (!empty($topItems)):
        foreach ($topItems as $top):
          $topId = (int)$top['id'];
          $hasChildren = !empty($navTree[$topId]);
          $topUrl = optimizers_url($top['url']);
          $topActive = isNavActive($top['url'], $currentPage);
          $targetAttr = !empty($top['open_in_new_tab']) ? ' target="_blank" rel="noopener"' : '';

          if (!$hasChildren):
      ?>
            <a<?= $topActive ? ' class="is-current" aria-current="page"' : '' ?> href="<?= $topUrl ?>"<?= $targetAttr ?>><?= htmlspecialchars($top['label'], ENT_QUOTES, 'UTF-8') ?></a>
      <?php 
          else: 
            // Dropdown menu (e.g. Services)
            $groups = $navTree[$topId] ?? [];
      ?>
            <div class="nav-dropdown" id="servicesDropdown">
              <button class="nav-dropdown__trigger" id="servicesToggle" type="button" aria-expanded="false" aria-controls="servicesMenu">
                <span><?= htmlspecialchars($top['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m6 8 4 4 4-4"/></svg>
              </button>
              <div class="nav-dropdown__menu" id="servicesMenu">
                <div class="nav-dropdown__primary">
                  <?php foreach ($groups as $grp): 
                    $grpId = (int)$grp['id'];
                    $subItems = $navTree[$grpId] ?? [];
                    $isGroupActive = false;
                    foreach ($subItems as $chk) {
                      if (isNavActive($chk['url'], $currentPage)) {
                        $isGroupActive = true;
                        break;
                      }
                    }
                  ?>
                    <div class="nav-dropdown__nested-group">
                      <button class="nav-dropdown__parent<?= $isGroupActive ? ' is-current-group' : '' ?>" type="button" aria-expanded="false">
                        <span><?= htmlspecialchars($grp['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m8 6 4 4-4 4"/></svg>
                      </button>
                      <?php if (!empty($subItems)): ?>
                        <div class="nav-dropdown__submenu" aria-label="<?= htmlspecialchars($grp['label'], ENT_QUOTES, 'UTF-8') ?>">
                          <?php foreach ($subItems as $sub): 
                            $subUrl = optimizers_url($sub['url']);
                            $subActive = isNavActive($sub['url'], $currentPage);
                            $subTarget = !empty($sub['open_in_new_tab']) ? ' target="_blank" rel="noopener"' : '';
                          ?>
                            <a<?= $subActive ? ' class="is-current" aria-current="page"' : '' ?> href="<?= $subUrl ?>"<?= $subTarget ?>><?= htmlspecialchars($sub['label'], ENT_QUOTES, 'UTF-8') ?></a>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
      <?php 
          endif;
        endforeach;
      else:
        // Fallback if database table is not initialized yet
      ?>
        <a href="<?= optimizers_url('index.php#home') ?>">Home</a>
        <a<?= $currentPage === 'about-us' ? ' class="is-current" aria-current="page"' : '' ?> href="<?= optimizers_url('about-us.php') ?>">About</a>
        <a<?= $currentPage === 'case-studies' ? ' class="is-current" aria-current="page"' : '' ?> href="<?= optimizers_url('case-studies.php') ?>">Case Studies</a>
        <a<?= $currentPage === 'contact-us' ? ' class="is-current" aria-current="page"' : '' ?> href="<?= optimizers_url('contact-us.php') ?>">Contact</a>
      <?php endif; ?>
    </nav>

    <div class="nav-actions">
      <a class="nav-action" href="<?= optimizers_url('index.php#consultation') ?>" aria-label="Free consultation">
        <svg class="op-ui-arrow op-ui-arrow--diagonal" viewBox="0 0 512 512" aria-hidden="true"><path d="M20 256H488"/><path d="M388 152L492 256L388 360"/></svg>
      </a>
      <a class="nav-action nav-phone" href="tel:+971 50 781 8373" aria-label="Call Optimizers">
        <img src="<?= optimizers_url('assets/icons/ui/telephone.svg') ?>" alt="" aria-hidden="true">
      </a>
      <button class="menu-toggle" id="menuToggle" type="button" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span>
      </button>
    </div>
  </div>
</header>
