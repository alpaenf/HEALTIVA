<template>
    <div class="min-h-screen bg-gradient-to-br from-primary to-primary-dark flex font-poppins">

        <!-- LEFT PANEL (desktop only) -->
        <div class="hidden lg:flex flex-col justify-between w-1/2 xl:w-5/12 p-12 relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute -top-20 -left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-0 w-56 h-56 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>

            <div class="relative z-10 animate-fade-in-left">
                <div class="flex items-center mb-10">
                    <img src="/images/logo.png" alt="Healtiva" class="h-[120px] w-auto brightness-0 invert" />
                </div>

                <h2 class="text-3xl font-bold text-white leading-tight mb-3">Monitor Kesehatan<br><span class="text-white/70">Lebih Cerdas</span></h2>
                <p class="text-white/60 text-sm leading-relaxed">Pantau kondisi kesehatan Anda setiap hari dengan analisis AI yang akurat dan mudah dipahami.</p>

                <div class="mt-8 space-y-3">
                    <div v-for="(ft, i) in features" :key="i" class="flex items-center gap-3 animate-fade-in-left" :style="`animation-delay:${(i+1)*100}ms`">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-white/80 text-sm">{{ ft }}</span>
                    </div>
                </div>
            </div>

            <p class="relative z-10 text-white/40 text-xs">© 2026 Medix · Sistem Monitor Kesehatan</p>
        </div>

        <!-- RIGHT PANEL (auth card) -->
        <div class="flex-1 flex flex-col items-center justify-center px-4 lg:px-16 py-8">
            <!-- Card -->
            <div class="w-full max-w-xs sm:max-w-sm lg:max-w-md bg-white rounded-2xl shadow-2xl p-4 lg:p-6 animate-scale-in">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
const features = [
    'Pantau tekanan darah, gula darah & BMI',
    'Analisis kondisi kesehatan berbasis AI',
    'Riwayat data lengkap & terstruktur',
];
</script>
