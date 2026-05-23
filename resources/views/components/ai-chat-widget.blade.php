<div x-data="aiChatWidget()" x-cloak>
    <!-- Floating Chat Button -->
    <button
        @click="toggleChat()"
        id="ai-chat-toggle-btn"
        class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-indigo-500/40 focus:outline-none focus:ring-4 focus:ring-indigo-300/50"
        :class="isOpen ? 'bg-gradient-to-br from-red-500 to-pink-600 rotate-0' : 'bg-gradient-to-br from-indigo-600 to-purple-600 animate-bounce-subtle'"
        :style="isOpen ? '' : 'animation: ai-bounce-subtle 3s ease-in-out infinite'"
    >
        <!-- Bot icon (when closed) -->
        <svg x-show="!isOpen" class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 14.5M14.25 3.104c.251.023.501.05.75.082M19.8 14.5a2.25 2.25 0 010 3l-3.3 3.3a2.25 2.25 0 01-3 0L12 19.3l-1.5 1.5a2.25 2.25 0 01-3 0l-3.3-3.3a2.25 2.25 0 010-3" />
        </svg>
        <!-- Close icon (when open) -->
        <svg x-show="isOpen" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Notification Badge -->
    <div x-show="!isOpen && hasUnread" x-transition
        class="fixed bottom-[72px] right-6 z-50 pointer-events-none">
        <span class="flex h-4 w-4">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
        </span>
    </div>

    <!-- Chat Window -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-24 right-6 z-50 w-[400px] max-h-[600px] rounded-2xl shadow-2xl overflow-hidden border border-gray-200/50 flex flex-col"
        style="backdrop-filter: blur(20px); max-height: calc(100vh - 140px);"
        id="ai-chat-window"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 px-5 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 14.5M14.25 3.104c.251.023.501.05.75.082M19.8 14.5a2.25 2.25 0 010 3l-3.3 3.3a2.25 2.25 0 01-3 0L12 19.3l-1.5 1.5a2.25 2.25 0 01-3 0l-3.3-3.3a2.25 2.25 0 010-3" />
                        </svg>
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-400 rounded-full border-2 border-indigo-600"></div>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm">BDM Assistant</h3>
                    <p class="text-white/70 text-xs">AI-powered • Always ready</p>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <!-- Clear chat button -->
                <button @click="clearChat()" class="p-2 rounded-lg hover:bg-white/10 transition-colors" title="Clear chat">
                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <button @click="toggleChat()" class="p-2 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div
            x-ref="messagesContainer"
            class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-50 to-white"
            style="min-height: 300px; max-height: 400px;"
        >
            <!-- Welcome Message -->
            <template x-if="messages.length === 0">
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                        </svg>
                    </div>
                    <h4 class="text-gray-800 font-semibold mb-1">Hi! I'm BDM Assistant 👋</h4>
                    <p class="text-gray-500 text-sm mb-4">Ask me anything about your business data</p>

                    <!-- Quick Action Chips -->
                    <div class="flex flex-wrap gap-2 justify-center">
                        <button @click="sendQuickMessage('How many customers do we have?')"
                            class="px-3 py-1.5 bg-white border border-indigo-200 text-indigo-700 rounded-full text-xs font-medium hover:bg-indigo-50 hover:border-indigo-300 transition-all shadow-sm">
                            👥 Customer count
                        </button>
                        <button @click="sendQuickMessage('Show me today\'s revenue')"
                            class="px-3 py-1.5 bg-white border border-green-200 text-green-700 rounded-full text-xs font-medium hover:bg-green-50 hover:border-green-300 transition-all shadow-sm">
                            💰 Today's revenue
                        </button>
                        <button @click="sendQuickMessage('Show me bookings for today')"
                            class="px-3 py-1.5 bg-white border border-blue-200 text-blue-700 rounded-full text-xs font-medium hover:bg-blue-50 hover:border-blue-300 transition-all shadow-sm">
                            📅 Today's bookings
                        </button>
                        <button @click="sendQuickMessage('List all employees')"
                            class="px-3 py-1.5 bg-white border border-purple-200 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-50 hover:border-purple-300 transition-all shadow-sm">
                            👨‍💼 Employees
                        </button>
                        <button @click="sendQuickMessage('Who are the top 5 customers by spend?')"
                            class="px-3 py-1.5 bg-white border border-orange-200 text-orange-700 rounded-full text-xs font-medium hover:bg-orange-50 hover:border-orange-300 transition-all shadow-sm">
                            ⭐ Top customers
                        </button>
                    </div>
                </div>
            </template>

            <!-- Message Bubbles -->
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <!-- Bot avatar -->
                    <div x-show="msg.role === 'assistant'" class="flex-shrink-0 mr-2 mt-1">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M14.25 3.104v5.714c0 .597.237 1.17.659 1.591L19.8 14.5" />
                            </svg>
                        </div>
                    </div>

                    <div :class="msg.role === 'user'
                        ? 'bg-gradient-to-br from-indigo-600 to-blue-600 text-white rounded-2xl rounded-br-md px-4 py-2.5 max-w-[85%] shadow-md'
                        : 'bg-white text-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 max-w-[85%] shadow-md border border-gray-100'">
                        <div class="text-sm leading-relaxed whitespace-pre-wrap" x-html="msg.role === 'assistant' ? formatMessage(msg.content) : msg.content"></div>
                        <div :class="msg.role === 'user' ? 'text-white/50' : 'text-gray-400'"
                             class="text-[10px] mt-1" x-text="msg.time"></div>
                    </div>
                </div>
            </template>

            <!-- Typing Indicator -->
            <div x-show="isTyping" class="flex justify-start">
                <div class="flex-shrink-0 mr-2 mt-1">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M14.25 3.104v5.714c0 .597.237 1.17.659 1.591L19.8 14.5" />
                        </svg>
                    </div>
                </div>
                <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-md border border-gray-100">
                    <div class="flex space-x-1.5">
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="flex-shrink-0 border-t border-gray-200 bg-white p-3">
            <form @submit.prevent="sendMessage()" class="flex items-end space-x-2">
                <div class="flex-1 relative">
                    <textarea
                        x-ref="chatInput"
                        x-model="userInput"
                        @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                        placeholder="Ask about customers, revenue, bookings..."
                        rows="1"
                        class="w-full resize-none rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white transition-all"
                        style="max-height: 100px;"
                        :disabled="isTyping"
                        id="ai-chat-input"
                    ></textarea>
                </div>
                <button
                    type="submit"
                    :disabled="!userInput.trim() || isTyping"
                    class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                    :class="userInput.trim() && !isTyping
                        ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg hover:shadow-indigo-500/40 hover:scale-105'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                    id="ai-chat-send-btn"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </button>
            </form>
            <p class="text-[10px] text-gray-400 mt-1.5 text-center">BDM AI • Powered by Gemini</p>
        </div>
    </div>
</div>

<!-- AI Chat Styles -->
<style>
    [x-cloak] { display: none !important; }

    @keyframes ai-bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    #ai-chat-window {
        font-family: 'Figtree', system-ui, -apple-system, sans-serif;
    }

    /* Custom scrollbar for messages */
    #ai-chat-window [x-ref="messagesContainer"]::-webkit-scrollbar {
        width: 4px;
    }
    #ai-chat-window [x-ref="messagesContainer"]::-webkit-scrollbar-track {
        background: transparent;
    }
    #ai-chat-window [x-ref="messagesContainer"]::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.3);
        border-radius: 20px;
    }
    #ai-chat-window [x-ref="messagesContainer"]::-webkit-scrollbar-thumb:hover {
        background: rgba(99, 102, 241, 0.5);
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        #ai-chat-window {
            width: calc(100vw - 24px) !important;
            right: 12px !important;
            bottom: 80px !important;
            max-height: calc(100vh - 100px) !important;
        }
    }
</style>

<!-- AI Chat Script -->
<script>
    // Auto-detect: use the same hostname the browser is on, just with port 8002
    const AI_AGENT_URL = `http://${window.location.hostname}:8002`;

    function aiChatWidget() {
        return {
            isOpen: false,
            isTyping: false,
            hasUnread: false,
            userInput: '',
            sessionId: null,
            messages: [],

            init() {
                // Restore session from localStorage
                const saved = localStorage.getItem('bdm_ai_chat');
                if (saved) {
                    try {
                        const data = JSON.parse(saved);
                        this.messages = data.messages || [];
                        this.sessionId = data.sessionId || null;
                    } catch (e) {
                        // ignore corrupt data
                    }
                }

                // Generate session ID if none
                if (!this.sessionId) {
                    this.sessionId = 'web-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                }
            },

            toggleChat() {
                this.isOpen = !this.isOpen;
                this.hasUnread = false;
                if (this.isOpen) {
                    this.$nextTick(() => {
                        this.scrollToBottom();
                        this.$refs.chatInput?.focus();
                    });
                }
            },

            async sendMessage() {
                const text = this.userInput.trim();
                if (!text || this.isTyping) return;

                // Add user message
                this.messages.push({
                    role: 'user',
                    content: text,
                    time: this.formatTime(new Date()),
                });
                this.userInput = '';
                this.isTyping = true;
                this.scrollToBottom();

                try {
                    const response = await fetch(`${AI_AGENT_URL}/api/chat`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            message: text,
                            session_id: this.sessionId,
                        }),
                    });

                    if (!response.ok) throw new Error('Failed to get response');

                    const data = await response.json();
                    this.sessionId = data.session_id || this.sessionId;

                    // Add bot response
                    this.messages.push({
                        role: 'assistant',
                        content: data.response,
                        time: this.formatTime(new Date()),
                    });

                    if (!this.isOpen) {
                        this.hasUnread = true;
                    }
                } catch (error) {
                    this.messages.push({
                        role: 'assistant',
                        content: '⚠️ Sorry, I couldn\'t connect to the AI Agent. Please make sure the agent is running on port 8002.\n\n```\ncd ai_agent && python3 main.py\n```',
                        time: this.formatTime(new Date()),
                    });
                }

                this.isTyping = false;
                this.saveChat();
                this.scrollToBottom();
            },

            sendQuickMessage(text) {
                this.userInput = text;
                this.sendMessage();
            },

            clearChat() {
                this.messages = [];
                this.sessionId = 'web-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                localStorage.removeItem('bdm_ai_chat');

                // Also clear on the agent side
                fetch(`${AI_AGENT_URL}/api/clear-session`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ session_id: this.sessionId }),
                }).catch(() => {});
            },

            saveChat() {
                // Keep only last 50 messages in storage
                const toSave = {
                    messages: this.messages.slice(-50),
                    sessionId: this.sessionId,
                };
                localStorage.setItem('bdm_ai_chat', JSON.stringify(toSave));
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            },

            formatTime(date) {
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            },

            formatMessage(text) {
                if (!text) return '';

                // Try to parse as JSON for structured responses
                try {
                    const parsed = JSON.parse(text);
                    return this.formatJsonResponse(parsed);
                } catch (e) {
                    // Not JSON — process as markdown-like text
                }

                // Basic markdown formatting
                let formatted = text
                    // Bold
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    // Inline code
                    .replace(/`([^`]+)`/g, '<code class="px-1 py-0.5 bg-gray-100 rounded text-xs text-indigo-700 font-mono">$1</code>')
                    // Code blocks
                    .replace(/```[\s\S]*?```/g, (match) => {
                        const code = match.replace(/```\w*\n?/g, '').replace(/```$/g, '');
                        return `<pre class="bg-gray-800 text-green-300 text-xs rounded-lg p-3 my-2 overflow-x-auto font-mono"><code>${code}</code></pre>`;
                    })
                    // Lists with bullet points
                    .replace(/^[•\-]\s(.+)$/gm, '<div class="flex items-start gap-1.5 my-0.5"><span class="text-indigo-400 mt-0.5">•</span><span>$1</span></div>')
                    // Numbered lists
                    .replace(/^(\d+)\.\s(.+)$/gm, '<div class="flex items-start gap-1.5 my-0.5"><span class="text-indigo-500 font-semibold text-xs min-w-[18px]">$1.</span><span>$2</span></div>');

                return formatted;
            },

            formatJsonResponse(data) {
                // Handle different response structures
                if (data.error) {
                    return `<div class="text-red-600 text-xs bg-red-50 rounded-lg p-2">⚠️ ${data.error}</div>`;
                }

                let html = '';

                // Customer count
                if (data.total_customers !== undefined) {
                    html += `<div class="flex items-center gap-2"><span class="text-2xl font-bold text-indigo-600">${data.total_customers}</span><span class="text-gray-500">total customers</span></div>`;
                }

                // Revenue summary
                if (data.total_revenue !== undefined) {
                    html += `<div class="space-y-1.5">`;
                    html += `<div class="flex justify-between"><span class="text-gray-600">Period</span><span class="font-medium capitalize">${data.period || 'N/A'}</span></div>`;
                    html += `<div class="flex justify-between"><span class="text-gray-600">Revenue</span><span class="font-bold text-green-600">$${Number(data.total_revenue).toFixed(2)}</span></div>`;
                    html += `<div class="flex justify-between"><span class="text-gray-600">Transactions</span><span class="font-medium">${data.total_transactions || 0}</span></div>`;
                    if (data.massage_revenue > 0) html += `<div class="flex justify-between text-xs"><span class="text-gray-500">Massage</span><span>$${Number(data.massage_revenue).toFixed(2)}</span></div>`;
                    if (data.product_revenue > 0) html += `<div class="flex justify-between text-xs"><span class="text-gray-500">Products</span><span>$${Number(data.product_revenue).toFixed(2)}</span></div>`;
                    html += `</div>`;
                }

                // Customer list / search results
                const customers = data.customers || data.results || data.vip_customers || data.top_customers;
                if (customers && Array.isArray(customers)) {
                    const label = data.total !== undefined ? `${data.total} customers found` :
                                  data.count !== undefined ? `${data.count} results` :
                                  `${customers.length} customers`;
                    html += `<div class="text-xs text-gray-500 mb-2">${label}</div>`;
                    if (customers.length === 0) {
                        html += `<div class="text-gray-400 text-center py-2">No customers found</div>`;
                    } else {
                        html += `<div class="space-y-1.5">`;
                        customers.slice(0, 10).forEach(c => {
                            const badge = c.vip_card_type ? `<span class="ml-1 px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded text-[10px] font-medium">VIP</span>` : '';
                            const extra = c.total_spend !== undefined ? ` • $${Number(c.total_spend).toFixed(2)}` :
                                          c.vip_card_balance !== undefined ? ` • Balance: $${Number(c.vip_card_balance).toFixed(2)}` : '';
                            html += `<div class="flex items-center justify-between bg-gray-50 rounded-lg px-2.5 py-1.5">
                                <div><span class="font-medium text-xs">${c.name || 'N/A'}</span>${badge}</div>
                                <span class="text-[10px] text-gray-500">${c.phone || ''}${extra}</span>
                            </div>`;
                        });
                        if (customers.length > 10) {
                            html += `<div class="text-center text-[10px] text-gray-400">+${customers.length - 10} more</div>`;
                        }
                        html += `</div>`;
                    }
                }

                // Bookings
                if (data.bookings && Array.isArray(data.bookings)) {
                    html += `<div class="text-xs text-gray-500 mb-2">${data.total || data.bookings.length} bookings${data.date ? ' on ' + data.date : ''}</div>`;
                    if (data.bookings.length === 0) {
                        html += `<div class="text-gray-400 text-center py-2">No bookings found</div>`;
                    } else {
                        html += `<div class="space-y-1.5">`;
                        data.bookings.slice(0, 8).forEach(b => {
                            const statusColor = b.status === 'completed' ? 'bg-green-100 text-green-700' :
                                                b.status === 'confirmed' ? 'bg-blue-100 text-blue-700' :
                                                b.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700';
                            html += `<div class="bg-gray-50 rounded-lg px-2.5 py-1.5">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-xs">${b.customer_name || 'N/A'}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium ${statusColor}">${b.status || 'pending'}</span>
                                </div>
                                <div class="text-[10px] text-gray-500">${b.service_name || ''} ${b.employee_name ? '• ' + b.employee_name : ''}</div>
                            </div>`;
                        });
                        html += `</div>`;
                    }
                }

                // Employees
                if (data.employees && Array.isArray(data.employees)) {
                    html += `<div class="text-xs text-gray-500 mb-2">${data.total || data.employees.length} employees (${data.active_count || 0} active)</div>`;
                    html += `<div class="space-y-1.5">`;
                    data.employees.forEach(e => {
                        const statusDot = e.working_status === 'active' ? 'bg-green-400' : 'bg-gray-300';
                        html += `<div class="flex items-center justify-between bg-gray-50 rounded-lg px-2.5 py-1.5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full ${statusDot}"></div>
                                <span class="font-medium text-xs">${e.name}</span>
                            </div>
                            <span class="text-[10px] text-gray-500">${e.phone || ''}</span>
                        </div>`;
                    });
                    html += `</div>`;
                }

                // Products
                if (data.products && Array.isArray(data.products)) {
                    html += `<div class="text-xs text-gray-500 mb-2">${data.total || data.products.length} products</div>`;
                    html += `<div class="space-y-1.5">`;
                    data.products.forEach(p => {
                        html += `<div class="flex items-center justify-between bg-gray-50 rounded-lg px-2.5 py-1.5">
                            <span class="font-medium text-xs">${p.name}</span>
                            <div class="text-right">
                                <span class="text-xs font-semibold text-green-600">$${Number(p.price || 0).toFixed(2)}</span>
                                <span class="text-[10px] text-gray-400 ml-1">qty: ${p.quantity ?? 'N/A'}</span>
                            </div>
                        </div>`;
                    });
                    html += `</div>`;
                }

                // Services
                if (data.services && Array.isArray(data.services)) {
                    html += `<div class="text-xs text-gray-500 mb-2">${data.total || data.services.length} services</div>`;
                    html += `<div class="space-y-1.5">`;
                    data.services.forEach(s => {
                        html += `<div class="flex items-center justify-between bg-gray-50 rounded-lg px-2.5 py-1.5">
                            <span class="font-medium text-xs">${s.name}</span>
                            <div class="text-right">
                                <span class="text-xs font-semibold text-green-600">$${Number(s.price || 0).toFixed(2)}</span>
                                <span class="text-[10px] text-gray-400 ml-1">${s.duration_minutes ? s.duration_minutes + 'min' : ''}</span>
                            </div>
                        </div>`;
                    });
                    html += `</div>`;
                }

                // Booking count
                if (data.booking_count !== undefined) {
                    html += `<div class="flex items-center gap-2"><span class="text-2xl font-bold text-blue-600">${data.booking_count}</span><span class="text-gray-500">bookings</span></div>`;
                }

                // Expenses
                if (data.expenses && Array.isArray(data.expenses)) {
                    html += `<div class="text-xs text-gray-500 mb-2">Total: $${Number(data.total_amount || 0).toFixed(2)}</div>`;
                    html += `<div class="space-y-1.5">`;
                    data.expenses.slice(0, 10).forEach(e => {
                        html += `<div class="flex items-center justify-between bg-gray-50 rounded-lg px-2.5 py-1.5">
                            <div>
                                <span class="font-medium text-xs">${e.item_name}</span>
                                <span class="text-[10px] text-gray-400 ml-1">${e.purpose || ''}</span>
                            </div>
                            <span class="text-xs font-semibold text-red-600">-$${Number(e.amount || 0).toFixed(2)}</span>
                        </div>`;
                    });
                    html += `</div>`;
                }

                // Fallback hint
                if (data.message && data.hint) {
                    html += `<div class="space-y-2">`;
                    html += `<div class="text-xs text-yellow-700 bg-yellow-50 rounded-lg p-2">⚠️ ${data.message}</div>`;
                    html += `<div class="text-xs text-gray-500">${data.hint}</div>`;
                    if (data.available_keywords) {
                        html += `<div class="text-[10px] text-gray-400">Try: ${data.available_keywords.join(', ')}</div>`;
                    }
                    html += `</div>`;
                }

                return html || `<pre class="text-xs bg-gray-50 rounded-lg p-2 overflow-x-auto">${JSON.stringify(data, null, 2)}</pre>`;
            },
        };
    }
</script>
