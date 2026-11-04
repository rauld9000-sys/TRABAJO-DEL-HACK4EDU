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

  <!-- CONTENEDOR PRINCIPAL DEL PERFIL -->
  <div class="dashboard-container">
    
    <!-- Tarjeta de Bienvenida -->
    <div class="welcome-card">
      <div class="welcome-info">
        <h1>¡Hola, <?php echo htmlspecialchars($usuario_actual); ?>! 👋</h1>
        <p>Bienvenido a tu panel de exploración en penguich</p>
      </div>
      <div class="user-avatar">🐧</div>
    </div>

    <!-- Secciones del Panel -->
    <div class="dashboard-grid">
      
      <!-- Sección: Actividades -->
      <div class="dash-card">
        <h3>🎯 Actividades</h3>
        <p>Realiza divertidos retos diarios para medir tus destrezas y agilidad mental.</p>
        <a href="actividades.php" class="btn-card">Ver Actividades</a>
      </div>

      <!-- Sección: Aptitudes Descubiertas -->
      <div class="dash-card">
        <h3>⭐ Aptitudes Descubiertas</h3>
        <p>Explora las medallas, talentos y habilidades que has ido desbloqueando.</p>
        <a href="aptitudes.php" class="btn-card yellow">Ver Mis Talentos</a>
      </div>

      <!-- Sección: Seguir Buscando -->
      <div class="dash-card">
        <h3>🔍 Seguir Buscando</h3>
        <p>Inicia un nuevo test o busca nuevas áreas de interés y habilidades ocultas.</p>
        <a href="usuario.php" class="btn-card">Buscar Aptitudes</a>
      </div>

      <!-- Sección: Más Herramientas -->
      <div class="dash-card">
        <h3>🛠️ Más Herramientas</h3>
        <p>Configuraciones de perfil, reportes para tutores y guías de aprendizaje.</p>
        <a href="herramientas.php" class="btn-card yellow">Explorar Herramientas</a>
      </div>

    </div>

    <!-- Botón de Cerrar Sesión -->
    <div class="logout-container">
      <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>

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