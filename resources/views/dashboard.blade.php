@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')

    <!-- Welcome Banner -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ Auth::user()->name }} 👋</h1>
            <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <a href="{{ route('letters.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Surat
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <!-- Total -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gray-900 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Total Surat</p>
                <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $totalSurat }}</p>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Menunggu</p>
                <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $pending }}</p>
            </div>
        </div>

        <!-- Disetujui -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Disetujui</p>
                <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $disetujui }}</p>
            </div>
        </div>

        <!-- Ditolak -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Ditolak</p>
                <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $ditolak }}</p>
            </div>
        </div>

    </div>

    <!-- Recent Letters -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Surat Terbaru</h3>
            <a href="{{ route('letters.index') }}" class="text-xs text-gray-400 hover:text-gray-700 font-medium transition">Lihat Semua →</a>
        </div>

        @if($recentLetters->count() > 0)
            <div class="divide-y divide-gray-50">
                @foreach($recentLetters as $letter)
                @php
                    $statusMap = [
                        'draft' => ['bg-gray-100 text-gray-500', 'Draft'],
                        'pending' => ['bg-amber-50 text-amber-600', 'Menunggu'],
                        'approved' => ['bg-green-50 text-green-600', 'Disetujui'],
                        'rejected' => ['bg-red-50 text-red-500', 'Ditolak'],
                    ];
                    [$badgeClass, $badgeLabel] = $statusMap[$letter->status] ?? ['bg-gray-100 text-gray-500', ucfirst($letter->status)];
                @endphp
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/60 transition-colors group">
                    <div class="flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:bg-white transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800">{{ $letter->title }}</h4>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $letter->user->name ?? 'Admin Tu' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        <span class="text-xs text-gray-400 hidden sm:block">{{ \Carbon\Carbon::parse($letter->created_at)->isoFormat('D MMM Y') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-16 flex flex-col items-center justify-center text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Belum ada surat</p>
                <p class="text-xs text-gray-400 mt-1">Mulai buat surat pertama Anda</p>
                <a href="{{ route('letters.create') }}" class="mt-4 px-4 py-2 bg-gray-900 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">
                    Buat Surat
                </a>
            </div>
        @endif
    </div>

@endsection
