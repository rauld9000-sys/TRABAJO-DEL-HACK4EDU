<?php
session_start();
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servidor = "localhost";
    $usuario_db = "root";
    $password_db = "";
    $nombre_db = "penguich";

    $conexion = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);

    if (!$conexion->connect_error) {
        $usuario = $_POST['user'];
        $contrasena = $_POST['password']; // Contraseña en texto plano tal como lo pediste

        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contraseña = '$contrasena'";
        $resultado = $conexion->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $_SESSION['usuario'] = $usuario;
            header("Location: diagnostico.php");
            exit();
        } else {
            $mensaje_error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>penguich - Iniciar Sesión</title>
  <!-- Fuente Nunito para alta legibilidad -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <!-- Archivo de estilos externo -->
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

  <!-- ELEMENTOS FLOTANTES DECORATIVOS POR TODA LA PANTALLA -->
  <div class="floating-element fe-1">⭐</div>
  <div class="floating-element fe-2">🎨</div>
  <div class="floating-element fe-3">🚀</div>
  <div class="floating-element fe-4">💡</div>
  <div class="floating-element fe-5">🎒</div>
  <div class="floating-element fe-6">✏️</div>

  <!-- CONTENEDOR PRINCIPAL DEL LOGIN -->
  <div class="login-container">
    
    <div class="login-header">
      <div class="app-icon">🐧</div>
      <h1>penguich</h1>
      <p>Descubre tu verdadero potencial</p>
    </div>

    <!-- Mensaje de error si falla el acceso -->
    <?php if (!empty($mensaje_error)): ?>
      <div style="background-color: #F8D7DA; color: #842029; padding: 10px; border-radius: 10px; text-align: center; margin-bottom: 15px; font-weight: bold; border: 1px solid #F5C2C7; font-size: 0.9rem;">
        <?php echo $mensaje_error; ?>
      </div>
    <?php endif; ?>

    <!-- Formulario apuntando a sí mismo -->
    <form class="login-form" action="login.php" method="POST">
      
      <div class="form-field">
        <label for="user">Usuario o Tutor</label>
        <input type="text" id="user" name="user" placeholder="Escribe tu usuario..." required>
      </div>

      <div class="form-field">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn-submit">
        Iniciar Sesión
      </button>
    </form>

    <div class="login-footer">
      <p>¿No tienes una cuenta?</p>
      <a href="registro.php" class="btn-crear-cuenta">Crear Cuenta Nueva</a>
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