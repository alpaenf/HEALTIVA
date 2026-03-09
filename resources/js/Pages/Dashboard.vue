<template>
    <AppLayout>
        <Head title="Dashboard" />

        <!-- Greeting Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-[#C63632] to-primary-dark text-white p-6 mb-5 animate-fade-in-down shadow-xl shadow-primary/20">
            <!-- Modern subtle abstract decorations (White/Glassy glows instead of hard circles) -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 blur-3xl rounded-full -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/10 blur-2xl rounded-full translate-y-1/2 -translate-x-1/4"></div>
            
            <div class="relative flex items-center justify-between z-10">
                <div>
                    <p class="text-white/80 text-sm font-medium tracking-wide uppercase">{{ greeting }}</p>
                    <h1 class="text-3xl font-bold mt-1 tracking-tight text-white">{{ user?.name?.split(' ')[0] }}</h1>
                    <p class="text-white/70 text-xs mt-1.5 font-medium">{{ currentDate }}</p>
                </div>
                <div class="text-right flex flex-col items-end">
                    <div class="relative w-20 h-20 mb-1">
                        <!-- Redesigned professional ring -->
                        <svg class="w-20 h-20 -rotate-90 drop-shadow-md" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="34" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="4"/>
                            <circle cx="40" cy="40" r="34" fill="none" class="stroke-white" stroke-width="5"
                                stroke-linecap="round"
                                :stroke-dasharray="`${(healthScore / 100) * 213.6} 213.6`"
                                style="transition: stroke-dasharray 1.5s ease-out;" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-black leading-none text-white">{{ healthScore }}</span>
                        </div>
                    </div>
                    <p class="text-xs font-semibold text-white/90 uppercase tracking-wider">{{ healthScoreLabel }}</p>
                    <p class="text-[9px] text-white/60 mt-0.5 font-medium tracking-widest uppercase">Skor Kesehatan</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-3 gap-3 mb-5">
            <div class="card-medix p-3.5 text-center hover-lift animate-fade-in-up delay-75"
                :class="latestRecord?.systolic ? '' : 'opacity-60'">
                <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center mx-auto mb-1.5">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-800">{{ latestRecord?.systolic ? `${latestRecord.systolic}/${latestRecord.diastolic}` : '—' }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Tekanan</p>
            </div>
            <div class="card-medix p-3.5 text-center hover-lift animate-fade-in-up delay-100"
                :class="latestRecord?.heart_rate ? '' : 'opacity-60'">
                <div class="w-8 h-8 rounded-xl bg-secondary/20 flex items-center justify-center mx-auto mb-1.5">
                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-800">{{ latestRecord?.heart_rate ? `${latestRecord.heart_rate}` : '—' }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">BPM</p>
            </div>
            <div class="card-medix p-3.5 text-center hover-lift animate-fade-in-up delay-150">
                <div class="w-8 h-8 rounded-xl bg-[#EFDBDC] flex items-center justify-center mx-auto mb-1.5">
                    <svg class="w-4 h-4 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-800">{{ totalRecords }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Total Data</p>
            </div>
        </div>

        <!-- Chart + Latest Record -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
            <!-- Chart -->
            <div v-if="chartData.length > 1" class="card-medix p-5 animate-fade-in-left delay-150">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700 text-sm">Tren Tekanan Darah</h3>
                    <div class="flex items-center gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary inline-block"></span>Sistolik</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-secondary inline-block"></span>Diastolik</span>
                    </div>
                </div>
                <div class="h-44">
                    <Line :data="lineChartData" :options="chartOptions" />
                </div>
            </div>

            <!-- Latest record metrics -->
            <div v-if="latestRecord" class="card-medix p-5 animate-fade-in-right delay-150"
                :class="chartData.length <= 1 ? 'lg:col-span-2' : ''">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700 text-sm">Data Terkini</h3>
                    <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-1 rounded-full">{{ formatDate(latestRecord.recorded_at) }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <MetricCard v-if="latestRecord.systolic"
                        label="Tekanan Darah"
                        :value="`${latestRecord.systolic}/${latestRecord.diastolic} mmHg`"
                        :status="bpStatus" icon="heart" />
                    <MetricCard v-if="latestRecord.heart_rate"
                        label="Detak Jantung"
                        :value="`${latestRecord.heart_rate} bpm`"
                        :status="hrStatus" icon="pulse" />
                    <MetricCard v-if="latestRecord.blood_sugar"
                        label="Gula Darah"
                        :value="`${latestRecord.blood_sugar} mg/dL`"
                        status="info" icon="drop" />
                    <MetricCard v-if="bmi"
                        label="BMI"
                        :value="`${bmi} — ${bmiLabel}`"
                        :status="bmiStatus" icon="weight" />
                    <MetricCard v-if="latestRecord.temperature"
                        label="Suhu Tubuh"
                        :value="`${latestRecord.temperature}°C`"
                        status="info" icon="temp" />
                    <MetricCard v-if="latestRecord.oxygen_saturation"
                        label="SpO2"
                        :value="`${latestRecord.oxygen_saturation}%`"
                        :status="latestRecord.oxygen_saturation >= 95 ? 'good' : 'warn'" icon="lungs" />
                </div>
            </div>

            <!-- No data -->
            <div v-if="!latestRecord" class="card-medix p-8 text-center animate-scale-in delay-150 lg:col-span-2">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <h3 class="font-semibold text-gray-700 mb-1">Belum Ada Data</h3>
                <p class="text-sm text-gray-500 mb-4">Mulailah memasukkan data kesehatan pertama Anda</p>
                <Link href="/input-data" class="inline-block bg-primary text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-primary-dark transition">
                    Input Data Sekarang
                </Link>
            </div>
        </div>

        <!-- Bottom: AI + Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
            <!-- AI Prompt Card -->
            <div class="bg-gradient-to-br from-primary to-primary-dark rounded-2xl p-5 text-white hover-lift animate-fade-in-up delay-250 shadow-lg shadow-primary/20">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-base">Analisis AI</p>
                        <p class="text-white/70 text-xs mt-0.5 mb-3">Dapatkan interpretasi mendalam kondisi kesehatanmu dari AI</p>
                        <Link href="/ai-analysis"
                            class="inline-flex items-center gap-1.5 bg-white text-primary font-semibold text-xs px-4 py-2 rounded-xl hover:bg-gray-50 focus:bg-gray-50 active:bg-gray-100 transition shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Mulai Analisis
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Latest Analysis preview OR quick navigation -->
            <div v-if="latestAnalysis" class="card-medix p-5 hover-lift animate-fade-in-up delay-300 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-700 text-sm">Analisis Terakhir</h3>
                        <span class="text-[10px] text-gray-400">{{ formatDate(latestAnalysis.created_at) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-2 mb-4">{{ truncateAnalysis(latestAnalysis.result) }}</p>
                </div>
                <div class="flex items-center justify-between border-t border-gray-50 pt-3 mt-auto">
                    <Link href="/ai-analysis" class="text-primary text-xs font-semibold inline-flex items-center gap-1 hover:gap-2 transition-all">
                        Lihat selengkapnya
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                    <Link href="/standar-normal" class="text-xs font-medium bg-[#FDD3CF]/50 text-[#B92521] px-3 py-1.5 rounded-lg hover:bg-[#FDD3CF] transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Kalkulator BMI
                    </Link>
                </div>
            </div>
            <div v-else class="card-medix p-5 animate-fade-in-up delay-300">
                <h3 class="font-semibold text-gray-700 text-sm mb-3">Aksi Cepat</h3>
                <div class="space-y-2">
                    <Link href="/input-data" class="flex items-center gap-3 p-2.5 rounded-xl bg-red-50 hover:bg-red-100 transition">
                        <div class="w-8 h-8 bg-primary rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Input Data Baru</span>
                    </Link>
                    <Link href="/history" class="flex items-center gap-3 p-2.5 rounded-xl bg-[#EFDBDC] hover:bg-[#e4cdce] transition">
                        <div class="w-8 h-8 bg-[#B74443] rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Lihat Riwayat</span>
                    </Link>
                    <Link href="/standar-normal" class="flex items-center gap-3 p-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 transition">
                        <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Cek Standar Normal</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Health Tips -->
        <div v-if="healthTip" class="card-medix p-4 border-l-4 animate-fade-in-up delay-400"
            :class="healthTip.border">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                    :class="healthTip.iconBg">
                    <svg v-if="healthTip.type === 'danger'" class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <svg v-else-if="healthTip.type === 'warning'" class="w-4 h-4 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <svg v-else-if="healthTip.type === 'sugar'" class="w-4 h-4 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    <svg v-else-if="healthTip.type === 'heart'" class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <svg v-else-if="healthTip.type === 'good'" class="w-4 h-4 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg v-else class="w-4 h-4 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">{{ healthTip.title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ healthTip.message }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, h } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Filler);

const props = defineProps({
    latestRecord: Object,
    recentRecords: Array,
    totalRecords: Number,
    latestAnalysis: Object,
    healthScore: Number,
    chartData: Array,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
});

const currentDate = computed(() =>
    new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
);

const healthScoreLabel = computed(() => {
    const s = props.healthScore;
    if (s >= 85) return 'Sangat Baik';
    if (s >= 70) return 'Baik';
    if (s >= 50) return 'Perlu Perhatian';
    if (s === 0) return 'Belum Ada Data';
    return 'Perlu Periksa';
});

const bmi = computed(() => {
    if (props.latestRecord?.weight && props.latestRecord?.height) {
        const h = props.latestRecord.height / 100;
        return (props.latestRecord.weight / (h * h)).toFixed(1);
    }
    return null;
});

const bmiLabel = computed(() => {
    const b = parseFloat(bmi.value);
    if (!b) return '';
    if (b < 18.5) return 'Kurus';
    if (b < 23) return 'Normal';
    if (b < 25) return 'Gemuk';
    return 'Obesitas';
});

const bmiStatus = computed(() => {
    const b = parseFloat(bmi.value);
    if (!b) return 'info';
    if (b >= 18.5 && b < 23) return 'good';
    if (b >= 23 && b < 25) return 'warn';
    return 'danger';
});

const bpStatus = computed(() => {
    const s = props.latestRecord?.systolic, d = props.latestRecord?.diastolic;
    if (!s) return 'info';
    if (s >= 140 || d >= 90) return 'danger';
    if (s >= 130 || d >= 80) return 'warn';
    if (s < 90 || d < 60) return 'info';
    return 'good';
});

const hrStatus = computed(() => {
    const hr = props.latestRecord?.heart_rate;
    if (!hr) return 'info';
    if (hr < 60 || hr > 100) return 'warn';
    return 'good';
});

const healthTip = computed(() => {
    const s = props.latestRecord?.systolic;
    const hr = props.latestRecord?.heart_rate;
    const bs = props.latestRecord?.blood_sugar;
    if (s && s >= 140) return { type: 'danger', iconBg: 'bg-primary/10',      title: 'Tekanan Darah Tinggi',           message: 'Tekanan darah Anda melebihi 140 mmHg. Segera konsultasikan dengan dokter dan kurangi konsumsi garam.', border: 'border-primary' };
    if (s && s >= 130) return { type: 'warning', iconBg: 'bg-secondary/20',     title: 'Tekanan Darah Perlu Dipantau',   message: 'Tekanan darah Anda di tahap prehipertensi. Olahraga ringan dan diet rendah garam bisa membantu.', border: 'border-secondary' };
    if (bs && bs > 200) return { type: 'sugar',  iconBg: 'bg-secondary/20',     title: 'Gula Darah Tinggi',              message: 'Gula darah di atas 200 mg/dL dapat mengindikasikan diabetes. Segera periksa ke dokter.', border: 'border-[#B74443]' };
    if (hr && (hr < 60 || hr > 100)) return { type: 'heart', iconBg: 'bg-secondary/20', title: 'Detak Jantung Tidak Normal', message: `Detak jantung ${hr} bpm berada di luar rentang normal (60–100 bpm). Istirahat yang cukup dan pantau terus.`, border: 'border-secondary' };
    if (props.healthScore >= 80) return { type: 'good',  iconBg: 'bg-[#FDD3CF]',   title: 'Kondisi Kesehatan Baik',         message: 'Pertahankan gaya hidup sehat Anda! Terus pantau secara rutin untuk menjaga kondisi tetap optimal.', border: 'border-[#F18E8C]' };
    if (props.totalRecords === 0) return { type: 'info',  iconBg: 'bg-[#EFDBDC]',   title: 'Mulai Pantau Kesehatanmu',       message: 'Belum ada data tercatat. Masukkan data kesehatan pertamamu untuk mendapatkan analisis.', border: 'border-[#B74443]' };
    return null;
});

const lineChartData = computed(() => ({
    labels: props.chartData.map(d => d.date),
    datasets: [
        { label: 'Sistolik', data: props.chartData.map(d => d.systolic), borderColor: '#F0404B', backgroundColor: 'rgba(240,64,75,0.08)', fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#F0404B' },
        { label: 'Diastolik', data: props.chartData.map(d => d.diastolic), borderColor: '#F18E8C', backgroundColor: 'transparent', fill: false, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#F18E8C' },
    ],
}));

const chartOptions = {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 0 } },
        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 9 }, precision: 0 } },
    },
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const truncateAnalysis = (text) => {
    if (!text) return '';
    return text.replace(/[#*_]/g, '').substring(0, 180) + '...';
};

// MetricCard render component
const statusStyles = {
    good:   { bg: 'bg-[#FDD3CF]',  dot: 'bg-[#B74443]',    text: 'text-[#B92521]' },
    warn:   { bg: 'bg-secondary/15', dot: 'bg-secondary',    text: 'text-[#B92521]' },
    danger: { bg: 'bg-primary/10',   dot: 'bg-primary',      text: 'text-primary-dark' },
    info:   { bg: 'bg-[#EFDBDC]',    dot: 'bg-[#B74443]',    text: 'text-[#B74443]' },
};

const MetricCard = {
    props: ['label', 'value', 'status'],
    setup(props) {
        return () => {
            const s = statusStyles[props.status] || statusStyles.info;
            return h('div', { class: `${s.bg} rounded-xl p-3` }, [
                h('div', { class: 'flex items-center gap-1.5 mb-1' }, [
                    h('span', { class: `w-1.5 h-1.5 rounded-full ${s.dot} flex-shrink-0` }),
                    h('p', { class: `text-[10px] font-medium ${s.text} truncate` }, props.label),
                ]),
                h('p', { class: 'text-sm font-bold text-gray-800 leading-tight' }, props.value),
            ]);
        };
    }
};
</script>
