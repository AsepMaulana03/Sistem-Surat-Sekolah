@extends(str_contains(strtolower(optional(Auth::user()->role)->name), 'kepala') || str_contains(strtolower(Auth::user()->name), 'kepala') || optional(Auth::user()->role)->code === 'kepsek' ? 'layouts.kepsek' : 'layouts.admin')

@section('page_title', 'Daftar Surat Masuk')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Surat Masuk</h3>
        @if(!optional(Auth::user()->role)->name || str_contains(strtolower(optional(Auth::user()->role)->name), 'tu') || str_contains(strtolower(Auth::user()->name), 'tu') || optional(Auth::user()->role)->name === 'admin')
        <a href="{{ route('incoming-letters.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Unggah Surat Masuk
        </a>
        @endif
    </div>

    <!-- Search & Filter Section -->
    <div class="px-6 py-4 border-b border-gray-50">
        <form action="{{ route('incoming-letters.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nomor atau judul surat..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none transition">
            </div>

            <select name="status" id="status" onchange="this.form.submit()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white text-gray-700 focus:ring-2 focus:ring-gray-900 outline-none min-w-[160px]">
                <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                <option value="menunggu_disposisi" {{ request('status') === 'menunggu_disposisi' ? 'selected' : '' }}>Menunggu Disposisi</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai / Terarsip</option>
            </select>
            
            <button type="submit" class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                Cari
            </button>

            @if(request()->hasAny(['search']) || (request()->has('status') && request('status') !== 'semua'))
            <a href="{{ route('incoming-letters.index') }}" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition">
                Reset
            </a>
            @endif
        </form>
    </div>

    @if($letters->count() > 0)
    <div class="divide-y divide-gray-50">
        @foreach($letters as $letter)
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between hover:bg-gray-50/60 transition-colors group gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-100 transition">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">{{ $letter->no_surat }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ $letter->perihal }} — <span class="font-medium text-gray-700">{{ $letter->asal_surat }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                @if($letter->status === 'menunggu_disposisi')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">Menunggu Disposisi</span>
                @else
                    <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-600 border border-green-200">Selesai / Terarsip</span>
                @endif
                <span class="text-xs text-gray-400 min-w-[80px] text-right">{{ $letter->created_at->format('d M Y') }}</span>
                <div class="flex items-center gap-1">
                    <form action="{{ route('incoming-letters.destroy', $letter->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat masuk ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                    <a href="{{ route('incoming-letters.show', $letter->id) }}" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Lihat">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="px-6 py-16 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-4 border border-gray-100">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
        </div>
        @if(request()->hasAny(['search']) || (request()->has('status') && request('status') !== 'semua'))
            <p class="text-sm font-medium text-red-500">Data Surat Tidak Ditemukan</p>
            <p class="text-xs text-gray-500 mt-1">Coba gunakan kata kunci atau filter status yang berbeda.</p>
            <a href="{{ route('incoming-letters.index') }}" class="mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors border border-gray-200 shadow-sm">
                Reset / Cari Ulang
            </a>
        @else
            <p class="text-sm font-medium text-gray-500">Belum ada surat masuk.</p>
        @endif
    </div>
    @endif
</div>
@endsection
