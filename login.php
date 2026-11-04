<?php
session_start();
$mensaje_error = "";
$tipo_alerta = "";

$modo = isset($_GET['modo']) ? $_GET['modo'] : 'login';
$es_registro = ($modo == 'registro');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servidor = "localhost";
    $usuario_db = "root";
    $password_db = "";
    $nombre_db = "penguich";

    $conexion = new mysqli($servidor, $usuario_db, $password_db, $nombre_db);

    if (!$conexion->connect_error) {
        $usuario = $_POST['user'];
        $contrasena = $_POST['password']; // Contraseña en texto plano
        $tipo_accion = $_POST['accion_tipo'];

        if ($tipo_accion == 'guardar_registro') {
            $verificar = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
            $resultado_v = $conexion->query($verificar);

            if ($resultado_v && $resultado_v->num_rows > 0) {
                $mensaje_error = "El usuario ya existe. Elige otro.";
                $tipo_alerta = "error";
            } else {
                $sql_insert = "INSERT INTO usuarios (usuario, contraseña) VALUES ('$usuario', '$contrasena')";
                if ($conexion->query($sql_insert) === TRUE) {
                    $mensaje_error = "¡Cuenta creada con éxito! Ya puedes iniciar sesión.";
                    $tipo_alerta = "exito";
                } else {
                    $mensaje_error = "Error al registrar el usuario.";
                    $tipo_alerta = "error";
                }
            }
        } else {
            $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contraseña = '$contrasena'";
            $resultado = $conexion->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
                $_SESSION['usuario'] = $usuario;
                header("Location: usuario.php"); // Redirige correctamente al panel de usuario
                exit();
            } else {
                $mensaje_error = "Usuario o contraseña incorrectos.";
                $tipo_alerta = "error";
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
  <title>penguich - <?php echo $es_registro ? "Crear Cuenta" : "Iniciar Sesión"; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
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

  <!-- CONTENEDOR PRINCIPAL DEL LOGIN -->
  <div class="login-container">
    
    <div class="login-header">
      <div class="app-icon">🐧</div>
      <h1>penguich</h1>
      <p><?php echo $es_registro ? "Crea una cuenta nueva" : "Descubre tu verdadero potencial"; ?></p>
    </div>

    <!-- Mensaje de error o éxito si ocurre algo -->
    <?php if (!empty($mensaje_error)): ?>
      <div style="background-color: <?php echo $tipo_alerta == 'exito' ? '#D1E7DD' : '#F8D7DA'; ?>; color: <?php echo $tipo_alerta == 'exito' ? '#0F5132' : '#842029'; ?>; padding: 10px; border-radius: 10px; text-align: center; margin-bottom: 15px; font-weight: bold; border: 1px solid <?php echo $tipo_alerta == 'exito' ? '#BADBCC' : '#F5C2C7'; ?>; font-size: 0.9rem;">
        <?php echo $mensaje_error; ?>
      </div>
    <?php endif; ?>

    <!-- Formulario apuntando a sí mismo con los mismos campos -->
    <form class="login-form" action="login.php?modo=<?php echo $modo; ?>" method="POST">
      
      <!-- Campo oculto para saber si guarda o valida -->
      <input type="hidden" name="accion_tipo" value="<?php echo $es_registro ? 'guardar_registro' : 'login'; ?>">

      <div class="form-field">
        <label for="user">Usuario o Tutor</label>
        <input type="text" id="user" name="user" placeholder="Escribe tu usuario..." required>
      </div>

      <div class="form-field">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn-submit">
        <?php echo $es_registro ? "Guardar Datos" : "Iniciar Sesión"; ?>
      </button>
    </form>

    <div class="login-footer">
      <p>¿Quieres regresar?</p>
      <a href="index.html" class="btn-crear-cuenta">Volver al Inicio</a>
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