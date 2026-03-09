<template>
    <AppLayout>
        <Head title="Input Data" />

        <div class="mb-6 animate-fade-in-down">
            <h1 class="text-2xl font-bold text-gray-800">Input Data Kesehatan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Catat kondisi kesehatan Anda hari ini</p>
        </div>

        <form @submit.prevent="submit">
            <!-- Desktop: 2 column, Mobile: 1 column -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                <!-- Tekanan Darah -->
                <div class="card-medix p-5 hover-lift animate-fade-in-up delay-75">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-700">Tekanan Darah</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Sistolik (mmHg)</label>
                            <input v-model="form.systolic" type="number" placeholder="120"
                                class="input-field" :class="{ 'border-red-400': form.errors.systolic }" />
                            <p v-if="form.errors.systolic" class="text-red-500 text-xs mt-1">{{ form.errors.systolic }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Diastolik (mmHg)</label>
                            <input v-model="form.diastolic" type="number" placeholder="80"
                                class="input-field" :class="{ 'border-red-400': form.errors.diastolic }" />
                        </div>
                    </div>
                    <transition name="badge">
                        <div v-if="bpStatus" class="mt-3 text-xs px-3 py-1.5 rounded-lg inline-block font-medium transition-all" :class="bpStatusClass">
                            {{ bpStatus }}
                        </div>
                    </transition>
                </div>

                <!-- Detak Jantung -->
                <div class="card-medix p-5 hover-lift animate-fade-in-up delay-100">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-secondary/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-700">Detak Jantung</h3>
                    </div>
                    <label class="text-xs text-gray-500 mb-1 block">BPM (denyut per menit)</label>
                    <input v-model="form.heart_rate" type="number" placeholder="72" class="input-field" />
                </div>

                <!-- Berat & Tinggi -->
                <div class="card-medix p-5 hover-lift animate-fade-in-up delay-150">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-[#EFDBDC] flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#B74443]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-700">Berat & Tinggi Badan</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Berat (kg)</label>
                            <input v-model="form.weight" type="number" step="0.1" placeholder="70" class="input-field" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Tinggi (cm)</label>
                            <input v-model="form.height" type="number" step="0.1" placeholder="170" class="input-field" />
                        </div>
                    </div>
                    <transition name="badge">
                        <div v-if="bmi" class="mt-3 text-xs px-3 py-1.5 rounded-lg inline-block font-medium" :class="bmiStatusClass">
                            BMI: {{ bmi }} - {{ bmiStatus }}
                        </div>
                    </transition>
                </div>

                <!-- Gula Darah -->
                <div class="card-medix p-5 hover-lift animate-fade-in-up delay-200">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-[#FDD3CF] flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#B92521]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-700">Gula Darah</h3>
                    </div>
                    <label class="text-xs text-gray-500 mb-1 block">mg/dL</label>
                    <input v-model="form.blood_sugar" type="number" step="0.1" placeholder="90" class="input-field" />
                </div>

                <!-- Suhu & SpO2 -->
                <div class="card-medix p-5 hover-lift animate-fade-in-up delay-250">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-700">Suhu & Saturasi Oksigen</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Suhu Tubuh (°C)</label>
                            <input v-model="form.temperature" type="number" step="0.1" placeholder="36.5" class="input-field" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">SpO2 (%)</label>
                            <input v-model="form.oxygen_saturation" type="number" placeholder="98" class="input-field" />
                        </div>
                    </div>
                </div>

                <!-- Waktu & Catatan -->
                <div class="card-medix p-5 hover-lift animate-fade-in-up delay-300">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-700">Waktu & Catatan</h3>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Waktu Pengukuran</label>
                            <input v-model="form.recorded_at" type="datetime-local" class="input-field" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Catatan (opsional)</label>
                            <textarea v-model="form.notes" rows="3" placeholder="Kondisi saat pengukuran, gejala, dll..."
                                class="input-field resize-none"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Submit Button -->
            <div class="mt-5 animate-fade-in-up delay-400">
                <button type="submit" :disabled="form.processing"
                    class="w-full lg:w-auto lg:px-12 bg-primary hover:bg-primary-dark text-white font-semibold py-3.5 rounded-xl transition-all duration-200 disabled:opacity-70 shadow-md shadow-primary/30 flex items-center justify-center gap-2">
                    <span v-if="form.processing">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span v-else class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Data
                    </span>
                </button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const now = new Date();
const localDatetime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);

const form = useForm({
    systolic: '', diastolic: '', heart_rate: '', blood_sugar: '',
    weight: '', height: '', temperature: '', oxygen_saturation: '',
    notes: '', recorded_at: localDatetime,
});

const submit = () => form.post('/input-data');

const bpStatus = computed(() => {
    if (!form.systolic || !form.diastolic) return null;
    const s = Number(form.systolic), d = Number(form.diastolic);
    if (s < 120 && d < 80) return 'Normal';
    if (s < 130 && d < 80) return 'Elevated';
    if (s < 140 || d < 90) return 'High Stage 1';
    return 'High Stage 2';
});

const bpStatusClass = computed(() => {
    if (!bpStatus.value) return '';
    if (bpStatus.value === 'Normal') return 'bg-[#FDD3CF] text-[#B92521]';
    if (bpStatus.value === 'Elevated') return 'bg-secondary/20 text-[#B92521]';
    return 'bg-primary/10 text-primary-dark';
});

const bmi = computed(() => {
    if (form.weight && form.height) {
        const h = Number(form.height) / 100;
        return (Number(form.weight) / (h * h)).toFixed(1);
    }
    return null;
});

const bmiStatus = computed(() => {
    if (!bmi.value) return null;
    const b = Number(bmi.value);
    if (b < 18.5) return 'Underweight';
    if (b < 25) return 'Normal';
    if (b < 30) return 'Overweight';
    return 'Obese';
});

const bmiStatusClass = computed(() => {
    if (!bmiStatus.value) return '';
    if (bmiStatus.value === 'Normal') return 'bg-[#FDD3CF] text-[#B92521]';
    if (bmiStatus.value === 'Overweight') return 'bg-secondary/20 text-[#B92521]';
    return 'bg-primary/10 text-primary-dark';
});
</script>

<style scoped>
.input-field {
    width: 100%; padding: 0.75rem 1rem; border: 1px solid #e5e7eb;
    border-radius: 0.75rem; font-size: 0.875rem; outline: none;
    transition: all 0.2s; font-family: 'Poppins', sans-serif; background: white;
}
.input-field:focus {
    border-color: #F0404B;
    box-shadow: 0 0 0 3px rgba(240, 64, 75, 0.1);
}
.badge-enter-active { animation: fadeInUp 0.3s ease both; }
.badge-leave-active { animation: fadeInUp 0.2s ease reverse both; }
</style>
