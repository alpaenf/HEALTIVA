<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
            <p class="text-sm text-gray-500">Ringkasan sistem HEALTIVA</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <StatCard :value="stats.totalPatients" label="Total Pasien" color="blue" class="animate-fade-in-up delay-75" />
            <StatCard :value="stats.totalKaders" label="Total Kader" color="green" class="animate-fade-in-up delay-150" />
            <StatCard :value="stats.totalRecords" label="Total Data" color="purple" class="animate-fade-in-up delay-200" />
            <StatCard :value="stats.newPatientsThisMonth" label="Pasien Baru (Bulan Ini)" color="orange" class="animate-fade-in-up delay-300" />
        </div>

        <!-- Risk Summary Card -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover-lift animate-fade-in-up delay-400 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 rounded-full bg-primary"></div>
                <h3 class="font-semibold text-gray-700 text-sm">Ringkasan Risiko Pasien</h3>
                <span class="ml-auto text-xs text-gray-400">dari {{ stats.analyzedPatients }} pasien tercatat</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Hipertensi -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-600">Hipertensi</span>
                        <span class="text-xs font-bold text-primary">{{ stats.countHighBp }}/{{ stats.analyzedPatients }} pasien</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary to-primary-dark transition-all duration-700"
                            :style="`width:${stats.pctHighBp}%`"></div>
                    </div>
                    <p class="text-[11px] text-gray-400">{{ stats.pctHighBp }}% dari total pasien</p>
                </div>
                <!-- Gula Tinggi -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-600">Gula Darah Tinggi</span>
                        <span class="text-xs font-bold text-primary">{{ stats.countHighSugar }}/{{ stats.analyzedPatients }} pasien</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary to-primary-dark transition-all duration-700"
                            :style="`width:${stats.pctHighSugar}%`"></div>
                    </div>
                    <p class="text-[11px] text-gray-400">{{ stats.pctHighSugar }}% dari total pasien</p>
                </div>
                <!-- Obesitas -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-600">Obesitas (BMI ≥25)</span>
                        <span class="text-xs font-bold text-primary">{{ stats.countObesity }}/{{ stats.analyzedPatients }} pasien</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary to-primary-dark transition-all duration-700"
                            :style="`width:${stats.pctObesity}%`"></div>
                    </div>
                    <p class="text-[11px] text-gray-400">{{ stats.pctObesity }}% dari total pasien</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <!-- Monthly Chart (wider) -->
            <div class="lg:col-span-3 bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover-lift animate-fade-in-left delay-100">
                <h3 class="font-semibold text-gray-700 mb-4">Data Kesehatan per Bulan ({{ currentYear }})</h3>
                <div class="h-56">
                    <Bar :data="barChartData" :options="barChartOptions" />
                </div>
            </div>

            <!-- Recent Patients -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover-lift animate-fade-in-right delay-150">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700">Pasien Terbaru</h3>
                    <Link href="/admin/patients" class="text-xs text-primary hover:underline">Lihat semua</Link>
                </div>
                <div class="space-y-2">
                    <div v-for="(patient, i) in recentPatients" :key="patient.id"
                        class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl hover:bg-gray-100 transition"
                        :style="`animation-delay:${i*60}ms`">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                            {{ patient.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ patient.name }}</p>
                            <p class="text-xs text-gray-400 font-mono truncate">{{ patient.nik }}</p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">{{ formatDate(patient.created_at) }}</span>
                    </div>
                    <div v-if="!recentPatients.length" class="text-center py-6 text-sm text-gray-400">
                        Belum ada pasien
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed, h } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Tooltip } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip);

const props = defineProps({
    stats: Object,
    recentPatients: Array,
    monthlyChart: Array,
});

const currentYear = new Date().getFullYear();

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

const barChartData = computed(() => ({
    labels: months,
    datasets: [{
        label: 'Data Kesehatan',
        data: props.monthlyChart.map(d => d.count),
        backgroundColor: 'rgba(240, 64, 75, 0.7)',
        borderColor: '#F0404B',
        borderWidth: 2,
        borderRadius: 8,
    }]
}));

const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 }, precision: 0 } },
    },
};

const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });

const StatCard = {
    props: ['value', 'label', 'color'],
    setup(props) {
        const colorMap = {
            blue:   'from-primary/70 to-primary',
            green:  'from-primary/50 to-primary/80',
            purple: 'from-primary to-primary-dark',
            orange: 'from-primary/60 to-primary/90',
            red:    'from-primary to-primary-dark',
        };
        return () => h('div', { class: 'bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover-lift' }, [
            h('div', { class: `w-3 h-3 bg-gradient-to-br ${colorMap[props.color] ?? colorMap.red} rounded-full mb-3` }),
            h('p', { class: 'text-2xl font-bold text-gray-800' }, props.value),
            h('p', { class: 'text-xs text-gray-500 mt-0.5' }, props.label),
        ]);
    }
};
</script>
