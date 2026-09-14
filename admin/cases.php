<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

$currentUser = requireAdminAuth();
$caseId = (int)($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Study Editor - Optimizers UAE Admin</title>
    
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
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .dark body {
            background-color: #0B0C10;
            color: #F8FAFC;
        }

        .glass-card {
            background: rgba(18, 18, 18, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-input {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }

        .glass-input:focus {
            border-color: #00E5FF;
            box-shadow: 0 0 10px rgba(0, 229, 255, 0.2);
            outline: none;
        }

        .tab-btn {
            @apply px-4 py-2.5 rounded-lg text-sm font-semibold transition-all text-slate-400 hover:text-white;
        }

        .tab-btn.active {
            @apply bg-brandCyan/20 text-brandCyan border border-brandCyan/40;
        }
    </style>
</head>
<body class="min-h-screen bg-brandDark">

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="index.php" class="text-brandCyan hover:text-brandCyanDark mb-2 inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span class="text-xs font-semibold">Back to Dashboard</span>
            </a>
            <h1 class="text-3xl font-extrabold text-white mt-2">
                <?= $caseId ? 'Edit Case Study' : 'Create New Case Study' ?>
            </h1>
        </div>
        <button onclick="saveCaseStudy()" class="px-6 py-2.5 bg-gradient-to-r from-brandRed to-red-600 hover:from-brandRedHover hover:to-red-500 text-white font-bold rounded-xl transition-all">
            <i class="fa-solid fa-save mr-2"></i>Save Case Study
        </button>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-2 mb-6 border-b border-slate-800/40 pb-4 overflow-x-auto">
        <button class="tab-btn active" data-tab="basic" onclick="switchTab('basic')">
            <i class="fa-solid fa-info-circle mr-2"></i>Basic Information
        </button>
        <button class="tab-btn" data-tab="content" onclick="switchTab('content')">
            <i class="fa-solid fa-paragraph mr-2"></i>Content & Sections
        </button>
        <button class="tab-btn" data-tab="statistics" onclick="switchTab('statistics')">
            <i class="fa-solid fa-chart-bar mr-2"></i>Statistics
        </button>
        <button class="tab-btn" data-tab="gallery" onclick="switchTab('gallery')">
            <i class="fa-solid fa-images mr-2"></i>Gallery
        </button>
        <button class="tab-btn" data-tab="tags" onclick="switchTab('tags')">
            <i class="fa-solid fa-tags mr-2"></i>Tags & Meta
        </button>
        <button class="tab-btn" data-tab="seo" onclick="switchTab('seo')">
            <i class="fa-solid fa-search mr-2"></i>SEO
        </button>
    </div>

    <!-- Tab Contents -->
    <!-- ===== BASIC INFORMATION TAB ===== -->
    <section id="tab-basic" class="tab-content space-y-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Case Study Title *</label>
                <input type="text" id="csTitle" placeholder="E.g., 45% Cost Reduction for Real Estate Agency" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Slug (URL)</label>
                <input type="text" id="csSlug" placeholder="Auto-generated from title" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Category *</label>
                <select id="csCategory" required class="w-full px-4 py-2.5 rounded-lg glass-input text-sm">
                    <option value="Digital Marketing Tips for UAE Business Owners">Digital Marketing Tips for UAE Business Owners</option>
                    <option value="Real Numbers. Real Clients. Real Growth.">Real Numbers. Real Clients. Real Growth.</option>
                </select>
            </div>
            <!-- <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Client Name</label>
                <input type="text" id="csClientName" placeholder="Client company name" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm">
            </div> -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Industry</label>
                <input type="text" id="csIndustry" placeholder="E.g., Real Estate" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">Short Description *</label>
            <textarea id="csShortDesc" placeholder="2-3 sentence summary visible on card..." class="w-full px-4 py-2.5 rounded-lg glass-input text-sm h-20 resize-none"></textarea>
        </div>

        <div class="glass-card rounded-lg p-4">
            <label class="block text-xs font-bold text-slate-300 mb-3">Featured Image</label>
            <div class="flex items-center gap-4">
                <div id="featuredImagePreview" class="w-32 h-32 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400">
                    <i class="fa-solid fa-image text-2xl"></i>
                </div>
                <div class="flex-1">
                    <input type="file" id="featuredImageUpload" accept="image/*" class="block w-full text-sm">
                    <button onclick="uploadFeaturedImage()" class="mt-2 px-4 py-2 bg-brandCyan/20 text-brandCyan hover:bg-brandCyan/30 font-semibold text-xs rounded-lg transition-all">
                        Upload Image
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Location</label>
                <input type="text" id="csLocation" placeholder="E.g., Dubai, UAE" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Project Date</label>
                <input type="text" id="csProjectDate" placeholder="E.g., Q1 2026" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Author</label>
                <input type="text" id="csAuthor" placeholder="Author name" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm" value="Optimizers Team">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="csPublished" class="w-4 h-4">
                <span class="text-sm font-semibold text-slate-300">Publish this case study</span>
            </label>
            <input type="number" id="csDisplayOrder" placeholder="Display Order" class="px-3 py-1.5 rounded-lg glass-input text-xs w-32" value="0">
        </div>
    </section>

    <!-- ===== CONTENT & SECTIONS TAB ===== -->
    <section id="tab-content" class="tab-content space-y-6 hidden">
        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">Full Description</label>
            <textarea id="csFullDesc" placeholder="Detailed background and context..." class="w-full px-4 py-2.5 rounded-lg glass-input text-sm h-24 resize-none"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">The Challenge</label>
                <textarea id="csChallenge" placeholder="What problem did the client face?" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm h-24 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Objectives</label>
                <textarea id="csObjectives" placeholder="Campaign goals and targets..." class="w-full px-4 py-2.5 rounded-lg glass-input text-sm h-24 resize-none"></textarea>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Strategy & Approach</label>
                <textarea id="csApproach" placeholder="How did you solve it?" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm h-24 resize-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Solution & Implementation</label>
                <textarea id="csSolution" placeholder="What tactics and tools were used?" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm h-24 resize-none"></textarea>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">Results</label>
            <textarea id="csResults" placeholder="Final outcomes and impact..." class="w-full px-4 py-2.5 rounded-lg glass-input text-sm h-24 resize-none"></textarea>
        </div>

        <div class="border-t border-slate-800 pt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-200">Custom Sections</h3>
                <button onclick="addSection()" class="px-3 py-1.5 bg-brandCyan/20 text-brandCyan hover:bg-brandCyan/30 text-xs font-bold rounded-lg transition-all">
                    <i class="fa-solid fa-plus mr-1"></i>Add Section
                </button>
            </div>
            <div id="sectionsContainer" class="space-y-4"></div>
        </div>
    </section>

    <!-- ===== STATISTICS TAB ===== -->
    <section id="tab-statistics" class="tab-content space-y-6 hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-200">Key Metrics & Statistics</h3>
            <button onclick="addStatistic()" class="px-3 py-1.5 bg-brandCyan/20 text-brandCyan hover:bg-brandCyan/30 text-xs font-bold rounded-lg transition-all">
                <i class="fa-solid fa-plus mr-1"></i>Add Statistic
            </button>
        </div>
        <div id="statisticsContainer" class="space-y-4"></div>
    </section>

    <!-- ===== GALLERY TAB ===== -->
    <section id="tab-gallery" class="tab-content space-y-6 hidden">
        <div class="glass-card rounded-lg p-4">
            <label class="block text-xs font-bold text-slate-300 mb-3">Upload Gallery Images</label>
            <input type="file" id="galleryUpload" multiple accept="image/*" class="block w-full text-sm mb-2">
            <button onclick="uploadGalleryImages()" class="px-4 py-2 bg-brandCyan/20 text-brandCyan hover:bg-brandCyan/30 font-semibold text-xs rounded-lg transition-all">
                Upload Images
            </button>
        </div>
        <div id="galleryContainer" class="grid grid-cols-4 gap-4"></div>
    </section>

    <!-- ===== TAGS & META TAB ===== -->
    <section id="tab-tags" class="tab-content space-y-6 hidden">
        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">Services Used (JSON array)</label>
            <textarea id="csServices" placeholder='["Google Ads", "Meta Ads"]' class="w-full px-4 py-2.5 rounded-lg glass-input text-sm font-mono h-20 resize-none"></textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">Technologies & Tools (JSON array)</label>
            <textarea id="csTechnologies" placeholder='["Google Analytics", "Meta Pixel"]' class="w-full px-4 py-2.5 rounded-lg glass-input text-sm font-mono h-20 resize-none"></textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">Tags/Keywords (JSON array)</label>
            <textarea id="csTags" placeholder='["Real Estate", "Lead Generation", "Dubai"]' class="w-full px-4 py-2.5 rounded-lg glass-input text-sm font-mono h-20 resize-none"></textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">Benefits (JSON array)</label>
            <textarea id="csBenefits" placeholder='["Reduced costs", "More leads"]' class="w-full px-4 py-2.5 rounded-lg glass-input text-sm font-mono h-20 resize-none"></textarea>
        </div>
    </section>

    <!-- ===== SEO TAB ===== -->
    <section id="tab-seo" class="tab-content space-y-6 hidden">
        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">CTA Title</label>
            <input type="text" id="csCTATitle" placeholder="Call to action headline" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 mb-2">CTA Description</label>
            <textarea id="csCTADesc" placeholder="Call to action description" class="w-full px-4 py-2.5 rounded-lg glass-input text-sm h-20 resize-none"></textarea>
        </div>
    </section>
</div>

<script>
    let caseStudyId = <?= $caseId ?>;
    let currentCaseData = {};

    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    }

    function addSection() {
        const index = document.querySelectorAll('[data-section-index]').length;
        const html = `
            <div class="glass-card rounded-lg p-4" data-section-index="${index}">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <input type="text" placeholder="Section Title" class="section-title px-3 py-2 rounded-lg glass-input text-sm">
                    <input type="number" placeholder="Order" value="0" class="section-order px-3 py-2 rounded-lg glass-input text-sm w-20">
                </div>
                <textarea placeholder="Section content..." class="section-content w-full px-4 py-2.5 rounded-lg glass-input text-sm h-20 resize-none mb-3"></textarea>
                <button onclick="this.closest('[data-section-index]').remove()" class="text-xs px-3 py-1.5 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30">
                    <i class="fa-solid fa-trash mr-1"></i>Delete
                </button>
            </div>
        `;
        document.getElementById('sectionsContainer').insertAdjacentHTML('beforeend', html);
    }

    function addStatistic() {
        const index = document.querySelectorAll('[data-stat-index]').length;
        const html = `
            <div class="glass-card rounded-lg p-4" data-stat-index="${index}">
                <div class="grid grid-cols-4 gap-3 mb-3">
                    <input type="text" placeholder="Label (e.g., Cost Per Lead)" class="stat-label px-3 py-2 rounded-lg glass-input text-sm">
                    <input type="text" placeholder="Before" class="stat-before px-3 py-2 rounded-lg glass-input text-sm">
                    <input type="text" placeholder="After" class="stat-after px-3 py-2 rounded-lg glass-input text-sm">
                    <input type="text" placeholder="Description" class="stat-desc px-3 py-2 rounded-lg glass-input text-sm">
                </div>
                <button onclick="this.closest('[data-stat-index]').remove()" class="text-xs px-3 py-1.5 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30">
                    <i class="fa-solid fa-trash mr-1"></i>Delete
                </button>
            </div>
        `;
        document.getElementById('statisticsContainer').insertAdjacentHTML('beforeend', html);
    }

    async function saveCaseStudy() {
        const parseInputArray = (val) => {
            if (!val || !val.trim()) return [];
            try {
                const parsed = JSON.parse(val);
                if (Array.isArray(parsed)) return parsed;
            } catch (e) {}
            return val.split(',').map(s => s.trim()).filter(Boolean);
        };

        const data = {
            id: caseStudyId || 0,
            title: document.getElementById('csTitle').value,
            slug: document.getElementById('csSlug').value,
            category: document.getElementById('csCategory').value,
            short_description: document.getElementById('csShortDesc').value,
            full_description: document.getElementById('csFullDesc').value,
            featured_image: document.getElementById('featuredImagePreview').dataset.url || '',
            author: document.getElementById('csAuthor').value,
            // client_name: document.getElementById('csClientName').value,
            industry: document.getElementById('csIndustry').value,
            location: document.getElementById('csLocation').value,
            project_date: document.getElementById('csProjectDate').value,
            challenge: document.getElementById('csChallenge').value,
            objectives: document.getElementById('csObjectives').value,
            approach: document.getElementById('csApproach').value,
            solution: document.getElementById('csSolution').value,
            results: document.getElementById('csResults').value,
            services: parseInputArray(document.getElementById('csServices').value),
            technologies: parseInputArray(document.getElementById('csTechnologies').value),
            tags: parseInputArray(document.getElementById('csTags').value),
            benefits: parseInputArray(document.getElementById('csBenefits').value),
            call_to_action_title: document.getElementById('csCTATitle').value,
            call_to_action_description: document.getElementById('csCTADesc').value,
            status: document.getElementById('csPublished').checked ? 'published' : 'draft',
            display_order: parseInt(document.getElementById('csDisplayOrder').value || 0)
        };

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'save_case_study', ...data })
            });
            const result = await res.json();
            if (result.success) {
                alert('Case study saved successfully!');
                caseStudyId = result.id;
            } else {
                alert('Error: ' + result.error);
            }
        } catch (err) {
            alert('Error saving case study: ' + err.message);
        }
    }

    // Load case study if editing
    if (caseStudyId) {
        fetch(`api.php?action=get_case_study&id=${caseStudyId}`)
            .then(r => r.json())
            .then(d => {
                if (d.success && d.data) {
                    const cs = d.data;
                    document.getElementById('csTitle').value = cs.title;
                    document.getElementById('csSlug').value = cs.slug;
                    document.getElementById('csCategory').value = cs.category;
                    document.getElementById('csShortDesc').value = cs.short_description;
                    document.getElementById('csFullDesc').value = cs.full_description || '';
                    // document.getElementById('csClientName').value = cs.client_name || '';
                    document.getElementById('csIndustry').value = cs.industry || '';
                    document.getElementById('csLocation').value = cs.location || '';
                    document.getElementById('csProjectDate').value = cs.project_date || '';
                    document.getElementById('csAuthor').value = cs.author || 'Optimizers Team';
                    document.getElementById('csChallenge').value = cs.challenge || '';
                    document.getElementById('csObjectives').value = cs.objectives || '';
                    document.getElementById('csApproach').value = cs.approach || '';
                    document.getElementById('csSolution').value = cs.solution || '';
                    document.getElementById('csResults').value = cs.results || '';
                    document.getElementById('csServices').value = JSON.stringify(JSON.parse(cs.services || '[]'), null, 2);
                    document.getElementById('csTechnologies').value = JSON.stringify(JSON.parse(cs.technologies || '[]'), null, 2);
                    document.getElementById('csTags').value = JSON.stringify(JSON.parse(cs.tags || '[]'), null, 2);
                    document.getElementById('csBenefits').value = JSON.stringify(JSON.parse(cs.benefits || '[]'), null, 2);
                    document.getElementById('csCTATitle').value = cs.call_to_action_title || '';
                    document.getElementById('csCTADesc').value = cs.call_to_action_description || '';
                    document.getElementById('csPublished').checked = cs.status === 'published';
                    document.getElementById('csDisplayOrder').value = cs.display_order || 0;

                    if (cs.featured_image) {
                        document.getElementById('featuredImagePreview').innerHTML = `<img src="../${cs.featured_image}" class="w-full h-full object-cover rounded-lg">`;
                        document.getElementById('featuredImagePreview').dataset.url = cs.featured_image;
                    }
                }
            });
    }

    function uploadFeaturedImage() {
        const file = document.getElementById('featuredImageUpload').files[0];
        if (!file) return alert('Please select an image');
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('action', 'upload_case_study_image');

        fetch('api.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.getElementById('featuredImagePreview').innerHTML = `<img src="../${d.url}" class="w-full h-full object-cover rounded-lg">`;
                    document.getElementById('featuredImagePreview').dataset.url = d.url;
                } else {
                    alert('Upload failed: ' + d.error);
                }
            });
    }
</script>

</body>
</html>
