# Documentación del Proyecto

## PanelController.php

### Descripción General
Controlador de Laravel encargado de gestionar la lógica principal del juego de adivinanzas. Maneja las sesiones de jugadores, la ruleta, el temporizador y la verificación de letras.

### Ubicación
`App\Http\Controllers\PanelController`

### Dependencias
- `App\Models\Phrase` - Modelo para las frases del juego
- `App\Models\Player` - Modelo para los jugadores
- `App\Models\Timer` - Modelo para gestionar temporizadores
- `Illuminate\Http\Request` - Manejo de peticiones HTTP
- `Illuminate\Support\Facades\Session` - Gestión de sesione

---

## Métodos

### 1. `index()`
**Descripción:** Inicializa el panel del juego y carga una frase aleatoria.

**Lógica:**
- Verifica que el jugador haya iniciado sesión
- Obtiene una frase aleatoria de la base de datos
- Si no hay frases, utiliza una frase por defecto ("CICLO SIN FIN" de "EL REY LEÓN")
- Inicializa la sesión del juego con las variables necesarias
- Crea un temporizador de 180 segundos (3 minutos) si no existe
- Retorna la vista `panel.index` con los datos de la frase

**Parámetros:** Ninguno

**Retorna:** Vista HTML o redirección a la página de bienvenida

---

### 2. `temporizador()`
**Descripción:** Retorna el tiempo restante del temporizador actual.

**Lógica:**
- Verifica que exista una sesión de jugador
- Busca el temporizador asociado al jugador
- Si no existe, crea uno nuevo con 180 segundos
- Retorna los segundos restantes en formato JSON

**Parámetros:** Ninguno

**Retorna:** JSON con `segundos_restantes` o error 401 si no hay sesión

---

### 3. `girar(Request $request)`
**Descripción:** Procesa el giro de la ruleta y aplica sus efectos.

**Parámetros:**
- `$request->opcion` - Opción seleccionada de la ruleta

**Efectos de la ruleta:**
- `DEMOPERRO` - Resta 5 segundos
- `DEMOGORGON` - Resta 10 segundos
- `VECNA` - Resta 15 segundos
- `ELEVEN` - Suma 20 segundos
- `VOCAL` - Limita las letras a vocales únicamente
- `CONSONANTE` - Limita las letras a consonantes únicamente

**Lógica:**
- Valida la sesión del jugador
- Obtiene o crea el temporizador
- Aplica los efectos según la opción seleccionada
- El tiempo no puede ser negativo (mínimo 0)
- Guarda la opción actual en la sesión si es VOCAL o CONSONANTE
- Actualiza el temporizador en la base de datos

**Retorna:** JSON con `segundos_restantes` y `opcion_actual`

---

### 4. `letra(Request $request)`
**Descripción:** Verifica si una letra existe en la frase y aplica restricciones de la ruleta.

**Parámetros:**
- `$request->letra` - Letra a verificar

**Validaciones:**
- Si la ruleta está en modo VOCAL, solo acepta vocales (A, E, I, O, U)
- Si la ruleta está en modo CONSONANTE, rechaza vocales

**Lógica:**
- Convierte la letra a mayúscula
- Verifica si existe en la frase actual
- Si existe y no ha sido adivinada, se agrega a la lista de letras adivinadas
- Retorna el resultado en JSON

**Retorna:** JSON con `success`, `letra` y `letras_adivinadas`

---

### 5. `checkLetter(Request $request)`
**Descripción:** Búsqueda avanzada que retorna todas las posiciones de una letra en la frase.

**Parámetros:**
- `$request->letra` - Letra a buscar

**Lógica:**
- Convierte la letra y frase a mayúscula
- Itera sobre cada carácter de la frase
- Registra todas las posiciones donde aparece la letra
- Retorna un array con las posiciones encontradas

**Retorna:** JSON con `existe`, `posiciones` (array de índices) y `letra`

---

### 6. `reset()`
**Descripción:** Reinicia el juego limpiando la sesión y el temporizador.

**Lógica:**
- Olvida todas las variables de sesión del juego
- Restablece el temporizador a 180 segundos
- Redirige a `/panel` para comenzar una nueva partida

**Parámetros:** Ninguno

**Retorna:** Redirección a `/panel`

---

## Variables de Sesión Utilizadas

| Variable | Tipo | Descripción |
|----------|------|-------------|
| `player_id` | Integer | ID único del jugador |
| `player_name` | String | Nombre del jugador |
| `frase_actual` | String | Frase a adivinar |
| `movie_actual` | String | Película de la que proviene la frase |
| `letras_adivinadas` | Array | Letras que ha adivinado el jugador |
| `opcion_ruleta_actual` | String | Restricción actual (VOCAL/CONSONANTE) |

---

## Flujo del Juego

1. Usuario accede a `/panel` → Se ejecuta `index()`
2. Se carga una frase aleatoria y se inicia un temporizador de 3 minutos
3. Usuario gira la ruleta → Se ejecuta `girar()`
4. Según el resultado, se aplican efectos de tiempo o restricciones
5. Usuario selecciona una letra → Se ejecuta `letra()`
6. Se verifica si existe y se actualiza la lista de adivinadas
7. Se usa `checkLetter()` para mostrar las posiciones de la letra
8. Cuando termina la partida → Se ejecuta `reset()` para jugar de nuevo

---

## Notas Técnicas

- El temporizador se decrementa automáticamente en el frontend
- Las frases se almacenan en la base de datos (tabla `phrases`)
- Cada jugador tiene su propia sesión independiente
- El tiempo mínimo es 0 segundos (no puede ser negativo)
- La validación de vocales/consonantes es case-insensitive (se convierte a mayúscula)

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

# Documentación del PlayerController

## Descripción General

Este controlador gestiona todo lo relacionado con los jugadores en la aplicación: registro, inicio de sesión y cierre de sesión. Es el punto de entrada principal para que los usuarios accedan al sistem
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

  # Documentación del ProfileController

## Descripción General

Este controlador gestiona el perfil de usuario: permite ver, editar y eliminar la cuenta del usuario autenticado. Es parte del sistema de autenticación estándar de Laravel.

---

## Estructura del Archivo

**Namespace:** `App\Http\Controllers`

**Clases y servicios utilizados:**
- `ProfileUpdateRequest` - Validación personalizada para actualizar el perfil
- `Auth` - Facade para gestionar la autenticación
- `Redirect` - Facade para redirecciones

---

## Métodos del Controlador

### 1. `edit(Request $request): View`

**Propósito:** Muestra el formulario de edición del perfil del usuario.

**Funcionamiento:**
```
1. Obtiene el usuario autenticado desde la petición
2. Pasa los datos del usuario a la vista
3. Muestra el formulario de edición
```

**Parámetros enviados a la vista:**
- `user` - Objeto del usuario autenticado actual

**Retorna:**
- Vista `profile.edit` con los datos del usuario

---

### 2. `update(ProfileUpdateRequest $request): RedirectResponse`

**Propósito:** Actualiza la información del perfil del usuario.

**Validación:**
- Utiliza `ProfileUpdateRequest` para validar los datos automáticamente

**Proceso paso a paso:**
1. Toma los datos validados y los asigna al usuario
2. **Verificación de email:**
   - Si el email cambió → marca `email_verified_at` como `null`
   - Esto requiere que el usuario verifique el nuevo email
3. Guarda los cambios en la base de datos
4. Redirige al formulario de edición con mensaje de éxito

**Comportamiento especial:**
- `isDirty('email')` detecta si el email fue modificado
- Al cambiar el email, se invalida la verificación anterior

**Retorna:**
- Redirección a `profile.edit` con status `profile-updated`

---

### 3. `destroy(Request $request): RedirectResponse`

**Propósito:** Elimina permanentemente la cuenta del usuario.

**Validación requerida:**
- `password` - Contraseña actual (obligatoria para confirmar la acción)
- Usa el bag `userDeletion` para agrupar los errores

**Proceso paso a paso:**
1. Valida que la contraseña sea correcta
2. Guarda referencia al usuario actual
3. Cierra la sesión del usuario (`Auth::logout()`)
4. Elimina el usuario de la base de datos
5. Invalida la sesión actual (seguridad)
6. Regenera el token CSRF (previene ataques)
7. Redirige a la página de inicio

**Medidas de seguridad:**
```
- Requiere contraseña para confirmar
- Cierra sesión antes de eliminar
- Invalida sesión completa
- Regenera token CSRF
```

**Retorna:**
- Redirección a la raíz del sitio (`/`)

---

## Flujo de Uso Típico
```
1. Usuario accede a su perfil → edit()
2. Usuario modifica sus datos → update() → Confirmación de actualización
3. Usuario decide eliminar cuenta → destroy() → Página de inicio
```

---

## Notas Importantes

### Seguridad
- **Verificación de email:** Al cambiar el email, se requiere nueva verificación
- **Confirmación por contraseña:** Eliminar cuenta requiere contraseña actual
- **Invalidación de sesión:** Al eliminar cuenta, se destruye toda la sesión

### Gestión de Email
- Si cambias el email, pierdes la verificación
- Debes volver a verificar el nuevo email
- Esto previene cambios no autorizados de email

### Eliminación de Cuenta
- Es una acción **irreversible**
- Cierra automáticamente la sesión
- Elimina todos los datos del usuario
- Usa `validateWithBag('userDeletion')` para agrupar errores específicos

---

## Dependencias

Este controlador depende de:
- Sistema de autenticación de Laravel (`Auth`)
- Tabla `users` en la base de datos
- `ProfileUpdateRequest` para validación personalizada
- Vistas: `profile.edit`
- Middleware de autenticación (debe estar aplicado en las rutas)

---

## Diferencias con PlayerController

**PlayerController:**
- Usa sesiones manuales
- No requiere contraseña
- Login por nombre únicamente

**ProfileController:**
- Usa sistema de autenticación completo de Laravel
- Requiere contraseña para acciones críticas
- Gestión completa de usuarios con email verificado

# Documentación del RouletteController

## Descripción General

Este controlador gestiona la ruleta del juego, que permite a los jugadores girar una ruleta para obtener opciones aleatorias (vocales, consonantes o personajes especiales) y aplicar sus efectos sobre el temporizador del jugador.

---

## Estructura del Archivo

**Namespace:** `App\Http\Controllers`

**Modelos utilizados:**
- `Player` - Representa a los jugadores
- `Timer` - Gestiona el temporizador de cada jugador

**Servicios utilizados:**
- `Session` - Para acceder a la sesión del jugador

---

## Métodos del Controlador

### 1. `spin(Request $request)`

**Propósito:** Gira la ruleta y devuelve una opción aleatoria.

**Opciones disponibles en la ruleta:**
```
- VOCAL (3 veces - 30% probabilidad)
- CONSONANTE (3 veces - 30% probabilidad)
- VECNA (1 vez - 10% probabilidad)
- DEMOGORGON (1 vez - 10% probabilidad)
- DEMOPERRO (1 vez - 10% probabilidad)
- ELEVEN (1 vez - 10% probabilidad)
```

**Funcionamiento:**
1. Define un array con todas las opciones posibles
2. Selecciona una opción al azar usando `array_rand()`
3. Devuelve la opción seleccionada en formato JSON

**Retorna:**
- JSON: `{'option': 'VOCAL'}` (o cualquier otra opción)

---

### 2. `apply(Request $request)`

**Propósito:** Aplica los efectos de la opción seleccionada en la ruleta sobre el temporizador del jugador.

**Parámetros requeridos:**
- `option` - La opción obtenida en la ruleta (en el body de la petición)

**Validaciones iniciales:**
1. Verifica que exista `player_id` en la sesión
   - Si no existe → Error 401: "No hay sesión"
2. Busca al jugador en la base de datos
   - Si no existe → Error 404: "Jugador no encontrado"
3. Verifica si el jugador tiene un timer
   - Si no existe → Crea uno nuevo con 180 segundos

**Efectos sobre el tiempo según la opción:**

| Opción | Efecto | Descripción |
|--------|--------|-------------|
| **VECNA** | -15 segundos | Resta 15 segundos (mínimo 0) |
| **DEMOGORGON** | -10 segundos | Resta 10 segundos (mínimo 0) |
| **DEMOPERRO** | -5 segundos | Resta 5 segundos (mínimo 0) |
| **ELEVEN** | +20 segundos | Suma 20 segundos |
| **VOCAL** | Sin efecto | Solo permite elegir letra |
| **CONSONANTE** | Sin efecto | Solo permite elegir letra |

**Protección contra tiempo negativo:**
- La función `max(0, $timer->seconds - X)` asegura que el tiempo nunca sea menor a 0

**Proceso paso a paso:**
1. Obtiene el ID del jugador desde la sesión
2. Valida que el jugador existe
3. Obtiene o crea el temporizador
4. Convierte la opción a mayúsculas
5. Aplica el efecto correspondiente según el switch
6. Guarda los cambios en la base de datos
7. Devuelve respuesta JSON con la acción a realizar

**Respuesta JSON:**
```json
{
  "action": "pickletter",  // o "time"
  "type": "VOCAL",         // o la opción que salió
  "seconds": 165           // tiempo actualizado
}
```

**Tipos de acción:**
- `pickletter` - Si salió VOCAL o CONSONANTE (el jugador debe elegir una letra)
- `time` - Si salió un personaje (solo afecta el tiempo)

**Retorna:**
- JSON con: acción, tipo de opción y segundos actuales
- Error 401 si no hay sesión
- Error 404 si el jugador no existe

---

## Flujo de Uso Típico
```
1. Usuario gira la ruleta → spin() → Devuelve opción aleatoria
2. Frontend envía la opción → apply() → Aplica efectos
3. Si es VOCAL/CONSONANTE → Jugador elige letra
4. Si es personaje → Solo se modifica el tiempo
```

---

## Notas Importantes

### Probabilidades de la Ruleta
- **60%** - Opciones positivas (VOCAL o CONSONANTE para elegir letras)
- **30%** - Opciones negativas (VECNA, DEMOGORGON, DEMOPERRO)
- **10%** - Opción muy positiva (ELEVEN - suma tiempo)

### Gestión del Tiempo
- **Mínimo:** 0 segundos (nunca puede ser negativo)
- **Tiempo inicial:** 180 segundos (3 minutos)
- **Sin máximo:** ELEVEN puede sumar tiempo indefinidamente

### Seguridad
- Requiere sesión activa del jugador
- Verifica la existencia del jugador en cada operación
- Crea automáticamente el timer si no existe

### Personajes de Stranger Things
Los nombres hacen referencia a la serie:
- **VECNA** - Villano principal (efecto muy negativo)
- **DEMOGORGON** - Monstruo (efecto negativo medio)
- **DEMOPERRO** - Criatura menor (efecto negativo leve)
- **ELEVEN** - Heroína (efecto positivo)

---

## Dependencias

Este controlador depende de:
- Sistema de sesiones de Laravel
- Tabla `players` en la base de datos
- Tabla `timers` en la base de datos
- Relación `player->timer` definida en el modelo Player
- Peticiones AJAX desde el frontend

---

## Posibles Mejoras

1. **Validar el parámetro `option`** en el método `apply()`
2. **Registrar historial** de giros para estadísticas
3. **Límite de giros** por partida o por tiempo
4. **Efectos combinados** si salen varias opciones seguidas
5. **Animaciones** coordinadas entre frontend y backend

# Documentación del ScoreController

## Descripción General

Este controlador gestiona el sistema de puntuación del juego. Permite a los jugadores adivinar letras individuales o intentar resolver frases completas, actualizando su puntuación según los aciertos o errores.

---

## Estructura del Archivo

**Namespace:** `App\Http\Controllers`

**Modelos utilizados:**
- `Player` - Representa a los jugadores
- `Panel` - Contiene los paneles del juego
- `Score` - Gestiona la puntuación de cada jugador (accedido a través de la relación `player->score`)

---

## Métodos del Controlador

### 1. `letter(Request $request)`

**Propósito:** Procesa la letra elegida por el jugador y verifica si aparece en alguna de las frases del panel.

**Parámetros requeridos:**
- `letter` - Letra seleccionada por el jugador
- `panel_id` - ID del panel actual
- `player_id` - ID del jugador

**Funcionamiento paso a paso:**

1. **Preparación:**
   - Convierte la letra a mayúsculas
   - Carga el panel con todas sus frases relacionadas
   - Busca al jugador en la base de datos

2. **Búsqueda de coincidencias:**
   - Recorre cada frase del panel
   - Busca la letra en cada posición de la frase
   - Guarda todas las posiciones donde aparece

3. **Cálculo de puntuación:**
   - Por cada letra encontrada: **+10 puntos**
   - Ejemplo: Si la letra 'A' aparece 3 veces → +30 puntos
   - Solo suma puntos si encuentra al menos una coincidencia

4. **Construcción de respuesta:**
   - Crea un array con cada frase y sus posiciones de coincidencia
   - Incluye la puntuación actualizada

**Sistema de puntuación:**
```
Letra encontrada = +10 puntos por cada aparición
Ejemplo: 
- "HOLA MUNDO" con letra 'O' → 2 apariciones → +20 puntos
- "STRANGER THINGS" con letra 'N' → 2 apariciones → +20 puntos
```

**Estructura de respuesta JSON:**
```json
{
  "found": [
    {
      "phrase": "HOLA MUNDO",
      "positions": [1, 6]
    },
    {
      "phrase": "SEGUNDA FRASE",
      "positions": []
    }
  ],
  "score": 150
}
```

**Retorna:**
- JSON con todas las frases, posiciones encontradas y puntuación total

---

### 2. `guess(Request $request)`

**Propósito:** Procesa un intento de resolver una frase completa y actualiza la puntuación según sea correcto o incorrecto.

**Parámetros requeridos:**
- `guess` - Frase completa propuesta por el jugador
- `panel_id` - ID del panel actual
- `player_id` - ID del jugador

**Funcionamiento paso a paso:**

1. **Preparación:**
   - Convierte la frase propuesta a mayúsculas
   - Carga el panel con todas sus frases
   - Busca al jugador

2. **Verificación:**
   - Recorre todas las frases del panel
   - Compara la propuesta con cada frase (usando `trim()` para eliminar espacios)
   - Si encuentra coincidencia exacta, marca como correcto

3. **Actualización de puntuación:**
   - **Si es correcto:** +100 puntos
   - **Si es incorrecto:** -10 puntos (mínimo 0)
   - Usa `max(0, ...)` para evitar puntuaciones negativas

4. **Guardado y respuesta:**
   - Guarda la puntuación actualizada
   - Devuelve el resultado y la puntuación actual

**Sistema de puntuación:**
```
Acierto completo = +100 puntos
Fallo = -10 puntos (nunca baja de 0)
```

**Protección contra puntuación negativa:**
- `max(0, $player->score->score - 10)` asegura que nunca sea menor a 0

**Estructura de respuesta JSON:**
```json
{
  "result": "correct",  // o "wrong"
  "score": 250
}
```

**Retorna:**
- JSON con el resultado ('correct' o 'wrong') y puntuación actualizada

---

## Comparación de Métodos

| Aspecto | `letter()` | `guess()` |
|---------|-----------|----------|
| **Acción** | Adivinar letra | Resolver frase completa |
| **Puntos por acierto** | +10 por letra | +100 por frase |
| **Puntos por fallo** | 0 (sin penalización) | -10 |
| **Búsqueda** | Posición por posición | Comparación exacta |
| **Respuesta** | Array de posiciones | Correcto/Incorrecto |

---

## Flujo de Uso Típico
```
ESCENARIO 1: Adivinar letra
1. Jugador selecciona letra 'A' → letter()
2. Sistema busca 'A' en todas las frases
3. Encuentra 3 apariciones → +30 puntos
4. Devuelve posiciones y puntuación

ESCENARIO 2: Resolver frase
1. Jugador propone "STRANGER THINGS" → guess()
2. Sistema compara con frases del panel
3. Si coincide → +100 puntos
4. Si falla → -10 puntos
5. Devuelve resultado y puntuación
```

---

## Notas Importantes

### Sistema de Puntuación Completo
```
Acción                          Puntos
─────────────────────────────────────────
Letra correcta (cada una)       +10
Frase completa correcta         +100
Frase completa incorrecta       -10
Mínimo posible                  0
```

### Búsqueda de Letras
- **Case-insensitive:** Convierte todo a mayúsculas
- **Posicional:** Guarda la posición exacta de cada letra
- **Sin penalización:** No resta puntos si no encuentra la letra

### Resolución de Frases
- **Coincidencia exacta:** Debe ser idéntica (sin contar espacios al inicio/final)
- **Penalización por fallo:** Resta puntos para desincentivar intentos aleatorios
- **Gran recompensa:** +100 puntos incentiva resolver la frase

### Protecciones
- **Puntuación mínima:** Nunca baja de 0 puntos
- **Relaciones cargadas:** Usa `with('phrases')` para optimizar consultas
- **FindOrFail:** Lanza error 404 si no encuentra panel o jugador

---

## Dependencias

Este controlador depende de:
- Tabla `players` con relación `score`
- Tabla `panels` con relación `phrases`
- Tabla `scores` vinculada a jugadores
- Tabla `phrases` vinculada a paneles
- Relaciones definidas en los modelos:
  - `Player->score`
  - `Panel->phrases`

---

## Estrategia de Juego

**Jugador conservador:**
- Adivina letras frecuentes (+10 puntos sin riesgo)
- Acumula puntos gradualmente

**Jugador arriesgado:**
- Intenta resolver frases temprano
- +100 si acierta, pero -10 si falla

**Óptimo:**
- Adivina letras clave primero
- Resuelve la frase cuando esté seguro

---

## Posibles Mejoras

1. **Validación de entrada:** Verificar que `letter` sea una sola letra
2. **Límite de intentos:** Restringir cantidad de intentos fallidos
3. **Multiplicadores:** Bonos por rachas de aciertos
4. **Histórico:** Registrar letras ya usadas
5. **Penalización dinámica:** Mayor penalización por múltiples fallos consecutivos
6. **Puntos por tiempo:** Bonus por resolver rápido

# Documentación del TimerController

## Descripción General

Este controlador gestiona la consulta del temporizador de un jugador. Permite obtener el tiempo restante en segundos para un jugador específico. Es un controlador simple y directo con un único propósito: devolver el estado actual del temporizador.

---

## Estructura del Archivo

**Namespace:** `App\Http\Controllers`

**Modelos utilizados:**
- `Player` - Representa a los jugadores (con relación al Timer)

**Relaciones necesarias:**
- `Player->timer` - Relación definida en el modelo Player para acceder al temporizador

---

## Métodos del Controlador

### 1. `get($player_id)`

**Propósito:** Obtiene el tiempo restante en segundos del temporizador de un jugador específico.

**Parámetros requeridos:**
- `$player_id` - ID del jugador (pasado en la URL como parámetro de ruta)

**Funcionamiento paso a paso:**

1. **Búsqueda del jugador:**
   - Busca al jugador por su ID usando `findOrFail()`
   - Si no existe → Lanza automáticamente error 404

2. **Acceso al temporizador:**
   - Accede al temporizador mediante la relación `player->timer`
   - Obtiene el valor de `seconds`

3. **Respuesta:**
   - Devuelve un JSON con los segundos restantes

**Estructura de respuesta JSON:**
```json
{
  "seconds": 165
}
```

**Posibles respuestas:**
- **Éxito (200):** JSON con los segundos actuales
- **Error (404):** Jugador no encontrado

**Retorna:**
- JSON con el tiempo restante en segundos
- Error 404 si el jugador no existe

---

## Ejemplo de Uso

### Petición HTTP
```
GET /api/timer/5
```

### Respuesta Exitosa
```json
{
  "seconds": 180
}
```

### Respuesta de Error (Jugador no existe)
```json
{
  "message": "No query results for model [App\\Models\\Player] 5"
}
```

---

## Integración con Otros Controladores

Este controlador trabaja en conjunto con:

**RouletteController:**
- `RouletteController->apply()` modifica el tiempo
- `TimerController->get()` consulta el tiempo actual

**Flujo típico:**
```
1. Frontend solicita tiempo actual → TimerController->get()
2. Usuario gira ruleta → RouletteController->spin()
3. Se aplica efecto → RouletteController->apply() (modifica tiempo)
4. Frontend actualiza display → TimerController->get() (consulta nuevo tiempo)
```

---

## Características Técnicas

### Simplicidad
- **Un solo método:** Controlador minimalista y enfocado
- **Sin lógica de negocio:** Solo consulta y devuelve datos
- **Stateless:** No modifica ningún dato, solo lee

### Seguridad
- **FindOrFail:** Protección automática contra IDs inexistentes
- **Solo lectura:** No puede modificar datos, menor riesgo
- **Sin validación compleja:** ID es validado implícitamente por la ruta

### Rendimiento
- **Consulta simple:** Una sola query a la base de datos
- **Relación eager loading:** Puede optimizarse con `with('timer')` si es necesario
- **Respuesta ligera:** JSON minimalista

---

## Uso en el Frontend

### JavaScript/AJAX
```javascript
// Consultar tiempo del jugador
fetch(`/api/timer/${playerId}`)
  .then(response => response.json())
  .then(data => {
    console.log(`Tiempo restante: ${data.seconds} segundos`);
    updateTimerDisplay(data.seconds);
  });
```

### Actualización periódica
```javascript
// Actualizar cada segundo
setInterval(() => {
  fetch(`/api/timer/${playerId}`)
    .then(response => response.json())
    .then(data => {
      updateTimerDisplay(data.seconds);
    });
}, 1000);
```

---

## Notas Importantes

### Relación con Timer
- **Dependencia crítica:** Requiere que la relación `Player->timer` esté definida
- **Timer obligatorio:** Asume que todo jugador tiene un timer asociado
- **Sin creación:** No crea timer si no existe (a diferencia de RouletteController)

### Gestión de Errores
- **404 automático:** `findOrFail()` maneja jugadores inexistentes
- **Sin timer:** Si el jugador no tiene timer, puede causar error
  - Solución: Asegurar que todo player tenga timer al crearse

### Frecuencia de Consulta
- **Polling vs WebSockets:** Este endpoint usa polling (consultas repetidas)
- **Carga del servidor:** Consultas frecuentes pueden generar mucho tráfico
- **Optimización:** Considerar WebSockets o Server-Sent Events para tiempo real

---

## Dependencias

Este controlador depende de:
- Tabla `players` en la base de datos
- Tabla `timers` en la base de datos
- Relación `Player->timer` definida en el modelo Player
- Ruta configurada para recibir `player_id` como parámetro

**Ejemplo de ruta necesaria:**
```php
Route::get('/timer/{player_id}', [TimerController::class, 'get']);
```

---

## Comparación con RouletteController

| Aspecto | TimerController | RouletteController |
|---------|-----------------|-------------------|
| **Operación** | Solo lectura | Lectura y escritura |
| **Modifica timer** | No | Sí |
| **Crea timer** | No | Sí (si no existe) |
| **Complejidad** | Mínima | Media |
| **Validaciones** | Solo ID | ID y opción |

---

## Posibles Mejoras

1. **Crear timer si no existe:**
```php
if (!$player->timer) {
    $player->timer()->create(['seconds' => 180]);
}
```

2. **Incluir información adicional:**
```php
return response()->json([
    'seconds' => $player->timer->seconds,
    'formatted' => gmdate('i:s', $player->timer->seconds), // "03:00"
    'status' => $player->timer->seconds > 0 ? 'active' : 'expired'
]);
```

3. **Caché para reducir consultas:**
```php
$seconds = Cache::remember(
    "player_{$player_id}_timer", 
    5, // 5 segundos
    fn() => Player::findOrFail($player_id)->timer->seconds
);
```

4. **Validación de parámetro:**
```php
public function get(Request $request, $player_id)
{
    $request->validate(['player_id' => 'integer|exists:players,id']);
    // ...
}
```

5. **WebSocket para tiempo real:**
```php
// Usar Laravel Broadcasting en lugar de polling
broadcast(new TimerUpdated($player_id, $seconds));
```

---

## Caso de Uso Real

**Juego de la Ruleta de la Fortuna:**
```
1. Jugador inicia partida (180 segundos)
2. Frontend consulta tiempo cada segundo → get()
3. Jugador gira ruleta y sale VECNA
4. Se aplican -15 segundos → RouletteController->apply()
5. Frontend consulta nuevo tiempo → get() (165 segundos)
6. Display se actualiza mostrando 2:45
7. Proceso se repite hasta llegar a 0 segundos
```

---

## Resumen

**TimerController** es un controlador minimalista enfocado en una única responsabilidad: consultar el tiempo restante de un jugador. Su simplicidad lo hace fácil de mantener y entender, pero también presenta oportunidades de optimización para aplicaciones de alta frecuencia de actualización.








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




-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------





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










   
