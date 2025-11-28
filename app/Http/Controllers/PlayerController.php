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
        // Si ya está logueado, redirigir al panel
        if (session('player_id')) {
            return redirect()->route('panel.index');
        }
        
        return view('welcome');
    }

    public function login(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $player = Player::where('name', $request->name)->first();

        if (!$player) {
            return back()->with('error', 'Jugador no encontrado');
        }
        session([
            'player_id' => $player->id,
            'player_name' => $player->name
        ]);

        return redirect()->route('panel.index');
    }

    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255|unique:players,name'
        ]);

        $player = Player::create([
            'name' => $request->name,
            'idavatar'=> $request->idavatar
        ]);

        Score::create([
            'player_id' => $player->id,
            'score' => 0
        ]);

        Timer::create([
            'player_id' => $player->id,
            'seconds' => 180
        ]);

        session([
            'player_id' => $player->id,
            'player_name' => $player->name
        ]);

        return redirect()->route('panel.index');
    }

    public function logout()
    {

        session()->forget(['player_id', 'player_name']);
        return redirect('/');
    }
}
