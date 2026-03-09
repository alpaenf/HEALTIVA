<template>
    <div class="min-h-screen bg-gray-50 font-poppins lg:flex">

        <!-- â”€â”€ SIDEBAR (Desktop only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <aside class="hidden lg:flex flex-col w-60 min-h-screen bg-white border-r border-gray-100 fixed top-0 left-0 z-40 shadow-sm">
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

                <Link href="/admin/users"
                    :class="isActive('/admin/users') ? 'bg-[#FFF5F5] text-primary font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 group">
                    <span :class="isActive('/admin/users') ? 'bg-primary/10' : 'bg-gray-100 group-hover:bg-gray-200'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" :class="isActive('/admin/users') ? 'text-primary' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span>
                    Kelola Pengguna
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
            </nav>

            <!-- Bottom: user info + logout -->
            <div class="px-3 py-4 border-t border-gray-100">
                <button @click="logout"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:bg-red-50 hover:text-red-500 transition-all duration-200 group">
                    <span class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <AdminBottomNavBar class="lg:hidden" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import AdminBottomNavBar from '@/Components/AdminBottomNavBar.vue';

const page = usePage();

const isActive = (href) => {
    const p = window.location.pathname;
    return p === href || p.startsWith(href + '/');
};

const logout = () => router.post('/logout');

const pageTitle = computed(() => {
    const p = window.location.pathname;
    if (p.startsWith('/admin/knowledge')) return 'Knowledge Base AI';
    if (p.startsWith('/admin/users'))     return 'Kelola Pengguna';
    if (p === '/admin/dashboard')         return 'Dashboard Admin';
    return 'Admin Panel';
});
</script>
