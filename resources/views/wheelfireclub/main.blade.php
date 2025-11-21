<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>WheelFire Club - Ruleta</title>
<link rel="stylesheet" href="{{ asset('css/main.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<h1 style="text-align:center; margin-top:16px;">WheelFire Club</h1>

<div class="contenedor-juego">
    <div class="marco-ruleta">
        <div id="flecha">⬆</div>

        <div id="ruleta">
        <img src="{{ asset('img/ruleta.png') }}" alt="rulete">
        </div>
    </div>

    <div style="text-align:center; margin-top: 12px;">
        <button id="btnGirar">GIRAR</button>
        <a href="{{ route('ruleta.reset') }}"><button>Reiniciar Ruleta</button></a>
    </div>  

    <p id="resultado" style="text-align:center; margin-top:10px; font-size:1.1rem;">...</p>
    <p id="temporizador" style="text-align:center; font-weight:700; margin-top:6px;">Tiempo restante: 02:00</p>

    <div id="efecto-temporizador" class="efecto-temporizador"></div>
</div>

<script>
/*
  Lógica front: mezcla de código del profe + nuestras reglas.
  - La ruleta decide el índice ganador en el cliente.
  - Luego POST /panel/girar { opcion } para que Laravel actualice DB y sesión.
*/

const ruleta = document.getElementById('ruleta');
const btnGirar = document.getElementById('btnGirar');
const displayResultado = document.getElementById('resultado');
const temporizadorHTML = document.getElementById("temporizador");
const efectoHTML = document.getElementById("efecto-temporizador");
const csrf = document.querySelector('meta[name="csrf-token"]').content;

const TOTAL_SECTORES = 8;
const GRADOS_POR_SECTOR = 360 / TOTAL_SECTORES;
let anguloActual = 0;

// Opciones mapeadas por sector (índices 0..7)
const opcionesPorSector = [
    "Vocal",      // 0 - 🍎
    "Vocal",      // 1 - 🍊  (repetimos Vocal para hacer 2 apariciones)
    "Consonante", // 2 - 🍇
    "Consonante", // 3 - 🍌
    "Demoperro",  // 4 - 🍒
    "Demogorgon", // 5 - 🍋
    "Vecna",      // 6 - 🍉
    "Eleven"      // 7 - 🍍
];

// Efecto por nombre (solo para visual local; servidor decide si aplica)
const efectoMap = {"Vocal":0,"Consonante":0,"Demoperro":-5,"Demogorgon":-10,"Vecna":-20,"Eleven":20};

// Temporizador
let tiempoActual = 120;
let temporizadorInterval = null;

function formatoTiempo(seg){ const m = Math.floor(seg/60); const s = seg%60; return `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`; }
temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;

function iniciarTemporizador(){
    if(!temporizadorInterval){
        temporizadorInterval = setInterval(()=>{
            if(tiempoActual>0){
                tiempoActual--;
                temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
            }
        },1000);
    }
}

// Sincronizar estado inicial desde servidor (por si recargas y BD tiene otro tiempo)
fetch("{{ route('panel.index') }}")
    .then(r => r.json())
    .then(d => {
        if(d.segundos_restantes !== undefined){
            tiempoActual = parseInt(d.segundos_restantes, 10);
            temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
        }
    });

// Girar ruleta (visual)
btnGirar.addEventListener('click', ()=>{
    btnGirar.disabled = true;
    displayResultado.textContent = "Girando...";

    iniciarTemporizador();

    // giro aleatorio (min 5 vueltas + 0..359º)
    const vueltasMinimas = 5;
    const gradosAleatorios = Math.floor(Math.random()*360);
    const giroTotal = (vueltasMinimas*360) + gradosAleatorios;

    anguloActual += giroTotal;
    ruleta.style.transition = "transform 4s cubic-bezier(0.1, 0.7, 0.1, 1)";
    ruleta.style.transform = `rotate(${anguloActual}deg)`;
});

// Cuando termina la transición calculamos el sector ganador y avisamos al servidor
ruleta.addEventListener('transitionend', function handler(){
    // quitamos listener para evitar dobles
    ruleta.removeEventListener('transitionend', handler);

    const gradosNorm = anguloActual % 360;
    const posicionFlecha = 180; // flecha abajo (6 en punto)
    const gradosResult = (360 - gradosNorm + posicionFlecha) % 360;
    const indiceGanador = Math.floor(gradosResult / GRADOS_POR_SECTOR);

    const opcion = opcionesPorSector[indiceGanador] || 'Vocal';
    displayResultado.textContent = `La ruleta ha caído en: ${opcion}`;

    // Mostrar animación local instantánea (antes de la respuesta del servidor)
    const efectoLocal = efectoMap[opcion] || 0;
    if(efectoLocal !== 0){
        efectoHTML.textContent = efectoLocal > 0 ? `¡${efectoLocal} segundos extras!` : `${efectoLocal} segundos`;
        if(efectoLocal > 0) efectoHTML.className = 'efecto-temporizador positivo';
        else {
            if(opcion === 'Vecna') efectoHTML.className = 'efecto-temporizador rojo-intenso';
            else if(opcion === 'Demogorgon') efectoHTML.className = 'efecto-temporizador rojo-medio';
            else efectoHTML.className = 'efecto-temporizador rojo-claro';
        }
        setTimeout(()=> { efectoHTML.textContent=''; efectoHTML.className='efecto-temporizador'; }, 1500);
    }

    // Enviar la opción al servidor para que valide, aplique efecto real en BD y registre el giro
    fetch("{{ route('panel.girar') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ opcion })
    })
    .then(resp => {
        if (!resp.ok) return resp.json().then(e => Promise.reject(e));
        return resp.json();
    })
    .then(json => {
        // Actualizamos tiempo con el valor oficial del servidor
        if(json.segundos_restantes !== undefined){
            tiempoActual = parseInt(json.segundos_restantes, 10);
            temporizadorHTML.textContent = `Tiempo restante: ${formatoTiempo(tiempoActual)}`;
        }
    })
    .catch(err => {
        console.error('Error servido al registrar giro:', err);
        // Si el servidor devuelve que opción no disponible, mostrárselo al usuario
        if(err && err.error) {
            displayResultado.textContent = `Error: ${err.error}`;
        }
    })
    .finally(()=> {
        btnGirar.disabled = false;
    });
});
</script>

</body>
</html>
