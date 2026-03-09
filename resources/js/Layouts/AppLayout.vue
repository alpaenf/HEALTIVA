<template>
    <div class="min-h-screen bg-app font-poppins">

        <!-- ─── DESKTOP SIDEBAR (lg+) ─────────────────────────────── -->
        <aside class="hidden lg:flex flex-col fixed left-0 top-0 bottom-0 w-64 bg-white shadow-lg z-40 border-r border-gray-100">
            <!-- Logo -->
            <div class="flex items-center px-2 -mb-5 border-b border-gray-100">
                <img src="/images/logo.png" alt="Healtiva" class="h-[120px] w-auto" />
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <SidebarLink href="/dashboard" :active="isActive('/dashboard')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </template>
                    Dashboard
                </SidebarLink>
                <SidebarLink href="/input-data" :active="isActive('/input-data')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </template>
                    Input Data
                </SidebarLink>
                <SidebarLink href="/ai-analysis" :active="isActive('/ai-analysis')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" />
                        </svg>
                    </template>
                    Analisis AI
                </SidebarLink>
                <SidebarLink href="/history" :active="isActive('/history')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </template>
                    Riwayat
                </SidebarLink>
                <SidebarLink href="/profile" :active="isActive('/profile')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </template>
                    Profil
                </SidebarLink>

                <!-- Divider -->
                <div class="my-2 border-t border-gray-100"></div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Fitur Lainnya</p>

                <SidebarLink href="/ai-chat" :active="isActive('/ai-chat')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </template>
                    Chat AI Kesehatan
                </SidebarLink>
                <SidebarLink href="/edukasi" :active="isActive('/edukasi')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </template>
                    Edukasi Kesehatan
                </SidebarLink>
                <SidebarLink href="/standar-normal" :active="isActive('/standar-normal')">
                    <template #icon>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </template>
                    Standar Normal
                </SidebarLink>
            </nav>

            <!-- Bottom admin / logout -->
            <div class="px-3 py-4 border-t border-gray-100 space-y-1">
                <a v-if="user?.role === 'admin'" href="/admin/dashboard"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-[#B92521] bg-[#FDD3CF] hover:bg-[#F18E8C]/30 transition font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Admin Panel
                </a>
                <form @submit.prevent="logout">
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:bg-red-50 hover:text-red-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- ─── MOBILE TOP HEADER ──────────────────────────────────── -->
        <header class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100">
            <div class="px-2 py-0.5 flex items-center justify-between">
                <div class="flex items-center">
                    <img src="/images/logo.png" alt="Healtiva" class="h-[60px] w-auto" />
                </div>
                <Link href="/profile" class="block w-10 h-10 rounded-xl overflow-hidden shadow flex-shrink-0 active:opacity-80 transition">
                    <img v-if="user?.avatar" :src="userAvatarUrl" class="w-full h-full object-cover" alt="avatar" />
                    <div v-else class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white text-sm font-bold">
                        {{ userInitial }}
                    </div>
                </Link>
            </div>
        </header>

        <!-- ─── DESKTOP TOP BAR ────────────────────────────────────── -->
        <div class="hidden lg:flex fixed top-0 left-64 right-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-3 items-center justify-between">
            <div>
                <p class="text-xs text-gray-400">{{ greeting }},</p>
                <p class="font-semibold text-gray-800 text-sm leading-tight">{{ user?.name }}</p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1.5 rounded-full">{{ currentDate }}</span>
        </div>

        <!-- ─── FLASH MESSAGES ─────────────────────────────────────── -->
        <transition name="flash">
            <div v-if="$page.props.flash?.success || $page.props.flash?.error"
                class="fixed z-50 top-16 lg:top-4 left-0 right-0 lg:left-64 px-4 lg:px-8 pt-2 pointer-events-none">
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

        <!-- ─── MAIN CONTENT ───────────────────────────────────────── -->
        <main class="lg:ml-64 px-4 lg:px-8 xl:px-10 pt-20 pb-28 lg:pt-20 lg:pb-10 min-h-screen">
            <Transition name="page-fade" mode="out-in">
                <div :key="$page.url">
                    <slot />
                </div>
            </Transition>
        </main>

        <!-- ─── MOBILE BOTTOM NAV ──────────────────────────────────── -->
        <BottomNavBar class="lg:hidden" />
    </div>
</template>

<script setup>
import { computed, h } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import BottomNavBar from '@/Components/BottomNavBar.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const userInitial = computed(() => user.value?.name?.charAt(0)?.toUpperCase() || 'U');
const userAvatarUrl = computed(() => {
    const avatar = user.value?.avatar;
    if (!avatar) return null;
    if (avatar.startsWith('http')) return avatar;
    return '/storage/' + avatar;
});
const isActive = (href) => window.location.pathname === href;

const greeting = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return 'Selamat Pagi';
    if (h < 15) return 'Selamat Siang';
    if (h < 18) return 'Selamat Sore';
    return 'Selamat Malam';
});

const currentDate = computed(() =>
    new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' })
);

const logout = () => router.post('/logout');

const SidebarLink = {
    props: ['href', 'active'],
    setup(props, { slots }) {
        return () => h(Link, {
            href: props.href,
            class: `flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group ${
                props.active
                    ? 'bg-gradient-to-r from-primary/10 to-primary/5 text-primary'
                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800'
            }`,
        }, () => [
            h('span', {
                class: `flex-shrink-0 ${props.active ? 'text-primary' : 'text-gray-400 group-hover:text-gray-600'}`,
            }, slots.icon?.()),
            h('span', {}, slots.default?.()),
            props.active ? h('span', { class: 'ml-auto w-1.5 h-1.5 rounded-full bg-primary' }) : null,
        ]);
    }
};
</script>

<style scoped>
.flash-enter-active { animation: fadeInDown 0.35s ease both; }
.flash-leave-active { animation: fadeInDown 0.25s ease reverse both; }

.page-fade-enter-active {
    animation: pageFadeIn 0.28s cubic-bezier(0.22, 0.61, 0.36, 1) both;
}
.page-fade-leave-active {
    animation: pageFadeOut 0.18s ease both;
}
@keyframes pageFadeIn {
    from { opacity: 0; transform: translateY(14px) scale(0.99); }
    to   { opacity: 1; transform: translateY(0)   scale(1); }
}
@keyframes pageFadeOut {
    from { opacity: 1; transform: translateY(0); }
    to   { opacity: 0; transform: translateY(-8px); }
}
</style>

