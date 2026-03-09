<template>
    <AppLayout>
        <Head title="Chat AI Kesehatan" />

        <!-- Header -->
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-primary via-primary to-primary-dark text-white p-3 px-4 mb-4 animate-fade-in-down shadow-sm">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-6 -left-2 w-16 h-16 bg-white/5 rounded-full"></div>
            <div class="relative flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h1 class="text-base font-bold leading-tight">Chat AI Kesehatan</h1>
                    <p class="text-white/80 text-[10px] mt-0.5 leading-tight">Berbasis Kemenkes RI & medis terpercaya</p>
                </div>
                <button @click="clearChat"
                    class="w-7 h-7 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition" title="Bersihkan chat">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="flex flex-col" style="height: calc(100dvh - 310px); min-height: 350px;">

            <!-- Messages -->
            <div ref="messagesEl" class="flex-1 overflow-y-auto space-y-3 pr-1 pb-3 scroll-smooth overscroll-contain">

                <!-- Welcome message -->
                <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center py-8 gap-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-primary/10 to-primary/20 rounded-3xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 mb-1">Halo! Saya Asisten Kesehatan HEALTIVA</h3>
                        <p class="text-sm text-gray-500 max-w-xs mx-auto">Tanyakan apa saja seputar kesehatan Anda. Jawaban saya berbasis pedoman <strong>Kemenkes RI</strong> dan sumber medis terpercaya.</p>
                    </div>
                    <!-- Quick suggestions -->
                    <div class="flex flex-wrap justify-center gap-2 mt-2">
                        <button v-for="q in quickQuestions" :key="q"
                            @click="sendQuick(q)"
                            class="text-xs bg-white border border-gray-200 hover:border-primary/50 hover:text-primary px-3 py-2 rounded-full transition text-gray-600">
                            {{ q }}
                        </button>
                    </div>
                </div>

                <!-- Chat Messages -->
                <template v-else>
                    <div v-for="(msg, i) in messages" :key="i"
                        :class="['flex items-end gap-2.5', msg.role === 'user' ? 'flex-row-reverse' : 'flex-row']">

                        <!-- Avatar -->
                        <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold shadow-sm overflow-hidden"
                            :class="msg.role === 'user' ? 'bg-primary text-white' : 'bg-white border border-gray-100'">
                            <template v-if="msg.role === 'user'">
                                <img v-if="userAvatarUrl" :src="userAvatarUrl" class="w-full h-full object-cover">
                                <span v-else>{{ userInitial }}</span>
                            </template>
                            <svg v-else class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                            </svg>
                        </div>

                        <!-- Bubble -->
                        <div :class="[
                            'max-w-[82%] rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm',
                            msg.role === 'user'
                                ? 'bg-primary text-white rounded-br-sm'
                                : 'bg-white border border-gray-100 text-gray-800 rounded-bl-sm'
                        ]">
                            <div v-if="msg.role === 'model'" v-html="renderMd(msg.text)" class="chat-md"></div>
                            <span v-else class="whitespace-pre-wrap">{{ msg.text }}</span>

                            <!-- YouTube Video Recommendations -->
                            <div v-if="msg.role === 'model' && msg.videos && msg.videos.length" class="mt-3 space-y-2 border-t border-gray-100 pt-2.5">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-3 h-3 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    Video Terkait
                                </p>
                                <a v-for="vid in msg.videos" :key="vid.video_id"
                                    :href="`https://www.youtube.com/watch?v=${vid.video_id}`"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex items-center gap-2.5 hover:bg-gray-50 rounded-xl p-1.5 -mx-1.5 transition group">
                                    <div class="relative w-14 h-9 rounded-lg overflow-hidden flex-shrink-0 bg-gray-200">
                                        <img :src="vid.thumbnail || `https://img.youtube.com/vi/${vid.video_id}/mqdefault.jpg`" :alt="vid.title" class="w-full h-full object-cover" />
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                            <div class="w-4 h-4 bg-white/90 rounded-full flex items-center justify-center">
                                                <svg class="w-2 h-2 text-[#FF0000] ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-semibold text-gray-700 truncate leading-snug">{{ vid.title }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">Tonton Video</p>
                                    </div>
                                </a>
                            </div>

                            <p class="text-[10px] mt-1.5 opacity-50 text-right">{{ msg.time }}</p>
                        </div>
                    </div>

                    <!-- Typing indicator -->
                    <div v-if="typing" class="flex items-end gap-2.5">
                        <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center bg-white border border-gray-100 shadow-sm">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                            </svg>
                        </div>
                        <div class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                            <div class="flex gap-1 items-center h-4">
                                <div class="w-2 h-2 bg-primary/60 rounded-full animate-bounce" style="animation-delay:0s"></div>
                                <div class="w-2 h-2 bg-primary/60 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                                <div class="w-2 h-2 bg-primary/60 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Disclaimer bar -->
            <div class="flex items-center gap-1.5 text-[11px] text-gray-400 mb-2 px-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informasi edukatif, bukan pengganti konsultasi dokter.
            </div>

            <!-- Input -->
            <div class="flex items-end gap-2 bg-white rounded-2xl border border-gray-200 px-4 py-3 shadow-sm focus-within:border-primary/40 focus-within:shadow-primary/10 transition-all">
                <textarea
                    ref="inputEl"
                    v-model="inputText"
                    @keydown.enter.exact.prevent="send"
                    rows="1"
                    placeholder="Tanyakan seputar kesehatan Andaâ€¦"
                    class="flex-1 resize-none text-sm outline-none text-gray-700 placeholder-gray-400 bg-transparent max-h-32"
                    style="min-height:24px"
                    @input="autoResize"
                ></textarea>
                <button @click="send"
                    :disabled="!inputText.trim() || typing"
                    :class="[
                        'w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 transition-all',
                        inputText.trim() && !typing ? 'bg-primary text-white hover:bg-primary-dark shadow-md shadow-primary/30' : 'bg-gray-100 text-gray-300'
                    ]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref, nextTick, computed, onMounted, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    latestRecord: Object,
    initQuestion: { type: String, default: null },
});

const page = usePage();
const userInitial = computed(() => page.props.auth?.user?.name?.charAt(0)?.toUpperCase() || 'U');
const userAvatarUrl = computed(() => {
    const avatar = page.props.auth?.user?.avatar;
    if (!avatar) return null;
    if (avatar.startsWith('http')) return avatar;
    return '/storage/' + avatar;
});

const messages = ref([]);
const inputText = ref('');
const typing = ref(false);
const messagesEl = ref(null);
const inputEl = ref(null);

const quickQuestions = [
    'Tensi 130/85 itu bahaya?',
    'Cara cepat turunin gula darah?',
    'BMI 27 harus diet ketat nggak?',
    'Detak jantung 95 bpm normal?',
    'Tanda-tanda harus ke dokter?',
    'Olahraga terbaik untuk jantung?',
];

const now = () => new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

const scrollToBottom = async () => {
    await nextTick();
    if (messagesEl.value) {
        messagesEl.value.scrollTop = messagesEl.value.scrollHeight;
    }
};

const autoResize = () => {
    const el = inputEl.value;
    if (!el) return;
    el.style.height = 'auto';
    el.style.height = el.scrollHeight + 'px';
};

const sendQuick = (q) => {
    inputText.value = q;
    send();
};

const send = async () => {
    const text = inputText.value.trim();
    if (!text || typing.value) return;

    messages.value.push({ role: 'user', text, time: now() });
    inputText.value = '';
    if (inputEl.value) {
        inputEl.value.style.height = 'auto';
    }
    await scrollToBottom();

    typing.value = true;
    await scrollToBottom();

    try {
        const payload = messages.value.map(m => ({ role: m.role, text: m.text }));
        const resp = await axios.post('/ai-chat/message', { messages: payload });
        const reply  = resp.data.reply  ?? 'Maaf, tidak ada respons.';
        const videos = resp.data.videos ?? [];
        messages.value.push({ role: 'model', text: reply, videos, time: now() });
    } catch (e) {
        messages.value.push({
            role: 'model',
            text: 'Maaf, terjadi kesalahan saat menghubungi layanan AI. Silakan coba lagi.',
            time: now(),
        });
    } finally {
        typing.value = false;
        await scrollToBottom();
    }
};

const clearChat = () => {
    if (messages.value.length && confirm('Hapus semua percakapan?')) {
        messages.value = [];
        localStorage.removeItem('HEALTIVA_chat_history');
    }
};

// SIMPAN OTO BILA ADA PERUBAHAN
watch(messages, (newVal) => {
    localStorage.setItem('HEALTIVA_chat_history', JSON.stringify(newVal));
}, { deep: true });

onMounted(() => {
    // 1. Cek Histori Obrolan sebelumnya di browser
    const sa = localStorage.getItem('HEALTIVA_chat_history');
    if (sa) {
        try {
            messages.value = JSON.parse(sa);
            scrollToBottom();
        } catch (e) {
            localStorage.removeItem('HEALTIVA_chat_history');
        }
    }

    if (props.initQuestion) {
        inputText.value = props.initQuestion;
        // Short delay so the page renders first
        setTimeout(() => send(), 300);
    }
});

// Lightweight markdown renderer for AI replies
const renderMd = (text) => {
    if (!text) return '';
    let html = text
        // Escape HTML first
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        // Bold
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        // Italic
        .replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>')
        // Headers
        .replace(/^### (.+)$/gm, '<p class="font-semibold text-gray-800 mt-3 mb-1 text-sm">$1</p>')
        .replace(/^## (.+)$/gm, '<p class="font-bold text-primary mt-3 mb-1">$1</p>')
        .replace(/^# (.+)$/gm, '<p class="font-bold text-primary mt-3 mb-1 text-base">$1</p>')
        // Numbered lists
        .replace(/^(\d+)\.\s(.+)$/gm, '<li class="ml-4 list-decimal my-0.5">$2</li>')
        // Bullet lists
        .replace(/^[-â€¢]\s(.+)$/gm, '<li class="ml-4 list-disc my-0.5">$1</li>')
        // Wrap consecutive <li> in <ul>/<ol>
        .replace(/(<li[^>]*>.*?<\/li>\n?)+/gs, (m) => `<ul class="my-1.5">${m}</ul>`)
        // Paragraphs
        .replace(/\n\n/g, '</p><p class="mt-2">')
        .replace(/\n/g, '<br>');
    return `<p>${html}</p>`;
};
</script>

<style scoped>
:deep(.chat-md) {
    font-size: 0.8125rem;
    line-height: 1.6;
}
:deep(.chat-md p) {
    margin: 0;
}
:deep(.chat-md p + p) {
    margin-top: 0.5rem;
}
:deep(.chat-md ul) {
    margin: 0.375rem 0;
    padding: 0;
}
:deep(.chat-md li) {
    list-style-position: outside;
    margin-left: 1.25rem;
    font-size: 0.8125rem;
    line-height: 1.5;
}
:deep(.chat-md strong) {
    font-weight: 700;
    color: inherit;
}
:deep(.chat-md em) {
    font-style: italic;
    opacity: 0.85;
}
</style>
