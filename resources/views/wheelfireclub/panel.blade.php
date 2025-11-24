<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista de Prueba</title>
</head>
<body>

<h1>Vista de prueba cargada correctamente</h1>

<p>Si ves esta página, las rutas y el middleware funcionan.</p>

<p>Jugador en sesión: 
    @if(session()->has('player_id'))
        {{ session('player_id') }}
    @else
        <strong>No hay jugador en sesión</strong>
    @endif
</p>

<a href="{{ url('/') }}">Volver al inicio</a>

</body>
</html>
