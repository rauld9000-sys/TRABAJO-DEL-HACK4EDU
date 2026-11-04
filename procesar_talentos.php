
<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function escapar($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

try {
    $conexion = new mysqli("localhost", "root", "", "penguich");
    $conexion->set_charset("utf8mb4");

    $nombre_usuario = (string)$_SESSION["usuario"];

    $stmt = $conexion->prepare(
        "SELECT id FROM usuarios WHERE usuario = ? LIMIT 1"
    );
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $stmt->bind_result($usuario_id);

    if (!$stmt->fetch()) {
        $stmt->close();
        header("Location: login.php");
        exit();
    }

    $stmt->close();

    $conexion->query("
        CREATE TABLE IF NOT EXISTS test_inteligencias_multiples (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            puntaje_a INT NOT NULL DEFAULT 0,
            puntaje_b INT NOT NULL DEFAULT 0,
            puntaje_c INT NOT NULL DEFAULT 0,
            puntaje_d INT NOT NULL DEFAULT 0,
            puntaje_e INT NOT NULL DEFAULT 0,
            puntaje_f INT NOT NULL DEFAULT 0,
            puntaje_g INT NOT NULL DEFAULT 0,
            perfil_predominante VARCHAR(255) NOT NULL,
            porcentaje_predominante DECIMAL(5,2) NOT NULL,
            respuestas_json LONGTEXT NOT NULL,
            fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (usuario_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

} catch (Throwable $e) {
    http_response_code(500);
    exit("No fue posible conectar con la base de datos de Penguich.");
}

/* ========================================
   SIETE ÁREAS DE EXPLORACIÓN
======================================== */

$perfiles = [
    "A" => [
        "nombre" => "Palabras e historias",
        "icono" => "📚",
        "descripcion" => "Te interesaron las historias, las palabras y las formas de expresar ideas.",
        "consejo" => "Inventa una historia con tres personajes y cuéntasela a alguien.",
        "actividades" => [
            "Crear cuentos e historietas",
            "Inventar adivinanzas",
            "Narrar aventuras",
            "Jugar con palabras nuevas"
        ]
    ],
    "B" => [
        "nombre" => "Lógica y estrategia",
        "icono" => "🧩",
        "descripcion" => "Elegiste acertijos, estrategias y problemas que invitan a pensar.",
        "consejo" => "Inventa un acertijo con tres pistas para que alguien lo resuelva.",
        "actividades" => [
            "Ajedrez y juegos de estrategia",
            "Rompecabezas",
            "Acertijos y patrones",
            "Retos de construcción lógica"
        ]
    ],
    "C" => [
        "nombre" => "Arte y pensamiento espacial",
        "icono" => "🎨",
        "descripcion" => "Te llamaron la atención los dibujos, las formas y los mundos imaginarios.",
        "consejo" => "Diseña un parque fantástico y dibuja un mapa para recorrerlo.",
        "actividades" => [
            "Dibujo y pintura",
            "Creación de mapas",
            "Diseño de maquetas",
            "Origami y esculturas"
        ]
    ],
    "D" => [
        "nombre" => "Movimiento y coordinación",
        "icono" => "⚽",
        "descripcion" => "Elegiste actividades donde puedes moverte, construir y usar tu cuerpo.",
        "consejo" => "Crea un circuito de movimientos seguros con tres estaciones.",
        "actividades" => [
            "Deportes y coordinación",
            "Baile y expresión corporal",
            "Teatro físico",
            "Juegos de equilibrio"
        ]
    ],
    "E" => [
        "nombre" => "Música y ritmo",
        "icono" => "🎵",
        "descripcion" => "Te interesaron los sonidos, las canciones y la creación de ritmos.",
        "consejo" => "Inventa una secuencia de cuatro sonidos y enséñasela a alguien.",
        "actividades" => [
            "Percusión con objetos seguros",
            "Canto y creación musical",
            "Juegos de ritmo",
            "Exploración de instrumentos"
        ]
    ],
    "F" => [
        "nombre" => "Colaboración y comunicación",
        "icono" => "🤝",
        "descripcion" => "Elegiste compartir ideas, escuchar a otros y participar en equipo.",
        "consejo" => "Organiza un juego donde todos tengan una tarea importante.",
        "actividades" => [
            "Juegos cooperativos",
            "Teatro en grupo",
            "Proyectos comunitarios",
            "Actividades de comunicación"
        ]
    ],
    "G" => [
        "nombre" => "Autoconocimiento y reflexión",
        "icono" => "🌟",
        "descripcion" => "Te interesaron las actividades para explorar tus ideas, emociones y metas.",
        "consejo" => "Dibuja tres cosas que disfrutas y una que te gustaría intentar.",
        "actividades" => [
            "Diario creativo",
            "Reconocimiento de emociones",
            "Creación de metas personales",
            "Actividades de reflexión"
        ]
    ]
];

/* ========================================
   10 PREGUNTAS DIVERTIDAS
   7 OPCIONES POR PREGUNTA
======================================== */

$preguntas = [
    [
        "titulo" => "¡Una tarde llena de aventuras!",
        "detalle" => "Tienes tiempo libre para hacer lo que quieras. ¿Qué eliges?",
        "imagen" => "01.png",
        "emoji" => "🚀",
        "opciones" => [
            "A" => "📚 Inventar una historia increíble.",
            "B" => "🧩 Resolver un misterio difícil.",
            "C" => "🎨 Dibujar un mundo fantástico.",
            "D" => "⚽ Jugar, bailar o hacer deporte.",
            "E" => "🎵 Crear música y ritmos.",
            "F" => "🤝 Organizar un juego con amigos.",
            "G" => "🌟 Imaginar mi propia aventura."
        ]
    ],
    [
        "titulo" => "¡Encontraste una caja mágica!",
        "detalle" => "La caja puede convertirse en cualquier cosa. ¿Qué harías?",
        "imagen" => "02.png",
        "emoji" => "🎁",
        "opciones" => [
            "A" => "📖 Crear un libro de aventuras.",
            "B" => "♟️ Inventar un juego de estrategia.",
            "C" => "🏰 Construir un castillo imaginario.",
            "D" => "🏃 Crear un circuito de obstáculos.",
            "E" => "🥁 Fabricar un instrumento musical.",
            "F" => "🎲 Crear un juego para todos.",
            "G" => "💭 Guardar mis ideas y sueños."
        ]
    ],
    [
        "titulo" => "¡Un misterio por resolver!",
        "detalle" => "Encontraste pistas de un tesoro escondido. ¿Cómo empiezas?",
        "imagen" => "03.png",
        "emoji" => "🔍",
        "opciones" => [
            "A" => "📜 Leo las pistas y creo una historia.",
            "B" => "🧠 Busco patrones y soluciones.",
            "C" => "🗺️ Dibujo un mapa del tesoro.",
            "D" => "🚶 Exploro el lugar con cuidado.",
            "E" => "🎶 Escucho si hay pistas sonoras.",
            "F" => "👫 Reúno un equipo de exploradores.",
            "G" => "💡 Pienso qué haría yo primero."
        ]
    ],
    [
        "titulo" => "¡El festival de talentos!",
        "detalle" => "Puedes presentar algo especial. ¿Qué mostrarías?",
        "imagen" => "04.png",
        "emoji" => "🏆",
        "opciones" => [
            "A" => "🎤 Contar una historia emocionante.",
            "B" => "🧩 Resolver un gran acertijo.",
            "C" => "🖼️ Presentar una obra de arte.",
            "D" => "🤸 Hacer una demostración deportiva.",
            "E" => "🎹 Tocar o crear una canción.",
            "F" => "🎭 Dirigir una presentación grupal.",
            "G" => "🌈 Compartir algo importante para mí."
        ]
    ],
    [
        "titulo" => "¡Diseña tu parque soñado!",
        "detalle" => "Puedes crear un parque totalmente nuevo. ¿Qué tendría?",
        "imagen" => "05.png",
        "emoji" => "🌳",
        "opciones" => [
            "A" => "📚 Un rincón para contar cuentos.",
            "B" => "♟️ Juegos de lógica gigantes.",
            "C" => "🎨 Murales y esculturas increíbles.",
            "D" => "🛝 Pistas para saltar y correr.",
            "E" => "🎶 Una zona de música y sonidos.",
            "F" => "🤝 Espacios para juegos en equipo.",
            "G" => "🌿 Un jardín tranquilo para pensar."
        ]
    ],
    [
        "titulo" => "¡Aprendiste algo sorprendente!",
        "detalle" => "Quieres compartir tu descubrimiento. ¿Cómo lo harías?",
        "imagen" => "06.png",
        "emoji" => "💡",
        "opciones" => [
            "A" => "🗣️ Lo cuento como una historia.",
            "B" => "🔢 Explico cómo descubrí la respuesta.",
            "C" => "🖍️ Hago un dibujo o un modelo.",
            "D" => "👐 Lo demuestro con movimientos.",
            "E" => "🎵 Invento una canción para recordarlo.",
            "F" => "👫 Organizo una actividad en equipo.",
            "G" => "📝 Anoto qué aprendí y qué sentí."
        ]
    ],
    [
        "titulo" => "¡Una misión en otro planeta!",
        "detalle" => "Llegaste a un planeta desconocido. ¿Qué quieres explorar?",
        "imagen" => "07.png",
        "emoji" => "🪐",
        "opciones" => [
            "A" => "📖 Las historias de sus habitantes.",
            "B" => "🧩 Sus misterios y acertijos.",
            "C" => "🏙️ Sus edificios y paisajes.",
            "D" => "🏃 Sus juegos y desafíos físicos.",
            "E" => "🎼 Sus canciones y sonidos.",
            "F" => "🤝 Cómo conviven sus habitantes.",
            "G" => "💭 Cómo me sentiría viviendo allí."
        ]
    ],
    [
        "titulo" => "¡Un desafío con tus amigos!",
        "detalle" => "Todos quieren intentar algo nuevo. ¿Qué propones?",
        "imagen" => "08.png",
        "emoji" => "🎯",
        "opciones" => [
            "A" => "🎭 Inventar una obra de teatro.",
            "B" => "♟️ Un torneo de estrategia.",
            "C" => "🏗️ Construir una gran maqueta.",
            "D" => "🏅 Una carrera con obstáculos.",
            "E" => "🥁 Una banda musical.",
            "F" => "🤝 Una misión cooperativa.",
            "G" => "🌟 Un mural de sueños y metas."
        ]
    ],
    [
        "titulo" => "¡Un recuerdo para siempre!",
        "detalle" => "Quieres recordar un día especial. ¿Qué crearías?",
        "imagen" => "09.png",
        "emoji" => "📸",
        "opciones" => [
            "A" => "📖 Un cuento sobre ese día.",
            "B" => "🧩 Un juego con pistas de lo ocurrido.",
            "C" => "🎨 Un dibujo de mi momento favorito.",
            "D" => "💃 Una coreografía divertida.",
            "E" => "🎵 Una canción del recuerdo.",
            "F" => "👫 Un álbum con mis amigos.",
            "G" => "💚 Una página sobre mis emociones."
        ]
    ],
    [
        "titulo" => "¡Tu próximo gran reto!",
        "detalle" => "Penguich te ofrece siete aventuras. ¿Cuál probarías primero?",
        "imagen" => "10.png",
        "emoji" => "🌟",
        "opciones" => [
            "A" => "✍️ Convertirme en creador de historias.",
            "B" => "🧠 Ser maestro de los acertijos.",
            "C" => "🎨 Diseñar un mundo extraordinario.",
            "D" => "⚽ Superar un desafío de movimiento.",
            "E" => "🎵 Crear mi propia música.",
            "F" => "🤝 Liderar una aventura cooperativa.",
            "G" => "🌈 Descubrir más cosas sobre mí."
        ]
    ]
];

$total_preguntas = count($preguntas);
$puntajes = array_fill_keys(array_keys($perfiles), 0);
$perfiles_ganadores = [];
$guardado_exitoso = false;
$error = "";
$respuestas_previas = [];

$iniciar_quiz = ($_GET["iniciar"] ?? "") === "1";

if (empty($_SESSION["csrf_multiples"])) {
    $_SESSION["csrf_multiples"] = bin2hex(random_bytes(32));
}

/* ========================================
   PROCESAR RESPUESTAS
======================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["enviar_test"])) {

    $iniciar_quiz = true;

    $token = (string)($_POST["csrf"] ?? "");

    if (!hash_equals($_SESSION["csrf_multiples"], $token)) {
        $error = "La sesión cambió. Actualiza la página e inténtalo otra vez.";
    } else {

        $respuestas_validas = true;

        for ($i = 1; $i <= $total_preguntas; $i++) {
            $clave = "P" . $i;
            $respuesta = $_POST[$clave] ?? null;

            if (!is_string($respuesta)
                || !array_key_exists($respuesta, $puntajes)) {
                $respuestas_validas = false;
                break;
            }

            $respuestas_previas[$clave] = $respuesta;
            $puntajes[$respuesta]++;
        }

        if (!$respuestas_validas) {
            $error = "Responde todas las preguntas para continuar.";
        } else {

            $maximo = max($puntajes);
            $perfiles_ganadores = array_keys($puntajes, $maximo);

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
                $respuestas_previas,
                JSON_UNESCAPED_UNICODE
            );

            try {
                $stmt = $conexion->prepare("
                    INSERT INTO test_inteligencias_multiples (
                        usuario_id,
                        puntaje_a,
                        puntaje_b,
                        puntaje_c,
                        puntaje_d,
                        puntaje_e,
                        puntaje_f,
                        puntaje_g,
                        perfil_predominante,
                        porcentaje_predominante,
                        respuestas_json
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "iiiiiiiisds",
                    $usuario_id,
                    $puntajes["A"],
                    $puntajes["B"],
                    $puntajes["C"],
                    $puntajes["D"],
                    $puntajes["E"],
                    $puntajes["F"],
                    $puntajes["G"],
                    $perfil_predominante,
                    $porcentaje_predominante,
                    $respuestas_json
                );

                $stmt->execute();
                $stmt->close();

                $guardado_exitoso = true;
                $iniciar_quiz = false;

                $_SESSION["csrf_multiples"] =
                    bin2hex(random_bytes(32));

            } catch (Throwable $e) {
                $error = "No se pudieron guardar los resultados.";
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

<title>Penguich | Mis talentos y habilidades</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body class="penguich-multiples">

<div class="pm-container">

<header class="pm-topbar">
    <a href="usuario.php" class="pm-back">
        ← Mi aventura
    </a>

    <div class="pm-brand">🐧 Penguich</div>

    <button type="button"
            id="pmVoiceToggle"
            class="pm-small-btn"
            aria-pressed="true">
        🔊 Voz activada
    </button>
</header>

<?php if ($guardado_exitoso): ?>

<main class="pm-panel pm-results">
    <div class="pm-big-icon">🎉</div>

    <span class="pm-eyebrow">MIS DESCUBRIMIENTOS</span>

    <h1>¡Completaste todas las misiones!</h1>

    <p class="pm-intro">
        ¡Excelente, <?php echo escapar($nombre_usuario); ?>!
        Estas son las actividades que elegiste durante tu aventura.
        Recuerda que puedes descubrir nuevos intereses cada día.
    </p>

    <div class="pm-result-highlight">
        <h2>🌟 Tus áreas más elegidas</h2>

        <div class="pm-result-icons">
            <?php foreach ($perfiles_ganadores as $letra): ?>
                <span><?php echo $perfiles[$letra]["icono"]; ?></span>
            <?php endforeach; ?>
        </div>

        <?php foreach ($perfiles_ganadores as $letra): ?>
            <h3><?php echo escapar($perfiles[$letra]["nombre"]); ?></h3>
            <p><?php echo escapar($perfiles[$letra]["descripcion"]); ?></p>
        <?php endforeach; ?>
    </div>

    <h2>📊 Así fueron tus elecciones</h2>

    <div class="pm-result-list">
        <?php foreach ($perfiles as $letra => $perfil): ?>
            <?php
                $puntos = $puntajes[$letra];
                $porcentaje = ($puntos / $total_preguntas) * 100;
            ?>

            <article class="pm-result-card">
                <div class="pm-result-heading">
                    <strong>
                        <?php echo $perfil["icono"]; ?>
                        <?php echo escapar($perfil["nombre"]); ?>
                    </strong>
                    <span>
                        <?php echo $puntos; ?>/<?php echo $total_preguntas; ?>
                    </span>
                </div>

                <div class="pm-result-track">
                    <div style="width:<?php echo $porcentaje; ?>%"></div>
                </div>
            </article>

        <?php endforeach; ?>
    </div>

    <h2>🚀 Actividades para desarrollar tus talentos</h2>

    <div class="pm-recommendations">
        <?php foreach ($perfiles_ganadores as $letra): ?>
            <article class="pm-recommendation">
                <div class="pm-recommendation-icon">
                    <?php echo $perfiles[$letra]["icono"]; ?>
                </div>

                <h3><?php echo escapar($perfiles[$letra]["nombre"]); ?></h3>

                <p>
                    <strong>Tu misión:</strong>
                    <?php echo escapar($perfiles[$letra]["consejo"]); ?>
                </p>

                <ul>
                    <?php foreach ($perfiles[$letra]["actividades"] as $actividad): ?>
                        <li><?php echo escapar($actividad); ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>

    <p class="pm-disclaimer">
        💚 Este juego explora intereses y preferencias actuales.
        No es un diagnóstico de inteligencia ni determina lo que
        puedes aprender o conseguir.
    </p>

    <div class="pm-actions">
        <a href="usuario.php" class="pm-btn pm-primary">
            🏠 Volver al panel
        </a>

        <a href="procesar_inteligencia.php?iniciar=1"
           class="pm-btn pm-secondary">
            🔄 Volver a jugar
        </a>
    </div>
</main>

<?php elseif (!$iniciar_quiz): ?>

<main class="pm-panel pm-welcome">
    <div class="pm-big-icon">🐧🌈</div>

    <span class="pm-eyebrow">UNA AVENTURA PARA DESCUBRIRTE</span>

    <h1>¡Descubre tus talentos e intereses!</h1>

    <p class="pm-intro">
        ¡Hola, <?php echo escapar($nombre_usuario); ?>!
        Penguich preparó diez preguntas llenas de aventuras,
        juegos, música, deportes y creatividad.
    </p>

    <div class="pm-feature-grid">
        <div>🔊 <strong>Preguntas con voz</strong></div>
        <div>👆 <strong>Opciones grandes</strong></div>
        <div>🖼️ <strong>Imágenes divertidas</strong></div>
        <div>🦖 <strong>Descanso con dinosaurio</strong></div>
    </div>

    <p class="pm-welcome-note">
        No existen respuestas correctas o incorrectas.
        ¡Elige lo que más te gustaría hacer!
    </p>

    <a href="procesar_inteligencia.php?iniciar=1"
       class="pm-btn pm-primary pm-start">
        🚀 ¡Comenzar mi aventura!
    </a>
</main>

<?php else: ?>

<main class="pm-panel pm-quiz-panel">

    <div class="pm-quiz-header">
        <div>
            <span class="pm-eyebrow">MISIÓN DE DESCUBRIMIENTO</span>
            <h1>Mis talentos e intereses</h1>
        </div>

        <button type="button"
                id="pmOpenBreak"
                class="pm-small-btn">
            🦖 Tomar un descanso
        </button>
    </div>

    <?php if ($error): ?>
        <div class="pm-error" role="alert">
            <?php echo escapar($error); ?>
        </div>
    <?php endif; ?>

    <div class="pm-progress-meta">
        <strong>
            Pregunta <span id="pmCurrent">1</span>
            de <?php echo $total_preguntas; ?>
        </strong>
        <span id="pmPercent">10%</span>
    </div>

    <div class="pm-progress-track"
         id="pmProgress"
         role="progressbar"
         aria-valuemin="0"
         aria-valuemax="<?php echo $total_preguntas; ?>"
         aria-valuenow="1">
        <div id="pmProgressFill"></div>
    </div>

    <form method="POST"
          action="procesar_inteligencia.php"
          id="pmForm"
          novalidate>

        <input type="hidden"
               name="csrf"
               value="<?php echo escapar($_SESSION["csrf_multiples"]); ?>">

        <?php foreach ($preguntas as $indice => $pregunta): ?>
            <?php $numero = $indice + 1; ?>

            <section class="pm-step"
                     data-step="<?php echo $indice; ?>"
                     <?php echo $indice > 0 ? "hidden" : ""; ?>>

                <div class="pm-question-layout">

                    <div class="pm-question-content">
                        <span class="pm-mission-tag">
                            ⭐ MISIÓN <?php echo $numero; ?>
                        </span>

                        <h2><?php echo escapar($pregunta["titulo"]); ?></h2>

                        <p class="pm-question-detail">
                            <?php echo escapar($pregunta["detalle"]); ?>
                        </p>

                        <button type="button"
                                class="pm-speak-btn"
                                data-read="<?php echo $indice; ?>">
                            🔊 Escuchar pregunta
                        </button>
                    </div>

                    <div class="pm-image-area">
                        <img
                            src="images/multiples/<?php echo escapar($pregunta["imagen"]); ?>"
                            alt=""
                            loading="lazy"
                            onerror="this.hidden=true;this.nextElementSibling.hidden=false;">

                        <div class="pm-image-fallback" hidden>
                            <span><?php echo $pregunta["emoji"]; ?></span>
                            <small>¡Imagina tu aventura!</small>
                        </div>
                    </div>

                </div>

                <fieldset class="pm-options">
                    <legend>¿Qué opción elegirías?</legend>

                    <?php foreach ($pregunta["opciones"] as $letra => $texto): ?>
                        <label class="pm-option">
                            <input
                                type="radio"
                                name="P<?php echo $numero; ?>"
                                value="<?php echo $letra; ?>"
                                <?php echo
                                    ($respuestas_previas["P".$numero] ?? "") === $letra
                                    ? "checked"
                                    : "";
                                ?>
                            >

                            <span class="pm-option-letter">
                                <?php echo $letra; ?>
                            </span>

                            <span class="pm-option-text">
                                <?php echo escapar($texto); ?>
                            </span>

                            <span class="pm-option-check">✓</span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

            </section>

        <?php endforeach; ?>

        <div class="pm-message"
             id="pmMessage"
             role="status"
             aria-live="polite"></div>

        <div class="pm-navigation">
            <button type="button"
                    id="pmPrevious"
                    class="pm-btn pm-secondary"
                    hidden>
                ← Anterior
            </button>

            <button type="button"
                    id="pmNext"
                    class="pm-btn pm-primary">
                Siguiente →
            </button>

            <button type="submit"
                    id="pmFinish"
                    name="enviar_test"
                    value="1"
                    class="pm-btn pm-primary"
                    hidden>
                🎉 Descubrir mis resultados
            </button>
        </div>
    </form>

    <p class="pm-bottom-note">
        💚 Responde a tu ritmo. Puedes cambiar tus respuestas
        antes de terminar.
    </p>
</main>

<?php endif; ?>

</div>

<dialog id="pmBreakDialog" class="pm-dialog">
    <div class="pm-dialog-content">
        <h2>🦖 ¡Descanso divertido!</h2>

        <p>
            Puedes jugar, respirar o descansar un momento.
            No hay prisa para terminar tu aventura.
        </p>

        <div class="pm-game-stats">
            <span>⭐ Puntos: <strong id="pmScore">0</strong></span>
            <span>⏱️ Tiempo: <strong id="pmTimer">10:00</strong></span>
        </div>

        <canvas id="pmCanvas"
                width="440"
                height="180"
                aria-label="Juego de dinosaurio que salta obstáculos">
        </canvas>

        <p class="pm-game-help">
            Presiona ESPACIO o el botón ¡Saltar!
            para esquivar obstáculos y recoger estrellas.
        </p>

        <div class="pm-actions">
            <button type="button"
                    id="pmJump"
                    class="pm-btn pm-secondary">
                🦖 ¡Saltar!
            </button>

            <button type="button"
                    id="pmResume"
                    class="pm-btn pm-primary">
                ▶️ Continuar aventura
            </button>
        </div>
    </div>
</dialog>

<script>
document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    const form = document.getElementById("pmForm");
    const voiceToggle = document.getElementById("pmVoiceToggle");
    const speechAvailable = "speechSynthesis" in window;

    let voiceEnabled = true;

    function stopSpeech() {
        if (speechAvailable) {
            window.speechSynthesis.cancel();
        }
    }

    function speak(text) {
        if (!speechAvailable || !voiceEnabled) return;

        stopSpeech();

        const speech = new SpeechSynthesisUtterance(text);

        speech.lang = "es-MX";
        speech.rate = 0.88;
        speech.pitch = 1.05;
        speech.volume = 1;

        const voices = window.speechSynthesis.getVoices();

        const spanishVoice = voices.find(
            voice => voice.lang.toLowerCase() === "es-mx"
        ) || voices.find(
            voice => voice.lang.toLowerCase().startsWith("es")
        );

        if (spanishVoice) {
            speech.voice = spanishVoice;
        }

        window.speechSynthesis.speak(speech);
    }

    voiceToggle.addEventListener("click", function () {
        voiceEnabled = !voiceEnabled;

        voiceToggle.textContent = voiceEnabled
            ? "🔊 Voz activada"
            : "🔇 Voz desactivada";

        voiceToggle.setAttribute(
            "aria-pressed",
            String(voiceEnabled)
        );

        if (!voiceEnabled) {
            stopSpeech();
        } else if (form) {
            readCurrentQuestion();
        }
    });

    /* ====================================
       CUESTIONARIO
    ==================================== */

    if (form) {
        const steps = Array.from(
            document.querySelectorAll(".pm-step")
        );

        const total = steps.length;

        let currentStep = 0;

        const currentLabel = document.getElementById("pmCurrent");
        const percentLabel = document.getElementById("pmPercent");
        const progressFill = document.getElementById("pmProgressFill");
        const progressBar = document.getElementById("pmProgress");

        const previousButton = document.getElementById("pmPrevious");
        const nextButton = document.getElementById("pmNext");
        const finishButton = document.getElementById("pmFinish");
        const message = document.getElementById("pmMessage");

        const storageKey = "penguich-multiples-" +
            <?php echo json_encode(hash("sha256", $nombre_usuario)); ?>;

        function readCurrentQuestion() {
            const step = steps[currentStep];

            const title = step.querySelector("h2");
            const detail = step.querySelector(".pm-question-detail");

            const text =
                (title ? title.textContent.trim() : "") + ". " +
                (detail ? detail.textContent.trim() : "");

            speak(text);
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
                localStorage.setItem(
                    storageKey,
                    JSON.stringify({
                        step: currentStep,
                        answers: answers
                    })
                );
            } catch (error) {}
        }

        function restoreProgress() {
            try {
                const saved = JSON.parse(
                    localStorage.getItem(storageKey)
                );

                if (!saved) return;

                steps.forEach(step => {
                    step.querySelectorAll(
                        'input[type="radio"]'
                    ).forEach(input => {
                        if (saved.answers &&
                            saved.answers[input.name] === input.value) {
                            input.checked = true;
                        }
                    });
                });

                if (Number.isInteger(saved.step)) {
                    currentStep = Math.max(
                        0,
                        Math.min(total - 1, saved.step)
                    );
                }
            } catch (error) {}
        }

        function renderStep(readAloud = false) {
            stopSpeech();

            steps.forEach((step, index) => {
                step.hidden = index !== currentStep;
            });

            const percentage = Math.round(
                (currentStep + 1) / total * 100
            );

            currentLabel.textContent = currentStep + 1;
            percentLabel.textContent = percentage + "%";
            progressFill.style.width = percentage + "%";

            progressBar.setAttribute(
                "aria-valuenow",
                String(currentStep + 1)
            );

            previousButton.hidden = currentStep === 0;
            nextButton.hidden = currentStep === total - 1;
            finishButton.hidden = currentStep !== total - 1;

            message.textContent = "";

            saveProgress();

            if (readAloud) {
                readCurrentQuestion();
            }

            window.scrollTo({
                top: 0,
                behavior: window.matchMedia(
                    "(prefers-reduced-motion: reduce)"
                ).matches ? "auto" : "smooth"
            });
        }

        function isAnswered(index) {
            return !!steps[index].querySelector(
                'input[type="radio"]:checked'
            );
        }

        nextButton.addEventListener("click", function () {
            if (!isAnswered(currentStep)) {
                message.textContent =
                    "💛 Elige una respuesta para continuar.";

                const first = steps[currentStep].querySelector(
                    'input[type="radio"]'
                );

                if (first) first.focus();

                return;
            }

            if (currentStep < total - 1) {
                currentStep++;
                renderStep(true);
            }
        });

        previousButton.addEventListener("click", function () {
            if (currentStep > 0) {
                currentStep--;
                renderStep(true);
            }
        });

        document.querySelectorAll("[data-read]").forEach(button => {
            button.addEventListener("click", function () {
                const index = Number(button.dataset.read);
                const step = steps[index];

                const title = step.querySelector("h2");
                const detail = step.querySelector(".pm-question-detail");

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
            for (let index = 0; index < total; index++) {
                if (!isAnswered(index)) {
                    event.preventDefault();
                    currentStep = index;
                    renderStep(false);

                    message.textContent =
                        "💛 Responde esta misión antes de terminar.";

                    return;
                }
            }

            stopSpeech();

            try {
                localStorage.removeItem(storageKey);
            } catch (error) {}
        });

        restoreProgress();
        renderStep(false);
    }

    /* ====================================
       MINIJUEGO DEL DINOSAURIO
    ==================================== */

    const dialog = document.getElementById("pmBreakDialog");
    const openBreak = document.getElementById("pmOpenBreak");
    const resume = document.getElementById("pmResume");
    const jumpButton = document.getElementById("pmJump");

    const canvas = document.getElementById("pmCanvas");
    const ctx = canvas.getContext("2d");

    const scoreLabel = document.getElementById("pmScore");
    const timerLabel = document.getElementById("pmTimer");

    const ground = 150;

    let playing = false;
    let animationId = null;
    let timerId = null;
    let lastFrame = 0;
    let score = 0;
    let remaining = 600;

    const dino = {
        x: 48,
        y: 112,
        width: 34,
        height: 38,
        velocity: 0,
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
        y: 80,
        radius: 10,
        active: true
    };

    function jump() {
        if (!playing || !dino.grounded) return;

        dino.velocity = -10.5;
        dino.grounded = false;
    }

    function drawGame() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = "#EAF9F2";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = "#FFFFFF";
        ctx.beginPath();
        ctx.arc(90, 40, 16, 0, Math.PI * 2);
        ctx.arc(110, 34, 20, 0, Math.PI * 2);
        ctx.arc(130, 42, 15, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = "#A6DDB8";
        ctx.fillRect(0, ground, canvas.width, 30);

        ctx.fillStyle = "#249B76";
        ctx.fillRect(dino.x + 3, dino.y + 12, 26, 23);
        ctx.fillRect(dino.x + 17, dino.y, 17, 20);

        ctx.fillStyle = "#FFFFFF";
        ctx.fillRect(dino.x + 27, dino.y + 5, 4, 4);

        ctx.fillStyle = "#176B57";
        ctx.fillRect(dino.x + 7, dino.y + 33, 7, 5);
        ctx.fillRect(dino.x + 21, dino.y + 33, 7, 5);

        ctx.fillStyle = "#E98D69";
        ctx.fillRect(
            obstacle.x,
            obstacle.y,
            obstacle.width,
            obstacle.height
        );

        ctx.fillStyle = "#C96D4E";
        ctx.fillRect(obstacle.x + 6, obstacle.y - 7, 10, 7);

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

    function updateGame(delta) {
        const factor = Math.min(delta / 16.67, 2);

        dino.velocity += 0.55 * factor;
        dino.y += dino.velocity * factor;

        if (dino.y >= ground - dino.height) {
            dino.y = ground - dino.height;
            dino.velocity = 0;
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

        const collision =
            dino.x + dino.width > obstacle.x &&
            dino.x < obstacle.x + obstacle.width &&
            dino.y + dino.height > obstacle.y &&
            dino.y < obstacle.y + obstacle.height;

        if (collision) {
            score = Math.max(0, score - 5);
            obstacle.x = canvas.width + 60;
        }

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

        updateGame(delta);
        drawGame();

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
        score = 0;
        remaining = 600;
        lastFrame = 0;

        dino.y = ground - dino.height;
        dino.velocity = 0;
        dino.grounded = true;

        obstacle.x = 460;
        star.x = 600;
        star.active = true;

        scoreLabel.textContent = "0";
        updateTimer();

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
            stopSpeech();
            dialog.showModal();
            startGame();
        });
    }

    resume.addEventListener("click", function () {
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
        stopSpeech();
        stopGame();
    });
});
</script>

</body>
</html>
