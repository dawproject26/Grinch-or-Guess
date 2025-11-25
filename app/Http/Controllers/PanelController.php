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
}