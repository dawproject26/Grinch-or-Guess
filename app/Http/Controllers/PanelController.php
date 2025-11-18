<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanelController extends Controller
{
    /**
     * Muestra el panel principal del juego y prepara los datos.
     */
    public function index()
    {
        // Paso 1: Generar el abecedario
        // La función range('A', 'Z') crea automáticamente un array con todas las letras mayúsculas.
        $alphabet = range('A', 'Z');

        // Paso 2: Retornar la vista, pasando el array del abecedario.
        // Asume que la vista se llama 'panel.blade.php' y está en la carpeta 'resources/views/panel/'.
        return view('panel.index', [
            'alphabet' => $alphabet,
        ]);
    }

    /**
     * Almacena la puntuación o el progreso del jugador (ruta POST /panel/store).
     */
    public function store(Request $request)
    {
        // Lógica para guardar la puntuación del jugador.
        // Aquí iría el código para procesar la puntuación recibida por la ruleta.

        // Por ahora, solo retorna a la misma página.
        return back()->with('success', 'Puntuación almacenada con éxito.');
    }
}











?>