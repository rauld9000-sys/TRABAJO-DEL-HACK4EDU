
<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario = htmlspecialchars(
    (string) $_SESSION['usuario'],
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);

$usuario_id = hash('sha256', (string) $_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Penguich | Mi aventura</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body class="penguich-dashboard pg-v4">

<a class="pg-skip" href="#contenido">Saltar al contenido</a>

<div class="pg-container">

<header class="pg-header">
    <a href="usuario.php" class="pg-logo">
        <span>🐧</span>
        <div>
            <strong>Penguich</strong>
            <small>Aprendo a mi ritmo</small>
        </div>
    </a>

    <div class="pg-header-actions">
        <button id="voiceBtn" type="button" class="pg-btn pg-light">
            🔊 Escuchar
        </button>

        <button id="fontBtn" type="button" class="pg-btn pg-light">
            Aa Texto grande
        </button>

        <label class="pg-color-label">
            🎨 Colores
            <select id="colorFilter">
                <option value="normal">Original</option>
                <option value="protanopia">Protanopía</option>
                <option value="deuteranopia">Deuteranopía</option>
                <option value="tritanopia">Tritanopía</option>
                <option value="contraste">Alto contraste</option>
            </select>
        </label>

        <a href="tutores.php" class="pg-btn pg-light">
            📄 Tutores
        </a>

        <a href="logout.php" class="pg-btn pg-exit">
            Salir
        </a>
    </div>
</header>

<main id="contenido">

<!-- BIENVENIDA -->
<section class="pg-hero">
    <div class="pg-hero-text">
        <span class="pg-kicker">✨ TU AVENTURA COMIENZA AQUÍ</span>

        <h1>¡Hola, <?php echo $usuario; ?>! 👋</h1>

        <p id="welcomeText">
            ¡Qué alegría verte! Aquí puedes descubrir,
            imaginar, aprender y desarrollar tus habilidades
            a tu propio ritmo. Cada intento cuenta.
        </p>

        <div class="pg-hero-buttons">
            <a href="#aventuras" class="pg-btn pg-primary">
                🚀 Explorar aventuras
            </a>
            <a href="#chat" class="pg-btn pg-white">
                💬 Hablar con Penguich
            </a>
        </div>
    </div>

    <div class="pg-mascot">
        <div class="pg-speech">¡Me encanta aprender contigo! 💛</div>
        <img
            src="img/Penguich sobre un arcoíris pastel.png"
            alt="Mascota Penguich"
            onerror="this.hidden=true;this.nextElementSibling.hidden=false"
        >
        <span hidden>🐧</span>
    </div>
</section>

<!-- FRASE POSITIVA -->
<section class="pg-positive-banner">
    <span class="pg-positive-icon">💛</span>
    <div>
        <strong>Un mensaje especial para ti</strong>
        <p id="positiveMessage">
            No necesitas hacerlo perfecto para aprender algo increíble.
        </p>
    </div>
    <button type="button" id="newMessage" class="pg-btn pg-white">
        ✨ Otro mensaje
    </button>
</section>

<div class="pg-layout">

<!-- COLUMNA PRINCIPAL -->
<div class="pg-main">

<section id="aventuras">
    <div class="pg-heading">
        <span class="pg-kicker">DESCUBRE TU POTENCIAL</span>
        <h2>🌈 Mis aventuras de aprendizaje</h2>
        <p>Elige una aventura. ¡Todas las formas de aprender son valiosas!</p>
    </div>

    <div class="pg-adventure-grid">

        <article class="pg-adventure pg-blue">
            <span class="pg-adventure-icon">🎯</span>
            <h3>¿Cómo me gusta aprender?</h3>
            <p>Descubre distintas maneras de explorar y comprender cosas nuevas.</p>
            <a href="procesar_estilos.php">Comenzar aventura →</a>
        </article>

        <article class="pg-adventure pg-yellow">
            <span class="pg-adventure-icon">🧠</span>
            <h3>Mis formas de pensar</h3>
            <p>Explora tu imaginación, razonamiento y formas de resolver problemas.</p>
            <a href="procesar_inteligencia.php">Vamos a explorar →</a>
        </article>

        <article class="pg-adventure pg-green">
            <span class="pg-adventure-icon">🌟</span>
            <h3>Mis talentos y habilidades</h3>
            <p>Conoce tus intereses y descubre lo que puedes seguir desarrollando.</p>
            <a href="procesar_talentos.php">Descubrir talentos →</a>
        </article>

        <article class="pg-adventure pg-purple">
            <span class="pg-adventure-icon">🗺️</span>
            <h3>Mi ruta de aprendizaje</h3>
            <p>Encuentra estrategias y actividades para aprender paso a paso.</p>
            <a href="procesar_estrategias.php">Crear mi ruta →</a>
        </article>

    </div>
</section>

<!-- CHAT -->
<section id="chat" class="pg-panel pg-chat-panel">
    <div class="pg-panel-heading">
        <div>
            <span class="pg-kicker">TU COMPAÑERO DE AVENTURAS</span>
            <h2>💬 Platiquemos con Penguich</h2>
        </div>
        <span class="pg-tag">Versión inicial</span>
    </div>

    <p>
        Cuéntame qué te gustaría aprender. Por ahora puedo
        sugerirte actividades y acompañarte con mensajes preparados.
    </p>

    <div class="pg-chat-messages" id="chatMessages" aria-live="polite">
        <div class="pg-chat-message pg-bot">
            <span class="pg-chat-avatar">🐧</span>
            <div class="pg-chat-bubble">
                ¡Hola! Soy Penguich. 💚
                ¿Quieres aprender algo, jugar o descubrir una nueva actividad?
            </div>
        </div>
    </div>

    <div class="pg-chat-suggestions">

<div class="pg-chat-suggestions pg-talent-suggestions">

    <button type="button" data-chat="deportes">
        ⚽ Deportes y movimiento
    </button>

    <button type="button" data-chat="arte">
        🎨 Arte e imaginación
    </button>

    <button type="button" data-chat="estrategia">
        ♟️ Estrategia y juegos
    </button>

    <button type="button" data-chat="musica">
        🎵 Música y ritmo
    </button>

    <button type="button" data-chat="exploracion">
        🔍 Exploración y ciencia
    </button>

    <button type="button" data-chat="liderazgo">
        🤝 Liderazgo y equipo
    </button>

    <button type="button" data-chat="memoria">
        🧠 Memoria y aprendizaje
    </button>

    <button type="button" data-chat="expresion">
        🎭 Expresión y confianza
    </button>

</div>

    </div>

    <form id="chatForm" class="pg-chat-form">
        <label class="pg-sr-only" for="chatInput">Escribe tu mensaje</label>
        <input
            id="chatInput"
            maxlength="250"
            autocomplete="off"
            placeholder="¿Qué te gustaría aprender hoy?"
            required
        >
        <button class="pg-btn pg-primary" type="submit">
            Enviar ➤
        </button>
    </form>

    <small class="pg-disclaimer">
        Penguich todavía no utiliza inteligencia artificial.
        No escribas información privada. Si algo te preocupa,
        puedes hablar con un adulto de confianza.
    </small>
</section>

<!-- PLANIFICADOR -->
<section class="pg-panel">
    <div class="pg-panel-heading">
        <div>
            <span class="pg-kicker">UN PASO A LA VEZ</span>
            <h2>🗓️ Mi pequeño plan</h2>
        </div>
    </div>

    <p>Escribe algo que quieras hacer y márcalo cuando termines.</p>

    <form id="taskForm" class="pg-inline-form">
        <label class="pg-sr-only" for="taskInput">Nueva tarea</label>
        <input id="taskInput" maxlength="90"
               placeholder="Ej. Leer 5 minutos" required>
        <button class="pg-btn pg-primary" type="submit">+ Agregar</button>
    </form>

    <ul id="taskList" class="pg-task-list"></ul>
    <p id="taskEmpty" class="pg-muted">Todavía no tienes actividades.</p>
</section>

<!-- LABORATORIO CREATIVO -->
<section class="pg-panel pg-creative-panel">
    <div class="pg-panel-heading">
        <div>
            <span class="pg-kicker">IMAGINA Y CREA</span>
            <h2>🎨 Laboratorio de ideas</h2>
        </div>
    </div>

    <p>¿Listo para inventar algo extraordinario?</p>

    <div id="creativeIdea" class="pg-idea-box">
        Presiona el botón para descubrir tu próximo reto.
    </div>

    <button id="newIdea" type="button" class="pg-btn pg-primary">
        💡 Nueva idea
    </button>
</section>

<!-- DIARIO DE BIENESTAR -->
<section class="pg-panel">
    <div class="pg-panel-heading">
        <div>
            <span class="pg-kicker">CONOCE TUS EMOCIONES</span>
            <h2>💖 Mi rincón de bienestar</h2>
        </div>
    </div>

    <p>¿Cómo te sientes hoy? Todas las emociones son importantes.</p>

    <div class="pg-emotions" id="emotionButtons">
        <button type="button" data-emotion="feliz">😊 Feliz</button>
        <button type="button" data-emotion="tranquilo">😌 Tranquilo</button>
        <button type="button" data-emotion="triste">😢 Triste</button>
        <button type="button" data-emotion="nervioso">😟 Nervioso</button>
        <button type="button" data-emotion="enojado">😠 Enojado</button>
    </div>

    <div id="emotionResponse" class="pg-emotion-response" aria-live="polite">
        Puedes elegir cómo te sientes.
    </div>

    <label for="goodThing" class="pg-field-label">
        🌻 Algo bueno que me pasó hoy
    </label>

    <textarea id="goodThing" rows="3" maxlength="300"
              placeholder="Hoy me sentí orgulloso porque..."></textarea>

    <button id="saveGoodThing" type="button" class="pg-btn pg-primary">
        💛 Guardar mi recuerdo
    </button>

    <p id="goodThingStatus" class="pg-muted" aria-live="polite"></p>
</section>

</div>

<!-- COLUMNA DERECHA -->
<aside class="pg-sidebar">

<!-- LOGROS -->
<section class="pg-panel pg-achievements">
    <div class="pg-side-heading">
        <span class="pg-side-icon">🏆</span>
        <div>
            <h2>Mis logros de hoy</h2>
            <p>¡Cada paso merece celebrarse!</p>
        </div>
    </div>

    <div class="pg-progress-info">
        <strong id="progressLabel">0 de 4 logros</strong>
        <strong id="progressPercent">0%</strong>
    </div>

    <div id="progressBar" class="pg-progress-track"
         role="progressbar" aria-label="Logros completados"
         aria-valuemin="0" aria-valuemax="4" aria-valuenow="0">
        <div id="progressFill"></div>
    </div>

    <h3 class="pg-mini-heading">⭐ Misiones de Penguich</h3>

    <div class="pg-achievement-list" id="fixedAchievements">
        <label>
            <input type="checkbox" data-fixed="explorar">
            <span>🔍 Descubrí algo nuevo</span>
        </label>
        <label>
            <input type="checkbox" data-fixed="crear">
            <span>🎨 Usé mi imaginación</span>
        </label>
        <label>
            <input type="checkbox" data-fixed="practicar">
            <span>📚 Practiqué algo que estoy aprendiendo</span>
        </label>
        <label>
            <input type="checkbox" data-fixed="descansar">
            <span>🌿 Me tomé una pausa</span>
        </label>
    </div>

    <h3 class="pg-mini-heading">🌟 Mis propios logros</h3>

    <form id="achievementForm" class="pg-add-achievement">
        <label class="pg-sr-only" for="achievementInput">
            Escribe tu logro
        </label>
        <input id="achievementInput" maxlength="65"
               placeholder="Ej. Aprendí una palabra" required>
        <button type="submit" class="pg-btn pg-primary">+</button>
    </form>

    <div id="customAchievements" class="pg-achievement-list"></div>

    <p class="pg-muted pg-small">
        Los logros se reinician cada día. Puedes crear hasta 8 logros propios.
    </p>

    <div class="pg-gift-preview">
        <span>🎁</span>
        <p>¡Completa tus logros para descubrir tu regalo sorpresa!</p>
    </div>
</section>

<!-- PARQUE DE JUEGOS -->
<section class="pg-panel">
    <div class="pg-side-heading">
        <span class="pg-side-icon">🎮</span>
        <div>
            <h2>Parque de juegos</h2>
            <p>¡Aprende mientras te diviertes!</p>
        </div>
    </div>

    <button id="memoryBtn" type="button" class="pg-game-btn">
        <span>🧩</span>
        <span><strong>Memoria mágica</strong><small>Encuentra parejas</small></span>
        <b>→</b>
    </button>

    <button id="quizBtn" type="button" class="pg-game-btn">
        <span>🔎</span>
        <span><strong>Detective curioso</strong><small>Preguntas por edad</small></span>
        <b>→</b>
    </button>

    <button id="breathBtn" type="button" class="pg-game-btn">
        <span>🌬️</span>
        <span><strong>Momento de calma</strong><small>Respira sin prisas</small></span>
        <b>→</b>
    </button>

    <button id="wordsBtn" type="button" class="pg-game-btn">
        <span>🔤</span>
        <span><strong>Palabras mágicas</strong><small>Completa palabras</small></span>
        <b>→</b>
    </button>
</section>

<!-- HERRAMIENTAS -->
<section class="pg-panel">
    <div class="pg-side-heading">
        <span class="pg-side-icon">🧰</span>
        <div>
            <h2>Mi caja de herramientas</h2>
            <p>Pequeñas ayudas para cada día</p>
        </div>
    </div>

    <button id="mathBtn" type="button" class="pg-game-btn">
        <span>🔢</span>
        <span><strong>Matemáticas divertidas</strong><small>Resuelve a tu ritmo</small></span>
        <b>→</b>
    </button>

    <button id="storyBtn" type="button" class="pg-game-btn">
        <span>📖</span>
        <span><strong>Cuento sorpresa</strong><small>Lee o escucha una historia</small></span>
        <b>→</b>
    </button>

    <button id="movementBtn" type="button" class="pg-game-btn">
        <span>🤸</span>
        <span><strong>Pausa de movimiento</strong><small>Activa tu cuerpo</small></span>
        <b>→</b>
    </button>
</section>

<section class="pg-kind-card">
    <h3>💛 Algo que quiero que sepas</h3>
    <p>
        Tus ideas importan. Tu esfuerzo importa.
        No tienes que aprender igual que los demás
        para lograr cosas increíbles.
    </p>
</section>

</aside>
</div>
</main>

<footer class="pg-footer">
    🐧 Penguich · Cada mente tiene su propia forma de brillar.
</footer>

</div>

<!-- VENTANA DE ACTIVIDADES -->
<dialog id="activityDialog" class="pg-dialog">
    <div class="pg-dialog-header">
        <h2 id="dialogTitle">Actividad</h2>
        <button id="closeDialog" type="button"
                class="pg-dialog-close" aria-label="Cerrar">✕</button>
    </div>
    <div id="dialogBody"></div>
</dialog>

<!-- REGALO ANIMADO -->
<dialog id="giftDialog" class="pg-gift-dialog">
    <div class="pg-gift-content">
        <div id="giftAnimation" class="pg-gift-animation" aria-hidden="true">
            🎁
        </div>
        <h2>¡Lo conseguiste! 🎉</h2>
        <p>
            Completaste todos tus logros de hoy.
            ¡Tu esfuerzo merece una celebración!
        </p>
        <div class="pg-gift-stars">⭐ 🌟 ⭐</div>
        <strong>¡Eres una persona capaz de aprender cosas maravillosas!</strong>
        <button id="closeGift" type="button" class="pg-btn pg-primary">
            💛 ¡Gracias, Penguich!
        </button>
    </div>
</dialog>

<div id="pgLive" class="pg-sr-only" aria-live="polite"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
"use strict";

const $ = id => document.getElementById(id);
const storagePrefix = "penguich-v4-" +
    <?php echo json_encode($usuario_id); ?> + "-";

const today = () => {
    const d = new Date();
    return [
        d.getFullYear(),
        String(d.getMonth() + 1).padStart(2, "0"),
        String(d.getDate()).padStart(2, "0")
    ].join("-");
};

function load(key, fallback) {
    try {
        const value = localStorage.getItem(storagePrefix + key);
        return value === null ? fallback : JSON.parse(value);
    } catch {
        return fallback;
    }
}

function save(key, value) {
    try {
        localStorage.setItem(storagePrefix + key, JSON.stringify(value));
        return true;
    } catch {
        $("pgLive").textContent = "No se pudo guardar en este navegador.";
        return false;
    }
}

function announce(message) {
    $("pgLive").textContent = "";
    requestAnimationFrame(() => $("pgLive").textContent = message);
}

function make(tag, text = "", className = "") {
    const el = document.createElement(tag);
    el.textContent = text;
    if (className) el.className = className;
    return el;
}

function button(text, callback, className = "pg-btn pg-primary") {
    const el = make("button", text, className);
    el.type = "button";
    el.addEventListener("click", callback);
    return el;
}

/* ACCESIBILIDAD */
const filters = [
    "normal", "protanopia", "deuteranopia",
    "tritanopia", "contraste"
];

function setFilter(value) {
    if (!filters.includes(value)) value = "normal";
    document.body.dataset.pgFilter = value;
    $("colorFilter").value = value;
    save("filter", value);
}

setFilter(load("filter", "normal"));

$("colorFilter").addEventListener("change", e => {
    setFilter(e.target.value);
});

let largeText = load("largeText", false) === true;

function updateFont() {
    document.body.classList.toggle("pg-large", largeText);
    $("fontBtn").setAttribute("aria-pressed", String(largeText));
    save("largeText", largeText);
}

updateFont();

$("fontBtn").addEventListener("click", () => {
    largeText = !largeText;
    updateFont();
});

let speechActive = false;

function speak(text) {
    if (!("speechSynthesis" in window)) {
        announce("Este navegador no admite lectura en voz alta.");
        return;
    }

    speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = "es-MX";
    utterance.rate = 0.85;

    utterance.onend = utterance.onerror = () => {
        speechActive = false;
        $("voiceBtn").textContent = "🔊 Escuchar";
    };

    speechActive = true;
    $("voiceBtn").textContent = "⏹ Detener";
    speechSynthesis.speak(utterance);
}

$("voiceBtn").addEventListener("click", () => {
    if (speechActive) {
        speechSynthesis.cancel();
        speechActive = false;
        $("voiceBtn").textContent = "🔊 Escuchar";
    } else {
        speak($("welcomeText").textContent.trim());
    }
});

/* MENSAJES POSITIVOS */
const positiveMessages = [
    "🌟 No necesitas hacerlo perfecto para aprender.",
    "💛 Tus ideas son importantes y merecen ser escuchadas.",
    "🌈 Cada persona aprende a su propio ritmo.",
    "🚀 Hoy puedes intentar algo que ayer parecía difícil.",
    "🌱 Equivocarte también puede ayudarte a descubrir algo.",
    "🎨 Tu imaginación puede crear cosas sorprendentes.",
    "🐧 No tienes que compararte con nadie.",
    "✨ Tu curiosidad es una herramienta maravillosa.",
    "💪 Pedir ayuda también es una habilidad.",
    "💖 Mereces respeto y cariño tal como eres.",
    "🧠 Hay muchas maneras de resolver un problema.",
    "🌻 Descansar también forma parte de aprender.",
    "⭐ Lo que piensas y sientes importa.",
    "🎉 Cada pequeño avance merece reconocimiento."
];

let lastPositive = -1;

$("newMessage").addEventListener("click", () => {
    let i;
    do {
        i = Math.floor(Math.random() * positiveMessages.length);
    } while (i === lastPositive && positiveMessages.length > 1);

    lastPositive = i;
    $("positiveMessage").textContent = positiveMessages[i];
});

/* LOGROS */
const fixedIds = ["explorar", "crear", "practicar", "descansar"];
let achievementState = load("achievements", null);

if (!achievementState ||
    achievementState.date !== today() ||
    !Array.isArray(achievementState.fixed) ||
    !Array.isArray(achievementState.custom)) {
    achievementState = {
        date: today(),
        fixed: [],
        custom: [],
        celebrated: false
    };
}

achievementState.fixed = achievementState.fixed.filter(
    id => fixedIds.includes(id)
);

achievementState.custom = achievementState.custom
    .filter(a =>
        a && typeof a.id === "string" &&
        typeof a.text === "string" &&
        typeof a.done === "boolean"
    ).slice(0, 8);

function ensureToday() {
    if (achievementState.date !== today()) {
        achievementState = {
            date: today(),
            fixed: [],
            custom: [],
            celebrated: false
        };
        save("achievements", achievementState);
    }
}

const fixedChecks = [
    ...document.querySelectorAll("[data-fixed]")
];

function renderAchievements() {
    ensureToday();

    fixedChecks.forEach(check => {
        check.checked = achievementState.fixed.includes(
            check.dataset.fixed
        );
    });

    const container = $("customAchievements");
    container.replaceChildren();

    achievementState.custom.forEach(item => {
        const row = make("div", "", "pg-custom-achievement");
        const label = document.createElement("label");
        const check = document.createElement("input");
        check.type = "checkbox";
        check.checked = item.done;

        check.addEventListener("change", () => {
            item.done = check.checked;
            save("achievements", achievementState);
            renderAchievements();
        });

        label.append(check, make("span", "⭐ " + item.text));

        const remove = button("✕", () => {
            achievementState.custom =
                achievementState.custom.filter(a => a.id !== item.id);
            save("achievements", achievementState);
            renderAchievements();
        }, "pg-remove");

        remove.setAttribute("aria-label", "Eliminar " + item.text);
        row.append(label, remove);
        container.append(row);
    });

    const total = fixedIds.length + achievementState.custom.length;
    const done = achievementState.fixed.length +
        achievementState.custom.filter(a => a.done).length;
    const percent = Math.round(done / total * 100);

    $("progressLabel").textContent = `${done} de ${total} logros`;
    $("progressPercent").textContent = percent + "%";
    $("progressFill").style.width = percent + "%";
    $("progressBar").setAttribute("aria-valuemax", total);
    $("progressBar").setAttribute("aria-valuenow", done);

    if (done === total && !achievementState.celebrated) {
        achievementState.celebrated = true;
        save("achievements", achievementState);
        showGift();
    }

    save("achievements", achievementState);
}

fixedChecks.forEach(check => {
    check.addEventListener("change", () => {
        ensureToday();
        const id = check.dataset.fixed;

        if (check.checked) {
            if (!achievementState.fixed.includes(id)) {
                achievementState.fixed.push(id);
            }
        } else {
            achievementState.fixed =
                achievementState.fixed.filter(x => x !== id);
        }

        save("achievements", achievementState);
        renderAchievements();
    });
});

$("achievementForm").addEventListener("submit", e => {
    e.preventDefault();
    ensureToday();

    const input = $("achievementInput");
    const text = input.value.trim();

    if (!text) return;

    if (achievementState.custom.length >= 8) {
        announce("Puedes agregar hasta ocho logros propios.");
        return;
    }

    achievementState.custom.push({
        id: Date.now().toString(36) +
            Math.random().toString(36).slice(2),
        text: text.slice(0, 65),
        done: false
    });

    // Si se agrega una misión nueva, se permite
    // celebrar al completar la nueva lista.
    achievementState.celebrated = false;

    input.value = "";
    save("achievements", achievementState);
    renderAchievements();
});

renderAchievements();

function showGift() {
    const dialog = $("giftDialog");
    if (!dialog.open) dialog.showModal();

    const gift = $("giftAnimation");
    gift.textContent = "🎁";

    setTimeout(() => {
        if (dialog.open) gift.textContent = "🎉";
    }, 1200);

    announce("¡Felicidades! Completaste todos tus logros.");
}

$("closeGift").addEventListener("click", () => {
    $("giftDialog").close();
});

/* PLANIFICADOR */
let tasks = load("tasks", []);

if (!Array.isArray(tasks)) tasks = [];

tasks = tasks.filter(t =>
    t && typeof t.id === "string" &&
    typeof t.text === "string" &&
    typeof t.done === "boolean"
).slice(0, 50);

function renderTasks() {
    const list = $("taskList");
    list.replaceChildren();

    tasks.forEach(task => {
        const li = make("li");
        if (task.done) li.classList.add("pg-task-done");

        const check = document.createElement("input");
        check.type = "checkbox";
        check.checked = task.done;
        check.setAttribute("aria-label", "Completar " + task.text);

        check.addEventListener("change", () => {
            task.done = check.checked;
            save("tasks", tasks);
            renderTasks();
        });

        const remove = button("Eliminar", () => {
            tasks = tasks.filter(t => t.id !== task.id);
            save("tasks", tasks);
            renderTasks();
        }, "pg-remove");

        li.append(check, make("span", task.text), remove);
        list.append(li);
    });

    $("taskEmpty").hidden = tasks.length > 0;
}

$("taskForm").addEventListener("submit", e => {
    e.preventDefault();

    const input = $("taskInput");
    const text = input.value.trim();

    if (!text) return;

    if (tasks.length >= 50) {
        announce("Puedes tener hasta 50 actividades.");
        return;
    }

    tasks.push({
        id: Date.now().toString(36) +
            Math.random().toString(36).slice(2),
        text: text.slice(0, 90),
        done: false
    });

    input.value = "";
    save("tasks", tasks);
    renderTasks();
});

renderTasks();

/* CHAT EDUCATIVO INICIAL */

const chatTopics = {

    deportes: [
        "⚽ ¡Exploremos tu coordinación! Coloca tres objetos separados en el suelo y camina alrededor de ellos sin tocarlos. ¿Qué recorrido se te ocurre?",

        "🏀 ¡Reto de precisión! Intenta lanzar una pelota suave dentro de una caja. Puedes cambiar la distancia y probar distintas técnicas.",

        "🤸 ¡Descubre tu equilibrio! Mantente sobre un pie unos segundos, cerca de una pared o con apoyo si lo necesitas. ¿Qué te ayuda a mantenerte estable?",

        "🏃 ¡Diseña un deporte nuevo! Inventa sus reglas, el objetivo y cómo podrían participar personas con distintas habilidades.",

        "🧠 ¡Movimiento y memoria! Inventa una secuencia de tres movimientos. Repítela y después agrega uno más.",

        "🥎 ¡Explora tu puntería! Construye un blanco grande de papel y lanza una pelota de tela. Prueba distintas formas de apuntar."
    ],

    arte: [
        "🎨 ¡Reto de imaginación! Dibuja un animal que combine tres criaturas diferentes. ¿Qué habilidades especiales tendría?",

        "🏗️ ¡Arquitectura fantástica! Diseña una casa que pueda flotar. Piensa qué materiales necesitaría.",

        "🧵 ¡Diseñador por un día! Inventa un uniforme para un deporte que todavía no existe.",

        "🖌️ ¡Arte con emociones! Representa la alegría, la calma o la sorpresa usando solamente colores y formas.",

        "♻️ ¡Inventor creativo! Utiliza materiales reciclados limpios para construir un objeto útil.",

        "🧠 ¡Memoria visual! Observa un dibujo durante unos segundos, ocúltalo e intenta reproducir sus detalles."
    ],

    estrategia: [
        "♟️ ¡Piensa como estratega! Imagina que tienes que cruzar un río con tres objetos. ¿Qué necesitarías planear primero?",

        "🧩 ¡Detective de patrones! Crea una secuencia con colores o figuras y pide a alguien que descubra cuál sigue.",

        "🏰 ¡Constructor de soluciones! Diseña un puente de papel que pueda sostener un objeto ligero.",

        "🎲 ¡Inventor de juegos! Crea un juego de mesa con tres reglas y una forma de ganar.",

        "🔍 ¡Reto de decisiones! Piensa en dos maneras de resolver un problema. ¿Qué ventajas tiene cada una?",

        "🧠 ¡Entrena tu planificación! Antes de comenzar una construcción, imagina los primeros tres pasos."
    ],

    musica: [
        "🥁 ¡Crea tu propio ritmo! Haz una secuencia de palmadas y golpes suaves sobre la mesa. Después repítela.",

        "🎵 ¡Memoria musical! Escucha una secuencia corta de sonidos e intenta reproducirla.",

        "🎤 ¡Compositor por un día! Inventa una canción sobre algo que te guste mucho.",

        "🎼 ¡Explorador de sonidos! Busca cinco objetos seguros que produzcan sonidos diferentes.",

        "💃 ¡Movimiento y ritmo! Crea una pequeña coreografía de cuatro movimientos.",

        "🧠 ¡Aprende con música! Inventa una canción para recordar una lista de palabras o una secuencia."
    ],

    exploracion: [
        "🌿 ¡Explorador de naturaleza! Observa una hoja y describe su forma, color y textura.",

        "🔎 ¡Detective de detalles! Elige un objeto y encuentra cinco características que normalmente no notas.",

        "🚀 ¡Diseñador espacial! Imagina una herramienta que ayude a explorar otro planeta.",

        "🌦️ ¡Observador del clima! Mira el cielo y describe las nubes. ¿Qué cambios notas durante el día?",

        "🧪 ¡Pequeño investigador! Coloca un objeto que flote y otro que se hunda en agua, con supervisión adulta. ¿Qué diferencias observas?",

        "🧠 ¡Recuerda lo observado! Después de explorar un lugar, intenta dibujar cinco cosas que viste."
    ],

    liderazgo: [
        "🤝 ¡Líder de equipo! Imagina que organizas un juego para cinco personas. ¿Cómo harías para que todas participen?",

        "💬 ¡Comunicación creativa! Explica las reglas de un juego de manera sencilla y amable.",

        "🌟 ¡Resolución de conflictos! Dos compañeros quieren usar el mismo juguete. Piensa en tres soluciones justas.",

        "🎯 ¡Organizador de misiones! Planea una actividad en equipo y reparte tareas según lo que cada persona disfruta.",

        "💛 ¡Empatía en acción! Imagina cómo ayudarías a alguien que se siente excluido de un juego.",

        "🧠 ¡Memoria colaborativa! Crea una historia con otra persona. Cada participante debe recordar lo anterior y agregar una frase."
    ],

    memoria: [
        "🧠 ¡Memoria de detective! Observa cinco objetos durante 20 segundos. Después intenta recordar sus nombres sin mirar.",

        "🎨 ¡Memoria con dibujos! Representa una idea mediante un dibujo y úsalo para recordarla después.",

        "🎵 ¡Memoria musical! Convierte una lista de palabras en una canción divertida.",

        "🗣️ ¡Enséñale a Penguich! Explica con tus propias palabras algo que aprendiste hoy.",

        "🧩 ¡Agrupa para recordar! Organiza varios objetos o palabras en grupos que tengan algo en común.",

        "📅 ¡Recuerda otro día! Aprende tres datos pequeños hoy, intenta recordarlos mañana y vuelve a practicarlos más adelante.",

        "🔄 ¡Recupera sin mirar! Después de aprender algo, cierra el libro e intenta decir lo que recuerdas. Luego revisa qué faltó.",

        "🏃 ¡Aprende en movimiento! Asocia una palabra o concepto con un gesto y úsalo para practicar."
    ],

    expresion: [
        "🎭 ¡Actor por un día! Representa una emoción sin hablar. ¿Puede alguien adivinarla?",

        "🎤 ¡Cuenta una historia! Inventa una aventura que tenga un personaje, un problema y una solución.",

        "🗣️ ¡Tu voz importa! Explica algo que te entusiasme durante un minuto, sin preocuparte por hacerlo perfecto.",

        "🎨 ¡Expresión sin palabras! Comunica una idea mediante dibujos o movimientos.",

        "🌟 ¡Descubre tus fortalezas! Piensa en tres cosas que disfrutas hacer, aunque todavía estés aprendiendo.",

        "💛 ¡Reto de confianza! Recuerda algo que antes te costaba y ahora haces con mayor facilidad."
    ]

};


const chatMessages = $("chatMessages");

function addChatMessage(text, who) {
    const row = make("div", "", "pg-chat-message pg-" + who);
    const avatar = make("span", who === "bot" ? "🐧" : "🙂",
                        "pg-chat-avatar");
    const bubble = make("div", text, "pg-chat-bubble");

    row.append(avatar, bubble);
    chatMessages.append(row);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // No se almacena el historial del chat.
}

function chooseResponse(topic) {
    const options = chatTopics[topic];
    return options[Math.floor(Math.random() * options.length)];
}


function detectTopic(text) {

    const value = text
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

    if (/deporte|futbol|basquet|correr|saltar|pelota|equilibrio|coordinacion|agilidad|movimiento/.test(value)) {
        return "deportes";
    }

    if (/arte|dibujar|pintar|crear|inventar|disenar|imaginacion|manualidad|construir/.test(value)) {
        return "arte";
    }

    if (/estrategia|ajedrez|logica|rompecabezas|resolver|planear|juego de mesa|patron/.test(value)) {
        return "estrategia";
    }

    if (/musica|cantar|ritmo|bailar|instrumento|sonido|cancion/.test(value)) {
        return "musica";
    }

    if (/explorar|naturaleza|ciencia|experimento|investigar|descubrir|observar|animales|plantas/.test(value)) {
        return "exploracion";
    }

    if (/lider|equipo|ayudar|organizar|colaborar|companeros|amistad/.test(value)) {
        return "liderazgo";
    }

    if (/memoria|recordar|memorizar|aprender|estudiar|retener|olvidar|informacion|concentracion/.test(value)) {
        return "memoria";
    }

    if (/hablar|expresar|actuar|teatro|confianza|timidez|historia|emociones|sentimientos/.test(value)) {
        return "expresion";
    }

    return null;
}


function replyToChat(text, forcedTopic = null) {
    addChatMessage(text, "user");

    const topic = forcedTopic || detectTopic(text);

    const response = topic
        ? chooseResponse(topic)
       : "🐧 ¡Qué interesante lo que me cuentas! Podemos explorar deportes, arte, música, estrategia, naturaleza, liderazgo, memoria o expresión. Elige una aventura y descubrirás actividades para desarrollar nuevas habilidades. 💚";
    addChatMessage(response, "bot");
}

$("chatForm").addEventListener("submit", e => {
    e.preventDefault();

    const input = $("chatInput");
    const text = input.value.trim();

    if (!text) return;

    input.value = "";
    replyToChat(text.slice(0, 250));
});

document.querySelectorAll("[data-chat]").forEach(btn => {
    btn.addEventListener("click", () => {
        const topic = btn.dataset.chat;
        replyToChat(btn.textContent.trim(), topic);
    });
});

/* LABORATORIO CREATIVO */
const ideas = [
    "🚀 Dibuja una nave espacial que pueda viajar por el océano.",
    "🐾 Inventa un animal fantástico y ponle un nombre.",
    "🏰 Construye un castillo con materiales reciclados.",
    "🌈 Imagina una ciudad donde todo funcione con colores.",
    "📖 Inventa una historia con un pingüino, una llave y una estrella.",
    "🎵 Crea un ritmo usando tus manos.",
    "🌱 Diseña un jardín para animales mágicos.",
    "🤖 Imagina un robot que cuide la naturaleza.",
    "🎭 Representa una emoción usando gestos.",
    "🧩 Diseña un juego que puedas enseñar a otra persona."
];

$("newIdea").addEventListener("click", () => {
    const idea = ideas[Math.floor(Math.random() * ideas.length)];
    $("creativeIdea").textContent = idea;
});

/* EMOCIONES */
const emotionResponses = {
    feliz: "😊 ¡Qué bonito! Puedes disfrutar ese momento y compartirlo con alguien.",
    tranquilo: "😌 Qué agradable sentir tranquilidad. Puedes aprovecharla para crear o descansar.",
    triste: "💛 Está bien sentirse triste. Puedes hablar con alguien de confianza o hacer algo que te reconforte.",
    nervioso: "🌿 A veces sentimos nervios. Respira suavemente y recuerda que puedes pedir ayuda.",
    enojado: "💛 Sentir enojo es normal. Puedes tomar un descanso y expresar lo que necesitas con palabras."
};

document.querySelectorAll("[data-emotion]").forEach(btn => {
    btn.addEventListener("click", () => {
        $("emotionResponse").textContent =
            emotionResponses[btn.dataset.emotion];
        save("emotion", {
            date: today(),
            value: btn.dataset.emotion
        });
    });
});

const savedEmotion = load("emotion", null);

if (savedEmotion && savedEmotion.date === today() &&
    emotionResponses[savedEmotion.value]) {
    $("emotionResponse").textContent =
        emotionResponses[savedEmotion.value];
}

$("goodThing").value = load("goodThing", "");

$("saveGoodThing").addEventListener("click", () => {
    const text = $("goodThing").value.trim().slice(0, 300);

    if (!text) {
        $("goodThingStatus").textContent =
            "Escribe primero algo que quieras recordar.";
        return;
    }

    if (save("goodThing", text)) {
        $("goodThingStatus").textContent =
            "💛 Tu recuerdo quedó guardado en este navegador.";
    }
});

/* ACTIVIDADES */
const dialog = $("activityDialog");
const dialogBody = $("dialogBody");
let cleanupActivity = () => {};

function closeActivity() {
    cleanupActivity();
    cleanupActivity = () => {};
    if (dialog.open) dialog.close();
}

function openActivity(title) {
    closeActivity();
    $("dialogTitle").textContent = title;
    dialogBody.replaceChildren();
    dialog.showModal();
}

$("closeDialog").addEventListener("click", closeActivity);

dialog.addEventListener("cancel", () => {
    cleanupActivity();
    cleanupActivity = () => {};
});

function p(text, className = "") {
    return make("p", text, className);
}

function activityButton(text, callback) {
    return button(text, callback, "pg-btn pg-primary");
}

/* MEMORIA */
$("memoryBtn").addEventListener("click", startMemory);

function startMemory() {
    openActivity("🧩 Memoria mágica");

    const status = p("Parejas encontradas: 0 de 4", "pg-game-status");
    const board = make("div", "", "pg-memory-board");

    dialogBody.append(
        p("Encuentra las cuatro parejas. No hay límite de tiempo."),
        status,
        board
    );

    const symbols = ["🐧", "🌈", "⭐", "🚀"];
    const deck = [...symbols, ...symbols]
        .map((symbol, i) => ({ symbol, i }))
        .sort(() => Math.random() - 0.5);

    let selected = [];
    let matched = new Set();
    let locked = false;
    let timer = null;

    const cards = deck.map((card, index) => {
        const el = make("button", "?", "pg-memory-card");
        el.type = "button";
        el.setAttribute("aria-label", "Carta oculta");

        el.addEventListener("click", () => {
            if (locked || matched.has(index) ||
                selected.includes(index)) return;

            el.textContent = card.symbol;
            el.setAttribute("aria-label", card.symbol);
            selected.push(index);

            if (selected.length === 2) {
                const [a, b] = selected;

                if (deck[a].symbol === deck[b].symbol) {
                    matched.add(a);
                    matched.add(b);
                    cards[a].classList.add("pg-matched");
                    cards[b].classList.add("pg-matched");
                    selected = [];

                    status.textContent =
                        `Parejas encontradas: ${matched.size / 2} de 4`;

                    if (matched.size === 8) {
                        status.textContent =
                            "🎉 ¡Encontraste todas las parejas!";
                    }
                } else {
                    locked = true;
                    timer = setTimeout(() => {
                        cards[a].textContent = "?";
                        cards[b].textContent = "?";
                        cards[a].setAttribute("aria-label", "Carta oculta");
                        cards[b].setAttribute("aria-label", "Carta oculta");
                        selected = [];
                        locked = false;
                        timer = null;
                    }, 1100);
                }
            }
        });

        board.append(el);
        return el;
    });

    dialogBody.append(activityButton("Jugar otra vez", startMemory));

    cleanupActivity = () => {
        if (timer !== null) clearTimeout(timer);
    };
}

/* BANCO DE PREGUNTAS */
const questionBank = [
    // 4-6 AÑOS
    {age:1,q:"🐱 ¿Qué sonido hace un gato?",a:["Miau","Guau","Pío"],c:0},
    {age:1,q:"🍎 ¿Cuántas manzanas hay? 🍎🍎🍎",a:["2","3","5"],c:1},
    {age:1,q:"☀️ ¿Qué vemos en el cielo durante el día?",a:["El Sol","Una cama","Un zapato"],c:0},
    {age:1,q:"🐟 ¿Dónde vive un pez?",a:["En el agua","En un árbol","En la Luna"],c:0},
    {age:1,q:"🔵 ¿Qué figura es redonda?",a:["Círculo","Triángulo","Cuadrado"],c:0},
    {age:1,q:"🌱 ¿Qué necesita una planta?",a:["Agua","Juguetes","Zapatos"],c:0},
    {age:1,q:"🖐️ ¿Cuántos dedos tiene normalmente una mano?",a:["3","5","8"],c:1},
    {age:1,q:"🐧 ¿Cuál de estos animales es un ave?",a:["Pingüino","Perro","Gato"],c:0},
    {age:1,q:"🌈 ¿Qué color resulta al mezclar pintura azul y amarilla?",a:["Verde","Rojo","Rosa"],c:0},
    {age:1,q:"👂 ¿Qué parte del cuerpo usamos para escuchar?",a:["Oídos","Rodillas","Codos"],c:0},

    // 7-9 AÑOS
    {age:2,q:"🔢 ¿Cuánto es 8 + 7?",a:["14","15","16"],c:1},
    {age:2,q:"🌍 ¿En qué planeta vivimos?",a:["Marte","Tierra","Venus"],c:1},
    {age:2,q:"📖 ¿Cuál es un sinónimo de feliz?",a:["Alegre","Enojado","Cansado"],c:0},
    {age:2,q:"🐝 ¿Qué producen las abejas?",a:["Miel","Queso","Pan"],c:0},
    {age:2,q:"🧮 ¿Cuánto es 6 × 3?",a:["12","18","24"],c:1},
    {age:2,q:"🌳 ¿Qué absorben las raíces de las plantas?",a:["Agua y minerales","Luz de estrellas","Sonidos"],c:0},
    {age:2,q:"📐 ¿Cuántos lados tiene un hexágono?",a:["5","6","8"],c:1},
    {age:2,q:"⏰ ¿Cuántos minutos tiene una hora?",a:["30","60","100"],c:1},
    {age:2,q:"🌊 ¿Qué estado tiene el agua en un cubo de hielo?",a:["Sólido","Gas","Plasma"],c:0},
    {age:2,q:"🔤 ¿Cuál palabra está escrita correctamente?",a:["Maripoza","Mariposa","Maripossa"],c:1},

    // 10-12 AÑOS
    {age:3,q:"🧮 ¿Cuánto es 12 × 8?",a:["86","96","108"],c:1},
    {age:3,q:"🌍 ¿Qué fuerza nos mantiene sobre la Tierra?",a:["Gravedad","Magnetismo únicamente","Electricidad"],c:0},
    {age:3,q:"📐 ¿Cuánto suman los ángulos interiores de un triángulo?",a:["90°","180°","360°"],c:1},
    {age:3,q:"🧪 ¿Cuál es la fórmula química del agua?",a:["CO₂","H₂O","O₂"],c:1},
    {age:3,q:"🔢 ¿Cuál es el 25% de 200?",a:["25","50","75"],c:1},
    {age:3,q:"📚 ¿Qué es una metáfora?",a:["Una comparación figurada","Una operación matemática","Una fecha"],c:0},
    {age:3,q:"🌿 ¿Qué proceso permite a las plantas producir alimento usando luz?",a:["Fotosíntesis","Evaporación","Digestión"],c:0},
    {age:3,q:"🧠 Si todos los gatos son mamíferos, ¿qué afirmación es correcta?",a:["Todos los mamíferos son gatos","Todos los gatos son mamíferos","Ningún gato es mamífero"],c:1},
    {age:3,q:"📏 ¿Cuántos centímetros hay en 2.5 metros?",a:["25","250","2500"],c:1},
    {age:3,q:"🔬 ¿Qué órgano bombea la sangre?",a:["Pulmones","Corazón","Estómago"],c:1}
];

$("quizBtn").addEventListener("click", () => {
    openActivity("🔎 Detective curioso");

    dialogBody.append(
        p("Elige un nivel. Puedes cambiarlo cuando quieras.")
    );

    const levels = make("div", "", "pg-levels");

    [
        ["4 a 6 años", 1],
        ["7 a 9 años", 2],
        ["10 a 12 años", 3]
    ].forEach(([name, age]) => {
        levels.append(activityButton(name, () => startQuiz(age)));
    });

    dialogBody.append(levels);
});

function startQuiz(age) {
    openActivity("🔎 Detective curioso");

    const questions = questionBank.filter(q => q.age === age);
    let index = 0;
    let correct = 0;

    const shuffled = [...questions].sort(() => Math.random() - 0.5);

    function next() {
        dialogBody.replaceChildren();

        if (index >= shuffled.length) {
            dialogBody.append(
                p(`¡Terminaste! Respondiste ${correct} de ${shuffled.length} preguntas correctamente.`, "pg-game-status"),
                p("Cada respuesta es una oportunidad para aprender. ¡Gracias por intentarlo!"),
                activityButton("Volver a jugar", () => startQuiz(age))
            );
            return;
        }

        const q = shuffled[index];

        dialogBody.append(
            p(`Pregunta ${index + 1} de ${shuffled.length}`, "pg-muted"),
            p(q.q, "pg-game-status")
        );

        const options = make("div", "", "pg-quiz-options");
        const feedback = p("", "pg-feedback");

        q.a.forEach((answer, answerIndex) => {
            const btn = button(answer, () => {
                [...options.children].forEach(b => b.disabled = true);

                if (answerIndex === q.c) {
                    correct++;
                    feedback.textContent = "🌟 ¡Muy bien! Esa es la respuesta.";
                    feedback.className = "pg-feedback pg-correct";
                } else {
                    feedback.textContent =
                        "💛 Buen intento. La respuesta es: " + q.a[q.c];
                    feedback.className = "pg-feedback pg-try";
                }

                dialogBody.append(
                    activityButton("Siguiente pregunta →", () => {
                        index++;
                        next();
                    })
                );
            }, "pg-option");

            options.append(btn);
        });

        dialogBody.append(options, feedback);
    }

    next();
}

/* RESPIRACIÓN */
$("breathBtn").addEventListener("click", () => {
    openActivity("🌿 Momento de calma");

    const circle = make("div", "🌿", "pg-breath-circle");
    const instruction = p(
        "Presiona comenzar cuando quieras.",
        "pg-game-status"
    );

    dialogBody.append(
        p("Respira suavemente. Puedes detenerte en cualquier momento."),
        circle,
        instruction
    );

    let timer = null;
    let step = 0;
    const steps = [
        "Inhala suavemente",
        "Exhala despacio",
        "Descansa un momento"
    ];

    const start = activityButton("Comenzar", () => {
        if (timer !== null) return;

        step = 0;
        instruction.textContent = steps[step];
        circle.classList.add("pg-breath-active");

        timer = setInterval(() => {
            step = (step + 1) % steps.length;
            instruction.textContent = steps[step];
        }, 4000);
    });

    const stop = activityButton("Detener", () => {
        if (timer !== null) clearInterval(timer);
        timer = null;
        circle.classList.remove("pg-breath-active");
        instruction.textContent = "¡Muy bien! Puedes descansar.";
    });

    dialogBody.append(start, stop);

    cleanupActivity = () => {
        if (timer !== null) clearInterval(timer);
    };
});

/* PALABRAS MÁGICAS */
const wordBank = [
    {hint:"🐱 Animal que dice miau",word:"GATO"},
    {hint:"☀️ Estrella que ilumina nuestro día",word:"SOL"},
    {hint:"🌙 La vemos muchas noches",word:"LUNA"},
    {hint:"🌳 Planta grande con tronco",word:"ARBOL"},
    {hint:"🐧 Ave que nada y vive en regiones frías",word:"PINGUINO"},
    {hint:"🌈 Aparece a veces después de la lluvia",word:"ARCOIRIS"},
    {hint:"📚 Lo leemos para conocer historias",word:"LIBRO"},
    {hint:"⭐ Brilla en el cielo nocturno",word:"ESTRELLA"}
];

$("wordsBtn").addEventListener("click", startWords);

function startWords() {
    openActivity("🔤 Palabras mágicas");

    const item = wordBank[Math.floor(Math.random() * wordBank.length)];
    const input = document.createElement("input");
    input.className = "pg-answer-input";
    input.maxLength = 30;
    input.placeholder = "Escribe la palabra";
    input.setAttribute("aria-label", "Respuesta");

    const result = p("", "pg-feedback");

    dialogBody.append(
        p(item.hint, "pg-game-status"),
        input,
        activityButton("Comprobar", () => {
            const normalize = s => s.normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .toUpperCase().trim();

            if (normalize(input.value) === item.word) {
                result.textContent = "🌟 ¡Excelente! Descubriste la palabra.";
            } else {
                result.textContent =
                    "💛 Sigue intentando. Puedes pedir ayuda.";
            }
        }),
        result,
        activityButton("Otra palabra", startWords)
    );
}

/* MATEMÁTICAS */
$("mathBtn").addEventListener("click", startMath);

function startMath() {
    openActivity("🔢 Matemáticas divertidas");

    const a = Math.floor(Math.random() * 10) + 1;
    const b = Math.floor(Math.random() * 10) + 1;
    const answer = a + b;

    const input = document.createElement("input");
    input.type = "number";
    input.className = "pg-answer-input";
    input.setAttribute("aria-label", "Resultado de la suma");

    const result = p("", "pg-feedback");

    dialogBody.append(
        p(`¿Cuánto es ${a} + ${b}?`, "pg-game-status"),
        input,
        activityButton("Comprobar", () => {
            if (input.value.trim() !== "" &&
                Number(input.value) === answer) {
                result.textContent = "🎉 ¡Muy bien! Lo resolviste.";
            } else {
                result.textContent =
                    "💛 Puedes contar con los dedos o usar objetos para ayudarte.";
            }
        }),
        result,
        activityButton("Otra suma", startMath)
    );
}

/* CUENTOS */
const stories = [
    {
        title:"La estrella curiosa",
        text:"Había una vez una estrella que pensaba que brillaba muy poquito. Una noche descubrió que su pequeña luz ayudaba a un viajero a encontrar el camino. Entonces comprendió que no necesitaba ser la estrella más grande para hacer algo importante."
    },
    {
        title:"El pingüino que aprendía despacio",
        text:"Penguich quería construir una cometa. La primera vez no voló. La segunda tampoco. Entonces pidió ayuda, cambió algunas piezas y volvió a intentarlo. Cuando la cometa se elevó, sonrió. Había descubierto que aprender puede necesitar muchos intentos."
    },
    {
        title:"El jardín de las ideas",
        text:"En un jardín crecían flores de muchos colores. Algunas florecían temprano y otras tardaban más. El jardinero cuidaba de todas con paciencia, porque sabía que cada flor tenía su propio momento para crecer."
    }
];

$("storyBtn").addEventListener("click", () => {
    openActivity("📖 Cuento sorpresa");

    const story = stories[Math.floor(Math.random() * stories.length)];

    dialogBody.append(
        p(story.title, "pg-game-status"),
        p(story.text),
        activityButton("🔊 Escuchar cuento", () => {
            speak(story.title + ". " + story.text);
        }),
        activityButton("Otro cuento", () => {
            if ("speechSynthesis" in window) speechSynthesis.cancel();
            $("storyBtn").click();
        })
    );

    cleanupActivity = () => {
        if ("speechSynthesis" in window) speechSynthesis.cancel();
    };
});

/* MOVIMIENTO */
const movements = [
    "🤸 Estira los brazos hacia arriba como si quisieras tocar una estrella.",
    "🐧 Camina suavemente como un pingüino.",
    "🦋 Mueve los brazos como alas de mariposa.",
    "🌳 Imagina que eres un árbol y mueve los brazos con el viento.",
    "🐱 Estírate como un gatito que acaba de despertar.",
    "🌈 Mueve los hombros lentamente y relájalos."
];

$("movementBtn").addEventListener("click", () => {
    openActivity("🤸 Pausa de movimiento");

    const instruction = p(
        movements[Math.floor(Math.random() * movements.length)],
        "pg-game-status"
    );

    dialogBody.append(
        p("Haz solamente movimientos que te resulten cómodos."),
        instruction,
        activityButton("Otro movimiento", () => {
            instruction.textContent =
                movements[Math.floor(Math.random() * movements.length)];
        })
    );
});

});
</script>

</body>
</html>
