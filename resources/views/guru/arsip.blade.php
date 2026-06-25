@extends('layouts.admin')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Arsip Surat (Pihak 1)</h1>
            <p class="text-sm text-gray-500 mt-0.5">Surat yang telah Anda setujui atau tolak sebagai Pihak 1</p>
        </div>
        <!-- Summary Badge -->
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-50 text-green-600 text-xs font-semibold rounded-lg border border-green-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ \App\Models\Letter::where('pihak1_id', Auth::id())->where('status','!=','draft')->where('status','!=','menunggu_persetujuan_pihak1')->where('status','!=','rejected')->count() }} Diteruskan
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 text-red-500 text-xs font-semibold rounded-lg border border-red-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ \App\Models\Letter::where('pihak1_id', Auth::id())->where('status','rejected')->count() }} Ditolak
            </span>
        </div>
    </div>

    <!-- Filter & Search -->
    <form method="GET" action="{{ route('guru.arsip') }}" class="mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nomor atau perihal surat..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <select name="jenis" onchange="this.form.submit()"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 min-w-[160px]">
                <option value="semua" {{ request('jenis','semua')==='semua'?'selected':'' }}>Semua Jenis</option>
                @foreach($letterTypes as $code => $name)
                    <option value="{{ $code }}" {{ request('jenis')===$code?'selected':'' }}>{{ $name }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 min-w-[130px]">
                <option value="semua" {{ request('status','semua')==='semua'?'selected':'' }}>Semua Status</option>
                <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending Kepsek</option>
                <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Disetujui Kepsek</option>
                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Ditolak</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Cari</button>
        </div>
    </form>

    <!-- Tabel Arsip -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase w-10">No</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Nomor Surat</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Surat</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Perihal</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Pengaju</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tgl. Proses</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Status Terkini</th>
                    <th class="px-5 py-4 text-center text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($letters as $i => $letter)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-4 text-gray-500">{{ $letters->firstItem() + $i }}.</td>
                    <td class="px-5 py-4 font-medium text-gray-800">{{ $letter->letter_number ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-700 text-xs">{{ $letterTypes[$letter->type_code] ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $letter->event_name ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">{{ $letter->user->name ?? '-' }}</td>
                    <td class="px-5 py-4 text-gray-600">
                        {{ $letter->updated_at ? $letter->updated_at->isoFormat('D MMM Y') : '-' }}
                    </td>
                    <td class="px-5 py-4">
                        @if($letter->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">
                                Disetujui Kepsek
                            </span>
                        @elseif($letter->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">
                                Pending Kepsek
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-500">
                                Ditolak
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Lihat Preview -->
                            <a href="{{ route('guru.show', $letter) }}" title="Lihat Surat"
                               class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <!-- Catatan Penolakan (jika ada) -->
                            @if($letter->status === 'rejected' && $letter->rejection_note)
                                <button type="button"
                                    onclick="showNote('{{ addslashes($letter->rejection_note) }}')"
                                    title="Lihat Catatan Penolakan"
                                    class="p-2 text-gray-500 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </div>
                            <p class="text-gray-400 font-semibold">Belum ada arsip surat</p>
                            <p class="text-gray-300 text-xs">Surat yang telah Anda setujui atau tolak akan muncul di sini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($letters->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $letters->links() }}</div>
        @endif
    </div>

</div>

<!-- Modal Catatan Penolakan -->
<div id="modal-note" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-800">Catatan Penolakan</h3>
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
