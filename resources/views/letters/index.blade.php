@extends('layouts.admin')

@section('page_title', 'Daftar Surat')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Daftar Surat</h1>
            <p class="text-xs text-gray-400 mt-0.5">Kelola seluruh surat yang ada di sistem</p>
        </div>
        <a href="{{ route('letters.create') }}" id="btn-buat-surat"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Surat
        </a>
    </div>

    <!-- Filter & Search -->
    <form method="GET" action="{{ route('letters.index') }}" id="filter-form">
        <div class="flex flex-wrap items-center gap-3 mb-5">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" id="input-search"
                    placeholder="Cari nomor atau judul surat..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition">
            </div>

            <select name="jenis" id="filter-jenis" onchange="this.form.submit()"
                class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white text-gray-700 focus:ring-2 focus:ring-gray-900 outline-none min-w-[160px]">
                <option value="semua" {{ request('jenis', 'semua') === 'semua' ? 'selected' : '' }}>Semua Jenis</option>
                @foreach($letterTypes as $code => $name)
                    <option value="{{ $code }}" {{ request('jenis') === $code ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <select name="status" id="filter-status" onchange="this.form.submit()"
                class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white text-gray-700 focus:ring-2 focus:ring-gray-900 outline-none min-w-[140px]">
                <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit" id="btn-cari"
                class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                Cari
            </button>
        </div>
    </form>

    <!-- Tabel -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-10">No</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Surat</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Jenis</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengaju</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($letters as $index => $letter)
                <tr class="hover:bg-gray-50/60 transition-colors" id="row-letter-{{ $letter->id }}">
                    <td class="px-5 py-4 text-gray-400 text-xs">{{ $letters->firstItem() + $index }}</td>
                    <td class="px-5 py-4 font-semibold text-gray-800 text-xs">{{ $letter->letter_number ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs max-w-[160px] truncate">{{ $letterTypes[$letter->type_code] ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">
                        {{ $letter->letter_date ? \Carbon\Carbon::parse($letter->letter_date)->isoFormat('D MMM Y') : '-' }}
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">{{ $letter->user->name ?? '-' }}</td>
                    <td class="px-5 py-4">
                        @php
                            $statusConfig = [
                                'draft'    => ['bg-gray-100 text-gray-500',   'Draft'],
                                'menunggu_persetujuan_pihak1' => ['bg-blue-50 text-blue-600', 'Menunggu Pihak 1'],
                                'pending'  => ['bg-amber-50 text-amber-600',  'Menunggu Kepsek'],
                                'approved' => ['bg-green-50 text-green-600',  'Disetujui'],
                                'rejected' => ['bg-red-50 text-red-500',      'Ditolak'],
                            ];
                            [$cls, $lbl] = $statusConfig[$letter->status] ?? ['bg-gray-100 text-gray-500', ucfirst($letter->status)];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium {{ $cls }}">{{ $lbl }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('letters.show', $letter) }}" title="Lihat" id="btn-show-{{ $letter->id }}"
                               class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('letters.edit', $letter) }}" title="Edit" id="btn-edit-{{ $letter->id }}"
                               class="p-1.5 text-gray-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button type="button" title="Hapus" id="btn-delete-{{ $letter->id }}"
                                onclick="confirmDelete({{ $letter->id }})"
                                class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <form id="form-delete-{{ $letter->id }}" action="{{ route('letters.destroy', $letter) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-400 font-medium text-sm">Belum ada surat ditemukan</p>
                            <a href="{{ route('letters.create') }}" class="text-xs text-gray-500 hover:text-gray-700 font-medium underline underline-offset-2">Buat surat baru →</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($letters->hasPages())
        <div class="px-5 py-3.5 border-t border-gray-50">
            {{ $letters->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="modal-hapus" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full mx-4 text-center">
        <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-800 mb-1">Hapus Surat?</h3>
        <p class="text-gray-400 text-sm mb-6">Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="closeModal()" id="btn-batal-hapus"
                class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
                Batal
            </button>
            <button onclick="submitDelete()" id="btn-konfirmasi-hapus"
                class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition text-sm">
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
    let deleteId = null;
    function confirmDelete(id) { deleteId = id; document.getElementById('modal-hapus').classList.remove('hidden'); }
    function closeModal() { deleteId = null; document.getElementById('modal-hapus').classList.add('hidden'); }
    function submitDelete() { if (deleteId) document.getElementById('form-delete-' + deleteId).submit(); }
    document.getElementById('modal-hapus').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
</script>
@endsection
