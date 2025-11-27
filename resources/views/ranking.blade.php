<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - Wheel Fire Club</title>
    <link rel="stylesheet" href="{{asset('css/ranking.css')}}">
</head>
<body>
    <div class="scene-container">
        <!-- Fondo del mundo real -->
        <div id="mundo-real" class="world">
            <!-- Título del ranking -->
            <div class="centered-title">
                <span class="stranger-things-text">RANKING</span>
            </div>

            <!-- Contenedor de la tabla de ranking -->
            <div class="ranking-container">
                @if($topPlayers->count() > 0)
                <div class="ranking-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Posición</th>
                                <th>Usuario</th>
                                <th>Puntuación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topPlayers as $index => $user)
                            @if($index >= 5)
                                @break
                            @endif                           
                            <tr>
                                <td>
                                    <strong>#{{ $index + 1 }}</strong>
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    <span class="score-badge">
                                        {{ $user->score }} pts
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="no-ranking">
                    No hay usuarios en el ranking.
                </div>
                @endif
            </div>

            <!-- Botón para volver al inicio -->
            <div class="centered-button" id="volver-button">
                <span class="stranger-things-text">VOLVER AL INICIO</span>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('volver-button').addEventListener('click', function() {
            window.location.href = "{{ url('/') }}";
        });
    </script>
</body>
</html>