<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Wheel Fire Club - Login</title>
    <link rel="stylesheet" href="{{asset('css/view1.css')}}"> 
</head>
<body>

<div class="scene-container">

    <audio loop muted id="background-music" preload="auto">
        <source src="{{ asset('audio/music.mp3') }}" type="audio/mpeg">
        <source src="{{ asset('audio/music.ogg') }}" type="audio/ogg">
    </audio>

    <div id="mundo-real" class="world">
        @if(session('error'))
            <div class="error">{{session('error')}}</div>
        @endif 

        <div class="centered-title"> 
        <span class="stranger-things-text title-line">WHEELFIRE</span>
        <span class="stranger-things-text title-line">CLUB</span>
        </div>
        <div class="centered-button" id="login-button">
            <span class="stranger-things-text">INICIAR SESIÓN</span>
        </div>
        <div class="ranking-top-right" id="ranking-button"> 
            <span class="stranger-things-text">RANKING</span>
        </div>
        <div class="centered-button2" id="register-button"> 
            <span class="stranger-things-text">REGISTRARSE</span>
        </div>
    </div>
    
    <div id="upside-down" class="world">
        <div class="avatar-selector">
            <div class="avatar-display" id="avatar-display">
                <img src="{{ asset('img/eleven.png') }}" alt="Avatar 1" data-avatar-id="1">
                <img src="{{ asset('img/mike.png') }}" alt="Avatar 2" data-avatar-id="2">
                <img src="{{ asset('img/lucas.png') }}" alt="Avatar 3" data-avatar-id="3">
                <img src="{{ asset('img/dustin.png') }}" alt="Avatar 4" data-avatar-id="4">
                <img src="{{ asset('img/will.png') }}" alt="Avatar 5" data-avatar-id="5">
            </div>
        </div>

        <div id="login-form-wrapper">
            <form action="{{ route('player.login') }}" method="POST">
                @csrf
                <input id="cuadrotexto_login" type="text" name="name" placeholder="Nombre" required>
                <button id="botonjugar_login" class="stranger-things-text" type="submit">JUGAR</button>
            </form>  
        </div>

        <div id="register-form-wrapper" style="display: none;">
            <form action="{{ route('player.register') }}" method="POST">
                @csrf
                <input id="cuadrotexto_register" type="text" name="name" placeholder="Nombre" required>
                <button id="botonjugar_register" class="stranger-things-text" type="submit">JUGAR</button>
                <input type="hidden" name="idavatar" id="idavatar" value="1">
            </form> 
        </div> 
        <div id="demogorgon-wrapper">
        <img src="{{ asset('img/demogorgon-boton.png') }}" id="demogorgon-image" alt="Demogorgon abrazando botón">
        </div>
    </div>

    <div id="crack"></div>

</div>

<script>
    // --- Lógica de Apertura del Portal y Visibilidad de Formularios ---
    const loginWrapper = document.getElementById('login-form-wrapper');
    const registerWrapper = document.getElementById('register-form-wrapper');
    const backgroundMusic = document.getElementById('background-music');

    document.getElementById('login-button').addEventListener('click', function(){
        document.querySelector('.scene-container').classList.add('open');
        // Mostrar Login y ocultar Registro
        loginWrapper.style.display = 'flex'; 
        registerWrapper.style.display = 'none';
    });

    document.getElementById('register-button').addEventListener('click', function(){
        document.querySelector('.scene-container').classList.add('open');
        // Mostrar Registro y ocultar Login
        loginWrapper.style.display = 'none';
        registerWrapper.style.display = 'flex';
    });
     document.getElementById('ranking-button').addEventListener('click', function() {
            window.location.href = '/ranking';
    });

    function enableAudioHandler() {
        if (backgroundMusic) {
            // 1. Quitar el silencio y ajustar volumen
            backgroundMusic.volume = 0.5; // Ajusta el volumen a tu gusto
            backgroundMusic.muted = false; 

            // 2. Intentar reproducir el audio
            backgroundMusic.play()
                .then(() => {
                    console.log("Música iniciada con el primer clic.");
                })
                .catch(error => {
                    console.error("Error al iniciar audio:", error);
                });
            
            // 3. Eliminar este listener para que el código solo se ejecute una vez
            document.removeEventListener('click', enableAudioHandler);
        }
    }

    // 4. Adjuntar el evento de escucha a todo el documento
    document.addEventListener('click', enableAudioHandler);
    // --- Lógica del Avatar ---
    const avatarDisplay = document.getElementById('avatar-display');
    const avatarImages = avatarDisplay.querySelectorAll('img');
    const idAvatarInput = document.getElementById('idavatar');
    let currentAvatarIndex = 0; 

    function updateAvatarDisplay() {
        const offset = -currentAvatarIndex * 230; 
        avatarDisplay.style.transform = `translateY(${offset}px)`;

        const selectedId = avatarImages[currentAvatarIndex].getAttribute('data-avatar-id');
        
        if(idAvatarInput) {
            idAvatarInput.value = selectedId;
        }
    }

    document.querySelector('.avatar-selector').addEventListener('click', function() {
        currentAvatarIndex = (currentAvatarIndex + 1) % avatarImages.length; 
        updateAvatarDisplay();
    });

    updateAvatarDisplay();

</script>
</body>
</html>