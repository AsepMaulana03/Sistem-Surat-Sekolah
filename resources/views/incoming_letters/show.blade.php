@extends(str_contains(strtolower(optional(Auth::user()->role)->name), 'kepala') || str_contains(strtolower(Auth::user()->name), 'kepala') || optional(Auth::user()->role)->code === 'kepsek' ? 'layouts.kepsek' : 'layouts.admin')

@section('page_title', 'Detail Surat Masuk')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Bagian Kiri: Preview Dokumen (2/3 width) -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Dokumen Fisik</h3>
            <a href="{{ Storage::url($incomingLetter->file_path) }}" target="_blank" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                Buka di Tab Baru
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
        </div>
        <div class="flex-1 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 flex items-center justify-center min-h-[600px]">
            @php
                $ext = pathinfo($incomingLetter->file_path, PATHINFO_EXTENSION);
            @endphp
            @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                <img src="{{ Storage::url($incomingLetter->file_path) }}" alt="Preview" class="max-w-full max-h-full object-contain">
            @else
                <iframe src="{{ Storage::url($incomingLetter->file_path) }}" frameborder="0" class="w-full h-full min-h-[600px]"></iframe>
            @endif
        </div>
    </div>

    <!-- Bagian Kanan: Informasi & Disposisi (1/3 width) -->
    <div class="space-y-6">
        
        <!-- Kartu Informasi Surat -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Informasi Metadata</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 font-medium">Status</p>
                    <div class="mt-1">
                        @if($incomingLetter->status === 'menunggu_disposisi')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">Menunggu Disposisi</span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-600 border border-green-200">Selesai / Terarsip</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Nomor Surat</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $incomingLetter->no_surat }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Asal Surat</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $incomingLetter->asal_surat }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Tujuan Surat</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $incomingLetter->tujuan }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Perihal</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $incomingLetter->perihal }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Diunggah Oleh</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $incomingLetter->creator->name ?? 'Sistem' }} <span class="text-xs text-gray-400 font-normal">({{ $incomingLetter->created_at->format('d M Y H:i') }})</span></p>
                </div>
            </div>
        </div>

        <!-- Kartu Disposisi -->
        @if($incomingLetter->status === 'menunggu_disposisi' && (!optional(Auth::user()->role)->name || str_contains(strtolower(optional(Auth::user()->role)->name), 'kepala') || str_contains(strtolower(Auth::user()->name), 'kepala') || optional(Auth::user()->role)->name === 'admin'))
            <div class="bg-indigo-50 rounded-2xl border border-indigo-100 shadow-sm p-6">
                <h3 class="text-sm font-bold text-indigo-900 mb-4">Tindak Lanjut: Disposisi Digital</h3>
                <form action="{{ route('incoming-letters.disposisi', $incomingLetter->id) }}" method="POST">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-indigo-800 mb-1">Instruksi Disposisi Digital</label>
                        <textarea name="instruksi_disposisi" rows="4" required class="w-full px-4 py-2.5 bg-white border border-indigo-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors placeholder-gray-400" placeholder="Contoh: Segera tindak lanjuti, koordinasi dengan Waka Kurikulum..."></textarea>
                    </div>
                    <button type="submit" class="mt-4 w-full px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition shadow-sm flex justify-center items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Disposisi & Arsipkan
                    </button>
                </form>
            </div>
        @elseif($incomingLetter->status === 'selesai')
            <div class="bg-green-50 rounded-2xl border border-green-100 shadow-sm p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-sm font-bold text-green-900">Disposisi Selesai</h3>
                </div>
                <div class="bg-white rounded-xl p-4 border border-green-200">
                    <p class="text-xs text-gray-500 font-medium mb-1">Instruksi:</p>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $incomingLetter->instruksi_disposisi }}</p>
                    <p class="text-xs text-gray-400 mt-3 pt-3 border-t border-gray-100">Didisposisikan pada: {{ $incomingLetter->disposisi_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
