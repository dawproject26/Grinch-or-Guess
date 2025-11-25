<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Phrase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PanelController extends Controller
{
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

    public function store(Request $request)
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
}
