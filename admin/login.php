<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (getLoggedInUser()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Optimizers UAE</title>
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
            background-color: #0B0C10;
        }
        .glow-red {
            box-shadow: 0 0 25px rgba(229, 9, 20, 0.35);
        }
        .glow-cyan {
            box-shadow: 0 0 25px rgba(0, 229, 255, 0.25);
        }
        .bg-grid {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 bg-grid flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient background glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-brandRed/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-brandCyan/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-brandRed/80 to-brandCyan/80 p-0.5 mb-4 shadow-xl">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <img src="<?= optimizers_url('assets/images/optimizers-uae-animated-logo.svg') ?>" alt="Optimizers UAE" class="w-10 h-10 object-contain" onerror="this.onerror=null; this.src='<?= optimizers_url('assets/icons/brand/google.svg') ?>';">
                </div>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Optimizers UAE</h1>
            <p class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-widest">Admin Control Portal</p>
        </div>

        <!-- Login Glass Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800/90 rounded-3xl p-8 shadow-2xl">
            <div class="mb-6 text-center">
                <h2 class="text-lg font-bold text-white">Sign In to Dashboard</h2>
                <p class="text-xs text-slate-400 mt-1">Manage inquiries, services & UAE footprint</p>
            </div>

            <!-- Demo Banner -->
            <div class="mb-6 p-3 flex items-center gap-3">
                <!-- <i class="fa-solid fa-circle-info text-cyan-400 text-base flex-shrink-0"></i> -->
                <!-- <div>
                    <span class="font-bold">Default Super Admin:</span><br>
                    <code class="bg-slate-900/80 px-1.5 py-0.5 rounded text-cyan-200">admin@optimizers.ae</code> / <code class="bg-slate-900/80 px-1.5 py-0.5 rounded text-cyan-200">admin123</code>
                </div> -->
            </div>

            <div id="errorAlert" class="hidden mb-6 p-3 rounded-xl bg-red-950/60 border border-red-500/40 text-red-200 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-red-400 text-base"></i>
                <span id="errorMessage">Invalid credentials</span>
            </div>

            <form id="loginForm" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Corporate Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </span>
                        <input type="email" id="email" value="admin@optimizers.ae" required
                            class="w-full pl-10 pr-4 py-3 bg-slate-950/70 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-brandCyan focus:ring-1 focus:ring-brandCyan transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="password" value="admin123" required
                            class="w-full pl-10 pr-10 py-3 bg-slate-950/70 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-brandCyan focus:ring-1 focus:ring-brandCyan transition-all">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white">
                            <i class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked class="rounded border-slate-700 bg-slate-900 text-brandRed focus:ring-0">
                        <span>Remember session</span>
                    </label>
                    <a href="#" onclick="alert('Contact system administrator to reset password.'); return false;" class="hover:text-brandCyan transition-colors">Forgot password?</a>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-brandRed to-red-600 hover:from-brandRedHover hover:to-red-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-red-900/30 transition-all flex items-center justify-center gap-2">
                    <span>Sign In to Admin</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        <div class="text-center mt-6 text-xs text-slate-500">
            &copy; <?= date('Y') ?> Optimizers UAE. All rights reserved. JVC · Dubai.
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        const submitBtn = document.getElementById('submitBtn');
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            togglePassword.innerHTML = `<i class="fa-solid fa-${isPassword ? 'eye-slash' : 'eye'} text-sm"></i>`;
        });

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorAlert.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-sm"></i><span>Authenticating...</span>`;

            try {
                const res = await fetch('api.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: document.getElementById('email').value.trim(),
                        password: passwordInput.value
                    })
                });

                const data = await res.json();

                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    errorMessage.textContent = data.error || 'Login failed. Please check credentials.';
                    errorAlert.classList.remove('hidden');
                }
            } catch (err) {
                errorMessage.textContent = 'Server connection error. Please try again.';
                errorAlert.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<span>Sign In to Admin</span><i class="fa-solid fa-arrow-right text-xs"></i>`;
            }
        });
    </script>
</body>
</html>
