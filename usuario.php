
<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_actual = htmlspecialchars(
    (string) $_SESSION['usuario'],
    ENT_QUOTES,
    'UTF-8'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penguich | Mi aventura</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="penguich-dashboard">

    <!-- DECORACIONES -->
    <div class="penguich-decoration penguich-star" aria-hidden="true">⭐</div>
    <div class="penguich-decoration penguich-rocket" aria-hidden="true">🚀</div>
    <div class="penguich-decoration penguich-sparkle" aria-hidden="true">✨</div>

    <div class="penguich-wrapper">

        <!-- BARRA SUPERIOR -->
        <header class="penguich-header">

            <div class="penguich-brand">
                <span aria-hidden="true">🐧</span>
                <span>Penguich</span>
            </div>

            <div class="penguich-header-actions">

                <div class="penguich-filter">
                    <label for="color-filter">👁️ Filtro visual</label>

                    <select id="color-filter">
                        <option value="normal">Estándar</option>
                        <option value="protanopia">Protanopía</option>
                        <option value="deuteranopia">Deuteranopía</option>
                        <option value="tritanopia">Tritanopía</option>
                    </select>
                </div>

                <a href="logout.php" class="penguich-logout">
                    Cerrar sesión
                </a>

            </div>
        </header>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="penguich-layout">

            <!-- COLUMNA IZQUIERDA -->
            <aside class="penguich-mascot-panel" aria-label="Mascota de Penguich">

                <div class="penguich-mascot-container">

                    <!--
                    COLOCA AQUÍ TU IMAGEN.
                    Cambia la ruta src por la ubicación
                    real de tu archivo PNG.
                    -->

                    <img
                        src="img/Penguich sobre un arcoíris pastel.png"
                        alt="Mascota Penguich"
                        class="penguich-mascot-image"
                    >

                </div>

            </aside>

            <!-- COLUMNA DERECHA -->
            <main class="penguich-main">

                <div class="penguich-section-heading">
                    <span class="penguich-eyebrow">TU ESPACIO DE EXPLORACIÓN</span>
                    <h1>¡Elige tu próxima aventura!</h1>
                    <p>Descubre, aprende y desarrolla tus talentos a tu ritmo.</p>
                </div>

                <!-- CUADRÍCULA 2 X 2 -->
                <div class="penguich-cards">

                    <!-- TARJETA 1 -->
                    <article class="penguich-card penguich-card-blue">

                        <div class="penguich-card-icon" aria-hidden="true">
                            🎯
                        </div>

                        <div class="penguich-card-content">
                            <h2>Mi Estilo de Aprendizaje</h2>
<p>Descubre cómo prefieres aprender y qué métodos te ayudan más.</p>
                        </div>

                        <a href="procesar_estilo.php" class="penguich-card-button">
                            Descubrir mi estilo
                            <span aria-hidden="true">→</span>
                        </a>

                    </article>

                    <!-- TARJETA 2 -->
                    <article class="penguich-card penguich-card-yellow">

                        <div class="penguich-card-icon" aria-hidden="true">
                            ⭐
                        </div>

                        <div class="penguich-card-content">
                            <h2>Mis Inteligencias</h2>
<p>Explora diferentes formas de pensar, crear y resolver problemas.</p>
                        </div>

                        <a href="procesar_inteligencia.php" class="penguich-card-button">
                            Explorar mis inteligencias
                            <span aria-hidden="true">→</span>
                        </a>

                    </article>

                    <!-- TARJETA 3 -->
                    <article class="penguich-card penguich-card-green">

                        <div class="penguich-card-icon" aria-hidden="true">
                            🔍
                        </div>

                        <div class="penguich-card-content">
                            <h2>Mis Talentos y Habilidades</h2>
<p>Reconoce tus fortalezas y descubre todo lo que puedes desarrollar.</p>
                        </div>

                        <a href="procesar_talentos.php" class="penguich-card-button">
                            Descubrir mis talentos
                            <span aria-hidden="true">→</span>
                        </a>

                    </article>

                    <!-- TARJETA 4 -->
                    <article class="penguich-card penguich-card-purple">

                        <div class="penguich-card-icon" aria-hidden="true">
                            🛠️
                        </div>

                        <div class="penguich-card-content">
                            <h2>Mi Ruta de Aprendizaje</h2>
<p>Encuentra estrategias y actividades para aprender a tu manera.</p>
                        </div>

                        <a href="procesar_estrategias.php" class="penguich-card-button">
                            Comienza mi ruta
                            <span aria-hidden="true">→</span>
                        </a>

                    </article>

                </div>
            </main>

        </div>
    </div>

    
<script>
document.addEventListener("DOMContentLoaded", function () {

    const selector = document.getElementById("color-filter");

    const filtrosValidos = [
        "normal",
        "protanopia",
        "deuteranopia",
        "tritanopia"
    ];

    function changeColorFilter(tipo) {

        if (!filtrosValidos.includes(tipo)) {
            tipo = "normal";
        }

        document.body.classList.remove(
            "protanopia",
            "deuteranopia",
            "tritanopia"
        );

        if (tipo !== "normal") {
            document.body.classList.add(tipo);
        }

        selector.value = tipo;

        try {
            localStorage.setItem("penguich-filtro", tipo);
        } catch (error) {
            console.warn("No se pudo guardar el filtro.");
        }
    }

    let filtroGuardado = "normal";

    try {
        filtroGuardado =
            localStorage.getItem("penguich-filtro") ||
            localStorage.getItem("penguich-color-filter") ||
            "normal";
    } catch (error) {
        filtroGuardado = "normal";
    }

    changeColorFilter(filtroGuardado);

    selector.addEventListener("change", function () {
        changeColorFilter(this.value);
    });

});
</script>

</body>
</html>
