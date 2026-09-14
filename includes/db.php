<?php
declare(strict_types=1);

if (!function_exists('optimizers_get_base_url')) {
    function optimizers_get_base_url(): string {
        static $baseUrl = null;
        if ($baseUrl !== null) {
            return $baseUrl;
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($scriptName));
        $baseDir = rtrim($dir, '/');

        if (substr($baseDir, -13) === '/case-studies') {
            $baseDir = substr($baseDir, 0, -13);
        } elseif (substr($baseDir, -9) === '/services') {
            $baseDir = substr($baseDir, 0, -9);
        } elseif (substr($baseDir, -6) === '/admin') {
            $baseDir = substr($baseDir, 0, -6);
        }

        $baseUrl = ($baseDir === '' || $baseDir === '.') ? '/' : $baseDir . '/';
        return $baseUrl;
    }
}

if (!function_exists('optimizers_url')) {
    function optimizers_url(string $path = ''): string {
        $base = optimizers_get_base_url();
        $path = ltrim($path, '/');
        return $base . $path;
    }
}

class OptimizersDB
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $dataDir = __DIR__ . '/../data';
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0755, true);
            }

            $dbPath = $dataDir . '/optimizers.sqlite';
            $isNewDb = !file_exists($dbPath);

            self::$pdo = new PDO('sqlite:' . $dbPath);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Always run schema init (handles both new and existing DBs)
            self::initializeDatabase();
        }

        return self::$pdo;
    }

    private static function runAlterIfNotExists(string $sql): void
    {
        try {
            self::$pdo->exec($sql);
        } catch (Throwable $e) {
            // Column likely already exists — safe to ignore
        }
    }

    private static function initializeDatabase(): void
    {
        $db = self::$pdo;

        // Admin Users Table
        $db->exec("CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'Super Admin',
            status TEXT NOT NULL DEFAULT 'Active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_login DATETIME NULL
        )");

        // Leads & Consultation Table
        $db->exec("CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NULL,
            budget TEXT NULL,
            service TEXT NULL,
            message TEXT NOT NULL,
            source TEXT NOT NULL DEFAULT 'consultation',
            status TEXT NOT NULL DEFAULT 'New',
            admin_notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Services & Sub-services Table
        $db->exec("CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            category TEXT NOT NULL,
            description TEXT NOT NULL,
            icon TEXT NULL,
            sub_services TEXT NULL,
            is_active INTEGER DEFAULT 1,
            display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Extend services table with new columns (safe ALTER for existing DBs)
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN short_description TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN full_description TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN featured_image TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN features TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN process_steps TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN benefits TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN meta_title TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN meta_description TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE services ADD COLUMN og_image TEXT NULL");

        // Footprint & Stats Table
        $db->exec("CREATE TABLE IF NOT EXISTS stats_footprint (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            stat_key TEXT UNIQUE NOT NULL,
            label TEXT NOT NULL,
            value TEXT NOT NULL,
            icon TEXT NULL,
            display_order INTEGER DEFAULT 0
        )");

        // Map Hubs Table
        $db->exec("CREATE TABLE IF NOT EXISTS map_hubs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            type TEXT NOT NULL DEFAULT 'Primary Client Hub',
            lat REAL NOT NULL,
            lng REAL NOT NULL,
            capacity TEXT NULL,
            status TEXT NOT NULL DEFAULT 'Active'
        )");

        // Case Studies Table
        $db->exec("CREATE TABLE IF NOT EXISTS case_studies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            category TEXT NOT NULL,
            short_description TEXT NOT NULL,
            full_description TEXT NULL,
            featured_image TEXT NULL,
            gallery_images TEXT NULL,
            author TEXT NULL,
            client_name TEXT NULL,
            industry TEXT NULL,
            location TEXT NULL,
            project_date TEXT NULL,
            challenge TEXT NULL,
            objectives TEXT NULL,
            approach TEXT NULL,
            solution TEXT NULL,
            services TEXT NULL,
            technologies TEXT NULL,
            results TEXT NULL,
            benefits TEXT NULL,
            tags TEXT NULL,
            statistics TEXT NULL,
            call_to_action_title TEXT NULL,
            call_to_action_description TEXT NULL,
            status TEXT NOT NULL DEFAULT 'published',
            display_order INTEGER DEFAULT 0,
            published_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // =====================================
        // NEW TABLES FOR CMS UPGRADE
        // =====================================

        // Navigation / Menu Items
        $db->exec("CREATE TABLE IF NOT EXISTS navigation_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT NOT NULL,
            url TEXT NOT NULL DEFAULT '#',
            parent_id INTEGER NULL DEFAULT NULL,
            display_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            open_in_new_tab INTEGER DEFAULT 0,
            css_class TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Tags library
        $db->exec("CREATE TABLE IF NOT EXISTS tags (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Technologies library
        $db->exec("CREATE TABLE IF NOT EXISTS technologies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            icon TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Case study custom content sections
        $db->exec("CREATE TABLE IF NOT EXISTS case_study_sections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            case_study_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            content TEXT NULL,
            image TEXT NULL,
            display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Case study statistics (relational rows)
        $db->exec("CREATE TABLE IF NOT EXISTS case_study_statistics (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            case_study_id INTEGER NOT NULL,
            metric_value TEXT NOT NULL,
            metric_label TEXT NOT NULL,
            metric_description TEXT NULL,
            display_order INTEGER DEFAULT 0
        )");

        // Pivot: case study <-> tags
        $db->exec("CREATE TABLE IF NOT EXISTS case_study_tags (
            case_study_id INTEGER NOT NULL,
            tag_id INTEGER NOT NULL,
            PRIMARY KEY (case_study_id, tag_id)
        )");

        // Pivot: case study <-> technologies
        $db->exec("CREATE TABLE IF NOT EXISTS case_study_technologies (
            case_study_id INTEGER NOT NULL,
            technology_id INTEGER NOT NULL,
            PRIMARY KEY (case_study_id, technology_id)
        )");

        // Case study custom tables
        $db->exec("CREATE TABLE IF NOT EXISTS case_study_tables (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            case_study_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            headers TEXT NOT NULL,
            rows TEXT NOT NULL,
            display_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Safely alter tables if missing columns
        self::runAlterIfNotExists("ALTER TABLE navigation_items ADD COLUMN css_class TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE case_study_sections ADD COLUMN image TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE case_studies ADD COLUMN card_title TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE case_studies ADD COLUMN detail_description TEXT NULL");
        self::runAlterIfNotExists("ALTER TABLE case_studies ADD COLUMN tables TEXT NULL");

        // =====================================
        // SEEDING
        // =====================================

        // Seed default admin user
        $userCount = (int)$db->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($userCount === 0) {
            $defaultPasswordHash = password_hash('admin123', PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO admin_users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['Super Admin', 'admin@optimizers.ae', $defaultPasswordHash, 'Super Admin', 'Active']);
        }

        // Seed default services
        $serviceCount = (int)$db->query("SELECT COUNT(*) FROM services")->fetchColumn();
        if ($serviceCount === 0) {
            $defaultServices = [
                ['title' => 'Search Engine Optimization (SEO)', 'slug' => 'seo', 'category' => 'SEO', 'description' => 'Rank on Page 1 of Google for high-intent UAE keywords that attract real buyers. Our SEO includes technical audits, on-page optimization, local SEO, and e-commerce SEO.', 'icon' => 'assets/icons/brand/google.svg', 'sub_services' => json_encode(['Technical SEO', 'On-Page SEO', 'Local SEO', 'E-Commerce SEO', 'AI / AEO / GEO']), 'is_active' => 1, 'display_order' => 1],
                ['title' => 'Performance Marketing & Google Ads', 'slug' => 'performance-marketing', 'category' => 'Marketing', 'description' => 'Run high-performing Google Ads and Meta campaigns designed for the UAE market. We optimize every dirham of your ad budget to maximize leads and return on investment.', 'icon' => 'assets/icons/brand/google.svg', 'sub_services' => json_encode(['Google Ads', 'Meta Ads', 'Lead Campaigns', 'Conversion Rate Optimization']), 'is_active' => 1, 'display_order' => 2],
                ['title' => 'Social Media Marketing', 'slug' => 'social-media-marketing', 'category' => 'Social Media', 'description' => 'Build a loyal, engaged audience across Instagram, TikTok, Facebook, LinkedIn, Snapchat, and YouTube. Our strategies are tailored to UAE audience behavior.', 'icon' => 'assets/icons/brand/instagram.svg', 'sub_services' => json_encode(['Facebook', 'Instagram', 'TikTok', 'LinkedIn', 'YouTube', 'Snapchat']), 'is_active' => 1, 'display_order' => 3],
                ['title' => 'WhatsApp Marketing', 'slug' => 'whatsapp-marketing', 'category' => 'WhatsApp', 'description' => 'Reach customers directly with powerful WhatsApp marketing built for the UAE market. Broadcast campaigns, automated follow-ups, and lead nurturing workflows.', 'icon' => 'assets/icons/brand/whatsapp.svg', 'sub_services' => json_encode(['Broadcast Campaigns', 'Chatbots', 'Lead Nurturing', 'Automation']), 'is_active' => 1, 'display_order' => 4],
                ['title' => 'Web Development', 'slug' => 'web-development', 'category' => 'Web Dev', 'description' => 'Fast, SEO-optimized WordPress, Shopify, and custom websites designed to convert UAE visitors into paying customers. Core Web Vitals optimized.', 'icon' => 'assets/icons/brand/html5.svg', 'sub_services' => json_encode(['WordPress', 'Shopify', 'Custom Web Development', 'UI/UX Redesign']), 'is_active' => 1, 'display_order' => 5],
                ['title' => 'Branding & Design', 'slug' => 'branding-design', 'category' => 'Branding', 'description' => 'Create a memorable brand with professional design, content creation, reels, and video editing. Produce scroll-stopping visuals for UAE audiences.', 'icon' => 'assets/icons/brand/adobe-illustrator.svg', 'sub_services' => json_encode(['Logo & Brand System', 'Reels & Video Editing', 'Graphic Design', 'Business Branding']), 'is_active' => 1, 'display_order' => 6]
            ];
            $stmt = $db->prepare("INSERT INTO services (title, slug, category, description, icon, sub_services, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultServices as $srv) {
                $stmt->execute([$srv['title'], $srv['slug'], $srv['category'], $srv['description'], $srv['icon'], $srv['sub_services'], $srv['is_active'], $srv['display_order']]);
            }
        }

        // Seed default stats footprint
        $statsCount = (int)$db->query("SELECT COUNT(*) FROM stats_footprint")->fetchColumn();
        if ($statsCount === 0) {
            $defaultStats = [
                ['stat_key' => 'brands_grown', 'label' => 'Brands Grown Across UAE', 'value' => '200+', 'icon' => 'target', 'display_order' => 1],
                ['stat_key' => 'projects_delivered', 'label' => 'Projects Successfully Delivered', 'value' => '300+', 'icon' => 'chart', 'display_order' => 2],
                ['stat_key' => 'emirates_covered', 'label' => 'UAE Emirates Covered', 'value' => '7', 'icon' => 'map', 'display_order' => 3],
                ['stat_key' => 'years_expertise', 'label' => 'Years Combined Expertise', 'value' => '14+', 'icon' => 'clock', 'display_order' => 4],
                ['stat_key' => 'client_network', 'label' => 'Client Network', 'value' => '200+', 'icon' => 'users', 'display_order' => 5]
            ];
            $stmt = $db->prepare("INSERT INTO stats_footprint (stat_key, label, value, icon, display_order) VALUES (?, ?, ?, ?, ?)");
            foreach ($defaultStats as $st) {
                $stmt->execute([$st['stat_key'], $st['label'], $st['value'], $st['icon'], $st['display_order']]);
            }
        }

        // Seed default map hubs
        $hubsCount = (int)$db->query("SELECT COUNT(*) FROM map_hubs")->fetchColumn();
        if ($hubsCount === 0) {
            $defaultHubs = [
                ['name' => 'Dubai (JVC)', 'type' => 'Primary Client Hub', 'lat' => 25.2048, 'lng' => 55.2708, 'capacity' => '200+ Clients', 'status' => 'Active'],
                ['name' => 'Abu Dhabi', 'type' => 'Regional Office Hub', 'lat' => 24.4539, 'lng' => 54.3773, 'capacity' => '50+ Clients', 'status' => 'Active'],
                ['name' => 'Sharjah', 'type' => 'Commercial Partner Hub', 'lat' => 25.3463, 'lng' => 55.4209, 'capacity' => '30+ Clients', 'status' => 'Active']
            ];
            $stmt = $db->prepare("INSERT INTO map_hubs (name, type, lat, lng, capacity, status) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($defaultHubs as $hb) {
                $stmt->execute([$hb['name'], $hb['type'], $hb['lat'], $hb['lng'], $hb['capacity'], $hb['status']]);
            }
        }

        // Seed default case studies
        $caseCount = (int)$db->query("SELECT COUNT(*) FROM case_studies")->fetchColumn();
        if ($caseCount === 0) {
            $defaultCases = [
                ['title' => '45% Reduction in Cost Per Lead for Dubai Luxury Real Estate Agency', 'slug' => 'dubai-luxury-real-estate-lead-generation', 'category' => 'Performance Marketing', 'short_description' => 'How Optimizers restructured Google Ads search & Meta retargeting campaigns to generate 145+ qualified HNW buyers monthly while reducing Cost Per Lead from AED 95 to AED 52.', 'full_description' => 'A luxury real estate brokerage operating out of Business Bay Dubai required a high-converting digital acquisition funnel targeting high-net-worth investors across the GCC and international markets.', 'featured_image' => 'assets/images/service-pages/google-ads-hero.svg', 'gallery_images' => json_encode(['assets/images/service-pages/social-media-ads-hero.svg', 'assets/images/service-pages/google-ads-hero.svg']), 'author' => 'Optimizers Strategy Team', 'client_name' => 'Damac Hills Luxury Villas Portfolio', 'industry' => 'Real Estate & Luxury Property', 'location' => 'Business Bay, Dubai, UAE', 'project_date' => 'Q1 2026', 'challenge' => 'The client was struggling with inflated ad costs on Google Ads and Meta platforms, resulting in an expensive AED 95 per lead with a high bounce rate from non-qualifying prospects looking for low-budget rentals.', 'objectives' => 'Lower Cost Per Lead below AED 60, increase monthly qualified lead volume to over 120 leads, and establish a high-intent audience funnel tailored for UAE high-net-worth investors.', 'approach' => 'We overhauled campaign structures into granular high-intent exact-match keyword clusters, implemented conversion-focused Arabic & English landing pages, and utilized custom Meta lookalike audiences built from verified property buyers in the UAE.', 'solution' => 'Deployed multi-stage Google Search Ads targeted specifically at high-income zones (Palm Jumeirah, Downtown Dubai, Emirates Hills) combined with interactive WhatsApp lead bots for instantaneous lead response.', 'services' => json_encode(['Google Ads', 'Meta Ads', 'Lead Generation', 'Conversion Rate Optimization']), 'technologies' => json_encode(['Google Ads API', 'Meta Pixel', 'GA4 Analytics', 'WhatsApp API']), 'results' => 'Generated over 145 high-intent buyer inquiries per month, slashed CPL by 45.2%, and delivered a 4.6x Return On Ad Spend (ROAS) across 6 consecutive months.', 'benefits' => json_encode(['Slashed lead acquisition cost', 'Increased qualified buyer pipeline', 'Automated instant lead response', 'Full attribution reporting']), 'tags' => json_encode(['Real Estate', 'Google Ads', 'Meta Ads', 'Dubai', 'Lead Generation']), 'statistics' => json_encode([['label' => 'Cost Per Lead', 'before' => 'AED 95', 'after' => 'AED 52', 'note' => '45.2% Cost Reduction'], ['label' => 'Monthly Qualified Leads', 'before' => '60', 'after' => '145', 'note' => '+141% Lead Volume'], ['label' => 'Return On Ad Spend', 'before' => '2.1x', 'after' => '4.6x', 'note' => '+119% ROAS Boost']]), 'call_to_action_title' => 'Ready to Scale Your Lead Generation in Dubai?', 'call_to_action_description' => 'Get a free audit of your current ad campaigns and learn how Optimizers can lower your cost per lead.', 'status' => 'published', 'display_order' => 1],
                ['title' => 'E-Commerce SEO Scaling: 240% Organic Revenue Growth for UAE Fashion Retailer', 'slug' => 'uae-ecommerce-seo-organic-growth', 'category' => 'SEO', 'short_description' => 'Comprehensive technical SEO overhaul, semantic content cluster strategy, and local e-commerce optimization driving Page 1 rankings for top fashion keywords across the GCC.', 'full_description' => 'Emirates Style Co. partnered with Optimizers to transform their organic search visibility and capture high-intent buyers searching for luxury fashion apparel in the UAE and Saudi Arabia.', 'featured_image' => 'assets/images/service-pages/optimizers-seo-search-ranking.svg', 'gallery_images' => json_encode(['assets/images/service-pages/optimizers-seo-on-page-score.svg', 'assets/images/service-pages/optimizers-site-health-scan.svg']), 'author' => 'Optimizers SEO Division', 'client_name' => 'Emirates Style Co.', 'industry' => 'E-Commerce & Fashion', 'location' => 'Dubai & Abu Dhabi, UAE', 'project_date' => 'Q4 2025', 'challenge' => 'Client had a large product catalog with severe duplicate content issues, slow mobile page loads, and zero ranking for competitive terms like designer luxury abayas Dubai and mens linen suits UAE.', 'objectives' => 'Achieve top 3 organic rankings for 50+ high-value transactional keywords, improve mobile PageSpeed score above 90, and double organic traffic revenue.', 'approach' => 'Conducted a complete technical SEO audit to fix crawl errors, canonical tags, and mobile speed bottleneck; created targeted collection page content hierarchies and high-authority Arabic/English backlink campaigns.', 'solution' => 'Implemented custom JSON-LD schema markup for product catalogs, optimized Core Web Vitals to sub-1.2s LCP, and produced structured buyer guides targeting UAE seasonal fashion trends.', 'services' => json_encode(['Technical SEO', 'On-Page SEO', 'E-Commerce SEO', 'Semantic Content Writing']), 'technologies' => json_encode(['Shopify Plus', 'Google Search Console', 'Screaming Frog', 'Ahrefs']), 'results' => 'Achieved #1 positions for 38 competitive keywords, boosted organic search revenue by 240%, and increased organic conversion rate from 1.4% to 3.2%.', 'benefits' => json_encode(['Dominated organic search in UAE', 'Boosted organic conversion rate', 'Sub-1.2 second load times', 'Sustainable free traffic funnel']), 'tags' => json_encode(['SEO', 'E-Commerce', 'Shopify', 'Fashion', 'Organic Growth']), 'statistics' => json_encode([['label' => 'Organic Revenue', 'before' => 'AED 42K', 'after' => 'AED 143K', 'note' => '+240% Monthly Growth'], ['label' => 'Page 1 Google Keywords', 'before' => '7', 'after' => '64', 'note' => '+814% Search Footprint'], ['label' => 'Organic Conversion Rate', 'before' => '1.4%', 'after' => '3.2%', 'note' => '+128% CRO Boost']]), 'call_to_action_title' => 'Dominate Google Rankings for Your E-Commerce Store', 'call_to_action_description' => 'Book an exclusive technical SEO consultation and discover untapped keyword opportunities.', 'status' => 'published', 'display_order' => 2],
                ['title' => 'Full-Funnel Social Media & Reels Campaign for Dubai Luxury Clinic', 'slug' => 'dubai-aesthetic-clinic-social-media', 'category' => 'Social Media & Branding', 'short_description' => 'Scroll-stopping video reels, localized influencer collaborations, and Instagram strategy resulting in 3.5M+ video views and 400+ appointment bookings.', 'full_description' => 'Aesthetic Atelier Clinic in Jumeirah Dubai teamed up with Optimizers to rebrand their social media image, launch original short-form video content, and drive direct consultations.', 'featured_image' => 'assets/images/service-pages/video-editing-preview.svg', 'gallery_images' => json_encode(['assets/images/service-pages/brand-design-preview.svg', 'assets/images/service-pages/content-studio-card.svg']), 'author' => 'Optimizers Creative Team', 'client_name' => 'Aesthetic Atelier Clinic', 'industry' => 'Healthcare & Aesthetics', 'location' => 'Jumeirah, Dubai, UAE', 'project_date' => 'Q1 2026', 'challenge' => 'The clinic was relying solely on word of mouth with minimal social media footprint and high reliance on expensive third-party lead brokers.', 'objectives' => 'Establish brand authority on Instagram and TikTok, produce high-engagement short-form video reels, and drive direct WhatsApp appointment bookings.', 'approach' => 'Designed an elegant visual brand identity system, scripted and shot cinematic patient journey reels, and launched location-targeted Instagram video ads across Dubai Marina, Jumeirah, and Downtown.', 'solution' => 'Created a weekly 4K video reel production workflow highlighting dermatologist expertise, doctor interviews, and treatment walkthroughs with bilingual Arabic/English subtitling.', 'services' => json_encode(['Social Media Marketing', 'Content Creation & Reels', 'Video Editing', 'Instagram Ads']), 'technologies' => json_encode(['Premiere Pro', 'After Effects', 'Meta Business Suite', 'WhatsApp Business API']), 'results' => 'Grew Instagram follower base from 1.2k to 28.4k organic followers, generated 3.5M total video views, and secured 410 new patient consultation bookings in 90 days.', 'benefits' => json_encode(['Direct WhatsApp booking pipeline', 'Cinematic brand positioning', '3.5M+ targeted UAE views', 'Reduced dependence on brokers']), 'tags' => json_encode(['Social Media', 'Reels', 'Video Editing', 'Instagram', 'Healthcare']), 'statistics' => json_encode([['label' => 'Video Impressions', 'before' => '45K', 'after' => '3.5M', 'note' => '+7677% Reach Boost'], ['label' => 'Monthly Consultation Bookings', 'before' => '28', 'after' => '140', 'note' => '+400% Patient Inquiries'], ['label' => 'Instagram Audience Growth', 'before' => '1.2K', 'after' => '28.4K', 'note' => '+2266% Brand Followers']]), 'call_to_action_title' => "Elevate Your Brand's Social Media Presence in the UAE", 'call_to_action_description' => 'Let our creative team craft high-converting video reels and social media strategies for your business.', 'status' => 'published', 'display_order' => 3]
            ];
            $stmt = $db->prepare("INSERT INTO case_studies (title, slug, category, short_description, full_description, featured_image, gallery_images, author, client_name, industry, location, project_date, challenge, objectives, approach, solution, services, technologies, results, benefits, tags, statistics, call_to_action_title, call_to_action_description, status, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultCases as $cs) {
                $stmt->execute([$cs['title'], $cs['slug'], $cs['category'], $cs['short_description'], $cs['full_description'], $cs['featured_image'], $cs['gallery_images'], $cs['author'], $cs['client_name'], $cs['industry'], $cs['location'], $cs['project_date'], $cs['challenge'], $cs['objectives'], $cs['approach'], $cs['solution'], $cs['services'], $cs['technologies'], $cs['results'], $cs['benefits'], $cs['tags'], $cs['statistics'], $cs['call_to_action_title'], $cs['call_to_action_description'], $cs['status'], $cs['display_order']]);
            }
        }

        // Seed default leads
        $leadsCount = (int)$db->query("SELECT COUNT(*) FROM leads")->fetchColumn();
        if ($leadsCount === 0) {
            $sampleLeads = [
                ['name' => 'Tariq Al-Mansoori', 'email' => 'tariq@almansoorigroup.ae', 'phone' => '+971 50 123 4567', 'budget' => '10k-25k', 'service' => 'Search Engine Optimization (SEO)', 'message' => 'Looking for complete SEO package for our luxury real estate agency in Business Bay Dubai.', 'source' => 'consultation', 'status' => 'New', 'admin_notes' => 'High priority lead. Client wants a quick call tomorrow at 10 AM.', 'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))],
                ['name' => 'Sarah Jenkins', 'email' => 'sarah@dubaispa.co', 'phone' => '+971 52 987 6543', 'budget' => '25k-50k', 'service' => 'Performance Marketing & Google Ads', 'message' => 'We need paid acquisition for our chain of wellness centers in Dubai Marina & Palm Jumeirah.', 'source' => 'consultation', 'status' => 'In Progress', 'admin_notes' => 'Proposal sent on AED 35k monthly retainer. Waiting for CEO approval.', 'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['name' => 'Hamdan Commercial LLC', 'email' => 'contact@hamdancommercial.ae', 'phone' => '+971 4 333 8899', 'budget' => '5k-10k', 'service' => 'Web Development', 'message' => 'Need custom Shopify store redesign with multilingual Arabic & English support.', 'source' => 'consultation', 'status' => 'Contacted', 'admin_notes' => 'Initial phone call done. Scheduled technical requirement gathering.', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))]
            ];
            $stmt = $db->prepare("INSERT INTO leads (name, email, phone, budget, service, message, source, status, admin_notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($sampleLeads as $ld) {
                $stmt->execute([$ld['name'], $ld['email'], $ld['phone'], $ld['budget'], $ld['service'], $ld['message'], $ld['source'], $ld['status'], $ld['admin_notes'], $ld['created_at']]);
            }
        }

        // Seed tags library
        $tagsCount = (int)$db->query("SELECT COUNT(*) FROM tags")->fetchColumn();
        if ($tagsCount === 0) {
            $defaultTags = [
                'Google Ads', 'SEO', 'Meta Ads', 'Lead Generation', 'PPC', 'Conversion Optimization',
                'Social Media', 'Branding', 'Web Development', 'App Development', 'Performance Marketing',
                'E-Commerce', 'Real Estate', 'Healthcare', 'Fashion', 'Dubai', 'Abu Dhabi', 'UAE', 'GCC'
            ];
            $stmt = $db->prepare("INSERT OR IGNORE INTO tags (name, slug) VALUES (?, ?)");
            foreach ($defaultTags as $tagName) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $tagName));
                $stmt->execute([$tagName, $slug]);
            }
        }

        // Seed technologies library
        $techCount = (int)$db->query("SELECT COUNT(*) FROM technologies")->fetchColumn();
        if ($techCount === 0) {
            $defaultTechs = [
                'Google Ads', 'Google Analytics', 'Google Tag Manager', 'Meta Pixel', 'Google Search Console',
                'Screaming Frog', 'Ahrefs', 'WordPress', 'Shopify', 'Laravel', 'React', 'Firebase',
                'WhatsApp Business API', 'Premiere Pro', 'After Effects', 'Meta Business Suite',
                'GA4 Analytics', 'Shopify Plus'
            ];
            $stmt = $db->prepare("INSERT OR IGNORE INTO technologies (name, slug) VALUES (?, ?)");
            foreach ($defaultTechs as $techName) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $techName));
                $stmt->execute([$techName, $slug]);
            }
        }

        // Seed navigation items (exact current menu structure)
        $navCount = (int)$db->query("SELECT COUNT(*) FROM navigation_items")->fetchColumn();
        if ($navCount <= 5) {
            $db->exec("DELETE FROM navigation_items");
            // Top-level items
            $navStmt = $db->prepare("INSERT INTO navigation_items (label, url, parent_id, display_order, is_active, css_class) VALUES (?, ?, ?, ?, ?, ?)");

            $navStmt->execute(['Home', 'index.php#home', null, 1, 1, '']);
            $navStmt->execute(['About', 'about-us.php', null, 2, 1, '']);
            $navStmt->execute(['Services', '#', null, 3, 1, 'nav-dropdown-trigger']);
            $servicesId = (int)$db->lastInsertId();
            $navStmt->execute(['Case Studies', 'case-studies.php', null, 4, 1, '']);
            $navStmt->execute(['Contact', 'contact-us.php', null, 5, 1, '']);

            // Services -> Groups
            $navStmt->execute(['Web Development', '#', $servicesId, 1, 1, 'nav-group']);
            $webDevId = (int)$db->lastInsertId();
            $navStmt->execute(['Search Engine Optimization (SEO)', '#', $servicesId, 2, 1, 'nav-group']);
            $seoId = (int)$db->lastInsertId();
            $navStmt->execute(['Social Media Marketing', '#', $servicesId, 3, 1, 'nav-group']);
            $socialId = (int)$db->lastInsertId();
            $navStmt->execute(['Performance Marketing Service', '#', $servicesId, 4, 1, 'nav-group']);
            $perfId = (int)$db->lastInsertId();
            $navStmt->execute(['Branding & Design Services', '#', $servicesId, 5, 1, 'nav-group']);
            $brandId = (int)$db->lastInsertId();

            // Web Development children
            $webChildren = [['WordPress Development', 'wordpress-development.php'], ['Shopify Development', 'shopify-development.php'], ['Custom Web Development', 'custom-web-development.php']];
            foreach ($webChildren as $i => $c) { $navStmt->execute([$c[0], $c[1], $webDevId, $i + 1, 1, '']); }

            // SEO children
            $seoChildren = [['On Page SEO', 'on-page-seo.php'], ['OFF-PAGE SEO', 'off-page-seo.php'], ['International SEO Services', 'international-seo.php'], ['Local SEO Service', 'local-seo-service.php'], ['E-Commerce SEO Services', 'e-commerce-seo-services.php'], ['Semantic Content Writing Services', 'semantic-content-writing-services.php'], ['Keyword Research', 'keyword-research.php'], ['AI SEO Services (AEO / GEO / LLM SEO)', 'ai-seo-services-aeo-geo-llm-seo.php'], ['Technical SEO', 'technical-seo.php']];
            foreach ($seoChildren as $i => $c) { $navStmt->execute([$c[0], $c[1], $seoId, $i + 1, 1, '']); }

            // Social Media children
            $socialChildren = [['Facebook Marketing', 'facebook-marketing.php'], ['Instagram Marketing', 'instagram-marketing.php'], ['Snapchat Marketing', 'snapchat-marketing.php'], ['TikTok Marketing', 'tiktok-marketing.php'], ['YouTube Marketing', 'youtube-marketing.php'], ['LinkedIn Marketing', 'linkedin-marketing.php']];
            foreach ($socialChildren as $i => $c) { $navStmt->execute([$c[0], $c[1], $socialId, $i + 1, 1, '']); }

            // Performance children
            $perfChildren = [['Social Media Ads', 'social-media-ads.php'], ['WhatsApp Marketing', 'whatsapp-marketing.php'], ['Email Marketing', 'email-marketing.php'], ['Google Ads', 'google-ads.php']];
            foreach ($perfChildren as $i => $c) { $navStmt->execute([$c[0], $c[1], $perfId, $i + 1, 1, '']); }

            // Branding children
            $brandChildren = [['Business Branding & Designing', 'business-branding-designing.php'], ['Graphic Designing', 'graphic-designing.php'], ['Content Creation & Reels', 'content-creation-reels.php'], ['Video Editing', 'video-editing.php']];
            foreach ($brandChildren as $i => $c) { $navStmt->execute([$c[0], $c[1], $brandId, $i + 1, 1, '']); }
        }
    }
}
