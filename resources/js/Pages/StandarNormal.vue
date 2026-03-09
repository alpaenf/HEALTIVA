<template>
    <AppLayout>
        <Head title="Standar Normal Pemeriksaan" />

        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-primary to-primary-dark text-white p-5 mb-5 animate-fade-in-down">
            <div class="absolute -top-6 -right-6 w-28 h-28 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-8 -left-4 w-20 h-20 bg-white/5 rounded-full"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Standar Normal Pemeriksaan</h1>
                    <p class="text-white/70 text-xs mt-0.5">Referensi nilai normal berdasarkan Kemenkes RI & WHO</p>
                </div>
            </div>
        </div>

        <!-- Quick Ref Pills -->
        <div class="flex gap-2 overflow-x-auto pb-2 mb-5 scrollbar-hide">
            <a v-for="s in standards" :key="s.id" :href="'#std-' + s.id"
                class="flex-shrink-0 px-3 py-1.5 bg-white border border-gray-200 rounded-full text-xs font-medium text-gray-600 hover:border-primary/40 hover:text-primary transition">
                {{ s.title }}
            </a>
        </div>

        <!-- Standards Cards -->
        <div class="space-y-5">
            <div v-for="std in standards" :key="std.id" :id="'std-' + std.id"
                class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm animate-fade-in-up">

                <!-- Card Header -->
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3"
                    :style="`background: linear-gradient(135deg, ${std.bgFrom}, ${std.bgTo})`">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" :style="`background: ${std.iconBg}`">
                        <!-- Tekanan Darah -->
                        <svg v-if="std.id === 'bp'" class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <!-- Detak Jantung -->
                        <svg v-else-if="std.id === 'hr'" class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                        <!-- Gula Darah -->
                        <svg v-else-if="std.id === 'sugar'" class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <!-- BMI -->
                        <svg v-else-if="std.id === 'bmi'" class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        <!-- Suhu Tubuh -->
                        <svg v-else-if="std.id === 'temp'" class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3a3 3 0 00-3 3v8.17A4 4 0 1015 14.17V6a3 3 0 00-3-3H9z"/></svg>
                        <!-- SpO2 -->
                        <svg v-else-if="std.id === 'spo2'" class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <!-- Kolesterol -->
                        <svg v-else class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800">{{ std.title }}</h2>
                        <p class="text-xs text-gray-500">{{ std.subtitle }}</p>
                    </div>
                    <span class="ml-auto text-[10px] font-bold text-white bg-primary/80 px-2 py-0.5 rounded-full">{{ std.source }}</span>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-2.5 text-xs font-bold text-gray-500 uppercase tracking-wide">Kategori</th>
                                <th class="text-left px-4 py-2.5 text-xs font-bold text-gray-500 uppercase tracking-wide">Nilai</th>
                                <th class="text-left px-4 py-2.5 text-xs font-bold text-gray-500 uppercase tracking-wide">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, ri) in std.rows" :key="ri"
                                class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3 font-medium text-gray-700">{{ row.label }}</td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ row.value }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold" :class="statusClass(row.status)">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="statusDot(row.status)"></span>
                                        {{ statusLabel(row.status) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Note -->
                <div v-if="std.note" class="px-5 py-3 bg-amber-50 border-t border-amber-100 text-xs text-amber-700 flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ std.note }}</span>
                </div>
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="mt-6 bg-gradient-to-br from-[#FFF5F5] to-[#EFDBDC] rounded-2xl p-5 border border-[#F18E8C]/30 animate-fade-in-up">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold text-gray-800 text-sm mb-1">Catatan Penting</p>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Nilai referensi ini bersifat umum. Kondisi individu dapat berbeda-beda. Selalu konsultasikan hasil pemeriksaan Anda dengan dokter atau tenaga kesehatan untuk interpretasi yang akurat dan penanganan yang tepat.
                    </p>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const statusClass = (s) => ({
    normal:   'bg-green-50 text-green-700',
    warning:  'bg-amber-50 text-amber-700',
    danger:   'bg-red-50 text-red-700',
    critical: 'bg-red-100 text-red-800',
    info:     'bg-blue-50 text-blue-700',
}[s] ?? 'bg-gray-100 text-gray-600');

const statusDot = (s) => ({
    normal:   'bg-green-500',
    warning:  'bg-amber-500',
    danger:   'bg-red-500',
    critical: 'bg-red-700',
    info:     'bg-blue-500',
}[s] ?? 'bg-gray-400');

const statusLabel = (s) => ({
    normal:   'Normal',
    warning:  'Perlu Perhatian',
    danger:   'Tidak Normal',
    critical: 'Kritis',
    info:     'Informasi',
}[s] ?? s);

const standards = [
    {
        id: 'bp', title: 'Tekanan Darah', subtitle: 'Nilai sistolik & diastolik (mmHg)',
        source: 'Kemenkes RI 2023', bgFrom: '#FFF5F5', bgTo: '#FEF2F2', iconBg: '#FDD3CF',
        note: 'Pengukuran sebaiknya dilakukan 2x dengan jeda 5 menit saat istirahat.',
        rows: [
            { label: 'Normal',           value: '< 120 / < 80 mmHg',    status: 'normal'  },
            { label: 'Elevated',         value: '120â€“129 / < 80 mmHg',  status: 'warning' },
            { label: 'Hipertensi Tkt. 1',value: '130â€“139 / 80â€“89 mmHg', status: 'danger'  },
            { label: 'Hipertensi Tkt. 2',value: '>= 140 / >= 90 mmHg',   status: 'danger'  },
            { label: 'Krisis Hipertensi',value: '> 180 / > 120 mmHg',   status: 'critical'},
        ],
    },
    {
        id: 'hr', title: 'Detak Jantung', subtitle: 'Denyut nadi istirahat (BPM)',
        source: 'AHA / Kemenkes', bgFrom: '#FFF5F5', bgTo: '#FEF0F0', iconBg: '#EFDBDC',
        note: 'Atlet terlatih dapat memiliki detak jantung 40â€“60 bpm dan tetap dianggap normal.',
        rows: [
            { label: 'Terlalu Lambat (Bradikardia)', value: '< 60 BPM',    status: 'warning' },
            { label: 'Normal',                       value: '60â€“100 BPM',  status: 'normal'  },
            { label: 'Takikardia Ringan',             value: '100â€“120 BPM',status: 'warning' },
            { label: 'Takikardia Berat',              value: '> 120 BPM',  status: 'danger'  },
        ],
    },
    {
        id: 'sugar', title: 'Gula Darah', subtitle: 'Kadar glukosa darah puasa (mg/dL)',
        source: 'PERKENI 2021', bgFrom: '#FFF5F5', bgTo: '#FEFCE8', iconBg: '#FDD3CF',
        note: 'Nilai di atas adalah untuk gula darah puasa (GDP). Gula darah 2 jam PP: Normal <140, Pradiabetes 140â€“199, DM >=200 mg/dL.',
        rows: [
            { label: 'Hipoglikemia',   value: '< 70 mg/dL',    status: 'danger'  },
            { label: 'Normal (puasa)', value: '70â€“99 mg/dL',   status: 'normal'  },
            { label: 'Pradiabetes',    value: '100â€“125 mg/dL', status: 'warning' },
            { label: 'Diabetes',       value: '>= 126 mg/dL',   status: 'danger'  },
        ],
    },
    {
        id: 'bmi', title: 'Indeks Massa Tubuh (BMI)', subtitle: 'Klasifikasi WHO Asia-Pacific',
        source: 'WHO 2000 / Kemenkes', bgFrom: '#FFF5F5', bgTo: '#FEF2F2', iconBg: '#EFDBDC',
        note: 'Cutoff BMI untuk populasi Asia berbeda dari Kaukasia. Indonesia menggunakan klasifikasi WHO Asia-Pacific.',
        rows: [
            { label: 'Kurus (Underweight)', value: '< 18.5',     status: 'warning' },
            { label: 'Normal',              value: '18.5 â€“ 22.9',status: 'normal'  },
            { label: 'Overweight',          value: '23.0 â€“ 27.4',status: 'warning' },
            { label: 'Obesitas Grade I',    value: '27.5 â€“ 32.4',status: 'danger'  },
            { label: 'Obesitas Grade II',   value: '>= 32.5',     status: 'danger'  },
        ],
    },
    {
        id: 'temp', title: 'Suhu Tubuh', subtitle: 'Temperatur oral/aksila (°C)',
        source: 'Kemenkes RI', bgFrom: '#FFF5F5', bgTo: '#FFF0EC', iconBg: '#FDD3CF',
        note: 'Pengukuran aksila biasanya 0.3â€“0.5°C lebih rendah dari oral. Pengukuran rektal lebih tinggi Â±0.5°C.',
        rows: [
            { label: 'Hipotermia',        value: '< 35.0 °C',     status: 'critical'},
            { label: 'Sedikit Rendah',    value: '35.0â€“35.9 °C',  status: 'warning' },
            { label: 'Normal',            value: '36.0â€“37.5 °C',  status: 'normal'  },
            { label: 'Demam Ringan',      value: '37.5â€“38.4 °C',  status: 'warning' },
            { label: 'Demam Tinggi',      value: '38.5â€“40.0 °C',  status: 'danger'  },
            { label: 'Demam Sangat Tinggi',value: '> 40.0 °C',    status: 'critical'},
        ],
    },
    {
        id: 'spo2', title: 'Saturasi Oksigen (SpO2)', subtitle: 'Kadar oksigen dalam darah (%)',
        source: 'WHO / Kemenkes', bgFrom: '#FFF5F5', bgTo: '#FEF2F2', iconBg: '#EFDBDC',
        note: 'Perokok aktif dan penderita PPOK mungkin memiliki baseline SpO2 yang lebih rendah. Nilai <94% perlu evaluasi dokter.',
        rows: [
            { label: 'Normal',               value: '>= 95%',   status: 'normal'  },
            { label: 'Hipoksia Ringan',       value: '90â€“94%',  status: 'warning' },
            { label: 'Hipoksia Berat',        value: '85â€“89%',  status: 'danger'  },
            { label: 'Darurat (segera UGD)',  value: '< 85%',   status: 'critical'},
        ],
    },
    {
        id: 'chol', title: 'Kolesterol (Referensi)', subtitle: 'Kadar lipid darah (mg/dL)',
        source: 'PERKI / Kemenkes RI', bgFrom: '#FFF5F5', bgTo: '#FEF2F2', iconBg: '#EFDBDC',
        note: 'Nilai ini adalah referensi umum. Target kolesterol individual bergantung pada faktor risiko jantung yang lain.',
        rows: [
            { label: 'Kolesterol Total â€“ Optimal', value: '< 200 mg/dL', status: 'normal'  },
            { label: 'Kolesterol Total â€“ Batas',   value: '200â€“239 mg/dL',status: 'warning'},
            { label: 'Kolesterol Total â€“ Tinggi',  value: '>= 240 mg/dL', status: 'danger'  },
            { label: 'HDL (Baik) â€“ Rendah',        value: '< 40 mg/dL',  status: 'danger'  },
            { label: 'HDL (Baik) â€“ Normal',        value: '40â€“59 mg/dL', status: 'warning' },
            { label: 'HDL (Baik) â€“ Optimal',       value: '>= 60 mg/dL',  status: 'normal'  },
            { label: 'LDL (Jahat) â€“ Optimal',      value: '< 100 mg/dL', status: 'normal'  },
            { label: 'LDL (Jahat) â€“ Tinggi',       value: '>= 160 mg/dL', status: 'danger'  },
        ],
    },
];
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
