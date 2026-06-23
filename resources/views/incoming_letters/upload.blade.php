@extends(str_contains(strtolower(optional(Auth::user()->role)->name), 'kepala') || str_contains(strtolower(Auth::user()->name), 'kepala') || optional(Auth::user()->role)->code === 'kepsek' ? 'layouts.kepsek' : 'layouts.admin')

@section('page_title', 'Unggah Surat Masuk Fisik')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Unggah Berkas</h2>
            <p class="text-sm text-gray-500 mt-2">Pindai surat fisik Anda menjadi file PDF atau JPG, kemudian unggah di sini. Sistem akan mencoba mengekstrak data menggunakan Engine OCR.</p>
        </div>

        <form action="{{ route('incoming-letters.ocr') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File (PDF/JPG)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-indigo-500 transition-colors bg-gray-50">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none px-1">
                                <span>Unggah file</span>
                                <input id="file" name="file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
                            </label>
                            <p class="pl-1">atau seret ke sini</p>
                        </div>
                        <p class="text-xs text-gray-500">PDF, PNG, JPG maksimal 5MB</p>
                    </div>
                </div>
                @error('file')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('incoming-letters.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    Lanjutkan ke OCR
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('file').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var label = e.target.previousElementSibling;
        label.textContent = fileName;
    });
</script>
@endsection
