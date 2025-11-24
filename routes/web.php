<?php

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
/* Define la ruta GET para la página de inicio (la URL raíz, '/'). */ 
Route::get('/', function () {

    /* Muestra la vista de Blade llamada 'welcome.blade.php' cuando alguien
    visita la página principal. */ 
    return view('welcome');
});

/* Define la ruta GET para el 'dashboard' (el escritorio principal). */ 
Route::get('/dashboard', function () {

    /* Restricción de Acceso:** Solo permite el acceso a usuarios que hayan 
    iniciado sesión ('auth') y cuyo email haya sido verificado ('verified'). */ 
    return view('dashboard');

    /* Asigna el nombre interno 'dashboard' para poder referenciar esta URL 
    fácilmente en el código. */ 
})->middleware(['auth', 'verified'])->name('dashboard');

// --- Rutas Clave del Juego (Registro, Inicio de Sesión y Panel) ---

/* Ruta GET para mostrar el formulario de registro del jugador. Llama al método 
'register' del PlayerController. */ 
Route::get('/register',[PlayerController::class,'register'])->name('player.register');

/* Ruta POST para procesar el formulario de inicio de sesión del jugador. Llama al 
método 'login' del PlayerController. */ 
Route::post('/login', [PlayerController::class, 'login'])->name('player.login');
Route::get('/index',[PanelController::class,'index'])->name('panel.index');

//recibe la puntución del jugador
/* Ruta GET para mostrar el panel principal del juego (donde estará la ruleta y la frase). 
Llama al método 'index' del PanelController. */ 
Route::post('/panel/store',[PanelController::class,'store'])->name('panel.store');
Route::post('/panel/check', [PanelController::class, 'checkLetter'])->name('panel.check');

Route::middleware('auth')->group(function () {
   
    /* Ruta GET para mostrar el formulario de edición del perfil 
    del usuario logueado. */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    
    /* Ruta PATCH (usada para actualizar datos) para procesar el envío del formulario de actualización de perfil.*/
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    /* Ruta DELETE (usada para eliminar) para eliminar la cuenta del usuario. */ 
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
