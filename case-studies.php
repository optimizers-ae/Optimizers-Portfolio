<?php
$pageTitle = 'Case Studies & Client Results | Optimizers UAE';
$pageDescription = 'Discover how Optimizers UAE delivers measurable digital growth through SEO, Google Ads, social media, and web development case studies.';
$currentPage = 'case-studies';

require_once __DIR__ . '/includes/db.php';

try {
    $db = OptimizersDB::getConnection();

    /*
     * ============================================================
     * INITIAL CATEGORY
     * ============================================================
     */

    $rawCategory = trim((string)($_GET['category'] ?? ''));

    if (
        $rawCategory === 'real-numbers-client-results' ||
        $rawCategory === 'Real Numbers. Real Clients. Real Growth.'
    ) {
        $activeCategoryKey = 'real-numbers-client-results';
        $activeCategoryTitle = 'Real Numbers. Real Clients. Real Growth.';
    } else {
        $activeCategoryKey = 'digital-marketing-tips';
        $activeCategoryTitle = 'Digital Marketing Tips for UAE Business Owners';
    }


    /*
     * ============================================================
     * FILTER OPTIONS
     * ============================================================
     */

    $industries = $db->query("
        SELECT DISTINCT industry
        FROM case_studies
        WHERE status = 'published'
          AND industry IS NOT NULL
          AND industry != ''
        ORDER BY industry ASC
    ")->fetchAll(PDO::FETCH_COLUMN);


    $servicesList = $db->query("
        SELECT DISTINCT title
        FROM services
        WHERE is_active = 1
        ORDER BY display_order ASC, title ASC
    ")->fetchAll(PDO::FETCH_COLUMN);


    $tagsList = $db->query("
        SELECT DISTINCT name
        FROM tags
        ORDER BY name ASC
    ")->fetchAll(PDO::FETCH_COLUMN);


    $techsList = $db->query("
        SELECT DISTINCT name
        FROM technologies
        ORDER BY name ASC
    ")->fetchAll(PDO::FETCH_COLUMN);


    /*
     * ============================================================
     * INITIAL SECONDARY FILTER VALUES
     * ============================================================
     *
     * These are only used to restore filters when URL contains them.
     * Actual filtering is handled by JavaScript without reload.
     */

    $selectedIndustry = trim((string)($_GET['industry'] ?? ''));
    $selectedService = trim((string)($_GET['service'] ?? ''));
    $selectedTag = trim((string)($_GET['tag'] ?? ''));
    $selectedTech = trim((string)($_GET['tech'] ?? ''));
    $searchQuery = trim((string)($_GET['search'] ?? ''));


    /*
     * ============================================================
     * FETCH ALL PUBLISHED CASE STUDIES
     * ============================================================
     *
     * IMPORTANT:
     * No category/service/tag/technology filter is applied here.
     * All published cards are loaded once.
     * JavaScript performs all filtering instantly.
     */

    $stmt = $db->prepare("
        SELECT *
        FROM case_studies
        WHERE status = 'published'
        ORDER BY display_order ASC, created_at DESC
    ");

    $stmt->execute();

    $caseStudies = $stmt->fetchAll();


} catch (Throwable $e) {

    $activeCategoryKey = 'digital-marketing-tips';
    $activeCategoryTitle = 'Digital Marketing Tips for UAE Business Owners';

    $industries = [];
    $servicesList = [];
    $tagsList = [];
    $techsList = [];

    $caseStudies = [];

    $selectedIndustry = '';
    $selectedService = '';
    $selectedTag = '';
    $selectedTech = '';
    $searchQuery = '';
}


$hasActiveAdvancedFilters = (
    $selectedIndustry !== '' ||
    $selectedService !== '' ||
    $selectedTag !== '' ||
    $selectedTech !== '' ||
    $searchQuery !== ''
);


require __DIR__ . '/header/header.php';
?>


<main class="op-case-archive-page">

  <!-- Case Studies Page Hero Banner -->
  <section class="op-case-hero">

    <div class="op-case-hero__glow" aria-hidden="true"></div>

    <div class="op-case-hero__inner">

      <span class="op-case-hero__eyebrow">
        Client Success Stories
      </span>

      <h1>
        Proven Digital Growth
        <span>in the UAE & GCC Market.</span>
      </h1>

      <p class="op-case-hero__lead">
        Explore detailed breakdowns of how our integrated search, performance marketing, creative reels, and custom web development drive revenue for leading businesses.
      </p>

    </div>

  </section>


  <!-- Filter & Grid Section -->
  <section class="op-case-archive-main">

    <div class="op-case-archive-main__inner">


      <!-- ======================================================
           CATEGORY FILTER
           ====================================================== -->

      <div
        class="op-case-filters"
        style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;"
      >

        <a
          href="<?= optimizers_url('case-studies.php?category=digital-marketing-tips') ?>"
          class="op-case-filter-pill <?= ($activeCategoryKey === 'digital-marketing-tips') ? 'is-active' : '' ?>"
          data-category-filter="digital-marketing-tips"
        >
          Digital Marketing Tips for UAE Business Owners
        </a>


        <a
          href="<?= optimizers_url('case-studies.php?category=real-numbers-client-results') ?>"
          class="op-case-filter-pill <?= ($activeCategoryKey === 'real-numbers-client-results') ? 'is-active' : '' ?>"
          data-category-filter="real-numbers-client-results"
        >
          Real Numbers. Real Clients. Real Growth.
        </a>

      </div>


      <!-- ======================================================
           ADVANCED FILTERS
           ====================================================== -->

      <form
        method="GET"
        action="<?= optimizers_url('case-studies.php') ?>"
        class="op-case-filter-row"
        style="margin-top: 14px; margin-bottom: 28px;"
        id="caseFilterForm"
      >

        <input
          type="hidden"
          name="category"
          value="<?= htmlspecialchars($activeCategoryKey, ENT_QUOTES, 'UTF-8') ?>"
          id="currentCategoryInput"
        >


        <!-- ====================================================
             INDUSTRY
             ==================================================== -->

        <?php if (!empty($industries)): ?>

          <select
            name="industry"
            class="op-case-filter-select <?= $selectedIndustry !== '' ? 'has-value' : '' ?>"
            id="industryFilter"
          >

            <option value="">
              Filter by Industry
            </option>

            <?php foreach ($industries as $ind): ?>

              <option
                value="<?= htmlspecialchars($ind, ENT_QUOTES, 'UTF-8') ?>"
                <?= $selectedIndustry === $ind ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($ind, ENT_QUOTES, 'UTF-8') ?>
              </option>

            <?php endforeach; ?>

          </select>

        <?php endif; ?>


        <!-- ====================================================
             SERVICE
             ==================================================== -->

        <?php if (!empty($servicesList)): ?>

          <select
            name="service"
            class="op-case-filter-select <?= $selectedService !== '' ? 'has-value' : '' ?>"
            id="serviceFilter"
          >

            <option value="">
              Filter by Service
            </option>

            <?php foreach ($servicesList as $srvName): ?>

              <option
                value="<?= htmlspecialchars($srvName, ENT_QUOTES, 'UTF-8') ?>"
                <?= $selectedService === $srvName ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($srvName, ENT_QUOTES, 'UTF-8') ?>
              </option>

            <?php endforeach; ?>

          </select>

        <?php endif; ?>


        <!-- ====================================================
             TAG
             ==================================================== -->

        <?php if (!empty($tagsList)): ?>

          <select
            name="tag"
            class="op-case-filter-select <?= $selectedTag !== '' ? 'has-value' : '' ?>"
            id="tagFilter"
          >

            <option value="">
              Filter by Tag
            </option>

            <?php foreach ($tagsList as $tg): ?>

              <option
                value="<?= htmlspecialchars($tg, ENT_QUOTES, 'UTF-8') ?>"
                <?= $selectedTag === $tg ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($tg, ENT_QUOTES, 'UTF-8') ?>
              </option>

            <?php endforeach; ?>

          </select>

        <?php endif; ?>


        <!-- ====================================================
             TECHNOLOGY
             ==================================================== -->

        <?php if (!empty($techsList)): ?>

          <select
            name="tech"
            class="op-case-filter-select <?= $selectedTech !== '' ? 'has-value' : '' ?>"
            id="techFilter"
          >

            <option value="">
              Filter by Technology
            </option>

            <?php foreach ($techsList as $tc): ?>

              <option
                value="<?= htmlspecialchars($tc, ENT_QUOTES, 'UTF-8') ?>"
                <?= $selectedTech === $tc ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($tc, ENT_QUOTES, 'UTF-8') ?>
              </option>

            <?php endforeach; ?>

          </select>

        <?php endif; ?>


        <!-- ====================================================
             CLEAR FILTERS
             ==================================================== -->

        <?php if ($hasActiveAdvancedFilters): ?>

          <a
            href="<?= optimizers_url('case-studies.php?category=' . $activeCategoryKey) ?>"
            class="op-case-filter-clear"
            id="clearSecondaryFilters"
          >
            ✕ Clear Secondary Filters
          </a>

        <?php else: ?>

          <a
            href="<?= optimizers_url('case-studies.php?category=' . $activeCategoryKey) ?>"
            class="op-case-filter-clear"
            id="clearSecondaryFilters"
            style="display: none;"
          >
            ✕ Clear Secondary Filters
          </a>

        <?php endif; ?>

      </form>


      <!-- ======================================================
           CASE STUDIES GRID
           ====================================================== -->

      <?php if (!empty($caseStudies)): ?>

        <div
          class="op-case-grid"
          id="caseStudiesGrid"
        >

          <?php foreach ($caseStudies as $index => $cs):

            /*
             * ----------------------------------------------------
             * CATEGORY NORMALIZATION
             * ----------------------------------------------------
             */

            $rawCsCategory = trim(
                (string)($cs['category'] ?? '')
            );

            if (
                $rawCsCategory === 'Digital Marketing Tips for UAE Business Owners' ||
                $rawCsCategory === 'digital-marketing-tips' ||
                stripos($rawCsCategory, 'Tips') !== false ||
                stripos($rawCsCategory, 'DIGITAL MARKETING') !== false ||
                stripos($rawCsCategory, 'TikTok') !== false
            ) {

                $normalizedCategory = 'digital-marketing-tips';

            } else {

                $normalizedCategory = 'real-numbers-client-results';

            }


            /*
             * ----------------------------------------------------
             * TAG DATA
             * ----------------------------------------------------
             */

            $tags = json_decode(
                $cs['tags'] ?? '[]',
                true
            ) ?: [];


            /*
             * Convert tags into searchable string
             */

            $tagSearchString = '';

            if (!empty($tags)) {

                $tagSearchString = implode(
                    ' ',
                    array_map(
                        'strval',
                        $tags
                    )
                );

            }


            /*
             * ----------------------------------------------------
             * TECHNOLOGY DATA
             * ----------------------------------------------------
             */

            $technologySearchString =
                (string)($cs['technologies'] ?? '');


            /*
             * ----------------------------------------------------
             * SERVICE DATA
             * ----------------------------------------------------
             */

            $serviceSearchString =
                (string)($cs['services'] ?? '');


            /*
             * ----------------------------------------------------
             * INDUSTRY DATA
             * ----------------------------------------------------
             */

            $industrySearchString =
                (string)($cs['industry'] ?? '');


            /*
             * ----------------------------------------------------
             * SEARCH DATA
             * ----------------------------------------------------
             */

            $generalSearchString = implode(
                ' ',
                [
                    (string)($cs['title'] ?? ''),
                    (string)($cs['card_title'] ?? ''),
                    (string)($cs['short_description'] ?? ''),
                    (string)($cs['client_name'] ?? ''),
                    (string)($cs['author'] ?? ''),
                    (string)($cs['category'] ?? ''),
                    $tagSearchString,
                    $technologySearchString,
                    $serviceSearchString,
                    $industrySearchString
                ]
            );


            /*
             * ----------------------------------------------------
             * IMAGE
             * ----------------------------------------------------
             */

            $coverImg = !empty($cs['featured_image'])
                ? optimizers_url($cs['featured_image'])
                : optimizers_url(
                    'assets/images/service-pages/google-ads-hero.svg'
                );


            /*
             * ----------------------------------------------------
             * TITLE
             * ----------------------------------------------------
             */

            $displayTitle = !empty($cs['card_title'])
                ? $cs['card_title']
                : $cs['title'];


            /*
             * ----------------------------------------------------
             * DETAIL URL
             * ----------------------------------------------------
             */

            $detailUrl = optimizers_url(
                'case-studies/' . rawurlencode($cs['slug'])
            );


            /*
             * ----------------------------------------------------
             * REVEAL DELAY
             * ----------------------------------------------------
             */

            $delayStr = '.' .
                str_pad(
                    (string)(($index % 3) * 5 + 4),
                    2,
                    '0',
                    STR_PAD_LEFT
                ) .
                's';

          ?>


            <article
              class="op-case-card"
              data-reveal

              data-case-category="<?= htmlspecialchars(
                  $normalizedCategory,
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>"

              data-case-industry="<?= htmlspecialchars(
                  strtolower($industrySearchString),
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>"

              data-case-service="<?= htmlspecialchars(
                  strtolower($serviceSearchString),
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>"

              data-case-tags="<?= htmlspecialchars(
                  strtolower($tagSearchString),
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>"

              data-case-tech="<?= htmlspecialchars(
                  strtolower($technologySearchString),
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>"

              data-case-search="<?= htmlspecialchars(
                  strtolower($generalSearchString),
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>"

              style="--reveal-delay: <?= $delayStr ?>"
            >


              <a
                href="<?= htmlspecialchars(
                    $detailUrl,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                class="op-case-card__link"
                aria-label="Read case study: <?= htmlspecialchars(
                    $displayTitle,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
              >


                <div class="op-case-card__media">

                  <img
                    src="<?= $coverImg ?>"
                    alt="<?= htmlspecialchars(
                        $displayTitle,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    loading="lazy"
                    onerror="this.onerror=null; this.src='<?= optimizers_url(
                        'assets/images/service-pages/google-ads-hero.svg'
                    ) ?>';"
                  >


                  <div class="op-case-card__badge-wrap">

                    <span class="op-case-card__category">
                      <?= htmlspecialchars(
                          $cs['category'],
                          ENT_QUOTES,
                          'UTF-8'
                      ) ?>
                    </span>

                  </div>

                </div>


                <div class="op-case-card__body">

                  <h3 class="op-case-card__title">
                    <?= htmlspecialchars(
                        $displayTitle,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                  </h3>


                  <p class="op-case-card__excerpt">
                    <?= htmlspecialchars(
                        $cs['short_description'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                  </p>


                  <div class="op-case-card__footer">

                    <?php if (!empty($tags)): ?>

                      <div class="op-case-card__tags">

                        <?php foreach (
                            array_slice($tags, 0, 3)
                            as $tg
                        ): ?>

                          <span class="op-case-card__tag">
                            <?= htmlspecialchars(
                                (string)$tg,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                          </span>

                        <?php endforeach; ?>

                      </div>

                    <?php endif; ?>


                    <span class="op-case-card__cta">

                      <span>
                        View Case Study
                      </span>

                      <svg
                        class="op-ui-arrow op-ui-arrow--diagonal"
                        viewBox="0 0 512 512"
                        aria-hidden="true"
                      >
                        <path d="M20 256H488"/>
                        <path d="M388 152L492 256L388 360"/>
                      </svg>

                    </span>

                  </div>

                </div>

              </a>

            </article>


          <?php endforeach; ?>

        </div>


        <!-- ======================================================
             JS EMPTY RESULT
             ====================================================== -->

        <div
          class="op-case-empty"
          id="jsCategoryEmpty"
          style="display: none; text-align: center; padding: 80px 20px;"
        >

          <div
            style="font-size: 2.5rem; margin-bottom: 12px; color: rgba(255,255,255,0.2);"
          >
            <i class="fa-solid fa-folder-open"></i>
          </div>

          <h3
            style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 8px;"
          >
            No Case Studies Found
          </h3>

          <p
            style="color: rgba(255,255,255,0.5); font-size: 0.9rem; max-width: 420px; margin: 0 auto 20px;"
          >
            No published projects matched your selected category or filter criteria.
          </p>

        </div>


      <?php else: ?>


        <!-- ======================================================
             SERVER EMPTY STATE
             ====================================================== -->

        <div
          class="op-case-empty"
          style="text-align: center; padding: 80px 20px;"
        >

          <div
            style="font-size: 2.5rem; margin-bottom: 12px; color: rgba(255,255,255,0.2);"
          >
            <i class="fa-solid fa-folder-open"></i>
          </div>


          <h3
            style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 8px;"
          >
            No Case Studies Found
          </h3>


          <p
            style="color: rgba(255,255,255,0.5); font-size: 0.9rem; max-width: 420px; margin: 0 auto 20px;"
          >
            No published projects matched your selected category or filter criteria.
          </p>


          <a
            href="<?= optimizers_url(
                'case-studies.php?category=' . $activeCategoryKey
            ) ?>"
            class="op-btn op-btn--light"
          >
            <span>
              Reset Category Filters
            </span>
          </a>

        </div>


      <?php endif; ?>


    </div>

  </section>


  <!-- Consultation Banner Strip -->
  <section
    class="op-case-cta-strip"
    style="border-top: 1px solid rgba(255,255,255,0.06); padding: 80px 0; background: radial-gradient(circle at 50% 50%, rgba(0,229,255,0.04) 0%, transparent 60%); text-align: center;"
  >

    <div
      style="max-width: 720px; margin: 0 auto; padding: 0 20px;"
    >

      <span
        style="display: inline-block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em; color: #00E5FF; margin-bottom: 12px;"
      >
        Ready for Real Results?
      </span>


      <h2
        style="font-size: clamp(1.8rem, 4vw, 2.5rem); font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 16px;"
      >
        Scale Your Digital Acquisition in the UAE
      </h2>


      <p
        style="color: rgba(255,255,255,0.6); font-size: 1rem; line-height: 1.7; margin-bottom: 32px;"
      >
        Discover how our performance marketing and search engine strategies can reduce your lead costs and accelerate business revenue.
      </p>


      <a
        href="<?= optimizers_url('index.php#consultation') ?>"
        class="op-btn op-btn--light"
      >

        <span>
          Request a Free Strategy Audit
        </span>

        <svg
          class="op-ui-arrow op-ui-arrow--diagonal"
          viewBox="0 0 512 512"
          aria-hidden="true"
        >
          <path d="M20 256H488"/>
          <path d="M388 152L492 256L388 360"/>
        </svg>

      </a>

    </div>

  </section>

</main>


<!-- ================================================================
     NO-RELOAD FILTER JAVASCRIPT
     ================================================================ -->

<script>
(function () {

    'use strict';


    /*
     * ============================================================
     * ELEMENTS
     * ============================================================
     */

    const categoryButtons = document.querySelectorAll(
        '[data-category-filter]'
    );

    const caseCards = Array.from(
        document.querySelectorAll(
            '[data-case-category]'
        )
    );

    const industryFilter =
        document.getElementById('industryFilter');

    const serviceFilter =
        document.getElementById('serviceFilter');

    const tagFilter =
        document.getElementById('tagFilter');

    const techFilter =
        document.getElementById('techFilter');

    const currentCategoryInput =
        document.getElementById('currentCategoryInput');

    const clearFilters =
        document.getElementById('clearSecondaryFilters');

    const emptyMessage =
        document.getElementById('jsCategoryEmpty');


    /*
     * ============================================================
     * INITIAL VALUES
     * ============================================================
     */

    let currentCategory =
        '<?= htmlspecialchars(
            $activeCategoryKey,
            ENT_QUOTES,
            'UTF-8'
        ) ?>';


    /*
     * ============================================================
     * HELPER
     * ============================================================
     */

    function normalize(value) {

        return String(value || '')
            .trim()
            .toLowerCase();

    }


    /*
     * ============================================================
     * CHECK TEXT MATCH
     * ============================================================
     *
     * This uses partial matching.
     *
     * Example:
     *
     * Filter = "Google Ads"
     *
     * Card data = "Google Ads, SEO, Marketing"
     *
     * Result = MATCH
     */

    function matchesFilter(
        cardValue,
        selectedValue
    ) {

        const selected =
            normalize(selectedValue);

        if (!selected) {
            return true;
        }

        const card =
            normalize(cardValue);

        return card.includes(selected);
    }


    /*
     * ============================================================
     * UPDATE URL WITHOUT RELOAD
     * ============================================================
     */

    function updateUrl() {

        if (
            !window.history ||
            !window.history.replaceState
        ) {
            return;
        }


        const url =
            new URL(window.location.href);


        /*
         * CATEGORY
         */

        url.searchParams.set(
            'category',
            currentCategory
        );


        /*
         * INDUSTRY
         */

        const industryValue =
            industryFilter
                ? industryFilter.value
                : '';

        if (industryValue) {

            url.searchParams.set(
                'industry',
                industryValue
            );

        } else {

            url.searchParams.delete(
                'industry'
            );

        }


        /*
         * SERVICE
         */

        const serviceValue =
            serviceFilter
                ? serviceFilter.value
                : '';

        if (serviceValue) {

            url.searchParams.set(
                'service',
                serviceValue
            );

        } else {

            url.searchParams.delete(
                'service'
            );

        }


        /*
         * TAG
         */

        const tagValue =
            tagFilter
                ? tagFilter.value
                : '';

        if (tagValue) {

            url.searchParams.set(
                'tag',
                tagValue
            );

        } else {

            url.searchParams.delete(
                'tag'
            );

        }


        /*
         * TECHNOLOGY
         */

        const techValue =
            techFilter
                ? techFilter.value
                : '';

        if (techValue) {

            url.searchParams.set(
                'tech',
                techValue
            );

        } else {

            url.searchParams.delete(
                'tech'
            );

        }


        /*
         * Replace URL.
         *
         * IMPORTANT:
         * This does NOT reload the page.
         */

        window.history.replaceState(
            {
                category: currentCategory
            },
            '',
            url.toString()
        );

    }


    /*
     * ============================================================
     * UPDATE ACTIVE CATEGORY BUTTON
     * ============================================================
     */

    function updateCategoryButtons() {

        categoryButtons.forEach(
            function (button) {

                const category =
                    button.getAttribute(
                        'data-category-filter'
                    );


                if (
                    category === currentCategory
                ) {

                    button.classList.add(
                        'is-active'
                    );

                } else {

                    button.classList.remove(
                        'is-active'
                    );

                }

            }
        );

    }


    /*
     * ============================================================
     * UPDATE CLEAR BUTTON
     * ============================================================
     */

    function updateClearButton() {

        if (!clearFilters) {
            return;
        }


        const hasFilters =
            Boolean(
                industryFilter &&
                industryFilter.value
            ) ||
            Boolean(
                serviceFilter &&
                serviceFilter.value
            ) ||
            Boolean(
                tagFilter &&
                tagFilter.value
            ) ||
            Boolean(
                techFilter &&
                techFilter.value
            );


        if (hasFilters) {

            clearFilters.style.display = '';

        } else {

            clearFilters.style.display = 'none';

        }

    }


    /*
     * ============================================================
     * MAIN FILTER FUNCTION
     * ============================================================
     */

    function applyAllFilters(
        shouldUpdateUrl = true
    ) {

        const selectedIndustry =
            industryFilter
                ? industryFilter.value
                : '';

        const selectedService =
            serviceFilter
                ? serviceFilter.value
                : '';

        const selectedTag =
            tagFilter
                ? tagFilter.value
                : '';

        const selectedTech =
            techFilter
                ? techFilter.value
                : '';


        let visibleCards = 0;


        /*
         * --------------------------------------------------------
         * FILTER EACH CARD
         * --------------------------------------------------------
         */

        caseCards.forEach(
            function (card) {

                const cardCategory =
                    card.getAttribute(
                        'data-case-category'
                    ) || '';


                const cardIndustry =
                    card.getAttribute(
                        'data-case-industry'
                    ) || '';


                const cardService =
                    card.getAttribute(
                        'data-case-service'
                    ) || '';


                const cardTags =
                    card.getAttribute(
                        'data-case-tags'
                    ) || '';


                const cardTech =
                    card.getAttribute(
                        'data-case-tech'
                    ) || '';


                /*
                 * CATEGORY MATCH
                 */

                const categoryMatch =
                    cardCategory === currentCategory;


                /*
                 * INDUSTRY MATCH
                 */

                const industryMatch =
                    matchesFilter(
                        cardIndustry,
                        selectedIndustry
                    );


                /*
                 * SERVICE MATCH
                 */

                const serviceMatch =
                    matchesFilter(
                        cardService,
                        selectedService
                    );


                /*
                 * TAG MATCH
                 */

                const tagMatch =
                    matchesFilter(
                        cardTags,
                        selectedTag
                    );


                /*
                 * TECHNOLOGY MATCH
                 */

                const techMatch =
                    matchesFilter(
                        cardTech,
                        selectedTech
                    );


                /*
                 * ALL FILTERS MUST MATCH
                 */

                const shouldShow =
                    categoryMatch &&
                    industryMatch &&
                    serviceMatch &&
                    tagMatch &&
                    techMatch;


                if (shouldShow) {

                    card.style.display = '';

                    visibleCards++;

                } else {

                    card.style.display = 'none';

                }

            }
        );


        /*
         * --------------------------------------------------------
         * EMPTY STATE
         * --------------------------------------------------------
         */

        if (emptyMessage) {

            if (visibleCards === 0) {

                emptyMessage.style.display = '';

            } else {

                emptyMessage.style.display = 'none';

            }

        }


        /*
         * --------------------------------------------------------
         * CATEGORY BUTTON
         * --------------------------------------------------------
         */

        updateCategoryButtons();


        /*
         * --------------------------------------------------------
         * CLEAR BUTTON
         * --------------------------------------------------------
         */

        updateClearButton();


        /*
         * --------------------------------------------------------
         * URL
         * --------------------------------------------------------
         */

        if (shouldUpdateUrl) {

            updateUrl();

        }

    }


    /*
     * ============================================================
     * CATEGORY CLICK
     * ============================================================
     */

    categoryButtons.forEach(
        function (button) {

            button.addEventListener(
                'click',
                function (event) {

                    /*
                     * STOP PAGE NAVIGATION
                     */

                    event.preventDefault();


                    const category =
                        button.getAttribute(
                            'data-category-filter'
                        );


                    if (!category) {
                        return;
                    }


                    currentCategory =
                        category;


                    /*
                     * Category change only.
                     *
                     * Secondary filters remain active.
                     */

                    applyAllFilters(true);

                }
            );

        }
    );


    /*
     * ============================================================
     * INDUSTRY CHANGE
     * ============================================================
     */

    if (industryFilter) {

        industryFilter.addEventListener(
            'change',
            function () {

                /*
                 * IMPORTANT:
                 * No form.submit()
                 *
                 * Therefore NO reload.
                 */

                applyAllFilters(true);

            }
        );

    }


    /*
     * ============================================================
     * SERVICE CHANGE
     * ============================================================
     */

    if (serviceFilter) {

        serviceFilter.addEventListener(
            'change',
            function () {

                /*
                 * IMPORTANT:
                 * No form.submit()
                 *
                 * Therefore NO reload.
                 */

                applyAllFilters(true);

            }
        );

    }


    /*
     * ============================================================
     * TAG CHANGE
     * ============================================================
     */

    if (tagFilter) {

        tagFilter.addEventListener(
            'change',
            function () {

                /*
                 * IMPORTANT:
                 * No form.submit()
                 *
                 * Therefore NO reload.
                 */

                applyAllFilters(true);

            }
        );

    }


    /*
     * ============================================================
     * TECHNOLOGY CHANGE
     * ============================================================
     */

    if (techFilter) {

        techFilter.addEventListener(
            'change',
            function () {

                /*
                 * IMPORTANT:
                 * No form.submit()
                 *
                 * Therefore NO reload.
                 */

                applyAllFilters(true);

            }
        );

    }


    /*
     * ============================================================
     * CLEAR SECONDARY FILTERS
     * ============================================================
     */

    if (clearFilters) {

        clearFilters.addEventListener(
            'click',
            function (event) {

                /*
                 * STOP NORMAL LINK NAVIGATION
                 */

                event.preventDefault();


                /*
                 * RESET SELECTS
                 */

                if (industryFilter) {
                    industryFilter.value = '';
                }

                if (serviceFilter) {
                    serviceFilter.value = '';
                }

                if (tagFilter) {
                    tagFilter.value = '';
                }

                if (techFilter) {
                    techFilter.value = '';
                }


                /*
                 * REMOVE has-value class
                 */

                [
                    industryFilter,
                    serviceFilter,
                    tagFilter,
                    techFilter
                ].forEach(
                    function (select) {

                        if (select) {

                            select.classList.remove(
                                'has-value'
                            );

                        }

                    }
                );


                /*
                 * Apply only category filter
                 */

                applyAllFilters(true);

            }
        );

    }


    /*
     * ============================================================
     * INITIAL FILTER
     * ============================================================
     *
     * When page opens with:
     *
     * ?category=real-numbers-client-results
     * &service=SEO
     *
     * both filters will automatically apply.
     */

    applyAllFilters(false);


})();
</script>


<?php require __DIR__ . '/footer/footer.php'; ?>