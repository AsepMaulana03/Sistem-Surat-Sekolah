<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kepala Sekolah — {{ config('app.name', 'Surat Digital') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f5f7f9; }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden">

    <!-- Sidebar Kepala Sekolah -->
    <aside class="w-64 bg-gray-900 flex flex-col justify-between hidden md:flex">
        <div>
            <!-- Header Sidebar -->
            <div class="flex flex-col items-center px-6 py-6 gap-3 border-b border-gray-700">
                <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo SMA AL MANSHUR" class="w-14 h-14 object-contain drop-shadow-md">
                <div class="font-bold text-xs tracking-widest leading-tight text-white text-center uppercase opacity-90">
                    Surat Digital<br>Sekolah
                </div>
            </div>

            <!-- Nav Kepala Sekolah -->
            <nav class="mt-4 px-3 space-y-1">
                <a href="{{ route('kepsek.approval') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('kepsek.approval') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm font-medium transition-all duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Approval Surat
                </a>

                <a href="{{ route('kepsek.arsip') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('kepsek.arsip') ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm font-medium transition-all duration-150">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    Arsip Surat
                </a>
            </nav>
        </div>

        <!-- Info User & Logout -->
        <div class="border-t border-gray-700 p-4">
            <div class="flex items-center gap-3 px-2 mb-3">
                <div class="w-7 h-7 rounded-full bg-gray-700 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-500">Kepala Sekolah</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition text-xs font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-gray-50">

        <!-- Topbar -->
        <header class="bg-white border-b border-gray-100 px-8 h-16 flex items-center justify-between flex-shrink-0">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">
                    @hasSection('page_title')
                        @yield('page_title')
                    @else
                        {{ ucfirst(request()->segment(2) ?: 'Kepala Sekolah') }}
                    @endif
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">SMA AL MANSHUR — Sistem Surat Digital</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-400">Kepala Sekolah</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl text-sm font-medium shadow-sm">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl text-sm font-medium shadow-sm">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>

</body>
</html>
