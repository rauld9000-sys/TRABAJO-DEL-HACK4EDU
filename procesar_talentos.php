<?php
session_start();

/* =========================================
   PENGUICH - INTELIGENCIAS MÚLTIPLES (ÁRBOL)
   ========================================= */

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli("localhost", "root", "", "penguich");
    $conexion->set_charset("utf8mb4");
} catch (Throwable $e) {
    http_response_code(500);
    exit("No se pudo conectar con la base de datos.");
}

function escapar($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

$nombre_usuario = (string)$_SESSION['usuario'];

$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ? LIMIT 1");
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
   DEFINICIÓN DE PERFILES / INTELIGENCIAS
   ========================================= */

$perfiles = [
    "A" => ["nombre" => "Lingüística", "icono" => "📚", "descripcion" => "Tienes gran facilidad para las palabras, las historias y la expresión oral o escrita.", "consejo" => "Escribe cuentos, participa en debates o lee novelas fascinantes."],
    "B" => ["nombre" => "Lógico-Matemática", "icono" => "🧩", "descripcion" => "Te encantan los números, los acertijos, la lógica y encontrar patrones ocultos.", "consejo" => "Resuelve retos de ingenio, rompecabezas lógicos y experimentos numéricos."],
    "C" => ["nombre" => "Espacial / Visual", "icono" => "🎨", "descripcion" => "Visualizas el mundo en formas, colores, mapas mentales y construcciones en 3D.", "consejo" => "Diseña maquetas, explora el arte visual y crea planos imaginarios."],
    "D" => ["nombre" => "Kinestésica / Corporal", "icono" => "⚡", "descripcion" => "Tu cuerpo y tus manos tienen gran energía, coordinación y destreza para la acción.", "consejo" => "Practica deportes, baile, teatro físico o construcción de objetos reales."],
    "E" => ["nombre" => "Musical", "icono" => "🎵", "descripcion" => "Los ritmos, los sonidos, las melodías y la música resuenan naturalmente en ti.", "consejo" => "Toca un instrumento, compone canciones o experimenta con sonidos."],
    "F" => ["nombre" => "Interpersonal", "icono" => "🤝", "descripcion" => "Entiendes a las personas a la perfección y disfrutas colaborar y unirlas en equipo.", "consejo" => "Organiza proyectos grupales, actividades colaborativas y dinámicas sociales."],
    "G" => ["nombre" => "Intrapersonal", "icono" => "🌟", "descripcion" => "Te conoces muy bien a ti mismo, eres independiente y valoras tus momentos de reflexión.", "consejo" => "Lleva un diario personal, medita y plantea tus propias metas de vida."]
];

/* =========================================
   CREAR TABLA AUTOMÁTICAMENTE
   ========================================= */

$conexion->query("
    CREATE TABLE IF NOT EXISTS test_inteligencias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        puntaje_a INT NOT NULL DEFAULT 0,
        puntaje_b INT NOT NULL DEFAULT 0,
        puntaje_c INT NOT NULL DEFAULT 0,
        puntaje_d INT NOT NULL DEFAULT 0,
        puntaje_e INT NOT NULL DEFAULT 0,
        puntaje_f INT NOT NULL DEFAULT 0,
        puntaje_g INT NOT NULL DEFAULT 0,
        perfil_predominante VARCHAR(150) NOT NULL,
        porcentaje_predominante DECIMAL(5,2) NOT NULL,
        respuestas_json LONGTEXT NOT NULL,
        fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (usuario_id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

/* =========================================
   PROCESAR RESPUESTAS DEL ÁRBOL
   ========================================= */

$guardado_exitoso = false;
$error = "";
$puntajes = ["A" => 0, "B" => 0, "C" => 0, "D" => 0, "E" => 0, "F" => 0, "G" => 0];
$perfil_predominante = "";
$porcentaje_predominante = 0;
$perfiles_ganadores = [];
$total_preguntas = 10;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["enviar_test"])) {
    $respuestas = [];

    // Recopilar respuestas del 1 al 10 enviadas por el formulario
    for ($i = 1; $i <= 10; $i++) {
        $clave = "P" . $i;
        $respuesta = $_POST[$clave] ?? null;

        if (!is_string($respuesta || !array_key_exists($respuesta, $puntajes))) {
            // Si falta alguna respuesta por el árbol, asignamos por defecto o validamos
            $respuesta = 'A'; 
        }
        $respuestas[$clave] = $respuesta;
        $puntajes[$respuesta]++;
    }

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

    $perfil_predominante = implode(" / ", $nombres_ganadores);
    $porcentaje_predominante = ($maximo / $total_preguntas) * 100;
    $respuestas_json = json_encode($respuestas, JSON_UNESCAPED_UNICODE);

    try {
        $stmt = $conexion->prepare("
            INSERT INTO test_inteligencias (
                usuario_id, puntaje_a, puntaje_b, puntaje_c, puntaje_d, puntaje_e, puntaje_f, puntaje_g,
                perfil_predominante, porcentaje_predominante, respuestas_json
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iiiiiiisdss",
            $usuario_id,
            $puntajes["A"], $puntajes["B"], $puntajes["C"], $puntajes["D"],
            $puntajes["E"], $puntajes["F"], $puntajes["G"],
            $perfil_predominante, $porcentaje_predominante, $respuestas_json
        );

        $stmt->execute();
        $stmt->close();
        $guardado_exitoso = true;
    } catch (Throwable $e) {
        $error = "No se pudo guardar el resultado. Inténtalo nuevamente.";
    }
}

$iniciar_quiz = (isset($_GET["iniciar"]) && $_GET["iniciar"] === "1") || ($_SERVER["REQUEST_METHOD"] === "POST" && !$guardado_exitoso);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Penguich | Inteligencias Múltiples</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
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

* { box-sizing: border-box; }

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
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

.quiz-header { text-align: center; margin-bottom: 30px; }

.app-icon {
    width: 78px; height: 78px;
    border-radius: 24px;
    background: linear-gradient(135deg, #D5F7E8, #FFF1C7);
    border: 2px solid #BCE8D6;
    margin: 0 auto 18px;
    display: flex; justify-content: center; align-items: center;
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
    font-weight: 900; margin: 0 0 10px; color: #176F65;
}

.quiz-header p { font-size: 1.04rem; color: var(--gris); line-height: 1.6; }

.welcome-box {
    padding: 25px; border-radius: 20px;
    background: #F0FBF6; border: 2px solid #D4EEE3;
    text-align: center; margin-bottom: 25px;
}
.welcome-box p { line-height: 1.7; margin: 10px 0; }

.section-title {
    margin: 28px 0 18px; padding-bottom: 12px;
    border-bottom: 3px solid #CBEFE1; color: #177C70;
    font-size: 1.25rem; font-weight: 900;
}

.question-step { display: none; }
.question-step.active { display: block; animation: cardBounce 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

@keyframes cardBounce {
    0% { opacity: 0; transform: scale(0.95) translateY(15px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

.question-card {
    background: #F8FCFA; border: 2px solid #E1EFE9;
    border-radius: 20px; padding: 22px; margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}

.question-card h3 {
    margin: 0; font-size: 1.15rem; line-height: 1.65; color: #245B52; font-weight: 800;
}

.question-number {
    display: inline-block; background: #E0F5EA; color: #16766B;
    border-radius: 30px; padding: 6px 13px; font-size: .8rem; font-weight: 900; margin-bottom: 12px;
}

.options-group { display: flex; flex-direction: column; gap: 12px; margin-top: 17px; }

.option-label {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 15px; background: white; border: 2px solid #E0EAE5;
    border-radius: 13px; cursor: pointer; font-size: .95rem; line-height: 1.55;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.option-label:hover {
    background: #F0FBF6; border-color: var(--turquesa);
    transform: translateX(8px) scale(1.01);
    box-shadow: 0 6px 20px rgba(42,157,143,0.15);
}

.option-label input:checked + span, .option-label:has(input:checked) {
    background: #E1F7ED; border-color: var(--turquesa);
    box-shadow: 0 3px 12px rgba(42,157,143,.15); font-weight: 700;
}

.option-label input {
    margin-top: 4px; accent-color: var(--turquesa); width: 18px; height: 18px; flex-shrink: 0; cursor: pointer;
}

.option-letter { font-weight: 900; color: #177C70; }

.btn-submit {
    display: block; width: 100%; padding: 16px 20px; border: none; border-radius: 15px;
    background: linear-gradient(135deg, #2A9D8F, #168477); color: white;
    font-family: 'Nunito', sans-serif; font-size: 1.05rem; font-weight: 900;
    text-decoration: none; text-align: center; cursor: pointer;
    box-shadow: 0 6px 16px rgba(42,157,143,.22); transition: .2s;
}
.btn-submit:hover { transform: translateY(-2px); background: linear-gradient(135deg, #238F82, #126C62); }

.btn-secondary { background: #EDF5F2; color: #216C62; box-shadow: none; }
.btn-secondary:hover { background: #DDEEE8; }

.buttons-row { display: flex; gap: 12px; margin-top: 20px; }
.buttons-row a, .buttons-row button { flex: 1; }

.progress-wrapper { margin: 0 0 25px; padding: 17px; border-radius: 16px; background: #F1FAF5; }
.progress-info { display: flex; justify-content: space-between; font-size: .9rem; font-weight: 800; margin-bottom: 10px; }
.progress-track { height: 12px; background: #D8EDE4; border-radius: 20px; overflow: hidden; }
.progress-fill { width: 10%; height: 100%; background: linear-gradient(90deg, #2A9D8F, #F4C95D); border-radius: 20px; transition: width .4s ease; }

.result-hero {
    padding: 28px; text-align: center; border-radius: 22px;
    background: linear-gradient(135deg, #E1F7EB, #FFF6D9); border: 2px solid #BCE8D6; margin-bottom: 25px;
}
.result-hero h2 { color: #176F65; font-size: 1.65rem; }
.result-hero .emoji { font-size: 55px; }
.result-hero .percentage { font-size: 1.5rem; font-weight: 900; color: #197F72; }

.result-card { border: 2px solid #E1EFE9; background: #FAFDFC; padding: 18px; border-radius: 16px; margin-bottom: 14px; }
.result-card h3 { margin: 0 0 10px; color: #24675E; font-size: 1rem; }
.result-track { height: 13px; background: #E1EEE8; border-radius: 20px; overflow: hidden; }
.result-fill { height: 100%; background: linear-gradient(90deg, #2A9D8F, #F4C95D); border-radius: 20px; }
.result-card p { margin-bottom: 0; font-size: .9rem; color: var(--gris); }

.tip-box { margin: 22px 0; padding: 20px; background: #FFF9E8; border: 2px solid #F5E5B7; border-radius: 17px; line-height: 1.65; }
.error-box { background: #FFF0EF; border: 2px solid #F3B6AF; color: #9D3029; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 800; }
.small-note { color: var(--gris); font-size: .86rem; text-align: center; margin-top: 15px; }

#modal-pausa {
    display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px);
    justify-content: center; align-items: center; z-index: 1000;
}
.modal-content {
    background: #1E293B; color: #F8FAFC; padding: 30px; border-radius: 24px;
    width: 90%; max-width: 500px; text-align: center; border: 4px solid var(--amarillo);
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
}
canvas { background: #0F172A; border: 3px solid #38BDF8; border-radius: 12px; margin-top: 15px; box-shadow: inset 0 0 20px rgba(56, 189, 248, 0.2); }

:focus-visible { outline: 3px solid #E7A32F; outline-offset: 3px; }

@media(max-width: 600px) {
    .quiz-container { padding: 20px 15px; margin: 10px auto; }
    .buttons-row { flex-direction: column; }
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
    <div class="quiz-header">
        <div class="app-icon">🐧</div>
        <h1>¡Aventura completada!</h1>
        <p>¡Excelente trabajo, <?php echo escapar($nombre_usuario); ?>! Descubrimos tus inteligencias múltiples predominantes.</p>
    </div>

    <div class="result-hero">
        <div class="emoji">
            <?php foreach ($perfiles_ganadores as $letra) { echo escapar($perfiles[$letra]["icono"]) . " "; } ?>
        </div>
        <h2><?php echo count($perfiles_ganadores) > 1 ? "¡Tienes varios perfiles destacados!" : "¡Tu perfil destacado!"; ?></h2>
        <h2><?php echo escapar($perfil_predominante); ?></h2>
        <div class="percentage"><?php echo number_format($porcentaje_predominante, 1); ?>%</div>
        <p>Afinidad con tus respuestas principales.</p>
    </div>

    <?php foreach ($perfiles_ganadores as $letra): ?>
        <div class="tip-box">
            <h3><?php echo escapar($perfiles[$letra]["icono"]); ?> <?php echo escapar($perfiles[$letra]["nombre"]); ?></h3>
            <p><?php echo escapar($perfiles[$letra]["descripcion"]); ?></p>
            <strong>🌟 Un reto para ti:</strong>
            <p><?php echo escapar($perfiles[$letra]["consejo"]); ?></p>
        </div>
    <?php endforeach; ?>

    <h2 class="section-title">📊 Tus resultados detallados</h2>
    <?php foreach ($puntajes as $letra => $puntos): 
        $porcentaje = ($puntos / $total_preguntas) * 100;
    ?>
        <div class="result-card">
            <h3><?php echo escapar($perfiles[$letra]["icono"]); ?> <?php echo escapar($perfiles[$letra]["nombre"]); ?></h3>
            <div class="result-track">
                <div class="result-fill" style="width: <?php echo $porcentaje; ?>%"></div>
            </div>
            <p><?php echo $puntos; ?> de <?php echo $total_preguntas; ?> respuestas (<?php echo number_format($porcentaje, 1); ?>%)</p>
        </div>
    <?php endforeach; ?>

    <div class="buttons-row">
        <a href="usuario.php" class="btn-submit" style="text-decoration:none;">🏠 Volver al panel</a>
        <a href="procesar_inteligencia.php?iniciar=1" class="btn-submit btn-secondary" style="text-decoration:none;">🔄 Repetir test</a>
    </div>

<?php elseif (!$iniciar_quiz): ?>
    <div class="quiz-header">
        <div class="app-icon">🐧</div>
        <h1>Inteligencias Múltiples</h1>
        <p>¡Hola, <?php echo escapar($nombre_usuario); ?>! Acompaña a Penguich a descubrir cómo brilla tu mente a través de una aventura ramificada.</p>
    </div>

    <div class="welcome-box">
        <h2>🌟 ¡El Camino de las Inteligencias!</h2>
        <p>Este test consta de <strong>10 preguntas interactivas en forma de árbol</strong>. Dependiendo de lo que elijas al inicio, las preguntas se adaptarán a tus mayores gustos.</p>
        <p><strong>🎯 ¡Cada forma de pensar es un superpoder único!</strong></p>
    </div>

    <a href="procesar_inteligencia.php?iniciar=1" class="btn-submit" style="text-decoration:none;">🚀 ¡Comenzar aventura!</a>
    <p class="small-note">Diseñado para que lo resuelvas paso a paso a tu propio ritmo.</p>

<?php else: ?>

    <div class="quiz-header" style="margin-bottom: 15px;">
        <div class="app-icon" style="width: 60px; height: 60px; font-size: 2.2rem; margin-bottom: 8px;">🐧</div>
        <h2 style="margin:0; color: #176F65; font-size: 1.5rem;">Inteligencias Múltiples</h2>
        <p style="margin: 6px 0 0 0; font-size: 0.95rem; color: var(--gris); font-weight: 700;">
            Pregunta <span id="current-q-num">1</span> de 10
        </p>
    </div>

    <?php if ($error !== ""): ?>
        <div class="error-box">⚠️ <?php echo escapar($error); ?></div>
    <?php endif; ?>

    <div class="progress-wrapper">
        <div class="progress-info">
            <span>🌟 Tu avance en el árbol</span>
            <span id="progress-text">1 / 10</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill" id="progress-fill"></div>
        </div>
    </div>

    <form action="procesar_inteligencia.php" method="POST" id="quiz-form">

        <!-- PASO 1: RAÍZ -->
        <div class="question-step active" data-step="1">
            <div class="question-card">
                <span class="question-number">Pregunta 1 de 10 (Raíz Principal)</span>
                <h3>Cuando tienes la libertad absoluta de elegir qué hacer durante una tarde completa sin reglas, ¿cuál de estas grandes aventuras llama más tu atención?</h3>
                <div class="options-group">
                    <label class="option-label"><input type="radio" name="P1" value="A" required><span><span class="option-letter">A)</span> Leer cuentos, inventar historias fantásticas o escribir mis propias palabras.</span></label>
                    <label class="option-label"><input type="radio" name="P1" value="B"><span><span class="option-letter">B)</span> Resolver acertijos difíciles, jugar con números o armar rompecabezas lógicos.</span></label>
                    <label class="option-label"><input type="radio" name="P1" value="C"><span><span class="option-letter">C)</span> Dibujar mapas, imaginar mundos en 3D o diseñar construcciones visuales.</span></label>
                    <label class="option-label"><input type="radio" name="P1" value="D"><span><span class="option-letter">D)</span> Moverme, bailar, hacer deportes o construir cosas con mis propias manos.</span></label>
                    <label class="option-label"><input type="radio" name="P1" value="E"><span><span class="option-letter">E)</span> Tararear canciones, inventar ritmos con objetos o escuchar música sin parar.</span></label>
                    <label class="option-label"><input type="radio" name="P1" value="F"><span><span class="option-letter">F)</span> Organizar a mis amigos, jugar en equipo o ayudar a resolver conflictos.</span></label>
                    <label class="option-label"><input type="radio" name="P1" value="G"><span><span class="option-letter">G)</span> Pensar a solas, reflexionar sobre mis metas o crear mis propias reglas en silencio.</span></label>
                </div>
            </div>
            <div class="buttons-row">
                <button type="button" class="btn-submit" onclick="avanzarDesdeRaiz()">Siguiente rama ➡️</button>
            </div>
        </div>

        <!-- PASOS 2 al 9: RAMAS INTERMEDIAS DINÁMICAS (Se muestran según la rama elegida) -->
        <?php 
        $ramas_ids = ['A','B','C','D','E','F','G'];
        $preguntas_ramas = [
            2 => "Si participaras en un concurso especial, ¿en qué categoría te destacarías con naturalidad?",
            3 => "Cuando intentas comprender un tema nuevo y difícil, ¿qué te ayuda más a que 'haga clic' en tu cabeza?",
            4 => "¿Qué tipo de personajes, héroes o profesionales te fascinan más en la vida o en los libros?",
            5 => "¿Cuál de estas actividades disfrutas con mayor entusiasmo y facilidad en tu día a día?",
            6 => "Cuando tienes un problema complejo o un dilema personal que resolver, ¿cómo actúas comúnmente?",
            7 => "Si te dieran un recurso especial para diseñar tu espacio ideal desde cero, ¿en qué te enfocarías?",
            8 => "¿Qué detalle de tu entorno notas con mayor rapidez que los demás cuando entras a un lugar nuevo?",
            9 => "Si tuvieras que crear una obra memorable para dejar un legado en el mundo, ¿de qué trataría principalmente?"
        ];

        foreach ($ramas_ids as $r):
            for ($step = 2; $step <= 9; $step++):
        ?>
            <div class="question-step" data-step="<?php echo $step; ?>" data-branch="<?php echo $r; ?>">
                <div class="question-card">
                    <span class="question-number">Pregunta <?php echo $step; ?> de 10 (Rama <?php echo $r; ?>)</span>
                    <h3><?php echo $preguntas_ramas[$step]; ?></h3>
                    <div class="options-group">
                        <label class="option-label"><input type="radio" name="P<?php echo $step; ?>" value="A"><span><span class="option-letter">A)</span> Enfocarme en la lectura, redacción y expresión con palabras.</span></label>
                        <label class="option-label"><input type="radio" name="P<?php echo $step; ?>" value="B"><span><span class="option-letter">B)</span> Aplicar lógica, razonamiento numérico y cálculos exactos.</span></label>
                        <label class="option-label"><input type="radio" name="P<?php echo $step; ?>" value="C"><span><span class="option-letter">C)</span> Utilizar formas visuales, perspectiva, diseño espacial e imaginación.</span></label>
                        <label class="option-label"><input type="radio" name="P<?php echo $step; ?>" value="D"><span><span class="option-letter">D)</span> Usar la destreza física, el movimiento corporal y el tacto directo.</span></label>
                        <label class="option-label"><input type="radio" name="P<?php echo $step; ?>" value="E"><span><span class="option-letter">E)</span> Incorporar melodías, tonos, compases y armonía auditiva.</span></label>
                        <label class="option-label"><input type="radio" name="P<?php echo $step; ?>" value="F"><span><span class="option-letter">F)</span> Priorizar la empatía, la comunicación y el trabajo colaborativo grupal.</span></label>
                        <label class="option-label"><input type="radio" name="P<?php echo $step; ?>" value="G"><span><span class="option-letter">G)</span> Fomentar la introspección, la independencia y el autoconocimiento profundo.</span></label>
                    </div>
                </div>
                <div class="buttons-row">
                    <button type="button" class="btn-submit btn-secondary" onclick="retrocederPaso(<?php echo $step; ?>)">⬅️ Anterior</button>
                    <button type="button" class="btn-submit" onclick="siguientePasoRama(<?php echo $step; ?>)">Siguiente ➡️</button>
                </div>
            </div>
        <?php 
            endfor;
        endforeach; 
        ?>

        <!-- PASO 10: CIERRE UNIVERSAL -->
        <div class="question-step" data-step="10">
            <div class="question-card">
                <span class="question-number">Pregunta 10 de 10 (Meta Final)</span>
                <h3>¿Cuál de estas frases describe mejor la superpotencia que sientes que vive de forma natural dentro de ti?</h3>
                <div class="options-group">
                    <label class="option-label"><input type="radio" name="P10" value="A" required><span><span class="option-letter">A)</span> "Las palabras y las historias son mi mejor herramienta para conectar."</span></label>
                    <label class="option-label"><input type="radio" name="P10" value="B"><span><span class="option-letter">B)</span> "Los números, la lógica y los misterios por resolver son lo mío."</span></label>
                    <label class="option-label"><input type="radio" name="P10" value="C"><span><span class="option-letter">C)</span> "Puedo ver todo en mi mente antes de dibujarlo o construirlo."</span></label>
                    <label class="option-label"><input type="radio" name="P10" value="D"><span><span class="option-letter">D)</span> "Mi cuerpo y mis manos tienen energía y habilidad para la acción."</span></label>
                    <label class="option-label"><input type="radio" name="P10" value="E"><span><span class="option-letter">E)</span> "Los ritmos y los sonidos guían mis emociones a cada instante."</span></label>
                    <label class="option-label"><input type="radio" name="P10" value="F"><span><span class="option-letter">F)</span> "Entiendo perfectamente a los demás y me encanta unirlos en equipo."</span></label>
                    <label class="option-label"><input type="radio" name="P10" value="G"><span><span class="option-letter">G)</span> "Me conozco a la perfección, disfruto mi espacio y soy muy independiente."</span></label>
                </div>
            </div>
            <div class="buttons-row">
                <button type="button" class="btn-submit btn-secondary" onclick="retrocederPaso(10)">⬅️ Anterior</button>
                <button type="submit" name="enviar_test" value="1" class="btn-submit" style="background: linear-gradient(135deg, #48BB78, #38A169);">💾 Guardar mi diagnóstico</button>
            </div>
        </div>

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
    const totalSteps = 10;
    let selectedBranch = 'A'; // Rama por defecto

    function actualizarProgresoWizard() {
        const barra = document.getElementById("progress-fill");
        const texto = document.getElementById("progress-text");
        const qNum = document.getElementById('current-q-num');
        if (barra && texto) {
            let porcentaje = (currentStep / totalSteps) * 100;
            barra.style.width = porcentaje + "%";
            texto.textContent = currentStep + " / " + totalSteps;
            if (qNum) qNum.innerText = currentStep;
        }
    }

    function avanzarDesdeRaiz() {
        const raizCard = document.querySelector('.question-step[data-step="1"]');
        const checkedRaiz = raizCard.querySelector('input[type="radio"]:checked');
        if (!checkedRaiz) {
            alert("🐧 Por favor selecciona una opción en la pregunta raíz antes de continuar.");
            return;
        }
        selectedBranch = checkedRaiz.value; // Guarda la rama (A, B, C, D, E, F o G)

        raizCard.classList.remove('active');
        currentStep = 2;

        // Muestra la tarjeta correspondiente a la rama y paso 2
        const nextCard = document.querySelector(`.question-step[data-step="2"][data-branch="${selectedBranch}"]`);
        if (nextCard) {
            nextCard.classList.add('active');
            actualizarProgresoWizard();
            window.scrollTo({ top: 150, behavior: 'smooth' });
        }
    }

    function siguientePasoRama(step) {
        const currentCard = document.querySelector(`.question-step[data-step="${step}"][data-branch="${selectedBranch}"]`);
        if (!currentCard) return;

        const radios = currentCard.querySelectorAll('input[type="radio"]');
        let seleccionado = false;
        radios.forEach(r => { if(r.checked) seleccionado = true; });

        if(!seleccionado) {
            alert("🐧 Por favor selecciona una opción antes de continuar.");
            return;
        }

        currentCard.classList.remove('active');
        currentStep = step + 1;

        if (currentStep === 10) {
            // Muestra la tarjeta de cierre final (Paso 10)
            const closingCard = document.querySelector(`.question-step[data-step="10"]`);
            if (closingCard) {
                closingCard.classList.add('active');
                actualizarProgresoWizard();
                window.scrollTo({ top: 150, behavior: 'smooth' });
            }
        } else {
            const nextCard = document.querySelector(`.question-step[data-step="${currentStep}"][data-branch="${selectedBranch}"]`);
            if (nextCard) {
                nextCard.classList.add('active');
                actualizarProgresoWizard();
                window.scrollTo({ top: 150, behavior: 'smooth' });
            }
        }
    }

    function retrocederPaso(step) {
        // Oculta la tarjeta actual activa
        const activeCards = document.querySelectorAll('.question-step.active');
        activeCards.forEach(c => c.classList.remove('active'));

        currentStep = step - 1;

        if (currentStep === 1) {
            const raizCard = document.querySelector('.question-step[data-step="1"]');
            if (raizCard) raizCard.classList.add('active');
        } else if (currentStep === 9) {
            const prevCard = document.querySelector(`.question-step[data-step="9"][data-branch="${selectedBranch}"]`);
            if (prevCard) prevCard.classList.add('active');
        } else {
            const prevCard = document.querySelector(`.question-step[data-step="${currentStep}"][data-branch="${selectedBranch}"]`);
            if (prevCard) prevCard.classList.add('active');
        }

        actualizarProgresoWizard();
        window.scrollTo({ top: 150, behavior: 'smooth' });
    }

    /* =========================================
       MINIJUEGO DINOSAURIO REALISTA & PUNTITOS
       ========================================= */
    let timerInterval;
    let tiempoRestante = 600; // 10 minutos

    function abrirPausa() {
        const modal = document.getElementById('modal-pausa');
        if (modal) {
            modal.style.display = 'flex';
            iniciarDinoJuego();
        }
        if(!timerInterval) {
            timerInterval = setInterval(() => {
                tiempoRestante--;
                let m = Math.floor(tiempoRestante / 60);
                let s = tiempoRestante % 60;
                const timerEl = document.getElementById('timer');
                if(timerEl) timerEl.innerText = `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
                if(tiempoRestante <= 0) { clearInterval(timerInterval); cerrarPausa(); }
            }, 1000);
        }
    }

    function cerrarPausa() {
        const modal = document.getElementById('modal-pausa');
        if (modal) modal.style.display = 'none';
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
        if (!canvas) return;
        ctx = canvas.getContext('2d');
        juegoActivo = true;
        puntosJuego = 0;
        const scoreEl = document.getElementById('score');
        if (scoreEl) scoreEl.innerText = puntosJuego;
        
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
            const modal = document.getElementById('modal-pausa');
            if(modal && modal.style.display === 'flex') {
                e.preventDefault(); 
            }
        }
    });

    function loopDino() {
        if(!juegoActivo || !ctx || !canvas) return;

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

        // Dibujar Dino Realista/Estilizado
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

            let distX = (dino.x + dino.w/2) - puntito.x;
            let distY = (dino.y + dino.h/2) - puntito.y;
            let distancia = Math.sqrt(distX * distX + distY * distY);

            if (distancia < puntito.radius + 15) {
                puntosJuego += 10;
                const scoreEl = document.getElementById('score');
                if (scoreEl) scoreEl.innerText = puntosJuego;
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
            const scoreEl = document.getElementById('score');
            if (scoreEl) scoreEl.innerText = puntosJuego;
            cactus.x = canvas.width + 50;
        }

        requestAnimationFrame(loopDino);
    }
</script>

</body>
</html>