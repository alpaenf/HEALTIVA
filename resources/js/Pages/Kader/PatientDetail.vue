<template>
    <KaderLayout>
        <Head :title="patient.name" />

        <!-- Back link -->
        <Link href="/kader/pasien"
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary mb-5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Pasien
        </Link>

        <!-- Patient Info Card -->
        <div class="bg-gradient-to-br from-primary to-primary-dark text-white rounded-2xl p-4 sm:p-6 lg:p-8 mb-5 shadow-lg shadow-primary/20">
            <!-- Top row: avatar + name + button -->
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex items-center gap-3 w-full md:w-auto flex-1">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 rounded-xl flex items-center justify-center text-xl sm:text-2xl font-bold flex-shrink-0">
                        {{ patient.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-base sm:text-xl font-bold leading-tight truncate">{{ patient.name }}</h1>
                        <p class="text-white/70 text-xs font-mono mt-0.5">NIK: {{ patient.nik }}</p>
                        <p class="text-white/60 text-xs mt-0.5 flex flex-wrap gap-x-1">
                            <span>{{ patient.gender === 'male' ? 'Laki-laki' : patient.gender === 'female' ? 'Perempuan' : '-' }}</span>
                            <span v-if="patient.date_of_birth">· {{ formatAge(patient.date_of_birth) }} th</span>
                        </p>
                    </div>
                </div>
                <Link :href="`/kader/pasien/${patient.id}/input`"
                    class="flex items-center justify-center gap-1.5 bg-white text-primary px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition w-full md:w-auto flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Data
                </Link>
            </div>
            <!-- Phone row -->
            <div v-if="patient.phone" class="flex items-center gap-1.5 mt-3 pt-3 border-t border-white/20">
                <svg class="w-3.5 h-3.5 text-white/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <a :href="`tel:${patient.phone}`" class="text-white/80 text-xs hover:text-white transition">{{ patient.phone }}</a>
            </div>
        </div>

        <!-- Flash -->
        <div v-if="$page.props.flash?.success"
            class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm mb-4">
            {{ $page.props.flash.success }}
        </div>

        <!-- Latest record summary -->
        <div v-if="latestRecord" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div v-if="latestRecord.systolic" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 text-center">
                <p class="text-sm font-bold text-gray-800">{{ latestRecord.systolic }}/{{ latestRecord.diastolic }}</p>
                <p class="text-[10px] text-gray-400">Tekanan <span class="text-[9px]">mmHg</span></p>
            </div>
            <div v-if="latestRecord.heart_rate" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 text-center">
                <p class="text-sm font-bold text-gray-800">{{ latestRecord.heart_rate }}</p>
                <p class="text-[10px] text-gray-400">BPM</p>
            </div>
            <div v-if="latestRecord.blood_sugar" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 text-center">
                <p class="text-sm font-bold text-gray-800">{{ latestRecord.blood_sugar }}</p>
                <p class="text-[10px] text-gray-400">Gula <span class="text-[9px]">mg/dL</span></p>
            </div>
            <div v-if="latestRecord.weight" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 text-center">
                <p class="text-sm font-bold text-gray-800">{{ latestRecord.weight }} kg</p>
                <p class="text-[10px] text-gray-400">Berat Badan</p>
            </div>
        </div>

        <!-- Health Trend Chart -->
        <div v-if="chartRecords.length >= 2" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5 mb-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-700 text-sm sm:text-base">Tren Kesehatan</h2>
                <span class="text-xs text-gray-400">{{ chartRecords.length }} data terakhir</span>
            </div>

            <div class="flex items-center gap-4 mb-3 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-1 rounded-full bg-primary inline-block"></span>
                    <span class="text-xs text-gray-500">Sistolik</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-1 rounded-full bg-[#F18E8C] inline-block"></span>
                    <span class="text-xs text-gray-500">Diastolik</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-1 rounded-full bg-blue-400 inline-block"></span>
                    <span class="text-xs text-gray-500">Detak Jantung</span>
                </div>
            </div>

            <div class="h-44 sm:h-48">
                <Line :data="lineChartData" :options="lineChartOptions" />
            </div>
        </div>

        <!-- Records Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2.5">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-800 text-sm">Riwayat Pemeriksaan</h3>
                    <span class="text-[10px] text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full hidden sm:inline-block">{{ records.total }} data</span>
                </div>
                <a :href="`/kader/pasien/${patient.id}/export`" class="flex-shrink-0 text-[11px] sm:text-xs font-semibold text-primary hover:bg-primary/5 transition border border-primary/20 px-2.5 py-1.5 sm:px-3 rounded-lg flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Unduh Excel</span>
                </a>
            </div>

            <!-- Empty state -->
            <div v-if="records.data.length === 0" class="px-4 py-8 text-center text-gray-400 text-sm">
                Belum ada data pemeriksaan
            </div>

            <!-- Mobile Card List -->
            <div class="divide-y divide-gray-50 sm:hidden">
                <div v-for="r in records.data" :key="r.id" class="px-4 py-3">
                    <p class="text-xs text-gray-400 mb-2">{{ formatDate(r.recorded_at) }}</p>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1.5">
                        <div v-if="r.systolic" class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Tekanan</span>
                            <span class="text-xs font-semibold text-gray-800">{{ r.systolic }}/{{ r.diastolic }} <span class="font-normal text-gray-400">mmHg</span></span>
                        </div>
                        <div v-if="r.heart_rate" class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">BPM</span>
                            <span class="text-xs font-semibold text-gray-800">{{ r.heart_rate }}</span>
                        </div>
                        <div v-if="r.blood_sugar" class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Gula</span>
                            <span class="text-xs font-semibold text-gray-800">{{ r.blood_sugar }} <span class="font-normal text-gray-400">mg/dL</span></span>
                        </div>
                        <div v-if="r.weight" class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">BB/TB</span>
                            <span class="text-xs font-semibold text-gray-800">{{ r.weight }}kg / {{ r.height }}cm</span>
                        </div>
                        <div v-if="r.temperature" class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Suhu</span>
                            <span class="text-xs font-semibold text-gray-800">{{ r.temperature }}°C</span>
                        </div>
                        <div v-if="r.oxygen_saturation" class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">SpO2</span>
                            <span class="text-xs font-semibold text-gray-800">{{ r.oxygen_saturation }}%</span>
                        </div>
                    </div>
                    <p v-if="r.notes" class="text-xs text-gray-400 mt-2 italic">{{ r.notes }}</p>
                </div>
            </div>

            <!-- Desktop Table (hidden on mobile) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tekanan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nadi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Gula</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">BB/TB</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Suhu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SpO2</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="records.data.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada data</td>
                        </tr>
                        <tr v-for="r in records.data" :key="r.id" class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-4 py-3 text-xs text-gray-500">{{ formatDate(r.recorded_at) }}</td>
                            <td class="px-4 py-3">
                                <span v-if="r.systolic" class="font-medium text-gray-800">{{ r.systolic }}/{{ r.diastolic }}</span>
                                <span v-else class="text-gray-300">—</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ r.heart_rate ? `${r.heart_rate} bpm` : '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ r.blood_sugar ? `${r.blood_sugar} mg/dL` : '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ r.weight && r.height ? `${r.weight}kg / ${r.height}cm` : '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ r.temperature ? `${r.temperature} C` : '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ r.oxygen_saturation ? `${r.oxygen_saturation}%` : '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="records.last_page > 1" class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-500">
                <span class="text-xs">{{ records.from }}–{{ records.to }} dari {{ records.total }}</span>
                <div class="flex gap-1">
                    <Link v-if="records.prev_page_url" :href="records.prev_page_url" class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs">← Prev</Link>
                    <Link v-if="records.next_page_url" :href="records.next_page_url" class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs">Next →</Link>
                </div>
            </div>
        </div>

        <!-- AI Analysis Section -->
        <div class="mt-6 space-y-4">
            <div class="flex items-center justify-between gap-2">
                <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Riwayat Analisis AI</h3>
                <form @submit.prevent="runAnalysis" class="flex-shrink-0">
                    <button type="submit" :disabled="analyzing || records.data.length === 0"
                        class="flex items-center gap-1.5 bg-gradient-to-r from-primary to-primary-dark text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs sm:text-sm font-medium hover:shadow-md hover:scale-[1.02] transition disabled:opacity-50 disabled:scale-100 disabled:cursor-not-allowed">
                        <svg v-if="analyzing" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ analyzing ? 'Menganalisis...' : 'Buat Analisis AI' }}
                    </button>
                </form>
            </div>

            <!-- Error message if analysis fails -->
            <div v-if="$page.props.errors?.error" class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                {{ $page.props.errors.error }}
            </div>

            <!-- Analyses List -->
            <div v-if="analyses && analyses.length > 0" class="space-y-3">
                <div v-for="(analysis, index) in analyses" :key="analysis.id" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md">
                    <div class="flex items-center justify-between p-4 bg-gray-50/50">
                        <div class="flex items-center gap-3 flex-1 cursor-pointer select-none" @click="toggleAnalysis(index)">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center" :class="index === 0 ? 'bg-primary' : 'bg-primary/10'">
                                <svg class="w-4 h-4" :class="index === 0 ? 'text-white' : 'text-primary'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700">
                                    {{ index === 0 ? 'Analisis Terbaru' : `Analisis #${analyses.length - index}` }}
                                </p>
                                <p class="text-xs text-gray-400">{{ formatDate(analysis.created_at) }} &bull; {{ analysis.records_analyzed }} data</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="confirmDelete(analysis.id)" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            <div class="cursor-pointer select-none w-8 h-8 flex items-center justify-center" @click="toggleAnalysis(index)">
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" :class="{ 'rotate-180': openAnalysis === index }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <transition name="accordion">
                        <div v-if="openAnalysis === index" class="border-t border-gray-100 text-sm">
                            <div class="analysis-result p-4 sm:p-5" v-html="renderMarkdown(analysis.result)"></div>
                            <!-- Action Buttons -->
                            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 px-4 sm:px-6 pb-4 pt-1">
                                <button @click="downloadPdf(analysis)"
                                    class="flex-1 md:flex-none flex items-center justify-center gap-1.5 text-sm font-medium px-4 py-2.5 rounded-xl bg-[#FDD3CF] text-[#B92521] hover:bg-[#F18E8C]/40 transition w-full md:w-auto">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Cetak PDF
                                </button>
                                <button @click="shareWhatsApp(analysis)"
                                    class="flex-1 md:flex-none flex items-center justify-center gap-1.5 text-sm font-medium px-4 py-2.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition w-full md:w-auto">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.673.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    Kirim WA
                                </button>
                            </div>

                            <!-- Video Edukasi Terkait -->
                            <div v-if="getRelatedVideos(analysis.result).length" class="px-5 pb-5 border-t border-gray-50 pt-4">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    Video Edukasi Rekomendasi
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <a v-for="vid in getRelatedVideos(analysis.result).slice(0, 2)" :key="vid.id"
                                        :href="`https://www.youtube.com/watch?v=${vid.youtubeId}`" target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-gray-100 hover:border-[#FF0000]/30 hover:bg-red-50/40 transition-all group">
                                        <div class="relative w-20 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 shadow-sm">
                                            <img :src="`https://i.ytimg.com/vi/${vid.youtubeId}/hqdefault.jpg`" :alt="vid.title" class="w-full h-full object-cover" />
                                            <div class="absolute inset-0 flex items-center justify-center group-hover:bg-black/10 transition">
                                                <div class="w-6 h-6 bg-white/90 rounded-full flex items-center justify-center shadow-sm">
                                                    <svg class="w-3 h-3 text-[#FF0000] ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[12px] font-semibold text-gray-700 truncate">{{ vid.title }}</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">{{ vid.channel }}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
            <div v-else class="text-center py-8 rounded-2xl border border-dashed text-gray-400">
                <p class="text-sm">Belum ada analisis AI untuk pasien ini.</p>
            </div>
        </div>
    </KaderLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import KaderLayout from '@/Layouts/KaderLayout.vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler);

const props = defineProps({
    patient: Object,
    records: Object,
    latestRecord: Object,
    analyses: { type: Array, default: () => [] },
    eduVideos: { type: Array, default: () => [] },
});

const analyzing = ref(false);
const openAnalysis = ref(0);

const chartRecords = computed(() => {
    const list = Array.isArray(props.records?.data) ? props.records.data : [];
    return [...list]
        .slice()
        .reverse()
        .slice(-10);
});

const lineChartData = computed(() => ({
    labels: chartRecords.value.map((r) => {
        const d = new Date(r.recorded_at || r.created_at);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }),
    datasets: [
        {
            label: 'Sistolik',
            data: chartRecords.value.map((r) => r.systolic || null),
            borderColor: '#F0404B',
            backgroundColor: 'rgba(240,64,75,0.07)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#F0404B',
        },
        {
            label: 'Diastolik',
            data: chartRecords.value.map((r) => r.diastolic || null),
            borderColor: '#F18E8C',
            backgroundColor: 'transparent',
            fill: false,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#F18E8C',
        },
        {
            label: 'Detak Jantung',
            data: chartRecords.value.map((r) => r.heart_rate || null),
            borderColor: '#60a5fa',
            backgroundColor: 'transparent',
            fill: false,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#60a5fa',
            borderDash: [4, 3],
        },
    ],
}));

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: { mode: 'index', intersect: false },
    },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 0 } },
        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 9 }, precision: 0 } },
    },
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';

const formatAge = (dob) => {
    const diff = Date.now() - new Date(dob).getTime();
    const age = Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
    return `${age}`;
};

const runAnalysis = () => {
    analyzing.value = true;
    router.post(`/kader/pasien/${props.patient.id}/analyze`, {}, {
        onFinish: () => { analyzing.value = false; },
        onSuccess: () => { openAnalysis.value = 0; }
    });
};

const toggleAnalysis = (index) => {
    openAnalysis.value = openAnalysis.value === index ? null : index;
};

const confirmDelete = (id) => {
    if (confirm('Hapus laporan analisis ini?')) {
        router.delete(`/kader/pasien/${props.patient.id}/analyze/${id}`);
    }
};

const downloadPdf = async (analysis) => {
    const date = formatDate(analysis.created_at);
    let logoHtml = '<div class="logo-text">HEALTIVA</div>';
    try {
        const resp = await fetch('/images/logo.png');
        if (resp.ok) {
            const blob = await resp.blob();
            const b64 = await new Promise(res => { const r = new FileReader(); r.onload = () => res(r.result); r.readAsDataURL(blob); });
            logoHtml = `<img src="${b64}" alt="HEALTIVA" class="logo-img">`;
        }
    } catch (_) {}

    const ageStr = props.patient.date_of_birth ? formatAge(props.patient.date_of_birth) + ' tahun' : '-';
    const genderStr = props.patient.gender === 'male' ? 'Laki-laki' : props.patient.gender === 'female' ? 'Perempuan' : '-';

    const identityHtml = `
    <table class="identity-table">
        <tr>
            <td class="id-label">Nama</td><td class="id-sep">:</td><td class="id-val">${props.patient.name}</td>
            <td class="id-label">Usia</td><td class="id-sep">:</td><td class="id-val">${ageStr}</td>
        </tr>
        <tr>
            <td class="id-label">Tanggal Cetak</td><td class="id-sep">:</td><td class="id-val">${date}</td>
            <td class="id-label">Jenis Kelamin</td><td class="id-sep">:</td><td class="id-val">${genderStr}</td>
        </tr>
    </table><div class="id-divider"></div>`;

    const lines = (analysis.result ?? '').split('\n');
    let html = '<table class="analysis-table">';
    for (const raw of lines) {
        const line = raw.trim();
        if (!line) continue;
        let formatted = line.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\*(.+?)\*/g, '<em>$1</em>');
        if (line.match(/^(#+|(?:\d+\.)?\s*(?:Ringkasan|Analisis|Rekomendasi|Kesimpulan|Status|Evaluasi|Parameter|Tindakan))/i) && !line.includes(': ')) {
            let title = formatted.replace(/^[#\s]+/, '');
            html += `<tr><th colspan="2" class="main-header">${title}</th></tr>`;
        } else if (line.includes(': ')) {
            let parts = formatted.split(': ');
            let key = parts[0];
            let val = parts.slice(1).join(': ');
            html += `<tr><td class="key-cell">${key}</td><td class="val-cell">${val}</td></tr>`;
        } else {
            html += `<tr><td colspan="2" class="text-cell">${formatted}</td></tr>`;
        }
    }
    html += '</table>';

    const content = `<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Laporan Analisis Kesehatan - HEALTIVA</title>
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 18px 30px 30px; color: #1f2937; font-size: 13px; line-height: 1.6; }
  .header-box { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding-bottom: 10px; border-bottom: 3px solid #B92521; }
  .logo-img { height: 88px; width: auto; object-fit: contain; }
  .logo-text { color: #B92521; font-size: 24px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; }
  .header-right { text-align: right; }
  .header-title { font-weight: 700; font-size: 13px; color: #374151; }
  .header-sub { font-size: 11px; color: #6b7280; margin-top: 2px; }
  .identity-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 13px; }
  .id-label { color: #6b7280; width: 18%; font-weight: 600; padding: 4px 6px 4px 0; }
  .id-sep { color: #6b7280; width: 2%; padding: 4px 6px; }
  .id-val { color: #111827; font-weight: 500; width: 30%; padding: 4px 6px; }
  .id-divider { border-top: 1px solid #e5e7eb; margin-bottom: 18px; }
  .analysis-table { width: 100%; border-collapse: collapse; border: 2px solid #111827; margin-bottom: 20px; }
  .analysis-table th, .analysis-table td { border: 1px solid #6b7280; padding: 10px 14px; vertical-align: top; }
  .main-header { background-color: #FEF0F0; color: #A91127; text-align: left; font-size: 14px; border-bottom: 2px solid #111827; border-top: 2px solid #111827; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .key-cell { width: 35%; font-weight: 700; color: #374151; background-color: #f9fafb; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  .val-cell { width: 65%; color: #111827; }
  .text-cell { color: #374151; padding: 12px 14px; }
  strong { color: #000; font-weight: 700; }
  .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #6b7280; padding-top: 15px; border-top: 1px dashed #d1d5db; }
  @media print { body { padding: 0; } .analysis-table { border: 2px solid #000; } .analysis-table th, .analysis-table td { border: 1pt solid #000; } .main-header { border-bottom: 2pt solid #000; border-top: 2pt solid #000; } }
</style></head><body>
<div class="header-box"><div>${logoHtml}</div><div class="header-right"><div class="header-title">Laporan Analisis Kesehatan</div><div class="header-sub">${date}</div></div></div>
${identityHtml}
${html}
<p class="footer">Dihasilkan oleh HEALTIVA AI. Data diproses oleh Kader Posbindu.</p>
<script>window.onload = () => { window.print(); };<\/script>
</body></html>`;
    const blob = new Blob([content], { type: 'text/html;charset=utf-8' });
    const url  = URL.createObjectURL(blob);
    const win  = window.open(url, '_blank');
    if (!win) {
        const a = document.createElement('a');
        a.href = url; a.target = '_blank'; a.click();
    }
    setTimeout(() => URL.revokeObjectURL(url), 60000);
};

const shareWhatsApp = (analysis) => {
    const phone = props.patient.phone;
    const date = formatDate(analysis.created_at);
    const plain = (analysis.result ?? '').replace(/\*\*(.+?)\*\*/g, '*$1*').replace(/\*(.+?)\*/g, '_$1_').replace(/#{1,3}\s/g, '');
    const preview = plain.trim().length > 1200 ? `${plain.trim().slice(0, 1200)}...` : plain.trim();
    const shareLink = analysis.share_url ? `Link PDF laporan:\n${analysis.share_url}\n\n` : '';
    const msg = `*Laporan Analisis Kesehatan Bapak/Ibu ${props.patient.name}*\n${date}\n\n${shareLink}${preview}\n\n_Dihasilkan oleh HEALTIVA Health Monitor dari Kader Posbindu._`;
    window.open(`https://wa.me/${phone ? phone.replace(/[^0-9]/g, '') : ''}?text=${encodeURIComponent(msg)}`, '_blank');
};

const staticEduVideos = [
    { id: 1,  youtubeId: 'b2iSH4VKpXo', keywords: ['hipertensi','tekanan darah','sistolik','diastolik','tensi'], title: 'Hipertensi: Penyebab, Gejala dan Penanganannya', channel: 'Kemenkes RI' },
    { id: 2,  youtubeId: 'l0C_GsEINHs', keywords: ['jantung','koroner','kardio','angina','pembuluh darah'], title: 'Penyakit Jantung Koroner: Kenali dan Cegah Sejak Dini', channel: 'PERKI Indonesia' },
    { id: 3,  youtubeId: 'fIAB4vdqYaQ', keywords: ['diabetes','gula darah','glukosa','hiperglikemia','pradiabetes'], title: 'Diabetes Melitus Tipe 2: Gejala dan Pencegahan', channel: 'Kemenkes RI' },
    { id: 4,  youtubeId: 'wuU1TGqV6IU', keywords: ['gula darah','diabetes','pola makan','diet','nutrisi'], title: 'Pola Makan Sehat untuk Penderita Diabetes', channel: 'PERKENI' },
    { id: 5,  youtubeId: 'tFnFDFNITKs', keywords: ['imt','berat badan','bmi','overweight','obesitas'], title: 'Cara Menghitung IMT dan Kategori Berat Badan', channel: 'Kemenkes RI' }
];

const getRelatedVideos = (text) => {
    if (!text) return [];
    const t = text.toLowerCase();
    
    // Hitung kemiripan dengan keyword static
    const scored = staticEduVideos.map(v => {
        let score = 0;
        for (const kw of (v.keywords ?? [])) {
            const matches = (t.match(new RegExp(kw.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g')) || []).length;
            score += matches * 2;
        }
        return { ...v, score };
    });
    
    const matched = scored.filter(v => v.score > 0).sort((a, b) => b.score - a.score).slice(0, 2);
    const candidates = matched.length > 0 ? matched : staticEduVideos.slice(0, 2);
    
    const liveVideos = props.eduVideos && props.eduVideos.length > 0 ? props.eduVideos : null;
    if (!liveVideos) return candidates;

    // Gantikan dengan video real dari YouTube jika cocok keywordnya
    return candidates.map(sv => {
        const liveMatch = liveVideos.find(lv =>
            (sv.keywords ?? []).some(kw => (lv.title ?? '').toLowerCase().includes(kw))
        );
        return liveMatch
            ? { ...sv, youtubeId: liveMatch.youtubeId, channel: liveMatch.channel, title: liveMatch.title }
            : sv;
    });
};

const formatInline = (text) => text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\*(.+?)\*/g, '<em>$1</em>');
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
            closeUl(); html += `<div class="ar-subheading">${formatInline(line.slice(4))}</div>`;
        } else if (line.startsWith('# ')) {
            closeUl(); closeSection(); html += `<div class="ar-title">${formatInline(line.slice(2))}</div>`;
        } else if (line.startsWith('- ') || line.startsWith('* ')) {
            if (!inUl) { html += '<ul class="ar-list">'; inUl = true; }
            html += `<li>${formatInline(line.slice(2))}</li>`;
        } else if (line === '') {
            closeUl();
        } else {
            closeUl(); html += `<p class="ar-p">${formatInline(line)}</p>`;
        }
    }
    closeUl(); closeSection();
    return html;
};
</script>

<style scoped>
.accordion-enter-active { animation: fadeInUp 0.3s ease both; }
.accordion-leave-active { animation: fadeInUp 0.2s ease reverse both; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

:deep(.analysis-result) { font-size: 0.8125rem; line-height: 1.65; color: #374151; }
:deep(.ar-title) { font-size: 1rem; font-weight: 700; color: #B92521; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #EFDBDC; }
:deep(.ar-section) { border-radius: 0.625rem; border-left: 4px solid; padding: 0.75rem 0.875rem; margin-bottom: 0.625rem; background: #fafafa; }
:deep(.ar-section:last-child) { margin-bottom: 0; }
:deep(.ar-heading) { font-weight: 700; font-size: 0.8125rem; margin-bottom: 0.5rem; }
:deep(.ar-subheading) { font-weight: 600; font-size: 0.8rem; color: #6b7280; margin: 0.375rem 0 0.25rem; }
:deep(.ar-p) { margin: 0.25rem 0 0.375rem; color: #4b5563; }
:deep(.ar-list) { list-style: none; padding: 0; margin: 0.25rem 0 0; display: flex; flex-direction: column; gap: 0.3rem; }
:deep(.ar-list li) { position: relative; padding-left: 1.1rem; color: #4b5563; }
:deep(.ar-list li::before) { content: ''; position: absolute; left: 0; top: 0.48rem; width: 0.4rem; height: 0.4rem; border-radius: 50%; background: currentColor; opacity: 0.55; }
:deep(.ar-section strong) { font-weight: 700; }
:deep(.ar-violet) { border-color: #F0404B; background: #FFF5F5; } :deep(.ar-violet .ar-heading) { color: #B92521; } :deep(.ar-violet .ar-list li::before) { background: #F0404B; }
:deep(.ar-blue) { border-color: #B92521; background: #FEF0F0; } :deep(.ar-blue .ar-heading) { color: #A91127; } :deep(.ar-blue .ar-list li::before) { background: #B92521; }
:deep(.ar-green) { border-color: #B74443; background: #F9ECEC; } :deep(.ar-green .ar-heading) { color: #B74443; } :deep(.ar-green .ar-list li::before) { background: #B74443; }
:deep(.ar-amber) { border-color: #E48888; background: #FDF4F4; } :deep(.ar-amber .ar-heading) { color: #B92521; } :deep(.ar-amber .ar-list li::before) { background: #E48888; }
:deep(.ar-rose) { border-color: #A91127; background: #FFF0F0; } :deep(.ar-rose .ar-heading) { color: #A91127; } :deep(.ar-rose .ar-list li::before) { background: #A91127; }
:deep(.ar-teal) { border-color: #F18E8C; background: #FDF2F2; } :deep(.ar-teal .ar-heading) { color: #B92521; } :deep(.ar-teal .ar-list li::before) { background: #F18E8C; }
</style>
