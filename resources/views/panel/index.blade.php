<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Juego - {{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <!-- Contenedor del Abecedario -->
    <div class="letter-sonia" id="alphabet-sidebar">
        @php
            $alphabet = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
            ];
        @endphp
 
        @foreach ($alphabet as $letter)
            @php
                $baseFileName = strtolower($letter);
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
            <h1 class="titulo-panel">{{ $title }}</h1>
        </div>

        <div class="panel-container">
            @foreach(explode(' ', $phraseSeleccionada) as $indice => $palabra)
            <span class="palabra">
                @foreach(str_split($palabra) as $letra)
                <span class="letra oculta" data-letra="{{ strtoupper($letra) }}">
                    <img src="{{ asset('img/postit.png') }}" class="postit">
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
        <div id="result-display" style="text-align:center; margin:10px;"></div>
        
        <div class="marco-ruleta">
            <div id="flecha">⬆</div>
            <div id="ruleta">
                <img src="{{ asset('img/ruleta.png') }}" alt="rulete">
            </div>
        </div>

        <div style="text-align:center; margin-top: 12px;">
            <button id="btnGirar">GIRAR</button>
            <a href="{{ route('panel.reset') }}"><button>Reiniciar Juego</button></a>
        </div>  

        <p id="resultado" style="text-align:center; margin-top:10px; font-size:1.1rem;">...</p>
        <p id="temporizador" style="text-align:center; font-weight:700; margin-top:6px;">Tiempo restante: 02:00</p>

        <div id="efecto-temporizador" class="efecto-temporizador"></div>
    </div>
</div>

<script>
    // Configuración CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // Variables globales para control de letras
    const vocales = ['A', 'E', 'I', 'O', 'U'];
    const consonantes = ['B', 'C', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z'];
    let tipoSeleccionPermitido = null; // 'vocal', 'consonante', o null para todas

    $(document).ready(function() {
        // Evento click para las letras
        $('#alphabet-sidebar').on('click', '.key-container', function() {
            const container = $(this);
            const letter = container.data('letter');
            
            // Verificar si ya fue clickeada
            if (container.hasClass('disabled')) {
                return; 
            }

            // Verificar restricción de tipo de letra
            if (tipoSeleccionPermitido === 'vocal' && !vocales.includes(letter)) {
                $('#result-display').html(`<p style="color: orange;">¡Solo puedes seleccionar VOCALES en este turno!</p>`);
                return;
            }
            
            if (tipoSeleccionPermitido === 'consonante' && !consonantes.includes(letter)) {
                $('#result-display').html(`<p style="color: orange;">¡Solo puedes seleccionar CONSONANTES en este turno!</p>`);
                return;
            }

            // Marcar la tecla como deshabilitada
            container.addClass('disabled'); 

            // Cambiar imagen a tachada
            const imageElement = $(`#img-${letter}`);
            const tachedSrc = imageElement.data('tached-src');
            imageElement.attr('src', tachedSrc);

            // Llamar a la función principal
            manejarLetraSeleccionada(letter);
        });
    });

    // Función para manejar letras
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
                revelarLetrasConFlip(letra);
                $('#result-display').html(`<p style="color: green;">¡Letra ${letra} encontrada!</p>`);
                
                // Verificar si se completó la frase
                setTimeout(verificarFraseCompleta, 500);
            } else {
                $('#result-display').html(`<p style="color: red;">La letra ${letra} no está en la frase.</p>`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $('#result-display').html(`<p style="color: red;">Error al procesar la letra.</p>`);
        });
    };

    // Función para revelar letras
    function revelarLetrasConFlip(letra) {
        const letras = document.querySelectorAll(`.letra.oculta[data-letra="${letra.toUpperCase()}"]`);
        
        letras.forEach((letraElement, index) => {
            setTimeout(() => {
                letraElement.classList.remove('oculta');
                letraElement.classList.add('revelada');
            }, index * 200);
        });
    }

    // Lógica de la ruleta
    const ruleta = document.getElementById('ruleta');
    const btnGirar = document.getElementById('btnGirar');
    const displayResultado = document.getElementById('resultado');
    const temporizadorHTML = document.getElementById("temporizador");
    const efectoHTML = document.getElementById("efecto-temporizador");
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const TOTAL_SECTORES = 8;
    const GRADOS_POR_SECTOR = 360 / TOTAL_SECTORES;
    let anguloActual = 0;
    let estaGirando = false;

    const opcionesPorSector = [
        "Demogorgon", "Consonante", "Eleven", "Vocal", 
        "Vecna", "Consonante", "Demoperro", "Vocal"
    ];

    // --- Lógica del Temporizador ---
    let tiempoActual = 120;
    let temporizadorInterval = null;

    function formatoTiempo(seg){ 
        const m = Math.floor(seg/60); 
        const s = seg%60; 
        return `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`; 
    }

    // Función para mostrar Game Over
    function mostrarGameOver() {
        clearInterval(temporizadorInterval);
        temporizadorInterval = null;
        displayResultado.textContent = "¡GAME OVER! Se acabó el tiempo.";
        btnGirar.disabled = true;
        
        // Deshabilitar todas las letras
        document.querySelectorAll('.key-container').forEach(container => {
            container.classList.add('disabled');
        });
        
        // Mostrar mensaje en el display de resultados
        $('#result-display').html('<p style="color: red; font-size: 1.2rem; font-weight: bold;">¡GAME OVER! Se acabó el tiempo.</p>');
    }

    // Función para mostrar Victoria
    function mostrarVictoria() {
        clearInterval(temporizadorInterval);
        temporizadorInterval = null;
        displayResultado.textContent = "¡FELICIDADES! Has completado la frase.";
        btnGirar.disabled = true;
        
        // Deshabilitar todas las letras
        document.querySelectorAll('.key-container').forEach(container => {
            container.classList.add('disabled');
        });
        
        // Mostrar mensaje en el display de resultados
        $('#result-display').html('<p style="color: green; font-size: 1.2rem; font-weight: bold;">¡FELICIDADES! Has completado la frase.</p>');
    }

    // Sincronización con el servidor al cargar la página
    function cargarTiempoDesdeServidor() {
        fetch("{{ route('panel.temporizador') }}", {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            }
        })
        .then(resp => {
            if (!resp.ok) throw new Error('Error en la respuesta del servidor');
            return resp.json();
        })
        .then(data => {
            if(data.segundos_restantes !== undefined){
                tiempoActual = parseInt(data.segundos_restantes, 10);
                temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
                
                // Si el tiempo es 0, mostrar Game Over
                if (tiempoActual <= 0) {
                    mostrarGameOver();
                }
            }
        })
        .catch(error => {
            console.log("Usando tiempo por defecto:", error);
            // Si hay error, usar 120 por defecto
            tiempoActual = 120;
            temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
        });
    }

    // Cargar el tiempo al iniciar la página
    cargarTiempoDesdeServidor();

    function iniciarTemporizador(){
        if(!temporizadorInterval){
            temporizadorInterval = setInterval(()=>{
                if(tiempoActual > 0){
                    tiempoActual--;
                    temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
                    
                    // Si el tiempo llega a 0, Game Over
                    if (tiempoActual <= 0) {
                        mostrarGameOver();
                    }
                }
            }, 1000);
        }
    }

    // --- BOTÓN GIRAR ---
    btnGirar.addEventListener('click', () => {
        if (estaGirando) return;
        estaGirando = true;
        btnGirar.disabled = true;
        displayResultado.textContent = "Girando...";

        iniciarTemporizador();

        const vueltasMinimas = 5;
        const gradosAleatorios = Math.floor(Math.random() * 360);
        const giroTotal = (vueltasMinimas * 360) + gradosAleatorios;

        anguloActual += giroTotal;
        ruleta.style.transform = `rotate(${anguloActual}deg)`;
    });

    ruleta.addEventListener('transitionend', () => {
        estaGirando = false;

        const gradosNorm = anguloActual % 360;
        const posicionFlecha = 180; 
        let gradosResult = (360 - gradosNorm + posicionFlecha) % 360;
        const indiceGanador = Math.floor(gradosResult / GRADOS_POR_SECTOR);
        const indiceSeguro = indiceGanador >= 0 ? indiceGanador : 0;

        const opcion = opcionesPorSector[indiceSeguro];
        displayResultado.textContent = `La ruleta ha caído en: ${opcion}`;

        // Establecer el tipo de letra permitido según la opción
        if (opcion === 'Vocal') {
            tipoSeleccionPermitido = 'vocal';
            $('#result-display').html('<p style="color: blue;">Ahora puedes seleccionar VOCALES</p>');
        } else if (opcion === 'Consonante') {
            tipoSeleccionPermitido = 'consonante';
            $('#result-display').html('<p style="color: blue;">Ahora puedes seleccionar CONSONANTES</p>');
        } else {
            tipoSeleccionPermitido = null;
            $('#result-display').html('<p style="color: blue;">Puedes seleccionar cualquier letra</p>');
        }

        const efectoLocal = efectoMap[opcion] || 0;
        if(efectoLocal !== 0){
            efectoHTML.textContent = efectoLocal > 0 ? `¡${efectoLocal} segundos extras!` : `${efectoLocal} segundos`;
            
            efectoHTML.className = 'efecto-temporizador';
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

        // Enviar al servidor para actualizar el temporizador
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

                // Si el tiempo llega a 0, Game Over
                if (tiempoActual <= 0) {
                    mostrarGameOver();
                }
            }
        })
        .catch(err => {
            console.error('Error:', err);
        })
        .finally(() => {
            btnGirar.disabled = false;
        });
    });

    // Mapa de efectos (puntuación)
    const efectoMap = {
        "Vocal": 0, 
        "Consonante": 0, 
        "Demoperro": -5, 
        "Demogorgon": -10, 
        "Vecna": -20, 
        "Eleven": 20
    };

    // Función para verificar si se completó la frase
    function verificarFraseCompleta() {
        const letrasOcultas = document.querySelectorAll('.letra.oculta');
        
        if (letrasOcultas.length === 0) {
            mostrarVictoria();
        }
    }

    // Observador para cambios en las letras
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                setTimeout(verificarFraseCompleta, 100);
            }
        });
    });

    // Observar cambios en las letras
    document.querySelectorAll('.letra').forEach(letra => {
        observer.observe(letra, { attributes: true, attributeFilter: ['class'] });
    });

    // Verificar al cargar la página por si ya está completa
    setTimeout(verificarFraseCompleta, 1000);
</script>
</body>
</html>