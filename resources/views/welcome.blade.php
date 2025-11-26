<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Wheel Fire Club - Login</title>
    <link rel="stylesheet" href="{{asset('css/view1.css')}}">
</head>
<body>

<div class="scene-container">

    <div id="mundo-real" class="world">
        @if(session('error'))
            <div class="error">{{session('error')}}</div>
        @endif 

        <div class="centered-title"> 
            <span class="stranger-things-text">WHEELFIRE CLUB</span>
        </div>
        <div class="centered-button" id="login-button">
            <span class="stranger-things-text">INICIAR SESIÓN</span>
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
            <input type="hidden" name="selected_avatar" id="selected-avatar-input" value="avatar1.png">
        </div>

        <form action="{{ route('player.login') }}" method="POST">
            @csrf
            <input id="cuadrotexto" type="text" name="name" placeholder="Nombre" required>
            <button id="botonjugar" class="stranger-things-text" type="submit">JUGAR</button>
        </form>  

        <form action="{{ route('player.register') }}" method="POST">
            @csrf
            <input id="cuadrotexto" type="text" name="name" placeholder="Nombre" required>
            <button id="botonjugar" class="stranger-things-text" type="submit">JUGAR</button>
            <input type="hidden" name="idavatar" id="idavatar" value="1">
        </form>  
    </div>

    <div id="crack"></div>

</div>

<script>
    // Código para el botón INICIAR SESIÓN (open-gate-button)
    document.getElementById('login-button').addEventListener('click', function(){
        document.querySelector('.scene-container').classList.add('open');
    });

    // Código para el botón REGISTRARSE (register-button)
    document.getElementById('register-button').addEventListener('click', function(){
        document.querySelector('.scene-container').classList.add('open');
    });

    const avatarDisplay = document.getElementById('avatar-display');
    const avatarImages = avatarDisplay.querySelectorAll('img');
    const selectedAvatarInput = document.getElementById('selected-avatar-input');
    let currentAvatarIndex = 0; // El índice del avatar actualmente visible

    // Función para actualizar la visualización y el input oculto
    function updateAvatarDisplay() {
        const offset = -currentAvatarIndex * 175; // 175px es la altura de cada imagen
        avatarDisplay.style.transform = `translateY(${offset}px)`;

        // Actualizar el valor del input oculto con la ruta del avatar seleccionado
        const selectedImageSrc = avatarImages[currentAvatarIndex].getAttribute('src');
        selectedAvatarInput.value = selectedImageSrc.split('/').pop(); // Solo el nombre del archivo (e.g., "avatar1.png")
    }

    // Event listener para cuando se hace clic en el selector de avatares
    document.querySelector('.avatar-selector').addEventListener('click', function() {
        currentAvatarIndex = (currentAvatarIndex + 1) % avatarImages.length; // Pasa al siguiente avatar (ciclo)
        updateAvatarDisplay();
    });

    // Inicializar el display al cargar la página
    updateAvatarDisplay();
</script>
</body>
</html>

