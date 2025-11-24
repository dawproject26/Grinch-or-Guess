<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Panel;
use App\Models\Phrase;

class PanelSeeder extends Seeder
{
    public function run(): void
    {
        $movies = [
            '300' => 'ESTO ES ESPARTA',
            'MATRIX' => 'YO SOLO PUEDO MOSTRARTE LA PUERTA TU ERES QUIEN LA TIENE QUE ATRAVESAR',
            'TERMINATOR' => 'SAYONARA BABY',
            'GLADIATOR' => 'MI NOMBRE ES MAXIMO DECIMO MERIDIO',
            'KARATE KID' => 'DAR CERA PULIR CERA',
            'STAR WARS' => 'LUKE YO SOY TU PADRE',
            'TOY STORY' => 'HAY UNA SERPIENTE EN MI BOTA',
            'STAR WARS 2' => 'HAZLO O NO LO HAGAS PERO NO LO INTENTES',
            'EL RESPLANDOR' => 'AQUI ESTA JOHNNY',
            'LOS SIMPSONS' => 'SIN TELE Y SIN CERVEZA HOMER PIERDE LA CABEZA',
            'EL CLUB DE LOS POETAS MUERTOS' => 'OH CAPITAN MI CAPITAN',
            'FROZEN' => 'HAY PERSONAS POR LAS QUE MERECE LA PENA DERRETIRSE',
            'EL REY LEON' => 'TODO LO QUE TOCA LA LUZ ES NUESTRO REINO',
            'TIBURON' => 'NECESITAREMOS OTRO BARCO MAS GRANDE',
            'APOCALIPSE NOW' => 'ME ENCANTA EL OLOR A NAPALM POR LA MANANA',
            'LOS JUEGOS DEL HAMBRE' => 'ME PRESENTO VOLUNTARIA COMO TRIBUTO',
            'EL VIAJE DE CHIHIRO' => 'NADA DE LO QUE SUCEDE SE OLVIDA JAMAS',
            'AQUI NO HAY QUIEN VIVA' => 'UN POQUITO DE POR FAVOR',
            'INTERSTELLAR' => 'EL AMOR ES LA UNICA FUERZA TRASCENDENTAL',
            'LA LLEGADA' => 'EL LENGUAJE MOLDEA NUESTRA REALIDAD',
            'EL DIARIO DE NOA' => 'NO PUEDES VIVIR TU VIDA PARA OTROS TIENES QUE HACER LO QUE ES CORRECTO PARA TI',
            'LA BELLA Y LA BESTIA' => 'EL AMOR NOS DA FUERZAS QUE NI IMAGINABAMOS TENER',
            'EL CURIOSO CASO DE BENJAMIN BUTTON' => 'NO SE TRATA DE CUANTO TIEMPO TENEMOS SINO DE COMO LO APROVECHAMOS'
        ];

        foreach ($movies as $movie => $phrase) {
            $panel = Panel::create(['title' => $movie]);
            
            Phrase::create([
                'panel_id' => $panel->id,
                'phrase' => $phrase
            ]);
        }
    }
}