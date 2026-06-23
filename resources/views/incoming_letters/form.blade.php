@extends(str_contains(strtolower(optional(Auth::user()->role)->name), 'kepala') || str_contains(strtolower(Auth::user()->name), 'kepala') || optional(Auth::user()->role)->code === 'kepsek' ? 'layouts.kepsek' : 'layouts.admin')

@section('page_title', 'Validasi Data OCR')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Bagian Kiri: Preview Dokumen -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Pratinjau Dokumen</h3>
        <div class="flex-1 bg-gray-100 rounded-xl overflow-hidden border border-gray-200 flex items-center justify-center min-h-[500px]">
            @php
                $ext = pathinfo($file_path, PATHINFO_EXTENSION);
            @endphp
            @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                <img src="{{ Storage::url($file_path) }}" alt="Preview" class="max-w-full max-h-full object-contain">
            @else
                <iframe src="{{ Storage::url($file_path) }}" frameborder="0" class="w-full h-full min-h-[500px]"></iframe>
            @endif
        </div>
    </div>

    <!-- Bagian Kanan: Form Auto-fill -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <div>
                <h4 class="text-sm font-bold text-indigo-900">Hasil Ekstraksi OCR</h4>
                <p class="text-xs text-indigo-700">Data di bawah ini diisi otomatis. Mohon periksa kembali (Perbaiki Ejaan Secara Manual) jika ada yang kurang akurat.</p>
            </div>
        </div>

        <form action="{{ route('incoming-letters.store') }}" method="POST">
            @csrf
            <input type="hidden" name="file_path" value="{{ $file_path }}">

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                    <input type="text" name="no_surat" value="{{ old('no_surat', $ocr_data['no_surat']) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    @error('no_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asal Surat</label>
                    <input type="text" name="asal_surat" value="{{ old('asal_surat', $ocr_data['asal_surat']) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    @error('asal_surat') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perihal</label>
                    <textarea name="perihal" rows="3" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">{{ old('perihal', $ocr_data['perihal']) }}</textarea>
                    @error('perihal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('incoming-letters.create') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                    Ulangi Unggah
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan & Ajukan Disposisi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
