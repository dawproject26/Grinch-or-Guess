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
        <div class="centered-title"> 
            <span class="stranger-things-text">¿ERES NUEVO?</span>
        </div>
        <div class="centered-button" id="login-button">
            <span class="stranger-things-text">INICIAR SESIÓN</span>
        </div>
        <div class="centered-button2" id="register-button"> 
            <span class="stranger-things-text">REGISTRARSE</span>
        </div>
    </div>
        <div id="upside-down" class="world">
        <form action="{{ route('player.register') }}" method="POST">
            @csrf
            <input id="cuadrotexto" type="text" name="name" placeholder="Nombre" required>
            <button id="botonjugar" class="stranger-things-text" type="submit">JUGAR</button>
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
</script>
</body>
</html>
