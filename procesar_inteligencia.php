
<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = new mysqli("localhost", "root", "", "penguich");
    $db->set_charset("utf8mb4");

    $nombre = (string) $_SESSION["usuario"];
    $stmt = $db->prepare(
        "SELECT id FROM usuarios WHERE usuario = ? LIMIT 1"
    );
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->bind_result($usuario_id);

    if (!$stmt->fetch()) {
        $stmt->close();
        header("Location: login.php");
        exit;
    }
    $stmt->close();
} catch (Throwable $e) {
    http_response_code(500);
    exit("No fue posible conectar con Penguich.");
}

function e($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

/*
 A = Maker / Ingeniería
 B = Creatividad / Diseño
 C = Liderazgo / Social
 D = Ciencia / Investigación

 Las respuestas representan intereses elegidos,
 no diagnósticos de inteligencia.
*/

$perfiles = [
    "A" => [
        "nombre" => "Inventos y construcción",
        "icono" => "🛠️",
        "descripcion" => "Te llamó la atención construir, probar mecanismos y resolver problemas prácticos.",
        "reto" => "Construye un puente de papel y descubre cuánto peso soporta.",
        "actividades" => [
            "Crear vehículos con materiales reciclados",
            "Construir estructuras con bloques",
            "Diseñar circuitos sencillos supervisados"
        ]
    ],
    "B" => [
        "nombre" => "Arte y creatividad",
        "icono" => "🎨",
        "descripcion" => "Elegiste imaginar, dibujar y crear cosas originales.",
        "reto" => "Diseña un personaje que tenga un superpoder completamente nuevo.",
        "actividades" => [
            "Diseñar personajes e historietas",
            "Crear esculturas y maquetas",
            "Inventar historias mediante dibujos"
        ]
    ],
    "C" => [
        "nombre" => "Comunicación y trabajo en equipo",
        "icono" => "🤝",
        "descripcion" => "Te interesó compartir ideas, colaborar y organizar actividades.",
        "reto" => "Organiza un juego cooperativo donde todos tengan una misión.",
        "actividades" => [
            "Teatro y expresión corporal",
            "Juegos deportivos cooperativos",
            "Organización de proyectos en equipo"
        ]
    ],
    "D" => [
        "nombre" => "Ciencia y descubrimiento",
        "icono" => "🔬",
        "descripcion" => "Elegiste investigar, resolver misterios y hacer preguntas.",
        "reto" => "Observa una planta durante varios días y registra sus cambios.",
        "actividades" => [
            "Experimentos científicos seguros",
            "Observación de plantas y naturaleza",
            "Acertijos y misiones de detectives"
        ]
    ]
];

/*
 Cada imagen se coloca en images/habilidades/
 Los títulos son breves para la lectura por voz.
*/
$preguntas = [
    "H1" => [
        "titulo" => "¡Un juguete misterioso!",
        "detalle" => "Te regalaron un juguete que viene en piezas. ¿Qué haces primero?",
        "emoji" => "🧩",
        "imagen" => "01.png",
        "opciones" => [
            "A" => "🔧 Intento descubrir cómo se arma.",
            "B" => "🎨 Imagino cómo hacerlo más bonito.",
            "C" => "🤝 Invito a alguien a construirlo conmigo.",
            "D" => "🔍 Investigo cómo funciona."
        ]
    ],
    "H2" => [
        "titulo" => "¡La caja de los inventos!",
        "detalle" => "Encontraste cartón, botellas y otros materiales. ¿Qué crearías?",
        "emoji" => "📦",
        "imagen" => "02.png",
        "opciones" => [
            "A" => "🚗 Un vehículo con ruedas.",
            "B" => "🏰 Un castillo o personaje fantástico.",
            "C" => "🎲 Un juego para toda mi familia.",
            "D" => "🌱 Un laboratorio para observar plantas."
        ]
    ],
    "H3" => [
        "titulo" => "¡Una misión en equipo!",
        "detalle" => "Tus amigos y tú tienen un proyecto. ¿Qué parte elegirías?",
        "emoji" => "🚀",
        "imagen" => "03.png",
        "opciones" => [
            "A" => "🛠️ Resolver problemas y construir.",
            "B" => "🖍️ Diseñar los dibujos y decoraciones.",
            "C" => "🎤 Organizar y presentar las ideas.",
            "D" => "📚 Investigar datos sorprendentes."
        ]
    ],
    "H4" => [
        "titulo" => "¡Un descubrimiento en el parque!",
        "detalle" => "Encuentras un insecto que nunca habías visto. ¿Qué harías?",
        "emoji" => "🦋",
        "imagen" => "04.png",
        "opciones" => [
            "A" => "⚙️ Observo cómo mueve sus patas y alas.",
            "B" => "🎨 Dibujo sus colores y formas.",
            "C" => "👫 Invito a mis amigos a observarlo.",
            "D" => "🔬 Investigo su nombre y dónde vive."
        ]
    ],
    "H5" => [
        "titulo" => "¡El gran festival de talentos!",
        "detalle" => "Puedes participar en una actividad especial. ¿Cuál eliges?",
        "emoji" => "🏆",
        "imagen" => "05.png",
        "opciones" => [
            "A" => "🤖 Crear un invento sorprendente.",
            "B" => "🖌️ Pintar una obra de arte.",
            "C" => "🎭 Actuar o presentar una historia.",
            "D" => "🧠 Resolver acertijos y misterios."
        ]
    ],
    "H6" => [
        "titulo" => "¡Un juego casi imposible!",
        "detalle" => "Un videojuego se vuelve difícil. ¿Qué intentas?",
        "emoji" => "🎮",
        "imagen" => "06.png",
        "opciones" => [
            "A" => "🕹️ Pruebo nuevos movimientos.",
            "B" => "👀 Busco pistas en los dibujos.",
            "C" => "💬 Pido ideas y comparto estrategias.",
            "D" => "🧩 Analizo las reglas del juego."
        ]
    ],
    "H7" => [
        "titulo" => "¡Una tablet llena de aventuras!",
        "detalle" => "Puedes elegir una actividad digital. ¿Cuál te emociona?",
        "emoji" => "💻",
        "imagen" => "07.png",
        "opciones" => [
            "A" => "🏗️ Construir máquinas y ciudades.",
            "B" => "🎨 Crear dibujos y personajes.",
            "C" => "🤝 Resolver misiones con amigos.",
            "D" => "🌍 Explorar mapas y misterios."
        ]
    ],
    "H8" => [
        "titulo" => "¡Elige tu superpoder!",
        "detalle" => "Puedes usar un superpoder para ayudar a tu escuela. ¿Cuál quieres?",
        "emoji" => "🦸",
        "imagen" => "08.png",
        "opciones" => [
            "A" => "🔧 Reparar cualquier objeto.",
            "B" => "🌈 Transformar todo con arte.",
            "C" => "💚 Ayudar a que todos colaboren.",
            "D" => "🔭 Descubrir respuestas a misterios."
        ]
    ],
    "H9" => [
        "titulo" => "¡Enséñale algo a tu amigo!",
        "detalle" => "Descubriste un truco genial. ¿Cómo se lo enseñarías?",
        "emoji" => "💡",
        "imagen" => "09.png",
        "opciones" => [
            "A" => "👐 Le enseño cómo hacerlo.",
            "B" => "🖍️ Le dibujo una explicación.",
            "C" => "🗣️ Se lo cuento paso a paso.",
            "D" => "🔍 Le explico por qué funciona."
        ]
    ],
    "H10" => [
        "titulo" => "¡Tu fiesta soñada!",
        "detalle" => "Puedes elegir una aventura para celebrar. ¿Cuál sería?",
        "emoji" => "🎉",
        "imagen" => "10.png",
        "opciones" => [
            "A" => "🤖 Una fiesta de inventores.",
            "B" => "🎭 Un festival de arte y disfraces.",
            "C" => "🏅 Juegos y desafíos en equipo.",
            "D" => "🕵️ Una misión de detectives."
        ]
    ]
];

$total = count($preguntas);
$error = "";
$guardado = false;
$puntajes = array_fill_keys(["A","B","C","D"], 0);
$ganadores = [];
$porcentaje_maximo = 0;
$iniciar = ($_GET["iniciar"] ?? "") === "1";
$respuestas_anteriores = [];

if (empty($_SESSION["csrf_habilidades"])) {
    $_SESSION["csrf_habilidades"] = bin2hex(random_bytes(32));
}

try {
    $db->query("
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
} catch (Throwable $e) {
    http_response_code(500);
    exit("No fue posible preparar la tabla de resultados.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["enviar_test"])) {

    $iniciar = true;
    $token = (string)($_POST["csrf"] ?? "");

    if (!hash_equals($_SESSION["csrf_habilidades"], $token)) {
        $error = "Tu sesión cambió. Actualiza la página e inténtalo de nuevo.";
    } else {
        $validas = true;

        foreach ($preguntas as $clave => $pregunta) {
            $respuesta = $_POST[$clave] ?? "";

            if (!is_string($respuesta)
                || !array_key_exists($respuesta, $puntajes)) {
                $validas = false;
                break;
            }

            $respuestas_anteriores[$clave] = $respuesta;
            $puntajes[$respuesta]++;
        }

        if (!$validas) {
            $error = "Responde las diez misiones antes de terminar.";
        } else {
            $maximo = max($puntajes);
            $ganadores = array_keys($puntajes, $maximo);

            $nombres_ganadores = [];
            foreach ($ganadores as $letra) {
                $nombres_ganadores[] = $perfiles[$letra]["nombre"];
            }

            $perfil_predominante = implode(
                " / ",
                $nombres_ganadores
            );

            $porcentaje_maximo = ($maximo / $total) * 100;

            $respuestas_json = json_encode(
                $respuestas_anteriores,
                JSON_UNESCAPED_UNICODE
            );

            try {
                $stmt = $db->prepare("
                    INSERT INTO test_inteligencias (
                        usuario_id,
                        puntaje_maker,
                        puntaje_creativo,
                        puntaje_liderazgo,
                        puntaje_cientifico,
                        perfil_predominante,
                        porcentaje_predominante,
                        respuestas_json
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "iiiiisds",
                    $usuario_id,
                    $puntajes["A"],
                    $puntajes["B"],
                    $puntajes["C"],
                    $puntajes["D"],
                    $perfil_predominante,
                    $porcentaje_maximo,
                    $respuestas_json
                );

                $stmt->execute();
                $stmt->close();

                $guardado = true;
                $iniciar = false;
                $_SESSION["csrf_habilidades"] =
                    bin2hex(random_bytes(32));

            } catch (Throwable $e) {
                $error = "No se pudieron guardar tus respuestas. Intenta nuevamente.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Penguich | Mis habilidades y aptitudes</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body class="habilidades-page">

<div class="hab-wrapper">

<header class="hab-topbar">
    <a href="usuario.php" class="hab-back">
        ← Mi aventura
    </a>

    <div class="hab-logo">🐧 Penguich</div>

    <button type="button" id="toggleVoice"
            class="hab-top-button" aria-pressed="true">
        🔊 Voz activada
    </button>
</header>

<?php if ($guardado): ?>

<main class="hab-panel hab-results">
    <div class="hab-big-emoji">🏆✨</div>
    <span class="hab-tag">MIS DESCUBRIMIENTOS</span>

    <h1>¡Completaste las 10 misiones!</h1>

    <p class="hab-intro">
        ¡Fantástico, <?php echo e($nombre); ?>!
        Estas son las actividades que más llamaron tu atención.
        ¡Todavía hay muchos talentos por descubrir!
    </p>

    <div class="hab-result-hero">
        <div class="hab-big-emoji">
            <?php foreach ($ganadores as $letra): ?>
                <?php echo $perfiles[$letra]["icono"]; ?>
            <?php endforeach; ?>
        </div>

        <h2>🌟 Tus intereses destacados</h2>

        <?php foreach ($ganadores as $letra): ?>
            <h3><?php echo e($perfiles[$letra]["nombre"]); ?></h3>
            <p><?php echo e($perfiles[$letra]["descripcion"]); ?></p>
        <?php endforeach; ?>
    </div>

    <h2>📊 Tus elecciones</h2>

    <div class="hab-result-grid">
        <?php foreach ($perfiles as $letra => $perfil): ?>
            <?php
                $puntos = $puntajes[$letra];
                $porcentaje = $puntos / $total * 100;
            ?>
            <div class="hab-result-card">
                <div class="hab-result-title">
                    <strong>
                        <?php echo $perfil["icono"]; ?>
                        <?php echo e($perfil["nombre"]); ?>
                    </strong>
                    <span><?php echo $puntos; ?>/<?php echo $total; ?></span>
                </div>

                <div class="hab-track">
                    <div style="width:<?php echo $porcentaje; ?>%"></div>
                </div>

                <small>
                    <?php echo number_format($porcentaje, 0); ?>%
                    de tus elecciones
                </small>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>🚀 Tus próximas aventuras</h2>

    <div class="hab-activity-grid">
        <?php foreach ($ganadores as $letra): ?>
            <article class="hab-activity-card">
                <div class="hab-activity-icon">
                    <?php echo $perfiles[$letra]["icono"]; ?>
                </div>

                <h3><?php echo e($perfiles[$letra]["nombre"]); ?></h3>

                <p><strong>Tu reto:</strong>
                    <?php echo e($perfiles[$letra]["reto"]); ?>
                </p>

                <ul>
                    <?php foreach ($perfiles[$letra]["actividades"] as $actividad): ?>
                        <li><?php echo e($actividad); ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>

    <p class="hab-disclaimer">
        💚 Este cuestionario explora intereses y preferencias.
        No es una prueba de inteligencia ni mide todas tus capacidades.
        Puedes disfrutar y desarrollar habilidades de cualquiera de las áreas.
    </p>

    <div class="hab-actions">
        <a href="usuario.php" class="hab-btn hab-primary">
            🏠 Volver a mi panel
        </a>

        <a href="procesar_inteligencia.php?iniciar=1"
           class="hab-btn hab-secondary">
            🔄 Jugar otra vez
        </a>
    </div>
</main>

<?php elseif (!$iniciar): ?>

<main class="hab-panel hab-welcome">

    <div class="hab-big-emoji">🐧🌟</div>

    <span class="hab-tag">MIS TALENTOS</span>

    <h1>¡Descubre tus habilidades y aptitudes!</h1>

    <p class="hab-intro">
        ¡Hola, <?php echo e($nombre); ?>!
        Penguich tiene diez misiones para descubrir
        qué aventuras, juegos y actividades te emocionan más.
    </p>

    <div class="hab-feature-grid">
        <div>🔊 <strong>Preguntas con voz</strong></div>
        <div>👆 <strong>Opciones grandes</strong></div>
        <div>🎨 <strong>Imágenes divertidas</strong></div>
        <div>🦖 <strong>Descanso con minijuego</strong></div>
    </div>

    <p class="hab-welcome-note">
        No hay respuestas correctas o incorrectas.
        ¡Lo importante es elegir lo que más te gusta!
    </p>

    <a href="procesar_inteligencia.php?iniciar=1"
       class="hab-btn hab-primary hab-start">
        🚀 ¡Comenzar mi aventura!
    </a>

</main>

<?php else: ?>

<main class="hab-panel">

    <div class="hab-quiz-heading">
        <div>
            <span class="hab-tag">MISIÓN DE DESCUBRIMIENTO</span>
            <h1>Mis habilidades y aptitudes</h1>
        </div>

        <button type="button" id="openBreak"
                class="hab-top-button">
            🦖 Tomar un descanso
        </button>
    </div>

    <?php if ($error): ?>
        <div class="hab-error" role="alert">
            <?php echo e($error); ?>
        </div>
    <?php endif; ?>

    <div class="hab-progress">
        <div class="hab-progress-label">
            <strong>
                Pregunta <span id="currentNumber">1</span>
                de <?php echo $total; ?>
            </strong>
            <span id="progressPercent">10%</span>
        </div>

        <div class="hab-track" id="quizProgress"
             role="progressbar"
             aria-valuemin="0"
             aria-valuemax="<?php echo $total; ?>"
             aria-valuenow="1">
            <div id="progressFill" style="width:10%"></div>
        </div>
    </div>

    <form method="POST" id="habilidadesForm"
          action="procesar_inteligencia.php" novalidate>

        <input type="hidden" name="csrf"
               value="<?php echo e($_SESSION["csrf_habilidades"]); ?>">

        <?php $numero = 0; ?>
        <?php foreach ($preguntas as $clave => $pregunta): ?>
            <?php $numero++; ?>

            <section class="hab-step"
                     data-index="<?php echo $numero - 1; ?>"
                     <?php echo $numero > 1 ? "hidden" : ""; ?>>

                <div class="hab-question-layout">

                    <div class="hab-question-content">
                        <span class="hab-mission">
                            ⭐ MISIÓN <?php echo $numero; ?>
                        </span>

                        <h2 id="habTitle<?php echo $numero; ?>">
                            <?php echo e($pregunta["titulo"]); ?>
                        </h2>

                        <p class="hab-question-detail">
                            <?php echo e($pregunta["detalle"]); ?>
                        </p>

                        <button type="button"
                                class="hab-speak"
                                data-speak="<?php echo $numero; ?>">
                            🔊 Escuchar pregunta
                        </button>
                    </div>

                    <div class="hab-image-area">
                        <img
                            src="images/habilidades/<?php echo e($pregunta["imagen"]); ?>"
                            alt=""
                            loading="lazy"
                            onerror="this.hidden=true;this.nextElementSibling.hidden=false;">

                        <div class="hab-image-fallback" hidden>
                            <span><?php echo $pregunta["emoji"]; ?></span>
                            <small>¡Imagina tu aventura!</small>
                        </div>
                    </div>

                </div>

                <fieldset class="hab-options">
                    <legend>¿Qué elegirías tú?</legend>

                    <?php foreach ($pregunta["opciones"] as $letra => $opcion): ?>
                        <label class="hab-option">
                            <input
                                type="radio"
                                name="<?php echo e($clave); ?>"
                                value="<?php echo e($letra); ?>"
                                <?php echo
                                    ($respuestas_anteriores[$clave] ?? "") === $letra
                                    ? "checked" : "";
                                ?>
                            >

                            <span class="hab-option-letter">
                                <?php echo $letra; ?>
                            </span>

                            <span class="hab-option-text">
                                <?php echo e($opcion); ?>
                            </span>

                            <span class="hab-option-check">✓</span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

            </section>
        <?php endforeach; ?>

        <div id="habMessage" class="hab-message"
             role="status" aria-live="polite"></div>

        <div class="hab-navigation">
            <button type="button" id="previousQuestion"
                    class="hab-btn hab-secondary" hidden>
                ← Anterior
            </button>

            <button type="button" id="nextQuestion"
                    class="hab-btn hab-primary">
                Siguiente →
            </button>

            <button type="submit" id="finishQuiz"
                    name="enviar_test" value="1"
                    class="hab-btn hab-primary" hidden>
                🎉 Descubrir mis talentos
            </button>
        </div>
    </form>

    <p class="hab-bottom-note">
        💚 Puedes responder a tu ritmo y cambiar tus elecciones.
    </p>

</main>

<?php endif; ?>

</div>

<!-- DESCANSO Y MINIJUEGO -->
<dialog id="breakDialog" class="hab-dialog">
    <div class="hab-dialog-content">
        <h2>🦖 ¡Descanso de aventura!</h2>

        <p>
            Puedes descansar, respirar o jugar un momento.
            Tu cuestionario seguirá donde lo dejaste.
        </p>

        <div class="hab-break-stats">
            <span>⭐ Puntos: <strong id="dinoScore">0</strong></span>
            <span>⏱️ Tiempo: <strong id="breakTimer">10:00</strong></span>
        </div>

        <canvas id="dinoCanvas" width="440" height="180"
                aria-label="Juego de dinosaurio que salta obstáculos">
        </canvas>

        <p class="hab-game-instructions">
            Presiona ESPACIO o ¡Saltar! para esquivar obstáculos.
            Recoge las estrellas para ganar puntos.
        </p>

        <div class="hab-actions">
            <button type="button" id="jumpButton"
                    class="hab-btn hab-secondary">
                🦖 ¡Saltar!
            </button>

            <button type="button" id="closeBreak"
                    class="hab-btn hab-primary">
                ▶️ Continuar
            </button>
        </div>
    </div>
</dialog>

<script>
document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    const form = document.getElementById("habilidadesForm");
    const voiceButton = document.getElementById("toggleVoice");
    const speechAvailable = "speechSynthesis" in window;
    let voiceEnabled = true;

    function stopVoice() {
        if (speechAvailable) {
            speechSynthesis.cancel();
        }
    }

    function speak(text) {
        if (!voiceEnabled || !speechAvailable) return;

        stopVoice();

        const speech = new SpeechSynthesisUtterance(text);
        speech.lang = "es-MX";
        speech.rate = 0.87;
        speech.pitch = 1.05;
        speech.volume = 1;

        const voices = speechSynthesis.getVoices();
        const spanish = voices.find(v =>
            v.lang.toLowerCase() === "es-mx"
        ) || voices.find(v =>
            v.lang.toLowerCase().startsWith("es")
        );

        if (spanish) speech.voice = spanish;

        speechSynthesis.speak(speech);
    }

    voiceButton.addEventListener("click", function () {
        voiceEnabled = !voiceEnabled;

        voiceButton.textContent = voiceEnabled
            ? "🔊 Voz activada"
            : "🔇 Voz desactivada";

        voiceButton.setAttribute(
            "aria-pressed",
            String(voiceEnabled)
        );

        if (!voiceEnabled) {
            stopVoice();
        } else if (form) {
            readQuestion();
        }
    });

    // CUESTIONARIO
    if (form) {
        const steps = Array.from(
            document.querySelectorAll(".hab-step")
        );

        const total = steps.length;
        let current = 0;

        const previous = document.getElementById("previousQuestion");
        const next = document.getElementById("nextQuestion");
        const finish = document.getElementById("finishQuiz");
        const message = document.getElementById("habMessage");
        const currentNumber = document.getElementById("currentNumber");
        const progressPercent = document.getElementById("progressPercent");
        const progressFill = document.getElementById("progressFill");
        const quizProgress = document.getElementById("quizProgress");

        const storageKey = "penguich-habilidades-" +
            <?php echo json_encode(hash("sha256", $nombre)); ?>;

        function readQuestion() {
            const title = steps[current].querySelector("h2");
            const detail = steps[current].querySelector(
                ".hab-question-detail"
            );

            if (title && detail) {
                speak(
                    title.textContent.trim() + ". " +
                    detail.textContent.trim()
                );
            }
        }

        function saveProgress() {
            const answers = {};

            steps.forEach(step => {
                const checked = step.querySelector(
                    'input[type="radio"]:checked'
                );

                if (checked) {
                    answers[checked.name] = checked.value;
                }
            });

            try {
                localStorage.setItem(storageKey, JSON.stringify({
                    current: current,
                    answers: answers
                }));
            } catch (error) {}
        }

        function restoreProgress() {
            try {
                const data = JSON.parse(
                    localStorage.getItem(storageKey)
                );

                if (!data) return;

                steps.forEach(step => {
                    step.querySelectorAll(
                        'input[type="radio"]'
                    ).forEach(input => {
                        if (data.answers &&
                            data.answers[input.name] === input.value) {
                            input.checked = true;
                        }
                    });
                });

                if (Number.isInteger(data.current)) {
                    current = Math.max(
                        0,
                        Math.min(total - 1, data.current)
                    );
                }
            } catch (error) {}
        }

        function render(readAloud = false) {
            stopVoice();

            steps.forEach((step, index) => {
                step.hidden = index !== current;
            });

            const percentage = Math.round(
                (current + 1) / total * 100
            );

            currentNumber.textContent = current + 1;
            progressPercent.textContent = percentage + "%";
            progressFill.style.width = percentage + "%";

            quizProgress.setAttribute(
                "aria-valuenow",
                String(current + 1)
            );

            previous.hidden = current === 0;
            next.hidden = current === total - 1;
            finish.hidden = current !== total - 1;

            message.textContent = "";
            saveProgress();

            if (readAloud) readQuestion();

            window.scrollTo({
                top: 0,
                behavior: window.matchMedia(
                    "(prefers-reduced-motion: reduce)"
                ).matches ? "auto" : "smooth"
            });
        }

        function answered(index) {
            return !!steps[index].querySelector(
                'input[type="radio"]:checked'
            );
        }

        next.addEventListener("click", function () {
            if (!answered(current)) {
                message.textContent =
                    "💛 Elige una opción para continuar.";

                const first = steps[current].querySelector(
                    'input[type="radio"]'
                );

                if (first) first.focus();
                return;
            }

            if (current < total - 1) {
                current++;
                render(true);
            }
        });

        previous.addEventListener("click", function () {
            if (current > 0) {
                current--;
                render(true);
            }
        });

        document.querySelectorAll("[data-speak]").forEach(button => {
            button.addEventListener("click", function () {
                const index = Number(button.dataset.speak) - 1;
                const title = steps[index].querySelector("h2");
                const detail = steps[index].querySelector(
                    ".hab-question-detail"
                );

                speak(
                    title.textContent.trim() + ". " +
                    detail.textContent.trim()
                );
            });
        });

        form.querySelectorAll('input[type="radio"]').forEach(input => {
            input.addEventListener("change", function () {
                message.textContent = "";
                saveProgress();
            });
        });

        form.addEventListener("submit", function (event) {
            for (let i = 0; i < total; i++) {
                if (!answered(i)) {
                    event.preventDefault();
                    current = i;
                    render(false);
                    message.textContent =
                        "💛 Responde esta misión antes de terminar.";
                    return;
                }
            }

            stopVoice();

            try {
                localStorage.removeItem(storageKey);
            } catch (error) {}
        });

        restoreProgress();
        render(false);
    }

    // MINIJUEGO DEL DINOSAURIO
    const dialog = document.getElementById("breakDialog");
    const openBreak = document.getElementById("openBreak");
    const closeBreak = document.getElementById("closeBreak");
    const jumpButton = document.getElementById("jumpButton");

    const canvas = document.getElementById("dinoCanvas");
    const ctx = canvas.getContext("2d");

    const scoreLabel = document.getElementById("dinoScore");
    const timerLabel = document.getElementById("breakTimer");

    let playing = false;
    let animationId = null;
    let timerId = null;
    let remaining = 600;
    let score = 0;
    let lastFrame = 0;

    const groundY = 150;

    const dino = {
        x: 48,
        y: 112,
        width: 34,
        height: 38,
        vy: 0,
        grounded: true
    };

    const obstacle = {
        x: 460,
        y: 124,
        width: 22,
        height: 26,
        speed: 3.5
    };

    const star = {
        x: 600,
        y: 82,
        radius: 10,
        active: true
    };

    function jump() {
        if (!playing || !dino.grounded) return;

        dino.vy = -10.5;
        dino.grounded = false;
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = "#EAF9F2";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Nubes
        ctx.fillStyle = "#FFFFFF";
        ctx.beginPath();
        ctx.arc(90, 40, 16, 0, Math.PI * 2);
        ctx.arc(108, 35, 20, 0, Math.PI * 2);
        ctx.arc(129, 43, 15, 0, Math.PI * 2);
        ctx.fill();

        // Suelo
        ctx.fillStyle = "#9ED9B5";
        ctx.fillRect(0, groundY, canvas.width, 30);

        // Dinosaurio
        ctx.fillStyle = "#249B76";
        ctx.fillRect(dino.x + 3, dino.y + 12, 26, 23);
        ctx.fillRect(dino.x + 17, dino.y, 17, 20);

        ctx.fillStyle = "#FFFFFF";
        ctx.fillRect(dino.x + 27, dino.y + 5, 4, 4);

        ctx.fillStyle = "#196C57";
        ctx.fillRect(dino.x + 7, dino.y + 33, 7, 5);
        ctx.fillRect(dino.x + 21, dino.y + 33, 7, 5);

        // Obstáculo
        ctx.fillStyle = "#E98D69";
        ctx.fillRect(
            obstacle.x,
            obstacle.y,
            obstacle.width,
            obstacle.height
        );

        ctx.fillStyle = "#C76B4E";
        ctx.fillRect(obstacle.x + 6, obstacle.y - 7, 10, 7);

        // Estrella
        if (star.active) {
            ctx.fillStyle = "#F4C95D";
            ctx.beginPath();

            for (let i = 0; i < 10; i++) {
                const angle = i * Math.PI / 5 - Math.PI / 2;
                const radius = i % 2 === 0 ? 12 : 5;

                const x = star.x + Math.cos(angle) * radius;
                const y = star.y + Math.sin(angle) * radius;

                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            }

            ctx.closePath();
            ctx.fill();
        }
    }

    function update(delta) {
        const factor = Math.min(delta / 16.67, 2);

        dino.vy += 0.55 * factor;
        dino.y += dino.vy * factor;

        if (dino.y >= groundY - dino.height) {
            dino.y = groundY - dino.height;
            dino.vy = 0;
            dino.grounded = true;
        }

        obstacle.x -= obstacle.speed * factor;

        if (obstacle.x < -30) {
            obstacle.x = canvas.width + 50 + Math.random() * 90;
        }

        star.x -= 3.5 * factor;

        if (star.x < -25) {
            star.x = canvas.width + 100 + Math.random() * 130;
            star.y = 70 + Math.random() * 30;
            star.active = true;
        }

        // Colisión con obstáculo
        const collision =
            dino.x + dino.width > obstacle.x &&
            dino.x < obstacle.x + obstacle.width &&
            dino.y + dino.height > obstacle.y &&
            dino.y < obstacle.y + obstacle.height;

        if (collision) {
            score = Math.max(0, score - 5);
            obstacle.x = canvas.width + 60;
        }

        // Recoger estrella
        const centerX = dino.x + dino.width / 2;
        const centerY = dino.y + dino.height / 2;

        const distance = Math.hypot(
            centerX - star.x,
            centerY - star.y
        );

        if (star.active && distance < 28) {
            score += 10;
            star.active = false;
        }

        scoreLabel.textContent = score;
    }

    function gameLoop(timestamp) {
        if (!playing) return;

        const delta = lastFrame ? timestamp - lastFrame : 16.67;
        lastFrame = timestamp;

        update(delta);
        draw();

        animationId = requestAnimationFrame(gameLoop);
    }

    function updateTimer() {
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;

        timerLabel.textContent =
            String(minutes).padStart(2, "0") + ":" +
            String(seconds).padStart(2, "0");
    }

    function stopGame() {
        playing = false;

        if (animationId !== null) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }

        if (timerId !== null) {
            clearInterval(timerId);
            timerId = null;
        }
    }

    function startGame() {
        stopGame();

        playing = true;
        remaining = 600;
        score = 0;
        lastFrame = 0;

        dino.y = groundY - dino.height;
        dino.vy = 0;
        dino.grounded = true;

        obstacle.x = 460;
        star.x = 600;
        star.active = true;

        scoreLabel.textContent = "0";
        updateTimer();
        draw();

        animationId = requestAnimationFrame(gameLoop);

        timerId = setInterval(function () {
            remaining = Math.max(0, remaining - 1);
            updateTimer();

            if (remaining === 0) {
                dialog.close();
            }
        }, 1000);
    }

    if (openBreak) {
        openBreak.addEventListener("click", function () {
            stopVoice();
            dialog.showModal();
            startGame();
        });
    }

    closeBreak.addEventListener("click", function () {
        dialog.close();
    });

    jumpButton.addEventListener("click", jump);

    window.addEventListener("keydown", function (event) {
        if (!dialog.open) return;

        if (event.code === "Space") {
            event.preventDefault();
            jump();
        }
    });

    dialog.addEventListener("close", stopGame);

    window.addEventListener("pagehide", function () {
        stopVoice();
        stopGame();
    });
});
</script>

</body>
</html>
