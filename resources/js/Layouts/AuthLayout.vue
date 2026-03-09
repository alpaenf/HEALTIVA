<template>
    <div class="auth-root font-poppins overflow-hidden">

        <!-- ======= DESKTOP (lg+) ======= -->
        <div class="hidden lg:block w-full h-screen relative bg-white">

            <!-- Subtle bg -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-50 to-white pointer-events-none z-0"></div>

            <!-- Form container â€” slides left/right -->
            <div
                class="auth-form-panel absolute top-0 h-full w-1/2 flex items-center justify-center px-12 z-10"
                :style="{ left: mode === 'login' ? '0%' : '50%' }"
            >
                <div class="w-full max-w-sm">
                    <Transition :name="formTransition" mode="out-in">
                        <div :key="mode" class="bg-white rounded-2xl shadow-xl ring-1 ring-gray-100 p-5">
                            <slot />
                        </div>
                    </Transition>
                </div>
            </div>

            <!-- Colored sliding panel â€” opposite direction -->
            <div
                class="auth-color-panel absolute top-0 h-full w-1/2 overflow-hidden z-20
                       bg-gradient-to-br from-primary to-primary-dark"
                :style="{ left: mode === 'login' ? '50%' : '0%' }"
            >
                <!-- Blobs -->
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute top-1/2 left-1/4 w-48 h-48 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                <!-- Dot grid -->
                <div class="absolute inset-0 opacity-[0.07]"
                    style="background-image:radial-gradient(circle,white 1.5px,transparent 1.5px);background-size:28px 28px;">
                </div>

                <!-- Panel content -->
                <div class="relative h-full flex flex-col p-12 z-10">
                    <img src="/images/logo.png" alt="HEALTIVA" class="h-20 w-auto max-w-[320px] object-contain brightness-0 invert" />

                    <div class="flex-1 flex items-center">
                        <Transition name="panel-content" mode="out-in">
                            <div :key="mode">
                                <h2 class="text-3xl xl:text-4xl font-bold text-white leading-tight">
                                    <template v-if="mode === 'login'">
                                        Belum Punya<br><span class="text-white/65">Akun?</span>
                                    </template>
                                    <template v-else>
                                        Monitor Kesehatan<br><span class="text-white/65">Lebih Cerdas</span>
                                    </template>
                                </h2>
                                <p class="text-white/60 text-sm mt-3 mb-8 leading-relaxed max-w-xs">
                                    <template v-if="mode === 'login'">
                                        Daftar sekarang dan mulai pantau kesehatan Anda dengan teknologi AI.
                                    </template>
                                    <template v-else>
                                        Pantau kondisi kesehatan Anda setiap hari dengan analisis AI yang akurat.
                                    </template>
                                </p>
                                <Link
                                    :href="mode === 'login' ? '/register' : '/login'"
                                    class="group inline-flex items-center gap-2 border-2 border-white/70 text-white
                                           font-semibold px-6 py-2.5 rounded-full hover:bg-white hover:text-primary
                                           transition-all duration-300 text-sm"
                                >
                                    {{ mode === 'login' ? 'Daftar Sekarang' : 'Masuk Sekarang' }}
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </Link>
                                <div class="mt-10 space-y-3">
                                    <div v-for="(ft, i) in features" :key="i" class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <span class="text-white/80 text-sm">{{ ft }}</span>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <p class="text-white/40 text-xs">Â© 2026 HEALTIVA Â· Sistem Monitor Kesehatan</p>
                </div>
            </div>
        </div>

        <!-- ======= MOBILE ======= -->
        <div class="lg:hidden min-h-screen flex flex-col bg-gray-50">

            <!-- Header -->
            <div class="relative overflow-hidden bg-gradient-to-br from-primary to-primary-dark">
                <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-white/10 rounded-full blur-lg"></div>
                <div class="relative z-10 px-5 pt-5 pb-4">
                    <img src="/images/logo.png" alt="HEALTIVA" class="h-12 w-auto max-w-[240px] object-contain brightness-0 invert mb-3" />
                    <!-- Tab pill -->
                    <div class="flex bg-white/20 backdrop-blur-sm rounded-2xl p-1 gap-1">
                        <Link href="/login"
                            class="flex-1 text-center text-sm font-semibold py-2 rounded-xl transition-all duration-300"
                            :class="mode === 'login' ? 'bg-white text-primary shadow-sm' : 'text-white/80 hover:bg-white/10'"
                        >Masuk</Link>
                        <Link href="/register"
                            class="flex-1 text-center text-sm font-semibold py-2 rounded-xl transition-all duration-300"
                            :class="mode === 'register' ? 'bg-white text-primary shadow-sm' : 'text-white/80 hover:bg-white/10'"
                        >Daftar</Link>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="flex-1 overflow-y-auto px-4 py-4">
                <Transition :name="mobileTransition" mode="out-in">
                    <div :key="mode" class="bg-white rounded-2xl shadow-lg p-4">
                        <slot />
                    </div>
                </Transition>
            </div>
        </div>

    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    mode: { type: String, default: 'login' }
});

const prevMode = ref(props.mode);
watch(() => props.mode, (_, old) => { prevMode.value = old; });

// Desktop: loginâ†’register = slide right (form moves right); registerâ†’login = slide left
const formTransition = computed(() =>
    prevMode.value === 'login' ? 'form-slide-right' : 'form-slide-left'
);
const mobileTransition = computed(() =>
    prevMode.value === 'login' ? 'mobile-left' : 'mobile-right'
);

const features = [
    'Pantau tekanan darah, gula darah & BMI',
    'Analisis kondisi kesehatan berbasis AI',
    'Riwayat data lengkap & terstruktur',
];
</script>

<style scoped>
.auth-root { min-height: 100vh; }

/* â”€â”€â”€ Desktop sliding panel â”€â”€â”€ */
.auth-form-panel,
.auth-color-panel {
    transition: left 0.75s cubic-bezier(0.76, 0, 0.24, 1);
}

/* â”€â”€â”€ Form card content (desktop) â”€â”€â”€ */
.form-slide-left-enter-active,
.form-slide-left-leave-active,
.form-slide-right-enter-active,
.form-slide-right-leave-active {
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.form-slide-left-enter-from  { opacity: 0; transform: translateX(28px); }
.form-slide-left-leave-to    { opacity: 0; transform: translateX(-28px); }
.form-slide-right-enter-from { opacity: 0; transform: translateX(-28px); }
.form-slide-right-leave-to   { opacity: 0; transform: translateX(28px); }

/* â”€â”€â”€ Panel inner content â”€â”€â”€ */
.panel-content-enter-active { transition: all 0.4s ease 0.3s; }
.panel-content-leave-active { transition: all 0.2s ease; }
.panel-content-enter-from   { opacity: 0; transform: translateY(16px); }
.panel-content-leave-to     { opacity: 0; transform: translateY(-8px); }

/* â”€â”€â”€ Mobile slide â”€â”€â”€ */
.mobile-left-enter-active,
.mobile-left-leave-active,
.mobile-right-enter-active,
.mobile-right-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.mobile-left-enter-from  { opacity: 0; transform: translateX(32px); }
.mobile-left-leave-to    { opacity: 0; transform: translateX(-32px); }
.mobile-right-enter-from { opacity: 0; transform: translateX(-32px); }
.mobile-right-leave-to   { opacity: 0; transform: translateX(32px); }
</style>
