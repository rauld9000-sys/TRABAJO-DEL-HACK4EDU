
<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$nombre = htmlspecialchars(
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

    <title>Penguich | Familias que impulsan</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="tut-page">

<header class="tut-top">
    <a href="usuario.php" class="tut-brand">
        🐧 Penguich
        <span>· Familias que impulsan</span>
    </a>

    <a href="usuario.php" class="tut-back">
        ← Volver al panel
    </a>
</header>

<main class="tut-wrap">

    <!-- BIENVENIDA -->

    <section class="tut-hero">
        <div>
            <span class="tut-kicker">
                💚 ESPACIO PARA MADRES, PADRES Y TUTORES
            </span>

            <h1>
                Su potencial crece cuando
                se siente acompañado.
            </h1>

            <p>
                Descubre estrategias para ayudarle a aprender,
                fortalecer sus talentos y afrontar las
                calificaciones difíciles con confianza.
            </p>

            <a href="#explorar" class="tut-btn">
                🌟 Empezar a explorar
            </a>
        </div>

        <div class="tut-hero-art" aria-hidden="true">
            <span>🐧</span>
            <span>💛</span>
            <span>📚</span>
        </div>
    </section>

    <!-- NAVEGACIÓN -->

    <nav class="tut-nav" aria-label="Secciones">
        <a href="#explorar">🔎 Observar</a>
        <a href="#herramientas">🧰 Herramientas</a>
        <a href="#emociones">💛 Emociones</a>
        <a href="#plan">📅 Plan semanal</a>
        <a href="#logros">🌟 Logros</a>
    </nav>

    <!-- SECCIÓN 1: OBSERVACIÓN -->

    <section id="explorar" class="tut-section">

        <div class="tut-heading">
            <span>01 · DESCUBRIR</span>

            <h2>¿Qué le ayuda a aprender?</h2>

            <p>
                Marca las situaciones que observas.
                No es un diagnóstico ni determina una forma fija
                de aprender: te ayudará a elegir estrategias
                para probar juntos.
            </p>
        </div>

        <div class="tut-grid tut-observe" id="tutObserve">

            <label>
                <input type="checkbox" value="lectura">
                <span>📖</span>
                <strong>Leer le cuesta trabajo</strong>
                <small>
                    Se cansa, pierde el renglón
                    o evita textos largos.
                </small>
            </label>

            <label>
                <input type="checkbox" value="atencion">
                <span>🎯</span>
                <strong>Le cuesta concentrarse</strong>
                <small>
                    Se distrae o le cuesta
                    empezar una tarea.
                </small>
            </label>

            <label>
                <input type="checkbox" value="matematicas">
                <span>🧩</span>
                <strong>Los números le frustran</strong>
                <small>
                    Se bloquea con operaciones
                    o problemas.
                </small>
            </label>

            <label>
                <input type="checkbox" value="memoria">
                <span>🧠</span>
                <strong>Olvida lo que estudió</strong>
                <small>
                    Lo comprende hoy, pero
                    no lo recuerda mañana.
                </small>
            </label>

            <label>
                <input type="checkbox" value="motivacion">
                <span>🌱</span>
                <strong>Dice «no puedo»</strong>
                <small>
                    Se compara, evita intentarlo
                    o teme equivocarse.
                </small>
            </label>

            <label>
                <input type="checkbox" value="organizacion">
                <span>🗂️</span>
                <strong>Se le acumulan tareas</strong>
                <small>
                    Olvida materiales,
                    fechas o pasos.
                </small>
            </label>

        </div>

        <div class="tut-action-row">
            <button id="tutRecommend" class="tut-btn" type="button">
                ✨ Ver recomendaciones
            </button>

            <button id="tutClear"
                    class="tut-btn tut-btn-soft"
                    type="button">
                Limpiar
            </button>
        </div>

        <div id="tutRecommendations"
             class="tut-recs"
             aria-live="polite">

            <p class="tut-placeholder">
                Selecciona una o varias situaciones
                y descubre ideas concretas para ayudarle.
            </p>
        </div>

    </section>

    <!-- SECCIÓN 2: HERRAMIENTAS -->

    <section id="herramientas" class="tut-section">

        <div class="tut-heading">
            <span>02 · ACOMPAÑAR</span>

            <h2>Herramientas que pueden usar en casa</h2>

            <p>
                Elige una estrategia, pruébenla durante varios días
                y ajusten lo que no funcione.
            </p>
        </div>

        <div class="tut-grid tut-tools">

            <article>
                <span>⏳</span>
                <h3>Estudio en bloques</h3>

                <p>
                    Comiencen con 10–15 minutos de actividad
                    y una pausa breve. Ajusta el tiempo según
                    su edad y necesidades, sin convertirlo
                    en una carrera.
                </p>

                <button class="tut-mini"
                        type="button"
                        data-tip="Acuerden una sola meta pequeña: resolver dos ejercicios, leer un párrafo o preparar la mochila. Después descansen y revisen cómo se sintió.">
                    ¿Cómo aplicarlo?
                </button>
            </article>

            <article>
                <span>🧸</span>
                <h3>Aprender con objetos</h3>

                <p>
                    Usen tapas, bloques, dibujos y ejemplos
                    cotidianos para representar cantidades,
                    fracciones y conceptos.
                </p>

                <button class="tut-mini"
                        type="button"
                        data-tip="Para 12 ÷ 3, reparte 12 tapas entre tres muñecos. Después dibujen lo que hicieron y, por último, escriban la operación.">
                    Ver ejemplo
                </button>
            </article>

            <article>
                <span>🗣️</span>
                <h3>Explicarlo con sus palabras</h3>

                <p>
                    Pídele que te enseñe lo aprendido.
                    Escucha sin interrumpir y ayúdale
                    con preguntas sencillas.
                </p>

                <button class="tut-mini"
                        type="button"
                        data-tip="Pregunta: ¿Cómo se lo explicarías a Penguich? Si se atasca, ofrécele una pista en lugar de dar toda la respuesta.">
                    Ver ejemplo
                </button>
            </article>

            <article>
                <span>🃏</span>
                <h3>Recordar sin releer todo</h3>

                <p>
                    Hagan tarjetas con preguntas,
                    cierren el cuaderno e intenten recordar.
                    Repitan en distintos días.
                </p>

                <button class="tut-mini"
                        type="button"
                        data-tip="Escribe cinco preguntas cortas. Repásenlas hoy, mañana y en unos días. Celebren lo que recordó y vuelvan a practicar lo pendiente.">
                    Ver ejemplo
                </button>
            </article>

            <article>
                <span>🎨</span>
                <h3>Varias formas de explicar</h3>

                <p>
                    Combinen una explicación oral con dibujos,
                    ejemplos, práctica y preguntas;
                    no se limiten a una sola modalidad.
                </p>

                <button class="tut-mini"
                        type="button"
                        data-tip="Para aprender el ciclo del agua: léanlo, dibújenlo, explíquenlo en voz alta y observen una demostración sencilla con ayuda adulta.">
                    Ver ejemplo
                </button>
            </article>

            <article>
                <span>📋</span>
                <h3>Rutinas visibles</h3>

                <p>
                    Una lista corta de pasos y un espacio
                    tranquilo pueden ayudar a comenzar
                    y terminar las tareas.
                </p>

                <button class="tut-mini"
                        type="button"
                        data-tip="Coloca una lista de tres pasos: 1. Preparo materiales. 2. Hago una actividad. 3. Reviso y guardo. Permite que el niño marque cada paso.">
                    Ver ejemplo
                </button>
            </article>

        </div>

        <div id="tutTip"
             class="tut-tip"
             hidden
             role="status">
        </div>

    </section>

    <!-- SECCIÓN 3: EMOCIONES -->

    <section id="emociones" class="tut-section tut-emotion">

        <div class="tut-heading">
            <span>03 · CUIDAR SU CONFIANZA</span>

            <h2>Una calificación no define a tu hijo</h2>

            <p>
                Una nota baja es información sobre lo que
                necesita practicar o sobre lo que pudo dificultar
                la evaluación. No es una medida de su valor,
                su inteligencia ni su futuro.
            </p>
        </div>

        <div class="tut-columns">

            <div class="tut-card">
                <h3>💬 Cambiemos las palabras</h3>

                <div class="tut-say">
                    <span>
                        En vez de «¿Por qué sacaste tan poco?»
                    </span>

                    <strong>
                        «¿Qué parte te resultó difícil?
                        Vamos a buscar otra forma de practicar.»
                    </strong>
                </div>

                <div class="tut-say">
                    <span>
                        En vez de «Tu hermano sí puede»
                    </span>

                    <strong>
                        «Cada persona aprende a su ritmo.
                        Veamos cuánto has avanzado tú.»
                    </strong>
                </div>

                <div class="tut-say">
                    <span>
                        En vez de «Eres malo en matemáticas»
                    </span>

                    <strong>
                        «Esta parte todavía cuesta.
                        Podemos probar con objetos y ejemplos.»
                    </strong>
                </div>

                <div class="tut-say">
                    <span>
                        En vez de «Si repruebas, me decepcionas»
                    </span>

                    <strong>
                        «Te quiero igual, sea cual sea tu nota.
                        Vamos a entender qué pasó.»
                    </strong>
                </div>
            </div>

            <div class="tut-card">
                <h3>🫶 Primeros cinco pasos</h3>

                <ol>
                    <li>
                        <strong>Conecta:</strong>
                        escucha cómo se siente antes
                        de hablar de soluciones.
                    </li>

                    <li>
                        <strong>Valida:</strong>
                        «Entiendo que estés triste o frustrado».
                    </li>

                    <li>
                        <strong>Explora:</strong>
                        revisen qué preguntas costaron y por qué.
                    </li>

                    <li>
                        <strong>Planea:</strong>
                        elijan una meta pequeña y realista.
                    </li>

                    <li>
                        <strong>Acompaña:</strong>
                        consulta al docente si la dificultad continúa.
                    </li>
                </ol>

                <div class="tut-reminder">
                    💛 Evita castigos humillantes,
                    comparaciones y retirar el afecto por una nota.
                </div>
            </div>

        </div>

        <div class="tut-support">
            <strong>¿Cuándo buscar más apoyo?</strong>

            <p>
                Si hay angustia persistente, rechazo intenso
                a la escuela, cambios importantes en el sueño
                o el ánimo, o dificultades que se mantienen
                pese al apoyo, conversa con su docente
                y considera orientación de un profesional
                educativo o de salud.

                Si expresa deseos de hacerse daño,
                busca ayuda inmediata.
            </p>
        </div>

    </section>

    <!-- SECCIÓN 4: PLAN SEMANAL -->

    <section id="plan" class="tut-section">

        <div class="tut-heading">
            <span>04 · PASAR A LA ACCIÓN</span>

            <h2>Un plan familiar de 7 días</h2>

            <p>
                Pequeñas acciones, sin presión.
                Marca las que realicen juntos.
                Se guardan en este navegador.
            </p>
        </div>

        <div class="tut-week" id="tutWeek"></div>

        <div class="tut-week-progress">
            <div id="tutWeekBar"></div>
        </div>

        <p id="tutWeekText" class="tut-progress-label">
            0 de 7 actividades completadas
        </p>

    </section>

    <!-- SECCIÓN 5: LOGROS -->

    <section id="logros" class="tut-section">

        <div class="tut-heading">
            <span>05 · CELEBRAR</span>

            <h2>El mural de nuestros logros</h2>

            <p>
                Anota un avance, por pequeño que sea:
                pedir ayuda, volver a intentarlo,
                terminar una lectura o explicar una idea.
            </p>
        </div>

        <form id="tutAchievementForm"
              class="tut-achievement-form">

            <label for="tutAchievement">
                🌟 Hoy celebramos que...
            </label>

            <div>
                <input
                    id="tutAchievement"
                    maxlength="180"
                    placeholder="Ej. Hoy intentó de nuevo un problema difícil"
                    required
                >

                <button class="tut-btn" type="submit">
                    Agregar logro
                </button>
            </div>
        </form>

        <div id="tutAchievements"
             class="tut-achievements"
             aria-live="polite">
        </div>

        <p class="tut-private">
            🔒 Estos registros se guardan únicamente
            en el navegador de este dispositivo;
            no se envían a la base de datos.
        </p>

    </section>

    <footer class="tut-footer">
        🐧 Penguich · Aprender también es sentirse
        querido, escuchado y capaz.
    </footer>

</main>

<script>
document.addEventListener("DOMContentLoaded", function () {

    "use strict";

    /* =========================================
       RECOMENDACIONES PERSONALIZADAS
    ========================================= */

    const recomendaciones = {

        lectura: {
            titulo: "📖 Lectura sin presión",
            pasos: [
                "Lean juntos en voz alta y alternen turnos si el niño quiere.",
                "Usa textos breves, letra clara y espaciado cómodo.",
                "Haz preguntas sobre la historia, no solo sobre la velocidad de lectura."
            ]
        },

        atencion: {
            titulo: "🎯 Atención paso a paso",
            pasos: [
                "Reduce distractores y acuerden una sola tarea a la vez.",
                "Divide la actividad en pasos cortos con pausas flexibles.",
                "Permite movimiento breve y reconoce cuando vuelve a intentarlo."
            ]
        },

        matematicas: {
            titulo: "🧩 Números con sentido",
            pasos: [
                "Representen las operaciones con tapas, bloques o dibujos.",
                "Conecta los problemas con compras, juegos o recetas.",
                "Pide que explique su procedimiento; corrige con calma, sin ridiculizar errores."
            ]
        },

        memoria: {
            titulo: "🧠 Recordar con práctica",
            pasos: [
                "Prueben recordar sin mirar el cuaderno, con pistas si hace falta.",
                "Repasen en días diferentes en lugar de estudiar todo de golpe.",
                "Mezclen preguntas, dibujos y ejemplos."
            ]
        },

        motivacion: {
            titulo: "🌱 Recuperar la confianza",
            pasos: [
                "Reconoce estrategias y avances concretos, no solo resultados.",
                "Evita comparaciones y frases que etiqueten su capacidad.",
                "Pregúntale qué ayuda necesita y acuerden una meta alcanzable."
            ]
        },

        organizacion: {
            titulo: "🗂️ Organizar sin regaños",
            pasos: [
                "Usen una lista visual de máximo tres pasos para empezar.",
                "Preparen materiales y mochila en un horario predecible.",
                "Revisen juntos el calendario y celebren completar cada paso."
            ]
        }
    };

    const observar = document.getElementById("tutObserve");
    const resultados = document.getElementById("tutRecommendations");

    document.getElementById("tutRecommend").addEventListener(
        "click",
        function () {

            const seleccionados = Array.from(
                observar.querySelectorAll("input:checked")
            ).map(input => input.value);

            resultados.replaceChildren();

            if (seleccionados.length === 0) {
                resultados.textContent =
                    "Selecciona al menos una situación para ver recomendaciones.";
                return;
            }

            seleccionados.forEach(function (clave) {

                const datos = recomendaciones[clave];

                if (!datos) return;

                const tarjeta = document.createElement("article");
                const titulo = document.createElement("h3");
                const lista = document.createElement("ol");

                titulo.textContent = datos.titulo;

                datos.pasos.forEach(function (paso) {
                    const elemento = document.createElement("li");
                    elemento.textContent = paso;
                    lista.appendChild(elemento);
                });

                tarjeta.appendChild(titulo);
                tarjeta.appendChild(lista);
                resultados.appendChild(tarjeta);
            });

            resultados.scrollIntoView({
                behavior: "smooth",
                block: "nearest"
            });
        }
    );

    document.getElementById("tutClear").addEventListener(
        "click",
        function () {

            observar.querySelectorAll("input").forEach(function (input) {
                input.checked = false;
            });

            resultados.textContent =
                "Selecciona una o varias situaciones y descubre ideas concretas para ayudarle.";
        }
    );

    /* =========================================
       EJEMPLOS DE TÉCNICAS DE ESTUDIO
    ========================================= */

    document.querySelectorAll("[data-tip]").forEach(function (boton) {

        boton.addEventListener("click", function () {

            const cuadro = document.getElementById("tutTip");

            cuadro.textContent = boton.dataset.tip;
            cuadro.hidden = false;

            cuadro.scrollIntoView({
                behavior: "smooth",
                block: "nearest"
            });
        });
    });

    /* =========================================
       PLAN FAMILIAR DE 7 DÍAS
    ========================================= */

    const actividades = [
        "Día 1 · Pregúntale qué le gusta aprender y escucha sin interrumpir.",
        "Día 2 · Elijan juntos un lugar cómodo para estudiar.",
        "Día 3 · Prueben una actividad de 10 minutos con una pausa.",
        "Día 4 · Aprendan algo mediante un juego o materiales cotidianos.",
        "Día 5 · Hablen de un error y qué les enseñó.",
        "Día 6 · Reconozcan un esfuerzo concreto de esta semana.",
        "Día 7 · Revisen qué funcionó y elijan una meta para la siguiente semana."
    ];

    const claveSemana = "penguich-tutores-semana-v1";
    const claveLogros = "penguich-tutores-logros-v1";

    function leerDatos(clave, valorInicial) {
        try {
            const guardado = localStorage.getItem(clave);

            return guardado === null
                ? valorInicial
                : JSON.parse(guardado);

        } catch (error) {
            return valorInicial;
        }
    }

    let completados = leerDatos(claveSemana, []);

    if (!Array.isArray(completados)) {
        completados = [];
    }

    const contenedorSemana = document.getElementById("tutWeek");

    actividades.forEach(function (actividad, indice) {

        const etiqueta = document.createElement("label");
        const checkbox = document.createElement("input");
        const texto = document.createElement("span");

        checkbox.type = "checkbox";
        checkbox.checked = completados.includes(indice);

        texto.textContent = actividad;

        checkbox.addEventListener("change", function () {

            const casillas = Array.from(
                contenedorSemana.querySelectorAll("input")
            );

            completados = casillas
                .map((casilla, posicion) => casilla.checked ? posicion : -1)
                .filter(posicion => posicion !== -1);

            try {
                localStorage.setItem(
                    claveSemana,
                    JSON.stringify(completados)
                );
            } catch (error) {}

            actualizarSemana();
        });

        etiqueta.appendChild(checkbox);
        etiqueta.appendChild(texto);
        contenedorSemana.appendChild(etiqueta);
    });

    function actualizarSemana() {

        const cantidad = contenedorSemana.querySelectorAll(
            "input:checked"
        ).length;

        const porcentaje = (cantidad / 7) * 100;

        document.getElementById("tutWeekBar").style.width =
            porcentaje + "%";

        document.getElementById("tutWeekText").textContent =
            cantidad + " de 7 actividades completadas";
    }

    actualizarSemana();

    /* =========================================
       MURAL DE LOGROS
    ========================================= */

    let logros = leerDatos(claveLogros, []);

    if (!Array.isArray(logros)) {
        logros = [];
    }

    const mural = document.getElementById("tutAchievements");

    function guardarLogros() {
        try {
            localStorage.setItem(
                claveLogros,
                JSON.stringify(logros)
            );
        } catch (error) {}
    }

    function mostrarLogros() {

        mural.replaceChildren();

        if (logros.length === 0) {

            const mensaje = document.createElement("p");

            mensaje.className = "tut-placeholder";
            mensaje.textContent =
                "Tu mural está listo para recibir el primer logro 💛";

            mural.appendChild(mensaje);
            return;
        }

        logros.forEach(function (logro, indice) {

            const tarjeta = document.createElement("div");
            const texto = document.createElement("p");
            const eliminar = document.createElement("button");

            texto.textContent = "⭐ " + logro;

            eliminar.type = "button";
            eliminar.textContent = "Eliminar";

            eliminar.setAttribute(
                "aria-label",
                "Eliminar logro"
            );

            eliminar.addEventListener("click", function () {

                logros.splice(indice, 1);

                guardarLogros();
                mostrarLogros();
            });

            tarjeta.appendChild(texto);
            tarjeta.appendChild(eliminar);

            mural.appendChild(tarjeta);
        });
    }

    document.getElementById("tutAchievementForm").addEventListener(
        "submit",
        function (evento) {

            evento.preventDefault();

            const entrada = document.getElementById("tutAchievement");
            const valor = entrada.value.trim();

            if (!valor) return;

            logros.unshift(valor.substring(0, 180));

            // Conservar los últimos 30 logros
            logros = logros.slice(0, 30);

            guardarLogros();
            mostrarLogros();

            entrada.value = "";
            entrada.focus();
        }
    );

    mostrarLogros();

});
</script>

</body>
</html>
