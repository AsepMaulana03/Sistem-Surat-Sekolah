<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setup', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate');
    \App\Models\User::updateOrCreate(
        ['email' => 'stafalmanshur@gmail.com'],
        ['name' => 'Staff TU', 'password' => \Illuminate\Support\Facades\Hash::make('almanshur123'), 'role' => 'tu']
    );
    \App\Models\User::updateOrCreate(
        ['email' => 'kepsekalmanshur@gmail.com'],
        ['name' => 'Kepala Sekolah', 'password' => \Illuminate\Support\Facades\Hash::make('almanshur123'), 'role' => 'kepsek']
    );
    return 'Setup complete';
});

Route::get('/dashboard', function () {
    $totalSurat = \App\Models\Letter::count();
    $pending = \App\Models\Letter::where('status', 'pending')->count();
    $disetujui = \App\Models\Letter::where('status', 'approved')->count();
    $ditolak = \App\Models\Letter::where('status', 'rejected')->count();
    $recentLetters = \App\Models\Letter::latest()->take(5)->get();

    return view('dashboard', compact('totalSurat', 'pending', 'disetujui', 'ditolak', 'recentLetters'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/surat', [\App\Http\Controllers\LetterController::class, 'index'])->name('letters.index');
    Route::get('/surat/buat', [\App\Http\Controllers\LetterController::class, 'create'])->name('letters.create');
    Route::get('/surat/arsip', [\App\Http\Controllers\LetterController::class, 'arsip'])->name('letters.arsip');
    Route::get('/surat/arsip/{letter}', [\App\Http\Controllers\LetterController::class, 'showArsip'])->name('letters.arsip.show');
    Route::post('/surat', [\App\Http\Controllers\LetterController::class, 'store'])->name('letters.store');
    Route::get('/surat/{letter}', [\App\Http\Controllers\LetterController::class, 'show'])->name('letters.show');
    Route::get('/surat/{letter}/edit', [\App\Http\Controllers\LetterController::class, 'edit'])->name('letters.edit');
    Route::put('/surat/{letter}', [\App\Http\Controllers\LetterController::class, 'update'])->name('letters.update');
    Route::delete('/surat/{letter}', [\App\Http\Controllers\LetterController::class, 'destroy'])->name('letters.destroy');

    // User Management
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    
    // Role Management
    Route::post('/roles', [\App\Http\Controllers\UserController::class, 'storeRole'])->name('roles.store');
    Route::delete('/roles/{role}', [\App\Http\Controllers\UserController::class, 'destroyRole'])->name('roles.destroy');

    // Template Surat
    Route::get('/templates', [\App\Http\Controllers\TemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [\App\Http\Controllers\TemplateController::class, 'store'])->name('templates.store');

    // Routes Kepala Sekolah
    Route::get('/kepsek/approval', [\App\Http\Controllers\KepsekController::class, 'approval'])->name('kepsek.approval');
    Route::post('/kepsek/approval/{letter}/approve', [\App\Http\Controllers\KepsekController::class, 'approve'])->name('kepsek.approve');
    Route::post('/kepsek/approval/{letter}/reject', [\App\Http\Controllers\KepsekController::class, 'reject'])->name('kepsek.reject');
    Route::get('/kepsek/arsip', [\App\Http\Controllers\KepsekController::class, 'arsip'])->name('kepsek.arsip');
    Route::get('/kepsek/surat/{letter}', [\App\Http\Controllers\KepsekController::class, 'show'])->name('kepsek.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
