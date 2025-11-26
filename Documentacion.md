PANEL DEL ABECEDARIO Y SU LÓGICA

index.blade.php

<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/ffea737b-2a7d-4c53-9453-aa116c474dcb" />

1. Encabezado HTML y Configuración

<img width="578" height="176" alt="image" src="https://github.com/user-attachments/assets/f1f32327-8f52-4fce-95ec-1fdae7183412" />

Propósito:

        ◦ Define el documento como HTML5 en español
        ◦ Configura la codificación de caracteres y la vista para dispositivos móviles
        ◦ Establece el título de la página
        ◦ Incluye jQuery para simplificar las operaciones complejas como el manejo de eventos (click) y las peticiones AJAX al servidor. 

2. Estilos CSS
   
	Estilos Principales:
            ▪ Body: Elimina márgenes predeterminados y ocupa toda la altura de la ventana
            ▪ #alphabet-sidebar:
            ▪ Contenedor fijo en el lado izquierdo de la pantalla
            ▪ Centrado verticalmente usando transform: translateY(-50%)
            ▪ Usa CSS Grid para organizar las letras en 4 columnas
            ▪ .key-container:
            ▪ Contenedores cuadrados de 60x60px para cada letra
            ▪ Efectos de hover y estados deshabilitados
            ▪ .key-image: Imágenes que ocupan todo el espacio del contenedor
            ▪ #result-display: Área en la esquina superior derecha para mostrar resultados

4. Estructura HTML con Blade
   
	Generación Dinámica del Abecedario:

<img width="552" height="128" alt="image" src="https://github.com/user-attachments/assets/bb2aef2e-b0a0-4729-a4ab-5a5347dae4b2" />


Características Especiales:
            ▪ La letra 'Y' tiene un tratamiento especial, usando 'igriega' como nombre de archivo
            ▪ Cada letra tiene dos imágenes asociadas:
            ▪ Imagen normal: img/letras/[letra].png
            ▪ Imagen tachada: img/letras_tachadas/[letra]_tachada.png
4. Lógica JavaScript/jQuery
		Configuración de Seguridad:

    <img width="471" height="132" alt="image" src="https://github.com/user-attachments/assets/24182776-67f0-4160-b014-702d6939eb55" />

Importancia: Protege contra ataques CSRF (Cross-Site Request Forgery) 						requerido por Laravel.
		Funcionalidad Principal:
                    1. Evento Click: Detecta clics en los contenedores de letras, dentro de la barra lateral. 
                    2. Validación: Verifica si la letra ya fue seleccionada (clase 'disabled')
                    3. Cambio Visual:
                    ◦ Añade clase 'disabled' al contenedor
                    ◦ Cambia la imagen a la versión tachada
			4. Comunicación con Servidor:
            
                • Envía la letra seleccionada via AJAX POST a la ruta panel.check
                • Maneja respuestas exitosas y errores
                
5. Flujo de Interacción del Usuario
   
            1. Selección de Letra: Usuario hace clic en cualquier letra del panel lateral
            2. Feedback Visual Inmediato:
                    ▪ La letra se marca como usada (opacidad reducida)
                    ▪ La imagen cambia a versión tachada
                    ▪ El cursor cambia a "no permitido"
            3.	Procesamiento en Servidor:
                    ▪ La letra se envía al backend para determinar si es vocal o consonante
            4.	Resultado:
                ▪ Se muestra el resultado en el área de mensajes
                ▪ En caso de error, se muestra mensaje de error
7. Dependencias y Requisitos
8. 
	Archivos de Imágenes Requeridos:

<img width="476" height="168" alt="image" src="https://github.com/user-attachments/assets/913f63c9-c9eb-4fa9-9db7-a14c9079df7e" />

Rutas Laravel Necesarias:

		route('panel.check'): Ruta POST que procesa la validación de letras
        
7. Consideraciones de Diseño Responsive
   
        ◦ Posicionamiento Fijo: El panel de letras permanece visible mientras se desplaza
        ◦ Centrado Vertical: Se adapta a diferentes alturas de pantalla
        ◦ Tamaños Fijos: Las teclas mantienen dimensiones consistentes

panelController.php

Estructura del Controlador
1. Namespace e Imports









   
