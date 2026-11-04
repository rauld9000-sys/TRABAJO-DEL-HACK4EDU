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
    <title>Penguich | Mi Aventura</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="penguich-dashboard">

    <!-- DECORACIONES FLOTANTES -->
    <div class="penguich-decoration penguich-star" aria-hidden="true">⭐</div>
    <div class="penguich-decoration penguich-rocket" aria-hidden="true">🚀</div>
    <div class="penguich-decoration penguich-sparkle" aria-hidden="true">✨</div>

    <div class="penguich-wrapper">

        <!-- BARRA SUPERIOR -->
        <header class="penguich-header">
            <div class="penguich-brand">
                <span class="brand-icon" aria-hidden="true">🐧</span>
                <span class="brand-name">Penguich</span>
            </div>

            <div class="penguich-header-actions">
                <div class="penguich-filter">
                    <label for="color-filter">👁️ Filtro visual:</label>
                    <select id="color-filter">
                        <option value="normal">Estándar</option>
                        <option value="protanopia">Protanopía</option>
                        <option value="deuteranopia">Deuteranopía</option>
                        <option value="tritanopia">Tritanopía</option>
                    </select>
                </div>

                <a href="reporte_tutores.php" class="penguich-btn penguich-btn-tutor">
                    📄 Reporte para Tutores
                </a>

                <a href="logout.php" class="penguich-btn penguich-btn-logout">
                    Cerrar sesión
                </a>
            </div>
        </header>

        <!-- LAYOUT GENERAL DE 3 COLUMNAS -->
        <div class="penguich-layout-extended">

            <!-- =========================================
               COLUMNA IZQUIERDA: PROGRESO Y MASCOTA
               ========================================= -->
            <aside class="penguich-left-panel" aria-label="Panel de Progreso y Mascota">
                
                <div class="penguich-widget">
                    <div class="widget-header">
                        <strong>Galería de Habilidades</strong>
                        <p>Monitoreo en tiempo real y metas.</p>
                    </div>

                    <div class="progress-mini-card">
                        <div class="progress-card-title">
                            <span>🤖</span>
                            <strong>El Inventor Junior</strong>
                        </div>
                        <div class="stars-rating">⭐⭐⭐⭐⭐</div>
                        <div class="progress-stats">
                            <span>Progreso</span>
                            <strong>100%</strong>
                        </div>
                        <div class="mini-progress-track">
                            <div class="mini-progress-fill" style="width: 100%;"></div>
                        </div>
                        <span class="progress-fraction">120/110</span>
                    </div>

                    <div class="progress-mini-card">
                        <div class="progress-card-title">
                            <span>🎨</span>
                            <strong>El Artista Creativo</strong>
                        </div>
                        <div class="stars-rating">⭐⭐⭐⭐</div>
                        <div class="progress-stats">
                            <span>Progreso</span>
                            <strong>100%</strong>
                        </div>
                        <div class="mini-progress-track">
                            <div class="mini-progress-fill" style="width: 100%;"></div>
                        </div>
                        <span class="progress-fraction">50/110</span>
                    </div>

                    <div class="progress-mini-card">
                        <div class="progress-card-title">
                            <span>💡</span>
                            <strong>Lógica Global</strong>
                        </div>
                        <div class="stars-rating">⭐⭐⭐</div>
                        <div class="progress-stats">
                            <span>Progreso</span>
                            <strong>100%</strong>
                        </div>
                        <div class="mini-progress-track">
                            <div class="mini-progress-fill" style="width: 100%;"></div>
                        </div>
                        <span class="progress-fraction">100/60</span>
                    </div>
                </div>

                <!-- Mascota Penguich -->
                <div class="penguich-mascot-box">
                    <img src="img/Penguich sobre un arcoíris pastel.png" alt="Mascota Penguich" class="penguich-mascot-image">
                </div>

            </aside>

            <!-- =========================================
               COLUMNA CENTRAL: MÓDULOS Y UTILIDADES
               ========================================= -->
            <main class="penguich-main">

                <div class="penguich-section-heading">
                    <span class="penguich-eyebrow">TU ESPACIO DE EXPLORACIÓN</span>
                    <h1>¡Hola, <?php echo $usuario_actual; ?>! Elige tu aventura</h1>
                    <p>Descubre, aprende y desarrolla tus talentos a tu propio ritmo.</p>
                </div>

                <!-- UTILIDADES PARA EL ÉXITO -->
                <div class="penguich-utility-card">
                    <div class="utility-header">
                        <h3>Utilidades para el Éxito</h3>
                        <span class="badge-nodes">+ Nodes</span>
                    </div>
                    <p class="utility-subtitle">Calendario Planificador</p>
                    <div class="utility-schema-box">
                        🗺️ Esquema interactivo de Nodos y Conexiones del Calendario
                    </div>
                </div>

                <!-- CUADRÍCULA PRINCIPAL DE TARJETAS -->
                <div class="penguich-cards-grid">

                    <!-- TARJETA 1 -->
                    <article class="penguich-card penguich-card-blue">
                        <div class="penguich-card-icon" aria-hidden="true">🎯</div>
                        <div class="penguich-card-content">
                            <h2>Mi Estilo de Aprendizaje</h2>
                            <p>Descubre cómo prefieres aprender y qué métodos te impulsan más.</p>
                            <div class="card-tags">
                                <span class="tag tag-visual">Visual</span>
                                <span class="tag tag-kinesthetic">Kinesthetic</span>
                                <span class="tag tag-auditory">Auditory</span>
                            </div>
                        </div>
                        <a href="procesar_estilos.php" class="penguich-card-button">
                            Descubrir mi estilo <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <!-- TARJETA 2 -->
                    <article class="penguich-card penguich-card-yellow">
                        <div class="penguich-card-icon" aria-hidden="true">⭐</div>
                        <div class="penguich-card-content">
                            <h2>Mis Inteligencias</h2>
                            <p>Explora diferentes formas de pensar, crear y resolver problemas.</p>
                            <div class="mini-chart">
                                <div class="chart-bar" style="height: 60%; background: #2A9D8F;"></div>
                                <div class="chart-bar" style="height: 90%; background: #F4C95D;"></div>
                                <div class="chart-bar" style="height: 45%; background: #E53E3E;"></div>
                                <div class="chart-bar" style="height: 75%; background: #48BB78;"></div>
                                <div class="chart-bar" style="height: 55%; background: #38BDF8;"></div>
                            </div>
                        </div>
                        <a href="procesar_inteligencia.php" class="penguich-card-button">
                            Explorar mis inteligencias <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <!-- TARJETA 3 (Ancha) -->
                    <article class="penguich-card penguich-card-green penguich-card-wide">
                        <div class="card-flex-wrapper">
                            <div>
                                <div class="penguich-card-icon" aria-hidden="true">🔍</div>
                                <div class="penguich-card-content">
                                    <h2>Mis Talentos y Habilidades</h2>
                                    <p>Reconoce tus fortalezas y descubre todo lo que eres capaz de desarrollar.</p>
                                </div>
                            </div>
                            <div class="skills-pill-list">
                                <div class="skill-pill">🤖 Robótica aplicada</div>
                                <div class="skill-pill">🎭 Teatro Creativo</div>
                                <div class="skill-pill">🔬 Experimentos de Campo</div>
                            </div>
                        </div>
                        <a href="procesar_talentos.php" class="penguich-card-button">
                            Descubrir mis talentos <span aria-hidden="true">→</span>
                        </a>
                    </article>

                    <!-- TARJETA 4 (Ancha) -->
                    <article class="penguich-card penguich-card-purple penguich-card-wide">
                        <div class="card-flex-wrapper">
                            <div>
                                <div class="penguich-card-icon" aria-hidden="true">🛠️</div>
                                <div class="penguich-card-content">
                                    <h2>Mi Ruta de Aprendizaje</h2>
                                    <p>Encuentra estrategias personalizadas y actividades para aprender a tu manera.</p>
                                </div>
                            </div>
                            <div class="adventure-map-badge">
                                <span>Mapa de Aventuras 🗺️</span>
                            </div>
                        </div>
                        <a href="procesar_estrategias.php" class="penguich-card-button">
                            Comienza mi ruta <span aria-hidden="true">→</span>
                        </a>
                    </article>

                </div>

                <!-- RED DE OPORTUNIDADES GLOBALES -->
                <div class="penguich-opportunities-card">
                    <h3>Red de Oportunidades Globales</h3>
                    <p class="opportunities-subtitle">Conexiones y recursos clave para potenciar tu desarrollo.</p>
                    
                    <div class="opportunities-grid">
                        <div class="opportunity-item">
                            <strong>Internships Virtual</strong>
                            <span>Prácticas virtuales para conectar con proyectos del mundo real.</span>
                        </div>
                        <div class="opportunity-item">
                            <strong>Certificaciones</strong>
                            <span>Validación oficial de tus conocimientos en áreas tecnológicas y creativas.</span>
                        </div>
                        <div class="opportunity-item">
                            <strong>Talleres Relevantes</strong>
                            <span>Capacitaciones especializadas impartidas por expertos.</span>
                        </div>
                    </div>
                </div>

            </main>

            <!-- =========================================
               COLUMNA DERECHA: PARQUE DE JUEGOS
               ========================================= -->
            <aside class="penguich-right-panel" aria-label="Parque de Juegos">
                <div class="penguich-widget game-park-widget">
                    <h3>Parque de Juegos</h3>

                    <div class="game-item game-color">
                        <span class="game-emoji">🎨</span>
                        <strong>Mi Cuaderno de Colores</strong>
                    </div>

                    <div class="game-item game-stories">
                        <span class="game-emoji">📖</span>
                        <strong>Historias de Penguich</strong>
                    </div>

                    <div class="game-item game-mini">
                        <span class="game-emoji">🎮</span>
                        <strong>Minijuegos Divertidos</strong>
                    </div>

                    <div class="game-item game-friends">
                        <span class="game-emoji">👥</span>
                        <strong>Amigos de Aventuras</strong>
                    </div>
                </div>
            </aside>

        </div>
    </div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const selector = document.getElementById("color-filter");
    const filtrosValidos = ["normal", "protanopia", "deuteranopia", "tritanopia"];

    function changeColorFilter(tipo) {
        if (!filtrosValidos.includes(tipo)) tipo = "normal";
        document.body.classList.remove("protanopia", "deuteranopia", "tritanopia");
        if (tipo !== "normal") document.body.classList.add(tipo);
        selector.value = tipo;
        try {
            localStorage.setItem("penguich-filtro", tipo);
        } catch (error) {
            console.warn("No se pudo guardar el filtro.");
        }
    }

    let filtroGuardado = "normal";
    try {
        filtroGuardado = localStorage.getItem("penguich-filtro") || "normal";
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