<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <title>AI Playground - Srishringarr Studio</title>
    <?php include __DIR__ . '/../partials/head.php'; ?>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>
</head>
<body class="bg-[#0a0a0a] font-sans text-zinc-300 text-sm antialiased selection:bg-pink-500/30">
    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0 relative">
            <?php include __DIR__ . '/../partials/topbar.php'; ?>
            
            <main class="flex-1 overflow-y-auto relative bg-[#0a0a0a]">
                
                <div class="p-4 sm:p-6 grid grid-cols-1 xl:grid-cols-12 gap-6 h-[calc(100vh-4rem)]">
                   
                   <!-- LEFT COLUMN: CONTROLS -->
                   <div class="xl:col-span-4 flex flex-col gap-4 overflow-y-auto custom-scrollbar pr-2">
                      
                      <!-- Header -->
                      <div>
                          <h1 class="text-xl font-bold text-white mb-1">AI Playground</h1>
                          <p class="text-[11px] text-zinc-500">Upload multiple angles to generate names, descriptions, and models.</p>
                      </div>

                      <!-- Image Dropzone -->
                      <div class="mb-2">
                          <input type="file" id="hidden_file_input" accept="image/*" multiple style="display:none;">
                          <button type="button" id="file_drop_zone" onclick="document.getElementById('hidden_file_input').click()" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-[0_0_15px_rgba(79,70,229,0.4)] transition-all flex items-center justify-center gap-2">
                              <i class="fas fa-cloud-upload-alt text-xl"></i>
                              <span>UPLOAD PRODUCT IMAGES</span>
                          </button>

                          <div id="preview_gallery" class="hidden flex-col bg-[#111] border border-indigo-500/30 rounded-xl mt-3 overflow-hidden">
                             <div id="gallery_grid" class="grid grid-cols-3 gap-2 p-3 bg-black/40 max-h-40 overflow-y-auto custom-scrollbar">
                                 <!-- Thumbnails go here -->
                             </div>
                             <div class="flex items-center justify-between p-3 border-t border-indigo-500/20 bg-[#111]">
                                 <button type="button" onclick="document.getElementById('hidden_file_input').click()" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 flex items-center">
                                     <i class="fas fa-plus mr-1"></i> ADD MORE
                                 </button>
                                 <button type="button" onclick="resetUpload()" class="text-[10px] font-bold text-red-400 hover:text-red-300">
                                     CLEAR ALL
                                 </button>
                             </div>
                          </div>
                      </div>
                      
                      <!-- Context Settings -->
                      <div class="bg-[#111] border border-white/5 rounded-xl p-4">
                         <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 mb-3"><i class="fas fa-sliders-h mr-1.5"></i> Context</h4>
                         <div class="space-y-3">
                             <div>
                                <input type="text" id="pg_size" class="w-full bg-[#050505] border border-white/5 rounded-md px-3 py-2 text-xs focus:border-purple-500 outline-none text-white placeholder-zinc-700" placeholder="Sizes: S, M, L or Custom">
                             </div>
                             <div>
                                <textarea id="pg_product_info" rows="2" class="w-full bg-[#050505] border border-white/5 rounded-md px-3 py-2 text-xs focus:border-purple-500 outline-none text-white placeholder-zinc-700 resize-none" placeholder="Details: Red pure silk lehenga..."></textarea>
                             </div>
                         </div>
                      </div>

                      <!-- Action Generators -->
                      <div class="grid grid-cols-2 gap-3 pb-6">
                          <button id="btn_names" onclick="pgGenerateNames()" class="bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/20 text-indigo-400 rounded-lg p-3 flex flex-col items-center justify-center gap-1.5 transition-colors relative">
                              <i class="fas fa-tag" id="icon_names"></i>
                              <i class="fas fa-spinner fa-spin hidden absolute" id="spinner_names"></i>
                              <span class="text-[10px] font-bold" id="text_names">5 Names</span>
                          </button>
                          
                          <button id="btn_desc" onclick="pgGenerateDescription()" class="bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/20 text-purple-400 rounded-lg p-3 flex flex-col items-center justify-center gap-1.5 transition-colors relative">
                              <i class="fas fa-align-left" id="icon_desc"></i>
                              <i class="fas fa-spinner fa-spin hidden absolute" id="spinner_desc"></i>
                              <span class="text-[10px] font-bold" id="text_desc">Description</span>
                          </button>

                          <div class="col-span-2 bg-[#111] border border-white/5 rounded-lg p-3 mt-1">
                              <div class="flex items-center justify-between mb-2">
                                  <span class="text-[10px] font-bold text-pink-400 uppercase tracking-wider"><i class="fas fa-camera mr-1"></i> AI Models</span>
                                  <select id="pgImageCount" class="bg-[#050505] border border-white/5 rounded px-2 py-1 text-[10px] text-zinc-300 outline-none">
                                      <option value="1">1 Image</option>
                                      <option value="2">2 Images</option>
                                      <option value="4">4 Images</option>
                                  </select>
                              </div>
                              <input type="text" id="pgImagePrompt" class="w-full bg-[#050505] border border-white/5 rounded px-3 py-2 text-[10px] focus:border-pink-500 outline-none text-white placeholder-zinc-700 mb-2" placeholder="Custom art direction...">
                              <button id="btn_img" onclick="pgGenerateImages()" class="w-full py-2 bg-pink-500/20 hover:bg-pink-500/30 border border-pink-500/30 text-pink-400 font-bold text-[10px] rounded transition-colors flex items-center justify-center gap-2">
                                  <i class="fas fa-image" id="icon_img"></i>
                                  <i class="fas fa-spinner fa-spin hidden" id="spinner_img"></i>
                                  <span id="text_img">Generate Photoshoot</span>
                              </button>
                          </div>

                          <div class="col-span-2 bg-[#111] border border-white/5 rounded-lg p-3 mt-1">
                              <div class="flex items-center justify-between mb-2">
                                  <span class="text-[10px] font-bold text-teal-400 uppercase tracking-wider"><i class="fas fa-video mr-1"></i> Video Showcase</span>
                              </div>
                              <input type="text" id="pgVideoPrompt" class="w-full bg-[#050505] border border-white/5 rounded px-3 py-2 text-[10px] focus:border-teal-500 outline-none text-white placeholder-zinc-700 mb-2" placeholder="Custom video direction...">
                              <button id="btn_vid" onclick="pgGenerateVideo()" class="w-full py-2 bg-teal-500/20 hover:bg-teal-500/30 border border-teal-500/30 text-teal-400 font-bold text-[10px] rounded transition-colors flex items-center justify-center gap-2">
                                  <i class="fas fa-film" id="icon_vid"></i>
                                  <i class="fas fa-spinner fa-spin hidden" id="spinner_vid"></i>
                                  <span id="text_vid">Generate Video (8s)</span>
                              </button>
                          </div>
                      </div>
                   </div>

                   <!-- RIGHT COLUMN: RESULTS -->
                   <div class="xl:col-span-8 bg-[#111] border border-white/5 rounded-xl p-5 flex flex-col relative h-full">
                      
                      <div class="flex items-center justify-between mb-4 border-b border-white/5 pb-3 shrink-0">
                          <h3 class="text-xs font-bold text-white uppercase tracking-wider"><i class="fas fa-magic text-yellow-500 mr-2"></i> Canvas</h3>
                          <button onclick="clearResults()" class="text-[10px] text-zinc-500 hover:text-white transition-colors">Clear All</button>
                      </div>

                      <div id="results_placeholder" class="flex-1 flex flex-col items-center justify-center text-center opacity-40">
                          <i class="fas fa-wand-magic-sparkles text-3xl text-zinc-500 mb-3"></i>
                          <p class="text-xs text-zinc-400">Results will appear here.</p>
                      </div>

                      <div id="results_container" class="hidden flex-col gap-4 overflow-y-auto custom-scrollbar pr-2 pb-2">
                          
                          <div id="res_names_block" class="hidden">
                             <h4 class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider mb-2"><i class="fas fa-tag mr-1"></i> Names</h4>
                             <div id="res_names_grid" class="grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
                          </div>

                          <div id="res_desc_block" class="hidden">
                             <div class="flex items-center justify-between mb-2">
                                 <h4 class="text-[10px] font-bold text-purple-400 uppercase tracking-wider"><i class="fas fa-align-left mr-1"></i> Description</h4>
                                 <button onclick="copyDesc()" id="copy_desc_btn" class="text-[10px] text-zinc-400 hover:text-white transition-colors"><i class="fas fa-copy mr-1"></i> Copy</button>
                             </div>
                             <textarea id="res_desc_text" rows="7" class="w-full bg-[#050505] border border-white/5 rounded-md p-3 text-xs text-zinc-300 outline-none resize-none leading-relaxed" readonly></textarea>
                          </div>

                          <div id="res_img_block" class="hidden">
                             <h4 class="text-[10px] font-bold text-pink-400 uppercase tracking-wider mb-2"><i class="fas fa-camera mr-1"></i> Photoshoot</h4>
                             <div id="res_img_grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                          </div>

                          <div id="res_vid_block" class="hidden">
                             <h4 class="text-[10px] font-bold text-teal-400 uppercase tracking-wider mb-2"><i class="fas fa-video mr-1"></i> Video Showcase</h4>
                             <div id="res_vid_container" class="w-full max-w-sm rounded-lg overflow-hidden border border-white/5 bg-black">
                                 <!-- Video player will be injected here -->
                             </div>
                          </div>

                      </div>
                   </div>

                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/scripts.php'; ?>
    <script>
        const sessionId = "<?php echo htmlspecialchars($session_id ?? ''); ?>";
        let currentBase64Array = [];

        document.getElementById('hidden_file_input').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length === 0) return;

            document.getElementById('file_drop_zone').classList.add('hidden');
            document.getElementById('preview_gallery').classList.remove('hidden');
            document.getElementById('preview_gallery').classList.add('flex');

            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const b64 = ev.target.result;
                    currentBase64Array.push(b64);
                    renderThumbnails();
                };
                reader.readAsDataURL(file);
            });
            
            this.value = '';
        });

        function renderThumbnails() {
            const container = document.getElementById('gallery_grid');
            container.innerHTML = currentBase64Array.map((b64, idx) => `
                <div class="relative group aspect-square rounded overflow-hidden bg-[#050505] border border-white/5">
                    <img src="${b64}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                    <button onclick="removeImage(${idx})" class="absolute top-1 right-1 bg-red-500/80 text-white w-4 h-4 rounded flex items-center justify-center text-[8px] opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-[8px] text-center py-0.5 text-zinc-400">${idx+1}</div>
                </div>
            `).join('');
            
            if(currentBase64Array.length === 0) {
                resetUpload();
            }
        }

        function removeImage(idx) {
            currentBase64Array.splice(idx, 1);
            renderThumbnails();
        }

        function resetUpload() {
            currentBase64Array = [];
            document.getElementById('preview_gallery').classList.add('hidden');
            document.getElementById('preview_gallery').classList.remove('flex');
            document.getElementById('file_drop_zone').classList.remove('hidden');
            document.getElementById('hidden_file_input').value = '';
        }

        function initCanvas() {
            document.getElementById('results_placeholder').classList.add('hidden');
            document.getElementById('results_container').classList.remove('hidden');
            document.getElementById('results_container').classList.add('flex');
        }

        function clearResults() {
            document.getElementById('results_container').classList.add('hidden');
            document.getElementById('results_container').classList.remove('flex');
            document.getElementById('results_placeholder').classList.remove('hidden');
            
            document.getElementById('res_names_block').classList.add('hidden');
            document.getElementById('res_desc_block').classList.add('hidden');
            document.getElementById('res_img_block').classList.add('hidden');
            document.getElementById('res_vid_block').classList.add('hidden');
            
            document.getElementById('res_names_grid').innerHTML = '';
            document.getElementById('res_desc_text').value = '';
            document.getElementById('res_img_grid').innerHTML = '';
            document.getElementById('res_vid_container').innerHTML = '';
        }

        function getContextData() {
            return {
                images: currentBase64Array,
                size: document.getElementById('pg_size').value.trim(),
                product_info: document.getElementById('pg_product_info').value.trim(),
                session_id: sessionId
            };
        }

        function toggleBtnLoading(prefix, isLoading) {
            const btn = document.getElementById('btn_' + prefix);
            const icon = document.getElementById('icon_' + prefix);
            const spinner = document.getElementById('spinner_' + prefix);
            const text = document.getElementById('text_' + prefix);
            
            btn.disabled = isLoading;
            if (isLoading) {
                if(icon) icon.classList.add('hidden');
                if(spinner) spinner.classList.remove('hidden');
                if(text) text.classList.add('opacity-50');
            } else {
                if(icon) icon.classList.remove('hidden');
                if(spinner) spinner.classList.add('hidden');
                if(text) text.classList.remove('opacity-50');
            }
        }

        async function pgGenerateNames() {
            if (currentBase64Array.length === 0) return alert('Please upload at least one image.');
            
            initCanvas();
            toggleBtnLoading('names', true);

            try {
                const response = await fetch('index.php?controller=aiPlayground&action=generateNames', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(getContextData())
                });
                const data = await response.json();
                
                if (data.success && data.names) {
                    const grid = document.getElementById('res_names_grid');
                    grid.innerHTML = data.names.map(name => `
                        <div class="bg-[#050505] border border-white/5 p-2 rounded-md text-[11px] text-zinc-300 hover:border-indigo-500/30 transition-colors cursor-pointer flex justify-between items-center group" onclick="copyText(this, \`${name.replace(/'/g, "\\'")}\`)">
                            <span class="pr-2 break-words shrink">${name}</span>
                            <i class="fas fa-copy text-zinc-600 group-hover:text-indigo-400 shrink-0"></i>
                        </div>
                    `).join('');
                    document.getElementById('res_names_block').classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate names'));
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred.');
            } finally {
                toggleBtnLoading('names', false);
            }
        }

        async function pgGenerateDescription() {
            if (currentBase64Array.length === 0) return alert('Please upload at least one image.');
            
            initCanvas();
            toggleBtnLoading('desc', true);
            
            const contextData = getContextData();
            contextData.max_words = 200;

            try {
                const response = await fetch('index.php?controller=aiPlayground&action=generateDescription', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(contextData)
                });
                const data = await response.json();
                
                if (data.success && data.description) {
                    document.getElementById('res_desc_text').value = data.description;
                    document.getElementById('res_desc_block').classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate description'));
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred.');
            } finally {
                toggleBtnLoading('desc', false);
            }
        }

        async function pgGenerateImages() {
            if (currentBase64Array.length === 0) return alert('Please upload at least one image.');
            
            initCanvas();
            toggleBtnLoading('img', true);
            
            const contextData = getContextData();
            contextData.count = document.getElementById('pgImageCount').value;
            contextData.prompt = document.getElementById('pgImagePrompt').value.trim();

            try {
                const response = await fetch('index.php?controller=aiPlayground&action=generateImages', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(contextData)
                });
                const data = await response.json();
                
                if (data.success && data.images) {
                    const grid = document.getElementById('res_img_grid');
                    grid.innerHTML = data.images.map(url => `
                        <div class="relative group rounded-xl overflow-hidden aspect-[3/4] bg-black border border-white/5">
                            <img src="${url}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                <a href="${url}" download="ai_generated_image.jpg" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white backdrop-blur-sm transition-colors">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    `).join('');
                    document.getElementById('res_img_block').classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'Failed to generate images'));
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred.');
            } finally {
                toggleBtnLoading('img', false);
            }
        }

        async function pgGenerateVideo() {
            if (currentBase64Array.length === 0) return alert('Please upload at least one image.');
            
            initCanvas();
            toggleBtnLoading('vid', true);
            const textVid = document.getElementById('text_vid');
            textVid.innerText = "Starting...";

            try {
                const response = await fetch('index.php?controller=aiPlayground&action=generateVideoStart', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        images: currentBase64Array,
                        prompt: document.getElementById('pgVideoPrompt').value
                    })
                });
                const data = await response.json();
                
                if (data.success && data.operation_name) {
                    textVid.innerText = "Generating... Please wait (est. 1-2 mins)";
                    pollVideoStatus(data.operation_name);
                } else {
                    alert('Error: ' + (data.error || 'Failed to start video generation'));
                    toggleBtnLoading('vid', false);
                    textVid.innerText = "Generate Video (8s)";
                }
            } catch (err) {
                console.error(err);
                alert('A network error occurred.');
                toggleBtnLoading('vid', false);
                textVid.innerText = "Generate Video (8s)";
            }
        }

        function pollVideoStatus(operationName) {
            const pollInterval = setInterval(async () => {
                try {
                    const response = await fetch('index.php?controller=aiPlayground&action=generateVideoStatus', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            operation_name: operationName,
                            size: document.getElementById('pg_size').value.trim(),
                            product_info: document.getElementById('pg_product_info').value.trim(),
                            session_id: sessionId
                        })
                    });
                    const data = await response.json();
                    
                    if (data.error) {
                        clearInterval(pollInterval);
                        alert('Error: ' + data.error);
                        toggleBtnLoading('vid', false);
                        document.getElementById('text_vid').innerText = "Generate Video (8s)";
                        return;
                    }

                    if (data.success && data.done) {
                        clearInterval(pollInterval);
                        
                        const vidContainer = document.getElementById('res_vid_container');
                        vidContainer.innerHTML = `
                            <video src="${data.video_url}" controls autoplay loop class="w-full h-auto aspect-[9/16] object-cover"></video>
                            <div class="p-3 bg-black flex justify-between items-center border-t border-white/5">
                                <span class="text-xs text-zinc-400">Veo 3.1</span>
                                <a href="${data.video_url}" download="ai_generated_video.mp4" class="text-xs text-teal-400 hover:text-teal-300 font-bold"><i class="fas fa-download mr-1"></i> Download</a>
                            </div>
                        `;
                        document.getElementById('res_vid_block').classList.remove('hidden');
                        
                        toggleBtnLoading('vid', false);
                        document.getElementById('text_vid').innerText = "Generate Video (8s)";
                    }
                } catch (err) {
                    console.error(err);
                    clearInterval(pollInterval);
                    alert('Polling error occurred.');
                    toggleBtnLoading('vid', false);
                    document.getElementById('text_vid').innerText = "Generate Video (8s)";
                }
            }, 10000); // poll every 10 seconds
        }

        function copyText(el, text) {
            navigator.clipboard.writeText(text);
            const icon = el.querySelector('i');
            icon.className = 'fas fa-check text-green-400';
            setTimeout(() => { icon.className = 'fas fa-copy text-zinc-600 group-hover:text-indigo-400'; }, 2000);
        }

        function copyDesc() {
            const text = document.getElementById('res_desc_text').value;
            navigator.clipboard.writeText(text);
            const btn = document.getElementById('copy_desc_btn');
            btn.innerHTML = '<i class="fas fa-check text-green-400 mr-1"></i> Copied';
            setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy mr-1"></i> Copy'; }, 2000);
        }
    </script>
</body>
</html>
