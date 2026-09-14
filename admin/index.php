<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$currentUser = requireAdminAuth();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimizers UAE - Admin Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brandDark: '#0B0C10',
                        brandSurface: '#121212',
                        brandRed: '#E50914',
                        brandRedHover: '#FF2A2A',
                        brandCyan: '#00E5FF',
                        brandCyanDark: '#00B8D4',
                    }
                }
            }
        }
    </script>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Dark Mode Glassmorphism Theme (Match Site Theme) */
        .dark body {
            background-color: #0B0C10;
            color: #F8FAFC;
        }

        .dark .glass-card {
            background: rgba(18, 18, 18, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .dark .glass-nav {
            background: rgba(11, 12, 16, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dark .glass-header {
            background: rgba(11, 12, 16, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dark .glass-input {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }
        .dark .glass-input:focus {
            border-color: #00E5FF;
            box-shadow: 0 0 10px rgba(0, 229, 255, 0.2);
        }

        /* Light Mode Clean & Minimal Enterprise UI Theme */
        html:not(.dark) body {
            background-color: #F8FAFC;
            color: #0F172A;
        }

        html:not(.dark) .glass-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        html:not(.dark) .glass-nav {
            background: #FFFFFF;
            border-right: 1px solid #E2E8F0;
        }

        html:not(.dark) .glass-header {
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
        }

        html:not(.dark) .glass-input {
            background: #F1F5F9;
            border: 1px solid #CBD5E1;
            color: #0F172A;
        }
        html:not(.dark) .glass-input:focus {
            border-color: #E50914;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.3);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.6);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row overflow-x-hidden">

    <!-- Sidebar Navigation -->
  <aside id="sidebar" class="w-full md:w-64 glass-nav flex-shrink-0 z-30 transition-all duration-300 flex flex-col justify-between md:min-h-screen">
        <div>
            <!-- Sidebar Header -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-slate-800/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brandRed to-brandCyan p-0.5 shadow-lg">
                        <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                            <img src="<?= optimizers_url('assets/images/optimizers-uae-animated-logo.svg') ?>" alt="Optimizers" class="w-6 h-6 object-contain" onerror="this.onerror=null; this.src='<?= optimizers_url('assets/icons/brand/google.svg') ?>';">
                        </div>
                    </div>
                    <div>
                        <div class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white">OPTIMIZERS UAE</div>
                        <div class="text-[10px] font-semibold text-brandCyan uppercase tracking-wider">Admin Portal</div>
                    </div>
                </div>
                <button id="sidebarCloseBtn" class="md:hidden text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5">
                <button data-tab="overview" class="nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-brandRed dark:text-brandCyan bg-brandRed/10 dark:bg-brandCyan/10 border border-brandRed/20 dark:border-brandCyan/20">
                    <span>Overview</span>
                    <i class="fa-solid fa-chart-pie text-base w-5 text-center ml-auto"></i>
                </button>

                <button data-tab="leads" class="nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-500/10 hover:text-slate-900 dark:hover:text-white">
                    <div class="flex items-center gap-2">
                        <span>Consultations & Leads</span>
                        <span id="newLeadsBadge" class="bg-brandRed text-white text-[10px] font-bold px-2 py-0.5 rounded-full hidden">0</span>
                    </div>
                    <i class="fa-solid fa-user-gear text-base w-5 text-center ml-auto"></i>
                </button>

                <button data-tab="services" class="nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-500/10 hover:text-slate-900 dark:hover:text-white">
                    <span>Services CMS</span>
                    <i class="fa-solid fa-layer-group text-base w-5 text-center ml-auto"></i>
                </button>

                <button data-tab="case-studies" class="nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-500/10 hover:text-slate-900 dark:hover:text-white">
                    <span>Case Studies CMS</span>
                    <i class="fa-solid fa-briefcase text-base w-5 text-center ml-auto"></i>
                </button>

                <button data-tab="navigation" class="nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-500/10 hover:text-slate-900 dark:hover:text-white">
                    <span>Header / Menu CMS</span>
                    <i class="fa-solid fa-compass text-base w-5 text-center ml-auto"></i>
                </button>

                <button data-tab="footprint" class="nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-500/10 hover:text-slate-900 dark:hover:text-white">
                    <span>UAE Footprint</span>
                    <i class="fa-solid fa-map-location-dot text-base w-5 text-center ml-auto"></i>
                </button>

                <button data-tab="team" class="nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-500/10 hover:text-slate-900 dark:hover:text-white">
                    <span>Team & Settings</span>
                    <i class="fa-solid fa-shield-halved text-base w-5 text-center ml-auto"></i>
                </button>
            </nav>
        </div>

        <!-- Sidebar Footer Admin Info -->
        <div class="p-4 border-t border-slate-800/40">
            <div class="glass-card rounded-2xl p-3 flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-brandRed to-brandCyan flex items-center justify-center font-bold text-white text-xs flex-shrink-0">
                        <?= strtoupper(substr($currentUser['name'], 0, 2)) ?>
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-slate-900 dark:text-white truncate"><?= htmlspecialchars($currentUser['name']) ?></div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium capitalize"><?= htmlspecialchars($currentUser['role']) ?></div>
                    </div>
                </div>
                <a href="logout.php" title="Sign Out" class="text-slate-400 hover:text-brandRed p-1.5 rounded-lg hover:bg-brandRed/10 transition-colors">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Bar -->
        <header class="h-20 glass-header px-6 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <button id="sidebarToggleBtn" class="md:hidden text-slate-400 hover:text-white">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div>
                    <h1 id="pageTitle" class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Overview Dashboard</h1>
                    <p id="pageSubTitle" class="text-xs text-slate-500 dark:text-slate-400 font-medium">Real-time consultation inquiries & activity metrics</p>
                </div>
            </div>

            <!-- Top Controls -->
            <div class="flex items-center gap-3">
                <!-- Theme Toggle Button -->
                <button id="themeToggleBtn" class="p-2.5 rounded-xl glass-card text-slate-600 dark:text-slate-300 hover:text-brandCyan transition-all flex items-center gap-2 text-xs font-semibold" title="Toggle Theme">
                    <i class="fa-solid fa-moon text-sm dark:hidden"></i>
                    <i class="fa-solid fa-sun text-sm hidden dark:inline text-amber-400"></i>
                    <span class="hidden sm:inline" id="themeLabel">Dark Mode</span>
                </button>

                <!-- Quick Notification Indicator -->
                <div class="relative">
                    <button id="notifBtn" data-tab="leads" class="p-2.5 rounded-xl glass-card text-slate-600 dark:text-slate-300 hover:text-brandRed transition-all relative">
                        <i class="fa-solid fa-bell text-sm"></i>
                        <span id="notifDot" class="absolute -top-1 -right-1 w-3 h-3 bg-brandRed rounded-full border-2 border-slate-900 hidden"></span>
                    </button>
                </div>

                <!-- Admin Avatar Dropdown Link -->
                <button data-tab="team" class="flex items-center gap-2 p-1.5 pr-3 rounded-xl glass-card hover:border-brandCyan/40 transition-all">
                    <div class="w-7 h-7 rounded-lg bg-brandRed text-white text-xs font-bold flex items-center justify-center">
                        <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                    </div>
                    <span class="text-xs font-semibold text-slate-900 dark:text-slate-200 hidden sm:inline"><?= htmlspecialchars(explode(' ', $currentUser['name'])[0]) ?></span>
                </button>
            </div>
        </header>

        <!-- Main Body Container -->
        <main class="p-4 sm:p-6 lg:p-8 flex-1 space-y-8">

            <!-- ================= TAB 1: OVERVIEW DASHBOARD ================= -->
            <section id="tab-overview" class="tab-content space-y-8">
                <!-- Stat Cards Grid (STRICTLY NO FINANCIAL DATA) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <!-- Total Leads Card -->
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:border-brandCyan/40 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Leads</span>
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-brandCyan flex items-center justify-center text-base font-bold">
                                <i class="fa-solid fa-users-viewfinder"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between">
                            <span id="statTotalLeads" class="text-3xl font-extrabold text-slate-900 dark:text-white">0</span>
                            <span class="text-xs font-semibold text-emerald-500 flex items-center gap-1">
                                <i class="fa-solid fa-arrow-trend-up"></i> Active
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Free Consultation & Contact form leads</p>
                    </div>

                    <!-- Active Consultations Card -->
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:border-brandRed/40 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Consultations</span>
                            <div class="w-10 h-10 rounded-xl bg-red-500/10 text-brandRed flex items-center justify-center text-base font-bold">
                                <i class="fa-solid fa-comments-dollar"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between">
                            <span id="statActiveConsultations" class="text-3xl font-extrabold text-slate-900 dark:text-white">0</span>
                            <span class="text-xs font-semibold text-amber-500 flex items-center gap-1">
                                <i class="fa-solid fa-clock"></i> In pipeline
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Leads under active follow up</p>
                    </div>

                    <!-- Total Projects Delivered Card -->
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:border-purple-500/40 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Projects Delivered</span>
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-base font-bold">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between">
                            <span id="statProjectsDelivered" class="text-3xl font-extrabold text-slate-900 dark:text-white">300+</span>
                            <span class="text-xs font-semibold text-purple-400 flex items-center gap-1">
                                <i class="fa-solid fa-shield"></i> UAE Footprint
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Verified completed UAE client projects</p>
                    </div>

                    <!-- Active Client Inquiries Card -->
                    <div class="glass-card rounded-2xl p-5 relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Inquiries</span>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-base font-bold">
                                <i class="fa-solid fa-bell-concierge"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-baseline justify-between">
                            <span id="statActiveInquiries" class="text-3xl font-extrabold text-slate-900 dark:text-white">0</span>
                            <span class="text-xs font-semibold text-brandCyan flex items-center gap-1">
                                <i class="fa-solid fa-bolt"></i> Needs Action
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Uncontacted new submission inquiries</p>
                    </div>
                </div>

                <!-- Inquiry Trends Chart -->
                <div class="glass-card rounded-3xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Inquiries & Consultation Trends</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Lead traffic and consultation request volume over time</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-brandCyan">
                                <span class="w-2.5 h-2.5 rounded-full bg-brandCyan"></span> Consultations
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-brandRed ml-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-brandRed"></span> Direct Contact
                            </span>
                        </div>
                    </div>
                    <div class="h-72 w-full">
                        <canvas id="overviewTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Recent Leads Quick Table -->
                <div class="glass-card rounded-3xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">Recent Consultation Requests</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Latest form submissions from Optimizers UAE website</p>
                        </div>
                        <button data-tab="leads" class="text-xs font-bold text-brandCyan hover:underline flex items-center gap-1">
                            <span>View All Leads</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800/40">
                                    <th class="py-3 px-4">Client Name</th>
                                    <th class="py-3 px-4">Email & Phone</th>
                                    <th class="py-3 px-4">Service / Subject</th>
                                    <th class="py-3 px-4">Budget Range</th>
                                    <th class="py-3 px-4">Date</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="recentLeadsTableBody" class="divide-y divide-slate-800/20 text-xs font-medium">
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-500">Loading recent leads...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ================= TAB 2: CONSULTATION / LEAD MANAGEMENT ================= -->
            <section id="tab-leads" class="tab-content space-y-6 hidden">
                
                <!-- Filters & Search Toolbar -->
                <div class="glass-card rounded-3xl p-6 space-y-4">
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                        <!-- Search Bar -->
                        <div class="relative flex-1">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" id="leadSearchInput" placeholder="Search by Client Name, Email, Phone, or Project keywords..."
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3">
                            <a href="api.php?action=export_leads" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all flex items-center gap-2">
                                <i class="fa-solid fa-file-csv text-emerald-400"></i>
                                <span>Export CSV</span>
                            </a>
                            <button id="resetLeadFiltersBtn" class="px-4 py-2.5 rounded-xl glass-card hover:bg-slate-500/10 text-xs font-semibold transition-all">
                                <i class="fa-solid fa-rotate-left mr-1"></i> Reset Filters
                            </button>
                        </div>
                    </div>

                    <!-- Dropdown Filter Controls -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                            <select id="filterStatus" class="w-full px-3 py-2 rounded-xl glass-input text-xs font-medium">
                                <option value="All">All Statuses</option>
                                <option value="New">New</option>
                                <option value="Contacted">Contacted</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Converted">Converted</option>
                                <option value="Archived">Archived</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Monthly Budget</label>
                            <select id="filterBudget" class="w-full px-3 py-2 rounded-xl glass-input text-xs font-medium">
                                <option value="">All Budgets</option>
                                <option value="5k-10k">AED 5k – 10k</option>
                                <option value="10k-25k">AED 10k – 25k</option>
                                <option value="25k-50k">AED 25k – 50k</option>
                                <option value="50k-plus">AED 50k+</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Requested Service</label>
                            <select id="filterService" class="w-full px-3 py-2 rounded-xl glass-input text-xs font-medium">
                                <option value="">All Services</option>
                                <option value="SEO">SEO</option>
                                <option value="Performance Marketing">Performance Marketing</option>
                                <option value="Social Media">Social Media</option>
                                <option value="WhatsApp">WhatsApp Marketing</option>
                                <option value="Web Development">Web Development</option>
                                <option value="Branding">Branding & Design</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Date Range</label>
                            <div class="flex items-center gap-2">
                                <input type="date" id="filterDateFrom" class="w-full px-2 py-2 rounded-xl glass-input text-[11px]">
                                <span class="text-slate-400 text-xs">-</span>
                                <input type="date" id="filterDateTo" class="w-full px-2 py-2 rounded-xl glass-input text-[11px]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Data Table Card -->
                <div class="glass-card rounded-3xl p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800/40">
                                    <th class="py-3 px-4">Client Name & Email</th>
                                    <th class="py-3 px-4">Phone / WhatsApp</th>
                                    <th class="py-3 px-4">Selected Budget</th>
                                    <th class="py-3 px-4">Service & Details</th>
                                    <th class="py-3 px-4">Submission Date</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="leadsTableBody" class="divide-y divide-slate-800/20 text-xs font-medium">
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-500">Loading lead records...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-slate-800/30 text-xs text-slate-400">
                        <div id="paginationInfo">Showing 0 of 0 leads</div>
                        <div class="flex items-center gap-2" id="paginationControls">
                            <!-- Pagination buttons dynamically rendered -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- ================= TAB 3: SERVICES CMS ================= -->
            <section id="tab-services" class="tab-content space-y-6 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Services & Sub-Services CMS</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Dynamically update services displayed on the main website</p>
                    </div>
                    <button id="addNewServiceBtn" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brandRed to-red-600 hover:from-brandRedHover hover:to-red-500 text-white text-xs font-bold transition-all shadow-lg shadow-red-900/30 flex items-center gap-2">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Add New Service</span>
                    </button>
                </div>

                <!-- Services Grid -->
                <div id="servicesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Service cards rendered via JS -->
                </div>
            </section>

            <!-- ================= TAB: CASE STUDIES CMS ================= -->
            <section id="tab-case-studies" class="tab-content space-y-6 hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Case Studies & Client Results CMS</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Manage dynamic case study cards, featured images, and client outcome statistics</p>
                    </div>
                    <button type="button" onclick="openCSModal(0)" id="addNewCaseStudyBtn" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brandRed to-red-600 hover:from-brandRedHover hover:to-red-500 text-white text-xs font-bold transition-all shadow-lg shadow-red-900/30 flex items-center gap-2 inline-flex">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Add New Case Study</span>
                    </button>
                </div>

                <!-- Case Studies Filter Bar -->
                <div class="glass-card rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3 flex-1 min-w-[240px]">
                        <div class="relative w-full">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" id="csSearchInput" placeholder="Search case studies by title, client, or tags..." class="w-full pl-9 pr-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <select id="csCategoryFilter" class="px-3.5 py-2.5 rounded-xl glass-input text-xs font-semibold">
                            <option value="All">All Categories</option>
                            <option value="Digital Marketing Tips for UAE Business Owners">Digital Marketing Tips for UAE Business Owners</option>
                            <option value="Real Numbers. Real Clients. Real Growth.">Real Numbers. Real Clients. Real Growth.</option>
                        </select>

                        <select id="csStatusFilter" class="px-3.5 py-2.5 rounded-xl glass-input text-xs font-semibold">
                            <option value="All">All Statuses</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                <!-- Case Studies Table -->
                <div class="glass-card rounded-3xl p-6 space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800/40">
                                    <th class="py-3 px-4">Case Study Title</th>
                                    <th class="py-3 px-4">Category</th>
                                    <th class="py-3 px-4">Client / Industry</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="caseStudiesTableBody" class="divide-y divide-slate-800/20 text-xs font-medium">
                                <tr><td colspan="5" class="py-8 text-center text-slate-500">Loading case studies...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="csPagination" class="flex items-center justify-between border-t border-slate-800/40 pt-4 text-xs"></div>
                </div>
            </section>

            <!-- ================= TAB: NAVIGATION / HEADER MENU CMS ================= -->
            <section id="tab-navigation" class="tab-content space-y-6 hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Header & Navigation Menu CMS</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Manage all header navigation links, nested service dropdowns, ordering, and active visibility</p>
                    </div>
                    <button onclick="openNavItemModal(0)" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brandRed to-red-600 hover:from-brandRedHover hover:to-red-500 text-white text-xs font-bold transition-all shadow-lg shadow-red-900/30 flex items-center gap-2">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Add Navigation Item</span>
                    </button>
                </div>

                <div class="glass-card rounded-3xl p-6 space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800/40">
                                    <th class="py-3 px-4">Menu Label / Hierarchy</th>
                                    <th class="py-3 px-4">Target URL</th>
                                    <th class="py-3 px-4">Level</th>
                                    <th class="py-3 px-4">Order</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="navTableBody" class="divide-y divide-slate-800/20 text-xs font-medium">
                                <tr><td colspan="6" class="py-8 text-center text-slate-500">Loading navigation menu items...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ================= TAB 4: STATISTICS & FOOTPRINT MANAGEMENT ================= -->
            <section id="tab-footprint" class="tab-content space-y-8 hidden">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Our UAE Footprint & Counter Stats</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Manage live achievement statistics and interactive map pins displayed on the site</p>
                </div>

                <!-- Footprint Counter Cards Form -->
                <div class="glass-card rounded-3xl p-6 space-y-6">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-hashtag text-brandCyan"></i>
                        <span>Website Achievement Counters</span>
                    </h3>

                    <div id="footprintStatsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <!-- Rendered via JS -->
                    </div>
                </div>

                <!-- Map Pins & Client Hubs Management -->
                <div class="glass-card rounded-3xl p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-brandRed"></i>
                                <span>UAE Map Client Hub Pins</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Manage interactive emirate map locations</p>
                        </div>
                        <button id="addNewMapHubBtn" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Add Map Pin</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800/40">
                                    <th class="py-3 px-4">Hub Name</th>
                                    <th class="py-3 px-4">Type</th>
                                    <th class="py-3 px-4">Coordinates (Lat / Lng)</th>
                                    <th class="py-3 px-4">Network Capacity</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="mapHubsTableBody" class="divide-y divide-slate-800/20 text-xs font-medium">
                                <tr><td colspan="6" class="py-6 text-center text-slate-500">Loading map hubs...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ================= TAB 5: TEAM & SETTINGS ================= -->
            <section id="tab-team" class="tab-content space-y-8 hidden">
                
                <!-- Admin Profile & Password Change Form -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="glass-card rounded-3xl p-6 space-y-5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-user-gear text-brandCyan"></i>
                            <span>My Profile Settings</span>
                        </h3>

                        <form id="profileForm" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Full Name</label>
                                <input type="text" id="profileName" value="<?= htmlspecialchars($currentUser['name']) ?>" required class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Corporate Email</label>
                                <input type="email" id="profileEmail" value="<?= htmlspecialchars($currentUser['email']) ?>" required class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                            </div>

                            <hr class="border-slate-800/40">

                            <div class="text-xs font-bold text-slate-900 dark:text-white">Change Security Password</div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Current Password</label>
                                <input type="password" id="profileCurrentPass" placeholder="Required only if changing password" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">New Password</label>
                                <input type="password" id="profileNewPass" placeholder="Leave blank to keep unchanged" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                            </div>

                            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-brandRed to-red-600 hover:from-brandRedHover hover:to-red-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-red-900/30 transition-all">
                                Save Profile Changes
                            </button>
                        </form>
                    </div>

                    <!-- Theme & Access Control Overview -->
                    <div class="glass-card rounded-3xl p-6 space-y-5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-palette text-amber-400"></i>
                            <span>System Theme Settings</span>
                        </h3>

                        <div class="p-4 rounded-2xl bg-slate-500/10 border border-slate-500/20 space-y-3">
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Active Visual Style</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Toggle between the default Dark Glassmorphism matching Optimizers UAE branding or clean Enterprise Light mode.</p>

                            <div class="flex items-center gap-3 pt-2">
                                <button id="setDarkBtn" class="flex-1 py-2.5 px-3 rounded-xl bg-slate-950 text-white border border-brandCyan text-xs font-bold flex items-center justify-center gap-2 shadow-lg">
                                    <i class="fa-solid fa-moon text-brandCyan"></i>
                                    <span>Dark Glassmorphism</span>
                                </button>

                                <button id="setLightBtn" class="flex-1 py-2.5 px-3 rounded-xl bg-white text-slate-900 border border-slate-300 text-xs font-bold flex items-center justify-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-sun text-amber-500"></i>
                                    <span>Clean Light Mode</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Team Users Table (Role Based Access Control) -->
                <div class="glass-card rounded-3xl p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-users-gear text-brandCyan"></i>
                                <span>Role-Based Access Control (RBAC) Users</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Super Admins manage system access permissions for Editors and Admins</p>
                        </div>
                        <?php if ($currentUser['role'] === 'Super Admin'): ?>
                        <button id="addNewAdminBtn" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-all flex items-center gap-2">
                            <i class="fa-solid fa-user-plus text-xs text-brandCyan"></i>
                            <span>Add Admin User</span>
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800/40">
                                    <th class="py-3 px-4">User Name</th>
                                    <th class="py-3 px-4">Email</th>
                                    <th class="py-3 px-4">Role Permission</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Last Active</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="teamTableBody" class="divide-y divide-slate-800/20 text-xs font-medium">
                                <tr><td colspan="6" class="py-6 text-center text-slate-500">Loading admin users...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <!-- ================= MODALS ================= -->

    <!-- LEAD DETAIL MODAL -->
    <div id="leadModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-2xl glass-card rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800/40 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brandCyan/10 text-brandCyan flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-address-card"></i>
                    </div>
                    <div>
                        <h3 id="modalLeadName" class="text-base font-extrabold text-slate-900 dark:text-white">Client Consultation Request</h3>
                        <p id="modalLeadDate" class="text-xs text-slate-500 dark:text-slate-400">Submitted date</p>
                    </div>
                </div>
                <button onclick="closeModal('leadModal')" class="text-slate-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3 rounded-xl bg-slate-500/10 border border-slate-500/15">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Corporate Email</span>
                    <a id="modalLeadEmail" href="#" class="font-semibold text-brandCyan hover:underline">email@domain.com</a>
                </div>

                <div class="p-3 rounded-xl bg-slate-500/10 border border-slate-500/15">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Phone Number & WhatsApp</span>
                    <div class="flex items-center justify-between">
                        <a id="modalLeadPhone" href="#" class="font-semibold text-slate-900 dark:text-white">+971 00 000 0000</a>
                        <a id="modalWhatsAppBtn" href="#" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] flex items-center gap-1">
                            <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                        </a>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-500/10 border border-slate-500/15">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Monthly Ad/Project Budget</span>
                    <span id="modalLeadBudget" class="font-bold text-amber-400">AED 10k - 25k</span>
                </div>

                <div class="p-3 rounded-xl bg-slate-500/10 border border-slate-500/15">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Lead Status</span>
                    <select id="modalLeadStatus" class="w-full px-2.5 py-1 rounded-lg glass-input text-xs font-semibold">
                        <option value="New">New</option>
                        <option value="Contacted">Contacted</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Converted">Converted</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Project Details / Inquiry Message</span>
                <div id="modalLeadMessage" class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-200 leading-relaxed font-sans whitespace-pre-wrap">
                    Message content...
                </div>
            </div>

            <!-- Internal Admin Notes -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-900 dark:text-white flex items-center justify-between">
                    <span>Internal Admin Follow-up Notes</span>
                    <span class="text-[10px] text-slate-400 font-normal">Visible only to Optimizers Admin Team</span>
                </label>
                <textarea id="modalAdminNotes" rows="3" placeholder="Add private client notes, meeting summaries, or follow-up details..." class="w-full p-3 rounded-xl glass-input text-xs font-medium"></textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-800/40">
                <button id="modalDeleteLeadBtn" class="px-4 py-2.5 rounded-xl bg-red-950/80 hover:bg-red-900 text-red-200 font-semibold text-xs transition-colors">
                    <i class="fa-solid fa-trash mr-1.5"></i> Delete Lead
                </button>
                <div class="flex items-center gap-3">
                    <button onclick="closeModal('leadModal')" class="px-4 py-2.5 rounded-xl glass-card text-xs font-semibold">Close</button>
                    <button id="modalSaveLeadBtn" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brandRed to-red-600 text-white text-xs font-bold shadow-lg">Save Lead Updates</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD / EDIT SERVICE MODAL -->
    <div id="serviceModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-2xl glass-card rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800/40 pb-4">
                <h3 id="serviceModalTitle" class="text-base font-extrabold text-slate-900 dark:text-white">Add New Service</h3>
                <button onclick="closeModal('serviceModal')" class="text-slate-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="serviceForm" class="space-y-4 text-xs">
                <input type="hidden" id="serviceId" value="0">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Service Title *</label>
                        <input type="text" id="serviceTitleInput" placeholder="e.g. Custom Web Development" required class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                    </div>
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">URL Slug</label>
                        <input type="text" id="serviceSlugInput" placeholder="e.g. web-development (auto if blank)" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Category</label>
                        <select id="serviceCategoryInput" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-medium">
                            <option value="SEO">SEO</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Social Media">Social Media</option>
                            <option value="WhatsApp">WhatsApp</option>
                            <option value="Web Dev">Web Dev</option>
                            <option value="Branding">Branding</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Icon Path / SVG</label>
                        <input type="text" id="serviceIconInput" value="assets/icons/brand/google.svg" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-medium">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Short Description (Card Excerpt) *</label>
                    <textarea id="serviceDescInput" rows="3" placeholder="Summary shown on website service card..." required class="w-full p-3 rounded-xl glass-input text-xs font-medium"></textarea>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Full Service Description (Detail Page)</label>
                    <textarea id="serviceFullDescInput" rows="4" placeholder="Detailed description for the dedicated service page..." class="w-full p-3 rounded-xl glass-input text-xs font-medium"></textarea>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Sub-Services / Tags (Comma Separated)</label>
                    <input type="text" id="serviceSubTagsInput" placeholder="e.g. WordPress, Shopify, Custom Development" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Key Features / Inclusions (One per line)</label>
                    <textarea id="serviceFeaturesInput" rows="3" placeholder="Technical Architecture&#10;Core Web Vitals Optimization&#10;Custom API Integration" class="w-full p-3 rounded-xl glass-input text-xs font-medium"></textarea>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Key Benefits (One per line)</label>
                    <textarea id="serviceBenefitsInput" rows="3" placeholder="High conversion rates&#10;Lightning fast load speeds&#10;Dedicated UAE support" class="w-full p-3 rounded-xl glass-input text-xs font-medium"></textarea>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Featured Image Path</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="serviceFeaturedImage" placeholder="assets/images/service-pages/google-ads-hero.svg" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs">
                        <label class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs cursor-pointer flex-shrink-0 flex items-center gap-1.5">
                            <i class="fa-solid fa-upload text-xs"></i> Upload
                            <input type="file" id="serviceImageFileInput" accept="image/*" class="hidden" onchange="uploadServiceImage(this)">
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="serviceActiveInput" checked class="rounded border-slate-700 bg-slate-900 text-brandRed focus:ring-0">
                        <span class="font-bold text-slate-900 dark:text-white">Active (Display on website)</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/40">
                    <button type="button" onclick="closeModal('serviceModal')" class="px-4 py-2.5 rounded-xl glass-card font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brandRed to-red-600 text-white font-bold shadow-lg">Save Service</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ADD / EDIT NAVIGATION ITEM MODAL -->
    <div id="navItemModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-lg glass-card rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-800/40 pb-4">
                <h3 id="navItemModalTitle" class="text-base font-extrabold text-slate-900 dark:text-white">Add Navigation Item</h3>
                <button onclick="closeModal('navItemModal')" class="text-slate-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="navItemForm" class="space-y-4 text-xs">
                <input type="hidden" id="navItemId" value="0">

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Menu Label / Title *</label>
                    <input type="text" id="navItemLabel" placeholder="e.g., WordPress Development" required class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Target URL / Link *</label>
                    <input type="text" id="navItemUrl" placeholder="e.g., wordpress-development.php or # for dropdown parent" required class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                    <span class="text-[10px] text-slate-500 mt-1 block">Use relative path like 'about-us.php' or 'case-studies.php' or '#' for menu groups.</span>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Parent Menu Item</label>
                    <select id="navItemParentId" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-medium">
                        <option value="">[ Top-Level Menu Item (No Parent) ]</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Display Order</label>
                        <input type="number" id="navItemDisplayOrder" value="0" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-medium">
                    </div>
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">CSS Class (Optional)</label>
                        <input type="text" id="navItemCssClass" placeholder="e.g., nav-group" class="w-full px-3 py-2.5 rounded-xl glass-input text-xs font-medium">
                    </div>
                </div>

                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="navItemIsActive" checked class="rounded border-slate-700 bg-slate-900 text-brandRed focus:ring-0">
                        <span class="font-bold text-slate-900 dark:text-white">Active (Visible in Header)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="navItemOpenInNewTab" class="rounded border-slate-700 bg-slate-900 text-brandRed focus:ring-0">
                        <span class="text-slate-400 font-medium">Open in new tab</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/40">
                    <button type="button" onclick="closeModal('navItemModal')" class="px-4 py-2.5 rounded-xl glass-card font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brandRed to-red-600 text-white font-bold shadow-lg">Save Menu Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ADD / EDIT MAP HUB MODAL -->
    <div id="mapHubModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-md glass-card rounded-3xl p-6 space-y-6 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-slate-800/40 pb-4">
                <h3 id="mapHubModalTitle" class="text-base font-extrabold text-slate-900 dark:text-white">Add Map Client Hub</h3>
                <button onclick="closeModal('mapHubModal')" class="text-slate-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="mapHubForm" class="space-y-4 text-xs">
                <input type="hidden" id="mapHubId" value="0">

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Emirate / Hub Name</label>
                    <input type="text" id="mapHubName" placeholder="e.g. Dubai (JVC)" required class="w-full px-4 py-2 rounded-xl glass-input text-xs">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Hub Type</label>
                    <input type="text" id="mapHubType" value="Primary Client Hub" required class="w-full px-4 py-2 rounded-xl glass-input text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Latitude</label>
                        <input type="number" step="0.0001" id="mapHubLat" value="25.2048" required class="w-full px-3 py-2 rounded-xl glass-input text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Longitude</label>
                        <input type="number" step="0.0001" id="mapHubLng" value="55.2708" required class="w-full px-3 py-2 rounded-xl glass-input text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Network Capacity Label</label>
                    <input type="text" id="mapHubCapacity" value="200+ Clients" required class="w-full px-4 py-2 rounded-xl glass-input text-xs">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/40">
                    <button type="button" onclick="closeModal('mapHubModal')" class="px-4 py-2 rounded-xl glass-card font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-brandRed to-red-600 text-white font-bold shadow-lg">Save Pin Hub</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ADD ADMIN USER MODAL -->
    <div id="adminUserModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="w-full max-w-md glass-card rounded-3xl p-6 space-y-6 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-slate-800/40 pb-4">
                <h3 id="adminUserModalTitle" class="text-base font-extrabold text-slate-900 dark:text-white">Add New Admin User</h3>
                <button onclick="closeModal('adminUserModal')" class="text-slate-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="adminUserForm" class="space-y-4 text-xs">
                <input type="hidden" id="adminUserId" value="0">

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Full Name</label>
                    <input type="text" id="adminUserName" required class="w-full px-4 py-2 rounded-xl glass-input text-xs">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Corporate Email</label>
                    <input type="email" id="adminUserEmail" required class="w-full px-4 py-2 rounded-xl glass-input text-xs">
                </div>

                <div>
                    <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Password</label>
                    <input type="password" id="adminUserPassword" placeholder="Required for new user" class="w-full px-4 py-2 rounded-xl glass-input text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Role Permission</label>
                        <select id="adminUserRole" class="w-full px-3 py-2 rounded-xl glass-input text-xs">
                            <option value="Editor">Editor</option>
                            <option value="Super Admin">Super Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                        <select id="adminUserStatus" class="w-full px-3 py-2 rounded-xl glass-input text-xs">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/40">
                    <button type="button" onclick="closeModal('adminUserModal')" class="px-4 py-2 rounded-xl glass-card font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-brandRed to-red-600 text-white font-bold shadow-lg">Save Admin User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CASE STUDY EDITOR MODAL -->
    <div id="caseStudyModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="w-full max-w-4xl glass-card rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative my-8 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-800/40 pb-4 flex-shrink-0">
                <div>
                    <h3 id="csModalTitle" class="text-lg font-extrabold text-slate-900 dark:text-white">Add New Case Study</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Fill in case study details, client metrics, and publication status</p>
                </div>
                <button onclick="closeModal('caseStudyModal')" class="text-slate-400 hover:text-white p-2">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="caseStudyForm" class="space-y-6 text-xs overflow-y-auto pr-2 flex-1">
                <input type="hidden" id="csId" value="0">

                <!-- 1. FEATURED IMAGE & MEDIA -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-brandCyan uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-image"></i> Featured Image & Media
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-slate-400 mb-1">Featured / Cover Image Path</label>
                            <div class="flex items-center gap-2">
                                <input type="text" id="csFeaturedImage" oninput="updateCardPreview()" placeholder="assets/images/service-pages/google-ads-hero.svg" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs">
                                <label class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs cursor-pointer flex-shrink-0 flex items-center gap-1.5">
                                    <i class="fa-solid fa-upload text-xs"></i> Upload
                                    <input type="file" id="csImageFileInput" accept="image/*" class="hidden" onchange="uploadCSImage(this)">
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center justify-center p-3 rounded-2xl bg-slate-900/60 border border-slate-800 h-24">
                            <img id="csFeaturedPreview" src="<?= optimizers_url('assets/images/service-pages/google-ads-hero.svg') ?>" alt="Preview" class="max-h-full max-w-full object-contain rounded-lg" onerror="this.src='<?= optimizers_url('assets/icons/brand/google.svg') ?>'">
                        </div>
                    </div>
                </div>

                <!-- 2. FRONTEND CARD DISPLAY & LIVE PREVIEW -->
                <div class="space-y-4 pt-4 border-t border-slate-800/40">
                    <h4 class="text-xs font-bold text-brandCyan uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-id-card"></i> Frontend Card Display
                    </h4>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Inputs -->
                        <div class="lg:col-span-7 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-400 mb-1">Category *</label>
                                    <select id="csCategory" onchange="updateCardPreview()" required class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-semibold">
                                        <option value="" disabled selected>Select Category *</option>
                                        <option value="Digital Marketing Tips for UAE Business Owners">Digital Marketing Tips for UAE Business Owners</option>
                                        <option value="Real Numbers. Real Clients. Real Growth.">Real Numbers. Real Clients. Real Growth.</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-400 mb-1">Frontend Card Title (Optional custom headline)</label>
                                <input type="text" id="csCardTitle" oninput="updateCardPreview()" placeholder="e.g., 320% ROI Boost for Abu Dhabi Luxury Real Estate Brokerage" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                                <span class="text-[10px] text-slate-500">If blank, safely falls back to the main Case Study title below.</span>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-400 mb-1">Frontend Card Description *</label>
                                <textarea id="csShortDesc" oninput="updateCardPreview()" rows="3" required placeholder="Short compelling excerpt displayed on the frontend card..." class="w-full p-3 rounded-xl glass-input text-xs font-medium"></textarea>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-400 mb-1">Category Tags (Comma-separated)</label>
                                <input type="text" id="csTagsList" oninput="updateCardPreview()" placeholder="Real Estate, Performance Marketing, Lead Generation" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs">
                            </div>
                        </div>

                        <!-- Live Card Preview Box -->
                        <div class="lg:col-span-5 flex flex-col justify-start">
                            <label class="block font-bold text-slate-400 mb-2 flex items-center gap-1.5 text-[11px] uppercase tracking-wider">
                                <i class="fa-solid fa-eye text-brandCyan"></i> Frontend Card Live Preview
                            </label>
                            
                            <!-- Reference Card UI Container -->
                            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 shadow-xl space-y-3">
                                <!-- Card Image & Badge -->
                                <div class="relative w-full h-32 rounded-xl overflow-hidden bg-slate-900 border border-slate-800">
                                    <img id="prevImg" src="<?= optimizers_url('assets/images/service-pages/google-ads-hero.svg') ?>" class="w-full h-full object-cover">
                                    <div class="absolute top-2 left-2">
                                        <span id="prevCategory" class="px-2.5 py-1 rounded-md bg-cyan-500/20 text-cyan-400 text-[10px] font-extrabold uppercase tracking-wider border border-cyan-500/30">
                                            PERFORMANCE MARKETING
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="space-y-2 text-left">
                                    <div id="prevTitle" class="text-xs font-extrabold text-white leading-snug">
                                        320% ROI Boost for Abu Dhabi Luxury Real Estate Brokerage
                                    </div>

                                    <div id="prevDesc" class="text-[11px] text-slate-400 line-clamp-3 leading-relaxed">
                                        How Optimizers structured Google Search Ads and Meta retargeting to generate 180+ verified high-net-worth buyer leads monthly.
                                    </div>

                                    <!-- Divider line -->
                                    <div class="border-t border-slate-800/80 pt-2">
                                        <div id="prevTags" class="flex flex-wrap gap-1 mb-2">
                                            <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 text-[9px] font-semibold border border-slate-800">Real Estate</span>
                                            <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 text-[9px] font-semibold border border-slate-800">Lead Generation</span>
                                        </div>

                                        <div class="flex items-center justify-between text-brandCyan text-[11px] font-bold pt-1">
                                            <span>View Case Study</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3+. ORDERED CUSTOM CONTENT -->
                <div class="space-y-3 pt-4 border-t border-slate-800/40">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h4 class="text-xs font-bold text-brandCyan uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-layer-group"></i> Ordered Content Flow
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-1">Arrange story sections and tables in the exact order they should appear on the detail page.</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button type="button" onclick="addCustomSectionRow()" class="px-3 py-1.5 rounded-lg bg-brandCyan/10 text-brandCyan hover:bg-brandCyan/20 text-[11px] font-bold flex items-center gap-1">
                                <i class="fa-solid fa-plus"></i> Add Story Section
                            </button>
                            <button type="button" onclick="addCustomTable()" class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 text-[11px] font-bold flex items-center gap-1">
                                <i class="fa-solid fa-plus"></i> Add Table
                            </button>
                        </div>
                    </div>

                    <div id="csContentBlocksContainer" class="space-y-4">
                        <!-- Story sections and tables are injected here in saved order. -->
                    </div>
                </div>

                <!-- 5. PUBLICATION SETTINGS & SEO -->
                <div class="space-y-3 pt-4 border-t border-slate-800/40">
                    <h4 class="text-xs font-bold text-brandCyan uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-sliders"></i> Publication & Settings
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-slate-400 mb-1">Main Case Study Title (Internal & Detail Page) *</label>
                            <input type="text" id="csTitle" oninput="updateCardPreview()" required placeholder="e.g., 320% ROI Boost for Abu Dhabi Luxury Real Estate Brokerage" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-400 mb-1">URL Slug (Auto-generated if blank)</label>
                            <input type="text" id="csSlug" placeholder="dubai-luxury-real-estate-lead-generation" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-400 mb-1">Main Case Study Description (Internal &amp; Detail Page)</label>
                            <textarea id="csDetailDescription" rows="4" placeholder="Description shown on the Case Study Detail page..." class="w-full p-3 rounded-xl glass-input text-xs font-medium"></textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-400 mb-1">Publication Status</label>
                            <select id="csStatus" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-semibold">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-400 mb-1">Display Order</label>
                            <input type="number" id="csDisplayOrder" value="0" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs font-medium">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-400 mb-1">Services Provided (Comma-separated)</label>
                            <input type="text" id="csServicesList" placeholder="Google Ads, Meta Ads, Lead Generation" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block font-semibold text-slate-400 mb-1">Technologies / Tools Used (Comma-separated)</label>
                            <input type="text" id="csTechnologiesList" placeholder="Google Ads API, Meta Pixel, GA4 Analytics" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-400 mb-1">CTA Section Title</label>
                            <input type="text" id="csCtaTitle" placeholder="Ready to Scale Your Lead Generation in Dubai?" class="w-full px-4 py-2.5 rounded-xl glass-input text-xs">
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800/40 flex-shrink-0">
                    <button type="button" onclick="closeModal('caseStudyModal')" class="px-5 py-2.5 rounded-xl glass-card font-semibold text-xs">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-brandRed to-red-600 hover:from-brandRedHover hover:to-red-500 text-white font-bold text-xs shadow-lg shadow-red-900/30">
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i> Save Case Study
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- APP LOGIC JAVASCRIPT -->
    <script>
        const optimizersAssetUrl = (path) => {
            if (!path || /^(?:[a-z]+:|\/\/|data:|#)/i.test(path)) return path;
            return <?= json_encode(optimizers_url(), JSON_UNESCAPED_SLASHES) ?> + String(path).replace(/^\/+/, '');
        };

        // Theme Toggle Persistence Logic
        const htmlEl = document.documentElement;
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeLabel = document.getElementById('themeLabel');
        const setDarkBtn = document.getElementById('setDarkBtn');
        const setLightBtn = document.getElementById('setLightBtn');

        function setTheme(mode) {
            if (mode === 'dark') {
                htmlEl.classList.add('dark');
                localStorage.setItem('optimizers_theme', 'dark');
                if (themeLabel) themeLabel.textContent = 'Dark Mode';
            } else {
                htmlEl.classList.remove('dark');
                localStorage.setItem('optimizers_theme', 'light');
                if (themeLabel) themeLabel.textContent = 'Light Mode';
            }
            if (window.trendsChart) updateChartTheme();
        }

        const savedTheme = localStorage.getItem('optimizers_theme') || 'dark';
        setTheme(savedTheme);

        themeToggleBtn.addEventListener('click', () => {
            setTheme(htmlEl.classList.contains('dark') ? 'light' : 'dark');
        });
        if (setDarkBtn) setDarkBtn.addEventListener('click', () => setTheme('dark'));
        if (setLightBtn) setLightBtn.addEventListener('click', () => setTheme('light'));

        // Sidebar Navigation Logic
        const navLinks = document.querySelectorAll('.nav-link');
        const tabContents = document.querySelectorAll('.tab-content');
        const pageTitle = document.getElementById('pageTitle');
        const pageSubTitle = document.getElementById('pageSubTitle');

        const tabHeadings = {
            'overview': ['Overview Dashboard', 'Real-time consultation inquiries & activity metrics'],
            'leads': ['Consultation / Lead Management', 'Full details and status workflow for client inquiries'],
            'services': ['Services & Sub-Services CMS', 'Manage main services and sub-category tags for Optimizers UAE'],
            'case-studies': ['Case Studies & Results CMS', 'Manage dynamic case studies, cover images, and metric cards'],
            'navigation': ['Header & Navigation Menu CMS', 'Manage website header navigation links, dropdown hierarchies, and visibility'],
            'footprint': ['Statistics & UAE Footprint', 'Editable counters & interactive map pin hubs'],
            'team': ['Team & Admin Settings', 'Security credentials and Role-Based Access Control']
        };

        function switchTab(tabId) {
            tabContents.forEach(content => content.classList.add('hidden'));
            const targetTab = document.getElementById(`tab-${tabId}`);
            if (targetTab) targetTab.classList.remove('hidden');

            navLinks.forEach(link => {
                if (link.dataset.tab === tabId) {
                    link.className = 'nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-brandRed dark:text-brandCyan bg-brandRed/10 dark:bg-brandCyan/10 border border-brandRed/20 dark:border-brandCyan/20';
                } else {
                    link.className = 'nav-link w-full px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between transition-all text-slate-600 dark:text-slate-400 hover:bg-slate-500/10 hover:text-slate-900 dark:hover:text-white';
                }
            });

            if (tabHeadings[tabId]) {
                pageTitle.textContent = tabHeadings[tabId][0];
                pageSubTitle.textContent = tabHeadings[tabId][1];
            }

            if (tabId === 'overview') loadOverviewData();
            if (tabId === 'leads') fetchLeadsData();
            if (tabId === 'services') loadServicesData();
            if (tabId === 'case-studies') loadCaseStudiesData();
            if (tabId === 'navigation') loadNavigationData();
            if (tabId === 'footprint') loadFootprintData();
            if (tabId === 'team') loadTeamData();
        }

        navLinks.forEach(link => {
            link.addEventListener('click', () => switchTab(link.dataset.tab));
        });
        document.getElementById('notifBtn').addEventListener('click', () => switchTab('leads'));

        // Modal Helpers
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // ================= OVERVIEW TAB DATA & CHART =================
        let trendsChartInstance = null;

        async function loadOverviewData() {
            try {
                const res = await fetch('api.php?action=get_overview');
                const data = await res.json();
                if (!data.success) return;

                const m = data.metrics;
                document.getElementById('statTotalLeads').textContent = m.total_leads;
                document.getElementById('statActiveConsultations').textContent = m.active_consultations;
                document.getElementById('statProjectsDelivered').textContent = m.projects_delivered;
                document.getElementById('statActiveInquiries').textContent = m.active_inquiries;

                // Update notification badge
                const badge = document.getElementById('newLeadsBadge');
                const notifDot = document.getElementById('notifDot');
                if (m.active_inquiries > 0) {
                    badge.textContent = m.active_inquiries;
                    badge.classList.remove('hidden');
                    notifDot.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                    notifDot.classList.add('hidden');
                }

                // Render Chart
                renderOverviewChart(data.trends);

                // Render Recent Leads Quick Table
                renderRecentLeadsTable(data.recent_leads);
            } catch (err) {
                console.error('Error loading overview data:', err);
            }
        }

        function renderOverviewChart(trends) {
            const ctx = document.getElementById('overviewTrendsChart').getContext('2d');
            if (trendsChartInstance) trendsChartInstance.destroy();

            const isDark = htmlEl.classList.contains('dark');
            const labels = trends.map(t => t.month);
            const consultData = trends.map(t => t.consultation);
            const contactData = trends.map(t => t.contact);

            trendsChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Consultation Forms',
                            data: consultData,
                            borderColor: '#00E5FF',
                            backgroundColor: 'rgba(0, 229, 255, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#00E5FF',
                            pointRadius: 4
                        },
                        {
                            label: 'Direct Inquiries',
                            data: contactData,
                            borderColor: '#E50914',
                            backgroundColor: 'rgba(229, 9, 20, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#E50914',
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                            ticks: { color: isDark ? '#94A3B8' : '#64748B', font: { size: 11 } }
                        },
                        y: {
                            grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                            ticks: { color: isDark ? '#94A3B8' : '#64748B', font: { size: 11 } }
                        }
                    }
                }
            });
            window.trendsChart = trendsChartInstance;
        }

        function updateChartTheme() {
            if (trendsChartInstance) {
                const isDark = htmlEl.classList.contains('dark');
                trendsChartInstance.options.scales.x.grid.color = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
                trendsChartInstance.options.scales.x.ticks.color = isDark ? '#94A3B8' : '#64748B';
                trendsChartInstance.options.scales.y.grid.color = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
                trendsChartInstance.options.scales.y.ticks.color = isDark ? '#94A3B8' : '#64748B';
                trendsChartInstance.update();
            }
        }

        function renderRecentLeadsTable(leads) {
            const tbody = document.getElementById('recentLeadsTableBody');
            if (!leads || leads.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="py-8 text-center text-slate-500">No lead submissions found yet.</td></tr>`;
                return;
            }

            tbody.innerHTML = leads.map(lead => `
                <tr class="hover:bg-slate-500/5 transition-colors">
                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">${escapeHtml(lead.name)}</td>
                    <td class="py-3 px-4 text-slate-400">
                        <div>${escapeHtml(lead.email)}</div>
                        <div class="text-[11px] text-slate-500">${escapeHtml(lead.phone || '-')}</div>
                    </td>
                    <td class="py-3 px-4 font-medium text-slate-300">${escapeHtml(lead.service || 'Free Consultation')}</td>
                    <td class="py-3 px-4 font-semibold text-amber-400">${escapeHtml(lead.budget || '-')}</td>
                    <td class="py-3 px-4 text-slate-400 text-[11px]">${lead.created_at ? lead.created_at.substring(0, 10) : ''}</td>
                    <td class="py-3 px-4">${getStatusBadge(lead.status)}</td>
                    <td class="py-3 px-4 text-right">
                        <button onclick="openLeadModal(${lead.id})" class="px-3 py-1.5 rounded-lg bg-brandCyan/10 text-brandCyan hover:bg-brandCyan/20 font-bold text-[11px] transition-colors">
                            View Details
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function getStatusBadge(status) {
            const colors = {
                'New': 'bg-cyan-500/10 text-brandCyan border-cyan-500/30',
                'Contacted': 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                'In Progress': 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                'Converted': 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                'Archived': 'bg-slate-500/10 text-slate-400 border-slate-500/30'
            };
            const cls = colors[status] || colors['New'];
            return `<span class="px-2.5 py-1 rounded-full border text-[10px] font-bold ${cls}">${escapeHtml(status)}</span>`;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // ================= TAB 2: CONSULTATION / LEADS MANAGEMENT =================
        let currentLeadsPage = 1;
        let activeLeadModalData = null;

        async function fetchLeadsData(page = 1) {
            currentLeadsPage = page;
            const search = document.getElementById('leadSearchInput').value.trim();
            const status = document.getElementById('filterStatus').value;
            const budget = document.getElementById('filterBudget').value;
            const service = document.getElementById('filterService').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;

            const query = new URLSearchParams({
                action: 'get_leads',
                page: page,
                limit: 10,
                search: search,
                status: status,
                budget: budget,
                service: service,
                date_from: dateFrom,
                date_to: dateTo
            });

            try {
                const res = await fetch(`api.php?${query.toString()}`);
                const data = await res.json();
                if (!data.success) return;

                renderLeadsTable(data.data);
                renderPagination(data.pagination);
            } catch (err) {
                console.error('Error fetching leads:', err);
            }
        }

        function renderLeadsTable(leads) {
            const tbody = document.getElementById('leadsTableBody');
            if (!leads || leads.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-slate-500">No consultation forms match your criteria.</td></tr>`;
                return;
            }

            tbody.innerHTML = leads.map(lead => {
                const cleanPhone = (lead.phone || '').replace(/[^0-9+]/g, '');
                const whatsappUrl = cleanPhone ? `https://wa.me/${cleanPhone.replace('+', '')}` : '#';
                return `
                <tr class="hover:bg-slate-500/5 transition-colors">
                    <td class="py-3.5 px-4">
                        <div class="font-bold text-slate-900 dark:text-white">${escapeHtml(lead.name)}</div>
                        <a href="mailto:${escapeHtml(lead.email)}" class="text-[11px] text-brandCyan hover:underline">${escapeHtml(lead.email)}</a>
                    </td>
                    <td class="py-3.5 px-4 text-slate-300">
                        <div>${escapeHtml(lead.phone || 'N/A')}</div>
                        ${cleanPhone ? `<a href="${whatsappUrl}" target="_blank" class="text-[10px] text-emerald-400 hover:underline inline-flex items-center gap-1 mt-0.5"><i class="fa-brands fa-whatsapp"></i> Chat</a>` : ''}
                    </td>
                    <td class="py-3.5 px-4 font-semibold text-amber-400">${escapeHtml(lead.budget || 'Not specified')}</td>
                    <td class="py-3.5 px-4 max-w-xs">
                        <div class="font-semibold text-slate-200">${escapeHtml(lead.service || 'Free Consultation')}</div>
                        <div class="text-[11px] text-slate-400 truncate">${escapeHtml(lead.message)}</div>
                    </td>
                    <td class="py-3.5 px-4 text-slate-400 text-[11px]">${lead.created_at ? lead.created_at : ''}</td>
                    <td class="py-3.5 px-4">${getStatusBadge(lead.status)}</td>
                    <td class="py-3.5 px-4 text-right space-x-2">
                        <button onclick="openLeadModal(${lead.id})" class="px-3 py-1.5 rounded-lg bg-brandCyan/10 text-brandCyan hover:bg-brandCyan/20 font-bold text-[11px]">
                            Details
                        </button>
                    </td>
                </tr>
            `;
            }).join('');
        }

        function renderPagination(p) {
            document.getElementById('paginationInfo').textContent = `Showing ${(p.page - 1) * p.limit + 1} - ${Math.min(p.page * p.limit, p.total)} of ${p.total} leads`;

            const container = document.getElementById('paginationControls');
            let html = '';

            if (p.page > 1) {
                html += `<button onclick="fetchLeadsData(${p.page - 1})" class="px-3 py-1.5 rounded-lg glass-card hover:bg-slate-500/20">Prev</button>`;
            }

            for (let i = 1; i <= p.total_pages; i++) {
                if (i === p.page) {
                    html += `<button class="px-3 py-1.5 rounded-lg bg-brandRed text-white font-bold">${i}</button>`;
                } else if (i <= 3 || i >= p.total_pages - 1 || Math.abs(i - p.page) <= 1) {
                    html += `<button onclick="fetchLeadsData(${i})" class="px-3 py-1.5 rounded-lg glass-card hover:bg-slate-500/20">${i}</button>`;
                }
            }

            if (p.page < p.total_pages) {
                html += `<button onclick="fetchLeadsData(${p.page + 1})" class="px-3 py-1.5 rounded-lg glass-card hover:bg-slate-500/20">Next</button>`;
            }

            container.innerHTML = html;
        }

        // Lead Filters Listeners
        document.getElementById('leadSearchInput').addEventListener('input', () => fetchLeadsData(1));
        document.getElementById('filterStatus').addEventListener('change', () => fetchLeadsData(1));
        document.getElementById('filterBudget').addEventListener('change', () => fetchLeadsData(1));
        document.getElementById('filterService').addEventListener('change', () => fetchLeadsData(1));
        document.getElementById('filterDateFrom').addEventListener('change', () => fetchLeadsData(1));
        document.getElementById('filterDateTo').addEventListener('change', () => fetchLeadsData(1));

        document.getElementById('resetLeadFiltersBtn').addEventListener('click', () => {
            document.getElementById('leadSearchInput').value = '';
            document.getElementById('filterStatus').value = 'All';
            document.getElementById('filterBudget').value = '';
            document.getElementById('filterService').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            fetchLeadsData(1);
        });

        // Lead Modal Logic
        async function openLeadModal(leadId) {
            try {
                const res = await fetch(`api.php?action=get_leads&search=&limit=1000`);
                const data = await res.json();
                const lead = data.data.find(l => l.id == leadId);
                if (!lead) return;

                activeLeadModalData = lead;
                document.getElementById('modalLeadName').textContent = lead.name;
                document.getElementById('modalLeadDate').textContent = `Submitted: ${lead.created_at}`;
                document.getElementById('modalLeadEmail').textContent = lead.email;
                document.getElementById('modalLeadEmail').href = `mailto:${lead.email}`;
                document.getElementById('modalLeadPhone').textContent = lead.phone || 'Not provided';
                document.getElementById('modalLeadPhone').href = lead.phone ? `tel:${lead.phone}` : '#';

                const cleanPhone = (lead.phone || '').replace(/[^0-9+]/g, '').replace('+', '');
                document.getElementById('modalWhatsAppBtn').href = cleanPhone ? `https://wa.me/${cleanPhone}` : '#';

                document.getElementById('modalLeadBudget').textContent = lead.budget ? `AED ${lead.budget}` : 'Not specified';
                document.getElementById('modalLeadStatus').value = lead.status;
                document.getElementById('modalLeadMessage').textContent = lead.message;
                document.getElementById('modalAdminNotes').value = lead.admin_notes || '';

                openModal('leadModal');
            } catch (err) {
                console.error(err);
            }
        }

        document.getElementById('modalSaveLeadBtn').addEventListener('click', async () => {
            if (!activeLeadModalData) return;
            const newStatus = document.getElementById('modalLeadStatus').value;
            const newNotes = document.getElementById('modalAdminNotes').value;

            await fetch('api.php?action=update_lead_status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: activeLeadModalData.id, status: newStatus })
            });

            await fetch('api.php?action=update_lead_notes', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: activeLeadModalData.id, admin_notes: newNotes })
            });

            closeModal('leadModal');
            fetchLeadsData(currentLeadsPage);
            loadOverviewData();
        });

        document.getElementById('modalDeleteLeadBtn').addEventListener('click', async () => {
            if (!activeLeadModalData || !confirm(`Delete lead request from ${activeLeadModalData.name}?`)) return;
            await fetch('api.php?action=delete_lead', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: activeLeadModalData.id })
            });
            closeModal('leadModal');
            fetchLeadsData(currentLeadsPage);
            loadOverviewData();
        });

        // ================= TAB 3: SERVICES CMS =================
        async function loadServicesData() {
            try {
                const res = await fetch('api.php?action=get_services');
                const data = await res.json();
                if (!data.success) return;

                renderServicesGrid(data.data);
            } catch (err) {
                console.error('Error loading services:', err);
            }
        }

        function renderServicesGrid(services) {
            const grid = document.getElementById('servicesGrid');
            grid.innerHTML = services.map(srv => {
                const tags = srv.sub_services || [];
                return `
                <div class="glass-card rounded-3xl p-6 flex flex-col justify-between space-y-4 hover:border-brandCyan/40 transition-all">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full bg-brandCyan/10 text-brandCyan text-[10px] font-extrabold uppercase tracking-wider border border-brandCyan/20">
                                ${escapeHtml(srv.category)}
                            </span>
                            <div class="flex items-center gap-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" ${srv.is_active ? 'checked' : ''} onchange="toggleServiceStatus(${srv.id})" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brandCyan"></div>
                                </label>
                            </div>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">${escapeHtml(srv.title)}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">${escapeHtml(srv.description)}</p>
                        
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            ${tags.map(t => `<span class="px-2 py-0.5 rounded-md bg-slate-500/10 text-slate-300 text-[10px] font-semibold">${escapeHtml(t)}</span>`).join('')}
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800/30 flex items-center justify-between">
                        <span class="text-[10px] font-bold ${srv.is_active ? 'text-emerald-400' : 'text-slate-500'}">
                            ${srv.is_active ? '● Displayed on Site' : '○ Hidden'}
                        </span>
                        <div class="flex items-center gap-2">
                            <button onclick="editServiceModal(${srv.id})" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold text-[11px]">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                            </button>
                            <button onclick="deleteService(${srv.id})" class="p-1.5 text-slate-400 hover:text-red-400">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
        }

        async function toggleServiceStatus(id) {
            await fetch('api.php?action=toggle_service_status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            loadServicesData();
        }

        async function deleteService(id) {
            if (!confirm('Are you sure you want to delete this service?')) return;
            await fetch('api.php?action=delete_service', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            loadServicesData();
        }

        async function uploadServiceImage(input) {
            if (!input.files || !input.files[0]) return;
            const formData = new FormData();
            formData.append('image', input.files[0]);

            try {
                const res = await fetch('api.php?action=upload_service_image', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('serviceFeaturedImage').value = data.url;
                } else {
                    alert(data.error);
                }
            } catch (err) {
                console.error('Error uploading service image:', err);
                alert('Image upload failed.');
            }
        }

        document.getElementById('addNewServiceBtn').addEventListener('click', () => {
            document.getElementById('serviceModalTitle').textContent = 'Add New Service';
            document.getElementById('serviceId').value = 0;
            document.getElementById('serviceTitleInput').value = '';
            document.getElementById('serviceSlugInput').value = '';
            document.getElementById('serviceCategoryInput').value = 'SEO';
            document.getElementById('serviceIconInput').value = 'assets/icons/brand/google.svg';
            document.getElementById('serviceDescInput').value = '';
            document.getElementById('serviceFullDescInput').value = '';
            document.getElementById('serviceSubTagsInput').value = '';
            document.getElementById('serviceFeaturesInput').value = '';
            document.getElementById('serviceBenefitsInput').value = '';
            document.getElementById('serviceFeaturedImage').value = '';
            document.getElementById('serviceActiveInput').checked = true;
            openModal('serviceModal');
        });

        async function editServiceModal(id) {
            try {
                const res = await fetch(`api.php?action=get_service&id=${id}`);
                const data = await res.json();
                const srv = data.data;
                if (!srv) return;

                document.getElementById('serviceModalTitle').textContent = 'Edit Service';
                document.getElementById('serviceId').value = srv.id;
                document.getElementById('serviceTitleInput').value = srv.title || '';
                document.getElementById('serviceSlugInput').value = srv.slug || '';
                document.getElementById('serviceCategoryInput').value = srv.category || 'SEO';
                document.getElementById('serviceIconInput').value = srv.icon || 'assets/icons/brand/google.svg';
                document.getElementById('serviceDescInput').value = srv.description || srv.short_description || '';
                document.getElementById('serviceFullDescInput').value = srv.full_description || '';
                document.getElementById('serviceSubTagsInput').value = (srv.sub_services || []).join(', ');
                document.getElementById('serviceFeaturesInput').value = (srv.features || []).map(f => typeof f === 'object' ? f.title : f).join('\n');
                document.getElementById('serviceBenefitsInput').value = (srv.benefits || []).map(b => typeof b === 'object' ? b.text : b).join('\n');
                document.getElementById('serviceFeaturedImage').value = srv.featured_image || '';
                document.getElementById('serviceActiveInput').checked = srv.is_active == 1;
                openModal('serviceModal');
            } catch (err) {
                console.error('Error fetching service:', err);
            }
        }

        document.getElementById('serviceForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('serviceId').value;
            const parseLines = str => str.split('\n').map(s => s.trim()).filter(Boolean);

            const payload = {
                id: id,
                title: document.getElementById('serviceTitleInput').value,
                slug: document.getElementById('serviceSlugInput').value,
                category: document.getElementById('serviceCategoryInput').value,
                icon: document.getElementById('serviceIconInput').value,
                description: document.getElementById('serviceDescInput').value,
                short_description: document.getElementById('serviceDescInput').value,
                full_description: document.getElementById('serviceFullDescInput').value,
                sub_services: document.getElementById('serviceSubTagsInput').value,
                features: parseLines(document.getElementById('serviceFeaturesInput').value),
                benefits: parseLines(document.getElementById('serviceBenefitsInput').value),
                featured_image: document.getElementById('serviceFeaturedImage').value,
                is_active: document.getElementById('serviceActiveInput').checked ? 1 : 0
            };

            await fetch('api.php?action=save_service', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            closeModal('serviceModal');
            loadServicesData();
        });

        // ================= TAB 4: STATISTICS & FOOTPRINT MANAGEMENT =================
        async function loadFootprintData() {
            try {
                const res = await fetch('api.php?action=get_footprint');
                const data = await res.json();
                if (!data.success) return;

                renderFootprintStats(data.stats);
                renderMapHubsTable(data.map_hubs);
            } catch (err) {
                console.error('Error loading footprint data:', err);
            }
        }

        function renderFootprintStats(stats) {
            const container = document.getElementById('footprintStatsGrid');
            container.innerHTML = stats.map(st => `
                <div class="p-4 rounded-2xl bg-slate-500/10 border border-slate-500/20 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-brandCyan">${escapeHtml(st.stat_key)}</span>
                        <i class="fa-solid fa-chart-line text-slate-400 text-xs"></i>
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 uppercase font-bold mb-1">Counter Value</label>
                        <input type="text" id="stat_val_${st.id}" value="${escapeHtml(st.value)}" class="w-full px-3 py-2 rounded-xl glass-input text-sm font-extrabold text-amber-400">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 uppercase font-bold mb-1">Display Label</label>
                        <input type="text" id="stat_lbl_${st.id}" value="${escapeHtml(st.label)}" class="w-full px-3 py-2 rounded-xl glass-input text-xs font-semibold">
                    </div>
                    <button onclick="saveFootprintStat(${st.id})" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl transition-colors">
                        Update Counter
                    </button>
                </div>
            `).join('');
        }

        async function saveFootprintStat(id) {
            const val = document.getElementById(`stat_val_${id}`).value;
            const lbl = document.getElementById(`stat_lbl_${id}`).value;

            await fetch('api.php?action=update_footprint_stat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, value: val, label: lbl })
            });

            alert('Counter stat updated successfully!');
            loadFootprintData();
        }

        function renderMapHubsTable(hubs) {
            const tbody = document.getElementById('mapHubsTableBody');
            if (!hubs || hubs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-slate-500">No map hubs configured yet.</td></tr>`;
                return;
            }

            tbody.innerHTML = hubs.map(h => `
                <tr class="hover:bg-slate-500/5 transition-colors">
                    <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">${escapeHtml(h.name)}</td>
                    <td class="py-3 px-4 text-brandCyan">${escapeHtml(h.type)}</td>
                    <td class="py-3 px-4 text-slate-400">${h.lat}, ${h.lng}</td>
                    <td class="py-3 px-4 font-semibold text-amber-400">${escapeHtml(h.capacity)}</td>
                    <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold text-[10px]">${escapeHtml(h.status)}</span></td>
                    <td class="py-3 px-4 text-right space-x-2">
                        <button onclick="deleteMapHub(${h.id})" class="text-slate-400 hover:text-red-400 p-1"><i class="fa-solid fa-trash text-xs"></i></button>
                    </td>
                </tr>
            `).join('');
        }

        document.getElementById('addNewMapHubBtn').addEventListener('click', () => {
            document.getElementById('mapHubId').value = 0;
            document.getElementById('mapHubName').value = '';
            openModal('mapHubModal');
        });

        document.getElementById('mapHubForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                id: document.getElementById('mapHubId').value,
                name: document.getElementById('mapHubName').value,
                type: document.getElementById('mapHubType').value,
                lat: document.getElementById('mapHubLat').value,
                lng: document.getElementById('mapHubLng').value,
                capacity: document.getElementById('mapHubCapacity').value
            };

            await fetch('api.php?action=save_map_hub', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            closeModal('mapHubModal');
            loadFootprintData();
        });

        async function deleteMapHub(id) {
            if (!confirm('Delete this map pin hub?')) return;
            await fetch('api.php?action=delete_map_hub', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            loadFootprintData();
        }

        // ================= TAB 5: TEAM & SETTINGS =================
        async function loadTeamData() {
            try {
                const res = await fetch('api.php?action=get_team');
                const data = await res.json();
                if (!data.success) return;

                renderTeamTable(data.data);
            } catch (err) {
                console.error('Error loading team users:', err);
            }
        }

        function renderTeamTable(users) {
            const tbody = document.getElementById('teamTableBody');
            tbody.innerHTML = users.map(u => `
                <tr class="hover:bg-slate-500/5 transition-colors">
                    <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-brandRed text-white text-[10px] font-bold flex items-center justify-center">
                            ${escapeHtml(u.name.substring(0, 2).toUpperCase())}
                        </div>
                        <span>${escapeHtml(u.name)}</span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-400">${escapeHtml(u.email)}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold ${u.role === 'Super Admin' ? 'bg-brandRed/10 text-brandRed border border-brandRed/30' : 'bg-brandCyan/10 text-brandCyan border border-brandCyan/30'}">
                            ${escapeHtml(u.role)}
                        </span>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="px-2 py-0.5 rounded-full ${u.status === 'Active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-500/10 text-slate-400'} font-bold text-[10px]">
                            ${escapeHtml(u.status)}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-400 text-[11px]">${u.last_login || 'Never'}</td>
                    <td class="py-3.5 px-4 text-right space-x-2">
                        <?php if ($currentUser['role'] === 'Super Admin'): ?>
                        <button onclick="deleteAdminUser(${u.id})" class="text-slate-400 hover:text-red-400 p-1"><i class="fa-solid fa-trash text-xs"></i></button>
                        <?php else: ?>
                        <span class="text-[10px] text-slate-500">Read Only</span>
                        <?php endif; ?>
                    </td>
                </tr>
            `).join('');
        }

        // Add Admin User Listener
        const addNewAdminBtn = document.getElementById('addNewAdminBtn');
        if (addNewAdminBtn) {
            addNewAdminBtn.addEventListener('click', () => {
                document.getElementById('adminUserId').value = 0;
                document.getElementById('adminUserName').value = '';
                document.getElementById('adminUserEmail').value = '';
                document.getElementById('adminUserPassword').value = '';
                openModal('adminUserModal');
            });
        }

        document.getElementById('adminUserForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                id: document.getElementById('adminUserId').value,
                name: document.getElementById('adminUserName').value,
                email: document.getElementById('adminUserEmail').value,
                password: document.getElementById('adminUserPassword').value,
                role: document.getElementById('adminUserRole').value,
                status: document.getElementById('adminUserStatus').value
            };

            const res = await fetch('api.php?action=save_admin_user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!data.success) {
                alert(data.error);
                return;
            }

            closeModal('adminUserModal');
            loadTeamData();
        });

        async function deleteAdminUser(id) {
            if (!confirm('Are you sure you want to delete this admin account?')) return;
            const res = await fetch('api.php?action=delete_admin_user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const data = await res.json();
            if (!data.success) {
                alert(data.error);
                return;
            }
            loadTeamData();
        }

        // Profile Form Submit
        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                name: document.getElementById('profileName').value,
                email: document.getElementById('profileEmail').value,
                current_password: document.getElementById('profileCurrentPass').value,
                new_password: document.getElementById('profileNewPass').value
            };

            const res = await fetch('api.php?action=update_profile', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                alert('Profile settings saved!');
                location.reload();
            } else {
                alert(data.error);
            }
        });

        // Mobile Sidebar Controls
        // ================= CASE STUDIES CMS LOGIC =================
        let currentCSPage = 1;
        let cachedCaseStudies = [];

        async function loadCaseStudiesData(page = 1) {
            currentCSPage = page;
            const search = document.getElementById('csSearchInput') ? document.getElementById('csSearchInput').value : '';
            const category = document.getElementById('csCategoryFilter') ? document.getElementById('csCategoryFilter').value : 'All';
            const status = document.getElementById('csStatusFilter') ? document.getElementById('csStatusFilter').value : 'All';

            try {
                const query = new URLSearchParams({
                    action: 'get_case_studies',
                    search: search,
                    category: category,
                    status: status,
                    page: page,
                    limit: 10
                });

                const res = await fetch(`api.php?${query.toString()}`);
                const data = await res.json();
                if (!data.success) return;

                cachedCaseStudies = data.data;
                renderCaseStudiesTable(data.data);
                renderCSPagination(data.pagination);
            } catch (err) {
                console.error('Error loading case studies:', err);
            }
        }

        // Search & Filter event listeners
        ['csSearchInput', 'csCategoryFilter', 'csStatusFilter'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener(id === 'csSearchInput' ? 'input' : 'change', () => {
                    loadCaseStudiesData(1);
                });
            }
        });

        function renderCaseStudiesTable(cases) {
            const tbody = document.getElementById('caseStudiesTableBody');
            if (!cases || cases.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="py-8 text-center text-slate-500">No case studies found matching your criteria.</td></tr>`;
                return;
            }

            tbody.innerHTML = cases.map(cs => {
                const isPublished = cs.status === 'published';
                const statusBadge = isPublished
                    ? `<span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 font-bold text-[10px] border border-emerald-500/20"><i class="fa-solid fa-check mr-1"></i>Published</span>`
                    : `<span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 font-bold text-[10px] border border-amber-500/20"><i class="fa-solid fa-pen-ruler mr-1"></i>Draft</span>`;
                
                const featuredImg = cs.featured_image ? escapeHtml(cs.featured_image) : 'assets/images/service-pages/google-ads-hero.svg';

                return `
                    <tr class="hover:bg-slate-500/5 transition-colors group">
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <img src="${optimizersAssetUrl(featuredImg)}" alt="" class="w-full h-full object-cover" onerror="this.src='${optimizersAssetUrl('assets/icons/brand/google.svg')}'">
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900 dark:text-white text-xs line-clamp-1">${escapeHtml(cs.title)}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">case-studies/${escapeHtml(cs.slug)}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-brandCyan font-semibold text-xs">${escapeHtml(cs.category)}</td>
                        <td class="py-3.5 px-4 text-slate-300 text-xs">
                            <div class="font-semibold">${escapeHtml(cs.client_name || 'N/A')}</div>
                            <div class="text-[10px] text-slate-400">${escapeHtml(cs.industry || 'General')}</div>
                        </td>
                        <td class="py-3.5 px-4">${statusBadge}</td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <a href="../case-studies/${escapeHtml(cs.slug)}" target="_blank" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-brandCyan text-[11px] font-bold" title="View Detail Page">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                            <button onclick="toggleCSStatus(${cs.id})" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold" title="Toggle Publish Status">
                                <i class="fa-solid ${isPublished ? 'fa-eye-slash' : 'fa-eye'}"></i>
                            </button>
                            <button onclick="editCSModal(${cs.id})" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="deleteCS(${cs.id})" class="px-2 py-1 rounded-lg bg-red-950/80 hover:bg-red-900 text-red-300 text-[11px] font-bold" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function renderCSPagination(pg) {
            const container = document.getElementById('csPagination');
            if (!container || !pg) return;

            if (pg.total_pages <= 1) {
                container.innerHTML = `<span class="text-slate-400">Total ${pg.total} Case Studies</span><span>Page 1 of 1</span>`;
                return;
            }

            container.innerHTML = `
                <span class="text-slate-400">Showing page ${pg.page} of ${pg.total_pages} (${pg.total} items)</span>
                <div class="flex items-center gap-2">
                    <button onclick="loadCaseStudiesData(${pg.page - 1})" ${pg.page <= 1 ? 'disabled' : ''} class="px-3 py-1.5 rounded-lg glass-card disabled:opacity-40 text-xs font-semibold">Prev</button>
                    <button onclick="loadCaseStudiesData(${pg.page + 1})" ${pg.page >= pg.total_pages ? 'disabled' : ''} class="px-3 py-1.5 rounded-lg glass-card disabled:opacity-40 text-xs font-semibold">Next</button>
                </div>
            `;
        }

        // Add Case Study Listener
        const addNewCaseStudyBtn = document.getElementById('addNewCaseStudyBtn');
        if (addNewCaseStudyBtn) {
            addNewCaseStudyBtn.addEventListener('click', () => {
                openCSModal(0);
            });
        }

        function updateCardPreview() {
            const category = document.getElementById('csCategory')?.value || 'PERFORMANCE MARKETING';
            const cardTitle = document.getElementById('csCardTitle')?.value || document.getElementById('csTitle')?.value || '320% ROI Boost for Abu Dhabi Luxury Real Estate Brokerage';
            const shortDesc = document.getElementById('csShortDesc')?.value || 'How Optimizers structured Google Search Ads and Meta retargeting to generate 180+ verified high-net-worth buyer leads monthly.';
            const imgUrl = document.getElementById('csFeaturedImage')?.value;
            const tagsStr = document.getElementById('csTagsList')?.value || 'Real Estate, Performance Marketing, Lead Generation';

            if (document.getElementById('prevCategory')) document.getElementById('prevCategory').textContent = category.toUpperCase();
            if (document.getElementById('prevTitle')) document.getElementById('prevTitle').textContent = cardTitle;
            if (document.getElementById('prevDesc')) document.getElementById('prevDesc').textContent = shortDesc;

            if (document.getElementById('prevImg')) {
                document.getElementById('prevImg').src = imgUrl ? optimizersAssetUrl(imgUrl) : optimizersAssetUrl('assets/images/service-pages/google-ads-hero.svg');
            }

            if (document.getElementById('prevTags')) {
                const tags = tagsStr.split(',').map(s => s.trim()).filter(Boolean);
                document.getElementById('prevTags').innerHTML = tags.map(t => `<span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 text-[9px] font-semibold border border-slate-800">${escapeHtml(t)}</span>`).join('');
            }
        }

        function addCustomSectionRow(sec = { title: '', content: '', image: '' }) {
            const container = document.getElementById('csContentBlocksContainer');
            if (!container) return;
            const div = document.createElement('div');
            div.className = 'p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800/80 space-y-2.5 relative group cs-custom-section-row';
            div.dataset.blockType = 'section';
            div.innerHTML = `
                <div class="flex items-center justify-between gap-2">
                    <input type="text" class="sec-title w-full px-3 py-1.5 rounded-lg glass-input text-xs font-bold text-brandCyan" placeholder="Section Heading (e.g. The Challenge, Campaign Objectives)" value="${escapeHtml(sec.title || '')}">
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" onclick="moveSectionUp(this)" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded text-[10px]" title="Move Up"><i class="fa-solid fa-arrow-up"></i></button>
                        <button type="button" onclick="moveSectionDown(this)" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded text-[10px]" title="Move Down"><i class="fa-solid fa-arrow-down"></i></button>
                        <button type="button" onclick="this.closest('.cs-custom-section-row').remove()" class="px-2 py-1 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded text-[10px]" title="Delete Section"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
                <textarea class="sec-content w-full p-2.5 rounded-lg glass-input text-xs font-medium" rows="3" placeholder="Section narrative content...">${escapeHtml(sec.content || '')}</textarea>
                <input type="text" class="sec-image w-full px-3 py-1.5 rounded-lg glass-input text-xs font-mono" placeholder="Optional image path: assets/images/..." value="${escapeHtml(sec.image || (sec.image_url || ''))}">
            `;
            container.appendChild(div);
        }

        function moveSectionUp(btn) {
            const row = btn.closest('.cs-custom-section-row');
            if (row && row.previousElementSibling) {
                row.parentNode.insertBefore(row, row.previousElementSibling);
            }
        }

        function moveSectionDown(btn) {
            const row = btn.closest('.cs-custom-section-row');
            if (row && row.nextElementSibling) {
                row.parentNode.insertBefore(row.nextElementSibling, row);
            }
        }

        function addCustomTable(tbl = { title: '', headers: [], rows: [] }) {
            const container = document.getElementById('csContentBlocksContainer');
            if (!container) return;

            const headers = (tbl.headers && tbl.headers.length) ? tbl.headers : ['Specialty', 'CPC Range (AED)', 'Lead quality', 'Minimum monthly spend'];
            const rows = (tbl.rows && tbl.rows.length) ? tbl.rows : [
                ['General practice / GP', '5–12', 'High volume', 'AED 3,000'],
                ['Physiotherapy', '8–18', 'High local intent', 'AED 4,000'],
                ['Dermatology (acne, laser)', '10–30', 'Medium-high intent', 'AED 5,000'],
                ['Dental (general)', '12–30', 'High volume', 'AED 5,000']
            ];

            const wrapper = document.createElement('div');
            wrapper.className = 'p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 cs-custom-table-card relative';
            wrapper.dataset.blockType = 'table';

            let tableHtml = `
                <div class="flex items-center justify-between gap-3">
                    <input type="text" class="tbl-title w-full px-3 py-2 rounded-xl glass-input text-xs font-extrabold text-emerald-400" placeholder="Table Title (e.g., What Do Google Ads Cost for UAE Clinics in 2026?)" value="${escapeHtml(tbl.title || '')}">
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="button" onclick="moveTableUp(this)" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded text-[10px]" title="Move Up"><i class="fa-solid fa-arrow-up"></i></button>
                        <button type="button" onclick="moveTableDown(this)" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded text-[10px]" title="Move Down"><i class="fa-solid fa-arrow-down"></i></button>
                        <button type="button" onclick="this.closest('.cs-custom-table-card').remove()" class="px-2 py-1 bg-red-500/10 text-red-400 hover:bg-red-500/20 rounded text-[10px]" title="Delete Table"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>

                <div class="overflow-x-auto border border-slate-800 rounded-xl">
                    <table class="w-full text-left text-xs tbl-matrix">
                        <thead>
                            <tr class="bg-slate-950/80 border-b border-slate-800 tbl-header-row">
                                ${headers.map((h, i) => `
                                    <th class="p-2 min-w-[140px]">
                                        <div class="flex items-center gap-1">
                                            <input type="text" class="tbl-header-input w-full px-2.5 py-1.5 rounded-lg glass-input text-[11px] font-bold text-emerald-400" value="${escapeHtml(h)}">
                                            ${headers.length > 2 ? `<button type="button" onclick="deleteTableColumn(this, ${i})" class="text-slate-500 hover:text-red-400 p-1" title="Delete Column"><i class="fa-solid fa-xmark"></i></button>` : ''}
                                        </div>
                                    </th>
                                `).join('')}
                                <th class="p-2 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 tbl-body">
                            ${rows.map(r => `
                                <tr class="tbl-row-item">
                                    ${headers.map((_, i) => `
                                        <td class="p-2 min-w-[140px]">
                                            <input type="text" class="tbl-cell-input w-full px-2.5 py-1.5 rounded-lg glass-input text-xs" value="${escapeHtml(r[i] || '')}">
                                        </td>
                                    `).join('')}
                                    <td class="p-2 w-10 text-center">
                                        <button type="button" onclick="this.closest('.tbl-row-item').remove()" class="text-slate-500 hover:text-red-400 p-1" title="Delete Row"><i class="fa-solid fa-trash-can"></i></button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <button type="button" onclick="addTableRow(this)" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold rounded-lg flex items-center gap-1">
                        <i class="fa-solid fa-plus text-[10px]"></i> Add Row
                    </button>
                    <button type="button" onclick="addTableColumn(this)" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold rounded-lg flex items-center gap-1">
                        <i class="fa-solid fa-columns text-[10px]"></i> Add Column
                    </button>
                </div>
            `;

            wrapper.innerHTML = tableHtml;
            container.appendChild(wrapper);
        }

        function addTableRow(btn) {
            const card = btn.closest('.cs-custom-table-card');
            const tbody = card.querySelector('.tbl-body');
            const colCount = card.querySelectorAll('.tbl-header-input').length;
            const tr = document.createElement('tr');
            tr.className = 'tbl-row-item';
            let cellsHtml = '';
            for (let i = 0; i < colCount; i++) {
                cellsHtml += `<td class="p-2 min-w-[140px]"><input type="text" class="tbl-cell-input w-full px-2.5 py-1.5 rounded-lg glass-input text-xs" value=""></td>`;
            }
            cellsHtml += `<td class="p-2 w-10 text-center"><button type="button" onclick="this.closest('.tbl-row-item').remove()" class="text-slate-500 hover:text-red-400 p-1"><i class="fa-solid fa-trash-can"></i></button></td>`;
            tr.innerHTML = cellsHtml;
            tbody.appendChild(tr);
        }

        function addTableColumn(btn) {
            const card = btn.closest('.cs-custom-table-card');
            const headerRow = card.querySelector('.tbl-header-row');
            const colIndex = card.querySelectorAll('.tbl-header-input').length;
            
            const actionTh = headerRow.lastElementChild;
            const th = document.createElement('th');
            th.className = 'p-2 min-w-[140px]';
            th.innerHTML = `
                <div class="flex items-center gap-1">
                    <input type="text" class="tbl-header-input w-full px-2.5 py-1.5 rounded-lg glass-input text-[11px] font-bold text-emerald-400" value="Header ${colIndex + 1}">
                    <button type="button" onclick="deleteTableColumn(this, ${colIndex})" class="text-slate-500 hover:text-red-400 p-1" title="Delete Column"><i class="fa-solid fa-xmark"></i></button>
                </div>
            `;
            headerRow.insertBefore(th, actionTh);

            const rows = card.querySelectorAll('.tbl-row-item');
            rows.forEach(tr => {
                const actionTd = tr.lastElementChild;
                const td = document.createElement('td');
                td.className = 'p-2 min-w-[140px]';
                td.innerHTML = `<input type="text" class="tbl-cell-input w-full px-2.5 py-1.5 rounded-lg glass-input text-xs" value="">`;
                tr.insertBefore(td, actionTd);
            });
        }

        function deleteTableColumn(btn, index) {
            const card = btn.closest('.cs-custom-table-card');
            const headers = card.querySelectorAll('.tbl-header-input');
            if (headers.length <= 1) {
                alert('Table must have at least 1 column.');
                return;
            }
            const th = btn.closest('th');
            const thIndex = Array.from(th.parentNode.children).indexOf(th);
            th.remove();

            const rows = card.querySelectorAll('.tbl-row-item');
            rows.forEach(tr => {
                if (tr.children[thIndex]) {
                    tr.children[thIndex].remove();
                }
            });
        }

        function moveTableUp(btn) {
            const row = btn.closest('.cs-custom-table-card');
            if (row && row.previousElementSibling) row.parentNode.insertBefore(row, row.previousElementSibling);
        }

        function moveTableDown(btn) {
            const row = btn.closest('.cs-custom-table-card');
            if (row && row.nextElementSibling) row.parentNode.insertBefore(row.nextElementSibling, row);
        }

        async function openCSModal(id = 0) {
            document.getElementById('csModalTitle').textContent = id > 0 ? 'Edit Case Study' : 'Add New Case Study';
            document.getElementById('csId').value = id;
            
            const contentContainer = document.getElementById('csContentBlocksContainer');
            if (contentContainer) contentContainer.innerHTML = '';

            if (id > 0) {
                let cs = cachedCaseStudies.find(c => c.id == id);
                try {
                    const res = await fetch(`api.php?action=get_case_study&id=${id}`);
                    const resData = await res.json();
                    if (resData.success && resData.data) {
                        cs = resData.data;
                    }
                } catch (e) {}

                if (cs) {
                    document.getElementById('csTitle').value = cs.title || '';
                    document.getElementById('csSlug').value = cs.slug || '';
                    let catVal = cs.category || 'Digital Marketing Tips for UAE Business Owners';
                    if (catVal.includes('Tips') || catVal.includes('digital-marketing') || catVal.toUpperCase().includes('DIGITAL MARKETING') || catVal.includes('TikTok') || catVal.includes('WhatsApp')) {
                        catVal = 'Digital Marketing Tips for UAE Business Owners';
                    } else {
                        catVal = 'Real Numbers. Real Clients. Real Growth.';
                    }
                    document.getElementById('csCategory').value = catVal;
                    document.getElementById('csCardTitle').value = cs.card_title || '';
                    document.getElementById('csDetailDescription').value = cs.detail_description || cs.short_description || '';
                    document.getElementById('csStatus').value = cs.status || 'published';
                    document.getElementById('csDisplayOrder').value = cs.display_order || 0;
                    document.getElementById('csFeaturedImage').value = cs.featured_image || '';
                    document.getElementById('csFeaturedPreview').src = cs.featured_image ? optimizersAssetUrl(cs.featured_image) : optimizersAssetUrl('assets/images/service-pages/google-ads-hero.svg');
                    document.getElementById('csShortDesc').value = cs.short_description || '';
                    document.getElementById('csCtaTitle').value = cs.call_to_action_title || '';

                    // JSON decode arrays
                    try {
                        const services = Array.isArray(cs.services) ? cs.services : JSON.parse(cs.services || '[]');
                        document.getElementById('csServicesList').value = Array.isArray(services) ? services.join(', ') : '';
                    } catch (e) { document.getElementById('csServicesList').value = ''; }

                    try {
                        const techs = Array.isArray(cs.technologies) ? cs.technologies : JSON.parse(cs.technologies || '[]');
                        document.getElementById('csTechnologiesList').value = Array.isArray(techs) ? techs.join(', ') : '';
                    } catch (e) { document.getElementById('csTechnologiesList').value = ''; }

                    try {
                        const tags = Array.isArray(cs.tags) ? cs.tags : JSON.parse(cs.tags || '[]');
                        document.getElementById('csTagsList').value = Array.isArray(tags) ? tags.join(', ') : '';
                    } catch (e) { document.getElementById('csTagsList').value = ''; }

                    const customSecs = cs.custom_sections || [];
                    const customTbls = cs.custom_tables || (Array.isArray(cs.tables) ? cs.tables : JSON.parse(cs.tables || '[]'));
                    const contentBlocks = Array.isArray(cs.content_blocks) ? cs.content_blocks : [];
                    if (contentBlocks.length > 0) {
                        contentBlocks.forEach(block => block.type === 'table' ? addCustomTable(block) : addCustomSectionRow(block));
                    } else {
                        customSecs.forEach(s => addCustomSectionRow(s));
                        customTbls.forEach(t => addCustomTable(t));
                    }
                }
            } else {
                // Default values for new case study
                document.getElementById('csTitle').value = '';
                document.getElementById('csSlug').value = '';
                document.getElementById('csCategory').value = 'Digital Marketing Tips for UAE Business Owners';
                document.getElementById('csCardTitle').value = '';
                document.getElementById('csDetailDescription').value = '';
                document.getElementById('csStatus').value = 'published';
                document.getElementById('csDisplayOrder').value = '0';
                document.getElementById('csFeaturedImage').value = 'assets/images/service-pages/google-ads-hero.svg';
                document.getElementById('csFeaturedPreview').src = optimizersAssetUrl('assets/images/service-pages/google-ads-hero.svg');
                document.getElementById('csShortDesc').value = 'How Optimizers structured Google Search Ads and Meta retargeting to generate 180+ verified high-net-worth buyer leads monthly while cutting Cost Per Lead.';
                document.getElementById('csServicesList').value = 'Google Ads, Meta Ads, Lead Generation';
                document.getElementById('csTechnologiesList').value = 'Google Ads API, Meta Pixel, GA4 Analytics';
                document.getElementById('csTagsList').value = 'Real Estate, Performance Marketing, Lead Generation';
                document.getElementById('csCtaTitle').value = 'Ready to Scale Your Lead Generation in Dubai?';
                
                addCustomSectionRow({ title: 'The Challenge', content: 'High lead acquisition costs and low conversion rates from unqualified leads.' });
                addCustomSectionRow({ title: 'Campaign Objectives', content: 'Lower CPL while increasing qualified lead volume.' });
            }

            updateCardPreview();
            openModal('caseStudyModal');
        }

        function editCSModal(id) {
            openCSModal(id);
        }

        async function uploadCSImage(input) {
            if (!input.files || !input.files[0]) return;
            const formData = new FormData();
            formData.append('image', input.files[0]);

            try {
                const res = await fetch('api.php?action=upload_case_study_image', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('csFeaturedImage').value = data.url;
                    document.getElementById('csFeaturedPreview').src = optimizersAssetUrl(data.url);
                    updateCardPreview();
                } else {
                    alert(data.error);
                }
            } catch (err) {
                console.error('Error uploading image:', err);
                alert('Image upload failed.');
            }
        }

        const caseStudyForm = document.getElementById('caseStudyForm');
        if (caseStudyForm) {
            caseStudyForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Gather ordered custom content blocks and compatibility arrays.
                const contentBlocksArr = [];
                const customSecsArr = [];
                const secRows = document.querySelectorAll('.cs-custom-section-row');
                secRows.forEach((row, sIdx) => {
                    const title = row.querySelector('.sec-title').value.trim();
                    const content = row.querySelector('.sec-content').value.trim();
                    const image = row.querySelector('.sec-image').value.trim();
                    if (title || content) {
                        const block = { type: 'section', title, content, image };
                        contentBlocksArr.push(block);
                        customSecsArr.push({ title, content, image, display_order: customSecsArr.length });
                    }
                });

                // Gather custom table rows
                const tableCards = document.querySelectorAll('.cs-custom-table-card');
                const customTablesArr = [];
                tableCards.forEach((card, tIdx) => {
                    const tTitle = card.querySelector('.tbl-title').value.trim();
                    const headerInputs = card.querySelectorAll('.tbl-header-input');
                    const headers = Array.from(headerInputs).map(h => h.value.trim());

                    const rowTrs = card.querySelectorAll('.tbl-row-item');
                    const rows = [];
                    rowTrs.forEach(tr => {
                        const cellInputs = tr.querySelectorAll('.tbl-cell-input');
                        const rowVals = Array.from(cellInputs).map(c => c.value.trim());
                        if (rowVals.some(v => v !== '')) {
                            rows.push(rowVals);
                        }
                    });

                    if (tTitle || headers.some(h => h !== '') || rows.length > 0) {
                        const table = {
                            type: 'table',
                            title: tTitle,
                            headers: headers,
                            rows: rows
                        };
                        contentBlocksArr.push(table);
                        customTablesArr.push({ ...table, display_order: customTablesArr.length });
                    }
                });

                // Restore the actual DOM order for the interleaved flow.
                contentBlocksArr.length = 0;
                document.querySelectorAll('#csContentBlocksContainer > [data-block-type]').forEach(block => {
                    if (block.dataset.blockType === 'section') {
                        contentBlocksArr.push({
                            type: 'section',
                            title: block.querySelector('.sec-title').value.trim(),
                            content: block.querySelector('.sec-content').value.trim(),
                            image: block.querySelector('.sec-image').value.trim()
                        });
                    } else {
                        const headers = Array.from(block.querySelectorAll('.tbl-header-input')).map(input => input.value.trim());
                        const rows = Array.from(block.querySelectorAll('.tbl-row-item')).map(row => Array.from(row.querySelectorAll('.tbl-cell-input')).map(input => input.value.trim())).filter(row => row.some(value => value !== ''));
                        const title = block.querySelector('.tbl-title').value.trim();
                        if (title || headers.some(header => header !== '') || rows.length > 0) contentBlocksArr.push({ type: 'table', title, headers, rows });
                    }
                });

                // Gather comma separated strings to arrays
                const parseCSV = str => str.split(',').map(s => s.trim()).filter(Boolean);

                const mainTitle = document.getElementById('csTitle').value.trim();
                const cardTitle = document.getElementById('csCardTitle').value.trim();

                const payload = {
                    id: document.getElementById('csId').value,
                    title: mainTitle || cardTitle,
                    card_title: cardTitle,
                    slug: document.getElementById('csSlug').value,
                    category: document.getElementById('csCategory').value,
                    status: document.getElementById('csStatus').value,
                    display_order: parseInt(document.getElementById('csDisplayOrder').value || 0),
                    featured_image: document.getElementById('csFeaturedImage').value,
                    short_description: document.getElementById('csShortDesc').value,
                    detail_description: document.getElementById('csDetailDescription').value,
                    call_to_action_title: document.getElementById('csCtaTitle').value,
                    services: parseCSV(document.getElementById('csServicesList').value),
                    technologies: parseCSV(document.getElementById('csTechnologiesList').value),
                    tags: parseCSV(document.getElementById('csTagsList').value),
                    custom_sections: customSecsArr,
                    custom_tables: customTablesArr,
                    content_blocks: contentBlocksArr
                };

                try {
                    const res = await fetch('api.php?action=save_case_study', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.message || 'Case study saved successfully!');
                        closeModal('caseStudyModal');
                        loadCaseStudiesData(currentCSPage);
                    } else {
                        alert('Error: ' + (data.error || 'Failed to save case study.'));
                    }
                } catch (err) {
                    console.error('Error saving case study:', err);
                    alert('Error saving case study: ' + err.message);
                }
            });
        }

        async function toggleCSStatus(id) {
            try {
                const res = await fetch('api.php?action=toggle_case_study_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const data = await res.json();
                if (data.success) {
                    loadCaseStudiesData(currentCSPage);
                }
            } catch (err) {
                console.error('Error toggling status:', err);
            }
        }

        async function deleteCS(id) {
            if (!confirm('Are you sure you want to delete this Case Study permanently?')) return;
            try {
                const res = await fetch('api.php?action=delete_case_study', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const data = await res.json();
                if (data.success) {
                    loadCaseStudiesData(currentCSPage);
                }
            } catch (err) {
                console.error('Error deleting case study:', err);
            }
        }

        // ================= HEADER / NAVIGATION MENU CMS LOGIC =================
        let cachedNavItems = [];

        async function loadNavigationData() {
            try {
                const res = await fetch('api.php?action=get_navigation');
                const data = await res.json();
                if (!data.success) return;

                cachedNavItems = data.data;
                renderNavigationTable(data.data);
            } catch (err) {
                console.error('Error loading navigation data:', err);
            }
        }

        function renderNavigationTable(items) {
            const tbody = document.getElementById('navTableBody');
            if (!items || items.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-slate-500">No navigation items found. Click 'Add Navigation Item' to create one.</td></tr>`;
                return;
            }

            // Group by parent_id
            const tree = {};
            items.forEach(it => {
                const pid = it.parent_id !== null && it.parent_id !== '' ? parseInt(it.parent_id) : 0;
                if (!tree[pid]) tree[pid] = [];
                tree[pid].push(it);
            });

            let html = '';

            // Render top level
            const topList = tree[0] || [];
            topList.forEach(top => {
                const isTopActive = top.is_active == 1;
                html += `
                    <tr class="bg-slate-900/40 font-bold hover:bg-slate-500/10 transition-colors">
                        <td class="py-3.5 px-4 text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-folder-tree text-brandCyan text-xs"></i>
                            <span>${escapeHtml(top.label)}</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-[11px] text-brandCyan">${escapeHtml(top.url)}</td>
                        <td class="py-3.5 px-4"><span class="px-2 py-0.5 rounded-full bg-brandCyan/10 text-brandCyan text-[10px] font-extrabold uppercase">Top Level</span></td>
                        <td class="py-3.5 px-4 text-slate-300 font-mono">${top.display_order}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${isTopActive ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-500/10 text-slate-400'}">
                                ${isTopActive ? 'Active' : 'Hidden'}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1.5">
                            <button onclick="openNavItemModal(0, ${top.id})" class="px-2 py-1 rounded-lg bg-brandCyan/10 hover:bg-brandCyan/20 text-brandCyan text-[11px] font-bold" title="Add Sub-item under this parent">
                                <i class="fa-solid fa-plus mr-1"></i>Add Sub-Item
                            </button>
                            <button onclick="openNavItemModal(${top.id})" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="deleteNavItem(${top.id})" class="px-2 py-1 rounded-lg bg-red-950/80 hover:bg-red-900 text-red-300 text-[11px] font-bold" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                // Level 2 (Groups under top item)
                const groupList = tree[top.id] || [];
                groupList.forEach(grp => {
                    const isGrpActive = grp.is_active == 1;
                    html += `
                        <tr class="hover:bg-slate-500/5 transition-colors">
                            <td class="py-3 px-4 pl-10 text-slate-200 font-semibold flex items-center gap-2">
                                <span class="text-slate-600 font-mono">├──</span>
                                <i class="fa-solid fa-layer-group text-amber-400 text-xs"></i>
                                <span>${escapeHtml(grp.label)}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-400">${escapeHtml(grp.url)}</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-extrabold uppercase">Dropdown Group</span></td>
                            <td class="py-3 px-4 text-slate-300 font-mono">${grp.display_order}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${isGrpActive ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-500/10 text-slate-400'}">
                                    ${isGrpActive ? 'Active' : 'Hidden'}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-1.5">
                                <button onclick="openNavItemModal(0, ${grp.id})" class="px-2 py-1 rounded-lg bg-brandCyan/10 hover:bg-brandCyan/20 text-brandCyan text-[11px] font-bold" title="Add Child Link">
                                    <i class="fa-solid fa-plus mr-1"></i>Add Link
                                </button>
                                <button onclick="openNavItemModal(${grp.id})" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button onclick="deleteNavItem(${grp.id})" class="px-2 py-1 rounded-lg bg-red-950/80 hover:bg-red-900 text-red-300 text-[11px] font-bold" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    // Level 3 (Sub-links under group)
                    const subLinks = tree[grp.id] || [];
                    subLinks.forEach(sub => {
                        const isSubActive = sub.is_active == 1;
                        html += `
                            <tr class="hover:bg-slate-500/5 transition-colors text-slate-400">
                                <td class="py-2.5 px-4 pl-16 text-slate-300 flex items-center gap-2 text-[11px]">
                                    <span class="text-slate-700 font-mono">│   └──</span>
                                    <i class="fa-solid fa-link text-slate-500 text-[10px]"></i>
                                    <span>${escapeHtml(sub.label)}</span>
                                </td>
                                <td class="py-2.5 px-4 font-mono text-[10px] text-slate-500">${escapeHtml(sub.url)}</td>
                                <td class="py-2.5 px-4"><span class="px-2 py-0.5 rounded-full bg-slate-500/10 text-slate-400 text-[10px]">Sub-Service Link</span></td>
                                <td class="py-2.5 px-4 text-slate-400 font-mono">${sub.display_order}</td>
                                <td class="py-2.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${isSubActive ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-500/10 text-slate-400'}">
                                        ${isSubActive ? 'Active' : 'Hidden'}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 text-right space-x-1.5">
                                    <button onclick="openNavItemModal(${sub.id})" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button onclick="deleteNavItem(${sub.id})" class="px-2 py-1 rounded-lg bg-red-950/80 hover:bg-red-900 text-red-300 text-[11px] font-bold" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                });
            });

            tbody.innerHTML = html;
        }

        function openNavItemModal(id = 0, parentId = null) {
            document.getElementById('navItemModalTitle').textContent = id > 0 ? 'Edit Navigation Item' : 'Add Navigation Item';
            document.getElementById('navItemId').value = id;

            // Populate parent select dropdown with top-level and group items
            const parentSelect = document.getElementById('navItemParentId');
            parentSelect.innerHTML = '<option value="">[ Top-Level Menu Item (No Parent) ]</option>';
            cachedNavItems.forEach(it => {
                if (it.id != id) {
                    const prefix = it.parent_id ? '── ' : '● ';
                    parentSelect.innerHTML += `<option value="${it.id}">${prefix}${escapeHtml(it.label)} (${escapeHtml(it.url)})</option>`;
                }
            });

            if (id > 0) {
                const item = cachedNavItems.find(it => it.id == id);
                if (item) {
                    document.getElementById('navItemLabel').value = item.label || '';
                    document.getElementById('navItemUrl').value = item.url || '';
                    document.getElementById('navItemParentId').value = item.parent_id || '';
                    document.getElementById('navItemDisplayOrder').value = item.display_order || 0;
                    document.getElementById('navItemCssClass').value = item.css_class || '';
                    document.getElementById('navItemIsActive').checked = item.is_active == 1;
                    document.getElementById('navItemOpenInNewTab').checked = item.open_in_new_tab == 1;
                }
            } else {
                document.getElementById('navItemLabel').value = '';
                document.getElementById('navItemUrl').value = '';
                document.getElementById('navItemParentId').value = parentId !== null ? parentId : '';
                document.getElementById('navItemDisplayOrder').value = (cachedNavItems.length + 1);
                document.getElementById('navItemCssClass').value = '';
                document.getElementById('navItemIsActive').checked = true;
                document.getElementById('navItemOpenInNewTab').checked = false;
            }

            openModal('navItemModal');
        }

        document.getElementById('navItemForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('navItemId').value;
            const parentVal = document.getElementById('navItemParentId').value;

            const payload = {
                id: id,
                label: document.getElementById('navItemLabel').value,
                url: document.getElementById('navItemUrl').value,
                parent_id: parentVal !== '' ? parseInt(parentVal) : null,
                display_order: parseInt(document.getElementById('navItemDisplayOrder').value || 0),
                css_class: document.getElementById('navItemCssClass').value,
                is_active: document.getElementById('navItemIsActive').checked ? 1 : 0,
                open_in_new_tab: document.getElementById('navItemOpenInNewTab').checked ? 1 : 0
            };

            const res = await fetch('api.php?action=save_navigation_item', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                closeModal('navItemModal');
                loadNavigationData();
            } else {
                alert(data.error);
            }
        });

        async function deleteNavItem(id) {
            if (!confirm('Are you sure you want to delete this menu item and any nested child links under it?')) return;
            const res = await fetch('api.php?action=delete_navigation_item', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const data = await res.json();
            if (data.success) {
                loadNavigationData();
            } else {
                alert(data.error);
            }
        }

        const sidebar = document.getElementById('sidebar');
        document.getElementById('sidebarToggleBtn').addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });
        document.getElementById('sidebarCloseBtn').addEventListener('click', () => {
            sidebar.classList.add('hidden');
        });

        // Initialize Default View
        switchTab('overview');
    </script>
</body>
</html>
