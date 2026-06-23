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
                <a href="{{ route('incoming-letters.show', $letter->id) }}" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="px-6 py-16 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
        </div>
        <p class="text-sm font-medium text-gray-500">Belum ada surat masuk.</p>
    </div>
    @endif
</div>
@endsection
