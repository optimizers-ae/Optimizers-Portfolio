<?php
$pageTitle = 'About Optimizers | Digital Marketing Company UAE';
$pageDescription = 'Meet Optimizers, a Dubai digital marketing company helping UAE businesses grow through consultant-led SEO, performance marketing, social media, branding, and web development.';
$currentPage = 'about-us';
$isCompanyPage = true;

$heroStats = [
  ['14+', 'Years Experience'],
  ['200+', 'Brands Grown'],
  ['30+', 'Professionals'],
  ['7+', 'Emirates Covered'],
  ['5.0', 'Google Rating'],
];

$approachSteps = [
  ['Diagnose Before We Pitch', 'Every engagement starts with an honest look at your current digital marketing, not a sales pitch. We identify what is actually working before recommending anything new.'],
  ['Prescribe, Don’t Push', 'Instead of pushing standard digital marketing packages, we recommend only the services that match your actual goals and budget.'],
  ['Explain the Strategy in Plain Terms', 'As your digital marketing consultant in Dubai, we walk you through why a strategy works before it goes live, so you understand exactly what you are paying for.'],
  ['Execute and Stay Accountable', 'Once you approve the plan, our team runs your digital marketing campaign in Dubai and across the UAE, with weekly reporting that shows real progress against real goals.'],
];

$story = [
  ['2022', 'The Beginning', 'Founded with a Single Client and a Big Idea', 'Muhammad Owais Khan started Optimizers in Dubai, convinced that UAE businesses deserved a real digital marketing consultant, not recycled agency playbooks.'],
  ['2023', 'Building the Core Team', 'First Dedicated Developer, SEO and Performance Marketing Specialists', 'As demand grew across real estate and e-commerce clients, we brought in dedicated digital marketing specialists in Dubai to lead SEO and paid media for our growing client base.'],
  ['2024', 'Expanding Across Emirates', 'From Dubai-Only to All 7 Emirates', 'We grew into a recognized digital marketing agency in Sharjah and one of the digital marketing companies in Abu Dhabi, developing vertical expertise in healthcare, hospitality, and business setup along the way.'],
  ['2025', 'Full-Stack Capability', 'Web Development and Branding Joined the Team', 'We expanded the team to deliver web development and branding directly alongside our marketing work, removing the back-and-forth that comes with juggling separate vendors.'],
  ['2026', 'Today', '30+ Professionals, One Consistent Standard', 'Today Optimizers is a team of 30+ professionals serving 10+ industries across the UAE, with AI-ready SEO (AEO/GEO) capability built for how people and AI search tools find businesses now.'],
];

$credentials = [
  ['GP', 'Google Premier Partner', 'Top-tier Google Ads & Analytics certification'],
  ['MB', 'Meta Business Partner', 'Facebook & Instagram Ads specialist status'],
  ['HS', 'HubSpot Certified Agency', 'CRM, automation & inbound marketing'],
  ['SP', 'Shopify Partner Agency', 'E-commerce development & growth'],
  ['UAE', 'UAE Registered Business', 'Licensed digital marketing entity, Dubai'],
  ['5.0', '5.0 Google Rated', '80+ verified UAE client reviews'],
  ['GA4', 'Google Analytics 4 Certified', 'Advanced GA4 measurement & attribution'],
  ['TT', 'TikTok Marketing Partner', 'Certified TikTok Ads & creator campaigns'],
  ['S', 'Semrush Certified', 'Technical & competitive SEO analysis'],
  ['AI', 'AI & Generative Search', 'Certified in AI & generative search optimisation'],
];

$reasons = [
  ['5+ Years of UAE Market Knowledge', 'We understand UAE buyer psychology, seasonal trends and regulatory nuances that international agencies simply don’t.'],
  ['Direct WhatsApp Access', 'No ticket systems. Your dedicated strategist is one message away, seven days a week.'],
  ['Consultant-Led, Not Sales-Led', 'We tell you what your digital marketing consulting engagement actually needs, even if that means recommending less, not more, of our services.'],
  ['A Team Built for Every Channel', '30+ professionals covering SEO, performance marketing, social media, web development, and branding, so every part of your campaign connects to the others.'],
  ['Radical Transparency in Reporting', 'You will always know exactly what is working, what is not, and what we are changing. No vanity metrics.'],
  ['A Proven Track Record', '200+ brands grown, a 5.0 Google rating from 80+ verified UAE clients, and active campaigns across all 10 industries we serve.'],
];

$teamMembers = [
  ['Waseem Ahmed', 'Co-Founder — Optimizers UAE', 'muhammad-waseem.png'],
  ['Huzaifa Khan', 'Director', 'huzaifa-khan.png'],
  ['Rauf Rajput', 'General Manager', 'rauf-rajput.png'],
  ['Ishmal Arshad', 'Sales Head', 'ishmal-arshad.png'],
  ['Ibrahim Khan', 'Manager', 'ibrahim-khan.png'],
  ['Abu Hurara', 'Creative Operations Manager', 'abu-hurara.png'],
  ['Maimoona Saleem', 'Head of Website Department', 'maimona-saleem.png'],
  ['Ahsan Ali', 'Performance Marketer', 'ahsan-ali.png'],
];

$e = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
require __DIR__ . '/header/header.php';
?>

<main class="op-company-page op-about-page">
  <section class="cp-hero cp-about-hero" aria-labelledby="about-page-title">
    <div class="cp-grid" aria-hidden="true"></div>
    <div class="cp-orbit cp-about-orbit" aria-hidden="true"><span>UAE</span></div>
    <div class="cp-shell cp-hero__inner">
      <span class="cp-eyebrow" data-reveal>About Us</span>
      <h1 id="about-page-title" data-reveal>About Optimizers:<span>#1 Digital Marketing Company in UAE</span></h1>
      <p class="cp-hero__copy" data-reveal>Optimizers is a digital marketing company based in Dubai, built to help UAE businesses turn digital strategy into real revenue. We work with brands across real estate, healthcare, e-commerce, hospitality, and technology, combining local market knowledge with measurable execution. This page tells you who we are, how we got here, and how we think about growing your business in the UAE.</p>
      <a class="cp-button cp-button--light" href="contact-us.php" data-reveal>Get Free Consultation</a>
      <div class="cp-hero-stats" data-reveal aria-label="Optimizers company statistics">
        <?php foreach ($heroStats as [$value, $label]): ?>
          <div class="cp-hero-stat"><strong><?= $e($value) ?></strong><span><?= $e($label) ?></span></div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="cp-section cp-who" aria-labelledby="who-title">
    <div class="cp-shell cp-split-head" data-reveal>
      <div><span class="cp-eyebrow">Who We Are</span><h2 id="who-title">A Dubai Digital Marketing Company<span>Built for Real Results.</span></h2></div>
      <div class="cp-lead-copy">
        <p>Optimizers is a full-stack digital marketing company based in Dubai, working with business owners across all 7 Emirates who want digital marketing solutions built around real results, not recycled templates.</p>
        <p>We combine deep UAE market intelligence with modern digital marketing campaigns in Dubai, covering SEO, performance marketing, social media, branding, and web development, delivered by a team of 30+ professionals who treat every client’s growth as their own responsibility.</p>
      </div>
    </div>
    <div class="cp-shell cp-principle-grid">
      <article class="cp-principle" data-reveal><span class="cp-number">01</span><h3>Full-channel team</h3><p>Strategists, developers, designers, and marketers covering every part of your digital presence.</p></article>
      <article class="cp-principle" data-reveal><span class="cp-number">02</span><h3>UAE-first strategy</h3><p>Built around local consumer behavior, not copy-pasted global templates.</p></article>
      <article class="cp-principle" data-reveal><span class="cp-number">03</span><h3>Transparent reporting</h3><p>You see exactly what is working, what is not, and what we are doing about it.</p></article>
    </div>
  </section>

  <section class="cp-section cp-founder" aria-labelledby="founder-title">
    <div class="cp-shell cp-founder-grid">
      <div class="cp-founder-portrait" data-reveal>
        <div class="cp-portrait-frame"><img src="assets/images/team/owais-khan.png" alt="Muhammad Owais Khan, Founder and CEO of Optimizers"></div>
        <div class="cp-founder-badge"><strong>14+</strong><span>Years Leading<br>Digital Growth</span></div>
      </div>
      <div class="cp-founder-copy" data-reveal>
        <span class="cp-eyebrow">Meet the Founder</span>
        <h2 id="founder-title">Muhammad Owais Khan<span>Founder &amp; CEO, Optimizers</span></h2>
        <blockquote>“Our job isn’t to run your ads. It’s to understand your business well enough to tell you the truth — even when it’s not what you want to hear.”</blockquote>
        <p>Owais founded Optimizers in 2022 after seeing too many UAE businesses burned by agencies selling reports instead of results. With a background spanning performance marketing, SEO and business strategy, he’s trained over 3,000 marketing professionals and personally led 300+ projects across Real Estate, Healthcare, E-Commerce and Hospitality sectors in the UAE.</p>
        <p>His philosophy is simple: act as a consultant first, vendor second. That means asking hard questions before pitching solutions — and staying accountable to the numbers that actually matter to your bottom line.</p>
        <div class="cp-founder-stats"><div><strong>14+</strong><span>Years Experience</span></div><div><strong>200+</strong><span>Brands Guided</span></div><div><strong>30+</strong><span>Team Members</span></div><div><strong>10+</strong><span>Industries Served</span></div></div>
      </div>
    </div>
  </section>

  <section class="cp-section cp-approach" aria-labelledby="approach-title">
    <div class="cp-shell cp-section-intro" data-reveal><span class="cp-eyebrow">Our Approach</span><h2 id="approach-title">We Think Like Consultants,<span>Not Like Vendors.</span></h2><p>Most agencies start with a service to sell. We start by understanding whether you even need it.</p></div>
    <div class="cp-shell cp-approach-grid">
      <?php foreach ($approachSteps as $index => [$title, $copy]): ?>
        <article class="cp-approach-card" data-reveal><div class="cp-approach-top"><span>Step</span><strong><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></strong></div><h3><?= $e($title) ?></h3><?php if ($index === 1): ?><h4>Recommend What You Need, Not a Fixed Package</h4><?php endif; ?><p><?= $e($copy) ?></p></article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cp-section cp-story" aria-labelledby="story-title">
    <div class="cp-shell cp-story-layout">
      <div class="cp-story-head" data-reveal><span class="cp-eyebrow">Our Story</span><h2 id="story-title">From a Single Client to a<span>30+ Professional Growth Team.</span></h2><p>Every milestone in our journey was driven by one thing: client results that spoke for themselves.</p></div>
      <div class="cp-timeline">
        <?php foreach ($story as [$year, $phase, $title, $copy]): ?>
          <article class="cp-timeline-item" data-reveal><div class="cp-timeline-year"><strong><?= $e($year) ?></strong><span><?= $e($phase) ?></span></div><div><h3><?= $e($title) ?></h3><p><?= $e($copy) ?></p></div></article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="cp-section cp-credentials" aria-labelledby="credentials-title">
    <div class="cp-shell cp-split-head" data-reveal><div><span class="cp-eyebrow">Certifications &amp; Credentials</span><h2 id="credentials-title">Certified Where It<span>Actually Matters.</span></h2></div><p>Google Premier Partner — certified Google Ads and Analytics expertise with top-tier partner status. Meta Business Partner — certified Facebook and Instagram advertising specialists.</p></div>
    <div class="cp-shell cp-credential-grid">
      <?php foreach ($credentials as [$mark, $title, $copy]): ?>
        <article class="cp-credential" data-reveal><span class="cp-credential-mark"><?= $e($mark) ?></span><div><h3><?= $e($title) ?></h3><p><?= $e($copy) ?></p></div></article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cp-section cp-reasons" aria-labelledby="reasons-title">
    <div class="cp-shell cp-section-intro" data-reveal><span class="cp-eyebrow">Why Optimizers</span><h2 id="reasons-title">Why UAE Businesses<span>Choose Us, and Stay.</span></h2><p>It’s never just one thing. It’s the combination of local expertise, full accountability, and consultant-level thinking.</p></div>
    <div class="cp-shell cp-reason-grid">
      <?php foreach ($reasons as $index => [$title, $copy]): ?>
        <article class="cp-reason" data-reveal><span class="cp-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><h3><?= $e($title) ?></h3><p><?= $e($copy) ?></p></article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cp-section cp-team" aria-labelledby="team-title">
    <div class="cp-shell cp-split-head" data-reveal><div><span class="cp-eyebrow">Our Team</span><h2 id="team-title">30+ Specialists,<span>One Accountable Team.</span></h2></div><p>Every department is staffed in-house — strategists, developers, designers and marketers who know your account personally.</p></div>
    <div class="cp-shell cp-team-grid">
      <?php foreach ($teamMembers as [$name, $role, $image]): ?>
        <article class="cp-team-card" data-reveal><div class="cp-team-photo"><img src="assets/images/team/<?= $e($image) ?>" alt="<?= $e($name) ?>"></div><div class="cp-team-copy"><h3><?= $e($name) ?></h3><p><?= $e($role) ?></p></div></article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cp-strategy" aria-labelledby="strategy-title">
    <div class="cp-strategy-orb" aria-hidden="true"></div>
    <div class="cp-shell cp-strategy-inner" data-reveal><span class="cp-eyebrow">Let’s Talk Strategy</span><h2 id="strategy-title">Ready to Work With a Digital Marketing Consultant<span>Who Actually Cares About Results?</span></h2><p>Book a free strategy session with the digital marketing company UAE businesses trust for honest, consultant-led campaigns. We will look at your current digital presence and tell you exactly where the opportunity is, no obligation.</p><div class="cp-cta-row"><a class="cp-button cp-button--light" href="contact-us.php">Book Free Strategy Session</a><a class="cp-button cp-button--whatsapp wa-unified-button" href="https://wa.me/971507818373" target="_blank" rel="noopener"><img src="assets/icons/brand/whatsapp.svg" alt="" aria-hidden="true">Chat on WhatsApp</a></div></div>
  </section>
</main>

<?php require __DIR__ . '/footer/footer.php'; ?>
