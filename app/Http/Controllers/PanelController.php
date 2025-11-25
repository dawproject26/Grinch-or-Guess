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
        // Obtener una frase aleatoria de la base de datos
        $phrase = Phrase::inRandomOrder()->first();

        if(!$phrase){
            // Si no hay frases, crear una por defecto
            $phrase = (object)[
                'movie' => 'PELÍCULA POR ADIVINAR',
                'phrase' => 'FRASE DE EJEMPLO'
            ];
        }

        // Guardar en sesión para verificar letras después
        Session::put([
            'frase_actual' => $phrase->phrase,
            'movie_actual' => $phrase->movie
        ]);

        return view('panel.index', [
            'title' => $phrase->movie,
            'phraseSeleccionada' => $phrase->phrase
        ]);
    }

    public function temporizador()
    {
        $playerId = session('player_id');
        
        $timer = Timer::where('player_id', $playerId)->first();
        
        if (!$timer) {
            $timer = Timer::create([
                'player_id' => $playerId,
                'seconds' => 120
            ]);
        }

        return response()->json([
            'segundos_restantes' => $timer->seconds
        ]);
    }

    public function girar(Request $request)
    {
        $opcion = $request->opcion;
        $playerId = session('player_id');

        $timer = Timer::where('player_id', $playerId)->first();
        if (!$timer) {
            $timer = Timer::create([
                'player_id' => $playerId,
                'seconds' => 120
            ]);
        }

        $segundos = $timer->seconds;

        // Aplicar efectos
        switch ($opcion) {
            case 'Demoperro': $segundos -= 5; break;
            case 'Demogorgon': $segundos -= 10; break;
            case 'Vecna': $segundos -= 20; break;
            case 'Eleven': $segundos += 20; break;
        }

        $segundos = max(0, $segundos);
        $timer->update(['seconds' => $segundos]);

        return response()->json([
            'segundos_restantes' => $segundos
        ]);
    }

    public function letra(Request $request)
    {
        $letra = strtoupper($request->letra);
        $frase = Session::get('frase_actual', '');
        
        $existe = strpos(strtoupper($frase), $letra) !== false;

        return response()->json([
            'success' => $existe,
            'letra' => $letra
        ]);
    }

    public function reset()
    {
        Session::forget(['frase_actual', 'movie_actual']);
        $playerId = session('player_id');
        
        if ($playerId) {
            Timer::where('player_id', $playerId)->update(['seconds' => 120]);
        }
        
        return redirect('/panel');
    }
}