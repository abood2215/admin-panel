<div x-data="chatWidget()" x-init="init()" :class="dark ? 'dark' : ''">
    <!-- زر الفتح -->
    <button @click="toggleChat(true)"
            class="fixed bottom-6 right-6 z-40 group"
            x-show="!showChat" x-transition>
        <div class="relative">
            <div class="absolute inset-0 rounded-full blur-xl opacity-60 bg-gradient-to-tr from-cyan-400 to-blue-600 scale-110"></div>
            <div class="relative w-14 h-14 rounded-full bg-gradient-to-tr from-cyan-500 to-blue-700 shadow-lg
                        flex items-center justify-center ring-2 ring-white/60 group-hover:scale-105 transition">
                <img src="{{ asset('images/chat.png') }}" class="w-7 h-7" alt="Chat">
            </div>
        </div>
    </button>

    <!-- نافذة الشات -->
    <div x-show="showChat" x-transition
         class="fixed bottom-6 right-6 z-50 w-[420px] max-w-[95vw]">
        <div class="rounded-2xl overflow-hidden ring-1 ring-black/10 shadow-2xl
                    backdrop-blur supports-[backdrop-filter]:bg-white/70
                    dark:supports-[backdrop-filter]:bg-slate-900/70">

            <!-- Header -->
            <div class="flex items-center gap-3 px-4 py-3
                        bg-gradient-to-r from-cyan-500 to-blue-700 text-white">
                <img src="{{ asset('images/bot.png') }}" class="w-10 h-10 rounded-full bg-white">
                <div class="flex-1">
                    <div class="font-bold leading-tight">Chat bot</div>
                    <div class="text-xs text-white/80" x-text="online ? 'Online' : 'Offline'"></div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="dark = !dark" class="px-2 py-1 text-xs rounded bg-white/20 hover:bg-white/30">Theme</button>
                    <button x-show="messages.length" @click="clearChat()" class="px-2 py-1 text-xs rounded bg-white/20 hover:bg-white/30">Clear</button>
                    <button x-show="loading" @click="stop()" class="px-2 py-1 text-xs rounded bg-white/20 hover:bg-white/30">Stop</button>
                    <button @click="toggleChat(false)" class="w-9 h-9 rounded-lg hover:bg-white/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Messages -->
            <div class="max-h-[65vh] overflow-y-auto p-4 space-y-3
                        bg-slate-50 dark:bg-slate-900/60"
                 x-ref="scroll">
                <template x-for="(m, idx) in messages" :key="idx">
                    <div class="flex" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                        <div class="flex items-start gap-2 max-w-[85%]">
                            <template x-if="m.role !== 'user'">
                                <img src="{{ asset('images/bot.png') }}" class="w-7 h-7 rounded-full bg-white ring-1 ring-black/5">
                            </template>
                            <div class="px-3 py-2 rounded-2xl whitespace-pre-wrap break-words
                                        text-[15px] leading-relaxed shadow-sm
                                        ring-1 ring-black/5"
                                 :dir="m.dir || 'auto'"
                                 :class="m.role === 'user'
                                        ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-br-sm'
                                        : 'bg-gradient-to-tr from-white to-slate-50 dark:from-slate-800 dark:to-slate-700 text-slate-800 dark:text-slate-100 rounded-bl-sm'">
                                <div x-text="m.content"></div>
                                <div class="mt-1 flex gap-2 opacity-70">
                                    <button class="text-[11px] underline" @click="copy(m.content)">Copy</button>
                                </div>
                            </div>
                            <template x-if="m.role === 'user'">
                                <img src="{{ asset('images/chat.png') }}" class="w-7 h-7 rounded-full bg-white ring-1 ring-black/5">
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Welcome -->
                <div class="text-center text-slate-600 dark:text-slate-300 py-10" x-show="messages.length === 0">
                    <div class="text-5xl mb-3">👋</div>
                    <div class="text-lg font-semibold">مرحباً! كيف أقدر أساعدك اليوم؟</div>
                </div>

                <!-- Typing / Errors -->
                <div class="flex justify-center" x-show="loading">
                    <div class="px-3 py-1 text-xs text-slate-500 bg-white/70 dark:bg-slate-800/70 rounded">Generating…</div>
                </div>
                <template x-if="error">
                    <div class="text-center">
                        <div class="text-xs text-red-600" x-text="error"></div>
                        <button @click="retry()" class="mt-1 text-xs px-2 py-1 rounded bg-red-50 border border-red-200">Retry</button>
                    </div>
                </template>
            </div>

            <!-- Input -->
            <div class="p-3 bg-white/80 dark:bg-slate-800/80 backdrop-blur">
                <form @submit.prevent="submit()" class="space-y-2">
                    <div class="flex items-end gap-2">
                        <textarea x-model="draft" rows="1" @input="autoGrow($event)"
                                  class="flex-1 resize-none rounded-xl border border-slate-200 dark:border-slate-600
                                         bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100
                                         px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
                                  :dir="guessDir(draft)" placeholder="اكتب رسالتك..."></textarea>
                        <button type="submit" :disabled="loading || !draft.trim()"
                                class="shrink-0 w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-600
                                       text-white flex items-center justify-center shadow hover:scale-[1.02] transition disabled:opacity-60">
                            <svg viewBox="0 0 24 24" fill="none" class="w-6 h-6">
                                <path d="M4 20l16-8-16-8v6l10 2-10 2v6z" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                        <div></div>
                        <div x-text="`${draft.length} / 60000`"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
function chatWidget() {
    return {
        dark: window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches,
        showChat: (localStorage.getItem('showChat') === 'true'),
        online: true,
        draft: '',
        loading: false,
        error: '',
        messages: JSON.parse(localStorage.getItem('chatHistory') || '[]'),
        aborter: null,
        lastUserText: '',
        pendingHash: null,

        init() {
            this.$watch('messages', (val) => localStorage.setItem('chatHistory', JSON.stringify(val)));
            this.$watch('showChat', (open) => { if (open) this.$nextTick(() => this.scrollToEnd()); });
        },
        toggleChat(open) {
            this.showChat = open;
            localStorage.setItem('showChat', open ? 'true' : 'false');
            if (open) this.$nextTick(() => this.scrollToEnd());
        },
        guessDir(text) { return /[\u0600-\u06FF]/.test(text) ? 'rtl' : 'ltr'; },
        autoGrow(e) {
            const t = e.target;
            t.style.height = 'auto';
            t.style.height = Math.min(t.scrollHeight, 180) + 'px';
        },
        addMessage(role, content) {
            this.messages.push({ role, content, dir: this.guessDir(content) });
            this.$nextTick(() => this.scrollToEnd());
        },
        scrollToEnd() {
            const sc = this.$refs.scroll;
            if (sc) sc.scrollTop = sc.scrollHeight;
        },
        copy(text) {
            navigator.clipboard.writeText(text || '').then(()=>{},()=>{});
        },
        clearChat() {
            this.messages = [];
            localStorage.removeItem('chatHistory');
        },
        stop() {
            if (this.aborter) { this.aborter.abort(); this.aborter = null; }
            this.loading = false;
        },
        retry() {
            if (!this.lastUserText) return;
            this.error = '';
            this.send(this.lastUserText, { pushUser: false });
        },
        submit() {
            const text = this.draft.trim();
            if (!text || this.loading) return;
            if (text.length > 60000) {
                alert('النص أطول من الحد الأقصى (60000 حرف).');
                return;
            }
            this.draft = '';
            this.send(text, { pushUser: true });
        },
        async send(text, { pushUser = true } = {}) {
            const hash = btoa(unescape(encodeURIComponent(text))).slice(0,40);
            if (this.pendingHash && this.pendingHash === hash) return;

            this.pendingHash = hash;
            this.loading = true;
            this.error = '';
            this.lastUserText = text;

            if (pushUser) this.addMessage('user', text);

            // مهلة أطول 180 ثانية للردود الكبيرة
            this.aborter = new AbortController();
            const t = setTimeout(() => this.aborter.abort('timeout'), 180000);

            try {
                const res = await fetch(`{{ route('chat.send') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: text,
                        history: this.messages.slice(-8),
                    }),
                    signal: this.aborter.signal,
                });

                const data = await res.json();
                if (data.ok) {
                    // لعدم تجميد الواجهة: نقسم العرض فقط دون حذف أي جزء من النص
                    const reply = data.reply || '';
                    const chunks = reply.match(/[\s\S]{1,4000}/g) || [reply];
                    for (const part of chunks) this.addMessage('assistant', part);
                } else {
                    this.error = data.reply || 'Error';
                }
            } catch (e) {
                this.error = (e && e.name === 'AbortError') ? 'انتهت المهلة. تم إيقاف التوليد.' : 'تعذر الاتصال بالخادم.';
            } finally {
                clearTimeout(t);
                this.loading = false;
                this.aborter = null;
                this.pendingHash = null;
            }
        }
    }
}
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
