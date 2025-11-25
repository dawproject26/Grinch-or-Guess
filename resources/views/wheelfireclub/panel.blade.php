<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wheel Fire Club</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    <img src="{{ Vite::asset('resources/img/postit.png') }}" class="postit">
                    <div class="letra-texto">{{ $letra }}</div>
                </span>
                @endforeach
            </span>
            @if($indice < count(explode(' ', $phraseSeleccionada)) - 1)
            <span class="espacio">&nbsp;&nbsp;&nbsp;</span>
            @endif
            @endforeach
        </div>
    </main>

    <script>
    // Función para hacer petición http a nuestro metodo de manejar letra del controlador PanelController
    window.manejarLetraSeleccionada = function(letra) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) return;
        
        fetch("/panel/letra", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({ letra: letra })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                //Si la letra está en la frase, revelamos las letras con animación flip
                revelarLetrasConFlip(letra);
            } else {
                alert('La letra "' + letra + '" no está en la frase.');
            }
        })
        .catch(error => console.error('Error:', error));
    };

// Función para revelar letras con animación flip
    function revelarLetrasConFlip(letra) {
        // Seleccionamos todas las letras ocultas que coinciden con la letra seleccionada
        // La letra de cada postit se ha añadido como atributo data-letra en el span correspondiente
        // Para saber que span contiene cada letra
        const letras = document.querySelectorAll(`.letra.oculta[data-letra="${letra.toUpperCase()}"]`);
        
        //Recorremos todos los span que nos ha devuelvo el querySelector anterior
        letras.forEach((letraElement, index) => {
            //Con setTimeout ponemos un retardo en el cambio de clase
            //El retardo es index * 200ms para que cada letra se revele con un pequeño retraso entre ellas
            //Al cambiar de clase se aplica el efecto flip en el CSS
            setTimeout(() => {
                letraElement.classList.remove('oculta');
                letraElement.classList.add('revelada');
            }, index * 200);
        });
    }
    </script>
</body>
</html>