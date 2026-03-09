<template>
    <AdminLayout>
        <Head :title="`Detail - ${user.name}`" />

        <!-- Back button -->
        <div class="mb-5 animate-fade-in-down">
            <Link href="/admin/users"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-primary transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Pengguna
            </Link>
        </div>

        <!-- Profile Header Card -->
        <div class="card-medix p-5 mb-5 animate-fade-in-up">
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
        <div v-if="records.length >= 2" class="card-medix p-5 mb-5 animate-fade-in-up delay-50">
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
        <div class="card-medix mb-5 overflow-hidden animate-fade-in-up delay-75">
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
                                <span v-else class="text-gray-300 text-xs">â€”</span>
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.heart_rate ? `${r.heart_rate} bpm` : 'â€”' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.blood_sugar ? `${r.blood_sugar} mg/dL` : 'â€”' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.weight && r.height ? `${r.weight}kg / ${r.height}cm` : 'â€”' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.temperature ? `${r.temperature}°C` : 'â€”' }}
                            </td>
                            <td class="px-4 py-2.5 text-xs text-gray-600">
                                {{ r.oxygen_saturation ? `${r.oxygen_saturation}%` : 'â€”' }}
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
        <div class="card-medix overflow-hidden animate-fade-in-up delay-150">
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
                                <p class="text-xs text-gray-400">{{ formatDateFull(a.created_at) }} Â· {{ a.records_analyzed }} data</p>
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
                        </div>
                    </transition>
                </div>
            </div>
            <div v-else class="py-12 text-center text-gray-400 text-sm">
                Belum ada analisis AI
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler);

const props = defineProps({
    user: Object,
    records: Array,
    analyses: Array,
});

const openAi = ref(0);

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
const formatDateFull = (d) => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });

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
</style>
