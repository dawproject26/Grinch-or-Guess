<?php

use App\Http\Controllers\PanelController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RouletteController;
use App\Http\Controllers\ScoreController;
use Illuminate\Support\Facades\Route;

// Ruta PRINCIPAL - Página de bienvenida
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Rutas de autenticación simple (SOLO NOMBRE)
Route::post('/login', [PlayerController::class, 'login'])->name('player.login');
Route::post('/register', [PlayerController::class, 'register'])->name('player.register');
Route::get('/logout', [PlayerController::class, 'logout'])->name('player.logout');

// Rutas del panel del juego
Route::middleware(['web'])->group(function () {
    Route::get('/panel', [PanelController::class, 'index'])->name('panel.index');
    Route::get('/panel/temporizador', [PanelController::class, 'temporizador'])->name('panel.temporizador');
    Route::post('/panel/girar', [PanelController::class, 'girar'])->name('panel.girar');
    Route::post('/panel/letra', [PanelController::class, 'letra'])->name('panel.letra');
    Route::post('/panel/check', [PanelController::class, 'checkLetter'])->name('panel.check');
    Route::get('/panel/reset', [PanelController::class, 'reset'])->name('panel.reset');
});

// Rutas de la ruleta
Route::post('/ruleta/girar', [RouletteController::class, 'spin'])->name('ruleta.spin');
Route::post('/ruleta/aplicar', [RouletteController::class, 'apply'])->name('ruleta.apply');

// Rutas de puntuación
Route::post('/puntuacion/letra', [ScoreController::class, 'letter'])->name('puntuacion.letra');
Route::post('/puntuacion/adivinar', [ScoreController::class, 'guess'])->name('puntuacion.guess');
