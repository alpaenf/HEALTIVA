<template>
    <div class="min-h-screen bg-gray-50 font-poppins lg:flex">

        <!-- ── SIDEBAR (Desktop) ──────────────────────────────────── -->
        <aside class="hidden lg:flex flex-col w-60 h-screen bg-white border-r border-gray-100 fixed top-0 left-0 z-40 shadow-sm">
            <!-- Logo -->
            <div class="px-5 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary to-primary-dark rounded-xl flex items-center justify-center shadow-sm shadow-primary/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800 leading-none">HEALTIVA</p>
                        <p class="text-[10px] text-primary font-semibold mt-0.5">Portal Kader</p>
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <NavLink href="/kader/dashboard" :active="isActive('/kader/dashboard')">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </template>
                    Dashboard
                </NavLink>

                <NavLink href="/kader/pasien" :active="isActive('/kader/pasien')">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </template>
                    Data Pasien
                </NavLink>

                <div class="my-2 border-t border-gray-100"></div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Informasi</p>

                <NavLink href="/edukasi" :active="isActive('/edukasi')">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </template>
                    Edukasi
                </NavLink>

                <NavLink href="/standar-normal" :active="isActive('/standar-normal')">
                    <template #icon>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </template>
                    Standar Normal
                </NavLink>
            </nav>

            <!-- Bottom: user info + logout -->
            <div class="px-3 py-4 border-t border-gray-100 flex flex-col gap-1">
                <div class="px-3 py-2.5 border border-gray-100 bg-gray-50 rounded-xl mb-1.5">
                    <p class="text-xs font-bold text-gray-800 truncate">{{ user?.name }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ user?.email }}</p>
                    <div class="mt-1.5 flex items-center">
                        <span class="px-2 py-0.5 rounded bg-primary/10 text-primary text-[9px] font-bold uppercase tracking-widest">Kader Posyandu</span>
                    </div>
                </div>

                <button @click="logout"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 group">
                    <span class="w-8 h-8 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition-colors flex-shrink-0 shadow-sm">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </span>
                    Keluar
                </button>
            </div>
        </aside>

        <!-- ── MAIN CONTENT ────────────────────────────────────────── -->
        <div class="flex-1 lg:ml-60">
            <!-- Top bar (desktop) -->
            <header class="hidden lg:flex items-center justify-between px-8 py-4 bg-white border-b border-gray-100 sticky top-0 z-30">
                <h2 class="text-sm font-semibold text-gray-500">{{ pageTitle }}</h2>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <span class="w-2 h-2 bg-green-400 rounded-full inline-block animate-pulse"></span>
                    Kader Posyandu
                </div>
            </header>

            <!-- MOBILE HEADER -->
            <header class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100 px-4 py-3 flex items-center">
                <img src="/images/logo.png" alt="Healtiva" class="h-10 w-auto" />
            </header>

            <!-- ── MOBILE BOTTOM NAV ──────────────────────────────── -->
            <div class="lg:hidden">

                <!-- Collapsed pill -->
                <Transition name="pill-up">
                    <div v-if="collapsed"
                        @click="collapsed = false"
                        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex items-center gap-2 bg-white/80 backdrop-blur-lg border border-white/40 shadow-lg rounded-full px-5 py-2.5 cursor-pointer pointer-events-auto select-none">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-600">Menu</span>
                    </div>
                </Transition>

                <!-- Full navbar -->
                <Transition name="nav-slide">
                <div v-if="!collapsed" class="fixed bottom-6 left-0 right-0 z-50 px-4 flex flex-col items-center pointer-events-none">
                    <!-- Collapse handle -->
                    <button @click="collapsed = true"
                        class="mb-1.5 pointer-events-auto flex items-center gap-1.5 px-4 py-1 rounded-full bg-white/70 backdrop-blur-md border border-white/40 shadow text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span class="text-[10px] font-semibold">Sembunyikan</span>
                    </button>
                    <nav class="w-full max-w-[390px] sm:max-w-[420px] bg-white/75 backdrop-blur-lg border border-white/40 shadow-[0_8px_32px_rgba(0,0,0,0.1)] rounded-[2rem] pointer-events-auto">
                        <div class="px-5 py-2">
                            <div class="flex items-center justify-between w-full">
                                <!-- Dashboard -->
                                <button @click="mobileNav('dashboard', '/kader/dashboard')"
                                    class="flex flex-col items-center gap-1 flex-1 min-w-0 relative group outline-none">
                                    <span v-if="rippling === 'dashboard'"
                                        class="absolute top-0 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-primary/20 animate-nav-ripple pointer-events-none"></span>
                                    <span :class="['relative flex items-center justify-center rounded-full transition-all duration-300',
                                        isActive('/kader/dashboard') ? 'w-16 h-8 bg-gradient-to-br from-primary to-primary-dark shadow-lg shadow-primary/40' : 'w-11 h-11 bg-gray-100/80 group-hover:bg-gray-200/80',
                                        tapping === 'dashboard' ? 'scale-90' : '']">
                                        <svg class="w-5 h-5 transition-all duration-300" :class="isActive('/kader/dashboard') ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </span>
                                    <span :class="['text-[10px] font-semibold leading-none transition-all duration-200', isActive('/kader/dashboard') ? 'text-primary' : 'text-gray-400']">Dashboard</span>
                                </button>

                                <!-- Data Pasien -->
                                <button @click="mobileNav('pasien', '/kader/pasien')"
                                    class="flex flex-col items-center gap-1 flex-1 min-w-0 relative group outline-none">
                                    <span v-if="rippling === 'pasien'"
                                        class="absolute top-0 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-primary/20 animate-nav-ripple pointer-events-none"></span>
                                    <span :class="['relative flex items-center justify-center rounded-full transition-all duration-300',
                                        isActive('/kader/pasien') ? 'w-16 h-8 bg-gradient-to-br from-primary to-primary-dark shadow-lg shadow-primary/40' : 'w-11 h-11 bg-gray-100/80 group-hover:bg-gray-200/80',
                                        tapping === 'pasien' ? 'scale-90' : '']">
                                        <svg class="w-5 h-5 transition-all duration-300" :class="isActive('/kader/pasien') ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </span>
                                    <span :class="['text-[10px] font-semibold leading-none transition-all duration-200', isActive('/kader/pasien') ? 'text-primary' : 'text-gray-400']">Pasien</span>
                                </button>

                                <!-- Lainnya -->
                                <button @click="showMore = !showMore"
                                    class="flex flex-col items-center gap-1 flex-1 min-w-0 relative group outline-none">
                                    <span :class="['relative flex items-center justify-center rounded-full transition-all duration-300',
                                        showMore ? 'w-16 h-8 bg-gradient-to-br from-primary to-primary-dark shadow-lg shadow-primary/40' : 'w-11 h-11 bg-gray-100/80 group-hover:bg-gray-200/80']">
                                        <svg class="w-5 h-5 transition-all duration-300" :class="showMore ? 'text-white' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                        </svg>
                                    </span>
                                    <span :class="['text-[10px] font-semibold leading-none transition-all duration-200', showMore ? 'text-primary' : 'text-gray-400']">Lainnya</span>
                                </button>
                            </div>
                        </div>
                    </nav>
                </div>
                </Transition>

                <!-- Backdrop -->
                <Transition name="fade-backdrop">
                    <div v-if="showMore" @click="showMore = false"
                        class="fixed inset-0 z-40 bg-black/30 backdrop-blur-sm"></div>
                </Transition>

                <!-- More Panel -->
                <Transition name="slide-up">
                    <div v-if="showMore" class="fixed bottom-[80px] left-0 right-0 z-50 max-w-lg mx-auto px-4">
                        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                            <div class="px-5 py-3.5 border-b border-gray-50">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Menu Lainnya</p>
                            </div>
                            <div class="p-2 grid grid-cols-1 gap-1">
                                <button @click="mobileTo('/edukasi')"
                                    class="flex items-center gap-4 px-4 py-3.5 rounded-xl hover:bg-[#FFF5F5] transition text-left group w-full">
                                    <div class="w-10 h-10 bg-[#EFDBDC] rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-800">Edukasi Kesehatan</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Video &amp; artikel kesehatan</p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <button @click="mobileTo('/standar-normal')"
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
                                <a href="/"
                                    class="flex items-center gap-4 px-4 py-3.5 rounded-xl hover:bg-gray-50 transition text-left group w-full">
                                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-800">Website Utama</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Kembali ke halaman depan</p>
                                    </div>
                                </a>
                                <button @click="logout"
                                    class="flex items-center gap-4 px-4 py-3.5 rounded-xl hover:bg-red-50 transition text-left group w-full">
                                    <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-primary">Keluar</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Keluar dari portal kader</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Flash Messages -->
            <transition name="flash">
                <div v-if="$page.props.flash?.success || $page.props.flash?.error"
                    class="fixed z-50 top-16 lg:top-4 left-0 right-0 lg:left-60 px-4 lg:px-8 pt-2 pointer-events-none">
                    <div v-if="$page.props.flash?.success"
                        class="max-w-xl lg:max-w-md bg-[#FDD3CF] border border-[#F18E8C] text-[#B92521] rounded-xl px-4 py-3 text-sm font-medium shadow-md pointer-events-auto">
                        {{ $page.props.flash.success }}
                    </div>
                    <div v-if="$page.props.flash?.error"
                        class="max-w-xl lg:max-w-md bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm font-medium shadow-md pointer-events-auto">
                        {{ $page.props.flash.error }}
                    </div>
                </div>
            </transition>

            <main class="px-4 lg:px-8 py-6 pt-20 lg:pt-8 pb-28 lg:pb-10 min-h-screen">
                <slot />
            </main>
        </div>
    </div>

    <!-- Logout Modal -->
    <LogoutModal
        :show="showLogoutModal"
        @confirm="doLogout"
        @cancel="showLogoutModal = false"
    />
</template>

<script setup>
import { ref, computed, h } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import LogoutModal from '@/Components/LogoutModal.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const showMore = ref(false);
const tapping  = ref(null);
const rippling  = ref(null);
const collapsed = ref(false);
const showLogoutModal = ref(false);

const doLogout = () => {
    showLogoutModal.value = false;
    router.post('/logout');
};

const isActive = (href) => {
    const p = window.location.pathname;
    return p === href || p.startsWith(href + '/');
};

const logout = () => {
    showLogoutModal.value = true;
};

const mobileNav = (name, href) => {
    if (isActive(href)) return;
    showMore.value = false;
    tapping.value  = name;
    rippling.value = name;
    setTimeout(() => { tapping.value  = null; }, 180);
    setTimeout(() => { rippling.value = null; }, 500);
    setTimeout(() => { router.visit(href); }, 120);
};

const mobileTo = (href) => {
    showMore.value = false;
    setTimeout(() => { router.visit(href); }, 150);
};

const pageTitle = computed(() => {
    const p = window.location.pathname;
    if (p.startsWith('/kader/pasien')) return 'Data Pasien';
    if (p === '/kader/dashboard')     return 'Dashboard Kader';
    if (p === '/edukasi')             return 'Edukasi Kesehatan';
    if (p === '/standar-normal')      return 'Standar Normal';
    return 'Portal Kader';
});

const NavLink = {
    props: ['href', 'active'],
    setup(props, { slots }) {
        return () => h(Link, {
            href: props.href,
            class: `flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 group ${
                props.active
                    ? 'bg-[#FFF5F5] text-primary font-semibold'
                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'
            }`,
        }, () => [
            h('span', {
                class: `w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0 ${
                    props.active ? 'bg-primary/10' : 'bg-gray-100 group-hover:bg-gray-200'
                }`,
            }, [
                h('span', { class: props.active ? 'text-primary' : 'text-gray-500' }, slots.icon?.()),
            ]),
            h('span', {}, slots.default?.()),
        ]);
    }
};
</script>

<style scoped>
.flash-enter-active { animation: fadeInDown 0.35s ease both; }
.flash-leave-active { animation: fadeInDown 0.25s ease reverse both; }

@keyframes nav-ripple {
    0%   { transform: translateX(-50%) scale(0.4); opacity: 0.7; }
    100% { transform: translateX(-50%) scale(2.2); opacity: 0; }
}
.animate-nav-ripple { animation: nav-ripple 0.5s ease-out forwards; }

.fade-backdrop-enter-active, .fade-backdrop-leave-active { transition: opacity 0.25s ease; }
.fade-backdrop-enter-from, .fade-backdrop-leave-to { opacity: 0; }

.slide-up-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-up-leave-active { transition: all 0.2s ease; }
.slide-up-enter-from   { opacity: 0; transform: translateY(24px); }
.slide-up-leave-to     { opacity: 0; transform: translateY(16px); }

/* Navbar collapse/expand */
.nav-slide-enter-active { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
.nav-slide-leave-active { transition: all 0.2s ease; }
.nav-slide-enter-from   { opacity: 0; transform: translateY(32px); }
.nav-slide-leave-to     { opacity: 0; transform: translateY(32px); }

/* Collapsed pill */
.pill-up-enter-active { transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
.pill-up-leave-active { transition: all 0.15s ease; }
.pill-up-enter-from   { opacity: 0; transform: translateX(-50%) translateY(16px); }
.pill-up-leave-to     { opacity: 0; transform: translateX(-50%) translateY(16px); }
</style>
