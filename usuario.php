<?php
session_start();

// Si el usuario no ha iniciado sesión, lo regresamos al login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_actual = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>penguich - Panel de Usuario</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <!-- Únicamente usa style.css -->
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- BARRA SUPERIOR DE ACCESIBILIDAD Y DALTONISMO -->
  <div class="accessibility-bar">
    <label for="color-filter">👁️ Filtro:</label>
    <select id="color-filter" onchange="changeColorFilter(this.value)">
      <option value="normal">Estándar</option>
      <option value="protanopia">Protanopía</option>
      <option value="deuteranopia">Deuteranopía</option>
      <option value="tritanopia">Tritanopía</option>
    </select>
  </div>

  <!-- ELEMENTOS FLOTANTES DECORATIVOS -->
  <div class="floating-element fe-1">⭐</div>
  <div class="floating-element fe-2">🎨</div>
  <div class="floating-element fe-3">🚀</div>
  <div class="floating-element fe-4">💡</div>
  <div class="floating-element fe-5">🎒</div>
  <div class="floating-element fe-6">✏️</div>

  <!-- CONTENEDOR PRINCIPAL (1/3 IZQUIERDA Y 2/3 DERECHA VERTICAL) -->
  <div class="dashboard-split-layout">
    
    <!-- COLUMNA IZQUIERDA (1/3): IMAGEN GRANDE + BIENVENIDA -->
    <aside class="columna-imagen-33">
      <div>
        <!-- CONTENEDOR DE LA IMAGEN GRANDE (Aquí luego pones tu <img src="...">) -->
        <div class="contenedor-imagen-grande">
          <!-- Ejemplo con icono, reemplázalo después por: <img src="tu-imagen.jpg" alt="Ilustración"> -->
          <span style="font-size: 5rem;">🐧✨</span>
        </div>

        <h2>¡Hola, <?php echo htmlspecialchars($usuario_actual); ?>! 👋</h2>
        <p>Bienvenido a tu panel de exploración en <strong>penguich</strong>. Descubre y potencia tus talentos.</p>
      </div>

      <div style="border-top: 2px solid #EDF2F7; padding-top: 15px;">
        <a href="logout.php" class="btn-logout" style="display: block; width: 100%; box-sizing: border-box;">Cerrar Sesión</a>
      </div>
    </aside>

    <!-- COLUMNA DERECHA (2/3): BLOQUES DE OPCIONES EN MODO VERTICAL -->
    <main class="columna-botones-67">
      
      <!-- Opción 1: Actividades -->
      <div class="dash-card-vertical">
        <div class="card-info-text">
          <h3>🎯 Actividades</h3>
          <p>Realiza divertidos retos diarios para medir tus destrezas y agilidad mental.</p>
        </div>
        <a href="actividades.php" class="btn-card" style="white-space: nowrap;">Ver Actividades</a>
      </div>

      <!-- Opción 2: Aptitudes Descubiertas -->
      <div class="dash-card-vertical">
        <div class="card-info-text">
          <h3>⭐ Aptitudes Descubiertas</h3>
          <p>Explora las medallas, talentos y habilidades que has ido desbloqueando.</p>
        </div>
        <a href="aptitudes.php" class="btn-card yellow" style="white-space: nowrap;">Ver Mis Talentos</a>
      </div>

      <!-- Opción 3: Seguir Buscando -->
      <div class="dash-card-vertical">
        <div class="card-info-text">
          <h3>🔍 Seguir Buscando</h3>
          <p>Inicia un nuevo test o busca nuevas áreas de interés y habilidades ocultas.</p>
        </div>
        <a href="usuario.php" class="btn-card" style="white-space: nowrap;">Buscar Aptitudes</a>
      </div>

      <!-- Opción 4: Más Herramientas -->
      <div class="dash-card-vertical">
        <div class="card-info-text">
          <h3>🛠️ Más Herramientas</h3>
          <p>Configuraciones de perfil, reportes para tutores y guías de aprendizaje.</p>
        </div>
        <a href="herramientas.php" class="btn-card yellow" style="white-space: nowrap;">Explorar Herramientas</a>
      </div>

    </main>

  </div>

  <script>
    function changeColorFilter(mode) {
      document.body.className = ''; 
      if (mode !== 'normal') {
        document.body.classList.add(mode);
      }
    }
  </script>
</body>
</html>