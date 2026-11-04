<?php
session_start();

// Si no hay sesión activa, creamos una por defecto para que NUNCA pida login al entrar directo
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
    
    $puntajes = [
        'Visual' => $puntaje_visual,
        'Auditivo' => $puntaje_auditivo,
        'Kinestésico' => $puntaje_kinestesico,
        'Lecto-Escritura' => $puntaje_lectoescritura
    ];

    arsort($puntajes);
    $estilo_predominante = key($puntajes);
    $max_puntaje = current($puntajes);
    $porcentaje_predominante = ($max_puntaje / 10) * 100;

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
        fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )";
    $conexion->query($sql_crear_tabla);

    // Guardar resultados
    $sql_insertar = "INSERT INTO test_estilo_aprendizaje 
        (usuario_id, puntaje_visual, puntaje_auditivo, puntaje_kinestesico, puntaje_lectoescritura, estilo_predominante, porcentaje_predominante) 
        VALUES 
        ($usuario_id, $puntaje_visual, $puntaje_auditivo, $puntaje_kinestesico, $puntaje_lectoescritura, '$estilo_predominante', $porcentaje_predominante)";
    
    if ($conexion->query($sql_insertar) === TRUE) {
        $guardado_exitoso = true;
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
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    body { font-family: 'Nunito', sans-serif; background: #F0F4F8; color: #2D3748; margin: 0; padding: 20px; }
    .quiz-container { max-width: 750px; margin: 40px auto; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); padding: 30px; border: 2px solid #E2E8F0; }
    .quiz-header { text-align: center; margin-bottom: 25px; }
    .app-icon { font-size: 2.5rem; background: #EBF8FF; width: 65px; height: 65px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin: 0 auto 15px auto; border: 2px solid #3182CE; }
    .question-card { background: #F7FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px; margin-bottom: 18px; }
    .question-card h3 { color: #2C5282; margin-top: 0; font-size: 1rem; }
    .options-group { display: flex; flex-direction: column; gap: 8px; margin-top: 10px; }
    .option-label { display: flex; align-items: flex-start; gap: 10px; background: white; padding: 10px 14px; border-radius: 8px; border: 1px solid #CBD5E0; cursor: pointer; transition: all 0.2s; font-size: 0.9rem; }
    .option-label:hover { background: #EBF8FF; border-color: #3182CE; }
    .btn-submit { background: #3182CE; color: white; border: none; padding: 14px; font-size: 1.1rem; font-weight: 700; border-radius: 12px; cursor: pointer; width: 100%; box-shadow: 0 4px 12px rgba(49,130,206,0.3); text-align: center; text-decoration: none; display: block; }
    .btn-submit:hover { background: #2B6CB0; }
  </style>
</head>
<body>

  <div class="quiz-container">
    
    <?php if ($guardado_exitoso): ?>
        <div class="quiz-header">
          <div class="app-icon">🐧</div>
          <h1>¡Resultado Guardado!</h1>
          <p>¡Excelente trabajo descubriendo tu superpoder de aprendizaje!</p>
        </div>

        <div style="background: #EBF8FF; color: #2B6CB0; padding: 20px; border-radius: 15px; text-align: center; margin-bottom: 20px; border: 2px solid #3182CE;">
          <h2 style="margin: 0 0 10px 0; font-size: 1.6rem; color: #2C5282;">Tu estilo es: <?php echo htmlspecialchars($estilo_predominante); ?></h2>
          <p style="font-size: 1.1rem; font-weight: bold; margin: 0;">Afinidad: <?php echo number_format($porcentaje_predominante, 1); ?>%</p>
        </div>

        <div style="background: #F7FAFC; padding: 15px; border-radius: 12px; border: 1px solid #E2E8F0; margin-bottom: 20px; font-size: 0.95rem;">
          <p style="margin: 0 0 5px 0;">📊 <strong>Tus puntos por estilo:</strong></p>
          <p style="margin: 0;">• Visual: <strong><?php echo $puntaje_visual; ?> / 10</strong></p>
          <p style="margin: 0;">• Auditivo: <strong><?php echo $puntaje_auditivo; ?> / 10</strong></p>
          <p style="margin: 0;">• Kinestésico: <strong><?php echo $puntaje_kinestesico; ?> / 10</strong></p>
          <p style="margin: 0;">• Lecto-Escritura: <strong><?php echo $puntaje_lectoescritura; ?> / 10</strong></p>
        </div>

        <div style="display: flex; gap: 15px;">
          <a href="usuario.php" class="btn-submit" style="flex: 1;">Volver al Panel</a>
          <a href="procesar_estilo.php?iniciar=1" style="background: #4A5568; color: white; flex: 1; padding: 14px; font-weight: 800; border-radius: 12px; text-align: center; text-decoration: none; display: block;">Repetir Test</a>
        </div>

    <?php elseif (!$iniciar_quiz): ?>
        <div class="quiz-header">
          <div class="app-icon">🐧</div>
          <h1>Mi Estilo de Aprendizaje</h1>
          <p>¡Descubre cómo aprende mejor tu cerebro jugando con Pinguich!</p>
        </div>

        <div style="background: #F7FAFC; border: 1px solid #CBD5E0; border-radius: 15px; padding: 20px; margin-bottom: 25px; text-align: center; color: #4A5568;">
          <p style="font-size: 1.05rem; margin-top: 0;">Responde unas divertidas preguntas sobre lo que haces cuando aprendes cosas nuevas.</p>
          <p style="margin-bottom: 0; font-size: 0.9rem; color: #718096;">⚡ ¡Sé tú mismo y contesta lo que prefieras!</p>
        </div>

        <div style="text-align: center;">
          <a href="procesar_estilo.php?iniciar=1" class="btn-submit" style="display: inline-block; max-width: 280px;">🚀 ¡Comenzar Quiz!</a>
        </div>

    <?php else: ?>
        <div class="quiz-header">
          <div class="app-icon">🐧</div>
          <h1>Test de Estilos de Aprendizaje</h1>
          <p>Elige la opción que más se parezca a ti en cada situación.</p>
        </div>

        <form action="procesar_estilo.php" method="POST">
          
          <!-- PREGUNTA RAÍZ -->
          <div class="question-card">
            <h3>¿Qué es lo primero que haces cuando juegas o aprendes algo totalmente nuevo?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="root" value="A" required> A) Miro imágenes, dibujos o los colores.</label>
              <label class="option-label"><input type="radio" name="root" value="B"> B) Pido que me expliquen hablando o escucho.</label>
              <label class="option-label"><input type="radio" name="root" value="C"> C) Lo toco, lo muevo o intento usarlo de inmediato.</label>
              <label class="option-label"><input type="radio" name="root" value="D"> D) Busco leer las instrucciones o notas escritas.</label>
            </div>
          </div>

          <!-- BLOQUE VISUAL (V1 - V10) -->
          <h2 style="color: #2C5282; font-size: 1.2rem; border-bottom: 2px solid #E2E8F0; padding-bottom: 5px; margin-top: 25px;">1. Exploración Visual</h2>

          <div class="question-card">
            <h3>V1. ¿Cómo recuerdas el camino a un lugar que visitaste una vez?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V1" value="A" required> A) Me imagino el mapa, esquinas y colores de edificios.</label>
              <label class="option-label"><input type="radio" name="V1" value="B"> B) Recuerdo ruidos o lo que me dijeron que escuchara.</label>
              <label class="option-label"><input type="radio" name="V1" value="C"> C) Recuerdo el cansancio o los giros que dio mi cuerpo.</label>
              <label class="option-label"><input type="radio" name="V1" value="D"> D) Recuerdo los nombres escritos en las calles.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V2. ¿Qué te ayuda más cuando el maestro explica algo difícil en el pizarrón?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V2" value="A" required> A) Ver esquemas, mapas mentales o dibujos a color.</label>
              <label class="option-label"><input type="radio" name="V2" value="B"> B) Escuchar su tono de voz y los ejemplos que cuenta.</label>
              <label class="option-label"><input type="radio" name="V2" value="C"> C) Manipular un objeto mientras escucho.</label>
              <label class="option-label"><input type="radio" name="V2" value="D"> D) Leer el texto en el libro en silencio.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V3. ¿Qué tipo de juegos prefieres en casa?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V3" value="A" required> A) Dibujar, pintar o armar rompecabezas.</label>
              <label class="option-label"><input type="radio" name="V3" value="B"> B) Escuchar música, cantar o tocar instrumentos.</label>
              <label class="option-label"><input type="radio" name="V3" value="C"> C) Hacer deportes o construir con bloques físicos.</label>
              <label class="option-label"><input type="radio" name="V3" value="D"> D) Leer cómics o escribir historias.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V4. ¿Qué truco usas para estudiar para un examen de memoria?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V4" value="A" required> A) Uso tarjetas de colores o dibujos divertidos.</label>
              <label class="option-label"><input type="radio" name="V4" value="B"> B) Me grabo la voz leyendo y lo escucho varias veces.</label>
              <label class="option-label"><input type="radio" name="V4" value="C"> C) Asocio cada cosa con un movimiento o paso de baile.</label>
              <label class="option-label"><input type="radio" name="V4" value="D"> D) Escribo listas de resumen una y otra vez.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V5. ¿Cómo organizas tus cosas o tu cuarto?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V5" value="A" required> A) Por colores que combinan y tamaños ordenados.</label>
              <label class="option-label"><input type="radio" name="V5" value="B"> B) Por lo que más uso al hablar o pedir ayuda.</label>
              <label class="option-label"><input type="radio" name="V5" value="C"> C) Por lo más cómodo para alcanzar rápido con las manos.</label>
              <label class="option-label"><input type="radio" name="V5" value="D"> D) Poniendo etiquetas escritas a cada caja o libreta.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V6. ¿Cómo imaginas lo que harás el próximo fin de semana?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V6" value="A" required> A) Como una película en mi mente con imágenes claras.</label>
              <label class="option-label"><input type="radio" name="V6" value="B"> B) Escuchando risas, sonidos y voces de lo que pasará.</label>
              <label class="option-label"><input type="radio" name="V6" value="C"> C) Sintiendo la emoción y energía de la actividad.</label>
              <label class="option-label"><input type="radio" name="V6" value="D"> D) Leyendo un plan o lista de pendientes.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V7. ¿Qué es lo que más recuerdas días después de ver una película?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V7" value="A" required> A) Los paisajes, trajes y efectos visuales.</label>
              <label class="option-label"><input type="radio" name="V7" value="B"> B) Las frases famosas y la música de fondo.</label>
              <label class="option-label"><input type="radio" name="V7" value="C"> C) Las escenas de acción y cómo se movían.</label>
              <label class="option-label"><input type="radio" name="V7" value="D"> D) Los subtítulos o textos que aparecían.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V8. Si le explicas un truco a un amigo, ¿cómo lo haces?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V8" value="A" required> A) Le hago un dibujo rápido en papel.</label>
              <label class="option-label"><input type="radio" name="V8" value="B"> B) Se lo platico paso a paso con voz divertida.</label>
              <label class="option-label"><input type="radio" name="V8" value="C"> C) Le tomo las manos para que él mismo mueva los objetos.</label>
              <label class="option-label"><input type="radio" name="V8" value="D"> D) Le escribo un pequeño instructivo.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V9. Cuando entras a un salón nuevo, ¿qué notas primero?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V9" value="A" required> A) La luz, los colores de paredes y cuadros.</label>
              <label class="option-label"><input type="radio" name="V9" value="B"> B) Si hay música o gente platicando.</label>
              <label class="option-label"><input type="radio" name="V9" value="C"> C) Si hay espacio cómodo para caminar.</label>
              <label class="option-label"><input type="radio" name="V9" value="D"> D) Si hay libros o letreros escritos a la vista.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>V10. ¿Cómo te gustaría presentar un proyecto escolar ganador?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="V10" value="A" required> A) Con carteles gigantes y dibujos hermosos.</label>
              <label class="option-label"><input type="radio" name="V10" value="B"> B) Con un podcast o presentación oral genial.</label>
              <label class="option-label"><input type="radio" name="V10" value="C"> C) Con una demostración donde todos puedan tocar el invento.</label>
              <label class="option-label"><input type="radio" name="V10" value="D"> D) Con un libro impreso con toda la investigación.</label>
            </div>
          </div>


          <!-- BLOQUE AUDITIVO (A1 - A10) -->
          <h2 style="color: #2C5282; font-size: 1.2rem; border-bottom: 2px solid #E2E8F0; padding-bottom: 5px; margin-top: 25px;">2. Exploración Auditiva</h2>

          <div class="question-card">
            <h3>A1. Cuando resuelves un problema difícil en silencio, ¿qué pasa en tu mente?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A1" value="A" required> A) Veo los números moverse como fichas.</label>
              <label class="option-label"><input type="radio" name="A1" value="B"> B) Me hablo en voz baja repitiendo los pasos.</label>
              <label class="option-label"><input type="radio" name="A1" value="C"> C) Muevo los dedos o un lápiz para pensar.</label>
              <label class="option-label"><input type="radio" name="A1" value="D"> D) Leo el problema en voz alta para entender.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A2. Si vas por tres cosas a la tienda sin anotarlas, ¿cómo las recuerdas?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A2" value="A" required> A) Me imagino el empaque de cada producto.</label>
              <label class="option-label"><input type="radio" name="A2" value="B"> B) Voy repitiendo los nombres en voz baja como cancioncita.</label>
              <label class="option-label"><input type="radio" name="A2" value="C"> C) Levanto tres dedos o los asocio a mi cuerpo.</label>
              <label class="option-label"><input type="radio" name="A2" value="D"> D) Deletreo mentalmente las letras iniciales.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A3. ¿Qué es lo primero que se te pega de una canción nueva?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A3" value="A" required> A) Los colores o cómo salían vestidos en el video.</label>
              <label class="option-label"><input type="radio" name="A3" value="B"> B) El ritmo, la música o la letra exacta.</label>
              <label class="option-label"><input type="radio" name="A3" value="C"> C) Las ganas de bailar y mover los pies.</label>
              <label class="option-label"><input type="radio" name="A3" value="D"> D) Lo que significa la letra y cómo se escribe.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A4. ¿Qué tipo de maestro capta más tu atención?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A4" value="A" required> A) El que llena el pizarrón de esquemas bonitos.</label>
              <label class="option-label"><input type="radio" name="A4" value="B"> B) El que cuenta historias increíbles platicando.</label>
              <label class="option-label"><input type="radio" name="A4" value="C"> C) El que nos pone a hacer juegos y experimentos.</label>
              <label class="option-label"><input type="radio" name="A4" value="D"> D) El que nos da lecturas interesantes y ensayos.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A5. ¿Cómo recuerdas un chiste que te contaron?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A5" value="A" required> A) Por las caras graciosas que hizo quien lo contó.</label>
              <label class="option-label"><input type="radio" name="A5" value="B"> B) Por su tono de voz exacto y las risas.</label>
              <label class="option-label"><input type="radio" name="A5" value="C"> C) Por la palmada en la espalda al terminar.</label>
              <label class="option-label"><input type="radio" name="A5" value="D"> D) Como si estuviera escrito en un libro.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A6. Si discutes con un amigo, ¿cómo prefieres arreglarlo?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A6" value="A" required> A) Viendo su cara para saber si está triste.</label>
              <label class="option-label"><input type="radio" name="A6" value="B"> B) Platicando largo y tendido escuchando su voz.</label>
              <label class="option-label"><input type="radio" name="A6" value="C"> C) Jugando un partido para calmar la tensión.</label>
              <label class="option-label"><input type="radio" name="A6" value="D"> D) Escribiéndole una carta o mensaje.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A7. ¿Qué te distrae más al hacer tarea en casa?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A7" value="A" required> A) Luces parpadeantes o desorden visual.</label>
              <label class="option-label"><input type="radio" name="A7" value="B"> B) La tele encendida o ruidos y voces en la calle.</label>
              <label class="option-label"><input type="radio" name="A7" value="C"> C) Estar sentado sin poder mover las piernas.</label>
              <label class="option-label"><input type="radio" name="A7" value="D"> D) Instrucciones de libros confusas.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A8. ¿Cómo prefieres hacer tus exámenes escolares?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A8" value="A" required> A) Con mapas y preguntas visuales.</label>
              <label class="option-label"><input type="radio" name="A8" value="B"> B) En una entrevista hablando con el profe.</label>
              <label class="option-label"><input type="radio" name="A8" value="C"> C) Con una prueba práctica armando algo.</label>
              <label class="option-label"><input type="radio" name="A8" value="D"> D) Escribiendo ensayos y respuestas largas.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A9. ¿Qué sonido te gusta de fondo al descansar en tu cuarto?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A9" value="A" required> A) Ninguno, prefiero ver todo ordenado.</label>
              <label class="option-label"><input type="radio" name="A9" value="B"> B) Música suave o sonidos de lluvia.</label>
              <label class="option-label"><input type="radio" name="A9" value="C"> C) Prefiero moverme antes que descansar quieto.</label>
              <label class="option-label"><input type="radio" name="A9" value="D"> D) El sonido de hojas de un libro al leer.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>A10. Si te regalan un aparato electrónico nuevo, ¿cómo aprendes a usarlo?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="A10" value="A" required> A) Viendo los iconos y tocando la pantalla.</label>
              <label class="option-label"><input type="radio" name="A10" value="B"> B) Usando asistente de voz o pidiendo que me digan.</label>
              <label class="option-label"><input type="radio" name="A10" value="C"> C) Presionando botones para ver qué hace.</label>
              <label class="option-label"><input type="radio" name="A10" value="D"> D) Leyendo el manual de instrucciones.</label>
            </div>
          </div>


          <!-- BLOQUE KINESTÉSICO (K1 - K10) -->
          <h2 style="color: #2C5282; font-size: 1.2rem; border-bottom: 2px solid #E2E8F0; padding-bottom: 5px; margin-top: 25px;">3. Exploración Kinestésica</h2>

          <div class="question-card">
            <h3>K1. Cuando armas bloques o LEGO, ¿qué guía tus manos?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K1" value="A" required> A) Ver la foto en la caja para copiar colores y formas.</label>
              <label class="option-label"><input type="radio" name="K1" value="B"> B) Hablar solo imaginando historias para los muñecos.</label>
              <label class="option-label"><input type="radio" name="K1" value="C"> C) El tacto; mis manos sienten cómo embonan por presión.</label>
              <label class="option-label"><input type="radio" name="K1" value="D"> D) Seguir paso a paso el manual escrito.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K2. ¿Qué sientes tras estar 45 minutos sentado escuchando una plática?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K2" value="A" required> A) Me aburro si no hay imágenes dinámicas.</label>
              <label class="option-label"><input type="radio" name="K2" value="B"> B) Me aburro si la voz del orador es monótona.</label>
              <label class="option-label"><input type="radio" name="K2" value="C"> C) Siento muchas ganas de pararme y estirar los brazos.</label>
              <label class="option-label"><input type="radio" name="K2" value="D"> D) Me da sueño si no puedo tomar notas.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K3. Si participas en un baile u obra de teatro, ¿cómo aprendes los pasos?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K3" value="A" required> A) Viéndome en un espejo con mis compañeros.</label>
              <label class="option-label"><input type="radio" name="K3" value="B"> B) Siguiendo el ritmo de la música con sonidos.</label>
              <label class="option-label"><input type="radio" name="K3" value="C"> C) Repitiendo el movimiento hasta que mi cuerpo se lo aprende.</label>
              <label class="option-label"><input type="radio" name="K3" value="D"> D) Leyendo el guion y memorizando dónde caminar.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K4. ¿Qué te sirvió más al aprender a andar en bicicleta o patineta?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K4" value="A" required> A) Ver videos de cómo ponían los pies otros.</label>
              <label class="option-label"><input type="radio" name="K4" value="B"> B) Escuchar las porras e indicaciones de gritos.</label>
              <label class="option-label"><input type="radio" name="K4" value="C"> C) Caerme, levantarme y sentir el equilibrio practicando.</label>
              <label class="option-label"><input type="radio" name="K4" value="D"> D) Leer un folleto de seguridad antes de subirme.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K5. ¿Qué pasa cuando vas a un museo que dice "Prohibido tocar"?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K5" value="A" required> A) Me conformo con ver detrás de las vitrinas.</label>
              <label class="option-label"><input type="radio" name="K5" value="B"> B) Escucho la audioguía con audífonos.</label>
              <label class="option-label"><input type="radio" name="K5" value="C"> C) Me frustra porque necesito tocar y mover botones.</label>
              <label class="option-label"><input type="radio" name="K5" value="D"> D) Leo con mucho detalle todos los textos de la pared.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K6. ¿Cómo saludas a tus amigos al verlos?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K6" value="A" required> A) Con una sonrisa y señas con la mano.</label>
              <label class="option-label"><input type="radio" name="K6" value="B"> B) Gritando un saludo alegre de inmediato.</label>
              <label class="option-label"><input type="radio" name="K6" value="C"> C) Dando un abrazo fuerte o chocando los cinco.</label>
              <label class="option-label"><input type="radio" name="K6" value="D"> D) Saludando con palabras formales o nota.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K7. Cuando te mandan a un mandado a otra habitación, ¿cómo vas?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K7" value="A" required> A) Mirando el camino y esquivando muebles.</label>
              <label class="option-label"><input type="radio" name="K7" value="B"> B) Tarareando una canción en el camino.</label>
              <label class="option-label"><input type="radio" name="K7" value="C"> C) Corriendo o saltando los escalones.</label>
              <label class="option-label"><input type="radio" name="K7" value="D"> D) Repitiendo en mi mente la lista escrita.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K8. Cuando usas una herramienta nueva, ¿cómo aprendes?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K8" value="A" required> A) Miro un diagrama de cómo se agarra.</label>
              <label class="option-label"><input type="radio" name="K8" value="B"> B) Pido que me expliquen hablando para qué sirve.</label>
              <label class="option-label"><input type="radio" name="K8" value="C"> C) La tomo y empiezo a usarla probando fuerzas.</label>
              <label class="option-label"><input type="radio" name="K8" value="D"> D) Leo las advertencias escritas en el empaque.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K9. ¿Qué te hace sentir más incómodo al estudiar?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K9" value="A" required> A) Luz mala o reflejos molestos en pantallas.</label>
              <label class="option-label"><input type="radio" name="K9" value="B"> B) Ruidos fuertes o eco alrededor.</label>
              <label class="option-label"><input type="radio" name="K9" value="C"> C) Estar obligado a no mover el cuerpo o ropa apretada.</label>
              <label class="option-label"><input type="radio" name="K9" value="D"> D) Papeles sucios o mal escritos.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>K10. ¿Qué prefieres construir para un proyecto de historia o geografía?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="K10" value="A" required> A) Una maqueta gigante llena de colores.</label>
              <label class="option-label"><input type="radio" name="K10" value="B"> B) Una radionovela grabada en audio.</label>
              <label class="option-label"><input type="radio" name="K10" value="C"> C) Una obra de teatro actuando con disfraces.</label>
              <label class="option-label"><input type="radio" name="K10" value="D"> D) Un informe escrito con mucha investigación.</label>
            </div>
          </div>


          <!-- BLOQUE LECTO-ESCRITURA (L1 - L10) -->
          <h2 style="color: #2C5282; font-size: 1.2rem; border-bottom: 2px solid #E2E8F0; padding-bottom: 5px; margin-top: 25px;">4. Exploración Lecto-Escritura</h2>

          <div class="question-card">
            <h3>L1. ¿Qué es lo primero que haces al abrir un libro de aventuras nuevo?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L1" value="A" required> A) Ver los dibujos de las páginas.</label>
              <label class="option-label"><input type="radio" name="L1" value="B"> B) Imaginar cómo sonarían las voces si me lo leen.</label>
              <label class="option-label"><input type="radio" name="L1" value="C"> C) Buscar cómo usar las hojas para jugar.</label>
              <label class="option-label"><input type="radio" name="L1" value="D"> D) Empezar a leer el primer párrafo palabra por palabra.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L2. ¿Cómo prefieres tomar notas en una clase interesante?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L2" value="A" required> A) Dibujando flechas y usando muchos colores.</label>
              <label class="option-label"><input type="radio" name="L2" value="B"> B) Anotando solo lo que el profe dicta en voz alta.</label>
              <label class="option-label"><input type="radio" name="L2" value="C"> C) Escribiendo rápido mientras muevo las piernas.</label>
              <label class="option-label"><input type="radio" name="L2" value="D"> D) Redactando oraciones completas y listas ordenadas.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L3. Si ayudas a un compañero a entender algo, ¿cómo se lo explicas?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L3" value="A" required> A) Le hago un esquema rápido en su libreta.</label>
              <label class="option-label"><input type="radio" name="L3" value="B"> B) Se lo platico hablándole claro al oído.</label>
              <label class="option-label"><input type="radio" name="L3" value="C"> C) Se lo muestro con las manos o hago que lo intente.</label>
              <label class="option-label"><input type="radio" name="L3" value="D"> D) Le escribo una nota con los pasos ordenados.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L4. ¿Qué haces durante una tarde aburrida en casa?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L4" value="A" required> A) Ver revistas de diseño o imágenes en internet.</label>
              <label class="option-label"><input type="radio" name="L4" value="B"> B) Escuchar podcasts o platicar con familia.</label>
              <label class="option-label"><input type="radio" name="L4" value="C"> C) Salir a correr o construir algo con las manos.</label>
              <label class="option-label"><input type="radio" name="L4" value="D"> D) Leer un libro nuevo o ponerme a escribir historias.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L5. ¿Qué te llama más la atención de un cartel en la calle?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L5" value="A" required> A) Letras llamativas y colores brillantes.</label>
              <label class="option-label"><input type="radio" name="L5" value="B"> B) Frases que riman o suenan chistosas al decirse.</label>
              <label class="option-label"><input type="radio" name="L5" value="C"> C) Anuncios que invitan a tocar un botón o participar.</label>
              <label class="option-label"><input type="radio" name="L5" value="D"> D) Textos largos que explican todo detallado.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L6. ¿Dónde buscas información para una tarea sobre un animal exótico?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L6" value="A" required> A) En documentales con videos cortos e imágenes.</label>
              <label class="option-label"><input type="radio" name="L6" value="B"> B) En audios educativos o entrevistas.</label>
              <label class="option-label"><input type="radio" name="L6" value="C"> C) En talleres interactivos donde vea el entorno.</label>
              <label class="option-label"><input type="radio" name="L6" value="D"> D) En enciclopedias, libros impresos y artículos largos.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L7. ¿Cómo prefieres conocer las reglas de un juego nuevo?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L7" value="A" required> A) Viendo un diagrama gráfico de la mesa.</label>
              <label class="option-label"><input type="radio" name="L7" value="B"> B) Que un amigo me platique las reglas hablando.</label>
              <label class="option-label"><input type="radio" name="L7" value="C"> C) Agarrando fichas y jugando sobre la marcha.</label>
              <label class="option-label"><input type="radio" name="L7" value="D"> D) Leyendo el folleto de instrucciones de la caja.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L8. ¿Qué te da más satisfacción al terminar un proyecto escolar?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L8" value="A" required> A) Ver que quedó hermoso y bien presentado.</label>
              <label class="option-label"><input type="radio" name="L8" value="B"> B) Haber expuesto genial recibiendo aplausos.</label>
              <label class="option-label"><input type="radio" name="L8" value="C"> C) Haber construido un objeto útil para usar.</label>
              <label class="option-label"><input type="radio" name="L8" value="D"> D) Ver mi informe escrito con ortografía perfecta.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L9. ¿Cómo anotas una cita importante para no olvidarla?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L9" value="A" required> A) Poniendo calcomanías de colores en el calendario.</label>
              <label class="option-label"><input type="radio" name="L9" value="B"> B) Poniendo una alarma con sonido en el teléfono.</label>
              <label class="option-label"><input type="radio" name="L9" value="C"> C) Haciendo un nudo o asociándolo a una rutina física.</label>
              <label class="option-label"><input type="radio" name="L9" value="D"> D) Escribiendo una nota Post-it con la fecha exacta.</label>
            </div>
          </div>

          <div class="question-card">
            <h3>L10. ¿Qué disfrutas más al leer una novela de misterio?</h3>
            <div class="options-group">
              <label class="option-label"><input type="radio" name="L10" value="A" required> A) Imaginar los escenarios oscuros y lupas.</label>
              <label class="option-label"><input type="radio" name="L10" value="B"> B) Escuchar mentalmente los diálogos tensos.</label>
              <label class="option-label"><input type="radio" name="L10" value="C"> C) Sentir la adrenalina de la persecución.</label>
              <label class="option-label"><input type="radio" name="L10" value="D"> D) Analizar cada palabra escrita para hallar pistas.</label>
            </div>
          </div>

          <button type="submit" name="enviar_test" class="btn-submit">💾 Guardar mi Diagnóstico</button>

        </form>
    <?php endif; ?>

  </div>
</body>
</html>