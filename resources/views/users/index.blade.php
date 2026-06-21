@extends('layouts.admin')

@section('page_title', 'User Management')

@section('content')
<div x-data="userManager()" class="flex flex-col h-full">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">User Management</h1>
            <p class="text-xs text-gray-400 mt-0.5">Kelola pengguna dan hak akses sistem</p>
        </div>
        <div class="flex gap-2">
            <button @click="showRoleModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Kelola Role
            </button>
            <button @click="openUserModal(null)"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah User
            </button>
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex-1">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600">
                                {{ $user->role ? $user->role->name : 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <button @click="openUserModal({{ $user->toJson() }})"
                                    class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-400 font-medium">Belum ada user</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit User -->
    <div x-show="showUserModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4" @click.away="showUserModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit User' : 'Tambah User Baru'"></h3>
                <button type="button" @click="showUserModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form :action="isEdit ? `{{ url('users') }}/${userForm.id}` : `{{ route('users.store') }}`" method="POST"
                class="flex flex-col gap-4">
                @csrf
                <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="userForm.name" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" name="email" x-model="userForm.email" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Role</label>
                    <select name="role_id" x-model="userForm.role_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-gray-900 outline-none transition">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        Password <span x-show="isEdit" class="text-gray-300 font-normal normal-case">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password" :required="!isEdit" minlength="8"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition">
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-50">
                    <button type="button" @click="showUserModal = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gray-900 hover:bg-gray-700 text-white font-semibold rounded-xl transition text-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Kelola Role -->
    <div x-show="showRoleModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4" @click.away="showRoleModal = false">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Kelola Role</h3>
                <button type="button" @click="showRoleModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Daftar Role -->
            <div class="mb-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Role Saat Ini</p>
                <div class="border border-gray-100 rounded-xl overflow-hidden divide-y divide-gray-50 max-h-52 overflow-y-auto">
                    @foreach($roles as $role)
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $role->name }}</p>
                            <p class="text-xs text-gray-400">{{ $role->code }}</p>
                        </div>
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                            onsubmit="return confirm('Hapus role ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium transition">Hapus</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Form Tambah Role -->
            <div class="border-t border-gray-50 pt-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Tambah Role Baru</p>
                <form action="{{ route('roles.store') }}" method="POST" class="flex flex-col gap-3">
                    @csrf
                    <input type="text" name="name" required placeholder="Nama Role (Misal: Guru)"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-900 outline-none transition">
                    <input type="text" name="code" required placeholder="Kode unik (misal: guru)"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-900 outline-none transition">
                    <button type="submit"
                        class="w-full py-2.5 bg-gray-900 hover:bg-gray-700 text-white font-semibold rounded-xl transition text-sm">
                        Tambah Role
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userManager', () => ({
            showUserModal: false,
            showRoleModal: false,
            isEdit: false,
            userForm: { id: null, name: '', email: '', role_id: '' },
            openUserModal(user) {
                if (user) {
                    this.isEdit = true;
                    this.userForm = { id: user.id, name: user.name, email: user.email, role_id: user.role_id };
                } else {
                    this.isEdit = false;
                    this.userForm = { id: null, name: '', email: '', role_id: '' };
                }
                this.showUserModal = true;
            }
        }))
    });
</script>
@endsection
