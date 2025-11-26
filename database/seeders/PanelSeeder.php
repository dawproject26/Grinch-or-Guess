<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Phrase;
use App\Models\Panel;


class PanelSeeder extends Seeder
{
    public function run(): void
    {
        $panel = Panel::first();
        if (!$panel) {
            $panel = Panel::create(['title' => 'Panel Principal']);
        }
        $phrases = [
            // TUS FRASES ORIGINALES
            ['movie' => '300', 'phrase' => 'ESTO ES ESPARTA'],
            ['movie' => 'MATRIX', 'phrase' => 'YO SOLO PUEDO MOSTRARTE LA PUERTA TU ERES QUIEN LA TIENE QUE ATRAVESAR'],
            ['movie' => 'TERMINATOR', 'phrase' => 'SAYONARA BABY'],
            ['movie' => 'GLADIATOR', 'phrase' => 'MI NOMBRE ES MAXIMO DECIMO MERIDIO'],
            ['movie' => 'KARATE KID', 'phrase' => 'DAR CERA PULIR CERA'],
            ['movie' => 'STAR WARS', 'phrase' => 'LUKE YO SOY TU PADRE'],
            ['movie' => 'TOY STORY', 'phrase' => 'HAY UNA SERPIENTE EN MI BOTA'],
            ['movie' => 'STAR WARS', 'phrase' => 'HAZLO O NO LO HAGAS PERO NO LO INTENTES'],
            ['movie' => 'EL RESPLANDOR', 'phrase' => 'AQUI ESTA JOHNNY'],
            ['movie' => 'LOS SIMPSONS', 'phrase' => 'SIN TELE Y SIN CERVEZA HOMER PIERDE LA CABEZA'],
            ['movie' => 'EL CLUB DE LOS POETAS MUERTOS', 'phrase' => 'OH CAPITAN MI CAPITAN'],
            ['movie' => 'FROZEN', 'phrase' => 'HAY PERSONAS POR LAS QUE MERECE LA PENA DERRETIRSE'],
            ['movie' => 'EL REY LEON', 'phrase' => 'TODO LO QUE TOCA LA LUZ ES NUESTRO REINO'],
            ['movie' => 'TIBURON', 'phrase' => 'NECESITAREMOS OTRO BARCO MAS GRANDE'],
            ['movie' => 'APOCALIPSE NOW', 'phrase' => 'ME ENCANTA EL OLOR A NAPALM POR LA MANANA'],
            ['movie' => 'LOS JUEGOS DEL HAMBRE', 'phrase' => 'ME PRESENTO VOLUNTARIA COMO TRIBUTO'],
            ['movie' => 'EL VIAJE DE CHIHIRO', 'phrase' => 'NADA DE LO QUE SUCEDE SE OLVIDA JAMAS'],
            ['movie' => 'AQUI NO HAY QUIEN VIVA', 'phrase' => 'UN POQUITO DE POR FAVOR'],
            ['movie' => 'INTERSTELLAR', 'phrase' => 'EL AMOR ES LA UNICA FUERZA TRASCENDENTAL'],
            ['movie' => 'LA LLEGADA', 'phrase' => 'EL LENGUAJE MOLDEA NUESTRA REALIDAD'],
            ['movie' => 'EL DIARIO DE NOA', 'phrase' => 'NO PUEDES VIVIR TU VIDA PARA OTROS TIENES QUE HACER LO QUE ES CORRECTO PARA TI'],
            ['movie' => 'LA BELLA Y LA BESTIA', 'phrase' => 'EL AMOR NOS DA FUERZAS QUE NI IMAGINABAMOS TENER'],
            ['movie' => 'EL CURIOSO CASO DE BENJAMIN BUTTON', 'phrase' => 'NO SE TRATA DE CUANTO TIEMPO TENEMOS SINO DE COMO LO APROVECHAMOS'],

            // NUEVAS FRASES AÑADIDAS (100)
            ['movie' => 'EL PLANETA DEL TESORO', 'phrase' => 'A POR ELLOS SIN MIEDO'],
            ['movie' => 'EL PLANETA DEL TESORO', 'phrase' => 'SIGUE BRILLANDO CON LUZ PROPIA'],
            ['movie' => 'BREAKING BAD', 'phrase' => 'YO SOY EL PELIGRO'],
            ['movie' => 'BREAKING BAD', 'phrase' => 'DI MI NOMBRE'],
            ['movie' => 'BREAKING BAD', 'phrase' => 'NO ESTOY EN PELIGRO YO SOY EL PELIGRO'],
            ['movie' => 'BREAKING BAD', 'phrase' => 'SOMOS NOSOTROS Y YA ESTA'],
            ['movie' => 'JUEGO DE TRONOS', 'phrase' => 'WINTER IS COMING'],
            ['movie' => 'JUEGO DE TRONOS', 'phrase' => 'UN LANNISTER SIEMPRE PAGA SUS DEUDAS'],
            ['movie' => 'JUEGO DE TRONOS', 'phrase' => 'EL CAOS NO ES UN ABISMO ES UNA ESCALERA'],
            ['movie' => 'JUEGO DE TRONOS', 'phrase' => 'LA NOCHE ES OSCURA Y ALBERGA HORRORES'],
            ['movie' => 'HERMANO OSO', 'phrase' => 'AMARTE CAMBIO MI VIDA PARA SIEMPRE'],
            ['movie' => 'HERMANO OSO', 'phrase' => 'LA VERDAD SIEMPRE SE VE MEJOR CON EL CORAZON'],
            ['movie' => 'HARRY POTTER', 'phrase' => 'ERES UN MAGO HARRY'],
            ['movie' => 'HARRY POTTER', 'phrase' => 'LA FELICIDAD SE HALLA HASTA EN LOS MOMENTOS MAS OSCUROS'],
            ['movie' => 'HARRY POTTER', 'phrase' => 'NI LA MAGIA MAS OSCURA PUEDE CONTRA EL AMOR'],
            ['movie' => 'HARRY POTTER', 'phrase' => 'NO TODO EL QUE VAGA ESTA PERDIDO'],
            ['movie' => 'BOJACK HORSEMAN', 'phrase' => 'SI NO TE GUSTA QUIEN ERES CAMBIALO'],
            ['movie' => 'BOJACK HORSEMAN', 'phrase' => 'NO PUEDE SER QUE TODO LO BUENO SEA SOLO TRISTE'],
            ['movie' => 'BOJACK HORSEMAN', 'phrase' => 'NO SE PUEDE SEGUIR CULPANDO A LOS DEMAS'],
            ['movie' => 'BOJACK HORSEMAN', 'phrase' => 'A VECES LA VIDA ES UNA PUTA MIERDA Y YA ESTA'],
            ['movie' => 'HORA DE AVENTURAS', 'phrase' => 'SUFRE LA FUERZA DE MIL SOLES'],
            ['movie' => 'HORA DE AVENTURAS', 'phrase' => 'A VECES LAS COSAS PEQUENAS SON LAS MAS IMPORTANTES'],
            ['movie' => 'HORA DE AVENTURAS', 'phrase' => 'LO IMPORTANTE ES SEGUIR INTENTANDOLO'],
            ['movie' => 'HORA DE AVENTURAS', 'phrase' => 'LOS AMIGOS SON LA AVENTURA REAL'],
            ['movie' => 'ATAQUE A LOS TITANES', 'phrase' => 'SI NO LUCHAMOS NO GANAREMOS'],
            ['movie' => 'ATAQUE A LOS TITANES', 'phrase' => 'LA LIBERTAD ESTA DETRAS DE ESAS MURALLAS'],
            ['movie' => 'ATAQUE A LOS TITANES', 'phrase' => 'VAMOS A RECUPERAR NUESTRA LIBERTAD'],
            ['movie' => 'LA CASA DE PAPEL', 'phrase' => 'EL AMOR PUEDE SER LA MEJOR Y LA PEOR ARMA'],
            ['movie' => 'LA CASA DE PAPEL', 'phrase' => 'LA RESISTENCIA ES PARTE DE NUESTRA IDENTIDAD'],
            ['movie' => 'DARK', 'phrase' => 'EL TIEMPO ES UN CIRCULO'],
            ['movie' => 'DARK', 'phrase' => 'TODO ESTA CONECTADO'],
            ['movie' => 'DARK', 'phrase' => 'EL PRINCIPIO ES EL FINAL Y EL FINAL ES EL PRINCIPIO'],
            ['movie' => 'NORMAL PEOPLE', 'phrase' => 'ERES LA MEJOR COSA QUE ME HA PASADO'],
            ['movie' => 'ARCANE', 'phrase' => 'SIEMPRE HEMOS TENIDO UNA A LA OTRA'],
            ['movie' => 'STRANGER THINGS', 'phrase' => 'AMIGOS NO MIENTEN'],
            ['movie' => 'STRANGER THINGS', 'phrase' => 'NO ERES TU EL QUE ROMPE LAS REGLAS ERES LAS REGLAS'],
            ['movie' => 'STRANGER THINGS', 'phrase' => 'EL MUNDO AL REVES TE ESTA LLAMANDO'],
            ['movie' => 'BICHOS', 'phrase' => 'UN SOLO BICHO PUEDE CAMBIARLO TODO'],
            ['movie' => 'LOS SIMPSONS', 'phrase' => 'PARA LOCA TU CALVA'],
            ['movie' => 'LOS SIMPSONS', 'phrase' => 'PASO QUE VOY ARDIENDO'],

            // EXTRAS PARA LLEGAR A LAS 100 NUEVAS
            ['movie' => 'SPIDERMAN', 'phrase' => 'UN GRAN PODER CONLLEVA UNA GRAN RESPONSABILIDAD'],
            ['movie' => 'BATMAN', 'phrase' => 'POR QUE CAEMOS BRUCE PARA APRENDER A LEVANTARNOS'],
            ['movie' => 'ROCKY', 'phrase' => 'NO SE TRATA DE CUANTO PUEDES GOLPEAR SINO DE CUANTO PUEDES RESISTIR'],
            ['movie' => 'FAST AND FURIOUS', 'phrase' => 'LA FAMILIA ES LO PRIMERO'],
            ['movie' => 'SHREK', 'phrase' => 'ES MEJOR AFUERA QUE ADENTRO DIGO YO'],
            ['movie' => 'SHREK', 'phrase' => 'LOS OGROS SON COMO CEBOLLAS'],
            ['movie' => 'KUNG FU PANDA', 'phrase' => 'LA GRANDEZA VIENE DE CREER EN UNO MISMO'],
            ['movie' => 'KUNG FU PANDA', 'phrase' => 'EL AYER ES HISTORIA EL MANANA ES UN MISTERIO'],
            ['movie' => 'COCO', 'phrase' => 'RECUERDAME Y NO DEJES QUE ME VAYA'],
            ['movie' => 'COCO', 'phrase' => 'NUNCA SUBESTIMES EL PODER DE LA MUSICA'],
            ['movie' => 'AVATAR', 'phrase' => 'TE VEO'],
            ['movie' => 'AVATAR', 'phrase' => 'LA VIDA ES PRECIOSA'],
            ['movie' => 'AVENGERS', 'phrase' => 'YO SOY IRON MAN'],
            ['movie' => 'AVENGERS', 'phrase' => 'LO AME TRES MIL'],
            ['movie' => 'DRAGON BALL', 'phrase' => 'NO IMPORTA CUANTAS VECES CAIGA SIEMPRE ME LEVANTARE'],
            ['movie' => 'DRAGON BALL', 'phrase' => 'ESTE ES EL PODER DE UN SAYAYIN'],
            ['movie' => 'ONE PIECE', 'phrase' => 'QUIEN NO SE ARRIESGA NO GANA'],
            ['movie' => 'ONE PIECE', 'phrase' => 'UN SUEÑO ES UNA PROMESA DEL CORAZON'],
            ['movie' => 'NARUTO', 'phrase' => 'NUNCA ME RENDIRE'],
            ['movie' => 'NARUTO', 'phrase' => 'LOS QUE ROMPEN LAS REGLAS SON BASURA PERO LOS QUE ABANDONAN A SUS AMIGOS SON PEORES QUE BASURA'],
            ['movie' => 'HAIKYUU', 'phrase' => 'LOS PEQUENOS TAMBIEN PUEDEN VOLAR'],
            ['movie' => 'HAIKYUU', 'phrase' => 'EL BALON NUNCA MIENTE'],
            ['movie' => 'LA VIDA ES BELLA', 'phrase' => 'LA VIDA ES BELLA SI SABES MIRARLA'],
            ['movie' => 'BRAVEHEART', 'phrase' => 'PUEDEN QUITARNOS LA VIDA PERO JAMAS LA LIBERTAD'],
            ['movie' => 'WALL E', 'phrase' => 'AMOR'],
            ['movie' => 'WALL E', 'phrase' => 'MANTENTE SEGURO'],
            ['movie' => 'FINDING NEMO', 'phrase' => 'SIGUE NADANDO'],
            ['movie' => 'UP', 'phrase' => 'GRACIAS POR LA AVENTURA AHORA TE TOCA A TI CREAR LA TUYA'],
            ['movie' => 'RATATOUILLE', 'phrase' => 'CUALQUIERA PUEDE COCINAR'],
            ['movie' => 'EL SEÑOR DE LOS ANILLOS', 'phrase' => 'NO TODOS LOS QUE VAGAN ESTAN PERDIDOS'],
            ['movie' => 'EL SEÑOR DE LOS ANILLOS', 'phrase' => 'HASTA LA PERSONA MAS PEQUENA PUEDE CAMBIAR EL DESTINO'],
        ];

         foreach ($phrases as $phraseData) {
            Phrase::create([
                'movie' => $phraseData['movie'],
                'phrase' => $phraseData['phrase'],
                'panel_id' => $panel->id,
            ]);
        }
    }
}