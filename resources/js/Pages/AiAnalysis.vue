<template>
    <AppLayout>
        <Head title="Analisis AI" />

        <!-- Header Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-primary to-primary-dark text-white p-5 mb-5 animate-fade-in-down">
            <div class="absolute -top-6 -right-6 w-28 h-28 bg-white/10 rounded-full"></div>
            <div class="absolute top-4 -right-1 w-14 h-14 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-8 -left-4 w-20 h-20 bg-white/5 rounded-full"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                </div>
                <div class="flex-1">
                    <h1 class="text-xl font-bold">Analisis Kesehatan AI</h1>
                    <p class="text-white/70 text-xs mt-0.5">Powered by HEALTIVA Health AI Engine</p>
                </div>
                <div v-if="analyses.length" class="text-right flex-shrink-0">
                    <p class="text-3xl font-bold leading-none">{{ analyses.length }}</p>
                    <p class="text-[10px] text-white/70 mt-0.5">Analisis</p>
                </div>
            </div>
        </div>

        <!-- Desktop: 2 col layout -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

            <!-- LEFT: Run Analysis (2/5) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Run Analysis Card -->
                <div class="p-6 rounded-2xl bg-gradient-to-br from-primary to-primary-dark text-white hover-lift animate-fade-in-left delay-75 shadow-lg shadow-primary/20">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" /></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Jalankan Analisis</h3>
                            <p class="text-white/70 text-xs">Analisis data kesehatan terakhir Anda</p>
                        </div>
                    </div>

                    <div v-if="!hasRecords" class="bg-white/10 rounded-xl p-3 mb-4 text-sm text-white/80">
                        Belum ada data kesehatan. Masukkan setidaknya satu data terlebih dahulu.
                    </div>

                    <form v-if="hasRecords" @submit.prevent="runAnalysis">
                        <button type="submit" :disabled="analyzing"
                            class="w-full bg-white text-primary font-bold py-3 rounded-xl transition-all hover:bg-white/90 active:bg-white/90 disabled:opacity-70">
                            <span v-if="analyzing" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Menganalisis...
                            </span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Analisis Sekarang
                            </span>
                        </button>
                    </form>
                    <Link v-else href="/input-data"
                        class="block w-full bg-white text-primary-dark font-semibold py-3 rounded-xl text-center mt-4 hover:bg-primary/5 transition">
                        Input Data Dulu
                    </Link>
                </div>

                <!-- Error -->
                <div v-if="$page.props.errors?.error"
                    class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm animate-fade-in-up">
                    {{ $page.props.errors.error }}
                </div>

                <!-- Info card -->
                <div class="card-medix p-4 animate-fade-in-up delay-150">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h4 class="font-semibold text-gray-700 text-sm">Yang Dianalisis</h4>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2.5 bg-[#FDD3CF] rounded-lg px-3 py-2">
                            <div class="w-5 h-5 rounded-md bg-primary/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <span class="text-xs text-gray-600">Tekanan darah (sistolik &amp; diastolik)</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-[#EFDBDC] rounded-lg px-3 py-2">
                            <div class="w-5 h-5 rounded-md bg-[#E48888]/40 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                            </div>
                            <span class="text-xs text-gray-600">Detak jantung &amp; ritme</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-[#FDD3CF] rounded-lg px-3 py-2">
                            <div class="w-5 h-5 rounded-md bg-primary/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <span class="text-xs text-gray-600">Gula darah &amp; risiko diabetes</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-[#EFDBDC] rounded-lg px-3 py-2">
                            <div class="w-5 h-5 rounded-md bg-[#E48888]/40 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-[#B92521]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            </div>
                            <span class="text-xs text-gray-600">BMI &amp; status berat badan</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-[#FDD3CF] rounded-lg px-3 py-2">
                            <div class="w-5 h-5 rounded-md bg-primary/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                            </div>
                            <span class="text-xs text-gray-600">Suhu tubuh &amp; SpO2</span>
                        </div>
                        <div class="flex items-center gap-2.5 bg-[#EFDBDC] rounded-lg px-3 py-2">
                            <div class="w-5 h-5 rounded-md bg-[#E48888]/40 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <span class="text-xs text-gray-600">Tren dari riwayat data</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Analysis History (3/5) -->
            <div class="lg:col-span-3 space-y-4 animate-fade-in-right delay-100">
                <div v-if="analyses.length">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-semibold text-gray-700">Riwayat Analisis</h2>
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-full">{{ analyses.length }} analisis</span>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(analysis, index) in analyses" :key="analysis.id"
                            class="card-medix overflow-hidden hover-lift"
                            :style="`animation-delay:${index*60}ms`">
                            <div class="flex items-center justify-between p-4">
                                <div class="flex items-center gap-3 flex-1 cursor-pointer select-none" @click="toggleAnalysis(index)">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center" :class="index === 0 ? 'bg-primary' : 'bg-primary/10'">
                                        <svg class="w-4 h-4" :class="index === 0 ? 'text-white' : 'text-primary'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" /></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">
                                            {{ index === 0 ? 'Analisis Terbaru' : `Analisis #${analyses.length - index}` }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ formatDate(analysis.created_at) }} &bull; {{ analysis.records_analyzed }} data</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button @click="confirmDelete(analysis.id)"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    <div class="cursor-pointer select-none w-8 h-8 flex items-center justify-center" @click="toggleAnalysis(index)">
                                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                            :class="{ 'rotate-180': openAnalysis === index }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <transition name="accordion">
                                <div v-if="openAnalysis === index" class="border-t border-gray-100">
                                    <div class="analysis-result p-4" v-html="renderMarkdown(analysis.result)"></div>
                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2 px-4 pb-4">
                                        <button @click="downloadPdf(analysis)"
                                            class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-[#FDD3CF] text-[#B92521] hover:bg-[#F18E8C]/40 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            PDF
                                        </button>
                                        <button @click="shareWhatsApp(analysis)"
                                            class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.673.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                            WA
                                        </button>
                                        <Link :href="'/ai-chat?from_analysis=' + analysis.id"
                                            class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition ml-auto">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                            Tanya AI
                                        </Link>
                                    </div>

                                    <!-- Video Edukasi Terkait -->
                                    <div v-if="getRelatedVideos(analysis.result).length" class="px-4 pb-4 border-t border-gray-50 pt-3">
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                            Video Edukasi Terkait
                                        </p>
                                        <div class="flex flex-col gap-2">
                                            <a v-for="vid in getRelatedVideos(analysis.result)" :key="vid.id"
                                               :href="`https://www.youtube.com/watch?v=${vid.youtubeId}`"
                                               target="_blank" rel="noopener noreferrer"
                                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-gray-100 hover:border-[#FF0000]/30 hover:bg-red-50/40 transition-all group">
                                                <!-- Thumbnail -->
                                                <div class="relative w-16 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gray-200">
                                                    <img
                                                        :src="`https://img.youtube.com/vi/${vid.youtubeId}/mqdefault.jpg`"
                                                        :alt="vid.title"
                                                        class="w-full h-full object-cover"
                                                    />
                                                    <div class="absolute inset-0 flex items-center justify-center bg-black/25 group-hover:bg-black/10 transition">
                                                        <div class="w-5 h-5 bg-white/90 rounded-full flex items-center justify-center">
                                                            <svg class="w-2.5 h-2.5 text-[#FF0000] ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[11px] font-semibold text-gray-700 truncate">{{ vid.title }}</p>
                                                    <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ vid.channel }}</p>
                                                </div>
                                                <!-- External link icon -->
                                                <svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0 group-hover:text-[#FF0000]/60 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>

                <!-- No analyses state -->
                <div v-else class="card-medix p-10 text-center animate-scale-in">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" /></svg>
                    </div>
                    <h3 class="font-semibold text-gray-700 mb-1">Belum Ada Analisis</h3>
                    <p class="text-sm text-gray-500">Jalankan analisis AI pertama Anda</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    analyses:       Array,
    latestAnalysis: Object,
    hasRecords:     Boolean,
    eduVideos:      { type: Array, default: () => [] },
});

const analyzing = ref(false);
const openAnalysis = ref(0);

const analysisPoints = [
    'Tekanan darah (sistolik & diastolik)',
    'Detak jantung & ritme',
    'Gula darah & risiko diabetes',
    'BMI & status berat badan',
    'Suhu tubuh & SpO2',
    'Tren dari riwayat data',
];

const runAnalysis = () => {
    analyzing.value = true;
    router.post('/ai-analysis', {}, {
        onFinish: () => { analyzing.value = false; },
        onSuccess: () => { openAnalysis.value = 0; }
    });
};

const toggleAnalysis = (index) => {
    openAnalysis.value = openAnalysis.value === index ? null : index;
};

const confirmDelete = (id) => {
    if (confirm('Hapus laporan analisis ini?')) {
        router.delete(`/ai-analysis/${id}`);
    }
};

const downloadPdf = (analysis) => {
    const date = formatDate(analysis.created_at);

    // Convert markdown to simple HTML for better readability
    const html = (analysis.result ?? '')
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/### (.+)/g, '<h3>$1</h3>')
        .replace(/## (.+)/g, '<h2>$1</h2>')
        .replace(/# (.+)/g, '<h1 class="section">$1</h1>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/\n- /g, '</li><li>')
        .replace(/(<\/li><li>)/g, '$1')
        .replace(/^- /gm, '<li>')
        .replace(/\n\n/g, '</p><p>')
        .replace(/\n/g, '<br>');

    const content = `<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
<title>Laporan Analisis Kesehatan - HEALTIVA</title>
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; max-width: 720px; margin: 0 auto; padding: 32px 24px; color: #1f2937; font-size: 14px; line-height: 1.7; }
  .logo { color: #B92521; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 4px; }
  .meta { color: #6b7280; font-size: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #EFDBDC; }
  h1 { color: #B92521; font-size: 17px; margin: 20px 0 6px; }
  h1.section { font-size: 15px; }
  h2 { color: #374151; font-size: 14px; margin: 16px 0 4px; }
  h3 { color: #6b7280; font-size: 13px; margin: 12px 0 4px; }
  p { margin: 8px 0; }
  li { margin: 4px 0; }
  strong { color: #111827; }
  .footer { margin-top: 32px; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 12px; }
  @media print { .no-print { display: none; } }
</style></head><body>
<div class="logo">HEALTIVA</div>
<div class="meta">Laporan Analisis Kesehatan &nbsp;|&nbsp; ${date}</div>
<div>${html}</div>
<p class="footer">Laporan ini dihasilkan oleh HEALTIVA AI. Bersifat informatif dan tidak menggantikan konsultasi dokter.</p>
<script>window.onload = () => { window.print(); };<\/script>
</body></html>`;

    const blob = new Blob([content], { type: 'text/html;charset=utf-8' });
    const url  = URL.createObjectURL(blob);
    const win  = window.open(url, '_blank');
    if (!win) {
        // Popup blocked — fallback: open as data URI in same tab
        const a = document.createElement('a');
        a.href = url;
        a.target = '_blank';
        a.click();
    }
    // Revoke after 60s
    setTimeout(() => URL.revokeObjectURL(url), 60000);
};

const shareWhatsApp = (analysis) => {
    const date = formatDate(analysis.created_at);
    const plain = (analysis.result ?? '')
        .replace(/\*\*(.+?)\*\*/g, '*$1*')
        .replace(/\*(.+?)\*/g, '_$1_')
        .replace(/#{1,3}\s/g, '');
    const msg = `*Laporan Analisis Kesehatan HEALTIVA*\n${date}\n\n${plain.trim()}\n\n_Dihasilkan oleh HEALTIVA Health Monitor. Bukan pengganti konsultasi dokter._`;
    window.open(`https://wa.me/?text=${encodeURIComponent(msg)}`, '_blank');
};

// Static fallback (used when YouTube API cache is not yet populated)
const staticEduVideos = [
    { id: 1,  youtubeId: 'b2iSH4VKpXo', category: 'heart',     title: 'Hipertensi: Penyebab, Gejala dan Penanganannya',                channel: 'Kemenkes RI' },
    { id: 2,  youtubeId: 'l0C_GsEINHs', category: 'heart',     title: 'Penyakit Jantung Koroner: Kenali dan Cegah Sejak Dini',        channel: 'PERKI Indonesia' },
    { id: 3,  youtubeId: 'fIAB4vdqYaQ', category: 'diabetes',  title: 'Diabetes Melitus Tipe 2: Gejala dan Pencegahan',               channel: 'Kemenkes RI' },
    { id: 4,  youtubeId: 'wuU1TGqV6IU', category: 'diabetes',  title: 'Pola Makan Sehat untuk Penderita Diabetes',                   channel: 'PERKENI' },
    { id: 5,  youtubeId: 'tFnFDFNITKs', category: 'nutrition', title: 'Cara Menghitung IMT dan Kategori Berat Badan',                 channel: 'Kemenkes RI' },
    { id: 6,  youtubeId: 'Yt59GKQ7oN8', category: 'nutrition', title: 'Pedoman Gizi Seimbang Isi Piringku',                          channel: 'Kemenkes RI' },
    { id: 7,  youtubeId: 'gHbYJfwFgOU', category: 'lifestyle', title: 'Manfaat Olahraga Rutin bagi Kesehatan Jantung',               channel: 'WHO Indonesia' },
    { id: 8,  youtubeId: 'MXuSCKuHe7I', category: 'lifestyle', title: 'Bahaya Rokok bagi Kesehatan Jantung dan Paru',               channel: 'Kemenkes RI' },
    { id: 9,  youtubeId: 'vgVGKyLpxeU', category: 'mental',    title: 'Cara Mengelola Stres untuk Kesehatan Optimal',               channel: 'Kemenkes RI' },
    { id: 10, youtubeId: 'wcHHSRMVHQE', category: 'heart',     title: 'Pertolongan Pertama pada Serangan Jantung',                  channel: 'PERKI Indonesia' },
    { id: 11, youtubeId: 'VHxpfgfonSk', category: 'diabetes',  title: 'Olahraga Aman dan Efektif untuk Penderita Diabetes',         channel: 'PERKENI' },
    { id: 12, youtubeId: 'nm1TxQj9IsQ', category: 'lifestyle', title: 'Tips Tidur Berkualitas untuk Kesehatan Optimal',             channel: 'Kemenkes RI' },
];

// Use live YouTube videos from cache (populated when user visits Edukasi), else static
const allEduVideos = computed(() =>
    props.eduVideos && props.eduVideos.length > 0 ? props.eduVideos : staticEduVideos
);

const getRelatedVideos = (text) => {
    if (!text) return [];
    const t = text.toLowerCase();
    const scored = allEduVideos.value.map(v => {
        let score = 0;
        if (v.category === 'heart'     && (t.includes('hipertensi') || t.includes('tekanan darah') || t.includes('jantung') || t.includes('sistolik') || t.includes('kardio'))) score += 3;
        if (v.category === 'diabetes'  && (t.includes('diabetes') || t.includes('gula darah') || t.includes('glukosa') || t.includes('hiperglikemia'))) score += 3;
        if (v.category === 'nutrition' && (t.includes('bmi') || t.includes('berat badan') || t.includes('obesitas') || t.includes('gizi') || t.includes('imt') || t.includes('overweight'))) score += 3;
        if (v.category === 'lifestyle' && (t.includes('olahraga') || t.includes('aktivitas') || t.includes('rokok') || t.includes('tidur') || t.includes('gaya hidup'))) score += 3;
        if (v.category === 'mental'    && (t.includes('stres') || t.includes('mental') || t.includes('psikologi') || t.includes('kecemasan'))) score += 3;
        return { ...v, score };
    });
    const matched = scored.filter(v => v.score > 0).sort((a, b) => b.score - a.score).slice(0, 3);
    return matched.length > 0 ? matched : allEduVideos.value.slice(0, 3);
};

const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
});

const formatInline = (text) =>
    text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>');

const renderMarkdown = (text) => {
    if (!text) return '';
    const lines = text.split('\n');
    let html = '';
    let inSection = false;
    let inUl = false;
    const sectionColors = ['violet', 'blue', 'green', 'amber', 'rose', 'teal'];
    let colorIdx = 0;

    const closeUl = () => { if (inUl) { html += '</ul>'; inUl = false; } };
    const closeSection = () => { if (inSection) { html += '</div>'; inSection = false; } };

    for (const raw of lines) {
        const line = raw.trim();
        if (line.startsWith('## ')) {
            closeUl(); closeSection();
            const title = line.slice(3);
            const c = sectionColors[colorIdx++ % sectionColors.length];
            inSection = true;
            html += `<div class="ar-section ar-${c}"><div class="ar-heading">${formatInline(title)}</div>`;
        } else if (line.startsWith('### ')) {
            closeUl();
            html += `<div class="ar-subheading">${formatInline(line.slice(4))}</div>`;
        } else if (line.startsWith('# ')) {
            closeUl(); closeSection();
            html += `<div class="ar-title">${formatInline(line.slice(2))}</div>`;
        } else if (line.startsWith('- ') || line.startsWith('* ')) {
            if (!inUl) { html += '<ul class="ar-list">'; inUl = true; }
            html += `<li>${formatInline(line.slice(2))}</li>`;
        } else if (line === '') {
            closeUl();
        } else {
            closeUl();
            html += `<p class="ar-p">${formatInline(line)}</p>`;
        }
    }
    closeUl(); closeSection();
    return html;
};
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

.accordion-enter-active { animation: fadeInUp 0.3s ease both; }
.accordion-leave-active { animation: fadeInUp 0.2s ease reverse both; }

/* -- Analysis Result Layout ----------------------------- */
:deep(.analysis-result) {
    font-size: 0.8125rem;
    line-height: 1.65;
    color: #374151;
}

:deep(.ar-title) {
    font-size: 1rem;
    font-weight: 700;
    color: #B92521;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #EFDBDC;
}

:deep(.ar-section) {
    border-radius: 0.625rem;
    border-left: 4px solid;
    padding: 0.75rem 0.875rem;
    margin-bottom: 0.625rem;
    background: #fafafa;
}
:deep(.ar-section:last-child) { margin-bottom: 0; }

:deep(.ar-heading) {
    font-weight: 700;
    font-size: 0.8125rem;
    margin-bottom: 0.5rem;
}

:deep(.ar-subheading) {
    font-weight: 600;
    font-size: 0.8rem;
    color: #6b7280;
    margin: 0.375rem 0 0.25rem;
}

:deep(.ar-p) {
    margin: 0.25rem 0 0.375rem;
    color: #4b5563;
}

:deep(.ar-list) {
    list-style: none;
    padding: 0;
    margin: 0.25rem 0 0;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

:deep(.ar-list li) {
    position: relative;
    padding-left: 1.1rem;
    color: #4b5563;
}

:deep(.ar-list li::before) {
    content: '';
    position: absolute;
    left: 0;
    top: 0.48rem;
    width: 0.4rem;
    height: 0.4rem;
    border-radius: 50%;
    background: currentColor;
    opacity: 0.55;
}

:deep(.ar-section strong) { font-weight: 700; }

/* Section color themes */
:deep(.ar-violet) {
    border-color: #F0404B;
    background: #FFF5F5;
}
:deep(.ar-violet .ar-heading) { color: #B92521; }
:deep(.ar-violet .ar-list li::before) { background: #F0404B; }

:deep(.ar-blue) {
    border-color: #B92521;
    background: #FEF0F0;
}
:deep(.ar-blue .ar-heading) { color: #A91127; }
:deep(.ar-blue .ar-list li::before) { background: #B92521; }

:deep(.ar-green) {
    border-color: #B74443;
    background: #F9ECEC;
}
:deep(.ar-green .ar-heading) { color: #B74443; }
:deep(.ar-green .ar-list li::before) { background: #B74443; }

:deep(.ar-amber) {
    border-color: #E48888;
    background: #FDF4F4;
}
:deep(.ar-amber .ar-heading) { color: #B92521; }
:deep(.ar-amber .ar-list li::before) { background: #E48888; }

:deep(.ar-rose) {
    border-color: #A91127;
    background: #FFF0F0;
}
:deep(.ar-rose .ar-heading) { color: #A91127; }
:deep(.ar-rose .ar-list li::before) { background: #A91127; }

:deep(.ar-teal) {
    border-color: #F18E8C;
    background: #FDF2F2;
}
:deep(.ar-teal .ar-heading) { color: #B92521; }
:deep(.ar-teal .ar-list li::before) { background: #F18E8C; }
</style>
