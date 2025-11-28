<?php

namespace App\Http\Controllers;
use App\Models\Phrase;
use App\Models\Player;
use App\Models\Timer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PanelController extends Controller
{
    public function index()
    {
        // Verificar sesión
        if (!session('player_id')) {
            return redirect()->route('welcome')->with('error', 'Debes iniciar sesión primero');
        }

        // Obtener una frase aleatoria
        $phrase = Phrase::inRandomOrder()->first();

        if (!$phrase) {
            // Crear frase por defecto si no hay en la base de datos
            $phrase = (object)[
                'movie' => 'EL REY LEON',
                'phrase' => 'CICLO SIN FIN'
            ];
        }

        // Inicializar sesión del juego
        Session::put([
            'frase_actual' => $phrase->phrase,
            'movie_actual' => $phrase->movie,
            'letras_adivinadas' => [],
            'opcion_ruleta_actual' => null
        ]);

        // Inicializar timer a 3 minutos (180 segundos)
        $playerId = session('player_id');
        $timer = Timer::where('player_id', $playerId)->first();
        if (!$timer) {
            Timer::create([
                'player_id' => $playerId,
                'seconds' => 180
            ]);
        }

        return view('panel.index', [
            'title' => $phrase->movie,
            'phraseSeleccionada' => $phrase->phrase,
            'player_name' => session('player_name')
        ]);
    }

    public function temporizador()
    {
        $playerId = session('player_id');
        if (!$playerId) {
            return response()->json(['error' => 'No hay sesión'], 401);
        }

        $timer = Timer::where('player_id', $playerId)->first();
        
        if (!$timer) {
            $timer = Timer::create([
                'player_id' => $playerId,
                'seconds' => 180
            ]);
        }

        return response()->json([
            'segundos_restantes' => $timer->seconds
        ]);
    }

    public function girar(Request $request)
    {
        $playerId = session('player_id');
        if (!$playerId) {
            return response()->json(['error' => 'No hay sesión'], 401);
        }

        $opcion = $request->opcion;
        $timer = Timer::where('player_id', $playerId)->first();

        if (!$timer) {
            $timer = Timer::create([
                'player_id' => $playerId,
                'seconds' => 180
            ]);
        }

        $segundos = $timer->seconds;

        // Aplicar efectos de la ruleta
        switch ($opcion) {
            case 'DEMOPERRO': 
                $segundos -= 5; 
                break;
            case 'DEMOGORGON': 
                $segundos -= 10; 
                break;
            case 'VECNA': 
                $segundos -= 15; 
                break;
            case 'ELEVEN': 
                $segundos += 20; 
                break;
            case 'VOCAL': 
            case 'CONSONANTE': 
                Session::put('opcion_ruleta_actual', $opcion);
                break;
        }

        $segundos = max(0, $segundos);
        $timer->update(['seconds' => $segundos]);

        return response()->json([
            'segundos_restantes' => $segundos,
            'opcion_actual' => $opcion
        ]);
    }

    public function letra(Request $request)
    {
        $playerId = session('player_id');
        if (!$playerId) {
            return response()->json(['error' => 'No hay sesión'], 401);
        }

        $letra = strtoupper($request->letra);
        $frase = Session::get('frase_actual', '');
        
        // Verificar restricciones de ruleta
        $opcionRuleta = Session::get('opcion_ruleta_actual');
        if ($opcionRuleta === 'VOCAL' && !in_array($letra, ['A','E','I','O','U'])) {
            return response()->json([
                'success' => false,
                'error' => 'Solo puedes seleccionar vocales'
            ]);
        }
        
        if ($opcionRuleta === 'CONSONANTE' && in_array($letra, ['A','E','I','O','U'])) {
            return response()->json([
                'success' => false,
                'error' => 'Solo puedes seleccionar consonantes'
            ]);
        }

        $existe = strpos(strtoupper($frase), $letra) !== false;
        
        if ($existe) {
            $letrasAdivinadas = Session::get('letras_adivinadas', []);
            if (!in_array($letra, $letrasAdivinadas)) {
                $letrasAdivinadas[] = $letra;
                Session::put('letras_adivinadas', $letrasAdivinadas);
            }
        }

        return response()->json([
            'success' => $existe,
            'letra' => $letra,
            'letras_adivinadas' => Session::get('letras_adivinadas', [])
        ]);
    }

    public function checkLetter(Request $request)
    {
        $letra = strtoupper($request->letra);
        $frase = strtoupper(Session::get('frase_actual', ''));
        
        $posiciones = [];
        for ($i = 0; $i < strlen($frase); $i++) {
            if ($frase[$i] === $letra) {
                $posiciones[] = $i;
            }
        }

        return response()->json([
            'existe' => count($posiciones) > 0,
            'posiciones' => $posiciones,
            'letra' => $letra
        ]);
    }

    public function reset()
    {
        Session::forget(['frase_actual', 'movie_actual', 'letras_adivinadas', 'opcion_ruleta_actual']);
        $playerId = session('player_id');
        
        if ($playerId) {
            Timer::where('player_id', $playerId)->update(['seconds' => 180]);
        }
        
        return redirect('/panel');
    }
}
