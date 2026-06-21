@extends('layouts.kepsek')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Approval Surat</h1>
            <p class="text-sm text-gray-500 mt-0.5">Surat yang menunggu persetujuan Anda</p>
        </div>
        @php $totalPending = \App\Models\Letter::where('status','pending')->count(); @endphp
        @if($totalPending > 0)
        <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 text-amber-600 text-sm font-semibold rounded-lg border border-amber-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ $totalPending }} Surat Menunggu
        </span>
        @endif
    </div>

    <!-- Filter & Search -->
    <form method="GET" action="{{ route('kepsek.approval') }}" class="mb-6">
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
            <button type="submit" class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Cari</button>
        </div>
    </form>

    <!-- Tabel Approval -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase w-10">No</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Nomor Surat</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Surat</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Perihal</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Pengaju</th>
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
                    <td class="px-5 py-4 text-gray-600">
                        {{ $letter->letter_date ? $letter->letter_date->isoFormat('D MMM Y') : '-' }}
                    </td>
                    <td class="px-5 py-4 text-gray-600">{{ $letter->user->name ?? '-' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Lihat Preview -->
                            <a href="{{ route('kepsek.show', $letter) }}" title="Lihat Surat"
                               class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <!-- Setujui -->
                            <form action="{{ route('kepsek.approve', $letter) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Setujui surat ini?')"
                                    class="inline-flex items-center gap-1 px-3 py-2 bg-green-500 text-white text-xs font-semibold rounded-lg hover:bg-green-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Setujui
                                </button>
                            </form>
                            <!-- Tolak -->
                            <button type="button"
                                onclick="openRejectModal({{ $letter->id }})"
                                class="inline-flex items-center gap-1 px-3 py-2 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-semibold">Tidak ada surat yang menunggu persetujuan</p>
                            <p class="text-gray-400 text-xs">Semua surat sudah diproses</p>
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

<!-- Modal Tolak Surat -->
<div id="modal-reject" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-800">Tolak Surat</h3>
                <p class="text-xs text-gray-500">Tambahkan catatan alasan penolakan (opsional)</p>
            </div>
        </div>

        <form id="reject-form" method="POST" action="">
            @csrf
            <textarea name="rejection_note" id="rejection-note-input" rows="4"
                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 resize-none mb-5"
                placeholder="Tulis catatan penolakan di sini (opsional)..."></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-lg font-medium text-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-lg font-medium text-sm hover:bg-red-600 transition">
                    Ya, Tolak Surat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(letterId) {
        document.getElementById('reject-form').action = '/kepsek/approval/' + letterId + '/reject';
        document.getElementById('rejection-note-input').value = '';
        document.getElementById('modal-reject').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('modal-reject').classList.add('hidden');
    }

    document.getElementById('modal-reject').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>
@endsection
