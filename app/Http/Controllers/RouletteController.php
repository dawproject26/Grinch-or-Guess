<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class RouletteController extends Controller
{
    public function spin(Request $request)
    {
        $options = [
            'VOCAL', 'VOCAL',
            'CONSONANTE', 'CONSONANTE',
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
        $player = Player::findOrFail($request->player_id);
        $timer = $player->timer;

        $option = strtoupper($request->option);

        if (in_array($option, ['VECNA','DEMOGORGON','DEMOPERRO'])) {
            $timer->seconds = max(0, $timer->seconds - 15);
            $timer->save();
            return response()->json(['action' => 'sub', 'seconds' => $timer->seconds]);
        }

        if ($option === 'ELEVEN') {
            $timer->seconds += 30;
            $timer->save();
            return response()->json(['action' => 'add', 'seconds' => $timer->seconds]);
        }

        return response()->json(['action' => 'pickletter', 'type' => $option]);
    }
}
