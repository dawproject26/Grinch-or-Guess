<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PanelController;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/main', function () {
    return view('wheelfireclub.main');
});

// GET: obtener estado actual del temporizador
Route::get('/panel', [PanelController::class, 'index'])->name('panel.index');

// POST: registrar un giro (el cliente envía la opción que salió)
Route::post('/panel/girar', [PanelController::class, 'girar'])->name('panel.girar');

// Reiniciar ruleta + temporizador (botón desde la vista)
Route::get('/reset-ruleta', function () {
    session()->forget('opciones_ruleta');
    DB::table('temporizador')->update(['segundos_restantes' => 120]);
    return redirect('/main');
})->name('ruleta.reset');

// Resto de tus rutas (dashboard, auth...) las dejas tal cual
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/register',[PlayerController::class,'register'])->name('player.register');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';