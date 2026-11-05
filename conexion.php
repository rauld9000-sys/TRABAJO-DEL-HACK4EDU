<?php
session_start();

/* =========================================
   CONFIGURACIÓN GENERAL
========================================= */

$mensaje_error = "";
$usuario_escrito = "";

$modo = $_POST["modo"] ?? $_GET["modo"] ?? "login";

if (!in_array($modo, ["login", "registro"], true)) {
    $modo = "login";
}

/* =========================================
   PROCESAR FORMULARIO
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $servidor = "localhost";
    $usuario_db = "root";
    $password_db = "";
    $nombre_db = "penguich";

    $usuario = trim($_POST["user"] ?? "");
    $contrasena = $_POST["password"] ?? "";

    $usuario_escrito = $usuario;

    if ($usuario === "" || $contrasena === "") {

        $mensaje_error = "Completa tu usuario y contraseña.";

    } else {

        try {

            mysqli_report(
                MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT
            );

            $conexion = new mysqli(
                $servidor,
                $usuario_db,
                $password_db,
                $nombre_db
            );

            $conexion->set_charset("utf8mb4");

            /* =====================================
               BUSCAR USUARIO
            ===================================== */

            $stmt = $conexion->prepare(
                "SELECT `usuario`, `contraseña`
                 FROM `usuarios`
                 WHERE `usuario` = ?
                 LIMIT 1"
            );

            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $cuenta = $resultado->fetch_assoc();
            $stmt->close();

            /* =====================================
               CREAR CUENTA NUEVA
            ===================================== */

            if ($modo === "registro") {

                if ($cuenta) {

                    $mensaje_error =
                        "Ese usuario ya existe. Elige otro nombre.";

                } else {

                    /* GUARDAR EN MYSQL SIN CIFRADO (TEXTO PLANO) */

                    $insertar = $conexion->prepare(
                        "INSERT INTO `usuarios`
                         (`usuario`, `contraseña`)
                         VALUES (?, ?)"
                    );

                    // Se guarda la contraseña tal cual la escribió el usuario
                    $insertar->bind_param(
                        "ss",
                        $usuario,
                        $contrasena
                    );

                    $insertar->execute();
                    $insertar->close();

                    /* =================================
                       INICIAR SESIÓN AUTOMÁTICAMENTE
                    ================================= */

                    $_SESSION = [];
                    session_regenerate_id(true);
                    $_SESSION["usuario"] = $usuario;
                    session_write_close();

                    header("Location: usuario.php");
                    exit();
                }

            } else {

                /* =====================================
                   INICIAR SESIÓN EXISTENTE
                ===================================== */

                $acceso = false;

                if ($cuenta) {
                    $contrasena_guardada = (string) $cuenta["contraseña"];

                    // Comparación directa en texto plano de forma segura
                    $acceso = hash_equals($contrasena_guardada, $contrasena);
                }

                /* =====================================
                   ACCESO CORRECTO
                ===================================== */

                if ($acceso) {

                    $_SESSION = [];
                    session_regenerate_id(true);
                    $_SESSION["usuario"] = $cuenta["usuario"];
                    session_write_close();

                    header("Location: usuario.php");
                    exit();

                } else {

                    $mensaje_error =
                        "Usuario o contraseña incorrectos.";
                }
            }

            $conexion->close();

        } catch (mysqli_sql_exception $e) {

            error_log(
                "Error Penguich: " . $e->getMessage()
            );

            if ($e->getCode() == 1062) {

                $mensaje_error =
                    "Ese usuario ya está registrado.";

            } else {

                $mensaje_error =
                    "No se pudo completar la operación. Revisa la conexión con MySQL.";
            }
        }
    }
}

$es_registro = ($modo === "registro");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Penguich |
        <?php echo $es_registro
            ? "Crear cuenta"
            : "Iniciar sesión"; ?>
    </title>

    <link
      href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap"
      rel="stylesheet"
    >

    <link rel="stylesheet" href="style.css">
</head>

<body class="penguich-auth">

    <!-- ACCESIBILIDAD -->

    <div class="accessibility-bar">

        <label for="color-filter">
            👁️ Filtro:
        </label>

        <select
          id="color-filter"
          onchange="changeColorFilter(this.value)"
        >
            <option value="normal">Estándar</option>
            <option value="protanopia">Protanopía</option>
            <option value="deuteranopia">Deuteranopía</option>
            <option value="tritanopia">Tritanopía</option>
        </select>

    </div>

    <!-- DECORACIONES -->

    <div class="floating-element fe-1">⭐</div>
    <div class="floating-element fe-2">🎨</div>
    <div class="floating-element fe-3">🚀</div>
    <div class="floating-element fe-4">💡</div>

    <!-- CONTENEDOR PRINCIPAL -->

    <main class="penguich-welcome penguich-form-card">

        <!-- MASCOTA -->

        <img
          src="img/Penguich sobre un arcoíris pastel.png"
          alt="Mascota de Penguich"
          class="penguich-mascot"
        >

        <!-- TÍTULO -->

        <h1>
            <?php echo $es_registro
                ? "¡Únete a Penguich!"
                : "¡Bienvenido!"; ?>
        </h1>

        <p>
            <?php echo $es_registro
                ? "¡Tu aventura comienza aquí!"
                : "¡Qué alegría verte de nuevo!"; ?>
        </p>

        <!-- MENSAJES DE ERROR -->

        <?php if ($mensaje_error !== ""): ?>

            <div class="penguich-error" role="alert">

                <?php
                echo htmlspecialchars(
                    $mensaje_error,
                    ENT_QUOTES,
                    "UTF-8"
                );
                ?>

            </div>

        <?php endif; ?>

        <!-- FORMULARIO -->

        <form
          action="conexion.php"
          method="POST"
          class="penguich-form"
        >

            <input
              type="hidden"
              name="modo"
              value="<?php echo $es_registro
                  ? 'registro'
                  : 'login'; ?>"
            >

            <!-- USUARIO -->

            <label for="user">
                👤 Usuario
            </label>

            <input
              type="text"
              id="user"
              name="user"
              placeholder="Ejemplo: toñito"
              value="<?php echo htmlspecialchars(
                  $usuario_escrito,
                  ENT_QUOTES,
                  'UTF-8'
              ); ?>"
              autocomplete="username"
              required
            >

            <!-- CONTRASEÑA -->

            <label for="password">
                🔒 Contraseña
            </label>

            <input
              type="password"
              id="password"
              name="password"
              placeholder="Ejemplo: numero1"
              autocomplete="<?php echo $es_registro
                  ? 'new-password'
                  : 'current-password'; ?>"
              required
            >

            <!-- BOTÓN PRINCIPAL -->

            <button
              type="submit"
              class="penguich-btn btn-login"
            >

                <?php echo $es_registro
                    ? "⭐ Crear cuenta y comenzar"
                    : "🚀 Iniciar sesión"; ?>

            </button>

        </form>

        <!-- NAVEGACIÓN -->

        <div class="penguich-bottom">

            <?php if ($es_registro): ?>

                <a href="conexion.php?modo=login">
                    ¿Ya tienes cuenta? Inicia sesión
                </a>

            <?php else: ?>

                <a href="conexion.php?modo=registro">
                    ¿No tienes cuenta? Regístrate
                </a>

            <?php endif; ?>

            <a href="index.html">
                ← Volver al inicio
            </a>

        </div>

    </main>

    <!-- JAVASCRIPT -->

    <script>

        function changeColorFilter(mode) {

            document.body.classList.remove(
                'protanopia',
                'deuteranopia',
                'tritanopia'
            );

            if (mode !== 'normal') {
                document.body.classList.add(mode);
            }

            localStorage.setItem(
                'penguichFiltro',
                mode
            );
        }

        const filtro =
            localStorage.getItem('penguichFiltro')
            || 'normal';

        document.getElementById(
            'color-filter'
        ).value = filtro;

        changeColorFilter(filtro);

    </script>

</body>
</html>