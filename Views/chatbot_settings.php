<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Settings - Srishringarr Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Work+Sans:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #000;
            color: #fff;
            font-family: 'Work Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .dash-card {
            background: rgba(24, 24, 27, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        .input-dark {
            background: rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.1);
            color: #e5e2e1;
            transition: all 0.2s;
        }
        .input-dark:focus {
            border-color: #f47d31;
            box-shadow: 0 0 0 2px rgba(244,125,49,0.1);
            outline: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #f47d31 0%, #e06b22 100%);
            color: white;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(244,125,49,0.2);
        }
    </style>
</head>
<body class="flex min-h-screen overflow-hidden">
    
    <!-- Sidebar -->
    <?php include 'partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative z-0">
        <!-- Topbar -->
        <?php $pageTitle = 'Chatbot Settings'; include 'partials/topbar.php'; ?>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold font-['Manrope'] mb-2">AI Configuration</h2>
                    <p class="text-zinc-400 text-sm">Configure the AI model provider and API keys used for the assistant.</p>
                </div>

                <?php if ($success): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 flex items-center gap-3">
                    <span class="material-symbols-outlined text-green-500">check_circle</span>
                    <span class="text-green-400 text-sm font-medium">Settings saved successfully!</span>
                </div>
                <?php endif; ?>

                <form action="index.php?controller=chatbot&action=saveSettings" method="POST" class="space-y-6">
                    
                    <!-- Active Provider Selection -->
                    <div class="dash-card p-6 border-l-4 border-l-orange-500">
                        <h3 class="text-lg font-semibold font-['Manrope'] mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500 text-[20px]">smart_toy</span>
                            Active AI Model
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Groq Option -->
                            <label class="relative cursor-pointer">
                                <input type="radio" name="provider" value="groq" class="peer sr-only" <?php echo $provider === 'groq' ? 'checked' : ''; ?>>
                                <div class="p-4 rounded-xl border border-zinc-700 bg-zinc-900/50 hover:bg-zinc-800 transition-colors peer-checked:border-orange-500 peer-checked:bg-orange-500/5">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="font-semibold text-white">Groq</div>
                                        <div class="w-4 h-4 rounded-full border-2 border-zinc-600 peer-checked:border-orange-500 peer-checked:bg-orange-500 flex items-center justify-center">
                                            <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-zinc-400">Llama 3.3 70B (Recommended, Free & Fast)</div>
                                </div>
                            </label>

                            <!-- Gemini Option -->
                            <label class="relative cursor-pointer">
                                <input type="radio" name="provider" value="gemini" class="peer sr-only" <?php echo $provider === 'gemini' ? 'checked' : ''; ?>>
                                <div class="p-4 rounded-xl border border-zinc-700 bg-zinc-900/50 hover:bg-zinc-800 transition-colors peer-checked:border-orange-500 peer-checked:bg-orange-500/5">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="font-semibold text-white">Google Gemini</div>
                                        <div class="w-4 h-4 rounded-full border-2 border-zinc-600 peer-checked:border-orange-500 peer-checked:bg-orange-500 flex items-center justify-center">
                                            <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-zinc-400">Gemini 2.5 Flash</div>
                                </div>
                            </label>

                            <!-- OpenRouter Option -->
                            <label class="relative cursor-pointer">
                                <input type="radio" name="provider" value="openrouter" class="peer sr-only" <?php echo $provider === 'openrouter' ? 'checked' : ''; ?>>
                                <div class="p-4 rounded-xl border border-zinc-700 bg-zinc-900/50 hover:bg-zinc-800 transition-colors peer-checked:border-orange-500 peer-checked:bg-orange-500/5">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="font-semibold text-white">OpenRouter</div>
                                        <div class="w-4 h-4 rounded-full border-2 border-zinc-600 peer-checked:border-orange-500 peer-checked:bg-orange-500 flex items-center justify-center">
                                            <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-zinc-400">DeepSeek Chat V3 (Free tier available)</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- API Keys -->
                    <div class="dash-card p-6">
                        <h3 class="text-lg font-semibold font-['Manrope'] mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-zinc-400 text-[20px]">key</span>
                            API Keys
                        </h3>
                        
                        <div class="space-y-5">
                            <!-- Groq Key -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Groq API Key</label>
                                <input type="password" name="groq_key" value="<?php echo htmlspecialchars($groqKey); ?>" class="input-dark w-full px-4 py-3 rounded-lg text-sm font-mono" placeholder="gsk_...">
                                <p class="text-xs text-zinc-500 mt-1">Get your free key at <a href="https://console.groq.com/keys" target="_blank" class="text-orange-500 hover:underline">console.groq.com/keys</a></p>
                            </div>

                            <!-- Gemini Key -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">Google Gemini API Key</label>
                                <input type="password" name="gemini_key" value="<?php echo htmlspecialchars($geminiKey); ?>" class="input-dark w-full px-4 py-3 rounded-lg text-sm font-mono" placeholder="AIzaSy...">
                                <p class="text-xs text-zinc-500 mt-1">Get your free key at <a href="https://aistudio.google.com/apikey" target="_blank" class="text-orange-500 hover:underline">aistudio.google.com/apikey</a></p>
                            </div>

                            <!-- OpenRouter Key -->
                            <div>
                                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">OpenRouter API Key</label>
                                <input type="password" name="openrouter_key" value="<?php echo htmlspecialchars($openrouterKey); ?>" class="input-dark w-full px-4 py-3 rounded-lg text-sm font-mono" placeholder="sk-or-v1-...">
                                <p class="text-xs text-zinc-500 mt-1">Get your free key at <a href="https://openrouter.ai/keys" target="_blank" class="text-orange-500 hover:underline">openrouter.ai/keys</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Save Configuration
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <!-- Script for styling selected radio button (handled by CSS peer-checked in Tailwind) -->
</body>
</html>
