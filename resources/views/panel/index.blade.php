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
        
        <div class="header">
            <h1 class="titulo-panel" id="movie-title">{{ $title ?? 'BOJACK HORSEMAN' }}</h1>
        </div>

        <div class="alphabet-panel">
            <div id="alphabet-sidebar">
                @php $alphabet = range('A', 'Z'); @endphp
                @foreach ($alphabet as $letter)
                    @php
                        $baseFileName = strtolower($letter);
                        if ($letter === 'Y') $baseFileName = 'igriega';
                    @endphp
                    <div class="key-container" data-letter="{{ $letter }}" onclick="seleccionarLetra('{{ $letter }}')">
                        <img id="img-{{ $letter }}"
                             class="key-image"
                             src="{{ asset('img/letras/' . $baseFileName . '.png') }}"
                             alt="{{ $letter }}"
                             onerror="this.style.display='none'; this.parentElement.innerText='{{ $letter }}'">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="center-panel">
            
            <div class="board-section">
                <div id="frase-container">
                    </div>
            </div>

            <div class="play-zone">
                
                <div class="player-block">
                    @php
                        $avatars = [1 => 'img/eleven.png', 2 => 'img/mike.png', 3 => 'img/lucas.png', 4 => 'img/dustin.png', 5 => 'img/will.png'];
                        $avatarImg = $avatars[session('idavatar', 1)] ?? 'img/default.png';
                    @endphp
                    <div class="avatar-circle">
                        <img src="{{ asset($avatarImg) }}" alt="Avatar">
                    </div>
                    <div class="stat-box">
                        <span style="font-size:0.8rem; color:#aaa;">JUGADOR</span>
                        <span style="display:block;">{{ session('player_name', 'Player 1') }}</span>
                    </div>
                    <div class="stat-box">
                        <span style="font-size:0.8rem; color:#aaa;">PUNTOS</span>
                        <span class="stat-value" id="puntos-actuales">0</span>
                    </div>
                </div>

                <div class="wheel-wrapper">
                    <div class="marco-ruleta">
                        <div id="flecha">⬆</div>
                        <div id="ruleta">
                            <img src="{{ asset('img/ruleta.png') }}" alt="Ruleta">
                        </div>
                    </div>
                    <div style="text-align:center; width:100%;">
                        <p id="mensaje-ruleta"></p>
                        <button id="btnGirar">GIRAR RULETA</button>
                    </div>
                </div>

            </div>
        </div>

        <div class="right-panel">
            <div class="timer-box">
                <span class="timer-label">TIEMPO RESTANTE</span>
                <div id="temporizador">03:00</div>
            </div>

            <div class="controls-box">
                <button id="btnAdivinar" class="action-btn btn-guess">⚡ ADIVINAR FRASE</button>
                <button onclick="resetGame()" class="action-btn btn-reset">🔄 REINICIAR</button>
                <button onclick="logout()" class="action-btn btn-exit">🚪 SALIR</button>
            </div>
        </div>

    </div>

    <div id="guessModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.95); z-index:3000; align-items:center; justify-content:center;">
        <div style="background:#111; padding:40px; border:4px solid #8b0000; text-align:center; max-width:600px; width:90%; border-radius:15px; box-shadow:0 0 50px #8b0000;">
            <h2 style="color:#f00; margin-bottom:10px; font-size:2rem;">¿ADIVINAR FRASE?</h2>
            <p style="color:#aaa; margin-bottom:20px;">⚠️ Si fallas, pierdes la partida inmediatamente.</p>
            <input type="text" id="guessInput" style="width:100%; padding:20px; font-size:1.5rem; background:#222; color:#fff; border:2px solid #555; margin-bottom:20px; text-transform:uppercase; text-align:center;" placeholder="ESCRIBE LA FRASE AQUÍ...">
            <div style="display:flex; gap:15px; justify-content:center;">
                <button onclick="confirmGuess()" style="padding:15px 40px; background:#28a745; color:#fff; border:none; font-weight:bold; cursor:pointer; font-size:1.1rem;">CONFIRMAR</button>
                <button onclick="closeGuessModal()" style="padding:15px 40px; background:#dc3545; color:#fff; border:none; font-weight:bold; cursor:pointer; font-size:1.1rem;">CANCELAR</button>
            </div>
        </div>
    </div>

    <div id="result-display"></div>

    <script>
        // ===== 1. VARIABLES GLOBALES =====
        let tiempoRestante = 180;
        let temporizadorInterval;
        let letrasUsadas = [];
        let opcionRuletaActual = null;
        let anguloActual = 0; // Para movimiento acumulativo
        let estaGirando = false;
        let juegoActivo = true;
        let puntuacion = 0;

        // Datos desde Laravel
        const fraseActual = "{{ Session::get('frase_actual', 'BOJACK HORSEMAN') }}";
        
        // Elementos DOM
        const ruleta = document.getElementById('ruleta');
        const btnGirar = document.getElementById('btnGirar');
        const displayResultado = document.getElementById('mensaje-ruleta');
        const temporizadorHTML = document.getElementById('temporizador');

        // Configuración Ruleta (Ajustar orden según tu imagen)
        const TOTAL_SECTORES = 8;
        const GRADOS_POR_SECTOR = 360 / TOTAL_SECTORES;
        const opcionesPorSector = [
            "Demogorgon", "Consonante", "Eleven", "Vocal", 
            "Vecna", "Consonante", "Demoperro", "Vocal"
        ];

        // ===== 2. INICIALIZACIÓN =====
        document.addEventListener('DOMContentLoaded', () => {
            actualizarFraseDisplay();
            iniciarTemporizador();
            
            btnGirar.addEventListener('click', girarRuleta);
            document.getElementById('btnAdivinar').addEventListener('click', openGuessModal);
            
            // IMPORTANTE: Detecta fin de transición CSS
            ruleta.addEventListener('transitionend', finalizarGiro);
        });

        // ===== 3. LÓGICA DE GIRO (ORIGINAL RESTAURADA) =====
        function girarRuleta() {
            if (estaGirando || !juegoActivo) return;

            estaGirando = true;
            btnGirar.disabled = true;
            displayResultado.textContent = "GIRANDO...";
            displayResultado.style.color = "#fff";

            // Cálculo acumulativo para evitar "rebobinado"
            const vueltasMinimas = 5;
            const gradosAleatorios = Math.floor(Math.random() * 360);
            const giroTotal = (vueltasMinimas * 360) + gradosAleatorios;
            
            anguloActual += giroTotal;
            ruleta.style.transform = `rotate(${anguloActual}deg)`;
        }

        function finalizarGiro() {
            estaGirando = false;
            btnGirar.disabled = false;

            // Calcular sector ganador
            const gradosNorm = anguloActual % 360;
            // Flecha abajo = 180 deg offset
            const posicionFlecha = 180; 
            let gradosResult = (360 - gradosNorm + posicionFlecha) % 360;
            const indice = Math.floor(gradosResult / GRADOS_POR_SECTOR);
            const opcion = opcionesPorSector[indice >= 0 ? indice : 0];

            procesarResultado(opcion);
        }

        function procesarResultado(opcion) {
            opcionRuletaActual = null;
            let msg = "";
            let color = "#fff";

            if (opcion === 'Vocal') {
                opcionRuletaActual = 'VOCAL';
                msg = "🗣 VOCAL";
                color = "#ffd700"; // Oro
            } else if (opcion === 'Consonante') {
                opcionRuletaActual = 'CONSONANTE';
                msg = "🔤 CONSONANTE";
                color = "#00ffff"; // Cyan
            } else if (opcion === 'Eleven') {
                modificarTiempo(20);
                msg = "🧇 ELEVEN (+20s)";
                color = "#4CAF50"; // Verde
                mostrarMensajeFlotante("🧇 ¡ELEVEN TE AYUDA!");
            } else if (opcion === 'Vecna') {
                // CORRECCIÓN ESPECÍFICA PARA VECNA
                modificarTiempo(-20);
                msg = "🕰 VECNA (-20s)";
                color = "#ff0000"; // Rojo
                mostrarMensajeFlotante("👹 ¡VECNA TE ENCONTRÓ!");
            } else if (['Demogorgon', 'Demoperro'].includes(opcion)) {
                const castigo = (opcion === 'Demogorgon') ? -10 : -5;
                modificarTiempo(castigo);
                msg = `👹 MONSTRUO (${castigo}s)`;
                color = "#ff4444";
                mostrarMensajeFlotante(`👹 ¡${opcion.toUpperCase()}!`);
            }

            displayResultado.textContent = msg;
            displayResultado.style.color = color;
            
            actualizarTeclado();
        }

        // ===== 4. LÓGICA DE JUEGO (LETRAS) =====
        function seleccionarLetra(letra) {
            if (!juegoActivo) return;

            // Validaciones
            if (letrasUsadas.includes(letra)) return;
            if (!opcionRuletaActual) {
                mostrarMensajeFlotante("🎡 GIRA PRIMERO");
                return;
            }

            const esVocal = "AEIOU".includes(letra);
            if (opcionRuletaActual === 'VOCAL' && !esVocal) {
                mostrarMensajeFlotante("❌ SOLO VOCALES");
                return;
            }
            if (opcionRuletaActual === 'CONSONANTE' && esVocal) {
                mostrarMensajeFlotante("❌ SOLO CONSONANTES");
                return;
            }

            // Ejecutar jugada
            letrasUsadas.push(letra);
            
            // Deshabilitar tecla visualmente
            const tecla = document.querySelector(`.key-container[data-letter="${letra}"]`);
            if(tecla) tecla.classList.add('disabled');

            if (fraseActual.includes(letra)) {
                revelarLetra(letra);
                puntuacion += 10;
                document.getElementById('puntos-actuales').innerText = puntuacion;
                mostrarMensajeFlotante("✅ CORRECTO");
                verificarVictoria();
            } else {
                mostrarMensajeFlotante("❌ FALLO");
                modificarTiempo(-5);
            }

            // Reset turno
            opcionRuletaActual = null;
            displayResultado.textContent = "";
            actualizarTeclado();
        }

        function actualizarTeclado() {
            document.querySelectorAll('.key-container').forEach(key => {
                // Quitar animaciones previas
                key.classList.remove('vocal-active', 'consonante-active');
                
                if (!key.classList.contains('disabled')) {
                    const l = key.dataset.letter;
                    const esVocal = "AEIOU".includes(l);

                    // Aplicar animación fluida si corresponde
                    if (opcionRuletaActual === 'VOCAL' && esVocal) {
                        key.classList.add('vocal-active');
                    } else if (opcionRuletaActual === 'CONSONANTE' && !esVocal) {
                        key.classList.add('consonante-active');
                    }
                }
            });
        }

        // ===== 5. UTILIDADES Y RENDER =====
        function actualizarFraseDisplay() {
            const container = document.getElementById('frase-container');
            container.innerHTML = '';
            const palabras = fraseActual.toUpperCase().split(' ');

            palabras.forEach(palabra => {
                const divPalabra = document.createElement('div');
                divPalabra.className = 'palabra';
                
                for (let letra of palabra) {
                    if (!/[A-Z]/.test(letra)) continue;
                    const divLetra = document.createElement('div');
                    divLetra.className = 'letra';
                    divLetra.dataset.letra = letra;
                    divLetra.innerHTML = `<span class="letra-texto">${letra}</span>`;
                    // Rotación sutil aleatoria
                    divLetra.style.transform = `rotate(${Math.random() * 4 - 2}deg)`;
                    divPalabra.appendChild(divLetra);
                }
                container.appendChild(divPalabra);
            });
            // Restaurar estado si recarga página
            letrasUsadas.forEach(l => revelarLetra(l));
        }

        function revelarLetra(letra) {
            document.querySelectorAll(`.letra[data-letra="${letra}"]`).forEach(el => {
                el.classList.add('revelada');
                // Quitar rotación al revelar para leer mejor
                el.style.transform = 'rotate(0deg) scale(1.05)';
            });
        }

        function verificarVictoria() {
            if (document.querySelectorAll('.letra:not(.revelada)').length === 0) {
                terminarJuego(true);
            }
        }

        // ===== 6. TEMPORIZADOR Y MODALES =====
        function iniciarTemporizador() {
            actualizarTimerUI();
            temporizadorInterval = setInterval(() => {
                if (juegoActivo && tiempoRestante > 0) {
                    tiempoRestante--;
                    actualizarTimerUI();
                } else if (tiempoRestante <= 0) {
                    terminarJuego(false);
                }
            }, 1000);
        }

        function actualizarTimerUI() {
            const m = Math.floor(tiempoRestante / 60).toString().padStart(2,'0');
            const s = (tiempoRestante % 60).toString().padStart(2,'0');
            temporizadorHTML.innerText = `${m}:${s}`;
            
            if (tiempoRestante < 30) temporizadorHTML.classList.add('danger');
            else temporizadorHTML.classList.remove('danger');
        }

        function modificarTiempo(segundos) {
            tiempoRestante += segundos;
            if (tiempoRestante < 0) tiempoRestante = 0;
            actualizarTimerUI();
        }

        function mostrarMensajeFlotante(texto) {
            const el = document.getElementById('result-display');
            el.innerHTML = texto;
            el.style.display = 'block';
            setTimeout(() => el.style.display = 'none', 1500);
        }

        function openGuessModal() { document.getElementById('guessModal').style.display = 'flex'; }
        function closeGuessModal() { document.getElementById('guessModal').style.display = 'none'; }

        function confirmGuess() {
            const input = document.getElementById('guessInput');
            if (input.value.toUpperCase().trim() === fraseActual) {
                // Revelar todo
                document.querySelectorAll('.letra').forEach(el => el.classList.add('revelada'));
                terminarJuego(true);
            } else {
                mostrarMensajeFlotante("☠️ INCORRECTO");
                setTimeout(() => terminarJuego(false), 1000);
            }
            closeGuessModal();
        }

        function terminarJuego(victoria) {
            juegoActivo = false;
            clearInterval(temporizadorInterval);
            const msg = victoria ? "🎉 ¡VICTORIA!" : "💀 GAME OVER";
            const color = victoria ? "#28a745" : "#dc3545";
            
            const el = document.getElementById('result-display');
            el.innerHTML = `<h1 style="color:${color}; font-size:3rem;">${msg}</h1>`;
            el.style.display = 'block';
            
            // Bloquear interfaz
            document.querySelector('.layout-container').style.filter = "grayscale(1) brightness(0.5)";
            document.querySelector('.layout-container').style.pointerEvents = "none";
        }

        function resetGame() { if(confirm("¿Reiniciar?")) window.location.href = "{{ route('panel.reset') }}"; }
        function logout() { window.location.href = "{{ route('player.logout') }}"; }
    </script>
</body>
</html>