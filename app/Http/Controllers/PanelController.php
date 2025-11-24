<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
 
class PanelController extends Controller
{
    /**
     * Muestra la vista principal del abecedario.
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Devuelve la vista donde se renderiza el HTML del abecedario
        return view('panel.index');
    }
 
    /**
     * Recibe y procesa la letra seleccionada por el usuario (vía AJAX).
     * @param Request $request La petición HTTP.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLetter(Request $request)
    {
        // 1. SOLUCIÓN AL ERROR DE VALIDACIÓN:
        // En lugar de 'alpha' (que solo acepta letras A-Z), usamos 'regex'
        // para decirle que la letra debe ser UNA ÚNICA letra mayúscula (A-Z).
        // Esto permite pasar la 'Y' y cualquier otra letra del abecedario sin la 'Ñ'.
        $request->validate([
            'letter' => 'required|string|size:1|regex:/^[A-Z]$/', 
            // 'string' y 'size:1' es más seguro que 'min:1|max:1' y 'alpha'
        ]);
 
        // 2. Extracción y normalización a mayúsculas
        $selectedLetter = strtoupper($request->input('letter'));
 
        // 3. Definición de vocales
        $vowels = ['A', 'E', 'I', 'O', 'U'];
        // 4. Lógica de comprobación:
        if (in_array($selectedLetter, $vowels)) {
            $type = 'vocal';
        } else {
            // Caso general: Si no es vocal (A, E, I, O, U), es consonante.
            $type = 'consonante';
        }
        // NO necesitamos el caso especial para la 'Ñ' ni la 'Y' si solo la definimos como consonante.
        // Si quieres que la 'Y' sea especial, puedes dejar el IF:
        // if ($selectedLetter === 'Y') {
        //     $type = 'semivocal/consonante especial';
        // }
        // 5. Devolución de la respuesta JSON
        return response()->json([
            'success'   => true,
            'letter'    => $selectedLetter,
            'type'      => $type,
            // Quitamos el formato de Markdown (**) para que se vea bien en el HTML
            'message'   => "La letra '$selectedLetter' es una $type.", 
        ]);
    }
}