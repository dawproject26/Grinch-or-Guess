<?php

namespace App\Http\Controllers;

use App\Models\Player;

class TimerController extends Controller
{
    public function get($player_id)
    {
        $player = Player::findOrFail($player_id);
        return response()->json([
            'seconds' => $player->timer->seconds
        ]);
    }
}
