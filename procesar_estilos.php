
<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$nombre_usuario = (string) $_SESSION['usuario'];

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "penguich"
);

if ($conexion->connect_error) {
    http_response_code(500);
    exit("No se pudo conectar con la base de datos.");
}

$conexion->set_charset("utf8mb4");

function escapar($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

/*
 * Conservamos la tabla de resultados del proyecto.
 */
$conexion->query("
    CREATE TABLE IF NOT EXISTS test_estilo_aprendizaje (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        puntaje_visual INT DEFAULT 0,
        puntaje_auditivo INT DEFAULT 0,
        puntaje_kinestesico INT DEFAULT 0,
        puntaje_lectoescritura INT DEFAULT 0,
        estilo_predominante VARCHAR(30) NOT NULL,
        porcentaje_predominante DECIMAL(5,2) NOT NULL,
        fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$consulta = $conexion->prepare(
    "SELECT id FROM usuarios WHERE usuario = ? LIMIT 1"
);
$consulta->bind_param("s", $nombre_usuario);
$consulta->execute();
$consulta->bind_result($usuario_id);

if (!$consulta->fetch()) {
    $usuario_id = null;
}
$consulta->close();

if ($usuario_id === null) {
    $registro = $conexion->prepare(
        "INSERT INTO usuarios (usuario) VALUES (?)"
    );
    $registro->bind_param("s", $nombre_usuario);

    if (!$registro->execute()) {
        http_response_code(500);
        exit("No se pudo registrar el usuario.");
    }

    $usuario_id = $conexion->insert_id;
    $registro->close();
}

/*
 * Cada pregunta conserva cuatro respuestas A, B, C y D.
 * Las imágenes se colocan en:
 * images/quiz/01.png, 02.png ... 10.png
 *
 * Si no existen, se muestra una ilustración de respaldo
 * hecha con emojis.
 */
$preguntas = [
    [
        "titulo" => "¡Descubriste algo nuevo! ¿Qué haces primero?",
        "descripcion" => "Imagina que encuentras un juego que nunca has visto.",
        "emoji" => "🎁",
        "imagen" => "images/quiz/01.png",
        "opciones" => [
            "A" => "👀 Miro sus dibujos y colores.",
            "B" => "🎧 Escucho cómo se juega.",
            "C" => "👐 Empiezo a probarlo.",
            "D" => "📖 Leo cómo funciona."
        ]
    ],
    [
        "titulo" => "¡Vamos de aventura! ¿Cómo recuerdas el camino?",
        "descripcion" => "Visitaste un lugar increíble y quieres volver.",
        "emoji" => "🗺️",
        "imagen" => "images/quiz/02.png",
        "opciones" => [
            "A" => "🏠 Recuerdo los lugares que vi.",
            "B" => "🗣️ Recuerdo las indicaciones.",
            "C" => "🚶 Recuerdo por dónde caminé.",
            "D" => "📝 Recuerdo los nombres escritos."
        ]
    ],
    [
        "titulo" => "¡Hay un misterio! ¿Cómo prefieres descubrirlo?",
        "descripcion" => "Alguien quiere enseñarte algo sorprendente.",
        "emoji" => "🔍",
        "imagen" => "images/quiz/03.png",
        "opciones" => [
            "A" => "🖼️ Viendo imágenes y pistas.",
            "B" => "🎤 Escuchando una explicación.",
            "C" => "🧩 Probando con objetos.",
            "D" => "📚 Leyendo las pistas."
        ]
    ],
    [
        "titulo" => "¡Un desafío difícil! ¿Cómo lo resolverías?",
        "descripcion" => "Tienes un rompecabezas muy interesante.",
        "emoji" => "🧠",
        "imagen" => "images/quiz/04.png",
        "opciones" => [
            "A" => "🎨 Imagino cómo encajan las piezas.",
            "B" => "💬 Explico mis ideas en voz alta.",
            "C" => "👐 Muevo las piezas para probar.",
            "D" => "✏️ Escribo o dibujo los pasos."
        ]
    ],
    [
        "titulo" => "¡Reto de memoria! ¿Cómo recordarías tres cosas?",
        "descripcion" => "Necesitas recordar tres objetos para una misión.",
        "emoji" => "🧺",
        "imagen" => "images/quiz/05.png",
        "opciones" => [
            "A" => "🌈 Imagino cómo se ven.",
            "B" => "🎵 Repito sus nombres cantando.",
            "C" => "✋ Uso mis dedos o movimientos.",
            "D" => "📝 Hago una pequeña lista."
        ]
    ],
    [
        "titulo" => "¡Hora de construir! ¿Por dónde comienzas?",
        "descripcion" => "Tienes bloques para construir algo increíble.",
        "emoji" => "🏗️",
        "imagen" => "images/quiz/06.png",
        "opciones" => [
            "A" => "🏰 Miro un modelo para inspirarme.",
            "B" => "🗣️ Escucho ideas de alguien.",
            "C" => "🧱 Comienzo a unir las piezas.",
            "D" => "📋 Sigo instrucciones escritas."
        ]
    ],
    [
        "titulo" => "¡Una actividad muy larga! ¿Qué te ayudaría?",
        "descripcion" => "Quieres mantener el interés mientras aprendes.",
        "emoji" => "🌟",
        "imagen" => "images/quiz/07.png",
        "opciones" => [
            "A" => "🖼️ Ver imágenes interesantes.",
            "B" => "🎶 Escuchar historias o sonidos.",
            "C" => "🤸 Hacer pausas para moverme.",
            "D" => "📓 Tener palabras o notas de apoyo."
        ]
    ],
    [
        "titulo" => "¡Un libro mágico! ¿Qué harías primero?",
        "descripcion" => "Encontraste un libro lleno de aventuras.",
        "emoji" => "📚",
        "imagen" => "images/quiz/08.png",
        "opciones" => [
            "A" => "🎨 Exploro sus ilustraciones.",
            "B" => "🎧 Quiero escuchar la historia.",
            "C" => "🎭 Represento a sus personajes.",
            "D" => "📖 Comienzo a leerlo."
        ]
    ],
    [
        "titulo" => "¡Quieres recordar una idea! ¿Qué eliges?",
        "descripcion" => "Aprendiste algo que te gustaría compartir.",
        "emoji" => "💡",
        "imagen" => "images/quiz/09.png",
        "opciones" => [
            "A" => "🖍️ Hago un dibujo de la idea.",
            "B" => "🗣️ Se la cuento a otra persona.",
            "C" => "🎲 Creo un juego para practicarla.",
            "D" => "📝 Escribo un pequeño resumen."
        ]
    ],
    [
        "titulo" => "¡Tu gran invento! ¿Cómo aprenderías a usarlo?",
        "descripcion" => "Te regalaron un aparato imaginario con funciones sorprendentes.",
        "emoji" => "🚀",
        "imagen" => "images/quiz/10.png",
        "opciones" => [
            "A" => "👀 Observo sus botones y símbolos.",
            "B" => "🎤 Pido que me expliquen.",
            "C" => "🕹️ Lo pruebo con cuidado.",
            "D" => "📘 Consulto sus instrucciones."
        ]
    ]
];

$total = count($preguntas);
$resultado = null;
$error = "";
$iniciar = isset($_GET["iniciar"]) && $_GET["iniciar"] === "1";

if (empty($_SESSION["csrf_quiz"])) {
    $_SESSION["csrf_quiz"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["enviar_test"])) {

    if (!hash_equals(
        $_SESSION["csrf_quiz"],
        (string)($_POST["csrf"] ?? "")
    )) {
        $error = "La sesión del cuestionario cambió. Intenta nuevamente.";
    } else {

        $puntajes = [
            "A" => 0,
            "B" => 0,
            "C" => 0,
            "D" => 0
        ];

        $validas = true;

        for ($i = 1; $i <= $total; $i++) {
            $respuesta = $_POST["Q".$i] ?? "";

            if (!array_key_exists($respuesta, $puntajes)) {
                $validas = false;
                break;
            }

            $puntajes[$respuesta]++;
        }

        if (!$validas) {
            $error = "Responde todas las preguntas para descubrir tus resultados.";
            $iniciar = true;
        } else {

            $nombres = [
                "A" => "Visual",
                "B" => "Auditivo",
                "C" => "Kinestésico",
                "D" => "Lecto-Escritura"
            ];

            $maximo = max($puntajes);
            $ganadores = array_keys($puntajes, $maximo);
            $predominante = $nombres[$ganadores[0]];
            $porcentaje = ($maximo / $total) * 100;

            $guardar = $conexion->prepare("
                INSERT INTO test_estilo_aprendizaje
                (
                    usuario_id,
                    puntaje_visual,
                    puntaje_auditivo,
                    puntaje_kinestesico,
                    puntaje_lectoescritura,
                    estilo_predominante,
                    porcentaje_predominante
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $guardar->bind_param(
                "iiiiisd",
                $usuario_id,
                $puntajes["A"],
                $puntajes["B"],
                $puntajes["C"],
                $puntajes["D"],
                $predominante,
                $porcentaje
            );

            if ($guardar->execute()) {
                $resultado = [
                    "puntajes" => $puntajes,
                    "ganadores" => $ganadores,
                    "porcentaje" => $porcentaje
                ];

                $_SESSION["csrf_quiz"] = bin2hex(random_bytes(32));
            } else {
                $error = "No se pudieron guardar los resultados.";
                $iniciar = true;
            }

            $guardar->close();
        }
    }
}

$categorias = [
    "A" => [
        "nombre" => "Imágenes y observación",
        "emoji" => "🎨",
        "descripcion" => "Elegiste explorar mediante dibujos, formas y detalles.",
        "reto" => "Crea un mapa visual de algo que quieras aprender."
    ],
    "B" => [
        "nombre" => "Sonidos y conversación",
        "emoji" => "🎵",
        "descripcion" => "Elegiste escuchar, conversar y utilizar sonidos.",
        "reto" => "Explica una idea en voz alta o inventa una canción."
    ],
    "C" => [
        "nombre" => "Movimiento y experimentación",
        "emoji" => "🏃",
        "descripcion" => "Elegiste probar, construir y aprender mediante actividades.",
        "reto" => "Diseña un pequeño experimento o juego práctico."
    ],
    "D" => [
        "nombre" => "Palabras y organización",
        "emoji" => "📚",
        "descripcion" => "Elegiste utilizar textos, listas e instrucciones.",
        "reto" => "Escribe o dicta tres ideas y trata de recordarlas después."
    ]
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Penguich | Descubro cómo me gusta aprender</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body class="penguich-quiz">

<div class="pq-container">

<header class="pq-topbar">
    <a href="usuario.php" class="pq-back">
        ← Mi aventura
    </a>

    <div class="pq-brand">🐧 Penguich</div>

    <button id="pqSoundToggle" type="button" class="pq-small-btn"
        aria-pressed="true">
        🔊 Voz activada
    </button>
</header>

<?php if ($resultado !== null): ?>

<section class="pq-result pq-panel">
    <div class="pq-celebration">🎉</div>
    <span class="pq-eyebrow">MIS DESCUBRIMIENTOS</span>
    <h1>¡Terminaste tu aventura!</h1>

    <p>
        ¡Buen trabajo, <?php echo escapar($nombre_usuario); ?>!
        Estas fueron las formas de explorar que elegiste hoy.
        No son etiquetas ni límites para tu aprendizaje.
    </p>

    <div class="pq-results-grid">
        <?php foreach ($categorias as $clave => $categoria): ?>
            <?php
                $valor = $resultado["puntajes"][$clave];
                $porcentajeBarra = ($valor / $total) * 100;
            ?>
            <div class="pq-result-item">
                <div class="pq-result-heading">
                    <strong>
                        <?php echo $categoria["emoji"]; ?>
                        <?php echo escapar($categoria["nombre"]); ?>
                    </strong>
                    <span><?php echo $valor; ?> de <?php echo $total; ?></span>
                </div>
                <div class="pq-result-track">
                    <div style="width:<?php echo $porcentajeBarra; ?>%"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pq-discovery">
        <h2>🌟 Lo que más elegiste</h2>

        <?php foreach ($resultado["ganadores"] as $clave): ?>
            <div class="pq-discovery-item">
                <h3>
                    <?php echo $categorias[$clave]["emoji"]; ?>
                    <?php echo escapar($categorias[$clave]["nombre"]); ?>
                </h3>
                <p><?php echo escapar($categorias[$clave]["descripcion"]); ?></p>
                <p><strong>Tu próximo reto:</strong>
                    <?php echo escapar($categorias[$clave]["reto"]); ?>
                </p>
            </div>
        <?php endforeach; ?>

        <p class="pq-result-note">
            💚 Puedes aprender de muchas maneras.
            Las respuestas muestran preferencias de este momento,
            no miden tu inteligencia ni determinan cómo aprenderás mejor.
        </p>
    </div>

    <div class="pq-result-actions">
        <a class="pq-button pq-primary" href="procesar_estrategias.php">
            🗺️ Explorar mi ruta
        </a>
        <a class="pq-button pq-secondary"
           href="procesar_estilos.php?iniciar=1">
            🔄 Volver a jugar
        </a>
        <a class="pq-button pq-secondary" href="usuario.php">
            🏠 Ir a mi panel
        </a>
    </div>
</section>

<?php elseif (!$iniciar): ?>

<section class="pq-welcome pq-panel">
    <div class="pq-welcome-icon">🐧✨</div>

    <span class="pq-eyebrow">UNA NUEVA AVENTURA</span>
    <h1>¿Cómo te gusta descubrir cosas nuevas?</h1>

    <p>
        ¡Hola, <?php echo escapar($nombre_usuario); ?>!
        Te esperan 10 preguntas divertidas.
        Elige lo que tú harías. No hay respuestas buenas o malas.
    </p>

    <div class="pq-welcome-features">
        <div>🔊 <strong>Preguntas con voz</strong></div>
        <div>👆 <strong>Respuestas grandes</strong></div>
        <div>🖼️ <strong>Imágenes divertidas</strong></div>
        <div>💚 <strong>Sin presión ni calificaciones</strong></div>
    </div>

    <a href="procesar_estilos.php?iniciar=1"
       class="pq-button pq-primary pq-start">
        🚀 ¡Comenzar mi aventura!
    </a>
</section>

<?php else: ?>

<section class="pq-panel pq-quiz-panel">

    <div class="pq-progress-heading">
        <div>
            <span class="pq-eyebrow">MI AVENTURA</span>
            <h1>Descubro cómo me gusta aprender</h1>
        </div>

        <button type="button" id="pqPause" class="pq-small-btn">
            🌿 Tomar un descanso
        </button>
    </div>

    <div class="pq-progress-meta">
        <strong>
            Pregunta <span id="pqCurrent">1</span>
            de <?php echo $total; ?>
        </strong>
        <span id="pqPercent">10%</span>
    </div>

    <div class="pq-progress-track"
         id="pqProgressTrack"
         role="progressbar"
         aria-valuemin="0"
         aria-valuemax="<?php echo $total; ?>"
         aria-valuenow="1">
        <div id="pqProgressFill"></div>
    </div>

    <?php if ($error): ?>
        <div class="pq-error" role="alert">
            <?php echo escapar($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="procesar_estilos.php"
          id="pqForm" novalidate>

        <input type="hidden" name="csrf"
               value="<?php echo escapar($_SESSION["csrf_quiz"]); ?>">

        <?php foreach ($preguntas as $indice => $pregunta): ?>
            <?php $numero = $indice + 1; ?>

            <section class="pq-question"
                     data-step="<?php echo $indice; ?>"
                     <?php echo $indice !== 0 ? 'hidden' : ''; ?>>

                <div class="pq-question-layout">

                    <div class="pq-question-main">
                        <span class="pq-question-tag">
                            🌟 MISIÓN <?php echo $numero; ?>
                        </span>

                        <h2 id="pqTitle<?php echo $numero; ?>">
                            <?php echo escapar($pregunta["titulo"]); ?>
                        </h2>

                        <p class="pq-question-description">
                            <?php echo escapar($pregunta["descripcion"]); ?>
                        </p>

                        <button type="button"
                                class="pq-read-button"
                                data-read="<?php echo $numero; ?>">
                            🔊 Escuchar pregunta
                        </button>
                    </div>

                    <div class="pq-question-image">
                        <img
                            src="<?php echo escapar($pregunta["imagen"]); ?>"
                            alt=""
                            loading="lazy"
                            onerror="this.hidden=true;this.nextElementSibling.hidden=false;">

                        <div class="pq-image-fallback" hidden>
                            <span><?php echo $pregunta["emoji"]; ?></span>
                            <small>Mi aventura ilustrada</small>
                        </div>
                    </div>

                </div>

                <fieldset class="pq-options">
                    <legend>Elige la opción que más te guste</legend>

                    <?php foreach ($pregunta["opciones"] as $clave => $texto): ?>
                        <label class="pq-option">
                            <input type="radio"
                                   name="Q<?php echo $numero; ?>"
                                   value="<?php echo $clave; ?>">

                            <span class="pq-option-letter">
                                <?php echo $clave; ?>
                            </span>

                            <span class="pq-option-text">
                                <?php echo escapar($texto); ?>
                            </span>

                            <span class="pq-option-check">✓</span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

            </section>
        <?php endforeach; ?>

        <div class="pq-feedback" id="pqFeedback"
             role="status" aria-live="polite"></div>

        <div class="pq-navigation">
            <button type="button"
                    id="pqPrevious"
                    class="pq-button pq-secondary"
                    hidden>
                ← Anterior
            </button>

            <button type="button"
                    id="pqNext"
                    class="pq-button pq-primary">
                Siguiente →
            </button>

            <button type="submit"
                    id="pqSubmit"
                    name="enviar_test"
                    value="1"
                    class="pq-button pq-primary"
                    hidden>
                🎉 Descubrir mis resultados
            </button>
        </div>

    </form>

    <p class="pq-footer-tip">
        💚 Cada persona tiene intereses y maneras de explorar diferentes.
        Puedes cambiar tus respuestas antes de terminar.
    </p>
</section>

<?php endif; ?>

</div>

<dialog id="pqPauseDialog" class="pq-pause-dialog">
    <div class="pq-pause-content">
        <div class="pq-pause-icon">🌿</div>
        <h2>¡Momento de descansar!</h2>
        <p>
            Puedes estirarte, beber agua o mirar a tu alrededor.
            No hay prisa para terminar.
        </p>

        <div class="pq-breathing" aria-hidden="true">
            💚
        </div>

        <p id="pqBreathingText">
            Respira suavemente y a tu ritmo.
        </p>

        <button type="button"
                id="pqResume"
                class="pq-button pq-primary">
            😊 Continuar mi aventura
        </button>
    </div>
</dialog>

<script>
document.addEventListener("DOMContentLoaded", () => {
    "use strict";

    const form = document.getElementById("pqForm");
    const soundToggle = document.getElementById("pqSoundToggle");
    const pauseDialog = document.getElementById("pqPauseDialog");

    let voiceEnabled = true;

    const canSpeak = "speechSynthesis" in window;

    function stopSpeech() {
        if (canSpeak) {
            window.speechSynthesis.cancel();
        }
    }

    function speak(text) {
        if (!voiceEnabled || !canSpeak) return;

        stopSpeech();

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = "es-MX";
        utterance.rate = 0.88;
        utterance.pitch = 1.05;
        utterance.volume = 1;

        window.speechSynthesis.speak(utterance);
    }

    soundToggle.addEventListener("click", () => {
        voiceEnabled = !voiceEnabled;
        soundToggle.setAttribute(
            "aria-pressed",
            String(voiceEnabled)
        );

        soundToggle.textContent = voiceEnabled
            ? "🔊 Voz activada"
            : "🔇 Voz desactivada";

        if (!voiceEnabled) {
            stopSpeech();
        } else if (form) {
            speakCurrentQuestion();
        }
    });

    if (!form) {
        return;
    }

    const questions = Array.from(
        document.querySelectorAll(".pq-question")
    );

    const total = questions.length;
    const storageKey = "penguich-quiz-borrador-" +
        <?php echo json_encode(hash("sha256", $nombre_usuario)); ?>;

    let currentStep = 0;

    const currentLabel = document.getElementById("pqCurrent");
    const percentLabel = document.getElementById("pqPercent");
    const progressFill = document.getElementById("pqProgressFill");
    const progressTrack = document.getElementById("pqProgressTrack");
    const previousButton = document.getElementById("pqPrevious");
    const nextButton = document.getElementById("pqNext");
    const submitButton = document.getElementById("pqSubmit");
    const feedback = document.getElementById("pqFeedback");

    function getQuestionText(index) {
        const question = questions[index];
        const title = question.querySelector("h2");
        return title ? title.textContent.trim() : "";
    }

    function speakCurrentQuestion() {
        speak(getQuestionText(currentStep));
    }

    function saveDraft() {
        const answers = {};

        questions.forEach((question, index) => {
            const selected = question.querySelector(
                'input[type="radio"]:checked'
            );

            if (selected) {
                answers["Q" + (index + 1)] = selected.value;
            }
        });

        try {
            localStorage.setItem(
                storageKey,
                JSON.stringify({
                    step: currentStep,
                    answers: answers
                })
            );
        } catch (error) {
            // El cuestionario continúa aunque el almacenamiento esté bloqueado.
        }
    }

    function loadDraft() {
        try {
            const saved = JSON.parse(
                localStorage.getItem(storageKey)
            );

            if (!saved || !saved.answers) return;

            questions.forEach((question, index) => {
                const answer = saved.answers["Q" + (index + 1)];

                if (["A","B","C","D"].includes(answer)) {
                    const input = question.querySelector(
                        'input[value="' + answer + '"]'
                    );

                    if (input) input.checked = true;
                }
            });

            if (Number.isInteger(saved.step)) {
                currentStep = Math.max(
                    0,
                    Math.min(total - 1, saved.step)
                );
            }
        } catch (error) {
            // Si el borrador está dañado, comenzamos normalmente.
        }
    }

    function renderStep(readAloud = false) {
        questions.forEach((question, index) => {
            question.hidden = index !== currentStep;
        });

        currentLabel.textContent = currentStep + 1;

        const percentage = Math.round(
            ((currentStep + 1) / total) * 100
        );

        percentLabel.textContent = percentage + "%";
        progressFill.style.width = percentage + "%";

        progressTrack.setAttribute(
            "aria-valuenow",
            String(currentStep + 1)
        );

        previousButton.hidden = currentStep === 0;
        nextButton.hidden = currentStep === total - 1;
        submitButton.hidden = currentStep !== total - 1;

        feedback.textContent = "";
        saveDraft();

        if (readAloud) {
            speakCurrentQuestion();
        }

        const title = questions[currentStep].querySelector("h2");
        if (title) {
            title.setAttribute("tabindex", "-1");
            title.focus({preventScroll:true});
        }

        window.scrollTo({
            top: 0,
            behavior: window.matchMedia(
                "(prefers-reduced-motion: reduce)"
            ).matches ? "auto" : "smooth"
        });
    }

    function isAnswered() {
        return !!questions[currentStep].querySelector(
            'input[type="radio"]:checked'
        );
    }

    function requireAnswer() {
        if (isAnswered()) return true;

        feedback.textContent =
            "💛 Elige una respuesta para continuar tu aventura.";

        const firstOption = questions[currentStep].querySelector(
            'input[type="radio"]'
        );

        if (firstOption) firstOption.focus();
        return false;
    }

    nextButton.addEventListener("click", () => {
        if (!requireAnswer()) return;

        if (currentStep < total - 1) {
            currentStep++;
            renderStep(true);
        }
    });

    previousButton.addEventListener("click", () => {
        if (currentStep > 0) {
            currentStep--;
            renderStep(true);
        }
    });

    document.querySelectorAll("[data-read]").forEach(button => {
        button.addEventListener("click", () => {
            const index = Number(button.dataset.read) - 1;
            speak(getQuestionText(index));
        });
    });

    questions.forEach(question => {
        question.querySelectorAll(
            'input[type="radio"]'
        ).forEach(input => {
            input.addEventListener("change", () => {
                feedback.textContent = "";
                saveDraft();
            });
        });
    });

    form.addEventListener("submit", event => {
        for (let i = 0; i < total; i++) {
            if (!questions[i].querySelector(
                'input[type="radio"]:checked'
            )) {
                event.preventDefault();
                currentStep = i;
                renderStep(false);
                requireAnswer();
                return;
            }
        }

        stopSpeech();

        try {
            localStorage.removeItem(storageKey);
        } catch (error) {}
    });

    document.getElementById("pqPause").addEventListener(
        "click",
        () => {
            stopSpeech();
            pauseDialog.showModal();
        }
    );

    document.getElementById("pqResume").addEventListener(
        "click",
        () => {
            pauseDialog.close();
        }
    );

    pauseDialog.addEventListener("close", () => {
        stopSpeech();
    });

    window.addEventListener("pagehide", stopSpeech);

    loadDraft();
    renderStep(false);
});
</script>

</body>
</html>
