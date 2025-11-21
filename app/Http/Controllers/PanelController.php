<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    // GET /panel -> devuelve estado actual del temporizador (segundos_restantes)
    public function index(Request $request)
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

    // POST /panel/girar -> recibe { opcion: "Vecna" } desde el cliente, registra y actualiza temporizador
    public function girar(Request $request)
    {
        $data = $request->validate([
            'opcion' => 'required|string'
        ]);

        $opcion = $data['opcion'];

        // Inicializar la sesión con los 8 segmentos (si no existe)
        $opcionesSesion = $request->session()->get('opciones_ruleta');
        if (!$opcionesSesion) {
            $opcionesSesion = [
                ['nombre' => 'Vocal', 'efecto' => 0, 'max' => 5, 'contador' => 0],
                ['nombre' => 'Vocal', 'efecto' => 0, 'max' => 5, 'contador' => 0],
                ['nombre' => 'Consonante', 'efecto' => 0, 'max' => 28, 'contador' => 0],
                ['nombre' => 'Consonante', 'efecto' => 0, 'max' => 28, 'contador' => 0],
                ['nombre' => 'Demoperro', 'efecto' => -5, 'max' => 5, 'contador' => 0],
                ['nombre' => 'Demogorgon', 'efecto' => -10, 'max' => 2, 'contador' => 0],
                ['nombre' => 'Vecna', 'efecto' => -20, 'max' => 1, 'contador' => 0],
                ['nombre' => 'Eleven', 'efecto' => 20, 'max' => 1, 'contador' => 0],
            ];
            $request->session()->put('opciones_ruleta', $opcionesSesion);
        }

        // Buscar el primer segmento válido que coincida con la opción enviada
        $opciones = $request->session()->get('opciones_ruleta');
        $encontrado = false;
        foreach ($opciones as $idx => $seg) {
            if ($seg['nombre'] === $opcion && $seg['contador'] < $seg['max']) {
                $opciones[$idx]['contador']++;
                $encontrado = true;
                break;
            }
        }

        // Si no se encontró (p. ej. ya agotada esa opción) -> devolver error con estado
        if (!$encontrado) {
            return response()->json(['error' => 'Opción no disponible'], 400);
        }

        // Guardar la sesión actualizada
        $request->session()->put('opciones_ruleta', $opciones);

        // Obtener temporizador actual
        $temporizador = DB::table('temporizador')->first();
        if (!$temporizador) {
            DB::table('temporizador')->insert([
                'segundos_restantes' => 120,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $temporizador = DB::table('temporizador')->first();
        }

        $segundos = (int) $temporizador->segundos_restantes;

        // Aplicar efecto SOLO si no es Vocal o Consonante
        $efectosPermitidos = ['Demoperro','Demogorgon','Vecna','Eleven'];
        $efectoAplicado = 0;
        // Buscar el efecto en el segmento (puede haber varias entradas con mismo nombre; usamos el que coincide)
        foreach ($opciones as $seg) {
            if ($seg['nombre'] === $opcion) {
                $efectoAplicado = (int) $seg['efecto'];
                break;
            }
        }

        if (in_array($opcion, $efectosPermitidos)) {
            $segundos += $efectoAplicado;
            $segundos = max(0, $segundos);
            DB::table('temporizador')->update([
                'segundos_restantes' => $segundos,
                'updated_at' => now()
            ]);
        }

        // Guardar histórico (siempre)
        DB::table('giros')->insert([
            'opcion_nombre' => $opcion,
            'efecto' => $efectoAplicado,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'nombre' => $opcion,
            'efecto' => $efectoAplicado,
            'segundos_restantes' => $segundos
        ]);
    }
}
