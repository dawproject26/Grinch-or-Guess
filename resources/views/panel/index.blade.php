<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wheel Fire Club - {{ $title ?? 'Juego' }}</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="layout-container">
        
        <!-- HEADER -->
        <div class="header">
            <h1 class="titulo-panel" id="movie-title">{{ $title ?? 'BOJACK HORSEMAN' }}</h1>
        </div>

        <!-- PANEL DEL ABECEDARIO (IZQUIERDA) -->
        <div class="alphabet-panel">
            <div id="alphabet-sidebar">
                @php
                    $alphabet = range('A', 'Z');
                @endphp
     
                @foreach ($alphabet as $letter)
                    @php
                        $baseFileName = strtolower($letter);
                        if ($letter === 'Y') {
                            $baseFileName = 'igriega';
                        }
                    @endphp
                    <div class="key-container" data-letter="{{ $letter }}" onclick="seleccionarLetra('{{ $letter }}')">
                        <img id="img-{{ $letter }}"
                             class="key-image"
                             src="{{ asset('img/letras/' . $baseFileName . '.png') }}"
                             alt="Letra {{ $letter }}"
                             data-tached-src="{{ asset('img/letras_tachadas/' . $baseFileName . '_tachada.png') }}"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'key-content\'>{{ $letter }}</div>'">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- TABLERO CENTRAL (POST-ITS) - ARRIBA -->
        <div class="board-panel">
            <div class="panel-container">
                <div id="frase-container">
                    <!-- Las letras se generarán aquí dinámicamente -->
                </div>
            </div>
        </div>

        <!-- RULETA - CENTRO -->
        <div class="wheel-panel">
            <div class="wheel-container">
                <div class="marco-ruleta">
                    <div id="flecha">⬆</div>
                    <div id="ruleta">
                        <img src="{{ asset('img/ruleta.png') }}" alt="ruleta">
                    </div>
                </div>

                <div class="wheel-controls">
                    <button id="btnGirar">GIRAR RULETA</button>
                    <p id="resultado">...</p>
                    <div id="efecto-temporizador" class="efecto-temporizador"></div>
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN DEL JUGADOR (DERECHA - ARRIBA) -->
        <div class="info-panel">
            <div class="player-info-card">
                <h3>Información del Jugador</h3>
                <div class="player-info-item">
                    <span class="player-info-label">Jugador:</span>
                    <span class="player-info-value">{{ session('player_name', 'Invitado') }}</span>
                </div>
                <div class="player-info-item">
                    <span class="player-info-label">Puntaje:</span>
                    <span class="player-info-value" id="puntos-actuales">0</span>
                </div>
                <div class="player-info-item">
                    <span class="player-info-label">Tiempo:</span>
                    <span class="player-info-value" id="temporizador">03:00</span>
                </div>
            </div>
        </div>

        <!-- CONTROLES (DERECHA - ABAJO) -->
        <div class="controls-panel">
            <button onclick="resetGame()" class="control-btn">🔄 Reiniciar Juego</button>
            <button onclick="logout()" class="control-btn">🚪 Salir</button>
            
            <div class="guess-section">
                <button id="btnAdivinar">🎯 Adivinar Frase Completa</button>
            </div>
        </div>

        <!-- MODAL PARA ADIVINAR -->
        <div id="guessModal" class="guess-modal">
            <div class="guess-modal-content">
                <h3>🔍 Adivinar la Frase Completa</h3>
                <p style="color: #ff6b6b; margin: 10px 0;">
                    ⚠️ ¿Estás seguro? Si fallas es GAME OVER y no podrás volver a jugar.
                </p>
                <input type="text" id="guessInput" class="guess-input" placeholder="Escribe la frase completa aquí...">
                <div class="guess-buttons">
                    <button onclick="confirmGuess()" class="guess-btn guess-confirm">✅ Confirmar</button>
                    <button onclick="closeGuessModal()" class="guess-btn guess-cancel">❌ Cancelar</button>
                </div>
            </div>
        </div>

        <!-- MENSAJES DE RESULTADO -->
        <div id="result-display"></div>
    </div>

    <script>
        // ===== VARIABLES GLOBALES =====
        let tiempoRestante = 180; // 3 minutos
        let temporizadorInterval;
        let letrasUsadas = [];
        let opcionRuletaActual = null;
        
        // VARIABLES DINÁMICAS DESDE LARAVEL
        let fraseActual = "{{ Session::get('frase_actual', 'BOJACK HORSEMAN') }}";
        let movieActual = "{{ Session::get('movie_actual', 'BOJACK HORSEMAN') }}";
        let playerName = "{{ session('player_name', 'Invitado') }}";
        
        let puntuacion = 0;
        let juegoActivo = true;

        // ===== CONFIGURACIÓN RULETA =====
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

        // ===== INICIALIZACIÓN =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando juego...');
            console.log('Frase actual:', fraseActual);
            console.log('Película actual:', movieActual);
            console.log('Jugador:', playerName);
            
            iniciarTemporizador();
            actualizarFraseDisplay();
            
            // Configurar eventos
            btnGirar.addEventListener('click', girarRuleta);
            document.getElementById('btnAdivinar').addEventListener('click', openGuessModal);
            
            // Mostrar título de la película
            document.getElementById('movie-title').textContent = movieActual;
            document.querySelector('.player-info-value:nth-child(1)').textContent = playerName;

            // Cargar tiempo desde servidor
            cargarTiempoDesdeServidor();
        });

        // ===== TEMPORIZADOR =====
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
                    tiempoRestante = parseInt(data.segundos_restantes, 10);
                    actualizarTemporizadorDisplay();
                }
            })
            .catch(error => {
                console.log("Usando tiempo por defecto:", error);
                tiempoRestante = 180;
                actualizarTemporizadorDisplay();
            });
        }

        function iniciarTemporizador() {
            actualizarTemporizadorDisplay();
            
            temporizadorInterval = setInterval(function() {
                if (tiempoRestante > 0 && juegoActivo) {
                    tiempoRestante--;
                    actualizarTemporizadorDisplay();

                    if (tiempoRestante <= 0) {
                        finDelJuego();
                    }
                }
            }, 1000);
        }

        function actualizarTemporizadorDisplay() {
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            temporizadorHTML.textContent = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
            
            // Cambiar estilo según el tiempo
            temporizadorHTML.classList.remove('timer-warning', 'timer-danger');
            if (tiempoRestante <= 30) {
                temporizadorHTML.classList.add('timer-danger');
            } else if (tiempoRestante <= 60) {
                temporizadorHTML.classList.add('timer-warning');
            }
        }

        // ===== RULETA =====
        function girarRuleta() {
            if (estaGirando || !juegoActivo) return;
            
            estaGirando = true;
            btnGirar.disabled = true;
            displayResultado.textContent = "Girando...";

            const vueltasMinimas = 5;
            const gradosAleatorios = Math.floor(Math.random() * 360);
            const giroTotal = (vueltasMinimas * 360) + gradosAleatorios;

            anguloActual += giroTotal;
            ruleta.style.transform = `rotate(${anguloActual}deg)`;
        }

        ruleta.addEventListener('transitionend', () => {
            estaGirando = false;

            const gradosNorm = anguloActual % 360;
            const posicionFlecha = 180; 
            let gradosResult = (360 - gradosNorm + posicionFlecha) % 360;
            const indiceGanador = Math.floor(gradosResult / GRADOS_POR_SECTOR);
            const indiceSeguro = indiceGanador >= 0 ? indiceGanador : 0;

            const opcion = opcionesPorSector[indiceSeguro];
            displayResultado.textContent = `La ruleta ha caído en: ${opcion}`;

            // NUEVO: Mostrar mensajes específicos según la opción
            if (opcion === 'Vocal') {
                opcionRuletaActual = 'VOCAL';
                mostrarMensaje('🔊 ¡Ahora puedes seleccionar solo VOCALES!', 'info');
            } else if (opcion === 'Consonante') {
                opcionRuletaActual = 'CONSONANTE';
                mostrarMensaje('🔇 ¡Ahora puedes seleccionar solo CONSONANTES!', 'info');
            } else if (opcion === 'Eleven') {
                opcionRuletaActual = null;
                mostrarMensaje('👧 ¡Eleven! +20 segundos - VUELVE A TIRAR', 'success');
            } else if (opcion === 'Vecna') {
                opcionRuletaActual = null;
                mostrarMensaje('👹 ¡Vecna! -20 segundos - VUELVE A TIRAR', 'error');
            } else if (opcion === 'Demogorgon') {
                opcionRuletaActual = null;
                mostrarMensaje('👺 ¡Demogorgon! -10 segundos - VUELVE A TIRAR', 'error');
            } else if (opcion === 'Demoperro') {
                opcionRuletaActual = null;
                mostrarMensaje('🐕 ¡Demoperro! -5 segundos - VUELVE A TIRAR', 'error');
            }

            // Aplicar efectos de tiempo
            aplicarEfectoRuleta(opcion);
            actualizarAbecedario();
            
            btnGirar.disabled = false;
        });

        function aplicarEfectoRuleta(opcion) {
            let cambioTiempo = 0;
            
            switch(opcion) {
                case 'Vocal':
                case 'Consonante':
                    // No cambia el tiempo, solo permite seleccionar letras
                    break;
                case 'Vecna':
                    cambioTiempo = -20;
                    efectoHTML.textContent = '👹 ¡Vecna te quita 20 segundos!';
                    efectoHTML.className = 'efecto-temporizador rojo-intenso';
                    break;
                case 'Demogorgon':
                    cambioTiempo = -10;
                    efectoHTML.textContent = '👺 ¡Demogorgon te quita 10 segundos!';
                    efectoHTML.className = 'efecto-temporizador rojo-medio';
                    break;
                case 'Demoperro':
                    cambioTiempo = -5;
                    efectoHTML.textContent = '🐕 ¡Demoperro te quita 5 segundos!';
                    efectoHTML.className = 'efecto-temporizador rojo-claro';
                    break;
                case 'Eleven':
                    cambioTiempo = 20;
                    efectoHTML.textContent = '👧 ¡Eleven te da 20 segundos extra!';
                    efectoHTML.className = 'efecto-temporizador positivo';
                    break;
            }

            if (cambioTiempo !== 0) {
                tiempoRestante = Math.max(0, tiempoRestante + cambioTiempo);
                actualizarTemporizadorDisplay();

                fetch("{{ route('panel.girar') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ 
                        opcion: opcion,
                        tiempo_actual: tiempoRestante
                    })
                })
                .then(resp => resp.ok ? resp.json() : Promise.reject(resp))
                .then(json => {
                    console.log('Tiempo actualizado en servidor:', json);
                })
                .catch(err => {
                    console.error('Error:', err);
                });

                setTimeout(() => {
                    efectoHTML.textContent = '';
                }, 3000);
            }
        }

        // ===== SISTEMA DE LETRAS =====
        function seleccionarLetra(letra) {
            if (!juegoActivo) {
                mostrarMensaje('El juego ha terminado', 'error');
                return;
            }

            if (letrasUsadas.includes(letra)) {
                mostrarMensaje('⚠️ Esta letra ya fue usada', 'warning');
                return;
            }

            if (opcionRuletaActual === 'VOCAL' && !'AEIOU'.includes(letra)) {
                mostrarMensaje('❌ Solo puedes seleccionar VOCALES', 'error');
                return;
            }
            
            if (opcionRuletaActual === 'CONSONANTE' && 'AEIOU'.includes(letra)) {
                mostrarMensaje('❌ Solo puedes seleccionar CONSONANTES', 'error');
                return;
            }

            if (!opcionRuletaActual) {
                mostrarMensaje('🎡 Gira la ruleta primero para seleccionar letras', 'warning');
                return;
            }

            letrasUsadas.push(letra);
            const keyContainer = document.querySelector(`[data-letter="${letra}"]`);
            keyContainer.classList.add('disabled');

            const imageElement = document.getElementById(`img-${letra}`);
            if (imageElement) {
                const tachedSrc = imageElement.getAttribute('data-tached-src');
                if (tachedSrc) {
                    imageElement.setAttribute('src', tachedSrc);
                }
            } else {
                const keyContent = keyContainer.querySelector('.key-content');
                if (keyContent) {
                    keyContent.classList.add('letra-tachada');
                }
            }

            const existe = fraseActual.toUpperCase().includes(letra);
            
            if (existe) {
                mostrarMensaje(`✅ ¡Letra ${letra} encontrada! +10 puntos`, 'success');
                actualizarPuntuacion(10);
                revelarLetra(letra);
                verificarFraseCompleta();
            } else {
                mostrarMensaje(`❌ Letra ${letra} no encontrada`, 'error');
            }

            opcionRuletaActual = null;
            actualizarAbecedario();
        }

        function revelarLetra(letra) {
            const letraElements = document.querySelectorAll('.letra');
            letraElements.forEach(element => {
                const letraOculta = element.getAttribute('data-letra');
                if (letraOculta === letra) {
                    element.classList.add('revelada');
                    element.querySelector('.letra-texto').textContent = letra;
                }
            });
        }

        function actualizarFraseDisplay() {
            const fraseContainer = document.getElementById('frase-container');
            fraseContainer.innerHTML = '';
            
            // Limpiar y normalizar la frase
            const fraseLimpia = fraseActual.toUpperCase().replace(/[^A-Z\s]/g, '');
            const palabras = fraseLimpia.split(' ').filter(palabra => palabra.length > 0);
            
            console.log('Frase procesada:', fraseLimpia);
            console.log('Palabras:', palabras);
            
            palabras.forEach(palabra => {
                const palabraDiv = document.createElement('div');
                palabraDiv.className = 'palabra';
                
                for (let i = 0; i < palabra.length; i++) {
                    const letra = palabra[i];
                    const letraDiv = document.createElement('div');
                    letraDiv.className = 'letra';
                    letraDiv.setAttribute('data-letra', letra);
                    
                    letraDiv.innerHTML = `
                        <div class="postit"></div>
                        <div class="letra-texto"></div>
                    `;
                    
                    palabraDiv.appendChild(letraDiv);
                }
                
                fraseContainer.appendChild(palabraDiv);
                
                // Agregar espacio entre palabras (excepto después de la última)
                if (palabras.indexOf(palabra) < palabras.length - 1) {
                    const espacio = document.createElement('div');
                    espacio.className = 'espacio';
                    espacio.style.width = '15px';
                    espacio.style.height = '2.5rem';
                    fraseContainer.appendChild(espacio);
                }
            });

            // Revelar letras que ya fueron adivinadas
            letrasUsadas.forEach(letra => revelarLetra(letra));
            ajustarTamañoPanel();
        }

        function ajustarTamañoPanel() {
            const fraseContainer = document.getElementById('frase-container');
            const panelContainer = document.querySelector('.panel-container');
            
            if (fraseContainer && panelContainer) {
                // Calcular tamaño necesario basado en el contenido
                const contenidoHeight = fraseContainer.scrollHeight;
                const maxHeight = window.innerHeight * 0.3;
                
                // Ajustar altura del panel
                panelContainer.style.height = 'auto';
                panelContainer.style.minHeight = Math.min(contenidoHeight, maxHeight) + 'px';
            }
        }

        function actualizarAbecedario() {
            const keys = document.querySelectorAll('.key-container');
            keys.forEach(key => {
                const letra = key.getAttribute('data-letter');
                
                key.classList.remove('vocal-active', 'consonante-active', 'disabled');
                
                if (letrasUsadas.includes(letra)) {
                    key.classList.add('disabled');
                } 
                else if (opcionRuletaActual === 'VOCAL' && 'AEIOU'.includes(letra)) {
                    key.classList.add('vocal-active');
                } 
                else if (opcionRuletaActual === 'CONSONANTE' && !'AEIOU'.includes(letra)) {
                    key.classList.add('consonante-active');
                }
            });
        }

        function actualizarPuntuacion(puntos) {
            puntuacion = Math.max(0, puntuacion + puntos);
            document.getElementById('puntos-actuales').textContent = puntuacion;
        }

        function verificarFraseCompleta() {
            const letrasReveladas = document.querySelectorAll('.letra.revelada').length;
            const totalLetras = document.querySelectorAll('.letra').length;
            
            if (letrasReveladas === totalLetras && totalLetras > 0) {
                mostrarVictoria();
            }
        }

        // ===== SISTEMA DE ADIVINANZA =====
        function openGuessModal() {
            if (!juegoActivo) {
                mostrarMensaje('El juego ha terminado', 'error');
                return;
            }
            document.getElementById('guessModal').style.display = 'flex';
            document.getElementById('guessInput').focus();
            document.getElementById('guessInput').value = '';
        }

        function closeGuessModal() {
            document.getElementById('guessModal').style.display = 'none';
        }

        function confirmGuess() {
            const guess = document.getElementById('guessInput').value.trim().toUpperCase();
            if (!guess) {
                mostrarMensaje('Por favor escribe una frase', 'warning');
                return;
            }

            closeGuessModal();

            const fraseCorrecta = fraseActual.toUpperCase();
            if (guess === fraseCorrecta) {
                // Revelar todas las letras
                document.querySelectorAll('.letra').forEach(letra => {
                    letra.classList.add('revelada');
                    const letraOculta = letra.getAttribute('data-letra');
                    letra.querySelector('.letra-texto').textContent = letraOculta;
                });
                mostrarVictoria();
            } else {
                mostrarMensaje('❌ ¡Frase incorrecta! GAME OVER', 'error');
                finDelJuego();
            }
        }

        // ===== FIN DEL JUEGO =====
        function mostrarVictoria() {
            juegoActivo = false;
            clearInterval(temporizadorInterval);
            actualizarPuntuacion(100);
            
            mostrarMensaje('🎉 ¡FELICIDADES! Has completado la frase +100 puntos', 'success');
            
            // Deshabilitar controles
            btnGirar.disabled = true;
            document.querySelectorAll('.key-container').forEach(btn => {
                btn.style.pointerEvents = 'none';
            });
            document.getElementById('btnAdivinar').disabled = true;
            
            // Aplicar clase de juego inactivo a todo el layout
            document.querySelector('.layout-container').classList.add('juego-inactivo');
        }

        function finDelJuego() {
            juegoActivo = false;
            clearInterval(temporizadorInterval);
            
            mostrarMensaje('⏰ ¡GAME OVER! Tiempo agotado', 'error');
            
            // Deshabilitar controles
            btnGirar.disabled = true;
            document.querySelectorAll('.key-container').forEach(btn => {
                btn.style.pointerEvents = 'none';
            });
            document.getElementById('btnAdivinar').disabled = true;
            
            // Aplicar clase de juego inactivo a todo el layout
            document.querySelector('.layout-container').classList.add('juego-inactivo');
        }

        // ===== UTILIDADES =====
        function mostrarMensaje(mensaje, tipo) {
            const resultDisplay = document.getElementById('result-display');
            resultDisplay.textContent = mensaje;
            resultDisplay.style.display = 'block';
            
            // Colores según el tipo
            const colores = {
                'success': '#4CAF50',
                'error': '#f44336', 
                'warning': '#ff9800',
                'info': '#2196F3'
            };
            
            resultDisplay.style.backgroundColor = colores[tipo] || '#333';
            resultDisplay.style.borderLeftColor = colores[tipo] || '#ffd700';

            setTimeout(() => {
                resultDisplay.style.display = 'none';
            }, 3000);
        }

        function resetGame() {
            if (confirm('¿Estás seguro de que quieres reiniciar el juego?')) {
                window.location.href = '{{ route("panel.reset") }}';
            }
        }

        function logout() {
            if (confirm('¿Estás seguro de que quieres salir?')) {
                window.location.href = '{{ route("player.logout") }}';
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

        // Ajustar panel cuando cambie el tamaño de la ventana
        window.addEventListener('resize', ajustarTamañoPanel);

        // Observar cambios en las letras después de que se cargue el DOM
        setTimeout(() => {
            document.querySelectorAll('.letra').forEach(letra => {
                observer.observe(letra, { attributes: true, attributeFilter: ['class'] });
            });
        }, 1000);
    </script>
</body>
</html>