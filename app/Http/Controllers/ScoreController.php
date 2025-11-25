<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Panel;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function letter(Request $request)
    {
        $letter = strtoupper($request->letter);
        $panel = Panel::with('phrases')->findOrFail($request->panel_id);
        $player = Player::findOrFail($request->player_id);

        $found = [];

        foreach ($panel->phrases as $idx => $phrase) {
            $positions = [];

            for ($i = 0; $i < strlen($phrase->phrase); $i++) {
                if ($phrase->phrase[$i] === $letter) {
                    $positions[] = $i;
                }
            }

            if(count($positions)){
                $player->score->score += count($positions) * 10;
                $player->score->save();
            }

            $found[] = [
                'phrase' => $phrase->phrase,
                'positions' => $positions
            ];
        }

        return response()->json([
            'found' => $found,
            'score' => $player->score->score
        ]);
    }

    public function guess(Request $request)
    {
        $guess = strtoupper($request->guess);

        $panel = Panel::with('phrases')->findOrFail($request->panel_id);
        $player = Player::findOrFail($request->player_id);

        $correct = false;

        foreach ($panel->phrases as $phrase) {
            if (trim($phrase->phrase) === trim($guess)) {
                $correct = true;
            }
        }

        if ($correct) {
            $player->score->score += 100;
        } else {
            $player->score->score = max(0, $player->score->score - 10);
        }

        $player->score->save();

        return response()->json([
            'result' => $correct ? 'correct' : 'wrong',
            'score' => $player->score->score
        ]);
    }
}
