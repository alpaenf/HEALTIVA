<template>
    <AppLayout>
        <Head title="Edukasi Kesehatan" />

        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-primary to-primary-dark text-white p-5 mb-5 animate-fade-in-down">
            <div class="absolute -top-6 -right-6 w-28 h-28 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-8 -left-4 w-20 h-20 bg-white/5 rounded-full"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Edukasi Kesehatan</h1>
                    <p class="text-white/70 text-xs mt-0.5">Video & artikel dari sumber terpercaya</p>
                </div>
                <!-- Live badge -->
                <div v-if="youtubeReady" class="ml-auto flex-shrink-0 flex items-center gap-1.5 bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                    <span class="text-[11px] font-semibold text-white">Live YouTube</span>
                </div>
            </div>
        </div>

        <!-- API Not Configured Banner -->
        <div v-if="!youtubeReady" class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 mb-4 text-sm text-amber-800">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-semibold">Video dari daftar statis</p>
                <p class="text-xs mt-0.5 text-amber-700">Tambahkan <code class="bg-amber-100 px-1 rounded">YOUTUBE_API_KEY</code> di <code class="bg-amber-100 px-1 rounded">.env</code> untuk video langsung dari YouTube secara otomatis.</p>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="flex gap-2 overflow-x-auto pb-2 mb-5 scrollbar-hide animate-fade-in-up">
            <button
                v-for="cat in categories" :key="cat.id"
                @click="activeCategory = cat.id"
                :class="[
                    'flex-shrink-0 px-4 py-2 rounded-full text-xs font-semibold transition-all',
                    activeCategory === cat.id
                        ? 'bg-primary text-white shadow-md shadow-primary/30'
                        : 'bg-white border border-gray-200 text-gray-600 hover:border-primary/40 hover:text-primary'
                ]">
                {{ cat.label }}
            </button>
        </div>

        <!-- Videos Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            <div
                v-for="(video, i) in filteredVideos" :key="video.id"
                class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg hover:shadow-primary/8 transition-all duration-300 animate-fade-in-up"
                :style="`animation-delay:${i * 60}ms`">

                <!-- Thumbnail / Embed Toggle -->
                <div class="relative aspect-video bg-gray-900 cursor-pointer group" @click="playVideo(video)">
                    <img
                        :src="`https://img.youtube.com/vi/${video.youtubeId}/mqdefault.jpg`"
                        :alt="video.title"
                        class="w-full h-full object-cover"
                        @error="(e) => e.target.style.display='none'"
                    />
                    <!-- Overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <!-- Play button -->
                    <div v-if="playingId !== video.id" class="absolute inset-0 flex items-center justify-center">
                        <div class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform shadow-xl">
                            <svg class="w-6 h-6 text-primary ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                    <!-- Badge -->
                    <span class="absolute top-2 right-2 bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                        {{ video.duration }}
                    </span>
                    <!-- Source badge -->
                    <span class="absolute top-2 left-2 bg-black/50 text-white text-[10px] px-2 py-0.5 rounded-full backdrop-blur-sm">
                        {{ video.source }}
                    </span>
                </div>

                <!-- Video iframe when playing -->
                <div v-if="playingId === video.id" class="aspect-video">
                    <iframe
                        :src="`https://www.youtube.com/embed/${video.youtubeId}?autoplay=1&rel=0`"
                        class="w-full h-full"
                        frameborder="0"
                        allow="autoplay; encrypted-media"
                        allowfullscreen>
                    </iframe>
                </div>

                <!-- Info -->
                <div class="p-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider" :class="getCategoryColor(video.category)">
                        {{ getCategoryLabel(video.category) }}
                    </span>
                    <h3 class="font-semibold text-gray-800 text-sm mt-1 leading-snug line-clamp-2">{{ video.title }}</h3>
                    <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">{{ video.desc }}</p>
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-[11px] text-gray-400">{{ video.channel }}</span>
                        <a :href="`https://www.youtube.com/watch?v=${video.youtubeId}`"
                            target="_blank"
                            class="text-[11px] text-primary font-medium hover:underline flex items-center gap-1">
                            YouTube
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sumber Terpercaya Banner -->
        <div class="bg-gradient-to-br from-[#FFF5F5] to-[#EFDBDC] rounded-2xl p-5 border border-[#F18E8C]/30 animate-fade-in-up">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Sumber Informasi Terpercaya</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Semua konten edukasi diambil dari saluran resmi dan sumber medis kredibel.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span v-for="src in sources" :key="src" class="text-xs bg-white border border-gray-200 px-2.5 py-1 rounded-full text-gray-600 font-medium">{{ src }}</span>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    videos:       { type: Array,   default: () => [] },
    youtubeReady: { type: Boolean, default: false },
    error:        { type: String,  default: null },
});

const activeCategory = ref('all');
const playingId = ref(null);

const playVideo = (video) => {
    playingId.value = playingId.value === video.id ? null : video.id;
};

const categories = [
    { id: 'all',        label: 'Semua' },
    { id: 'heart',      label: 'Jantung & TD' },
    { id: 'diabetes',   label: 'Gula Darah' },
    { id: 'nutrition',  label: 'Gizi & BMI' },
    { id: 'lifestyle',  label: 'Gaya Hidup' },
    { id: 'mental',     label: 'Kesehatan Mental' },
];

const getCategoryLabel = (id) => categories.find(c => c.id === id)?.label ?? id;
const getCategoryColor = (id) => {
    const map = {
        heart:     'text-primary',
        diabetes:  'text-[#B74443]',
        nutrition: 'text-green-600',
        lifestyle: 'text-blue-600',
        mental:    'text-purple-600',
    };
    return map[id] ?? 'text-primary';
};

const filteredVideos = computed(() => {
    if (activeCategory.value === 'all') return props.videos;
    return props.videos.filter(v => v.category === activeCategory.value);
});

const sources = ['Kemenkes RI', 'WHO Indonesia', 'PERKENI', 'PERKI', 'RSCM', 'IDI'];
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
}
</style>
