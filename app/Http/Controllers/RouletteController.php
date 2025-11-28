<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Timer;

class RouletteController extends Controller
{
    public function spin(Request $request)
    {
        $options = [

            'VOCAL', 'VOCAL', 'VOCAL',
            'CONSONANTE', 'CONSONANTE', 'CONSONANTE',
            'VECNA',
            'DEMOGORGON',
            'DEMOPERRO',
            'ELEVEN'
        ];

        $pick = $options[array_rand($options)];

        return response()->json(['option' => $pick]);
    }

    public function apply(Request $request)
    {
        $playerId = session('player_id');
        if (!$playerId) {
            return response()->json(['error' => 'No hay sesión'], 401);
        }

        $player = Player::find($playerId);
        if (!$player) {
            return response()->json(['error' => 'Jugador no encontrado'], 404);
        }

        $timer = $player->timer;
        if (!$timer) {
            $timer = Timer::create([
                'player_id' => $playerId,
                'seconds' => 180
            ]);
        }

        $option = strtoupper($request->option);

        // Aplicar efectos en el tiempo
        switch ($option) {
            case 'VECNA':
                $timer->seconds = max(0, $timer->seconds - 15);
                break;
            case 'DEMOGORGON':
                $timer->seconds = max(0, $timer->seconds - 10);
                break;
            case 'DEMOPERRO':
                $timer->seconds = max(0, $timer->seconds - 5);
                break;
            case 'ELEVEN':
                $timer->seconds += 20;
                break;
        }

        $timer->save();

        return response()->json([
            'action' => in_array($option, ['VOCAL','CONSONANTE']) ? 'pickletter' : 'time',
            'type' => $option,
            'seconds' => $timer->seconds
        ]);
    }
}