<template>
    <AdminLayout>
        <Head title="Knowledge Base AI" />

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 animate-fade-in-down">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Knowledge Base AI</h1>
                <p class="text-sm text-gray-500">{{ items.total }} pengetahuan tersimpan â€” AI belajar dari sini</p>
            </div>
            <button @click="openAdd"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition shadow-sm shadow-primary/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pengetahuan
            </button>
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
            <input v-model="search" @input="doSearch" type="text" placeholder="Cari topik, kategori, atau keyword..."
                class="pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 w-full sm:w-80"/>
        </div>

        <!-- List -->
        <div class="space-y-3">
            <div v-for="item in items.data" :key="item.id"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 animate-fade-in-up hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="text-sm font-semibold text-gray-800">{{ item.title }}</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide"
                                :class="categoryColor(item.category)">
                                {{ item.category }}
                            </span>
                            <span v-if="!item.is_active" class="text-[10px] bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full">Nonaktif</span>
                        </div>
                        <p class="text-xs text-gray-400 mb-2">
                            <span class="font-medium text-gray-500">Keywords:</span> {{ item.keywords }}
                        </p>
                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-3">{{ item.content }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button @click="openEdit(item)"
                            class="p-2 text-gray-400 hover:text-primary hover:bg-[#FFF5F5] rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="deleteItem(item.id)"
                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="!items.data.length" class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-[#FDD3CF] rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium">Belum ada pengetahuan</p>
                <p class="text-gray-400 text-xs mt-1">Tambahkan pengetahuan medis agar AI semakin pintar</p>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="items.last_page > 1" class="flex justify-center gap-2 mt-6">
            <Link v-if="items.prev_page_url" :href="items.prev_page_url"
                class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                Sebelumnya
            </Link>
            <Link v-if="items.next_page_url" :href="items.next_page_url"
                class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                Selanjutnya
            </Link>
        </div>

        <!-- Modal Add/Edit -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 px-4 pb-4 sm:pb-0">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl animate-fade-in-up">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">{{ isEditing ? 'Edit Pengetahuan' : 'Tambah Pengetahuan Baru' }}</h3>
                    <button @click="showModal = false" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Judul Topik</label>
                        <input v-model="form.title" type="text" placeholder="contoh: Hipertensi Tahap 1"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori</label>
                        <select v-model="form.category"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 bg-white">
                            <option v-for="c in categories" :key="c.value" :value="c.value">{{ c.label }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keywords <span class="text-gray-400 font-normal">(dipisah koma)</span></label>
                        <input v-model="form.keywords" type="text" placeholder="contoh: hipertensi,tensi,tekanan darah,sistolik"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30"/>
                        <p class="text-[10px] text-gray-400 mt-1">AI akan menggunakan pengetahuan ini saat mendeteksi kata kunci yang sesuai</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Isi Pengetahuan</label>
                        <textarea v-model="form.content" rows="6"
                            placeholder="Tuliskan pengetahuan medis secara lengkap dan akurat. AI akan mempelajari dan menggunakan informasi ini dalam responnya."
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 resize-none leading-relaxed"></textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="form.is_active = !form.is_active"
                            :class="form.is_active ? 'bg-primary' : 'bg-gray-200'"
                            class="relative w-10 h-5 rounded-full transition-colors duration-200 flex-shrink-0">
                            <span :class="form.is_active ? 'translate-x-5' : 'translate-x-0.5'"
                                class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block"></span>
                        </button>
                        <span class="text-xs text-gray-600 font-medium">{{ form.is_active ? 'Aktif (AI menggunakan ini)' : 'Nonaktif (diabaikan AI)' }}</span>
                    </div>
                </div>
                <div class="px-5 py-4 border-t border-gray-100 flex gap-3">
                    <button @click="showModal = false"
                        class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="submitForm"
                        class="flex-1 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary-dark transition">
                        {{ isEditing ? 'Simpan Perubahan' : 'Tambah & Ajarkan AI' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ items: Object, filters: Object });

const search = ref(props.filters?.search ?? '');
let searchTimer = null;
const doSearch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get('/admin/knowledge', { search: search.value }, { preserveState: true, replace: true });
    }, 350);
};

const categories = [
    { value: 'umum',       label: 'Umum' },
    { value: 'tensi',      label: 'Tekanan Darah' },
    { value: 'gula',       label: 'Gula Darah / Diabetes' },
    { value: 'jantung',    label: 'Jantung & Nadi' },
    { value: 'bmi',        label: 'BMI & Berat Badan' },
    { value: 'kolesterol', label: 'Kolesterol' },
    { value: 'pernapasan', label: 'Pernapasan & SpO2' },
    { value: 'ginjal',     label: 'Ginjal' },
    { value: 'obat',       label: 'Obat & Suplemen' },
    { value: 'gaya_hidup', label: 'Gaya Hidup Sehat' },
];

const categoryColor = (c) => ({
    umum:       'bg-gray-100 text-gray-500',
    tensi:      'bg-[#FDD3CF] text-primary',
    gula:       'bg-amber-50 text-amber-700',
    jantung:    'bg-red-50 text-red-600',
    bmi:        'bg-blue-50 text-blue-600',
    kolesterol: 'bg-purple-50 text-purple-600',
    pernapasan: 'bg-cyan-50 text-cyan-600',
    ginjal:     'bg-orange-50 text-orange-600',
    obat:       'bg-green-50 text-green-600',
    gaya_hidup: 'bg-teal-50 text-teal-600',
}[c] ?? 'bg-gray-100 text-gray-500');

const showModal = ref(false);
const isEditing = ref(false);
const editId = ref(null);

const emptyForm = { title: '', category: 'umum', keywords: '', content: '', is_active: true };
const form = ref({ ...emptyForm });

const openAdd = () => {
    isEditing.value = false;
    editId.value = null;
    form.value = { ...emptyForm };
    showModal.value = true;
};

const openEdit = (item) => {
    isEditing.value = true;
    editId.value = item.id;
    form.value = {
        title: item.title,
        category: item.category,
        keywords: item.keywords,
        content: item.content,
        is_active: item.is_active,
    };
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        router.put(`/admin/knowledge/${editId.value}`, form.value, {
            onSuccess: () => { showModal.value = false; }
        });
    } else {
        router.post('/admin/knowledge', form.value, {
            onSuccess: () => { showModal.value = false; }
        });
    }
};

const deleteItem = (id) => {
    if (!confirm('Hapus pengetahuan ini? AI tidak akan lagi menggunakannya.')) return;
    router.delete(`/admin/knowledge/${id}`);
};
</script>
