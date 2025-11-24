<?php
namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Score;
use App\Models\Timer;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        $player = Player::where('name', $request->name)->first();

        if (!$player) {
            return back()->with('error', 'Jugador no encontrado');
        }

        session([
            'player_id' => $player->id,
            'player_name' => $player->name
        ]);

        return redirect()->route('wheelfireclub.panel');
    }

    public function register(Request $request)
    {
        $player = Player::create([
            'name' => $request->name
        ]);

        Score::create([
            'player_id' => $player->id,
            'score' => 0
        ]);

        Timer::create([
            'player_id' => $player->id,
            'seconds' => 120
        ]);

        session([
            'player_id' => $player->id,
            'player_name' => $player->name
        ]);

        return redirect()->route('wheelfireclub.panel');
    }

    public function logout()
    {
        session()->forget(['player_id', 'player_name']);
        return redirect()->route('welcome');
    }
}