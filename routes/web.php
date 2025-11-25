<?php

use App\Http\Controllers\PanelController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Ruta principal
Route::get('/', [PlayerController::class, 'welcome'])->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas de jugador
Route::post('/login', [PlayerController::class, 'login'])->name('player.login');
Route::post('/register', [PlayerController::class, 'register'])->name('player.register');
Route::get('/logout', [PlayerController::class, 'logout'])->name('player.logout');

// Rutas del panel principal
Route::get('/panel', [PanelController::class, 'index'])->name('panel.index');
Route::get('/panel/temporizador', [PanelController::class, 'temporizador'])->name('panel.temporizador');
Route::post('/panel/girar', [PanelController::class, 'girar'])->name('panel.girar');
Route::post('/panel/check', [PanelController::class, 'checkLetter'])->name('panel.check');
Route::post('/panel/letra', [PanelController::class, 'letra'])->name('panel.letra');
Route::get('/panel/reset', function () {
    session()->forget(['frase_actual', 'movie_actual', 'letras_encontradas', 'opciones_ruleta']);
    $playerId = session('player_id');
    if ($playerId) {
        \App\Models\Timer::where('player_id', $playerId)->update(['seconds' => 120]);
    }
    return redirect('/panel');
})->name('panel.reset');

// Rutas de perfil (auth)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';