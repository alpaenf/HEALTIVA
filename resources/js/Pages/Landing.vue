<template>
    <div class="min-h-screen bg-white font-poppins overflow-x-hidden">

        <!-- â”€â”€ FLOATING NAVBAR â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <div class="fixed top-4 left-0 right-0 z-50 flex justify-center px-6 animate-nav-drop">
            <nav :class="['w-full max-w-6xl backdrop-blur-lg rounded-full px-6 flex items-center justify-between border transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] hover:shadow-2xl hover:bg-white/95',
                navScrolled
                    ? 'bg-white/60 shadow-md shadow-black/5 border-white/40 py-1.5'
                    : 'bg-white shadow-xl shadow-black/10 border-gray-100 py-2']">
                
                <div class="relative flex items-center h-10 w-40 group cursor-pointer">
                    <img src="/images/logo.png" alt="HEALTIVA" class="absolute top-1/2 -translate-y-1/2 -left-2 max-w-none h-20 w-auto drop-shadow-sm transition-transform duration-500 ease-out group-hover:scale-[1.08] group-hover:-rotate-1 origin-center" />
                </div>

                <div class="flex items-center gap-2">
                    <button v-if="canInstall && !installDone" @click="handleInstall"
                        :disabled="isInstalling"
                        class="text-sm font-semibold border border-primary/20 text-primary bg-white px-5 py-2 rounded-full transition-all duration-300 hover:bg-primary/5 disabled:opacity-60">
                        {{ isInstalling ? 'Memproses...' : 'Install App' }}
                    </button>
                    <span v-else-if="installDone" class="text-xs font-semibold bg-green-50 text-green-700 px-4 py-2 rounded-full border border-green-100">
                        Aplikasi Terpasang
                    </span>
                    <a href="/login"
                        class="text-sm font-semibold bg-gradient-to-r from-primary to-primary-dark text-white px-6 py-2 rounded-full transition-all duration-300 shadow-md shadow-primary/30 hover:shadow-primary/50 hover:shadow-lg hover:-translate-y-0.5 hover:scale-105 active:translate-y-0 active:scale-95">
                        Masuk 
                    </a>
                </div>
            </nav>
        </div>

        <!-- â”€â”€ HERO â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section class="relative flex flex-col items-center overflow-hidden bg-gradient-to-br from-primary via-primary to-primary-dark pt-40 pb-0 min-h-screen">
            <!-- Blobs -->
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] bg-white/10 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute bottom-0 -left-24 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-blob" style="animation-delay:3s"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-white/5 rounded-full blur-3xl animate-blob" style="animation-delay:6s"></div>
            <!-- Dot grid -->
            <div class="absolute inset-0 opacity-[0.06] pointer-events-none" style="background-image:radial-gradient(circle,white 1.5px,transparent 1.5px);background-size:28px 28px;"></div>

            <!-- Ring ornaments -->
            <svg class="absolute top-24 left-6 w-24 h-24 text-white/10 svg-spin-slow pointer-events-none" viewBox="0 0 96 96" fill="none">
                <circle cx="48" cy="48" r="44" stroke="currentColor" stroke-width="1.5" stroke-dasharray="8 6"/>
                <circle cx="48" cy="48" r="28" stroke="currentColor" stroke-width="1" stroke-dasharray="4 8"/>
            </svg>
            <svg class="absolute bottom-32 right-8 w-20 h-20 text-white/10 svg-spin-slow-rev pointer-events-none" viewBox="0 0 80 80" fill="none">
                <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="1.5" stroke-dasharray="6 5"/>
            </svg>

            <!-- Floating cross marks -->
            <svg class="absolute top-40 right-[10%] text-white/20 w-6 h-6 svg-drift-1 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute top-52 left-[8%] text-white/15 w-5 h-5 svg-drift-2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute bottom-48 left-[15%] text-white/20 w-4 h-4 svg-drift-3 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute bottom-40 right-[18%] text-white/15 w-5 h-5 svg-drift-2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute top-[35%] left-[5%] text-white/10 w-7 h-7 svg-drift-1 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="4" y="4" width="16" height="16" rx="3"/></svg>
            <svg class="absolute top-[45%] right-[6%] text-white/10 w-6 h-6 svg-drift-3 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="4" y="4" width="16" height="16" rx="3"/></svg>

            <!-- TOP TEXT -->
            <div class="relative z-10 text-center px-5 max-w-3xl mx-auto w-full">
                <h1 class="hero-anim-1 text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4 tracking-tight">
                    Kesehatan Anda,<br>
                    <span class="text-white/70">Lebih Terkontrol</span>
                </h1>
                <p class="hero-anim-2 text-sm sm:text-base leading-relaxed mb-7 max-w-xl mx-auto min-h-[4rem] sm:min-h-[3rem]">
                    <span class="text-white/80 font-medium whitespace-pre-line">{{ typewriterText }}<span class="animate-pulse inline-block align-baseline text-white">|</span></span>
                </p>
                <div class="hero-anim-3 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="/masuk" class="w-full sm:w-auto bg-white text-primary font-bold px-9 py-3.5 rounded-full hover:bg-white/90 transition-all shadow-2xl shadow-black/20 text-sm hover:scale-105 active:scale-100">
                        Masuk Pasien
                    </a>
                    <a href="#features" class="w-full sm:w-auto border border-white/40 text-white font-semibold px-9 py-3.5 rounded-full hover:bg-white/10 transition text-sm">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>

            <!-- STATS MINI (floating) -->
            <div class="hero-anim-5 relative z-10 w-full max-w-2xl mx-auto px-5 mt-8 mb-8 pt-6 border-t border-white/20 flex items-center justify-center gap-8 sm:gap-16">
                <div class="float-stat-1 text-center">
                    <p class="text-3xl sm:text-4xl font-bold text-white">6+</p>
                    <p class="text-white/60 text-xs mt-1">Metrik Kesehatan</p>
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div class="float-stat-2 text-center">
                    <p class="text-3xl sm:text-4xl font-bold text-white">AI</p>
                    <p class="text-white/60 text-xs mt-1">Analisis Cerdas</p>
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div class="float-stat-3 text-center">
                    <p class="text-3xl sm:text-4xl font-bold text-white">24/7</p>
                    <p class="text-white/60 text-xs mt-1">Monitoring</p>
                </div>
            </div>

            <!-- Wave divider hero â†’ features -->
            <div class="absolute bottom-0 left-0 right-0 pointer-events-none z-10">
                <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full" preserveAspectRatio="none">
                    <path d="M0,40 C240,80 480,0 720,40 C960,80 1200,0 1440,40 L1440,80 L0,80 Z" fill="white"/>
                </svg>
            </div>
        </section>

        <!-- â”€â”€ FEATURES â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section id="features" class="relative py-20 px-6 bg-white overflow-hidden">
            <!-- Ring ornament top-left -->
            <svg class="absolute -top-8 -left-8 w-40 h-40 text-primary/5 svg-spin-slow pointer-events-none" viewBox="0 0 160 160" fill="none">
                <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="1" stroke-dasharray="10 8"/>
                <circle cx="80" cy="80" r="50" stroke="currentColor" stroke-width="1" stroke-dasharray="5 10"/>
            </svg>
            <!-- Cross marks -->
            <svg class="absolute top-20 right-[6%] text-primary/10 w-5 h-5 svg-drift-2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute bottom-16 left-[8%] text-primary/8 w-4 h-4 svg-drift-3 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute bottom-24 right-[12%] text-primary/6 w-6 h-6 svg-drift-1 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="4" y="4" width="16" height="16" rx="3"/></svg>
            <div class="max-w-5xl mx-auto">
                <div class="reveal text-center mb-14" data-delay="0">
                    <span class="text-xs font-bold text-primary uppercase tracking-widest">Fitur Unggulan</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2">Semua yang Anda Butuhkan</h2>
                    <p class="text-gray-500 mt-3 max-w-xl mx-auto text-sm leading-relaxed">HEALTIVA hadir dengan fitur lengkap untuk memantau dan memahami kondisi kesehatan Anda secara menyeluruh</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div v-for="(f, i) in features" :key="i"
                        class="reveal group p-6 rounded-2xl border border-gray-100 hover:border-primary/20 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 bg-white"
                        :data-delay="i * 80 + 100">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 transition-all duration-300"
                            :class="f.bg">
                            <component :is="f.icon" class="w-6 h-6" :class="f.color" />
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2">{{ f.title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- â”€â”€ AI SECTION â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section class="relative py-20 px-6 bg-gradient-to-br from-[#FEF2F2] to-[#FECACA] overflow-hidden">
            <!-- Wave top -->
            <div class="absolute top-0 left-0 right-0 pointer-events-none">
                <svg viewBox="0 0 1440 50" fill="none" class="w-full" preserveAspectRatio="none">
                    <path d="M0,25 C360,50 1080,0 1440,25 L1440,0 L0,0 Z" fill="white"/>
                </svg>
            </div>
            <!-- Wave bottom -->
            <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
                <svg viewBox="0 0 1440 50" fill="none" class="w-full" preserveAspectRatio="none">
                    <path d="M0,25 C480,0 960,50 1440,25 L1440,50 L0,50 Z" fill="white"/>
                </svg>
            </div>
            <!-- Ring ornament right -->
            <svg class="absolute top-10 right-6 w-28 h-28 text-primary/8 svg-spin-slow pointer-events-none" viewBox="0 0 112 112" fill="none">
                <circle cx="56" cy="56" r="50" stroke="currentColor" stroke-width="1" stroke-dasharray="8 6"/>
                <circle cx="56" cy="56" r="32" stroke="currentColor" stroke-width="1" stroke-dasharray="4 8"/>
            </svg>
            <!-- Cross marks -->
            <svg class="absolute bottom-20 left-[5%] text-primary/10 w-5 h-5 svg-drift-1 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute top-16 left-[12%] text-primary/8 w-4 h-4 svg-drift-3 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <div class="max-w-5xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="reveal from-left" data-delay="100">
                        <span class="text-xs font-bold text-primary uppercase tracking-widest">Kecerdasan Buatan</span>
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2 mb-4">Analisis Kesehatan<br>yang Lebih Cerdas</h2>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6">
                            Engine AI HEALTIVA membantu kader membaca data kesehatan warga secara menyeluruh, mulai dari tekanan darah, gula darah, IMT, hingga tren dari riwayat pemeriksaan, lalu menyajikannya dalam laporan yang jelas.
                        </p>
                        <ul class="space-y-3">
                            <li v-for="(p, i) in aiPoints" :key="i" class="flex items-start gap-3">
                                <div class="w-5 h-5 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600">{{ p }}</span>
                            </li>
                        </ul>
                        <a href="/login"
                            class="inline-flex items-center gap-2 mt-8 bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-3 rounded-full transition text-sm shadow-md shadow-primary/30">
                            Masuk Dashboard Kader
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Fake analysis card preview -->
                    <div class="reveal from-right" data-delay="200">
                    <div class="relative">
                        <div class="bg-white rounded-3xl shadow-xl shadow-primary/10 p-6 border border-[#FECACA]">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">Hasil Analisis AI</p>
                                    <p class="text-xs text-gray-400">Analisis Terbaru</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div v-for="(r, i) in previewResults" :key="i"
                                    class="rounded-xl p-3 border-l-4"
                                    :class="r.style">
                                    <p class="text-xs font-bold mb-1" :class="r.titleColor">{{ r.title }}</p>
                                    <p class="text-xs text-gray-600 leading-relaxed">{{ r.text }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Floating badge -->
                        <div class="absolute -top-4 -right-4 bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                            Powered by AI
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- â”€â”€ METRICS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section class="relative py-20 px-6 bg-gradient-to-br from-primary to-primary-dark">
            <!-- Wave top -->
            <div class="absolute top-0 left-0 right-0 pointer-events-none">
                <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full" preserveAspectRatio="none">
                    <path d="M0,30 C360,0 1080,60 1440,30 L1440,0 L0,0 Z" fill="white"/>
                </svg>
            </div>
            <div class="max-w-4xl mx-auto">
                <div class="reveal text-center mb-12" data-delay="0">
                    <h2 class="text-3xl font-bold text-white">Metrik yang Dipantau</h2>
                    <p class="text-white/60 mt-2 text-sm">Data lengkap untuk gambaran kesehatan yang menyeluruh</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div v-for="(m, i) in metrics" :key="i"
                        class="reveal bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/20 hover:bg-white/20 transition text-center"
                        :data-delay="i * 70 + 100">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <component :is="m.icon" class="w-5 h-5 text-white" />
                        </div>
                        <p class="font-bold text-white text-sm">{{ m.name }}</p>
                        <p class="text-white/50 text-xs mt-1">{{ m.unit }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- â”€â”€ CARA KERJA â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section class="relative py-24 px-6 bg-white overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary/3 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-primary/3 rounded-full blur-3xl pointer-events-none"></div>

            <div class="max-w-5xl mx-auto">
                <div class="reveal text-center mb-20" data-delay="0">
                    <span class="text-xs font-bold text-primary uppercase tracking-widest">Cara Kerja</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2">Alur 3 Langkah Kader</h2>
                    <p class="text-gray-500 mt-3 max-w-md mx-auto text-sm leading-relaxed">
                        Dirancang untuk alur kerja posyandu: data pasien rapi, pemeriksaan cepat, dan analisis AI langsung terbaca.
                    </p>
                </div>

                <div class="relative grid grid-cols-1 sm:grid-cols-3 gap-12 sm:gap-6">
                    <!-- Connector line desktop -->
                    <div class="hidden sm:block absolute top-14 left-[22%] right-[22%] pointer-events-none">
                        <svg viewBox="0 0 400 4" fill="none" class="w-full" preserveAspectRatio="none">
                            <line x1="0" y1="2" x2="400" y2="2" stroke="#F0404B" stroke-width="1.5" stroke-dasharray="6 5" opacity="0.25"/>
                        </svg>
                    </div>

                    <!-- Step 1 -->
                    <div class="reveal text-center" data-delay="100">
                        <div class="relative inline-block mb-6">
                            <span class="absolute -top-2 -right-2 w-6 h-6 bg-primary rounded-full text-white text-xs font-black flex items-center justify-center z-10 shadow-md shadow-primary/40">1</span>
                            <div class="w-28 h-28 bg-primary/8 rounded-3xl flex items-center justify-center mx-auto border border-primary/10">
                                <svg class="w-14 h-14 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Pendataan Pasien</h3>
                        <p class="text-sm text-gray-500 leading-relaxed max-w-[200px] mx-auto">
                            Cari atau tambahkan data pasien agar setiap pemeriksaan tersimpan pada profil yang tepat.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="reveal text-center" data-delay="250">
                        <div class="relative inline-block mb-6">
                            <span class="absolute -top-2 -right-2 w-6 h-6 bg-primary rounded-full text-white text-xs font-black flex items-center justify-center z-10 shadow-md shadow-primary/40">2</span>
                            <div class="w-28 h-28 bg-primary/8 rounded-3xl flex items-center justify-center mx-auto border border-primary/10">
                                <svg class="w-14 h-14 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Input Pemeriksaan</h3>
                        <p class="text-sm text-gray-500 leading-relaxed max-w-[200px] mx-auto">
                            Masukkan data vital pasien seperti tekanan darah, gula darah, IMT, dan metrik penting lain.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="reveal text-center" data-delay="400">
                        <div class="relative inline-block mb-6">
                            <span class="absolute -top-2 -right-2 w-6 h-6 bg-primary rounded-full text-white text-xs font-black flex items-center justify-center z-10 shadow-md shadow-primary/40">3</span>
                            <div class="w-28 h-28 bg-primary/8 rounded-3xl flex items-center justify-center mx-auto border border-primary/10">
                                <svg class="w-14 h-14 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Analisis & Tindak Lanjut</h3>
                        <p class="text-sm text-gray-500 leading-relaxed max-w-[200px] mx-auto">
                            Sistem AI membantu ringkasan kondisi pasien sehingga kader lebih cepat menentukan tindak lanjut.
                        </p>
                    </div>
                </div>

                <!-- CTA mini -->
                <div class="reveal text-center mt-16" data-delay="500">
                    <a href="/login"
                        class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-semibold px-8 py-3.5 rounded-full transition-all shadow-lg shadow-primary/30 text-sm hover:scale-105 active:scale-100">
                        Mulai dari Dashboard Kader
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- â”€â”€ VIDEO EDUKASI â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section class="py-20 px-6 bg-[#FDF4F4]">
            <div class="max-w-5xl mx-auto">
                <div class="reveal flex flex-col sm:flex-row justify-between items-end mb-10 gap-4" data-delay="0">
                    <div>
                        <span class="text-xs font-bold text-primary uppercase tracking-widest">Pusat Pengetahuan</span>
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2">Video Edukasi Kesehatan</h2>
                        <p class="text-gray-500 mt-3 text-sm max-w-lg">Tonton panduan dan tips informatif dari pakar untuk menjaga pola hidup yang lebih sehat setiap hari.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="(v, i) in videos" :key="i" class="reveal group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-primary/10 transition-all duration-300 border border-gray-100" :data-delay="i * 100 + 100">
                        <div class="relative aspect-video bg-gray-200 overflow-hidden">
                            <img :src="v.thumb" :alt="v.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-primary ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                            <div class="absolute bottom-2 right-2 bg-black/70 text-white text-[10px] font-bold px-2 py-1 rounded">
                                {{ v.duration }}
                            </div>
                        </div>
                        <div class="p-5">
                            <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-1 rounded">{{ v.category }}</span>
                            <h3 class="font-bold text-gray-800 mt-3 text-sm line-clamp-2 group-hover:text-primary transition-colors">{{ v.title }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- â”€â”€ TESTIMONI PENGGUNA â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section class="relative py-20 px-6 bg-white overflow-hidden">
            <!-- Wave top from Cara Kerja -->
            <div class="absolute top-0 left-0 right-0 pointer-events-none">
                <svg viewBox="0 0 1440 50" fill="none" class="w-full" preserveAspectRatio="none">
                    <path d="M0,25 C480,0 960,50 1440,25 L1440,0 L0,0 Z" fill="#f9fafb" opacity="0.7"/>
                </svg>
            </div>
            <!-- Subtle ring ornament top-right -->
            <svg class="absolute top-8 right-8 w-32 h-32 text-primary/5 svg-spin-slow pointer-events-none" viewBox="0 0 128 128" fill="none">
                <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="1" stroke-dasharray="10 7"/>
                <circle cx="64" cy="64" r="38" stroke="currentColor" stroke-width="1" stroke-dasharray="5 9"/>
            </svg>
            <!-- Cross marks -->
            <svg class="absolute top-16 left-[5%] text-primary/10 w-5 h-5 svg-drift-2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute bottom-16 right-[8%] text-primary/10 w-4 h-4 svg-drift-1 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <div class="max-w-5xl mx-auto">
                <div class="reveal text-center mb-14" data-delay="0">
                    <span class="text-xs font-bold text-primary uppercase tracking-widest">Kisah Mereka</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2">Lebih Sehat bersama HEALTIVA</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="(t, i) in testimonials" :key="i" class="reveal bg-white p-6 rounded-3xl border border-gray-100 shadow-lg shadow-gray-100/50 relative" :data-delay="i * 150">
                        <!-- Quote icon bg -->
                        <svg class="absolute top-4 right-4 w-12 h-12 text-gray-50" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        
                        <div class="flex items-center gap-3 mb-4 relative z-10">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-primary-dark text-white flex items-center justify-center font-bold text-lg shadow-md">
                                {{ t.name.charAt(0) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">{{ t.name }}</h4>
                                <p class="text-xs text-gray-500">{{ t.role }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed relative z-10">"{{ t.quote }}"</p>
                        <div class="flex text-yellow-400 mt-4 relative z-10">
                            <svg v-for="s in 5" :key="s" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- â”€â”€ CTA â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <section class="relative py-24 px-6 bg-gradient-to-br from-primary via-primary to-primary-dark overflow-hidden">
            <!-- Wave top -->
            <div class="absolute top-0 left-0 right-0 pointer-events-none">
                <svg viewBox="0 0 1440 60" fill="none" class="w-full" preserveAspectRatio="none">
                    <path d="M0,30 C360,60 1080,0 1440,30 L1440,0 L0,0 Z" fill="white"/>
                </svg>
            </div>
            <!-- Wave bottom -->
            <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
                <svg viewBox="0 0 1440 50" fill="none" class="w-full" preserveAspectRatio="none">
                    <path d="M0,25 C360,0 1080,50 1440,25 L1440,50 L0,50 Z" fill="#f9fafb"/>
                </svg>
            </div>
            <!-- Ring ornament -->
            <svg class="absolute top-8 left-8 w-28 h-28 text-white/10 svg-spin-slow-rev pointer-events-none" viewBox="0 0 112 112" fill="none">
                <circle cx="56" cy="56" r="50" stroke="currentColor" stroke-width="1" stroke-dasharray="8 6"/>
            </svg>
            <svg class="absolute bottom-8 right-8 w-20 h-20 text-white/10 svg-spin-slow pointer-events-none" viewBox="0 0 80 80" fill="none">
                <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="1" stroke-dasharray="5 7"/>
                <circle cx="40" cy="40" r="20" stroke="currentColor" stroke-width="1" stroke-dasharray="3 6"/>
            </svg>
            <!-- Cross marks -->
            <svg class="absolute top-[40%] left-[8%] text-white/15 w-5 h-5 svg-drift-1 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>
            <svg class="absolute top-[30%] right-[10%] text-white/15 w-4 h-4 svg-drift-3 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 4v16M4 12h16"/></svg>

            <div class="reveal relative z-10 max-w-2xl mx-auto text-center" data-delay="0">
                <div class="w-16 h-16 bg-white/15 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-white/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Mulai Jaga Kesehatan Anda</h2>
                <p class="text-white/70 text-sm mb-8 leading-relaxed">Akses untuk pasien dan kader sudah dipisah agar alur layanan lebih rapi dan tidak membingungkan.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="/masuk"
                        class="w-full sm:w-auto bg-white text-primary font-bold px-8 py-3.5 rounded-full hover:bg-white/90 transition shadow-xl shadow-black/20 text-sm hover:scale-105 active:scale-100">
                        Masuk sebagai Pasien
                    </a>
                    <a href="/login"
                        class="w-full sm:w-auto border border-white/40 text-white font-semibold px-8 py-3.5 rounded-full hover:bg-white/10 transition text-sm">
                        Masuk sebagai Kader
                    </a>
                </div>
            </div>
        </section>

        <!-- â”€â”€ FOOTER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
        <footer class="relative bg-gray-50 border-t border-gray-100 py-10 px-6 overflow-hidden">
            <!-- Subtle ornament bg -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image:radial-gradient(circle,#F0404B 1px,transparent 1px);background-size:24px 24px;"></div>
            <!-- Ring ornament -->
            <svg class="absolute -bottom-8 -right-8 w-36 h-36 text-primary/8 svg-spin-slow pointer-events-none" viewBox="0 0 144 144" fill="none">
                <circle cx="72" cy="72" r="66" stroke="currentColor" stroke-width="1" stroke-dasharray="8 8"/>
            </svg>
            <div class="relative z-10 max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
                <img src="/images/logo.png" alt="HEALTIVA" class="h-16 w-auto" />
                <p class="text-xs text-gray-400">(c) 2026 HEALTIVA | Sistem Monitor Kesehatan Cerdas</p>
                <div class="flex gap-4 text-xs text-gray-400">
                    <a href="/login" class="hover:text-primary transition">Masuk</a>
                    <a href="/masuk" class="hover:text-primary transition">Masuk Pasien</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { h, ref, onMounted, onUnmounted } from 'vue';

const navScrolled = ref(false);
const deferredPrompt = ref(null);
const canInstall = ref(false);
const isInstalling = ref(false);
const installDone = ref(false);

const typewriterText = ref('');
const fullText = "Pantau tekanan darah, gula darah, IMT, dan kondisi vital lainnya setiap hari.\nDapatkan analisis cerdas berbasis AI untuk menjaga kesehatan optimal.";
let typewriterTimer = null;

const handleInstall = async () => {
    if (!deferredPrompt.value) return;
    isInstalling.value = true;
    deferredPrompt.value.prompt();
    const { outcome } = await deferredPrompt.value.userChoice;
    if (outcome === 'accepted') {
        canInstall.value = false;
    }
    deferredPrompt.value = null;
    isInstalling.value = false;
};

const onBeforeInstallPrompt = (event) => {
    event.preventDefault();
    deferredPrompt.value = event;
    canInstall.value = true;
};

const onAppInstalled = () => {
    installDone.value = true;
    canInstall.value = false;
    deferredPrompt.value = null;
};

onMounted(() => {
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    installDone.value = isStandalone;
    window.addEventListener('beforeinstallprompt', onBeforeInstallPrompt);
    window.addEventListener('appinstalled', onAppInstalled);

    let i = 0;
    let isDeleting = false;

    const typeWriter = () => {
        if (!isDeleting && i < fullText.length) {
            typewriterText.value += fullText.charAt(i);
            i++;
            typewriterTimer = setTimeout(typeWriter, 80);
        } else if (isDeleting && i > 0) {
            typewriterText.value = fullText.substring(0, i - 1);
            i--;
            typewriterTimer = setTimeout(typeWriter, 30);
        } else {
            isDeleting = !isDeleting;
            typewriterTimer = setTimeout(typeWriter, isDeleting ? 4000 : 1500);
        }
    };
    
    // Start after 1.2 seconds, matching the other entry animations
    typewriterTimer = setTimeout(typeWriter, 1200);

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        navScrolled.value = window.scrollY > 60;
    }, { passive: true });

    // Scroll-reveal via IntersectionObserver
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const delay = Number(el.dataset.delay || 0);
                    setTimeout(() => el.classList.add('visible'), delay);
                    observer.unobserve(el);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
});

onUnmounted(() => {
    window.removeEventListener('beforeinstallprompt', onBeforeInstallPrompt);
    window.removeEventListener('appinstalled', onAppInstalled);
});

// SVG icon components inline
const HeartIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' })]) };
const ChartIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' })]) };
const BrainIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2' })]) };
const ShieldIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' })]) };
const BellIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' })]) };
const PhoneIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z' })]) };
const DropIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z' })]) };
const ThermIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z' })]) };
const ScaleIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3' })]) };

const BookIcon  = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' })]) };
const ChatIcon  = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z' })]) };
const ClipIcon  = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' })]) };

const features = [
    { title: 'Monitoring Harian', desc: 'Catat dan pantau data vital kesehatan Anda setiap hari dengan mudah dan terstruktur.', bg: 'bg-primary/10', color: 'text-primary', icon: HeartIcon },
    { title: 'Riwayat Data Lengkap', desc: 'Semua data tersimpan rapi dengan visualisasi tren sehingga Anda bisa melihat perkembangan dari waktu ke waktu.', bg: 'bg-[#FECACA]', color: 'text-[#B92521]', icon: ChartIcon },
    { title: 'Analisis AI', desc: 'Engine AI menganalisis data Anda dan memberikan laporan kondisi kesehatan yang mudah dipahami.', bg: 'bg-[#FCA5A5]', color: 'text-[#B92521]', icon: BrainIcon },
    { title: 'Chat AI Kesehatan', desc: 'Tanya langsung ke AI seputar kondisi kesehatan Anda. Jawaban berbasis pedoman Kemenkes RI.', bg: 'bg-primary/10', color: 'text-primary', icon: ChatIcon },
    { title: 'Edukasi Kesehatan', desc: 'Video edukasi dari Kemenkes, WHO, PERKENI, dan sumber medis terpercaya lainnya.', bg: 'bg-[#FECACA]', color: 'text-[#B92521]', icon: BookIcon },
    { title: 'Standar Normal Pemeriksaan', desc: 'Referensi nilai normal tekanan darah, gula darah, IMT, SpO2, dan parameter lainnya berbasis WHO.', bg: 'bg-[#FCA5A5]', color: 'text-[#B92521]', icon: ClipIcon },
    { title: 'Privasi Terjaga', desc: 'Data kesehatan Anda bersifat pribadi dan hanya dapat diakses oleh Anda sendiri.', bg: 'bg-primary/10', color: 'text-primary', icon: ShieldIcon },
    { title: 'Unduh PDF & Bagikan WA', desc: 'Ekspor laporan analisis AI ke PDF atau kirim langsung ke WhatsApp untuk konsultasi dokter.', bg: 'bg-[#FECACA]', color: 'text-[#B92521]', icon: PhoneIcon },
    { title: 'Akses Kapan Saja', desc: 'Tersedia di semua perangkat — smartphone, tablet, maupun komputer.', bg: 'bg-[#FCA5A5]', color: 'text-[#B92521]', icon: BellIcon },
];

const aiPoints = [
    'Analisis tekanan darah, gula darah, dan IMT secara bersamaan',
    'Deteksi risiko berdasarkan data terkini dan tren historis',
    'Rekomendasi gaya hidup yang dipersonalisasi',
    'Laporan mudah dipahami tanpa istilah medis yang rumit',
];

const previewResults = [
    { title: 'Tekanan Darah', text: 'Tekanan darah dalam rentang normal. Pertahankan pola hidup sehat saat ini.', style: 'border-[#F0404B] bg-[#FEF2F2]', titleColor: 'text-[#B92521]' },
    { title: 'Gula Darah', text: 'Kadar gula darah sedikit di atas normal. Kurangi konsumsi karbohidrat sederhana.', style: 'border-[#E48888] bg-[#FEE2E2]', titleColor: 'text-[#B92521]' },
    { title: 'Rekomendasi', text: 'Olahraga ringan 30 menit per hari dan perbanyak konsumsi sayuran hijau.', style: 'border-[#F18E8C] bg-[#FEE2E2]', titleColor: 'text-[#B92521]' },
];

const steps = [
    { title: 'Buat Akun', desc: 'Daftar gratis dengan email atau akun Google Anda hanya dalam beberapa detik.', bg: 'bg-primary', numColor: 'text-white' },
    { title: 'Input Data Kesehatan', desc: 'Masukkan data vital seperti tekanan darah, gula darah, berat badan, dan lainnya.', bg: 'bg-[#FECACA]', numColor: 'text-[#B92521]' },
    { title: 'Dapatkan Analisis AI', desc: 'Jalankan analisis dan terima laporan kondisi kesehatan yang lengkap dan mudah dipahami.', bg: 'bg-primary/10', numColor: 'text-primary' },
];

const metrics = [
    { name: 'Tekanan Darah', unit: 'mmHg (Sistolik & Diastolik)', icon: HeartIcon },
    { name: 'Gula Darah', unit: 'mg/dL', icon: DropIcon },
    { name: 'Detak Jantung', unit: 'BPM', icon: HeartIcon },
    { name: 'Suhu Tubuh', unit: '°C', icon: ThermIcon },
    { name: 'IMT & Berat Badan', unit: 'kg / m²', icon: ScaleIcon },
    { name: 'Saturasi Oksigen', unit: 'SpO2 (%)', icon: ChartIcon },
];

const videos = [
    { title: 'Cara Menstabilkan Gula Darah dengan Pola Makan Sehat', thumb: 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', duration: '08:24', category: 'Nutrisi' },
    { title: 'Latihan Rutin 15 Menit untuk Pemulihan Jantung', thumb: 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', duration: '15:10', category: 'Kebugaran' },
    { title: 'Memahami Tekanan Darah Tinggi dan Cara Pencegahannya', thumb: 'https://images.unsplash.com/photo-1505576399279-565b52d4ac71?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', duration: '12:05', category: 'Edukasi Medis' },
];

const testimonials = [
    { name: 'Budi Santoso', role: 'Pasien Hipertensi', quote: 'Aplikasi ini sangat membantu saya melacak hasil tensi darah saya setiap pagi. Laporan AI-nya mudah dimengerti.' },
    { name: 'Siti Aminah', role: 'Pengguna Aktif', quote: 'Saya selalu mengecek video edukasi di sini karena sangat sinkron dengan rekam medis saya. Navigasinya juga mulus!' },
    { name: 'dr. Hendra', role: 'Praktisi Kesehatan', quote: 'Data PDF yang diunduh pasien dari HEALTIVA amat membantu saya dalam menganalisis histori medis mereka lebih cepat.' }
];
</script>

<style scoped>
/* â”€â”€ Premium Navbar Drop â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.animate-nav-drop {
    opacity: 0;
    animation: navDrop 1.2s cubic-bezier(0.16,1,0.3,1) forwards;
    animation-delay: 0.1s;
}
@keyframes navDrop {
    from { opacity: 0; transform: translateY(-40px) scale(0.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* â”€â”€ Brand gradient â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.text-gradient {
    background: linear-gradient(135deg, #F0404B, #B92521);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* â”€â”€ Hero stagger animations â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.hero-anim-1 { animation: fadeInUp 0.75s cubic-bezier(0.16,1,0.3,1) both; animation-delay: 0.05s; }
.hero-anim-2 { animation: fadeInUp 0.75s cubic-bezier(0.16,1,0.3,1) both; animation-delay: 0.2s;  }
.hero-anim-3 { animation: fadeInUp 0.75s cubic-bezier(0.16,1,0.3,1) both; animation-delay: 0.35s; }
.hero-anim-4 { animation: fadeInUp 0.75s cubic-bezier(0.16,1,0.3,1) both; animation-delay: 0.5s;  }
.hero-anim-5 { animation: fadeInUp 0.75s cubic-bezier(0.16,1,0.3,1) both; animation-delay: 0.75s; }

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(36px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* â”€â”€ Scroll-reveal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.reveal {
    opacity: 0;
    transform: translateY(36px);
    transition: opacity 0.75s cubic-bezier(0.16,1,0.3,1), transform 0.75s cubic-bezier(0.16,1,0.3,1);
}
.reveal.from-left  { transform: translateX(-48px); }
.reveal.from-right { transform: translateX(48px); }
.reveal.visible    { opacity: 1 !important; transform: none !important; }

/* â”€â”€ Blob float â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.animate-blob {
    animation: blobFloat 14s ease-in-out infinite;
}
@keyframes blobFloat {
    0%, 100% { transform: scale(1) translate(0, 0); }
    33%       { transform: scale(1.06) translate(12px, -14px); }
    66%       { transform: scale(0.96) translate(-10px, 10px); }
}

/* â”€â”€ AI card floating â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.animate-card-float {
    animation: cardFloat 6s ease-in-out infinite;
}
@keyframes cardFloat {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-10px); }
}

/* â”€â”€ Slow pulse for badge â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.animate-pulse-slow {
    animation: pulseSlow 3s ease-in-out infinite;
}
@keyframes pulseSlow {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.85; transform: scale(1.04); }
}

/* â”€â”€ Floating stats animation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.float-stat-1 { animation: floatCard 4s ease-in-out infinite; }
.float-stat-2 { animation: floatCard 4s ease-in-out infinite; animation-delay: 0.7s; }
.float-stat-3 { animation: floatCard 4s ease-in-out infinite; animation-delay: 1.4s; }

@keyframes floatCard {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-10px); }
}

/* â”€â”€ SVG ornament animations â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.svg-spin-slow     { animation: spinSlow 24s linear infinite; transform-origin: center; }
.svg-spin-slow-rev { animation: spinSlow 20s linear infinite reverse; transform-origin: center; }

@keyframes spinSlow {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

.svg-drift-1 { animation: drift1 8s ease-in-out infinite; }
.svg-drift-2 { animation: drift2 10s ease-in-out infinite; }
.svg-drift-3 { animation: drift3 7s ease-in-out infinite; }

@keyframes drift1 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    33%       { transform: translate(6px, -8px) rotate(20deg); }
    66%       { transform: translate(-4px, 4px) rotate(-10deg); }
}
@keyframes drift2 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50%       { transform: translate(-8px, -6px) rotate(15deg); }
}
@keyframes drift3 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    40%       { transform: translate(5px, 8px) rotate(-20deg); }
    80%       { transform: translate(-3px, -4px) rotate(10deg); }
}

/* ── Shimmering text ─────────────────────────────────────── */
.text-shimmer {
    background: linear-gradient(90deg, rgba(255,255,255,0.7) 0%, rgba(255,255,255,1) 50%, rgba(255,255,255,0.7) 100%);
    background-size: 200% auto;
    color: transparent;
    -webkit-background-clip: text;
    background-clip: text;
    animation: shimmerText 3s linear infinite;
}
@keyframes shimmerText {
    to { background-position: 200% center; }
}
</style>
