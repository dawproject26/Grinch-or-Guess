<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Wheel Fire Club - Login</title>
    <link rel="stylesheet" href="view1.css">
</head>
<body>

<div class="scene-container">

    <div id="mundo-real" class="world">
        <div class="centered-button" id="open-gate-button">
            <span class="stranger-things-text">LOGIN</span>
        </div>
        <div class="centered-button2" id="open-gate-button">
            <span class="stranger-things-text">REGISTER</span>
        </div>
    </div>
        <div id="upside-down" class="world">
        <form action="{{ route('player.register') }}" method="POST">
            @csrf
            <input id="cuadrotexto" type="text" name="name" placeholder="Nombre" required>
            <button id="botonjugar" type="submit">Jugar</button>
        </form>
        </div>

        <div id="crack"></div>
</div>

<script>
    document.getElementById('open-gate-button').addEventListener('click', function(){
        document.querySelector('.scene-container').classList.add('open');
    })
</script>
</body>
</html>
