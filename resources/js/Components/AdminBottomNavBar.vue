<template>
    <div>
        <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-100 shadow-[0_-4px_24px_rgba(0,0,0,0.07)]">
            <div class="max-w-lg mx-auto px-1">
                <div class="flex items-end justify-around py-2 pb-3">
                    <button
                        v-for="item in navItems"
                        :key="item.name"
                        @click="navigate(item)"
                        class="flex flex-col items-center gap-1 min-w-[72px] relative group outline-none"
                    >
                        <!-- Ripple -->
                        <span
                            v-if="rippling === item.name"
                            class="absolute top-0 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-primary/20 animate-nav-ripple pointer-events-none"
                        />

                        <!-- Icon circle -->
                        <span
                            :class="[
                                'relative flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300',
                                isActive(item.href)
                                    ? 'bg-gradient-to-br from-primary to-primary-dark shadow-lg shadow-primary/40 scale-110'
                                    : 'bg-gray-100/80 group-hover:bg-gray-200/80',
                                tapping === item.name ? 'scale-90' : '',
                            ]"
                        >
                            <component
                                :is="item.icon"
                                :class="[
                                    'w-5 h-5 transition-all duration-300',
                                    isActive(item.href) ? 'text-white' : 'text-gray-400 group-hover:text-gray-600',
                                ]"
                            />
                            <span
                                v-if="isActive(item.href)"
                                class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-primary rounded-full"
                            />
                        </span>

                        <!-- Label -->
                        <span
                            :class="[
                                'text-[10px] font-semibold leading-none transition-all duration-200',
                                isActive(item.href) ? 'text-primary-dark' : 'text-gray-400',
                            ]"
                        >{{ item.label }}</span>
                    </button>

                    <!-- More button -->
                    <button @click="toggleMore"
                        class="flex flex-col items-center gap-1 min-w-[72px] relative group outline-none">
                        <span :class="[
                            'relative flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300',
                            showMore ? 'bg-gradient-to-br from-primary to-primary-dark shadow-lg shadow-primary/40 scale-110' : 'bg-gray-100/80 group-hover:bg-gray-200/80',
                        ]">
                            <svg class="w-5 h-5 transition-all duration-300" :class="showMore ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                            </svg>
                        </span>
                        <span :class="['text-[10px] font-semibold leading-none transition-all duration-200', showMore ? 'text-primary' : 'text-gray-400']">Lainnya</span>
                    </button>
                </div>
            </div>
            <div class="h-safe-area-inset-bottom bg-transparent"></div>
        </nav>

        <!-- Backdrop -->
        <Transition name="fade-backdrop">
            <div v-if="showMore" @click="showMore = false"
                class="fixed inset-0 z-40 bg-black/30 backdrop-blur-sm">
            </div>
        </Transition>

        <!-- More Panel (Slide up) -->
        <Transition name="slide-up">
            <div v-if="showMore"
                class="fixed bottom-[80px] left-0 right-0 z-50 max-w-lg mx-auto px-4">
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-50">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menu Lainnya</p>
                    </div>

                    <div class="p-2 grid grid-cols-1 gap-1">
                        <button @click="goTo('/edukasi')"
                            class="flex items-center gap-4 px-4 py-3.5 rounded-xl hover:bg-[#FFF5F5] transition text-left group w-full">
                            <div class="w-10 h-10 bg-[#EFDBDC] rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">Edukasi Kesehatan</p>
                                <p class="text-xs text-gray-400 mt-0.5">Video & artikel kesehatan</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <button @click="goTo('/standar-normal')"
                            class="flex items-center gap-4 px-4 py-3.5 rounded-xl hover:bg-[#FFF5F5] transition text-left group w-full">
                            <div class="w-10 h-10 bg-[#FDD3CF] rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">Standar Normal</p>
                                <p class="text-xs text-gray-400 mt-0.5">Referensi nilai pemeriksaan</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <div class="mx-4 my-1 border-t border-gray-100"></div>

                        <button @click="logout"
                            class="flex items-center gap-4 px-4 py-3.5 rounded-xl hover:bg-red-50 transition text-left group w-full">
                            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-primary">Logout</p>
                                <p class="text-xs text-gray-400 mt-0.5">Keluar dari akun Admin</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, h } from 'vue';
import { router } from '@inertiajs/vue3';

const tapping = ref(null);
const rippling = ref(null);
const showMore = ref(false);

const isActive = (href) => window.location.pathname === href;

const toggleMore = () => {
    showMore.value = !showMore.value;
};

const goTo = (href) => {
    showMore.value = false;
    setTimeout(() => { router.visit(href); }, 150);
};

const navigate = (item) => {
    if (isActive(item.href)) return;
    showMore.value = false;
    tapping.value = item.name;
    rippling.value = item.name;
    setTimeout(() => { tapping.value = null; }, 180);
    setTimeout(() => { rippling.value = null; }, 500);
    setTimeout(() => { router.visit(item.href); }, 120);
};

const emit = defineEmits(['logout']);
const logout = () => {
    showMore.value = false;
    emit('logout');
};

const DashboardIcon = {
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
    ])
};

const UsersIcon = {
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' })
    ])
};

const KnowledgeIcon = {
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z' })
    ])
};

const KaderIcon = {
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
    ])
};

const EdukasiIcon = {
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })
    ])
};

const StandarIcon = {
    render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' })
    ])
};

const navItems = [
    { name: 'dashboard', href: '/admin/dashboard', label: 'Dashboard', icon: DashboardIcon },
    { name: 'patients',  href: '/admin/patients',  label: 'Pasien', icon: UsersIcon },
    { name: 'kaders',    href: '/admin/kaders',    label: 'Kader',  icon: KaderIcon },
    { name: 'knowledge', href: '/admin/knowledge', label: 'Knowledge', icon: KnowledgeIcon },
];
</script>

<style scoped>
@keyframes nav-ripple {
    0%   { transform: translateX(-50%) scale(0.4); opacity: 0.7; }
    100% { transform: translateX(-50%) scale(2.2); opacity: 0; }
}
.animate-nav-ripple {
    animation: nav-ripple 0.5s ease-out forwards;
}

/* Backdrop fade */
.fade-backdrop-enter-active, .fade-backdrop-leave-active { transition: opacity 0.25s ease; }
.fade-backdrop-enter-from, .fade-backdrop-leave-to { opacity: 0; }

/* Panel slide up */
.slide-up-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-up-leave-active { transition: all 0.2s ease; }
.slide-up-enter-from { opacity: 0; transform: translateY(24px); }
.slide-up-leave-to   { opacity: 0; transform: translateY(16px); }

.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
