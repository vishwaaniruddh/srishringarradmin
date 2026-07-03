<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Srishringarr Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Work+Sans:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #000;
            font-family: 'Work Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        /* Animated gradient background */
        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 10% 90%, rgba(244,125,49,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 90% 20%, rgba(233,195,73,0.06) 0%, transparent 50%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(58,223,171,0.03) 0%, transparent 50%),
                #000;
        }

        /* Subtle grid pattern */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 1;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            animation: float 8s ease-in-out infinite;
        }
        .particle:nth-child(1) {
            width: 4px; height: 4px;
            background: #f47d31;
            top: 20%; left: 15%;
            animation-delay: 0s;
            animation-duration: 7s;
        }
        .particle:nth-child(2) {
            width: 3px; height: 3px;
            background: #e9c349;
            top: 60%; left: 80%;
            animation-delay: 2s;
            animation-duration: 9s;
        }
        .particle:nth-child(3) {
            width: 5px; height: 5px;
            background: #3adfab;
            top: 80%; left: 40%;
            animation-delay: 4s;
            animation-duration: 6s;
        }
        .particle:nth-child(4) {
            width: 3px; height: 3px;
            background: #ffb68f;
            top: 30%; left: 70%;
            animation-delay: 1s;
            animation-duration: 10s;
        }

        @keyframes float {
            0%, 100% { opacity: 0; transform: translateY(0px) scale(1); }
            25% { opacity: 0.6; }
            50% { opacity: 0.8; transform: translateY(-30px) scale(1.5); }
            75% { opacity: 0.4; }
        }

        /* Login card */
        .login-card {
            background: rgba(18,18,18,0.85);
            border: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.03),
                0 20px 50px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.05);
        }

        /* Input styling */
        .login-input {
            background: rgba(0,0,0,0.6);
            border: 1px solid rgba(255,255,255,0.08);
            color: #e5e2e1;
            font-family: 'Work Sans', sans-serif;
            font-size: 14px;
            padding: 12px 16px 12px 44px;
            border-radius: 10px;
            width: 100%;
            outline: none;
            transition: all 0.2s ease;
        }
        .login-input::placeholder {
            color: rgba(255,255,255,0.25);
        }
        .login-input:focus {
            border-color: #f47d31;
            box-shadow: 0 0 0 3px rgba(244,125,49,0.1), inset 0 0 20px rgba(244,125,49,0.03);
        }

        /* Submit button */
        .login-btn {
            background: linear-gradient(135deg, #f47d31 0%, #e06b22 100%);
            color: #fff;
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
            font-size: 14px;
            padding: 13px 24px;
            border-radius: 10px;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.02em;
        }
        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(244,125,49,0.3);
        }
        .login-btn:active {
            transform: translateY(0px);
            box-shadow: 0 2px 8px rgba(244,125,49,0.2);
        }
        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Shimmer on button */
        .login-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: 0.5s;
        }
        .login-btn:hover::after {
            left: 100%;
        }

        /* Error shake */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
        .shake {
            animation: shake 0.4s ease-in-out;
        }

        /* Material icon alignment */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }

        /* Password toggle */
        .pass-toggle {
            cursor: pointer;
            color: rgba(255,255,255,0.3);
            transition: color 0.2s;
        }
        .pass-toggle:hover {
            color: rgba(255,255,255,0.6);
        }

        /* Fade in animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .fade-in-up-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .fade-in-up-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .fade-in-up-delay-3 { animation-delay: 0.3s; opacity: 0; }
        .fade-in-up-delay-4 { animation-delay: 0.4s; opacity: 0; }
    </style>
</head>
<body>
    <!-- Background layers -->
    <div class="bg-mesh"></div>
    <div class="bg-grid"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <!-- Login Container -->
    <div class="relative z-10 min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-[400px]">

            <!-- Logo & Branding -->
            <div class="text-center mb-8 fade-in-up">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-orange-500/20 to-orange-600/10 border border-orange-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]" style="color: #f47d31;">diamond</span>
                </div>
                <h1 class="text-[22px] font-extrabold text-white tracking-tight" style="font-family: 'Manrope', sans-serif;">Srishringarr</h1>
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] mt-1" style="color: #f47d31;">Luxury Admin Panel</p>
            </div>

            <!-- Login Card -->
            <div class="login-card p-8 fade-in-up fade-in-up-delay-1" id="login-card">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-white" style="font-family: 'Manrope', sans-serif;">Welcome back</h2>
                    <p class="text-sm text-zinc-500 mt-1">Sign in to your admin account</p>
                </div>

                <!-- Error Message -->
                <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center gap-2" id="error-box">
                    <span class="material-symbols-outlined text-[18px] text-red-500">error</span>
                    <span class="text-sm text-red-400"><?php echo htmlspecialchars($error); ?></span>
                </div>
                <?php endif; ?>

                <form method="POST" action="index.php?controller=auth&action=login" id="login-form" autocomplete="off">
                    <!-- Username -->
                    <div class="mb-4 fade-in-up fade-in-up-delay-2">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2">Username</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-zinc-600">person</span>
                            <input
                                type="text"
                                name="username"
                                class="login-input"
                                placeholder="Enter your username"
                                value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-6 fade-in-up fade-in-up-delay-3">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-2">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-zinc-600">lock</span>
                            <input
                                type="password"
                                name="password"
                                id="password-input"
                                class="login-input"
                                placeholder="Enter your password"
                                required
                            >
                            <span class="material-symbols-outlined pass-toggle absolute right-3.5 top-1/2 -translate-y-1/2 text-[18px]" id="toggle-password">visibility_off</span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="fade-in-up fade-in-up-delay-4">
                        <button type="submit" class="login-btn" id="login-btn">
                            <span id="btn-text">Sign In</span>
                            <span id="btn-loader" class="hidden">
                                <svg class="animate-spin inline-block w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Signing in...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 fade-in-up fade-in-up-delay-4">
                <p class="text-[11px] text-zinc-600">
                    Protected admin panel &bull; Srishringarr &copy; <?php echo date('Y'); ?>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password-input');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                togglePassword.textContent = isPassword ? 'visibility' : 'visibility_off';
            });
        }

        // Form submit loading state
        const loginForm = document.getElementById('login-form');
        const loginBtn = document.getElementById('login-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoader = document.getElementById('btn-loader');

        if (loginForm) {
            loginForm.addEventListener('submit', () => {
                if (btnText && btnLoader && loginBtn) {
                    btnText.classList.add('hidden');
                    btnLoader.classList.remove('hidden');
                    loginBtn.disabled = true;
                }
            });
        }

        // Shake animation on error
        const errorBox = document.getElementById('error-box');
        const loginCard = document.getElementById('login-card');
        if (errorBox && loginCard) {
            loginCard.classList.add('shake');
            setTimeout(() => loginCard.classList.remove('shake'), 500);
        }
    </script>
</body>
</html>
