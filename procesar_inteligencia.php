<?php
session_start();

/* =========================================
   PENGUICH - HABILIDADES Y APTITUDES
   ========================================= */

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli(
        "localhost",
        "root",
        "",
        "penguich"
    );
    $conexion->set_charset("utf8mb4");
} catch (Throwable $e) {
    http_response_code(500);
    exit("No se pudo conectar con la base de datos.");
}

function escapar($texto) {
    return htmlspecialchars(
        (string)$texto,
        ENT_QUOTES,
        'UTF-8'
    );
}

$nombre_usuario = (string)$_SESSION['usuario'];

$stmt = $conexion->prepare(
    "SELECT id FROM usuarios WHERE usuario = ? LIMIT 1"
);
$stmt->bind_param("s", $nombre_usuario);
$stmt->execute();
$stmt->bind_result($usuario_id);

if (!$stmt->fetch()) {
    $stmt->close();
    http_response_code(403);
    exit("Tu cuenta no existe. Inicia sesión nuevamente.");
}
$stmt->close();

/* =========================================
   PREGUNTAS DEL CUESTIONARIO (10 PREGUNTAS)
   ========================================= */

$preguntas = [

    "H1" => [
        "titulo" => "H1",
        "pregunta" => "Si te regalan un juego de construcción o un juguete complejo que viene totalmente desarmado, ¿qué es lo primero que haces?",
        "opciones" => [
            "A" => "Busco las piezas mecánicas y empiezo a armarlo probando cómo embonan por lógica de peso y fuerza.",
            "B" => "Me fijo primero en el diseño visual de la caja para que los colores y la forma queden perfectos.",
            "C" => "Busco a alguien para armarlo en equipo y organizamos quién monta cada parte.",
            "D" => "Leo con calma el instructivo de principio a fin para entender cómo funciona la estructura antes de tocar nada."
        ]
    ],

    "H2" => [
        "titulo" => "H2",
        "pregunta" => "Tienes una tarde libre y te dan una caja llena de materiales reciclados (cartón, botellas, cinta, tijeras). ¿Qué prefieres inventar?",
        "opciones" => [
            "A" => "Un mecanismo que tenga ruedas, poleas o puertas que se abran solas.",
            "B" => "Una maqueta, escultura o un disfraz detallado de un personaje imaginario.",
            "C" => "Un juego de mesa o una dinámica divertida para jugar con toda la familia.",
            "D" => "Un mini laboratorio o un artefacto para observar insectos y plantas del jardín."
        ]
    ],

    "H3" => [
        "titulo" => "H3",
        "pregunta" => "En un proyecto escolar en equipo, ¿en qué parte te sientes más útil y disfrutas más participar?",
        "opciones" => [
            "A" => "Resolviendo los problemas técnicos de última hora cuando algo no encaja o no funciona.",
            "B" => "Diseñando la cartulina, los dibujos, las portadas y la parte visual del trabajo.",
            "C" => "Liderando la presentación oral, organizando a los compañeros y repartiendo turnos.",
            "D" => "Haciendo la investigación profunda, buscando datos curiosos y redactando los textos."
        ]
    ],

    "H4" => [
        "titulo" => "H4",
        "pregunta" => "Encuentras un objeto curioso o un insecto raro en el parque. ¿Cuál es tu primera reacción natural?",
        "opciones" => [
            "A" => "Intentar ver cómo se mueve, qué mecanismo físico tiene o cómo reacciona si lo toco.",
            "B" => "Dibujarlo rápidamente en una libreta o detallar los colores extraños que tiene.",
            "C" => "Llamar a mis amigos a gritos para que vengan a verlo y organizarnos para observarlo juntos.",
            "D" => "Preguntar '¿Cómo se llama?', '¿De dónde salió?' y buscar información sobre él."
        ]
    ],

    "H5" => [
        "titulo" => "H5",
        "pregunta" => "Si tuvieras que participar en un concurso escolar, ¿a cuál categoría te inscribirías con los ojos cerrados?",
        "opciones" => [
            "A" => "Feria de ciencias e inventos mecánicos.",
            "B" => "Concurso de pintura, dibujo o diseño digital.",
            "C" => "Concurso de debates, oratoria o teatro escolar.",
            "D" => "Olimpiada de matemáticas o resolución de acertijos lógicos."
        ]
    ],

    "H6" => [
        "titulo" => "H6",
        "pregunta" => "Cuando un juego de mesa o un videojuego se pone muy difícil y pierdes varias veces seguidas, ¿cómo reaccionas?",
        "opciones" => [
            "A" => "Cambio de estrategia, pruebo otro camino o intento modificar mis movimientos hasta lograrlo.",
            "B" => "Me detengo a observar los detalles del escenario para ver si hay una pista visual que no vi.",
            "C" => "Pido consejo a alguien más o platico con mis compañeros para ver qué táctica usaron ellos.",
            "D" => "Analizo las reglas lógicas del juego para entender en qué punto exacto me equivoqué."
        ]
    ],

    "H7" => [
        "titulo" => "H7",
        "pregunta" => "¿Qué tipo de herramientas o aplicaciones tecnológicas te divierte más usar en una computadora o tablet?",
        "opciones" => [
            "A" => "Simuladores de construcción, programación por bloques o juegos de ingeniería.",
            "B" => "Programas de dibujo, edición de imágenes o diseño de avatares.",
            "C" => "Redes sociales infantiles seguras, chats con amigos o juegos multijugador colaborativos.",
            "D" => "Enciclopedias interactivas, juegos de misterio o mapas del mundo digital."
        ]
    ],

    "H8" => [
        "titulo" => "H8",
        "pregunta" => "Si te dieran un superpoder por un solo día para ayudar en tu escuela, ¿cuál elegirías?",
        "opciones" => [
            "A" => "El poder de reparar cualquier cosa descompuesta con solo tocarla.",
            "B" => "El poder de hacer que todo se vea hermoso, colorido y perfectamente diseñado al instante.",
            "C" => "El poder de unir a todos los compañeros para que nunca haya peleas y trabajen felices.",
            "D" => "El poder de saber la respuesta a cualquier pregunta difícil del universo al instante."
        ]
    ],

    "H9" => [
        "titulo" => "H9",
        "pregunta" => "Cuando tienes que explicarle un concepto complicado o un truco nuevo a un amigo, ¿cómo se lo transmites?",
        "opciones" => [
            "A" => "Le muestro con las manos y hago que él mismo manipule el objeto para que lo descubra.",
            "B" => "Le hago un esquema visual rápido o un dibujo explicativo en un papel.",
            "C" => "Me siento a platicar con él animadamente guiándolo paso a paso con mi voz.",
            "D" => "Le explico las razones lógicas y científicas de por qué funciona así."
        ]
    ],

    "H10" => [
        "titulo" => "H10",
        "pregunta" => "Si pudieras elegir el tema de tu fiesta de cumpleaños ideal, ¿cómo sería la temática principal?",
        "opciones" => [
            "A" => "Una aventura de constructores, ingenieros o inventores locos.",
            "B" => "Un festival de arte, disfraces creativos y estudio de cine.",
            "C" => "Una fiesta de juegos en equipo, competencias masivas y dinámicas de grupo.",
            "D" => "Una misión de detectives secretos, misterios espaciales o búsqueda del tesoro científico."
        ]
    ]

];

/* =========================================
   PERFILES Y DESCRIPCIONES
   ========================================= */

$perfiles = [
    "A" => [
        "nombre" => "Maker / Ingeniería",
        "icono" => "🛠️",
        "descripcion" => "Te gusta construir, experimentar con objetos y encontrar soluciones prácticas. Disfrutas descubrir cómo funcionan las cosas.",
        "consejo" => "Prueba construir prototipos, armar mecanismos y resolver retos prácticos."
    ],
    "B" => [
        "nombre" => "Creativo / Diseño",
        "icono" => "🎨",
        "descripcion" => "Te gusta imaginar, diseñar y transformar tus ideas en creaciones originales. Disfrutas explorar colores, formas y nuevas posibilidades.",
        "consejo" => "Explora el dibujo, las maquetas, el diseño y la creación de historias visuales."
    ],
    "C" => [
        "nombre" => "Liderazgo / Social",
        "icono" => "🤝",
        "descripcion" => "Te gusta colaborar, comunicar ideas y organizar actividades. Disfrutas participar y ayudar a que las personas trabajen juntas.",
        "consejo" => "Participa en proyectos grupales, teatro, debates y actividades colaborativas."
    ],
    "D" => [
        "nombre" => "Científico / Investigador",
        "icono" => "🔬",
        "descripcion" => "Te gusta hacer preguntas, investigar misterios y buscar explicaciones. Disfrutas analizar información y descubrir cosas nuevas.",
        "consejo" => "Prueba experimentos seguros, acertijos, investigaciones y retos científicos."
    ]
];

/* =========================================
   CREAR TABLA AUTOMÁTICAMENTE
   ========================================= */

$conexion->query("
    CREATE TABLE IF NOT EXISTS test_inteligencias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        puntaje_maker INT NOT NULL DEFAULT 0,
        puntaje_creativo INT NOT NULL DEFAULT 0,
        puntaje_liderazgo INT NOT NULL DEFAULT 0,
        puntaje_cientifico INT NOT NULL DEFAULT 0,
        perfil_predominante VARCHAR(150) NOT NULL,
        porcentaje_predominante DECIMAL(5,2) NOT NULL,
        respuestas_json LONGTEXT NOT NULL,
        fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (usuario_id),
        FOREIGN KEY (usuario_id)
            REFERENCES usuarios(id)
            ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

/* =========================================
   PROCESAR RESPUESTAS
   ========================================= */

$guardado_exitoso = false;
$error = "";
$puntajes = [
    "A" => 0,
    "B" => 0,
    "C" => 0,
    "D" => 0
];

$perfil_predominante = "";
$porcentaje_predominante = 0;
$perfiles_ganadores = [];

$total_preguntas = count($preguntas);

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["enviar_test"])
) {

    $respuestas = [];

    foreach ($preguntas as $clave => $pregunta) {

        $respuesta = $_POST[$clave] ?? null;

        if (
            !is_string($respuesta) ||
            !array_key_exists($respuesta, $puntajes)
        ) {
            $error = "Debes responder todas las preguntas antes de guardar.";
            break;
        }

        $respuestas[$clave] = $respuesta;
        $puntajes[$respuesta]++;
    }

    if ($error === "") {

        $maximo = max($puntajes);

        foreach ($puntajes as $letra => $puntos) {
            if ($puntos === $maximo) {
                $perfiles_ganadores[] = $letra;
            }
        }

        $nombres_ganadores = [];

        foreach ($perfiles_ganadores as $letra) {
            $nombres_ganadores[] = $perfiles[$letra]["nombre"];
        }

        $perfil_predominante = implode(
            " / ",
            $nombres_ganadores
        );

        $porcentaje_predominante =
            ($maximo / $total_preguntas) * 100;

        $respuestas_json = json_encode(
            $respuestas,
            JSON_UNESCAPED_UNICODE
        );

        try {

            $stmt = $conexion->prepare("
                INSERT INTO test_inteligencias (
                    usuario_id,
                    puntaje_maker,
                    puntaje_creativo,
                    puntaje_liderazgo,
                    puntaje_cientifico,
                    perfil_predominante,
                    porcentaje_predominante,
                    respuestas_json
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iiiiisds",
                $usuario_id,
                $puntajes["A"],
                $puntajes["B"],
                $puntajes["C"],
                $puntajes["D"],
                $perfil_predominante,
                $porcentaje_predominante,
                $respuestas_json
            );

            $stmt->execute();
            $stmt->close();

            $guardado_exitoso = true;

        } catch (Throwable $e) {
            $error = "No se pudo guardar el resultado. Inténtalo nuevamente.";
        }
    }
}

$iniciar_quiz =
    (isset($_GET["iniciar"]) && $_GET["iniciar"] === "1")
    || ($_SERVER["REQUEST_METHOD"] === "POST" && !$guardado_exitoso);

?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Penguich | Mis habilidades y aptitudes</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>

/* =========================================
   COLORES PENGUICH & ESTILOS MODERNOS
   ========================================= */

:root {
    --menta: #E8F8F1;
    --turquesa: #2A9D8F;
    --turquesa-oscuro: #18786E;
    --amarillo: #F4C95D;
    --naranja: #FFAD60;
    --texto: #173D39;
    --gris: #657C78;
    --blanco: #FFFFFF;
    --borde: #CDEAE1;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 25px 15px;
    min-height: 100vh;
    font-family: 'Nunito', sans-serif;
    color: var(--texto);
    background:
        radial-gradient(circle at 10% 10%, #D7F6E8 0, transparent 30%),
        radial-gradient(circle at 90% 15%, #FFF0C5 0, transparent 28%),
        linear-gradient(145deg, #F3FFF9, #EAF8F7);
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Barra superior con botón de pausa y minijuego */
.top-bar {
    width: 100%;
    max-width: 820px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.brand-title {
    font-weight: 800;
    color: #176F65;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-pause {
    background: linear-gradient(135deg, var(--amarillo) 0%, #D69E2E 100%);
    color: #744210;
    border: none;
    padding: 10px 18px;
    font-weight: 800;
    border-radius: 14px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(236,201,75,0.4);
    transition: all 0.3s ease;
}
.btn-pause:hover { transform: translateY(-2px) scale(1.05); }

.quiz-container {
    width: 100%;
    max-width: 820px;
    background: white;
    padding: 35px;
    border-radius: 28px;
    border: 2px solid var(--borde);
    box-shadow: 0 18px 45px rgba(29, 112, 101, 0.10);
    animation: aparecer .5s ease;
    position: relative;
}

@keyframes aparecer {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.quiz-header {
    text-align: center;
    margin-bottom: 30px;
}

.app-icon {
    width: 78px;
    height: 78px;
    border-radius: 24px;
    background: linear-gradient(135deg, #D5F7E8, #FFF1C7);
    border: 2px solid #BCE8D6;
    margin: 0 auto 18px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 42px;
    box-shadow: 0 8px 20px rgba(42,157,143,.12);
    animation: floatIcon 3s ease-in-out infinite;
}

@keyframes floatIcon {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

.quiz-header h1 {
    font-size: clamp(1.6rem, 4vw, 2.2rem);
    font-weight: 900;
    margin: 0 0 10px;
    color: #176F65;
}

.quiz-header p {
    font-size: 1.04rem;
    color: var(--gris);
    line-height: 1.6;
}

.welcome-box {
    padding: 25px;
    border-radius: 20px;
    background: #F0FBF6;
    border: 2px solid #D4EEE3;
    text-align: center;
    margin-bottom: 25px;
}

.welcome-box p {
    line-height: 1.7;
    margin: 10px 0;
}

.section-title {
    margin: 28px 0 18px;
    padding-bottom: 12px;
    border-bottom: 3px solid #CBEFE1;
    color: #177C70;
    font-size: 1.25rem;
    font-weight: 900;
}

/* Tarjetas paso a paso dinámicas */
.question-step { display: none; }
.question-step.active { display: block; animation: cardBounce 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

@keyframes cardBounce {
    0% { opacity: 0; transform: scale(0.95) translateY(15px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

.question-card {
    background: #F8FCFA;
    border: 2px solid #E1EFE9;
    border-radius: 20px;
    padding: 22px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}

.question-card h3 {
    margin: 0;
    font-size: 1.15rem;
    line-height: 1.65;
    color: #245B52;
    font-weight: 800;
}

.question-number {
    display: inline-block;
    background: #E0F5EA;
    color: #16766B;
    border-radius: 30px;
    padding: 6px 13px;
    font-size: .8rem;
    font-weight: 900;
    margin-bottom: 12px;
}

.options-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 17px;
}

/* Animación súper fluida al pasar el cursor y seleccionar */
.option-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px;
    background: white;
    border: 2px solid #E0EAE5;
    border-radius: 13px;
    cursor: pointer;
    font-size: .95rem;
    line-height: 1.55;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.option-label:hover {
    background: #F0FBF6;
    border-color: var(--turquesa);
    transform: translateX(8px) scale(1.01);
    box-shadow: 0 6px 20px rgba(42,157,143,0.15);
}

.option-label input:checked + span, .option-label:has(input:checked) {
    background: #E1F7ED;
    border-color: var(--turquesa);
    box-shadow: 0 3px 12px rgba(42,157,143,.15);
    font-weight: 700;
}

.option-label input {
    margin-top: 4px;
    accent-color: var(--turquesa);
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    cursor: pointer;
}

.option-letter {
    font-weight: 900;
    color: #177C70;
}

.btn-submit {
    display: block;
    width: 100%;
    padding: 16px 20px;
    border: none;
    border-radius: 15px;
    background: linear-gradient(135deg, #2A9D8F, #168477);
    color: white;
    font-family: 'Nunito', sans-serif;
    font-size: 1.05rem;
    font-weight: 900;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
    box-shadow: 0 6px 16px rgba(42,157,143,.22);
    transition: .2s;
}

.btn-submit:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #238F82, #126C62);
}

.btn-secondary {
    background: #EDF5F2;
    color: #216C62;
    box-shadow: none;
}

.btn-secondary:hover {
    background: #DDEEE8;
}

.buttons-row {
    display: flex;
    gap: 12px;
    margin-top: 20px;
}

.buttons-row a, .buttons-row button {
    flex: 1;
}

.progress-wrapper {
    margin: 0 0 25px;
    padding: 17px;
    border-radius: 16px;
    background: #F1FAF5;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    font-size: .9rem;
    font-weight: 800;
    margin-bottom: 10px;
}

.progress-track {
    height: 12px;
    background: #D8EDE4;
    border-radius: 20px;
    overflow: hidden;
}

.progress-fill {
    width: 10%;
    height: 100%;
    background: linear-gradient(90deg, #2A9D8F, #F4C95D);
    border-radius: 20px;
    transition: width .4s ease;
}

.result-hero {
    padding: 28px;
    text-align: center;
    border-radius: 22px;
    background: linear-gradient(135deg, #E1F7EB, #FFF6D9);
    border: 2px solid #BCE8D6;
    margin-bottom: 25px;
}

.result-hero h2 {
    color: #176F65;
    font-size: 1.65rem;
}

.result-hero .emoji {
    font-size: 55px;
}

.result-hero .percentage {
    font-size: 1.5rem;
    font-weight: 900;
    color: #197F72;
}

.result-card {
    border: 2px solid #E1EFE9;
    background: #FAFDFC;
    padding: 18px;
    border-radius: 16px;
    margin-bottom: 14px;
}

.result-card h3 {
    margin: 0 0 10px;
    color: #24675E;
    font-size: 1rem;
}

.result-track {
    height: 13px;
    background: #E1EEE8;
    border-radius: 20px;
    overflow: hidden;
}

.result-fill {
    height: 100%;
    background: linear-gradient(90deg, #2A9D8F, #F4C95D);
    border-radius: 20px;
}

.result-card p {
    margin-bottom: 0;
    font-size: .9rem;
    color: var(--gris);
}

.tip-box {
    margin: 22px 0;
    padding: 20px;
    background: #FFF9E8;
    border: 2px solid #F5E5B7;
    border-radius: 17px;
    line-height: 1.65;
}

.error-box {
    background: #FFF0EF;
    border: 2px solid #F3B6AF;
    color: #9D3029;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 800;
}

.small-note {
    color: var(--gris);
    font-size: .86rem;
    text-align: center;
    margin-top: 15px;
}

/* Modal del Minijuego Dinosaurio Realista */
#modal-pausa {
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(8px);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-content {
    background: #1E293B;
    color: #F8FAFC;
    padding: 30px;
    border-radius: 24px;
    width: 90%;
    max-width: 500px;
    text-align: center;
    border: 4px solid var(--amarillo);
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
}
canvas {
    background: #0F172A;
    border: 3px solid #38BDF8;
    border-radius: 12px;
    margin-top: 15px;
    box-shadow: inset 0 0 20px rgba(56, 189, 248, 0.2);
}

:focus-visible {
    outline: 3px solid #E7A32F;
    outline-offset: 3px;
}

@media(max-width: 600px) {
    .quiz-container {
        padding: 20px 15px;
        margin: 10px auto;
    }
    .buttons-row {
        flex-direction: column;
    }
}

@media(prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        transition-duration: .01ms !important;
    }
}

</style>
</head>

<body>

<?php if (!$guardado_exitoso && $iniciar_quiz): ?>
<div class="top-bar">
    <span class="brand-title">🐧 Pinguich Challenge</span>
    <button class="btn-pause" onclick="abrirPausa()">⏸️ Descanso & Dino Game (10 min)</button>
</div>
<?php endif; ?>

<main class="quiz-container">

<?php if ($guardado_exitoso): ?>

    <!-- ==================================
         PANTALLA DE RESULTADOS
         ================================== -->

    <div class="quiz-header">
        <div class="app-icon">🐧</div>
        <h1>¡Aventura completada!</h1>
        <p>
            ¡Excelente trabajo,
            <?php echo escapar($nombre_usuario); ?>!
            Descubrimos algunas de tus preferencias y fortalezas.
        </p>
    </div>

    <div class="result-hero">

        <div class="emoji">
            <?php
            foreach ($perfiles_ganadores as $letra) {
                echo escapar($perfiles[$letra]["icono"]) . " ";
            }
            ?>
        </div>

        <h2>
            <?php
            echo count($perfiles_ganadores) > 1
                ? "¡Tienes varios perfiles destacados!"
                : "¡Tu perfil destacado!";
            ?>
        </h2>

        <h2><?php echo escapar($perfil_predominante); ?></h2>

        <div class="percentage">
            <?php
            echo number_format(
                $porcentaje_predominante,
                1
            );
            ?>%
        </div>

        <p>Porcentaje de respuestas de tu perfil más frecuente.</p>

    </div>

    <?php foreach ($perfiles_ganadores as $letra): ?>

        <div class="tip-box">
            <h3>
                <?php echo escapar($perfiles[$letra]["icono"]); ?>
                <?php echo escapar($perfiles[$letra]["nombre"]); ?>
            </h3>

            <p>
                <?php echo escapar($perfiles[$letra]["descripcion"]); ?>
            </p>

            <strong>🌟 Un reto para ti:</strong>

            <p>
                <?php echo escapar($perfiles[$letra]["consejo"]); ?>
            </p>
        </div>

    <?php endforeach; ?>

    <h2 class="section-title">📊 Tus resultados</h2>

    <?php foreach ($puntajes as $letra => $puntos): ?>

        <?php
        $porcentaje = ($puntos / $total_preguntas) * 100;
        ?>

        <div class="result-card">

            <h3>
                <?php echo escapar($perfiles[$letra]["icono"]); ?>
                <?php echo escapar($perfiles[$letra]["nombre"]); ?>
            </h3>

            <div class="result-track">
                <div
                    class="result-fill"
                    style="width: <?php echo $porcentaje; ?>%">
                </div>
            </div>

            <p>
                <?php echo $puntos; ?>
                de <?php echo $total_preguntas; ?> respuestas
                (<?php echo number_format($porcentaje, 1); ?>%)
            </p>

        </div>

    <?php endforeach; ?>

    <div class="tip-box">
        <strong>💚 Mensaje de Penguich</strong>

        <p>
            Todos tenemos diferentes habilidades.
            Este cuestionario muestra las actividades
            que más te llaman la atención, pero no
            limita lo que puedes aprender o lograr.
        </p>
    </div>

    <div class="buttons-row">

        <a href="usuario.php" class="btn-submit" style="text-decoration:none;">
            🏠 Volver al panel
        </a>

        <a
            href="procesar_inteligencia.php?iniciar=1"
            class="btn-submit btn-secondary" style="text-decoration:none;">
            🔄 Repetir test
        </a>

    </div>

<?php elseif (!$iniciar_quiz): ?>

    <!-- ==================================
         PANTALLA DE BIENVENIDA
         ================================== -->

    <div class="quiz-header">

        <div class="app-icon">🐧</div>

        <h1>Mis habilidades y aptitudes</h1>

        <p>
            ¡Hola, <?php echo escapar($nombre_usuario); ?>!
            Acompaña a Penguich en una aventura
            para descubrir lo que más disfrutas hacer.
        </p>

    </div>

    <div class="welcome-box">

        <h2>🌟 ¡Descubre tus talentos!</h2>

        <p>
            Te esperan 10 preguntas interactivas sobre juegos,
            inventos, creatividad y resolución de retos.
        </p>

        <p>
            No hay respuestas correctas ni incorrectas.
            Elige lo que más se parezca a ti.
        </p>

        <p><strong>🎯 ¡Cada forma de aprender y crear es valiosa!</strong></p>

    </div>

    <a
        href="procesar_inteligencia.php?iniciar=1"
        class="btn-submit" style="text-decoration:none;">
        🚀 ¡Comenzar aventura!
    </a>

    <p class="small-note">
        Puedes responder a tu ritmo con tarjetas paso a paso.
    </p>

<?php else: ?>

    <!-- ==================================
         PANTALLA DEL CUESTIONARIO (WIZARD PASO A PASO)
         ================================== -->

    <div class="quiz-header" style="margin-bottom: 15px;">
        <div class="app-icon" style="width: 60px; height: 60px; font-size: 2.2rem; margin-bottom: 8px;">🐧</div>
        <h2 style="margin:0; color: #176F65; font-size: 1.5rem;">Habilidades y Aptitudes</h2>
        <p style="margin: 6px 0 0 0; font-size: 0.95rem; color: var(--gris); font-weight: 700;">
            Pregunta <span id="current-q-num">1</span> de <?php echo $total_preguntas; ?>
        </p>
    </div>

    <?php if ($error !== ""): ?>
        <div class="error-box">
            ⚠️ <?php echo escapar($error); ?>
        </div>
    <?php endif; ?>

    <div class="progress-wrapper">
        <div class="progress-info">
            <span>🌟 Tu avance</span>
            <span id="progress-text">1 / <?php echo $total_preguntas; ?></span>
        </div>
        <div class="progress-track">
            <div class="progress-fill" id="progress-fill"></div>
        </div>
    </div>

    <form
        action="procesar_inteligencia.php"
        method="POST"
        id="quiz-form">

        <?php
        $numero = 0;
        foreach ($preguntas as $clave => $pregunta):
            $numero++;
            $es_activa = ($numero === 1) ? "active" : "";
        ?>

            <div class="question-step <?php echo $es_activa; ?>" data-step="<?php echo $numero; ?>">
                <div class="question-card">
                    <span class="question-number">
                        Pregunta <?php echo $numero; ?> de <?php echo $total_preguntas; ?>
                    </span>

                    <h3>
                        <?php echo escapar($pregunta["pregunta"]); ?>
                    </h3>

                    <div class="options-group">
                        <?php foreach ($pregunta["opciones"] as $letra => $texto): ?>
                            <label class="option-label">
                                <input
                                    type="radio"
                                    name="<?php echo escapar($clave); ?>"
                                    value="<?php echo escapar($letra); ?>"
                                    required
                                >
                                <span>
                                    <span class="option-letter"><?php echo escapar($letra); ?>)</span>
                                    <?php echo escapar($texto); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="buttons-row">
                    <?php if ($numero > 1): ?>
                        <button type="button" class="btn-submit btn-secondary" onclick="anteriorPaso(<?php echo $numero; ?>)">⬅️ Anterior</button>
                    <?php endif; ?>

                    <?php if ($numero < $total_preguntas): ?>
                        <button type="button" class="btn-submit" onclick="siguientePaso(<?php echo $numero; ?>)">Siguiente ➡️</button>
                    <?php else: ?>
                        <button type="submit" name="enviar_test" value="1" class="btn-submit" style="background: linear-gradient(135deg, #48BB78, #38A169);">
                            💾 Guardar mi diagnóstico
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        <?php endforeach; ?>

    </form>

<?php endif; ?>

</main>

<!-- Modal de Pausa con Minijuego de Dinosaurio y Puntitos Interactivos -->
<div id="modal-pausa">
    <div class="modal-content">
        <h2>🦖 Dino Runner: Zona Pinguich</h2>
        <p style="margin: 5px 0;">Tiempo restante: <span id="timer" style="color: var(--amarillo); font-size: 1.3rem; font-weight: bold;">10:00</span></p>
        <p style="font-size: 0.9rem; color: #38BDF8; font-weight: 700;">⭐ Puntos: <span id="score" style="color: #48BB78; font-size: 1.2rem;">0</span></p>
        <p style="font-size: 0.8rem; color: #94A3B8; margin-top:0;">Presiona <strong>ESPACIO</strong> o haz clic en <strong>¡SALTAR!</strong> para esquivar cactus y atrapar los puntitos brillantes.</p>
        
        <canvas id="dinoCanvas" width="440" height="160"></canvas>
        
        <div style="margin-top: 20px; display: flex; gap: 12px;">
            <button class="btn-pause" style="flex:1;" onclick="saltarDino()">¡SALTAR! 🦖</button>
            <button class="btn-submit" style="flex:1; padding: 12px;" onclick="cerrarPausa()">Continuar Test ▶️</button>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    const totalSteps = <?php echo $total_preguntas; ?>;

    function actualizarProgresoWizard() {
        const barra = document.getElementById("progress-fill");
        const texto = document.getElementById("progress-text");
        if (barra && texto) {
            let porcentaje = (currentStep / totalSteps) * 100;
            barra.style.width = porcentaje + "%";
            texto.textContent = currentStep + " / " + totalSteps;
            document.getElementById('current-q-num').innerText = currentStep;
        }
    }

    function siguientePaso(step) {
        const currentCard = document.querySelector(`.question-step[data-step="${step}"]`);
        const radios = currentCard.querySelectorAll('input[type="radio"]');
        let seleccionado = false;
        radios.forEach(r => { if(r.checked) seleccionado = true; });

        if(!seleccionado) {
            alert("🐧 Por favor selecciona una opción antes de continuar.");
            return;
        }

        currentCard.classList.remove('active');
        currentStep = step + 1;
        let siguienteCard = document.querySelector(`.question-step[data-step="${currentStep}"]`);
        if(siguienteCard) {
            siguienteCard.classList.add('active');
            actualizarProgresoWizard();
            window.scrollTo({ top: 150, behavior: 'smooth' });
        }
    }

    function anteriorPaso(step) {
        document.querySelector(`.question-step[data-step="${step}"]`).classList.remove('active');
        currentStep = step - 1;
        document.querySelector(`.question-step[data-step="${currentStep}"]`).classList.add('active');
        actualizarProgresoWizard();
        window.scrollTo({ top: 150, behavior: 'smooth' });
    }

    // Lógica del Temporizador y Minijuego del Dinosaurio
    let timerInterval;
    let tiempoRestante = 600; // 10 minutos

    function abrirPausa() {
        document.getElementById('modal-pausa').style.display = 'flex';
        iniciarDinoJuego();
        if(!timerInterval) {
            timerInterval = setInterval(() => {
                tiempoRestante--;
                let m = Math.floor(tiempoRestante / 60);
                let s = tiempoRestante % 60;
                document.getElementById('timer').innerText = `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
                if(tiempoRestante <= 0) { clearInterval(timerInterval); cerrarPausa(); }
            }, 1000);
        }
    }

    function cerrarPausa() {
        document.getElementById('modal-pausa').style.display = 'none';
        juegoActivo = false;
    }

    let canvas, ctx;
    let dino = { x: 40, y: 105, w: 30, h: 35, vy: 0, gravity: 0.7, jumpForce: -10, grounded: true };
    let cactus = { x: 460, y: 110, w: 18, h: 30, speed: 5 };
    let puntito = { x: 480, y: 70, radius: 7, speed: 5, activo: true };
    let puntosJuego = 0;
    let juegoActivo = false;

    function iniciarDinoJuego() {
        canvas = document.getElementById('dinoCanvas');
        ctx = canvas.getContext('2d');
        juegoActivo = true;
        puntosJuego = 0;
        document.getElementById('score').innerText = puntosJuego;
        dino.y = 105;
        dino.vy = 0;
        cactus.x = 460;
        puntito.x = 500;
        puntito.activo = true;
        loopDino();
    }

    function saltarDino() {
        if (dino.grounded) {
            dino.vy = dino.jumpForce;
            dino.grounded = false;
        }
    }

    window.addEventListener('keydown', (e) => {
        if(e.code === 'Space') { 
            saltarDino(); 
            if(document.getElementById('modal-pausa').style.display === 'flex') {
                e.preventDefault(); 
            }
        }
    });

    function loopDino() {
        if(!juegoActivo) return;

        ctx.fillStyle = '#0F172A';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Suelo
        ctx.fillStyle = '#38BDF8';
        ctx.fillRect(0, 140, canvas.width, 20);

        // Física del Dinosaurio
        dino.vy += dino.gravity;
        dino.y += dino.vy;
        if (dino.y >= 105) {
            dino.y = 105;
            dino.vy = 0;
            dino.grounded = true;
        }

        // Dibujar Dino
        ctx.fillStyle = '#48BB78';
        ctx.fillRect(dino.x + 5, dino.y + 10, 20, 20);
        ctx.fillRect(dino.x + 16, dino.y, 12, 14);
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(dino.x + 22, dino.y + 3, 3, 3);

        // Cactus
        cactus.x -= cactus.speed;
        if (cactus.x < -30) {
            cactus.x = canvas.width + Math.random() * 60;
            cactus.speed = 5 + Math.random() * 1.5;
        }
        ctx.fillStyle = '#E53E3E';
        ctx.fillRect(cactus.x, cactus.y, cactus.w, cactus.h);

        // Puntito Interactivo (Suma puntos reales al tocar el dino)
        puntito.x -= puntito.speed;
        if (puntito.x < -20) {
            puntito.x = canvas.width + Math.random() * 100;
            puntito.y = 50 + Math.random() * 50;
            puntito.activo = true;
        }

        if (puntito.activo) {
            ctx.fillStyle = '#F4C95D';
            ctx.beginPath();
            ctx.arc(puntito.x, puntito.y, puntito.radius, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = '#D69E2E';
            ctx.lineWidth = 2;
            ctx.stroke();

            // Colisión de los puntitos con el dinosaurio
            let distX = (dino.x + dino.w/2) - puntito.x;
            let distY = (dino.y + dino.h/2) - puntito.y;
            let distancia = Math.sqrt(distX * distX + distY * distY);

            if (distancia < puntito.radius + 15) {
                puntosJuego += 10;
                document.getElementById('score').innerText = puntosJuego;
                puntito.activo = false;
            }
        }

        // Colisión con cactus
        if (
            dino.x < cactus.x + cactus.w &&
            dino.x + dino.w > cactus.x &&
            dino.y < cactus.y + cactus.h &&
            dino.y + dino.h > cactus.y
        ) {
            puntosJuego = Math.max(0, puntosJuego - 5);
            document.getElementById('score').innerText = puntosJuego;
            cactus.x = canvas.width + 50;
        }

        requestAnimationFrame(loopDino);
    }
</script>

</body>
</html>