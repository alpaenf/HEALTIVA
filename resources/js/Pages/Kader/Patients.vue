<template>
    <KaderLayout>
        <Head title="Data Pasien" />

        <div class="flex flex-col gap-3 mb-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Data Pasien</h1>
                    <p class="text-sm text-gray-500">{{ patients.total }} pasien terdaftar</p>
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <a href="/kader/pasien/export-all"
                        class="flex flex-1 md:flex-none items-center justify-center gap-1.5 bg-green-500 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-green-600 transition shadow-sm shadow-green-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Excel
                    </a>
                    <button @click="showAddModal = true"
                        class="flex flex-1 md:flex-none items-center justify-center gap-1.5 bg-primary text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-primary-dark transition shadow-sm shadow-primary/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Pasien
                    </button>
                </div>
            </div>
        </div>

        <!-- Flash -->
        <div v-if="$page.props.flash?.success"
            class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm mb-4">
            {{ $page.props.flash.success }}
        </div>

        <!-- Search -->
        <div class="relative mb-4">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input v-model="search" @input="doSearch" type="text" placeholder="Cari nama atau NIK pasien..."
                class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30" />
        </div>

        <!-- Table / Card List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Mobile card list -->
            <div class="divide-y divide-gray-50 sm:hidden">
                <div v-if="patients.data.length === 0" class="px-4 py-8 text-center text-gray-400 text-sm">Tidak ada data pasien</div>
                <div v-for="patient in patients.data" :key="patient.id"
                    class="px-4 py-3.5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ patient.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 text-sm truncate">{{ patient.name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ patient.nik }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-gray-400">{{ patient.gender === 'male' ? 'Laki-laki' : patient.gender === 'female' ? 'Perempuan' : '-' }}</span>
                            <span class="text-xs bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded-md font-medium">{{ patient.health_records_count }} data</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5 flex-shrink-0">
                        <button @click="openEditModal(patient)"
                            class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-lg hover:bg-amber-200 transition text-center">Edit</button>
                        <Link :href="`/kader/pasien/${patient.id}/input`"
                            class="text-xs bg-primary text-white px-2 py-1 rounded-lg hover:bg-primary-dark transition text-center">+ Input</Link>
                        <Link :href="`/kader/pasien/${patient.id}`"
                            class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg hover:bg-gray-200 transition text-center">Detail</Link>
                    </div>
                </div>
            </div>

            <!-- Desktop table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pasien</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIK</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-if="patients.data.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">Tidak ada data pasien</td>
                        </tr>
                        <tr v-for="patient in patients.data" :key="patient.id"
                            class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-semibold text-xs flex-shrink-0">
                                        {{ patient.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ patient.name }}</p>
                                        <p class="text-gray-400 text-xs">{{ patient.gender === 'male' ? 'Laki-laki' : patient.gender === 'female' ? 'Perempuan' : '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ patient.nik }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-lg font-medium">
                                    {{ patient.health_records_count }} data
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button @click="openEditModal(patient)"
                                        class="text-xs bg-amber-100 text-amber-700 px-2.5 py-1 rounded-lg hover:bg-amber-200 transition">
                                        Edit
                                    </button>
                                    <Link :href="`/kader/pasien/${patient.id}/input`"
                                        class="text-xs bg-primary text-white px-2.5 py-1 rounded-lg hover:bg-primary-dark transition">
                                        + Input
                                    </Link>
                                    <Link :href="`/kader/pasien/${patient.id}`"
                                        class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg hover:bg-gray-200 transition">
                                        Detail
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="patients.last_page > 1" class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-500">
                <span>{{ patients.from }}–{{ patients.to }} dari {{ patients.total }}</span>
                <div class="flex gap-1">
                    <Link v-if="patients.prev_page_url" :href="patients.prev_page_url"
                        class="px-3 py-1 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs">← Prev</Link>
                    <Link v-if="patients.next_page_url" :href="patients.next_page_url"
                        class="px-3 py-1 rounded-lg border border-gray-200 hover:bg-gray-50 text-xs">Next →</Link>
                </div>
            </div>
        </div>

        <!-- Add Patient Modal -->
        <Transition name="modal">
            <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showAddModal = false"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-4 sm:p-6 lg:p-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Pasien Baru</h3>

                    <form @submit.prevent="submitAdd" class="space-y-4">
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">NIK (16 digit) <span class="text-red-500">*</span></label>
                            <input v-model="addForm.nik" type="text" maxlength="16" inputmode="numeric"
                                placeholder="3201xxxxxxxxxxxxxxx"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 tracking-widest"
                                :class="{ 'border-red-400': addForm.errors.nik }" />
                            <p v-if="addForm.errors.nik" class="text-red-500 text-xs mt-1">{{ addForm.errors.nik }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input v-model="addForm.name" type="text"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                                :class="{ 'border-red-400': addForm.errors.name }" />
                            <p v-if="addForm.errors.name" class="text-red-500 text-xs mt-1">{{ addForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input v-model="addForm.date_of_birth" type="date"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-medium text-gray-700 mb-1 block">Jenis Kelamin</label>
                                <select v-model="addForm.gender"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    <option value="">Pilih...</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700 mb-1 block">No. HP</label>
                                <input v-model="addForm.phone" type="tel"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30" />
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Alamat</label>
                            <textarea v-model="addForm.address" rows="2"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 resize-none"></textarea>
                        </div>
                        <div class="flex flex-col-reverse md:flex-row gap-3 pt-2">
                            <button type="button" @click="showAddModal = false"
                                class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition w-full">
                                Batal
                            </button>
                            <button type="submit" :disabled="addForm.processing"
                                class="flex-1 bg-primary text-white py-2.5 rounded-xl text-sm font-medium hover:bg-primary-dark transition disabled:opacity-50 w-full">
                                <span v-if="addForm.processing">Menyimpan...</span>
                                <span v-else>Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Edit Patient Modal -->
        <Transition name="modal">
            <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-4 sm:p-6 lg:p-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Data Pasien</h3>

                    <form @submit.prevent="submitEdit" class="space-y-4">
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">NIK</label>
                            <input :value="editForm.nik" type="text" disabled
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 tracking-widest" />
                            <p class="text-[11px] text-gray-400 mt-1">NIK tidak dapat diubah.</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input v-model="editForm.name" type="text"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                                :class="{ 'border-red-400': editForm.errors.name }" />
                            <p v-if="editForm.errors.name" class="text-red-500 text-xs mt-1">{{ editForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input v-model="editForm.date_of_birth" type="date"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
                                :class="{ 'border-red-400': editForm.errors.date_of_birth }" />
                            <p v-if="editForm.errors.date_of_birth" class="text-red-500 text-xs mt-1">{{ editForm.errors.date_of_birth }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-medium text-gray-700 mb-1 block">Jenis Kelamin</label>
                                <select v-model="editForm.gender"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    <option value="">Pilih...</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700 mb-1 block">No. HP</label>
                                <input v-model="editForm.phone" type="tel"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30" />
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Alamat</label>
                            <textarea v-model="editForm.address" rows="2"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 resize-none"></textarea>
                        </div>
                        <div class="flex flex-col-reverse md:flex-row gap-3 pt-2">
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition w-full">
                                Batal
                            </button>
                            <button type="submit" :disabled="editForm.processing"
                                class="flex-1 bg-primary text-white py-2.5 rounded-xl text-sm font-medium hover:bg-primary-dark transition disabled:opacity-50 w-full">
                                <span v-if="editForm.processing">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </KaderLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import KaderLayout from '@/Layouts/KaderLayout.vue';

const props = defineProps({
    patients: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingPatientId = ref(null);

let searchTimeout = null;
const doSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/kader/pasien', { search: search.value }, { preserveState: true, replace: true });
    }, 400);
};

const addForm = useForm({
    nik: '',
    name: '',
    date_of_birth: '',
    gender: '',
    phone: '',
    address: '',
});

const editForm = useForm({
    nik: '',
    name: '',
    date_of_birth: '',
    gender: '',
    phone: '',
    address: '',
});

const normalizeDateInput = (dateValue) => {
    if (!dateValue) return '';
    return String(dateValue).slice(0, 10);
};

const submitAdd = () => {
    addForm.post('/kader/pasien', {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        },
    });
};

const openEditModal = (patient) => {
    editingPatientId.value = patient.id;
    editForm.reset();
    editForm.clearErrors();
    editForm.nik = patient.nik || '';
    editForm.name = patient.name || '';
    editForm.date_of_birth = normalizeDateInput(patient.date_of_birth);
    editForm.gender = patient.gender || '';
    editForm.phone = patient.phone || '';
    editForm.address = patient.address || '';
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!editingPatientId.value) return;

    editForm.put(`/kader/pasien/${editingPatientId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
        },
    });
};
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
