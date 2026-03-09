<template>
    <AppLayout>
        <Head title="Riwayat" />

        <div class="flex items-center justify-between mb-6 animate-fade-in-down">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Riwayat Kesehatan</h1>
                <p class="text-sm text-gray-500">{{ records.total }} data tersimpan</p>
            </div>
            <Link href="/input-data"
                class="bg-primary hover:bg-primary-dark text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-sm shadow-primary/20 flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input
            </Link>
        </div>

        <!-- Empty state -->
        <div v-if="!records.data.length" class="card-medix p-10 text-center animate-scale-in">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h3 class="font-semibold text-gray-700 mb-1">Belum Ada Riwayat</h3>
            <p class="text-sm text-gray-500 mb-4">Mulai catat data kesehatan Anda</p>
            <Link href="/input-data" class="inline-block bg-primary text-white text-sm px-5 py-2.5 rounded-xl font-medium hover:bg-primary-dark transition">
                Input Data Pertama
            </Link>
        </div>

        <!-- Records Grid -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-4">
            <div v-for="(record, index) in records.data" :key="record.id"
                class="card-medix p-4 hover-lift animate-fade-in-up"
                :style="`animation-delay:${index * 50}ms`">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ formatDate(record.recorded_at) }}</p>
                        <p class="text-xs text-gray-400">{{ formatTime(record.recorded_at) }}</p>
                    </div>
                    <button @click="confirmDelete(record.id)"
                        class="text-gray-300 hover:text-red-400 transition-colors p-1 rounded-lg hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <MetricBadge v-if="record.systolic"
                        :label="`${record.systolic}/${record.diastolic} mmHg`"
                        sub="Tekanan Darah"
                        :status="getBPStatus(record.systolic, record.diastolic)" />
                    <MetricBadge v-if="record.heart_rate"
                        :label="`${record.heart_rate} bpm`"
                        sub="Detak Jantung"
                        :status="getHRStatus(record.heart_rate)" />
                    <MetricBadge v-if="record.blood_sugar"
                        :label="`${record.blood_sugar} mg/dL`"
                        sub="Gula Darah" />
                    <MetricBadge v-if="record.weight"
                        :label="`${record.weight} kg`"
                        sub="Berat Badan" />
                    <MetricBadge v-if="record.temperature"
                        :label="`${record.temperature} °C`"
                        sub="Suhu Tubuh" />
                    <MetricBadge v-if="record.oxygen_saturation"
                        :label="`${record.oxygen_saturation}%`"
                        sub="SpO2" />
                </div>

                <div v-if="record.notes" class="mt-3 text-xs text-gray-500 bg-gray-50 rounded-lg p-2 italic">
                    {{ record.notes }}
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="records.last_page > 1" class="flex items-center justify-center gap-3 mt-6">
            <Link v-if="records.prev_page_url" :href="records.prev_page_url"
                class="px-4 py-2 text-sm font-medium text-primary bg-white rounded-xl border border-primary/20 hover:bg-primary/5 transition">
                â† Sebelumnya
            </Link>
            <span class="text-sm text-gray-500">{{ records.current_page }} / {{ records.last_page }}</span>
            <Link v-if="records.next_page_url" :href="records.next_page_url"
                class="px-4 py-2 text-sm font-medium text-primary bg-white rounded-xl border border-primary/20 hover:bg-primary/5 transition">
                Berikutnya â†’
            </Link>
        </div>

        <!-- Delete Confirmation Modal -->
        <transition name="modal">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4" @click.self="showDeleteModal = false">
                <div class="card-medix p-6 w-full max-w-sm animate-scale-in">
                    <h3 class="font-bold text-gray-800 mb-2">Hapus Data?</h3>
                    <p class="text-sm text-gray-500 mb-5">Data yang dihapus tidak dapat dikembalikan.</p>
                    <div class="flex gap-3">
                        <button @click="showDeleteModal = false"
                            class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button @click="deleteRecord"
                            class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 rounded-xl text-sm font-medium text-white transition">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </AppLayout>
</template>

<script setup>
import { ref, h } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ records: Object });

const showDeleteModal = ref(false);
const deleteId = ref(null);

const confirmDelete = (id) => { deleteId.value = id; showDeleteModal.value = true; };
const deleteRecord = () => {
    router.delete(`/history/${deleteId.value}`, { onFinish: () => { showDeleteModal.value = false; } });
};

const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric' });
const formatTime = (d) => new Date(d).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

const getBPStatus = (s, d) => {
    if (s < 120 && d < 80) return 'normal';
    if (s < 130) return 'warning';
    return 'danger';
};
const getHRStatus = (hr) => (hr >= 60 && hr <= 100) ? 'normal' : 'warning';

const MetricBadge = {
    props: ['label', 'sub', 'status'],
    setup(props) {
        const statusClass = {
            normal: 'bg-[#FDD3CF] border-[#F18E8C]/40',
            warning: 'bg-[#EFDBDC] border-[#B74443]/30',
            danger: 'bg-primary/10 border-primary/20',
        };
        return () => h('div', {
            class: `p-2.5 rounded-lg border ${statusClass[props.status] || 'bg-gray-50 border-gray-100'}`
        }, [
            h('p', { class: 'text-xs font-semibold text-gray-700' }, props.label),
            h('p', { class: 'text-[10px] text-gray-400 mt-0.5' }, props.sub),
        ]);
    }
};
</script>

<style scoped>
.modal-enter-active { animation: scaleIn 0.25s ease both; }
.modal-leave-active { animation: scaleIn 0.2s ease reverse both; }
</style>
