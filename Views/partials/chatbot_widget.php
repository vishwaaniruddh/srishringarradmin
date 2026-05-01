<!-- Chatbot CSS -->
<link rel="stylesheet" href="assets/chatbot.css">

<!-- Floating Action Button -->
<button class="chatbot-fab" id="chatbot-fab" title="Chat with Shri AI">
    <svg class="icon-chat" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/>
        <path d="M7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"/>
    </svg>
    <svg class="icon-close" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
    </svg>
</button>

<!-- Chat Window -->
<div class="chatbot-window" id="chatbot-window">
    <!-- Header -->
    <div class="chatbot-header">
        <div class="chatbot-avatar">🤖</div>
        <div class="chatbot-header-info">
            <div class="chatbot-header-name">Shri AI</div>
            <div class="chatbot-header-status">
                <span class="dot"></span>
                <span id="chatbot-status-text">Online • Srishringarr Assistant</span>
            </div>
        </div>
        <button class="chatbot-header-close" id="chatbot-close" title="Close chat">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Messages -->
    <div class="chatbot-messages" id="chatbot-messages">
        <!-- Welcome message will be injected by JS -->
    </div>

    <!-- Input -->
    <div class="chatbot-input-area">
        <div class="chatbot-input-wrapper">
            <input 
                type="text" 
                class="chatbot-input" 
                id="chatbot-input" 
                placeholder="Ask me anything about your store..." 
                autocomplete="off"
            />
            <button class="chatbot-send-btn" id="chatbot-send" title="Send">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Chatbot JavaScript -->
<script>
(function() {
    'use strict';

    // ========== DOM Elements ==========
    const fab = document.getElementById('chatbot-fab');
    const chatWindow = document.getElementById('chatbot-window');
    const closeBtn = document.getElementById('chatbot-close');
    const messagesContainer = document.getElementById('chatbot-messages');
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    const statusText = document.getElementById('chatbot-status-text');

    // ========== State ==========
    let isOpen = false;
    let isLoading = false;
    let conversationHistory = [];
    let isConfigured = true;

    // ========== Quick Action Prompts ==========
    const quickActions = [
        { label: '📊 Total Products', prompt: 'How many total products do we have?' },
        { label: '💰 Monthly Revenue', prompt: 'What is this month\'s revenue?' },
        { label: '📦 Active Rentals', prompt: 'Show me active rentals today' },
        { label: '🔍 How to add product?', prompt: 'How do I add a new product?' },
        { label: '📋 Top Categories', prompt: 'Show me product categories and their counts' },
    ];

    // ========== Initialize ==========
    async function init() {
        // Check if API key is configured
        try {
            const res = await fetch('index.php?controller=chatbot&action=status');
            const data = await res.json();
            isConfigured = data.configured;
        } catch (e) {
            console.warn('Chatbot status check failed:', e);
        }

        showWelcomeMessage();
    }

    // ========== Welcome Message ==========
    function showWelcomeMessage() {
        const welcomeHtml = `
            <div class="chatbot-msg bot">
                <div class="chatbot-msg-avatar">🤖</div>
                <div>
                    <div class="chatbot-msg-bubble">
                        <strong>Hello! I'm Shri</strong> 👋<br><br>
                        I'm your AI assistant for the Srishringarr admin panel. I can help you with:<br><br>
                        • 📊 <strong>Live data</strong> — product counts, revenue, orders<br>
                        • 🔍 <strong>Product lookup</strong> — search by SKU, category<br>
                        • 📖 <strong>How-to guides</strong> — navigate the admin panel<br>
                        • 💡 <strong>Business insights</strong> — pricing, rentals, commissions<br><br>
                        Try one of these or ask me anything:
                        <div class="chatbot-quick-actions" id="chatbot-quick-actions"></div>
                    </div>
                </div>
            </div>
        `;
        messagesContainer.innerHTML = welcomeHtml;

        // Add quick action buttons
        const quickContainer = document.getElementById('chatbot-quick-actions');
        if (quickContainer) {
            quickActions.forEach(action => {
                const btn = document.createElement('button');
                btn.className = 'chatbot-quick-btn';
                btn.textContent = action.label;
                btn.addEventListener('click', () => sendMessage(action.prompt));
                quickContainer.appendChild(btn);
            });
        }

        // Show config warning if needed
        if (!isConfigured) {
            const warning = document.createElement('div');
            warning.className = 'chatbot-config-warning';
            warning.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <span>API key not configured. Please set your Gemini API key in <code>Config/chatbot_config.php</code></span>
            `;
            messagesContainer.appendChild(warning);
        }
    }

    // ========== Toggle Chat ==========
    function toggleChat() {
        isOpen = !isOpen;
        chatWindow.classList.toggle('visible', isOpen);
        fab.classList.toggle('open', isOpen);
        
        if (isOpen) {
            setTimeout(() => input.focus(), 350);
        }
    }

    // ========== Add Message to UI ==========
    function addMessage(text, role = 'bot') {
        const msgDiv = document.createElement('div');
        msgDiv.className = `chatbot-msg ${role}`;
        
        const avatar = document.createElement('div');
        avatar.className = 'chatbot-msg-avatar';
        avatar.textContent = role === 'bot' ? '🤖' : '👤';
        
        const bubble = document.createElement('div');
        bubble.className = 'chatbot-msg-bubble';
        
        if (role === 'bot') {
            bubble.innerHTML = formatBotMessage(text);
        } else {
            bubble.textContent = text;
        }
        
        msgDiv.appendChild(avatar);
        msgDiv.appendChild(bubble);
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();
    }

    // ========== Format Bot Message (simple markdown) ==========
    function formatBotMessage(text) {
        let html = text;
        
        // Bold: **text** or __text__
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/__(.*?)__/g, '<strong>$1</strong>');
        
        // Italic: *text* or _text_
        html = html.replace(/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');
        
        // Inline code: `text`
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        
        // Links: [text](url)
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>');
        
        // Unordered lists: lines starting with - or •
        html = html.replace(/^[\s]*[-•]\s+(.+)$/gm, '<li>$1</li>');
        html = html.replace(/((?:<li>.*<\/li>\s*)+)/g, '<ul>$1</ul>');

        // Ordered lists: lines starting with numbers
        html = html.replace(/^[\s]*\d+\.\s+(.+)$/gm, '<li>$1</li>');
        
        // Line breaks
        html = html.replace(/\n/g, '<br>');
        
        // Clean up extra <br> around lists
        html = html.replace(/<br><ul>/g, '<ul>');
        html = html.replace(/<\/ul><br>/g, '</ul>');
        
        return html;
    }

    // ========== Show/Hide Typing Indicator ==========
    function showTyping() {
        // Remove existing
        hideTyping();
        
        const typing = document.createElement('div');
        typing.className = 'chatbot-typing active';
        typing.id = 'chatbot-typing';
        typing.innerHTML = `
            <div class="chatbot-msg-avatar" style="background: linear-gradient(135deg, #6e8efb, #a777e3); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;">🤖</div>
            <div class="chatbot-typing-dots">
                <span></span><span></span><span></span>
            </div>
        `;
        messagesContainer.appendChild(typing);
        scrollToBottom();
        statusText.textContent = 'Thinking...';
    }

    function hideTyping() {
        const typing = document.getElementById('chatbot-typing');
        if (typing) typing.remove();
        statusText.textContent = 'Online • Srishringarr Assistant';
    }

    // ========== Scroll to Bottom ==========
    function scrollToBottom() {
        requestAnimationFrame(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
    }

    // ========== Send Message ==========
    async function sendMessage(text = null) {
        const message = text || input.value.trim();
        if (!message || isLoading) return;

        // Clear input
        input.value = '';
        
        // Add user message to UI
        addMessage(message, 'user');
        
        // Add to history
        conversationHistory.push({ role: 'user', text: message });

        // Show typing
        isLoading = true;
        sendBtn.disabled = true;
        showTyping();

        try {
            const response = await fetch('index.php?controller=chatbot&action=chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    message: message,
                    history: conversationHistory.slice(-20) // Send last 20 turns
                })
            });

            const data = await response.json();
            
            hideTyping();
            
            if (data.success && data.reply) {
                addMessage(data.reply, 'bot');
                conversationHistory.push({ role: 'model', text: data.reply });
            } else {
                addMessage(data.error || 'Sorry, something went wrong. Please try again.', 'bot');
            }
        } catch (error) {
            hideTyping();
            console.error('Chatbot error:', error);
            addMessage('⚠️ Connection error. Please check if the server is running and try again.', 'bot');
        } finally {
            isLoading = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    // ========== Event Listeners ==========
    fab.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);
    
    sendBtn.addEventListener('click', () => sendMessage());
    
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) {
            toggleChat();
        }
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (isOpen && !chatWindow.contains(e.target) && !fab.contains(e.target)) {
            toggleChat();
        }
    });

    // ========== Start ==========
    init();
})();
</script>
