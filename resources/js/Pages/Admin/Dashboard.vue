<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
            <p class="text-sm text-gray-500">Ringkasan sistem HEALTIVA</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatCard :value="stats.totalUsers" label="Total Pengguna" color="blue" class="animate-fade-in-up delay-75" />
            <StatCard :value="stats.totalRecords" label="Total Data" color="green" class="animate-fade-in-up delay-150" />
            <StatCard :value="stats.totalAnalyses" label="Analisis AI" color="purple" class="animate-fade-in-up delay-200" />
            <StatCard :value="stats.newUsersThisMonth" label="User Baru (Bulan Ini)" color="orange" class="animate-fade-in-up delay-300" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            <!-- Monthly Chart (wider) -->
            <div class="lg:col-span-3 bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover-lift animate-fade-in-left delay-100">
                <h3 class="font-semibold text-gray-700 mb-4">Data Kesehatan per Bulan ({{ currentYear }})</h3>
                <div class="h-56">
                    <Bar :data="barChartData" :options="barChartOptions" />
                </div>
            </div>

            <!-- Recent Users -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover-lift animate-fade-in-right delay-150">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700">Pengguna Terbaru</h3>
                    <Link href="/admin/users" class="text-xs text-primary hover:underline">Lihat semua</Link>
                </div>
                <div class="space-y-2">
                    <div v-for="(user, i) in recentUsers" :key="user.id"
                        class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl hover:bg-gray-100 transition"
                        :style="`animation-delay:${i*60}ms`">
                        <div class="w-8 h-8 rounded-xl overflow-hidden flex-shrink-0">
                            <img v-if="user.avatar"
                                :src="user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`"
                                :alt="user.name"
                                class="w-full h-full object-cover" />
                            <div v-else
                                class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold text-xs">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ user.name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ user.email }}</p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">{{ formatDate(user.created_at) }}</span>
                    </div>
                    <div v-if="!recentUsers.length" class="text-center py-6 text-sm text-gray-400">
                        Belum ada pengguna
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
    recentUsers: Array,
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
            blue: 'from-blue-500 to-blue-600',
            green: 'from-green-500 to-green-600',
            purple: 'from-violet-500 to-violet-600',
            orange: 'from-orange-400 to-orange-500',
        };
        return () => h('div', { class: 'bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover-lift' }, [
            h('div', { class: `w-3 h-3 bg-gradient-to-br ${colorMap[props.color]} rounded-full mb-3` }),
            h('p', { class: 'text-2xl font-bold text-gray-800' }, props.value),
            h('p', { class: 'text-xs text-gray-500 mt-0.5' }, props.label),
        ]);
    }
};
</script>
