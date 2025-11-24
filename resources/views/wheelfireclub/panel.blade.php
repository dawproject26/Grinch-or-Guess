<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wheel Fire Club</title>

    @vite(['resources/css/wheelfireclub.css', 'resources/js/postit.js'])
</head>
<body>
    <main>
        <div class="cabecera">
            <h1 class="titulo-panel">{{$title}}</h1>
        </div>

        <div class="panel-container">

            @foreach(explode(' ', $phraseSeleccionada) as $indice => $palabra)
                <span class="palabra">
                    @foreach(str_split($palabra) as $letra)
                        <span class="letra oculta" data-letra="{{ strtoupper($letra) }}">
                            <div class="cara cara-frontal">
                                <img src="{{ Vite::asset('resources/img/postit.png') }}" class="postit">
                            </div>

                            <div class="cara cara-trasera" aria-hidden="true">
                                {{ strtoupper($letra) }}
                            </div>
                        </span>
                    @endforeach
                </span>

                @if($indice < count(explode(' ', $phraseSeleccionada)) - 1)
                    <span class="espacio">&nbsp;&nbsp;&nbsp;</span>
                @endif
            @endforeach

        </div>
    </main>
</body>
</html>
