<?php

namespace App\Http\Controllers;

use App\Models\Panel;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    public function welcome()
    {
        return view('player.welcome');
    }

    public function register(Request $request)
    {
        $player = Player::create([
            'name' => $request->name
        ]);

        session(['player_id' => $player->id]);

        return redirect()->route('wheelfireclub.panel');
    }

    public function login(Request $request)
    {
        $player = Player::where('name', $request->name)->first();

        if (!$player) {
            return back()->with('error', 'Jugador no encontrado');
        }

        session(['player_id' => $player->id]);

        return redirect()->route('wheelfireclub.panel');
    }

    public function logout()
    {
        session()->forget('player_id');
        return redirect()->route('welcome');
    }

    public function index()
    {
        $panel = Panel::with('phrases')->inRandomOrder()->first();

        if(!$panel){
            abort(500, "No hay paneles en la base de datos.");
        }

        return view('wheelfireclub.panel', compact('panel'));
    }

    public function temporizador()
    {
        $temporizador = DB::table('temporizador')->first();

        if (!$temporizador) {
            DB::table('temporizador')->insert([
                'segundos_restantes' => 120,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $temporizador = DB::table('temporizador')->first();
        }

        return response()->json([
            'segundos_restantes' => (int) $temporizador->segundos_restantes
        ]);
    }

    public function girar(Request $request)
    {
        $data = $request->validate([
            'opcion' => 'required|string'
        ]);

        $opcion = $data['opcion'];

        $segmentos = $request->session()->get('opciones_ruleta');

        if (!$segmentos) {
            $segmentos = [
                ['nombre' => 'Vocal', 'efecto' => 0, 'max' => 5, 'contador' => 0],
                ['nombre' => 'Vocal', 'efecto' => 0, 'max' => 5, 'contador' => 0],
                ['nombre' => 'Consonante', 'efecto' => 0, 'max' => 28, 'contador' => 0],
                ['nombre' => 'Consonante', 'efecto' => 0, 'max' => 28, 'contador' => 0],
                ['nombre' => 'Demoperro', 'efecto' => -5, 'max' => 5, 'contador' => 0],
                ['nombre' => 'Demogorgon', 'efecto' => -10, 'max' => 2, 'contador' => 0],
                ['nombre' => 'Vecna', 'efecto' => -20, 'max' => 1, 'contador' => 0],
                ['nombre' => 'Eleven', 'efecto' => 20, 'max' => 1, 'contador' => 0],
            ];

            $request->session()->put('opciones_ruleta', $segmentos);
        }

        $found = false;
        foreach ($segmentos as $i => $seg) {
            if ($seg['nombre'] === $opcion && $seg['contador'] < $seg['max']) {
                $segmentos[$i]['contador']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json(['error' => 'Opción no disponible'], 400);
        }

        $request->session()->put('opciones_ruleta', $segmentos);

        $tempo = DB::table('temporizador')->first();
        if (!$tempo) {
            DB::table('temporizador')->insert([
                'segundos_restantes' => 120,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $tempo = DB::table('temporizador')->first();
        }

        $segundos = (int) $tempo->segundos_restantes;

        $especiales = ['Demoperro', 'Demogorgon', 'Vecna', 'Eleven'];

        $efecto = 0;
        foreach ($segmentos as $seg) {
            if ($seg['nombre'] === $opcion) {
                $efecto = (int) $seg['efecto'];
                break;
            }
        }

        if (in_array($opcion, $especiales)) {
            $segundos += $efecto;
            $segundos = max(0, $segundos);

            DB::table('temporizador')->update([
                'segundos_restantes' => $segundos,
                'updated_at' => now()
            ]);
        }

        DB::table('giros')->insert([
            'opcion_nombre' => $opcion,
            'efecto' => $efecto,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'nombre' => $opcion,
            'efecto' => $efecto,
            'segundos_restantes' => $segundos
        ]);
    }

    public function show()
    {
        $phraseSeleccionada = Phrase::inRandomOrder()->first();

        Session::put([
            'frase_actual' => $phraseSeleccionada->phrase,
            'movie_actual' => $phraseSeleccionada->movie,
            'letras_encontradas' => []
        ]);


        return view('wheelfireclub.panel', [
            'phraseSeleccionada' => $phraseSeleccionada->phrase,
            'title' => $phraseSeleccionada->movie,
            'letrasEncontradas' => Session::get('letras_encontradas', [])
        ]);
    }

    public function letra(Request $request)
    {
        // Recibir la letra del request
        $letra = $request->input('letra');

        // Obtener datos de sesión
        $frase = Session::get('frase_actual');
        $letrasEncontradas = Session::get('letras_encontradas', []);
        $response = [];
        // Verificar si la letra está en la frase (case insensitive)
        //Si está en la frase la añadimos al array de letras encontradas y devolvemos 
        // un json con success true, la letra, las letras encontradas
        if (str_contains(strtoupper($frase), strtoupper($letra))) {
            $letrasEncontradas[] = strtoupper($letra);
            Session::put('letras_encontradas', $letrasEncontradas);
            $response = [
                'success' => true,
                'letra' => strtoupper($letra),
                'letrasEncontradas' => $letrasEncontradas
            ];
            //Si no está en la frase devolvemos un json con success false, la letra, las letras encontradas
        } else {
            $response = [
                'success' => false,
                'letra' => strtoupper($letra),
                'letrasEncontradas' => $letrasEncontradas
            ];
        }

        return response()->json($response);
    }

     /**
     * Muestra la vista principal del abecedario.
     * @return \Illuminate\Contracts\View\View
     */
    public function index2()
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
