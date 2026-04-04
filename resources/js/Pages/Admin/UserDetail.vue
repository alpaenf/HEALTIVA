<template>
    <AdminLayout>
        <Head :title="`Detail - ${user.name}`" />

        <!-- Back button -->
        <div class="mb-5 animate-fade-in-down">
            <Link href="/admin/patients"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Pengguna
            </Link>
        </div>

        <!-- Profile Header Card -->
        <div class="card-Healtiva p-5 mb-5 animate-fade-in-up">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl overflow-hidden flex-shrink-0 shadow-md">
                    <img v-if="user.avatar"
                        :src="user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`"
                        :alt="user.name"
                        class="w-full h-full object-cover" />
                    <div v-else
                        class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white text-2xl font-bold">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl font-bold text-gray-800">{{ user.name }}</h1>
                    <p class="text-sm text-gray-400">{{ user.email }}</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span v-if="user.gender" class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">
                            {{ user.gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                        <span v-if="user.date_of_birth" class="text-xs bg-green-50 text-green-600 px-2 py-0.5 rounded-full">
                            {{ age }} tahun
                        </span>
                        <span v-if="user.phone" class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                            {{ user.phone }}
                        </span>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <div class="text-2xl font-bold text-primary">{{ records.length }}</div>
                    <div class="text-xs text-gray-400">Data Kesehatan</div>
                    <div class="text-xl font-bold text-violet-600 mt-1">{{ analyses.length }}</div>
                    <div class="text-xs text-gray-400">Analisis AI</div>
                </div>
            </div>
        </div>

        <!-- Health Trend Chart -->
        <div v-if="records.length >= 2" class="card-Healtiva p-5 mb-5 animate-fade-in-up delay-50">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-700">Tren Kesehatan</h2>
                <span class="text-xs text-gray-400">{{ records.length }} data terakhir</span>
            </div>
            <!-- Legend -->
            <div class="flex items-center gap-4 mb-3">
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
            <div class="h-48">
                <Line :data="lineChartData" :options="lineChartOptions" />
            </div>
        </div>

        <!-- Health Records -->
        <div class="card-Healtiva mb-5 overflow-hidden animate-fade-in-up delay-75">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="font-semibold text-gray-700">Riwayat Data Kesehatan</h2>
                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-medium">{{ records.length }} data</span>
            </div>

            <div v-if="records.length" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Tanggal</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Tekanan</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Nadi</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Gula</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">BB/TB</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Suhu</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">SpO2</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="(r, i) in records" :key="r.id"
                            class="hover:bg-gray-50/60 transition-colors"
                            :style="`animation-delay:${i * 30}ms`">
                            <td class="px-4 py-2.5 text-xs text-gray-500 whitespace-nowrap">
                                {{ formatDate(r.recorded_at || r.created_at) }}
                            </td>
                            <td class="px-4 py-2.5">
                                <span v-if="r.systolic" :class="bpClass(r.systolic, r.diastolic)"
                                    class="text-xs font-medium px-2 py-0.5 rounded-lg">
                                    {{ r.systolic }}/{{ r.diastolic }}
                                </span>
                                <span v-else class="text-gray-300 text-xs">-</span>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.heart_rate ? `${r.heart_rate} bpm` : '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.blood_sugar ? `${r.blood_sugar} mg/dL` : '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.weight && r.height ? `${r.weight}kg / ${r.height}cm` : '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.temperature ? `${r.temperature} C` : '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.oxygen_saturation ? `${r.oxygen_saturation}%` : '-' }}
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <button
                                    @click="deleteRecord(r)"
                                    :disabled="deletingRecordId === r.id"
                                    class="inline-flex items-center justify-center text-xs font-medium px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ deletingRecordId === r.id ? 'Menghapus...' : 'Hapus' }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="py-12 text-center text-gray-400 text-sm">
                Belum ada data kesehatan
            </div>
        </div>

        <!-- AI Analyses -->
        <div class="card-Healtiva overflow-hidden animate-fade-in-up delay-150">
            <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                <h2 class="font-semibold text-gray-700">Riwayat Analisis AI</h2>
                <span class="text-xs bg-violet-50 text-violet-600 px-2 py-1 rounded-full font-medium">{{ analyses.length }} analisis</span>
            </div>

            <div v-if="analyses.length" class="divide-y divide-gray-50">
                <div v-for="(a, index) in analyses" :key="a.id" class="overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3.5 cursor-pointer hover:bg-gray-50/60 transition"
                        @click="openAi = openAi === index ? null : index">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                                :class="index === 0 ? 'bg-violet-600' : 'bg-violet-100'">
                                <svg class="w-4 h-4" :class="index === 0 ? 'text-white' : 'text-violet-500'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ index === 0 ? 'Analisis Terbaru' : `Analisis #${analyses.length - index}` }}
                                </p>
                                <p class="text-xs text-gray-400">{{ formatDateFull(a.created_at) }} | {{ a.records_analyzed }} data</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-300"
                            :class="{ 'rotate-180': openAi === index }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <transition name="accordion">
                        <div v-if="openAi === index" class="border-t border-gray-100">
                            <div class="analysis-result p-4" v-html="renderMarkdown(a.result)"></div>
                            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 px-4 pb-4 pt-1">
                                <button @click="downloadPdf(a)"
                                    class="flex-1 md:flex-none flex items-center justify-center gap-1.5 text-sm font-medium px-4 py-2.5 rounded-xl bg-[#FDD3CF] text-[#B92521] hover:bg-[#F18E8C]/40 transition w-full md:w-auto">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Cetak PDF
                                </button>
                                <button @click="shareWhatsApp(a)"
                                    class="flex-1 md:flex-none flex items-center justify-center gap-1.5 text-sm font-medium px-4 py-2.5 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition w-full md:w-auto">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.673.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    Kirim WA
                                </button>
                            </div>

                            <div v-if="getRelatedVideos(a.result).length" class="px-5 pb-5 border-t border-gray-50 pt-4">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    Video Edukasi Rekomendasi
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <a v-for="vid in getRelatedVideos(a.result).slice(0, 2)" :key="vid.id"
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
            <div v-else class="py-12 text-center text-gray-400 text-sm">
                Belum ada analisis AI
            </div>
        </div>

        <transition name="modal-fade">
            <div
                v-if="showDeleteModal && pendingDeleteRecord"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <div class="absolute inset-0 bg-black/40" @click="closeDeleteModal"></div>

                <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-100 p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-gray-800">Hapus Riwayat Kesehatan?</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Data tanggal {{ pendingDeleteDate }} akan dihapus permanen dan tidak bisa dibatalkan.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-2.5">
                        <button
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition"
                            :disabled="deletingRecordId !== null"
                            @click="closeDeleteModal"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            class="px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="deletingRecordId !== null"
                            @click="confirmDeleteRecord"
                        >
                            {{ deletingRecordId !== null ? 'Menghapus...' : 'Ya, Hapus' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler);

const props = defineProps({
    user: Object,
    records: Array,
    analyses: Array,
    eduVideos: { type: Array, default: () => [] },
});

const openAi = ref(0);
const deletingRecordId = ref(null);
const showDeleteModal = ref(false);
const pendingDeleteRecord = ref(null);
const pendingDeleteDate = ref('');

// Chart dari records (urut dari lama ke baru)
const chartRecords = computed(() => [...props.records].reverse().slice(-10));

const lineChartData = computed(() => ({
    labels: chartRecords.value.map(r => {
        const d = new Date(r.recorded_at || r.created_at);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }),
    datasets: [
        {
            label: 'Sistolik',
            data: chartRecords.value.map(r => r.systolic || null),
            borderColor: '#F0404B',
            backgroundColor: 'rgba(240,64,75,0.07)',
            fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#F0404B',
        },
        {
            label: 'Diastolik',
            data: chartRecords.value.map(r => r.diastolic || null),
            borderColor: '#F18E8C',
            backgroundColor: 'transparent',
            fill: false, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#F18E8C',
        },
        {
            label: 'Detak Jantung',
            data: chartRecords.value.map(r => r.heart_rate || null),
            borderColor: '#60a5fa',
            backgroundColor: 'transparent',
            fill: false, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#60a5fa',
            borderDash: [4, 3],
        },
    ],
}));

const lineChartOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 0 } },
        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 9 }, precision: 0 } },
    },
};

const age = computed(() => {
    if (!props.user.date_of_birth) return null;
    const dob = new Date(props.user.date_of_birth);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    return age;
});

const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
const formatDateFull = (d) => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });

const bpClass = (sys, dia) => {
    if (sys >= 140 || dia >= 90) return 'bg-red-100 text-red-700';
    if (sys >= 130 || dia >= 80) return 'bg-orange-100 text-orange-700';
    if (sys >= 120) return 'bg-yellow-100 text-yellow-700';
    if (sys < 90 || dia < 60) return 'bg-blue-100 text-blue-600';
    return 'bg-green-100 text-green-700';
};

const formatInline = (text) =>
    text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>');

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
            const c = sectionColors[colorIdx++ % sectionColors.length];
            inSection = true;
            html += `<div class="ar-section ar-${c}"><div class="ar-heading">${formatInline(line.slice(3))}</div>`;
        } else if (line.startsWith('### ')) {
            closeUl();
            html += `<div class="ar-subheading">${formatInline(line.slice(4))}</div>`;
        } else if (line.startsWith('# ')) {
            closeUl(); closeSection();
            html += `<div class="ar-title">${formatInline(line.slice(2))}</div>`;
        } else if (line.startsWith('- ') || line.startsWith('* ')) {
            if (!inUl) { html += '<ul class="ar-list">'; inUl = true; }
            html += `<li>${formatInline(line.slice(2))}</li>`;
        } else if (line === '') {
            closeUl();
        } else {
            closeUl();
            html += `<p class="ar-p">${formatInline(line)}</p>`;
        }
    }
    closeUl(); closeSection();
    return html;
};

const formatAge = (dob) => {
    const diff = Date.now() - new Date(dob).getTime();
    const ageYears = Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
    return `${ageYears}`;
};

const downloadPdf = async (analysis) => {
    const date = formatDateFull(analysis.created_at);
    let logoHtml = '<div class="logo-text">HEALTIVA</div>';
    try {
        const resp = await fetch('/images/logo.png');
        if (resp.ok) {
            const blob = await resp.blob();
            const b64 = await new Promise((res) => {
                const r = new FileReader();
                r.onload = () => res(r.result);
                r.readAsDataURL(blob);
            });
            logoHtml = `<img src="${b64}" alt="HEALTIVA" class="logo-img">`;
        }
    } catch (_) {}

    const ageStr = props.user.date_of_birth ? `${formatAge(props.user.date_of_birth)} tahun` : '-';
    const genderStr = props.user.gender === 'male' ? 'Laki-laki' : props.user.gender === 'female' ? 'Perempuan' : '-';

    const identityHtml = `
    <table class="identity-table">
        <tr>
            <td class="id-label">Nama</td><td class="id-sep">:</td><td class="id-val">${props.user.name}</td>
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
        const formatted = line.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\*(.+?)\*/g, '<em>$1</em>');
        if (line.match(/^(#+|(?:\d+\.)?\s*(?:Ringkasan|Analisis|Rekomendasi|Kesimpulan|Status|Evaluasi|Parameter|Tindakan))/i) && !line.includes(': ')) {
            const title = formatted.replace(/^[#\s]+/, '');
            html += `<tr><th colspan="2" class="main-header">${title}</th></tr>`;
        } else if (line.includes(': ')) {
            const parts = formatted.split(': ');
            const key = parts[0];
            const val = parts.slice(1).join(': ');
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
<p class="footer">Dihasilkan oleh HEALTIVA AI. Data diproses oleh Admin Posbindu.</p>
<script>window.onload = () => { window.print(); };<\/script>
</body></html>`;

    const blob = new Blob([content], { type: 'text/html;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const win = window.open(url, '_blank');
    if (!win) {
        const a = document.createElement('a');
        a.href = url;
        a.target = '_blank';
        a.click();
    }
    setTimeout(() => URL.revokeObjectURL(url), 60000);
};

const shareWhatsApp = (analysis) => {
    const phone = props.user.phone;
    const date = formatDateFull(analysis.created_at);
    const plain = (analysis.result ?? '')
        .replace(/\*\*(.+?)\*\*/g, '*$1*')
        .replace(/\*(.+?)\*/g, '_$1_')
        .replace(/#{1,3}\s/g, '');
    const preview = plain.trim().length > 1200 ? `${plain.trim().slice(0, 1200)}...` : plain.trim();
    const shareLink = analysis.share_url ? `Link PDF laporan:\n${analysis.share_url}\n\n` : '';
    const msg = `*Laporan Analisis Kesehatan Bapak/Ibu ${props.user.name}*\n${date}\n\n${shareLink}${preview}\n\n_Dihasilkan oleh HEALTIVA Health Monitor dari Admin Posbindu._`;
    window.open(`https://wa.me/${phone ? phone.replace(/[^0-9]/g, '') : ''}?text=${encodeURIComponent(msg)}`, '_blank');
};

const deleteRecord = (record) => {
    pendingDeleteRecord.value = record;
    pendingDeleteDate.value = formatDate(record.recorded_at || record.created_at);
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    if (deletingRecordId.value !== null) return;
    showDeleteModal.value = false;
    pendingDeleteRecord.value = null;
    pendingDeleteDate.value = '';
};

const confirmDeleteRecord = () => {
    if (!pendingDeleteRecord.value) return;

    deletingRecordId.value = pendingDeleteRecord.value.id;
    router.delete(`/admin/patients/${props.user.id}/records/${pendingDeleteRecord.value.id}`, {
        preserveScroll: true,
        onFinish: () => {
            deletingRecordId.value = null;
            closeDeleteModal();
        },
    });
};

const staticEduVideos = [
    { id: 1, youtubeId: 'b2iSH4VKpXo', keywords: ['hipertensi', 'tekanan darah', 'sistolik', 'diastolik', 'tensi'], title: 'Hipertensi: Penyebab, Gejala dan Penanganannya', channel: 'Kemenkes RI' },
    { id: 2, youtubeId: 'l0C_GsEINHs', keywords: ['jantung', 'koroner', 'kardio', 'angina', 'pembuluh darah'], title: 'Penyakit Jantung Koroner: Kenali dan Cegah Sejak Dini', channel: 'PERKI Indonesia' },
    { id: 3, youtubeId: 'fIAB4vdqYaQ', keywords: ['diabetes', 'gula darah', 'glukosa', 'hiperglikemia', 'pradiabetes'], title: 'Diabetes Melitus Tipe 2: Gejala dan Pencegahan', channel: 'Kemenkes RI' },
    { id: 4, youtubeId: 'wuU1TGqV6IU', keywords: ['gula darah', 'diabetes', 'pola makan', 'diet', 'nutrisi'], title: 'Pola Makan Sehat untuk Penderita Diabetes', channel: 'PERKENI' },
    { id: 5, youtubeId: 'tFnFDFNITKs', keywords: ['imt', 'berat badan', 'bmi', 'overweight', 'obesitas'], title: 'Cara Menghitung IMT dan Kategori Berat Badan', channel: 'Kemenkes RI' },
];

const getRelatedVideos = (text) => {
    if (!text) return [];
    const t = text.toLowerCase();

    const scored = staticEduVideos.map((v) => {
        let score = 0;
        for (const kw of v.keywords ?? []) {
            const matches = (t.match(new RegExp(kw.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g')) || []).length;
            score += matches * 2;
        }
        return { ...v, score };
    });

    const matched = scored
        .filter((v) => v.score > 0)
        .sort((a, b) => b.score - a.score)
        .slice(0, 2);
    const candidates = matched.length > 0 ? matched : staticEduVideos.slice(0, 2);

    const liveVideos = props.eduVideos && props.eduVideos.length > 0 ? props.eduVideos : null;
    if (!liveVideos) return candidates;

    return candidates.map((sv) => {
        const liveMatch = liveVideos.find((lv) =>
            (sv.keywords ?? []).some((kw) => (lv.title ?? '').toLowerCase().includes(kw)),
        );
        return liveMatch
            ? { ...sv, youtubeId: liveMatch.youtubeId, channel: liveMatch.channel, title: liveMatch.title }
            : sv;
    });
};
</script>

<style scoped>
.accordion-enter-active { animation: fadeInUp 0.25s ease both; }
.accordion-leave-active { animation: fadeInUp 0.15s ease reverse both; }

:deep(.analysis-result) { font-size: 0.8125rem; line-height: 1.65; color: #374151; }
:deep(.ar-title) { font-size: 1rem; font-weight: 700; color: #B92521; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #EFDBDC; }
:deep(.ar-section) { border-radius: 0.625rem; border-left: 4px solid; padding: 0.75rem 0.875rem; margin-bottom: 0.625rem; background: #fafafa; }
:deep(.ar-heading) { font-weight: 700; font-size: 0.8125rem; margin-bottom: 0.5rem; }
:deep(.ar-subheading) { font-weight: 600; font-size: 0.8rem; color: #6b7280; margin: 0.375rem 0 0.25rem; }
:deep(.ar-p) { margin: 0.25rem 0 0.375rem; color: #4b5563; }
:deep(.ar-list) { list-style: none; padding: 0; margin: 0.25rem 0 0; display: flex; flex-direction: column; gap: 0.3rem; }
:deep(.ar-list li) { position: relative; padding-left: 1.1rem; color: #4b5563; }
:deep(.ar-list li::before) { content: ''; position: absolute; left: 0; top: 0.48rem; width: 0.4rem; height: 0.4rem; border-radius: 50%; opacity: 0.55; }
:deep(.ar-section strong) { font-weight: 700; }
:deep(.ar-violet) { border-color: #F0404B; background: #FFF5F5; }
:deep(.ar-violet .ar-heading) { color: #B92521; }
:deep(.ar-violet .ar-list li::before) { background: #F0404B; }
:deep(.ar-blue) { border-color: #B92521; background: #FEF0F0; }
:deep(.ar-blue .ar-heading) { color: #A91127; }
:deep(.ar-blue .ar-list li::before) { background: #B92521; }
:deep(.ar-green) { border-color: #B74443; background: #F9ECEC; }
:deep(.ar-green .ar-heading) { color: #B74443; }
:deep(.ar-green .ar-list li::before) { background: #B74443; }
:deep(.ar-amber) { border-color: #E48888; background: #FDF4F4; }
:deep(.ar-amber .ar-heading) { color: #B92521; }
:deep(.ar-amber .ar-list li::before) { background: #E48888; }
:deep(.ar-rose) { border-color: #A91127; background: #FFF0F0; }
:deep(.ar-rose .ar-heading) { color: #A91127; }
:deep(.ar-rose .ar-list li::before) { background: #A91127; }
:deep(.ar-teal) { border-color: #F18E8C; background: #FDF2F2; }
:deep(.ar-teal .ar-heading) { color: #B92521; }
:deep(.ar-teal .ar-list li::before) { background: #F18E8C; }

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}
</style>
