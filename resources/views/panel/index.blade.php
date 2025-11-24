<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Juego - Abecedario</title>
    <!-- Incluye jQuery para simplificar el manejo de AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* ---------------------------------- */
        /* ESTILOS CSS PARA POSICIONAMIENTO Y DISEÑO */
        /* ---------------------------------- */
        body {
            /* Asegura que no haya márgenes predeterminados del body */
            margin: 0;
            padding: 0;
            height: 100vh;
            
        }

        /* Contenedor principal del abecedario */
        #alphabet-sidebar {
            /* Fija el contenedor a la ventana del navegador */
            position: fixed;
            left: 10px; /* Pegado o cerca del borde izquierdo */
            top: 50%; /* Mueve el borde superior al centro vertical */
            transform: translateY(-50%); /* Centra verticalmente el elemento */
            padding: 10px;
            /* Usamos CSS Grid para la estructura 4xN */
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Crea 4 columnas de igual ancho */
            gap: 10px; /* Espacio entre las teclas */
            background: rgba(255, 255, 255, 0.05); /* Opcional: fondo semitransparente */
            border-radius: 10px;
        }

        /* Estilos para cada tecla (DIV) */
        .key-container {
            width: 60px; /* Ancho fijo para las imágenes de la tecla */
            height: 60px; /* Alto fijo */
            cursor: pointer;
            transition: opacity 0.2s; /* Pequeña animación al hacer hover */
        }
        .key-container:hover:not(.disabled) {
            opacity: 0.8;
        }
        .key-container.disabled {
            cursor: not-allowed;
            opacity: 0.5; /* Opacidad reducida si ya se usó la tecla */
        }

        /* Estilo para la imagen dentro del DIV */
        .key-image {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Estilo para el área de mensajes de resultado */
        #result-display {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px;
            background-color: #333;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <!-- Contenedor del Abecedario -->
    <div id="alphabet-sidebar">
        @php
        // 1. Definimos las letras del abecedario (sin Ñ).
            $alphabet = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
            ];
        @endphp
 
        @foreach ($alphabet as $letter)
            @php
                // 2. Definimos el nombre base de la imagen. 
                // Por defecto, es la letra en minúscula (ej: 'a', 'b', 'c').
                $baseFileName = strtolower($letter);
                // 3. ¡REGLA ESPECIAL PARA LA 'Y'!
                // Si la letra es 'Y', usamos 'igriega' en lugar de 'y'.
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

    <!-- ---------------------------------------------------- -->
    <!-- LÓGICA JAVASCRIPT/AJAX PARA EL CLIC Y EL CAMBIO DE IMAGEN -->
    <!-- ---------------------------------------------------- -->
    <script>
        // 🚨 PASO CRÍTICO: Configuración de seguridad para peticiones POST/AJAX
        // Laravel necesita este token para permitir la ruta POST '/panel/check'
        $.ajaxSetup({
            headers: {
                // Obtenemos el token que Laravel genera en el campo oculto
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $(document).ready(function() {
            // Se escucha el evento click en cualquier contenedor de tecla
            $('#alphabet-sidebar').on('click', '.key-container', function() {
                const container = $(this); // El DIV de la tecla
                const letter = container.data('letter'); // La letra (e.g., 'A')
                const imageElement = $(`#img-${letter}`); // La etiqueta IMG dentro del DIV
                
                // Si la tecla ya tiene la clase 'disabled', no hacemos nada (ya fue clicada)
                if (container.hasClass('disabled')) {
                    return; 
                }

                // 1. Marcar la tecla como deshabilitada y visualmente usada
                container.addClass('disabled'); 

                // 2. CAMBIO DE IMAGEN: Obtenemos la URL de la imagen tachada
                const tachedSrc = imageElement.data('tached-src');
                imageElement.attr('src', tachedSrc); // Cambia la fuente de la imagen

                // 3. Petición AJAX al controlador
                $.ajax({
                    // Usamos la ruta nombrada 'panel.check' definida en web.php
                    url: '{{ route('panel.check') }}',
                    type: 'POST',
                    data: {
                        letter: letter // Enviamos la letra seleccionada
                    },
                    success: function(response) {
                        // 4. Manejo de la respuesta exitosa (mostrar si es Vocal/Consonante)
                        const messageHtml = `<p>Has seleccionado la letra <strong>${response.letter}</strong>. Es una <strong>${response.type}</strong>.</p>`;
                        $('#result-display').html(messageHtml);
                    },
                    error: function(xhr) {
                        // Manejo de errores de servidor o validación
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Error desconocido al procesar la jugada.";
                        $('#result-display').html(`<p style="color: red;">¡Error! ${errorMsg}</p>`);
                        
                      
                    }
                });
            });
        });
    </script>
</body>
</html>