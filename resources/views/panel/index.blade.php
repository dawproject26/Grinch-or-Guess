<!DOCTYPE html> <!-- SONIA -->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Juego</title>
    <!-- Incluye jQuery para simplificar el manejo de AJAX -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <!-- Contenedor del Abecedario -->
    <div class="letter-sonia" id="alphabet-sidebar">
        @php
        // 1. Definimos las letras del abecedario (sin Ñ).
            $alphabet = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
            ];
        @endphp
 
        @foreach ($alphabet as $letter)
            @php
                // 2. Definimos el nombre base de la imagen. 
                // Por defecto, es la letra en minúscula (ej: 'a', 'b', 'c').
                $baseFileName = strtolower($letter);
                // 3. ¡REGLA ESPECIAL PARA LA 'Y'!
                // Si la letra es 'Y', usamos 'igriega' en lugar de 'y'.
                if ($letter === 'Y') {
                    $baseFileName = 'igriega';
                }
            @endphp
 
            <div class="key-container" data-letter="{{ $letter }}">
                <img id="img-{{ $letter }}"
                 class="key-image"
                 src="{{ asset('img/letras/' . $baseFileName . '.png') }}"
                 alt="Letra {{ $letter }}"
                 data-tached-src="{{ asset('img/letras_tachadas/' . $baseFileName . '_tachada.png') }}">
            </div>
        @endforeach
    </div>

    <div class="panel-tirsa">
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
    </div>


<div class="david">
    <h1 style="text-align:center; margin-top:16px;">WheelFireClub</h1>

    <div class="contenedor-juego">
        <div class="marco-ruleta">
            <div id="flecha">⬆</div>

            <div id="ruleta">
            <img src="{{ asset('img/ruleta.png') }}" alt="rulete">
            </div>
        </div>

        <div style="text-align:center; margin-top: 12px;">
            <button id="btnGirar">GIRAR</button>
            <a href="{{ route('panel.reset') }}"><button>Reiniciar Ruleta</button></a>
        </div>  

        <p id="resultado" style="text-align:center; margin-top:10px; font-size:1.1rem;">...</p>
        <p id="temporizador" style="text-align:center; font-weight:700; margin-top:6px;">Tiempo restante: 02:00</p>

        <div id="efecto-temporizador" class="efecto-temporizador"></div>
    </div>
</div>


    <!-- ---------------------------------------------------- -->
    <!-- LÓGICA JAVASCRIPT/AJAX PARA EL CLIC Y EL CAMBIO DE IMAGEN -->
    <!-- ---------------------------------------------------- -->
    <script>
        // 🚨 PASO CRÍTICO: Configuración de seguridad para peticiones POST/AJAX
        // Laravel necesita este token para permitir la ruta POST '/panel/check'
        $.ajaxSetup({
            headers: {
                // Obtenemos el token que Laravel genera en el campo oculto
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $(document).ready(function() {
            // Se escucha el evento click en cualquier contenedor de tecla
            $('#alphabet-sidebar').on('click', '.key-container', function() {
                const container = $(this); // El DIV de la tecla
                const letter = container.data('letter'); // La letra (e.g., 'A')
                const imageElement = $(`#img-${letter}`); // La etiqueta IMG dentro del DIV
                
                // Si la tecla ya tiene la clase 'disabled', no hacemos nada (ya fue clicada)
                if (container.hasClass('disabled')) {
                    return; 
                }

                // 1. Marcar la tecla como deshabilitada y visualmente usada
                container.addClass('disabled'); 

                // 2. CAMBIO DE IMAGEN: Obtenemos la URL de la imagen tachada
                const tachedSrc = imageElement.data('tached-src');
                imageElement.attr('src', tachedSrc); // Cambia la fuente de la imagen

                // 3. Petición AJAX al controlador
                $.ajax({
                    // Usamos la ruta nombrada 'panel.check' definida en web.php
                    url: '{{ route('panel.check') }}',
                    type: 'POST',
                    data: {
                        letter: letter // Enviamos la letra seleccionada
                    },
                    success: function(response) {
                        // 4. Manejo de la respuesta exitosa (mostrar si es Vocal/Consonante)
                        const messageHtml = `<p>Has seleccionado la letra <strong>${response.letter}</strong>. Es una <strong>${response.type}</strong>.</p>`;
                        $('#result-display').html(messageHtml);
                    },
                    error: function(xhr) {
                        // Manejo de errores de servidor o validación
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Error desconocido al procesar la jugada.";
                        $('#result-display').html(`<p style="color: red;">¡Error! ${errorMsg}</p>`);
                        
                      
                    }
                });
            });
        });

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

    /*
  Lógica corregida:
  1. Array sincronizado con la imagen de Stranger Things.
  2. Evento de transición arreglado (ya no se borra a sí mismo).
*/

const ruleta = document.getElementById('ruleta');
const btnGirar = document.getElementById('btnGirar');
const displayResultado = document.getElementById('resultado');
const temporizadorHTML = document.getElementById("temporizador");
const efectoHTML = document.getElementById("efecto-temporizador");
// OJO: Asegúrate de que esta meta etiqueta existe en el <head>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

const TOTAL_SECTORES = 8;
const GRADOS_POR_SECTOR = 360 / TOTAL_SECTORES;
let anguloActual = 0;
let estaGirando = false; // Bandera de seguridad extra

/* MAPPING DE LA IMAGEN (Stranger Things)
   Leyendo la imagen en sentido de las agujas del reloj (Clockwise)
   Empezando desde el sector superior (aprox las 12 en punto) y girando a la derecha.
   
   NOTA: Ajustad esto si al probar veis que cae en uno y dice el de al lado.
   Orden visual estimado:
   1. Cinta Verde (Vocal)
   2. Demogorgon
   3. Cinta Naranja (Consonante)
   4. Eleven
   5. Cinta Verde (Vocal) - Abajo
   6. Vecna
   7. Cinta Naranja (Consonante)
   8. Demoperros
*/
const opcionesPorSector = [
    "Demogorgon",       // 0 - Abajo del todo (bajo la flecha)
    "Consonante",       // 1
    "Eleven",  // 2
    "Vocal",  // 3
    "Vecna",      // 4
    "Consonante",       // 5
    "Demoperro",   // 6
    "Vocal"   // 7
];


// Mapa de efectos (puntuación)
const efectoMap = {
    "Vocal": 0, 
    "Consonante": 0, 
    "Demoperro": -5, 
    "Demogorgon": -10, 
    "Vecna": -20, 
    "Eleven": 20
};

// --- Lógica del Temporizador ---
let tiempoActual = 120;
let temporizadorInterval = null;

function formatoTiempo(seg){ 
    const m = Math.floor(seg/60); 
    const s = seg%60; 
    return `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`; 
}
temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;

function iniciarTemporizador(){
    if(!temporizadorInterval){
        temporizadorInterval = setInterval(()=>{
            if(tiempoActual > 0){
                tiempoActual--;
                temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
            }
        }, 1000);
    }
}

// Sincronización inicial con servidor
fetch("{{ route('panel.index') }}")
    .then(r => r.json())
    .then(d => {
        if(d.segundos_restantes !== undefined){
            tiempoActual = parseInt(d.segundos_restantes, 10);
            temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
        }
    })
    .catch(e => console.log("Modo offline o error de red inicial"));

// --- BOTÓN GIRAR ---
btnGirar.addEventListener('click', () => {
    if (estaGirando) return; // Evita doble click
    estaGirando = true;
    btnGirar.disabled = true;
    displayResultado.textContent = "Girando...";

    iniciarTemporizador();

    // Cálculo de giro
    const vueltasMinimas = 5;
    const gradosAleatorios = Math.floor(Math.random() * 360);
    const giroTotal = (vueltasMinimas * 360) + gradosAleatorios;

    anguloActual += giroTotal; // Sumamos para que siempre gire en el mismo sentido
    ruleta.style.transform = `rotate(${anguloActual}deg)`;
});


ruleta.addEventListener('transitionend', () => {
    estaGirando = false;

    // 1. Calcular ángulos
    const gradosNorm = anguloActual % 360;
    
    // Ajuste: La flecha está ABAJO (180º).
    // Para saber qué sector está tocando la flecha, invertimos la lógica.
    // Si la rueda gira X grados, el sector ganador es el que queda en la posición de la flecha.
    const posicionFlecha = 180; 
    
    // Fórmula corregida para rotación horaria
    let gradosResult = (360 - gradosNorm + posicionFlecha) % 360;
    
    const indiceGanador = Math.floor(gradosResult / GRADOS_POR_SECTOR);
    
    // Aseguramos que el índice esté entre 0 y 7
    const indiceSeguro = indiceGanador >= 0 ? indiceGanador : 0;

    const opcion = opcionesPorSector[indiceSeguro];
    displayResultado.textContent = `La ruleta ha caído en: ${opcion}`;

    // 2. Efectos visuales inmediatos
    const efectoLocal = efectoMap[opcion] || 0;
    if(efectoLocal !== 0){
        efectoHTML.textContent = efectoLocal > 0 ? `¡${efectoLocal} segundos extras!` : `${efectoLocal} segundos`;
        
        // Clases de colores según personaje
        efectoHTML.className = 'efecto-temporizador'; // Reset
        if(efectoLocal > 0) efectoHTML.classList.add('positivo');
        else {
            if(opcion === 'Vecna') efectoHTML.classList.add('rojo-intenso');
            else if(opcion === 'Demogorgon') efectoHTML.classList.add('rojo-medio');
            else efectoHTML.classList.add('rojo-claro');
        }
        
        setTimeout(()=> { 
            efectoHTML.textContent=''; 
            efectoHTML.className='efecto-temporizador'; 
        }, 2500);
    }

    // 3. Enviar al servidor (Laravel)
    fetch("{{ route('panel.girar') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ opcion: opcion })
    })
    .then(resp => resp.ok ? resp.json() : Promise.reject(resp))
    .then(json => {
        if(json.segundos_restantes !== undefined){
            tiempoActual = parseInt(json.segundos_restantes, 10);
            temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
        }
    })
    .catch(err => {
        console.error('Error:', err);
    })
    .finally(() => {
        // Reactivamos el botón siempre, pase lo que pase
        btnGirar.disabled = false;
    });
});
    </script>
</body>
</html>