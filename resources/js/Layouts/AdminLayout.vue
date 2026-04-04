<template>
    <div class="min-h-screen bg-gray-50 font-poppins lg:flex">

        <!-- â”€â”€ SIDEBAR (Desktop only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <aside class="hidden lg:flex flex-col w-60 h-screen bg-white border-r border-gray-100 fixed top-0 left-0 z-40 shadow-sm">
            <!-- Logo -->
            <div class="px-5 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-primary to-primary-dark rounded-xl flex items-center justify-center shadow-sm shadow-primary/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800 leading-none">HEALTIVA</p>
                        <p class="text-[10px] text-primary font-semibold mt-0.5">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-2">Menu Utama</p>

                <Link href="/admin/dashboard"
                    :class="isActive('/admin/dashboard') ? 'bg-[#FFF5F5] text-primary font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 group">
                    <span :class="isActive('/admin/dashboard') ? 'bg-primary/10' : 'bg-gray-100 group-hover:bg-gray-200'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" :class="isActive('/admin/dashboard') ? 'text-primary' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </span>
                    Dashboard
                </Link>

                <Link href="/admin/patients"
                    :class="isActive('/admin/patients') ? 'bg-[#FFF5F5] text-primary font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 group">
                    <span :class="isActive('/admin/patients') ? 'bg-primary/10' : 'bg-gray-100 group-hover:bg-gray-200'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" :class="isActive('/admin/patients') ? 'text-primary' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span>
                    Kelola Pasien
                </Link>

                <Link href="/admin/kaders"
                    :class="isActive('/admin/kaders') ? 'bg-[#FFF5F5] text-primary font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 group">
                    <span :class="isActive('/admin/kaders') ? 'bg-primary/10' : 'bg-gray-100 group-hover:bg-gray-200'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" :class="isActive('/admin/kaders') ? 'text-primary' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    Kelola Kader
                </Link>

                <Link href="/admin/knowledge"
                    :class="isActive('/admin/knowledge') ? 'bg-[#FFF5F5] text-primary font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 group">
                    <span :class="isActive('/admin/knowledge') ? 'bg-primary/10' : 'bg-gray-100 group-hover:bg-gray-200'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" :class="isActive('/admin/knowledge') ? 'text-primary' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </span>
                    Knowledge Base AI
                </Link>

                <div class="my-2 border-t border-gray-100"></div>
                <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Informasi Tambahan</p>

                <Link href="/edukasi"
                    :class="isActive('/edukasi') ? 'bg-[#FFF5F5] text-primary font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 group">
                    <span :class="isActive('/edukasi') ? 'bg-primary/10' : 'bg-gray-100 group-hover:bg-gray-200'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" :class="isActive('/edukasi') ? 'text-primary' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    Edukasi Kesehatan
                </Link>

                <Link href="/standar-normal"
                    :class="isActive('/standar-normal') ? 'bg-[#FFF5F5] text-primary font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 group">
                    <span :class="isActive('/standar-normal') ? 'bg-primary/10' : 'bg-gray-100 group-hover:bg-gray-200'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" :class="isActive('/standar-normal') ? 'text-primary' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </span>
                    Standar Normal
                </Link>
            </nav>

            <!-- Bottom: user info + logout -->
            <div class="px-3 py-4 border-t border-gray-100 flex flex-col gap-1">
                <div class="px-3 py-2.5 border border-gray-100 bg-gray-50 rounded-xl mb-1.5">
                    <p class="text-xs font-bold text-gray-800 truncate">{{ $page.props.auth?.user?.name }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ $page.props.auth?.user?.email }}</p>
                    <div class="mt-1.5 flex items-center">
                        <span class="px-2 py-0.5 rounded bg-primary/10 text-primary text-[9px] font-bold uppercase tracking-widest">Administrator</span>
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

        <!-- â”€â”€ MAIN CONTENT â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <div class="flex-1 lg:ml-60">
            <!-- Top bar (desktop) -->
            <header class="hidden lg:flex items-center justify-between px-8 py-4 bg-white border-b border-gray-100 sticky top-0 z-30">
                <h2 class="text-sm font-semibold text-gray-500">
                    {{ pageTitle }}
                </h2>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <span class="w-2 h-2 bg-green-400 rounded-full inline-block animate-pulse"></span>
                    Admin Mode Aktif
                </div>
            </header>

            <main class="px-4 lg:px-8 py-6 pb-28 lg:pb-8">
                <slot />
            </main>
        </div>

        <!-- â”€â”€ BOTTOM NAV (Mobile only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <!-- Logout Modal -->
        <LogoutModal
            :show="showLogoutModal"
            @confirm="doLogout"
            @cancel="showLogoutModal = false"
        />

        <!-- ── BOTTOM NAV (Mobile only) ──────────────────────────── -->
        <AdminBottomNavBar class="lg:hidden" @logout="showLogoutModal = true" />
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import AdminBottomNavBar from '@/Components/AdminBottomNavBar.vue';
import LogoutModal from '@/Components/LogoutModal.vue';

const page = usePage();
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

const pageTitle = computed(() => {
    const p = window.location.pathname;
    if (p.startsWith('/admin/knowledge')) return 'Knowledge Base AI';
    if (p.startsWith('/admin/patients'))  return 'Kelola Pasien';
    if (p.startsWith('/admin/kaders'))    return 'Kelola Kader';
    if (p === '/admin/dashboard')         return 'Dashboard Admin';
    return 'Admin Panel';
});
</script>
