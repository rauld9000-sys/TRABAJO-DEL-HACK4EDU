<?php
// Mostrar errores para depurar fácilmente
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Si no hay sesión activa, creamos una por defecto
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = 'Tania';
}

$servidor = "localhost";
$usuario_db = "root";
$password_db = "";
$nombre_db = "penguich";

$conexion = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener o registrar automáticamente el usuario en la base de datos
$nombre_usuario = $_SESSION['usuario'];
$sql_user = "SELECT id FROM usuarios WHERE usuario = '$nombre_usuario'";
$res_user = $conexion->query($sql_user);

if ($res_user && $res_user->num_rows > 0) {
    $row_user = $res_user->fetch_assoc();
    $usuario_id = $row_user['id'];
} else {
    $sql_insert_user = "INSERT INTO usuarios (usuario) VALUES ('$nombre_usuario')";
    if ($conexion->query($sql_insert_user) === TRUE) {
        $usuario_id = $conexion->insert_id;
    } else {
        $usuario_id = 1;
    }
}

$estilo_predominante = "";
$porcentaje_predominante = 0;
$puntaje_visual = 0;
$puntaje_auditivo = 0;
$puntaje_kinestesico = 0;
$puntaje_lectoescritura = 0;
$guardado_exitoso = false;

// Procesar el test al enviar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enviar_test'])) {
    foreach ($_POST as $clave => $valor) {
        if ($clave === 'enviar_test') continue;
        
        if ($valor === 'A') { $puntaje_visual++; }
        elseif ($valor === 'B') { $puntaje_auditivo++; }
        elseif ($valor === 'C') { $puntaje_kinestesico++; }
        elseif ($valor === 'D') { $puntaje_lectoescritura++; }
    }
    
    $total_respuestas = $puntaje_visual + $puntaje_auditivo + $puntaje_kinestesico + $puntaje_lectoescritura;
    if ($total_respuestas == 0) $total_respuestas = 1;

    $puntajes = [
        'Visual' => $puntaje_visual,
        'Auditivo' => $puntaje_auditivo,
        'Kinestésico' => $puntaje_kinestesico,
        'Lecto-Escritura' => $puntaje_lectoescritura
    ];

    arsort($puntajes);
    $estilo_predominante = key($puntajes);
    $max_puntaje = current($puntajes);
    $porcentaje_predominante = ($max_puntaje / $total_respuestas) * 100;

    // Crear tabla si no existe
    $sql_crear_tabla = "CREATE TABLE IF NOT EXISTS test_estilo_aprendizaje (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        puntaje_visual INT DEFAULT 0,
        puntaje_auditivo INT DEFAULT 0,
        puntaje_kinestesico INT DEFAULT 0,
        puntaje_lectoescritura INT DEFAULT 0,
        estilo_predominante VARCHAR(30) NOT NULL,
        porcentaje_predominante DECIMAL(5,2) NOT NULL,
        fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conexion->query($sql_crear_tabla);

    // Guardar resultados
    $sql_insertar = "INSERT INTO test_estilo_aprendizaje 
        (usuario_id, puntaje_visual, puntaje_auditivo, puntaje_kinestesico, puntaje_lectoescritura, estilo_predominante, porcentaje_predominante) 
        VALUES 
        ($usuario_id, $puntaje_visual, $puntaje_auditivo, $puntaje_kinestesico, $puntaje_lectoescritura, '$estilo_predominante', $porcentaje_predominante)";
    
    if ($conexion->query($sql_insertar) === TRUE) {
        $guardado_exitoso = true;
    } else {
        echo "<script>alert('Error al guardar en la base de datos: " . $conexion->error . "');</script>";
    }
}

$iniciar_quiz = isset($_GET['iniciar']) && $_GET['iniciar'] == '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penguich - Test de Estilos de Aprendizaje</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Nunito', sans-serif;
      background: linear-gradient(135deg, #E6F4EC 0%, #D4EDDA 100%);
      color: #2D3748;
      margin: 0;
      padding: 20px;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .top-bar {
      width: 100%;
      max-width: 780px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    .brand-title {
      font-weight: 800;
      color: #2C5282;
      font-size: 1.2rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-pause {
      background: linear-gradient(135deg, #ECC94B 0%, #D69E2E 100%);
      color: #744210;
      border: none;
      padding: 10px 20px;
      font-weight: 800;
      border-radius: 14px;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(236,201,75,0.4);
      transition: all 0.3s ease;
    }
    .btn-pause:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 6px 20px rgba(236,201,75,0.6); }

    .quiz-container {
      width: 100%;
      max-width: 780px;
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(10px);
      border-radius: 28px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.08);
      padding: 40px;
      border: 3px solid #FFFFFF;
      position: relative;
    }
    .quiz-header { text-align: center; margin-bottom: 25px; }
    .app-icon {
      font-size: 2.8rem;
      background: linear-gradient(135deg, #EBF8FF 0%, #BEE3F8 100%);
      width: 80px;
      height: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin: 0 auto 15px auto;
      border: 3px solid #3182CE;
      box-shadow: 0 8px 20px rgba(49,130,206,0.2);
      animation: floatIcon 3s ease-in-out infinite;
    }
    @keyframes floatIcon {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-8px); }
    }
    
    .question-step { display: none; }
    .question-step.active { display: block; animation: cardBounce 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    @keyframes cardBounce {
      0% { opacity: 0; transform: scale(0.9) translateY(20px); }
      100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    .question-card {
      background: #F8FAFC;
      border: 2px solid #E2E8F0;
      border-radius: 20px;
      padding: 25px;
      margin-bottom: 22px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .question-card h3 { color: #2C5282; margin-top: 0; font-size: 1.25rem; font-weight: 700; line-height: 1.4; }
    .options-group { display: flex; flex-direction: column; gap: 14px; margin-top: 18px; }
    
    /* Animación fluida y atractiva al pasar el cursor y al seleccionar */
    .option-label {
      display: flex;
      align-items: center;
      gap: 14px;
      background: white;
      padding: 16px 20px;
      border-radius: 14px;
      border: 2px solid #E2E8F0;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      font-size: 1.05rem;
      font-weight: 600;
      color: #4A5568;
      position: relative;
      overflow: hidden;
    }
    .option-label:hover {
      background: #EBF8FF;
      border-color: #3182CE;
      transform: translateX(10px) scale(1.01);
      box-shadow: 0 6px 20px rgba(49,130,206,0.18);
      color: #2B6CB0;
    }
    .option-label input { accent-color: #3182CE; width: 20px; height: 20px; cursor: pointer; }
    
    /* Efecto cuando el radio está seleccionado */
    .option-label input:checked + span, .option-label:has(input:checked) {
      background: #EBF8FF;
      border-color: #3182CE;
      color: #2C5282;
      font-weight: 700;
    }

    .btn-action {
      background: linear-gradient(135deg, #3182CE 0%, #2B6CB0 100%);
      color: white;
      border: none;
      padding: 16px 28px;
      font-size: 1.15rem;
      font-weight: 800;
      border-radius: 14px;
      cursor: pointer;
      width: 100%;
      box-shadow: 0 6px 20px rgba(49,130,206,0.3);
      text-align: center;
      text-decoration: none;
      display: block;
      transition: all 0.3s ease;
    }
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(49,130,206,0.4); }
    .nav-buttons { display: flex; gap: 15px; margin-top: 20px; }
    .btn-secondary { background: linear-gradient(135deg, #CBD5E0 0%, #A0AEC0 100%); color: #2D3748; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .btn-secondary:hover { background: linear-gradient(135deg, #A0AEC0 0%, #718096 100%); }

    .progress-bar-container {
      width: 100%;
      background: #E2E8F0;
      border-radius: 12px;
      height: 12px;
      margin-bottom: 25px;
      overflow: hidden;
      padding: 2px;
      border: 1px solid #CBD5E0;
    }
    .progress-bar {
      height: 100%;
      background: linear-gradient(90deg, #3182CE 0%, #63B3ED 100%);
      border-radius: 10px;
      width: 10%;
      transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
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
      padding: 35px;
      border-radius: 24px;
      width: 90%;
      max-width: 520px;
      text-align: center;
      border: 4px solid #ECC94B;
      box-shadow: 0 25px 50px rgba(0,0,0,0.5);
      animation: modalPopup 0.3s ease-out;
    }
    @keyframes modalPopup {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    canvas {
      background: #0F172A;
      border: 3px solid #38BDF8;
      border-radius: 12px;
      margin-top: 15px;
      box-shadow: inset 0 0 20px rgba(56, 189, 248, 0.2);
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

  <div class="quiz-container">
    
    <?php if ($guardado_exitoso): ?>
        <div class="quiz-header">
          <div class="app-icon">🐧</div>
          <h1>¡Resultado Guardado!</h1>
          <p>¡Excelente trabajo descubriendo tu superpoder de aprendizaje con Pinguich!</p>
        </div>

        <div style="background: #EBF8FF; color: #2B6CB0; padding: 25px; border-radius: 18px; text-align: center; margin-bottom: 20px; border: 2px solid #3182CE;">
          <h2 style="margin: 0 0 10px 0; font-size: 1.8rem; color: #2C5282;">Tu estilo es: <?php echo htmlspecialchars($estilo_predominante); ?></h2>
          <p style="font-size: 1.2rem; font-weight: bold; margin: 0;">Afinidad: <?php echo number_format($porcentaje_predominante, 1); ?>%</p>
        </div>

        <div style="background: #F8FAFC; padding: 22px; border-radius: 16px; border: 2px solid #E2E8F0; margin-bottom: 20px;">
          <p style="margin: 0 0 12px 0; font-weight: 800; color: #2C5282;">📊 Tus puntos por estilo:</p>
          <p style="margin: 6px 0;">Visual: <strong><?php echo $puntaje_visual; ?></strong></p>
          <p style="margin: 6px 0;">Auditivo: <strong><?php echo $puntaje_auditivo; ?></strong></p>
          <p style="margin: 6px 0;">Kinestésico: <strong><?php echo $puntaje_kinestesico; ?></strong></p>
          <p style="margin: 6px 0;">Lecto-Escritura: <strong><?php echo $puntaje_lectoescritura; ?></strong></p>
        </div>

        <div style="display: flex; gap: 15px;">
          <a href="usuario.php" class="btn-action" style="flex: 1; text-decoration: none;">Volver al Panel</a>
          <a href="procesar_estilos.php?iniciar=1" class="btn-action btn-secondary" style="flex: 1; text-decoration: none;">Repetir Test</a>
        </div>

    <?php elseif (!$iniciar_quiz): ?>
        <div class="quiz-header">
          <div class="app-icon">🐧</div>
          <h1>Mi Estilo de Aprendizaje</h1>
          <p>¡Descubre cómo aprende mejor tu cerebro jugando con Pinguich!</p>
        </div>

        <div style="background: #F8FAFC; border: 2px solid #E2E8F0; border-radius: 18px; padding: 30px; margin-bottom: 25px; text-align: center; color: #4A5568;">
          <p style="font-size: 1.15rem; margin-top: 0; font-weight: 600;">Responde las 10 preguntas interactivas con tarjetas fluidas y animaciones mágicas.</p>
          <p style="margin-bottom: 0; font-size: 0.95rem; color: #718096;">⚡ ¡Incluye pausa con minijuego de dinosaurio y puntos interactivos!</p>
        </div>

        <div style="text-align: center;">
          <a href="procesar_estilos.php?iniciar=1" class="btn-action" style="display: inline-block; max-width: 280px; text-decoration: none;">🚀 ¡Comenzar Quiz!</a>
        </div>

    <?php else: ?>
        <div class="quiz-header" style="margin-bottom: 15px;">
          <div class="app-icon" style="width: 60px; height: 60px; font-size: 2.2rem; margin-bottom: 8px;">🐧</div>
          <h2 style="margin:0; color: #2C5282; font-size: 1.5rem;">Test de Estilos de Aprendizaje</h2>
          <p style="margin: 6px 0 0 0; font-size: 0.95rem; color: #718096; font-weight: 700;">Pregunta <span id="current-q-num">1</span> de <span id="total-q-num">10</span></p>
        </div>

        <div class="progress-bar-container">
          <div class="progress-bar" id="progress-bar"></div>
        </div>

        <form action="procesar_estilos.php" method="POST" id="quiz-form">
          
          <!-- Pregunta 1 -->
          <div class="question-step active" data-step="1">
            <div class="question-card">
              <h3>1. ¿Qué es lo primero que haces cuando aprendes algo totalmente nuevo?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q1" value="A" required> A) Miro imágenes, dibujos o los colores.</label>
                <label class="option-label"><input type="radio" name="Q1" value="B"> B) Pido que me expliquen hablando o escucho.</label>
                <label class="option-label"><input type="radio" name="Q1" value="C"> C) Lo toco, lo muevo o intento usarlo de inmediato.</label>
                <label class="option-label"><input type="radio" name="Q1" value="D"> D) Busco leer las instrucciones o notas escritas.</label>
              </div>
            </div>
            <button type="button" class="btn-action" onclick="siguientePaso(1)">Siguiente ➡️</button>
          </div>

          <!-- Pregunta 2 -->
          <div class="question-step" data-step="2">
            <div class="question-card">
              <h3>2. ¿Cómo recuerdas el camino a un lugar que visitaste una vez?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q2" value="A"> A) Me imagino el mapa, esquinas y colores de edificios.</label>
                <label class="option-label"><input type="radio" name="Q2" value="B"> B) Recuerdo ruidos o lo que me dijeron que escuchara.</label>
                <label class="option-label"><input type="radio" name="Q2" value="C"> C) Recuerdo el cansancio o los giros que dio mi cuerpo.</label>
                <label class="option-label"><input type="radio" name="Q2" value="D"> D) Recuerdo los nombres escritos en las calles.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(2)">⬅️ Anterior</button>
              <button type="button" class="btn-action" onclick="siguientePaso(2)">Siguiente ➡️</button>
            </div>
          </div>

          <!-- Pregunta 3 -->
          <div class="question-step" data-step="3">
            <div class="question-card">
              <h3>3. ¿Qué te ayuda más cuando el maestro explica algo difícil en el pizarrón?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q3" value="A"> A) Ver esquemas, mapas mentales o dibujos a color.</label>
                <label class="option-label"><input type="radio" name="Q3" value="B"> B) Escuchar su tono de voz y los ejemplos que cuenta.</label>
                <label class="option-label"><input type="radio" name="Q3" value="C"> C) Manipular un objeto mientras escucho.</label>
                <label class="option-label"><input type="radio" name="Q3" value="D"> D) Leer el texto en el libro en silencio.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(3)">⬅️ Anterior</button>
              <button type="button" class="btn-action" onclick="siguientePaso(3)">Siguiente ➡️</button>
            </div>
          </div>

          <!-- Pregunta 4 -->
          <div class="question-step" data-step="4">
            <div class="question-card">
              <h3>4. Cuando resuelves un problema difícil en silencio, ¿qué pasa en tu mente?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q4" value="A"> A) Veo los números moverse como fichas.</label>
                <label class="option-label"><input type="radio" name="Q4" value="B"> B) Me hablo en voz baja repitiendo los pasos.</label>
                <label class="option-label"><input type="radio" name="Q4" value="C"> C) Muevo los dedos o un lápiz para pensar.</label>
                <label class="option-label"><input type="radio" name="Q4" value="D"> D) Leo el problema en voz alta para entender.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(4)">⬅️ Anterior</button>
              <button type="button" class="btn-action" onclick="siguientePaso(4)">Siguiente ➡️</button>
            </div>
          </div>

          <!-- Pregunta 5 -->
          <div class="question-step" data-step="5">
            <div class="question-card">
              <h3>5. Si vas por tres cosas a la tienda sin anotarlas, ¿cómo las recuerdas?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q5" value="A"> A) Me imagino el empaque de cada producto.</label>
                <label class="option-label"><input type="radio" name="Q5" value="B"> B) Voy repitiendo los nombres en voz baja como cancioncita.</label>
                <label class="option-label"><input type="radio" name="Q5" value="C"> C) Levanto tres dedos o los asocio a mi cuerpo.</label>
                <label class="option-label"><input type="radio" name="Q5" value="D"> D) Deletreo mentalmente las letras iniciales.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(5)">⬅️ Anterior</button>
              <button type="button" class="btn-action" onclick="siguientePaso(5)">Siguiente ➡️</button>
            </div>
          </div>

          <!-- Pregunta 6 -->
          <div class="question-step" data-step="6">
            <div class="question-card">
              <h3>6. Cuando armas bloques o LEGO, ¿qué guía tus manos?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q6" value="A"> A) Ver la foto en la caja para copiar colores y formas.</label>
                <label class="option-label"><input type="radio" name="Q6" value="B"> B) Hablar solo imaginando historias para los muñecos.</label>
                <label class="option-label"><input type="radio" name="Q6" value="C"> C) El tacto; mis manos sienten cómo embonan por presión.</label>
                <label class="option-label"><input type="radio" name="Q6" value="D"> D) Seguir paso a paso el manual escrito.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(6)">⬅️ Anterior</button>
              <button type="button" class="btn-action" onclick="siguientePaso(6)">Siguiente ➡️</button>
            </div>
          </div>

          <!-- Pregunta 7 -->
          <div class="question-step" data-step="7">
            <div class="question-card">
              <h3>7. ¿Qué sientes tras estar 45 minutos sentado escuchando una plática?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q7" value="A"> A) Me aburro si no hay imágenes dinámicas.</label>
                <label class="option-label"><input type="radio" name="Q7" value="B"> B) Me aburro si la voz del orador es monótona.</label>
                <label class="option-label"><input type="radio" name="Q7" value="C"> C) Siento muchas ganas de pararme y estirar los brazos.</label>
                <label class="option-label"><input type="radio" name="Q7" value="D"> D) Me da sueño si no puedo tomar notas.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(7)">⬅️ Anterior</button>
              <button type="button" class="btn-action" onclick="siguientePaso(7)">Siguiente ➡️</button>
            </div>
          </div>

          <!-- Pregunta 8 -->
          <div class="question-step" data-step="8">
            <div class="question-card">
              <h3>8. ¿Qué es lo primero que haces al abrir un libro de aventuras nuevo?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q8" value="A"> A) Ver los dibujos de las páginas.</label>
                <label class="option-label"><input type="radio" name="Q8" value="B"> B) Imaginar cómo sonarían las voces si me lo leen.</label>
                <label class="option-label"><input type="radio" name="Q8" value="C"> C) Buscar cómo usar las hojas para jugar.</label>
                <label class="option-label"><input type="radio" name="Q8" value="D"> D) Empezar a leer el primer párrafo palabra por palabra.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(8)">⬅️ Anterior</button>
              <button type="button" class="btn-action" onclick="siguientePaso(8)">Siguiente ➡️</button>
            </div>
          </div>

          <!-- Pregunta 9 -->
          <div class="question-step" data-step="9">
            <div class="question-card">
              <h3>9. ¿Cómo prefieres tomar notas en una clase interesante?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q9" value="A"> A) Dibujando flechas y usando muchos colores.</label>
                <label class="option-label"><input type="radio" name="Q9" value="B"> B) Anotando solo lo que el profe dicta en voz alta.</label>
                <label class="option-label"><input type="radio" name="Q9" value="C"> C) Escribiendo rápido mientras muevo las piernas.</label>
                <label class="option-label"><input type="radio" name="Q9" value="D"> D) Redactando oraciones completas y listas ordenadas.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(9)">⬅️ Anterior</button>
              <button type="button" class="btn-action" onclick="siguientePaso(9)">Siguiente ➡️</button>
            </div>
          </div>

          <!-- Pregunta 10 -->
          <div class="question-step" data-step="10">
            <div class="question-card">
              <h3>10. Si te regalan un aparato electrónico nuevo, ¿cómo aprendes a usarlo?</h3>
              <div class="options-group">
                <label class="option-label"><input type="radio" name="Q10" value="A" required> A) Viendo los iconos y tocando la pantalla.</label>
                <label class="option-label"><input type="radio" name="Q10" value="B"> B) Usando asistente de voz o pidiendo que me digan.</label>
                <label class="option-label"><input type="radio" name="Q10" value="C"> C) Presionando botones para ver qué hace.</label>
                <label class="option-label"><input type="radio" name="Q10" value="D"> D) Leyendo el manual de instrucciones.</label>
              </div>
            </div>
            <div class="nav-buttons">
              <button type="button" class="btn-action btn-secondary" onclick="anteriorPaso(10)">⬅️ Anterior</button>
              <button type="submit" name="enviar_test" class="btn-action" style="background: linear-gradient(135deg, #48BB78 0%, #38A169 100%);">💾 Guardar mi Diagnóstico</button>
            </div>
          </div>

        </form>
    <?php endif; ?>

  </div>

  <!-- Modal de Pausa con Minijuego de Dinosaurio Realista y Puntitos interactivos -->
  <div id="modal-pausa">
    <div class="modal-content">
      <h2>🦖 Dino Runner: Zona Pinguich</h2>
      <p style="margin: 5px 0;">Tiempo restante: <span id="timer" style="color: #ECC94B; font-size: 1.3rem; font-weight: bold;">10:00</span></p>
      <p style="font-size: 0.9rem; color: #38BDF8; font-weight: 700;">⭐ Puntos: <span id="score" style="color: #48BB78; font-size: 1.2rem;">0</span></p>
      <p style="font-size: 0.8rem; color: #94A3B8; margin-top:0;">Presiona <strong>ESPACIO</strong> o haz clic en <strong>¡SALTAR!</strong> para esquivar cactus y atrapar los puntitos brillantes.</p>
      
      <canvas id="dinoCanvas" width="450" height="180"></canvas>
      
      <div style="margin-top: 20px; display: flex; gap: 12px;">
        <button class="btn-pause" style="flex:1;" onclick="saltarDino()">¡SALTAR! 🦖</button>
        <button class="btn-action" style="flex:1; padding: 12px;" onclick="cerrarPausa()">Continuar Test ▶️</button>
      </div>
    </div>
  </div>

  <script>
    let currentStep = 1;
    const totalSteps = 10; // Reducido a 10 preguntas

    document.getElementById('total-q-num').innerText = totalSteps;

    function actualizarProgreso() {
      document.getElementById('current-q-num').innerText = currentStep;
      let porcentaje = (currentStep / totalSteps) * 100;
      document.getElementById('progress-bar').style.width = porcentaje + '%';
    }

    function siguientePaso(step) {
      const currentCard = document.querySelector(`.question-step[data-step="${step}"]`);
      const radios = currentCard.querySelectorAll('input[type="radio"]');
      let seleccionado = false;
      radios.forEach(r => { if(r.checked) seleccionado = true; });

      if(!seleccionado) {
        alert("Por favor selecciona una opción antes de continuar.");
        return;
      }

      currentCard.classList.remove('active');
      currentStep = step + 1;
      let siguienteCard = document.querySelector(`.question-step[data-step="${currentStep}"]`);
      if(siguienteCard) {
        siguienteCard.classList.add('active');
        actualizarProgreso();
      }
    }

    function anteriorPaso(step) {
      document.querySelector(`.question-step[data-step="${step}"]`).classList.remove('active');
      currentStep = step - 1;
      document.querySelector(`.question-step[data-step="${currentStep}"]`).classList.add('active');
      actualizarProgreso();
    }

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

    // Lógica del Minijuego Dinosaurio Realista & Puntitos Interactivos
    let canvas, ctx;
    let dino = { x: 40, y: 120, w: 32, h: 40, vy: 0, gravity: 0.7, jumpForce: -11, grounded: true };
    let cactus = { x: 480, y: 125, w: 20, h: 35, speed: 5 };
    let puntito = { x: 500, y: 80, radius: 8, speed: 5, activo: true };
    let puntosJuego = 0;
    let juegoActivo = false;

    function iniciarDinoJuego() {
      canvas = document.getElementById('dinoCanvas');
      ctx = canvas.getContext('2d');
      juegoActivo = true;
      puntosJuego = 0;
      document.getElementById('score').innerText = puntosJuego;
      dino.y = 120;
      dino.vy = 0;
      cactus.x = 480;
      puntito.x = 520;
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

      // Limpiar pantalla
      ctx.fillStyle = '#0F172A';
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      // Suelo realista con textura
      ctx.fillStyle = '#38BDF8';
      ctx.fillRect(0, 165, canvas.width, 15);
      ctx.fillStyle = '#1E293B';
      for(let i=0; i<canvas.width; i+=25) {
        ctx.fillRect(i, 170, 10, 3);
      }

      // Física del Dinosaurio
      dino.vy += dino.gravity;
      dino.y += dino.vy;
      if (dino.y >= 120) {
        dino.y = 120;
        dino.vy = 0;
        dino.grounded = true;
      }

      // Dibujar Dinosaurio Realista / Estilizado
      ctx.fillStyle = '#48BB78';
      ctx.fillRect(dino.x + 6, dino.y + 12, 22, 22);
      ctx.fillRect(dino.x + 18, dino.y, 14, 16);
      ctx.fillStyle = '#FFFFFF';
      ctx.fillRect(dino.x + 26, dino.y + 3, 4, 4);
      ctx.fillStyle = '#38A169';
      ctx.fillRect(dino.x + 8, dino.y + 34, 5, 6);
      ctx.fillRect(dino.x + 20, dino.y + 34, 5, 6);

      // Movimiento y Dibujo del Cactus (Obstáculo)
      cactus.x -= cactus.speed;
      if (cactus.x < -30) {
        cactus.x = canvas.width + Math.random() * 80;
        cactus.speed = 5 + Math.random() * 1.5;
      }
      ctx.fillStyle = '#E53E3E';
      ctx.fillRect(cactus.x, cactus.y, cactus.w, cactus.h);
      ctx.fillStyle = '#FEB2B2';
      ctx.fillRect(cactus.x + 4, cactus.y + 6, 3, 12);

      // Movimiento y Dibujo del Puntito Interactivo (Estrella / Moneda)
      puntito.x -= puntito.speed;
      if (puntito.x < -20) {
        puntito.x = canvas.width + Math.random() * 120;
        puntito.y = 60 + Math.random() * 60;
        puntito.activo = true;
      }

      if (puntito.activo) {
        ctx.fillStyle = '#ECC94B';
        ctx.beginPath();
        ctx.arc(puntito.x, puntito.y, puntito.radius, 0, Math.PI * 2);
        ctx.fill();
        ctx.strokeStyle = '#D69E2E';
        ctx.lineWidth = 2;
        ctx.stroke();

        // Colisión exacta de los puntitos con el dinosaurio
        let distX = (dino.x + dino.w/2) - puntito.x;
        let distY = (dino.y + dino.h/2) - puntito.y;
        let distancia = Math.sqrt(distX * distX + distY * distY);

        if (distancia < puntito.radius + 18) {
          puntosJuego += 10; // ¡Suma 10 puntos reales al tocar el puntito!
          document.getElementById('score').innerText = puntosJuego;
          puntito.activo = false;
        }
      }

      // Colisión con el cactus
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