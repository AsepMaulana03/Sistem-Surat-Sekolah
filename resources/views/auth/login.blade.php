<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Surat Digital SMA AL MANSHUR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex">

    <!-- Left Panel: Branding -->
    <div class="hidden lg:flex w-1/2 bg-gray-900 flex-col items-center justify-center p-16 relative overflow-hidden">
        <!-- Decorative circles -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative z-10 flex flex-col items-center text-center">
            <div class="flex items-center gap-6 mb-10">
                <img src="{{ asset('images/logo_yayasan.png') }}" alt="Logo Yayasan" class="w-20 h-20 object-contain drop-shadow-lg">
                <div class="w-px h-16 bg-white/20"></div>
                <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo Sekolah" class="w-20 h-20 object-contain drop-shadow-lg">
            </div>
            <h1 class="text-3xl font-bold text-white leading-tight mb-3">Sistem Surat Digital</h1>
            <h2 class="text-xl font-bold text-white mb-2">SMA AL MANSHUR</h2>
            <p class="text-sm text-gray-400 max-w-xs leading-relaxed mt-2">Kelola surat sekolah secara digital, efisien, dan terorganisir.</p>
        </div>
    </div>

    <!-- Right Panel: Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-sm">

            <!-- Mobile Logo -->
            <div class="flex lg:hidden justify-center gap-4 mb-8">
                <img src="{{ asset('images/logo_sekolah.png') }}" alt="Logo Sekolah" class="w-14 h-14 object-contain">
                <img src="{{ asset('images/logo_yayasan.png') }}" alt="Logo Yayasan" class="w-14 h-14 object-contain">
            </div>

            <div class="mb-8">
                <h3 class="text-2xl font-bold text-gray-900">Selamat Datang</h3>
                <p class="text-sm text-gray-400 mt-1">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition placeholder-gray-300"
                        placeholder="contoh@email.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition placeholder-gray-300"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="p-3 bg-red-50 border border-red-100 rounded-xl">
                    <p class="text-red-600 text-xs">{{ $errors->first() }}</p>
                </div>
                @endif

                <button type="submit"
                    class="w-full bg-gray-900 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-xl transition text-sm mt-2">
                    Masuk
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-8">SMA AL MANSHUR · Sistem Administrasi Surat</p>
        </div>
    </div>

</body>
</html>
