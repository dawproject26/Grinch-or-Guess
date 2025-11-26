# Documentación del PlayerController

## Descripción General

Este controlador gestiona todo lo relacionado con los jugadores en la aplicación: registro, inicio de sesión y cierre de sesión. Es el punto de entrada principal para que los usuarios accedan al sistema.

---

## Estructura del Archivo

**Namespace:** `App\Http\Controllers`

**Modelos utilizados:**
- `Player` - Representa a los jugadores
- `Score` - Gestiona las puntuaciones
- `Timer` - Controla el temporizador de cada jugador

---

## Métodos del Controlador

### 1. `welcome()`

**Propósito:** Muestra la página de bienvenida o redirige al panel si el usuario ya inició sesión.

**Funcionamiento:**
```
1. Verifica si existe 'player_id' en la sesión
2. Si existe → redirige al panel del jugador
3. Si no existe → muestra la vista de bienvenida
```

**Retorna:**
- Redirección a `panel.index` (si hay sesión activa)
- Vista `welcome` (si no hay sesión)

---

### 2. `login(Request $request)`

**Propósito:** Permite a un jugador existente iniciar sesión con su nombre.

**Parámetros requeridos:**
- `name` (texto, máximo 255 caracteres, obligatorio)

**Proceso paso a paso:**
1. Valida que el nombre sea correcto
2. Busca al jugador en la base de datos por su nombre
3. Si no existe, devuelve error "Jugador no encontrado"
4. Si existe, guarda en la sesión:
   - `player_id` - ID del jugador
   - `player_name` - Nombre del jugador
5. Redirige al panel del jugador

**Retorna:**
- Redirección al panel (si login exitoso)
- Mensaje de error (si el jugador no existe)

---

### 3. `register(Request $request)`

**Propósito:** Crea una nueva cuenta de jugador con configuración inicial.

**Parámetros requeridos:**
- `name` (texto, máximo 255 caracteres, único, obligatorio)
- `idavatar` (ID del avatar seleccionado)

**Proceso paso a paso:**
1. Valida que el nombre sea único (no puede repetirse)
2. Crea el jugador en la tabla `players`
3. Crea un registro de puntuación inicial con **0 puntos**
4. Crea un temporizador inicial con **180 segundos** (3 minutos)
5. Guarda la información en la sesión:
   - `player_id`
   - `player_name`
6. Redirige al panel del jugador

**Configuración inicial automática:**
- Puntuación: `0`
- Tiempo: `180 segundos`

**Retorna:**
- Redirección al panel (registro exitoso)
- Error de validación (si el nombre ya existe)

---

### 4. `logout()`

**Propósito:** Cierra la sesión del jugador actual.

**Funcionamiento:**
1. Elimina de la sesión:
   - `player_id`
   - `player_name`
2. Redirige a la página de inicio

**Retorna:**
- Redirección a la raíz del sitio (`/`)

---

## Flujo de Uso Típico
```
1. Usuario visita la página → welcome()
2. Usuario se registra → register() → Panel
   O
   Usuario inicia sesión → login() → Panel
3. Usuario cierra sesión → logout() → Página de inicio
```

---

## Notas Importantes

- **Seguridad:** El sistema usa sesiones de Laravel para mantener al usuario autenticado
- **Sin contraseña:** El login solo requiere el nombre (considera agregar seguridad adicional para producción)
- **Registro único:** Cada nombre de jugador debe ser único en el sistema
- **Inicialización automática:** Al registrarse, se crean automáticamente el score y el timer

---

## Dependencias

Este controlador depende de:
- Sistema de sesiones de Laravel
- Migraciones de las tablas: `players`, `scores`, `timers`
- Vistas: `welcome` y rutas hacia `panel.index`








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









   
