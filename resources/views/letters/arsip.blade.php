@extends('layouts.admin')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Arsip Surat</h1>
        <p class="text-sm text-gray-500 mt-0.5">Surat yang telah disetujui atau ditolak oleh Kepala Sekolah</p>
    </div>

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('letters.arsip') }}" class="mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="relative flex-1 min-w-[220px] max-w-xs">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari Surat..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Filter Jenis -->
            <select name="jenis" onchange="this.form.submit()"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 min-w-[160px]">
                <option value="semua" {{ request('jenis','semua')==='semua'?'selected':'' }}>Semua Jenis</option>
                @foreach($letterTypes as $code => $name)
                    <option value="{{ $code }}" {{ request('jenis')===$code?'selected':'' }}>{{ $name }}</option>
                @endforeach
            </select>

            <!-- Submit Cari -->
            <button type="submit"
                class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                Cari
            </button>
        </div>
    </form>

    <!-- Tabel Arsip -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Surat</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Surat</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengaju</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider text-center w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($letters as $i => $letter)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-4 text-gray-500 font-medium">{{ $letters->firstItem() + $i }}.</td>
                    <td class="px-5 py-4 font-medium text-gray-800">{{ $letter->letter_number ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-700">{{ $letterTypes[$letter->type_code] ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">
                        {{ $letter->letter_date ? $letter->letter_date->isoFormat('D MMM Y') : '-' }}
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $letter->user->name ?? '-' }}</td>
                    <td class="px-5 py-4">
                        @if($letter->status === 'approved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">
                                Disetujui
                            </span>
                        @elseif($letter->status === 'rejected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-500 border border-red-100">
                                Ditolak
                            </span>
                            @if($letter->rejection_note)
                                <button type="button" onclick="showNote('{{ addslashes($letter->rejection_note) }}')"
                                    class="ml-1 text-gray-400 hover:text-amber-500 transition" title="Lihat catatan penolakan">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                </button>
                            @endif
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        <a href="{{ route('letters.arsip.show', $letter->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Lihat Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </div>
                            <p class="text-gray-400 font-medium">Belum ada arsip surat</p>
                            <p class="text-gray-300 text-xs">Surat yang sudah disetujui atau ditolak akan tampil di sini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($letters->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $letters->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Catatan Penolakan -->
<div id="modal-note" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-800">Alasan Penolakan</h3>
        </div>
        <p id="note-content" class="text-gray-600 text-sm bg-red-50 rounded-lg px-4 py-3 border border-red-100 leading-relaxed mb-6"></p>
        <button onclick="document.getElementById('modal-note').classList.add('hidden')"
            class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-200 transition">
            Tutup
        </button>
    </div>
</div>

<script>
    function showNote(note) {
        document.getElementById('note-content').textContent = note;
        document.getElementById('modal-note').classList.remove('hidden');
    }
    document.getElementById('modal-note').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endsection
