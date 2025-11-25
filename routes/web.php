<?php

use App\Http\Controllers\RouletteController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\TimerController;
use Illuminate\Support\Facades\DB;

/*<<<<<<< HEAD*/
/*Importa el controlador estándar de Laravel para gestionar 
el perfil del usuario (editar, actualizar, etc.).*/

use App\Http\Controllers\ProfileController;

/* Importa la clase principal de Rutas de Laravel, que es 
fundamental para definir todas las URLs. */ 
use Illuminate\Support\Facades\Route;

/* Importa el controlador PlayerController que usarás para -
manejar la lógica de registro e inicio de sesión específica 
de los jugadores. */
use App\Http\Controllers\PlayerController;

/* Importa el controlador PanelController, que maneja la lógica 
central del juego (mostrar frase, guardar puntuación). */
use App\Http\Controllers\PanelController;


// --- Rutas de Vista Estándar ---
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// --- Rutas Clave del Juego (Registro, Inicio de Sesión y Panel) ---
Route::get('/register',[PlayerController::class,'register'])->name('player.register');
Route::post('/login', [PlayerController::class, 'login'])->name('player.login');
Route::get('/index2',[PanelController::class,'index2'])->name('panel.index2');

Route::post('/panel/store',[PanelController::class,'store'])->name('panel.store');
Route::post('/panel/check', [PanelController::class, 'checkLetter'])->name('panel.check');

Route::get('/panel', [PanelController::class, 'show']);
Route::post('/panel/letra',[PanelController::class,'letra'])->name('panel.letra');


// --- Perfil del Usuario (auth) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// --- Rutas duplicadas del jugador (versión PlayerController) ---
Route::get('/', [PlayerController::class, 'welcome'])->name('welcome');

Route::post('/login', [PlayerController::class, 'login'])->name('player.login');
Route::post('/register', [PlayerController::class, 'register'])->name('player.register');
Route::get('/logout', [PlayerController::class, 'logout'])->name('player.logout');


// --- Panel estándar ---
Route::get('/panel', [PanelController::class, 'index'])->name('panel.index');
Route::post('/panel/girar', [PanelController::class, 'girar'])->name('panel.girar');

Route::get('/panel/reset', function () {
    session()->forget('opciones_ruleta');
    DB::table('temporizador')->update(['segundos_restantes' => 120]);
    return redirect('/panel');
})->name('panel.reset');


// --- Rutas Wheelfireclub (middleware jugador) ---
Route::middleware(['player.session'])->group(function () {
    Route::get('/wheelfireclub/panel', [PanelController::class, 'index'])->name('wheelfireclub.panel');
    Route::post('/wheelfireclub/spin', [RouletteController::class, 'spin'])->name('wheelfireclub.spin');
    Route::post('/wheelfireclub/roulette/apply', [RouletteController::class, 'apply'])->name('wheelfireclub.roulette.apply');
    Route::post('/wheelfireclub/check', [ScoreController::class, 'letter'])->name('wheelfireclub.check');
    Route::post('/wheelfireclub/guess', [ScoreController::class, 'guess'])->name('wheelfireclub.guess');
    Route::get('/wheelfireclub/timer/{player_id}', [TimerController::class, 'get'])->name('wheelfireclub.timer');
});
