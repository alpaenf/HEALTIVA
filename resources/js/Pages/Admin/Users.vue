<template>
    <AdminLayout>
        <Head title="Kelola Pengguna" />

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 animate-fade-in-down">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Pengguna</h1>
                <p class="text-sm text-gray-500">{{ users.total }} pengguna terdaftar</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Search -->
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                    <input
                        v-model="search"
                        @input="doSearch"
                        type="text"
                        placeholder="Cari nama / email..."
                        class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30 w-52"
                    />
                </div>
                <!-- Export CSV -->
                <a :href="exportUrl"
                    class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-green-500 hover:bg-green-600 text-white rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>

        <!-- Flash -->
        <div v-if="$page.props.flash?.success"
            class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm mb-4">
            {{ $page.props.flash.success }}
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up delay-75">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pengguna</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Analisis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bergabung</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="user in users.data" :key="user.id"
                            class="hover:bg-gray-50/70 transition-colors cursor-pointer"
                            @click="goToDetail(user.id)">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0">
                                        <img v-if="user.avatar"
                                            :src="user.avatar.startsWith('http') ? user.avatar : `/storage/${user.avatar}`"
                                            :alt="user.name"
                                            class="w-full h-full object-cover" />
                                        <div v-else
                                            class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-semibold text-xs">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ user.name }}</p>
                                        <p class="text-gray-400 text-xs">{{ user.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-lg font-medium">
                                    {{ user.health_records_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-xs bg-violet-50 text-violet-700 px-2 py-1 rounded-lg font-medium">
                                    {{ user.ai_analyses_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-500">{{ formatDate(user.created_at) }}</span>
                            </td>
                            <td class="px-4 py-3" @click.stop>
                                <button @click="confirmDelete(user.id, user.name)"
                                    class="text-xs text-red-500 hover:text-red-700 px-2 py-1 border border-red-100 hover:border-red-200 rounded-lg transition">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!users.data.length">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada pengguna</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    Menampilkan {{ users.from }}-{{ users.to }} dari {{ users.total }} pengguna
                </p>
                <div class="flex gap-2">
                    <Link v-if="users.prev_page_url" :href="users.prev_page_url"
                        class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        â† Prev
                    </Link>
                    <Link v-if="users.next_page_url" :href="users.next_page_url"
                        class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Next â†’
                    </Link>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
            <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl">
                <h3 class="font-bold text-gray-800 mb-1">Hapus Pengguna?</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Anda akan menghapus <strong>{{ deleteUserName }}</strong> beserta seluruh data kesehatannya. Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false"
                        class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>
                    <button @click="deleteUser"
                        class="flex-1 py-2.5 bg-red-500 rounded-xl text-sm font-medium text-white hover:bg-red-600">
                        Hapus
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

const props = defineProps({ users: Object, filters: Object });

// Search
const search = ref(props.filters?.search ?? '');
let searchTimer = null;
const doSearch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get('/admin/users', { search: search.value }, { preserveState: true, replace: true });
    }, 350);
};

// Export URL â€” include current search if any
const exportUrl = computed(() => {
    const q = search.value ? `?search=${encodeURIComponent(search.value)}` : '';
    return `/admin/users/export${q}`;
});

const showDeleteModal = ref(false);
const deleteId = ref(null);
const deleteUserName = ref('');

const goToDetail = (id) => router.visit(`/admin/users/${id}`);

const confirmDelete = (id, name) => {
    deleteId.value = id;
    deleteUserName.value = name;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    router.delete(`/admin/users/${deleteId.value}`, {
        onFinish: () => { showDeleteModal.value = false; }
    });
};

const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
</script>
