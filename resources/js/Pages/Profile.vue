<template>
    <AppLayout>
        <Head title="Profil" />

        <div class="mb-6 animate-fade-in-down">
            <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
            <p class="text-sm text-gray-500">Kelola informasi akun Anda</p>
        </div>

        <!-- Desktop: 2 column layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- LEFT: Profile summary + action buttons -->
            <div class="space-y-4">
                <!-- Profile Header -->
                <div class="card-medix p-5 text-center hover-lift animate-fade-in-left delay-75">
                    <!-- Avatar with upload button -->
                    <div class="relative w-20 h-20 mx-auto mb-3">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden shadow-lg">
                            <img v-if="user?.avatar" :src="avatarUrl" class="w-full h-full object-cover" alt="Avatar" />
                            <div v-else class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white text-3xl font-bold">
                                {{ user?.name?.charAt(0)?.toUpperCase() }}
                            </div>
                        </div>
                        <button @click="$refs.avatarInput.click()"
                            class="absolute -bottom-1.5 -right-1.5 w-7 h-7 bg-primary rounded-lg flex items-center justify-center shadow-md hover:bg-primary-dark transition">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                        <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="onFileChange" />
                    </div>
                    <h2 class="text-lg font-bold text-gray-800">{{ user?.name }}</h2>
                    <p class="text-sm text-gray-400">{{ user?.email }}</p>
                    <span class="inline-block mt-2 text-xs bg-primary/10 text-primary font-medium px-3 py-1 rounded-full">
                        {{ user?.role === 'admin' ? 'Administrator' : 'Pengguna' }}
                    </span>

                    <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-100">
                        <div class="text-center p-2 rounded-xl bg-primary/5">
                            <p class="text-xl font-bold text-primary">{{ totalRecords }}</p>
                            <p class="text-xs text-gray-500">Data Kesehatan</p>
                        </div>
                        <div class="text-center p-2 rounded-xl bg-[#EFDBDC]">
                            <p class="text-xl font-bold text-[#B74443]">{{ totalAnalyses }}</p>
                            <p class="text-xs text-gray-500">Analisis AI</p>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <div class="card-medix p-4 animate-fade-in-left delay-150">
                    <form @submit.prevent="logout">
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-2.5 text-red-500 font-semibold rounded-xl border border-red-200 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar dari Akun
                        </button>
                    </form>
                </div>

                <!-- Admin Link -->
                <div v-if="user?.role === 'admin'" class="card-medix p-4 border border-[#F18E8C] bg-[#FDD3CF] hover-lift animate-fade-in-left delay-200">
                    <Link href="/admin/dashboard" class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#B92521]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="font-medium text-[#B92521] text-sm">Panel Admin</span>
                        </div>
                        <svg class="w-4 h-4 text-[#B92521]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </Link>
                </div>
            </div>

            <!-- RIGHT: Forms -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Edit Profile Form -->
                <div class="card-medix p-5 animate-fade-in-right delay-100">
                    <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Profil
                    </h3>
                    <div v-if="profileSuccess" class="bg-[#FDD3CF] border border-[#F18E8C] text-[#B92521] rounded-xl px-4 py-3 text-sm mb-4 animate-fade-in-down">
                        Profil berhasil diperbarui
                    </div>
                    <form @submit.prevent="updateProfile" class="space-y-3">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Nama Lengkap</label>
                                <input v-model="profileForm.name" type="text" class="input-field" :class="{ 'border-red-400': profileForm.errors.name }" />
                                <p v-if="profileForm.errors.name" class="text-red-500 text-xs mt-1">{{ profileForm.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Email</label>
                                <input v-model="profileForm.email" type="email" class="input-field" :class="{ 'border-red-400': profileForm.errors.email }" />
                                <p v-if="profileForm.errors.email" class="text-red-500 text-xs mt-1">{{ profileForm.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Nomor HP</label>
                                <input v-model="profileForm.phone" type="tel" placeholder="081234567890" class="input-field" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Jenis Kelamin</label>
                                <select v-model="profileForm.gender" class="input-field">
                                    <option value="">Pilih</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tanggal Lahir</label>
                            <input v-model="profileForm.date_of_birth" type="date" class="input-field" />
                        </div>
                        <button type="submit" :disabled="profileForm.processing"
                            class="w-full lg:w-auto lg:px-8 bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-xl transition-all disabled:opacity-70">
                            {{ profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="card-medix p-5 animate-fade-in-right delay-200">
                    <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Ubah Password
                    </h3>
                    <div v-if="passwordSuccess" class="bg-[#FDD3CF] border border-[#F18E8C] text-[#B92521] rounded-xl px-4 py-3 text-sm mb-4 animate-fade-in-down">
                        Password berhasil diubah
                    </div>
                    <form @submit.prevent="updatePassword" class="space-y-3">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Password Saat Ini</label>
                                <input v-model="passwordForm.current_password" type="password" class="input-field" :class="{ 'border-red-400': passwordForm.errors.current_password }" />
                                <p v-if="passwordForm.errors.current_password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Password Baru</label>
                                <input v-model="passwordForm.password" type="password" class="input-field" :class="{ 'border-red-400': passwordForm.errors.password }" />
                                <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Konfirmasi Password</label>
                                <input v-model="passwordForm.password_confirmation" type="password" class="input-field" />
                            </div>
                        </div>
                        <button type="submit" :disabled="passwordForm.processing"
                            class="w-full lg:w-auto lg:px-8 bg-gray-800 hover:bg-gray-900 text-white font-semibold py-3 rounded-xl transition-all disabled:opacity-70">
                            {{ passwordForm.processing ? 'Mengubah...' : 'Ubah Password' }}
                        </button>
                    </form>
                </div>

                <p class="text-center text-xs text-gray-400 pb-2">HEALTIVA v1.0 - Sistem Monitor Kesehatan</p>
            </div>
        </div>
        <!-- Crop Modal -->
        <div v-if="showCropModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl mx-4 relative overflow-hidden">
                <h3 class="font-bold text-gray-800 text-lg mb-4">Sesuaikan Foto/Gambar</h3>
                <div class="relative w-full h-[300px] bg-gray-100 rounded-xl overflow-hidden mb-4">
                    <img ref="imageElement" :src="imageSrc" class="max-w-full block" alt="Picture">
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button @click="cancelCrop" class="px-5 py-2 rounded-xl text-gray-600 font-medium hover:bg-gray-100 transition-colors">Batal</button>
                    <button @click="cropAndUpload" :disabled="uploading" class="px-6 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary-dark transition-colors flex items-center gap-2">
                        <span v-if="uploading">Mengunggah...</span>
                        <span v-else>Terapkan & Simpan</span>
                    </button>
                </div>
            </div>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

const props = defineProps({
    user: Object,
    totalRecords: Number,
    totalAnalyses: Number,
});

const avatarUrl = computed(() => {
    if (!props.user?.avatar) return null;
    if (props.user.avatar.startsWith('http')) return props.user.avatar;
    return '/storage/' + props.user.avatar;
});

const showCropModal = ref(false);
const imageSrc = ref('');
const imageElement = ref(null);
const uploading = ref(false);
let cropper = null;
const avatarInput = ref(null);

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('File harus berupa gambar');
        return;
    }

    const reader = new FileReader();
    reader.onload = (event) => {
        imageSrc.value = event.target.result;
        showCropModal.value = true;
        
        nextTick(() => {
            if (cropper) cropper.destroy();
            cropper = new Cropper(imageElement.value, {
                aspectRatio: 1, // 1:1 untuk lingkaran/persegi
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        });
    };
    reader.readAsDataURL(file);
    // Reset file input agar bisa pilih file yang sama dua kali
    if (avatarInput.value) avatarInput.value.value = '';
};

const cancelCrop = () => {
    showCropModal.value = false;
    imageSrc.value = '';
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
};

const cropAndUpload = () => {
    if (!cropper) return;
    
    uploading.value = true;
    
    cropper.getCroppedCanvas({
        width: 300,
        height: 300,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    }).toBlob((blob) => {
        if (!blob) {
            uploading.value = false;
            return;
        }

        const form = new FormData();
        form.append('avatar', blob, 'avatar.png');

        router.post('/profile/avatar', form, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                cancelCrop();
            },
            onFinish: () => {
                uploading.value = false;
            }
        });
    }, 'image/png');
};

const profileSuccess = ref(false);
const passwordSuccess = ref(false);

const profileForm = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
    gender: props.user?.gender || '',
    date_of_birth: props.user?.date_of_birth || '',
});

const updateProfile = () => {
    profileForm.put('/profile', {
        onSuccess: () => {
            profileSuccess.value = true;
            setTimeout(() => profileSuccess.value = false, 3000);
        }
    });
};

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    passwordForm.put('/profile/password', {
        onSuccess: () => {
            passwordSuccess.value = true;
            passwordForm.reset();
            setTimeout(() => passwordSuccess.value = false, 3000);
        }
    });
};

const logout = () => router.post('/logout');
</script>

<style scoped>
.input-field {
    width: 100%; padding: 0.65rem 0.875rem; border: 1px solid #e5e7eb;
    border-radius: 0.75rem; font-size: 0.875rem; outline: none;
    transition: all 0.2s; font-family: 'Poppins', sans-serif; background: white;
}
.input-field:focus {
    border-color: #F0404B;
    box-shadow: 0 0 0 3px rgba(240, 64, 75, 0.1);
}
</style>
