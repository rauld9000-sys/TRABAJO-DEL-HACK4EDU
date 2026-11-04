
<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = htmlspecialchars(
    (string) $_SESSION['usuario'],
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);

$usuarioId = hash('sha256', (string) $_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Penguich | Mi ruta de aprendizaje</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body class="ruta-penguich">

<div class="ruta-container">

<header class="ruta-header">
    <a href="usuario.php" class="ruta-back">
        ← Volver a mi aventura
    </a>
    <div class="ruta-brand">🐧 Penguich</div>
    <button id="rutaVoice" class="ruta-small-btn" type="button">
        🔊 Escuchar
    </button>
</header>

<main>

<section class="ruta-hero">
    <div>
        <span class="ruta-eyebrow">🌈 UNA AVENTURA A TU RITMO</span>
        <h1>¡Construyamos tu ruta de aprendizaje!</h1>
        <p>
            Hola, <?php echo $usuario; ?>. Aquí descubrirás nuevas
            habilidades, practicarás de formas divertidas y aprenderás
            trucos para recordar lo que descubres.
        </p>
    </div>
    <div class="ruta-hero-emoji" aria-hidden="true">🐧🗺️</div>
</section>

<section id="rutaConfig" class="ruta-card">
    <span class="ruta-eyebrow">PASO 1 · PERSONALIZA TU AVENTURA</span>
    <h2>¿Qué te gustaría explorar?</h2>
    <p>Elige lo que más te interese. Puedes probar otras rutas después.</p>

    <div id="rutaAreas" class="ruta-areas"></div>

    <div class="ruta-config-bottom">
        <div>
            <label for="rutaEdad">Mi grupo de edad</label>
            <select id="rutaEdad">
                <option value="4-6">4 a 6 años</option>
                <option value="7-9" selected>7 a 9 años</option>
                <option value="10-12">10 a 12 años</option>
            </select>
        </div>
        <button id="rutaStart" class="ruta-btn ruta-primary" type="button">
            🚀 Crear mi ruta
        </button>
    </div>
</section>

<section id="rutaWorkspace" hidden>

    <div class="ruta-summary ruta-card">
        <div class="ruta-summary-top">
            <div>
                <span class="ruta-eyebrow">MI AVENTURA ACTUAL</span>
                <h2 id="rutaTitle"></h2>
                <p id="rutaSubtitle"></p>
            </div>
            <button id="rutaChange" class="ruta-small-btn" type="button">
                Cambiar ruta
            </button>
        </div>

        <div class="ruta-progress-label">
            <strong id="rutaProgressText">0 de 5 misiones</strong>
            <strong id="rutaProgressPercent">0%</strong>
        </div>

        <div class="ruta-progress" role="progressbar"
             id="rutaProgressBar"
             aria-valuemin="0" aria-valuemax="5" aria-valuenow="0">
            <div id="rutaProgressFill"></div>
        </div>

        <p id="rutaEncouragement" class="ruta-encouragement"></p>
    </div>

    <div class="ruta-workspace-grid">

        <section class="ruta-card">
            <span class="ruta-eyebrow">PASO 2 · MIS PEQUEÑAS MISIONES</span>
            <h2>🗺️ Mi camino de aventuras</h2>
            <p>Completa una misión cuando hayas intentado su actividad.</p>
            <div id="rutaMissionList" class="ruta-mission-list"></div>
        </section>

        <aside class="ruta-card ruta-mission-detail">
            <span class="ruta-eyebrow">MI MISIÓN</span>
            <h2 id="rutaMissionTitle"></h2>
            <p id="rutaMissionDescription"></p>

            <div class="ruta-activity-box">
                <h3>🎯 Tu reto</h3>
                <p id="rutaMissionActivity"></p>
            </div>

            <div class="ruta-memory-box">
                <h3>🧠 Para recordar mejor</h3>
                <p id="rutaMissionMemory"></p>
            </div>

            <label for="rutaReflection">
                💛 ¿Qué descubriste o intentaste?
            </label>
            <textarea id="rutaReflection" rows="4"
                maxlength="500"
                placeholder="Puedes escribir lo que hiciste, qué te gustó o qué intentarías diferente."></textarea>

            <div class="ruta-detail-actions">
                <button id="rutaComplete" class="ruta-btn ruta-primary" type="button">
                    ⭐ Completar misión
                </button>
                <button id="rutaSaveNote" class="ruta-btn ruta-secondary" type="button">
                    Guardar mi idea
                </button>
            </div>
            <p id="rutaSaveStatus" class="ruta-status" aria-live="polite"></p>
        </aside>

    </div>

    <section class="ruta-card ruta-review">
        <span class="ruta-eyebrow">PASO 3 · MI MEMORIA EN ACCIÓN</span>
        <h2>🧠 ¿Qué puedo recordar?</h2>
        <p>
            Sin mirar tus notas, intenta recordar lo que hiciste
            en tu última misión. No importa si recuerdas poco:
            después puedes revisar y volver a intentarlo.
        </p>

        <label for="rutaRecall">Lo que recuerdo</label>
        <textarea id="rutaRecall" rows="3" maxlength="500"
            placeholder="Recuerdo que aprendí o practiqué..."></textarea>

        <button id="rutaSaveRecall" class="ruta-btn ruta-primary" type="button">
            💾 Guardar lo que recuerdo
        </button>

        <p id="rutaRecallStatus" class="ruta-status" aria-live="polite"></p>
    </section>

    <section class="ruta-card ruta-reflection">
        <span class="ruta-eyebrow">MI CRECIMIENTO PERSONAL</span>
        <h2>🌟 Así voy descubriendo mis habilidades</h2>

        <div class="ruta-growth-grid">
            <div>
                <span>🎯</span>
                <strong id="rutaDoneCount">0</strong>
                <small>Misiones exploradas</small>
            </div>
            <div>
                <span>💡</span>
                <strong id="rutaNotesCount">0</strong>
                <small>Reflexiones guardadas</small>
            </div>
            <div>
                <span>🧠</span>
                <strong id="rutaRecallCount">0</strong>
                <small>Prácticas de memoria</small>
            </div>
        </div>

        <p class="ruta-kind-message">
            💚 Tus habilidades pueden crecer con la práctica, la curiosidad
            y el apoyo de otras personas. No necesitas compararte con nadie.
        </p>
    </section>

</section>

</main>

<footer class="ruta-footer">
    🐧 Penguich · Descubrir, practicar, recordar y crear.
</footer>

</div>

<dialog id="rutaGift" class="ruta-gift">
    <div class="ruta-gift-inner">
        <div class="ruta-gift-icon">🎁</div>
        <h2>¡Completaste tu aventura!</h2>
        <p>
            Exploraste cinco misiones. ¡Tu curiosidad y tu esfuerzo
            merecen una celebración!
        </p>
        <div class="ruta-gift-stars">🌟 ⭐ 🌟</div>
        <p><strong>¡Siempre hay nuevas habilidades por descubrir!</strong></p>
        <button id="rutaGiftClose" class="ruta-btn ruta-primary" type="button">
            ¡Seguir explorando!
        </button>
    </div>
</dialog>

<script>
document.addEventListener("DOMContentLoaded", () => {
"use strict";

const $ = id => document.getElementById(id);
const storageKey = "penguich-rutas-" +
    <?php echo json_encode($usuarioId); ?>;

const areas = {
    deportes: {
        emoji:"⚽", name:"Deportes y movimiento",
        description:"Coordinación, equilibrio y creatividad corporal",
        missions:[
            [
                "Descubro cómo me muevo",
                "Explora distintas formas de mover tu cuerpo.",
                "Crea tres movimientos cómodos y diferentes. Puedes realizarlos sentado o de pie.",
                "Intenta recordar los tres movimientos en el mismo orden."
            ],
            [
                "Mi desafío de precisión",
                "Experimenta con tu coordinación.",
                "Lanza una pelota suave hacia una caja o señala un objetivo desde una posición cómoda. Cambia la distancia si quieres.",
                "Recuerda qué estrategia te ayudó y explica por qué."
            ],
            [
                "Invento una secuencia",
                "Combina movimientos y creatividad.",
                "Diseña una secuencia de cuatro gestos o movimientos y enséñasela a alguien.",
                "Repite la secuencia sin consultar tus notas."
            ],
            [
                "Exploro una estrategia",
                "Prueba una manera diferente de lograr tu objetivo.",
                "Elige un reto de movimiento y prueba dos formas seguras de realizarlo.",
                "Explica qué cambiaste entre el primer y segundo intento."
            ],
            [
                "Creo mi propio juego",
                "Utiliza lo que descubriste.",
                "Inventa un juego de movimiento con reglas sencillas y alternativas para diferentes capacidades.",
                "Cuenta sus reglas de memoria a otra persona."
            ]
        ]
    },

    arte: {
        emoji:"🎨", name:"Arte e imaginación",
        description:"Creatividad, diseño y expresión visual",
        missions:[
            [
                "Mis ideas tienen color",
                "Explora distintas maneras de expresarte.",
                "Crea un dibujo usando tres formas diferentes.",
                "Observa tu creación y después describe sus formas sin mirarla."
            ],
            [
                "Un invento extraordinario",
                "Imagina algo que todavía no existe.",
                "Dibuja una herramienta que resuelva un problema cotidiano.",
                "Explica de memoria para qué sirve y cómo funciona."
            ],
            [
                "Arte con emociones",
                "Representa ideas sin usar palabras.",
                "Elige una emoción y exprésala mediante colores, figuras o texturas.",
                "Recuerda qué elementos elegiste y qué representan."
            ],
            [
                "Transformo una idea",
                "Prueba otra forma de crear.",
                "Toma un dibujo anterior y conviértelo en algo nuevo.",
                "Explica tres cambios que realizaste."
            ],
            [
                "Mi pequeña exposición",
                "Comparte una creación.",
                "Elige una de tus obras y prepara una presentación breve sobre ella.",
                "Presenta tu obra sin leer una explicación completa."
            ]
        ]
    },

    estrategia: {
        emoji:"♟️", name:"Estrategia y juegos",
        description:"Planificación, patrones y decisiones",
        missions:[
            [
                "Detective de patrones",
                "Encuentra relaciones entre objetos.",
                "Crea una secuencia con figuras o colores y descubre qué elemento sigue.",
                "Intenta reproducir el patrón sin mirarlo."
            ],
            [
                "Mi primera estrategia",
                "Planifica antes de actuar.",
                "Construye una torre con objetos seguros. Piensa antes cómo evitar que caiga.",
                "Explica los pasos que planeaste."
            ],
            [
                "Dos caminos posibles",
                "Descubre que existen diferentes soluciones.",
                "Inventa dos maneras de resolver un pequeño problema cotidiano.",
                "Recuerda una ventaja de cada solución."
            ],
            [
                "El reto del constructor",
                "Prueba y mejora una idea.",
                "Construye un puente con papel y prueba si sostiene un objeto ligero.",
                "Explica qué funcionó y qué cambiarías."
            ],
            [
                "Invento un juego",
                "Combina reglas, decisiones y creatividad.",
                "Diseña un juego sencillo con objetivo, reglas y turnos.",
                "Enseña las reglas sin consultar tu explicación."
            ]
        ]
    },

    musica: {
        emoji:"🎵", name:"Música y ritmo",
        description:"Ritmo, escucha y memoria auditiva",
        missions:[
            [
                "Exploro sonidos",
                "Descubre sonidos diferentes.",
                "Busca tres objetos seguros que produzcan sonidos suaves.",
                "Recuerda el orden en que escuchaste los sonidos."
            ],
            [
                "Mi ritmo especial",
                "Crea secuencias musicales.",
                "Inventa una secuencia de cuatro palmadas o sonidos.",
                "Repite tu secuencia sin mirar ninguna pista."
            ],
            [
                "Música y emociones",
                "Relaciona sonidos con sentimientos.",
                "Crea un ritmo que represente alegría, calma o sorpresa.",
                "Explica qué características hacen que el ritmo represente esa emoción."
            ],
            [
                "Una canción para recordar",
                "Utiliza la música como apoyo para aprender.",
                "Inventa una melodía corta para recordar tres palabras.",
                "Intenta recordar las palabras usando tu canción."
            ],
            [
                "Mi pequeña presentación",
                "Combina lo aprendido.",
                "Crea una composición breve con sonidos, palmas o voz.",
                "Presenta la composición y explica su estructura."
            ]
        ]
    },

    exploracion: {
        emoji:"🔍", name:"Exploración y ciencia",
        description:"Observación, curiosidad y experimentación",
        missions:[
            [
                "Observo como detective",
                "Aprende a identificar detalles.",
                "Elige un objeto y describe cinco características.",
                "Oculta el objeto e intenta recordar sus características."
            ],
            [
                "Mis preguntas curiosas",
                "Investiga lo que te interesa.",
                "Observa algo de la naturaleza y formula tres preguntas.",
                "Recuerda tus preguntas sin consultar lo escrito."
            ],
            [
                "Mi pequeña investigación",
                "Compara y descubre.",
                "Compara dos hojas, piedras u objetos seguros. Describe semejanzas y diferencias.",
                "Explica de memoria dos semejanzas y dos diferencias."
            ],
            [
                "Predigo y compruebo",
                "Aprende a contrastar ideas.",
                "Predice cuál de dos objetos seguros flotará en agua y compruébalo con un adulto.",
                "Cuenta qué predijiste y qué observaste."
            ],
            [
                "Mi descubrimiento",
                "Comparte lo que investigaste.",
                "Crea un cartel o dibujo sobre algo que descubriste.",
                "Explica el descubrimiento usando tus propias palabras."
            ]
        ]
    },

    liderazgo: {
        emoji:"🤝", name:"Liderazgo y equipo",
        description:"Colaboración, empatía y comunicación",
        missions:[
            [
                "Escuchar también es importante",
                "Practica escuchar otras ideas.",
                "Pregunta a alguien qué actividad disfruta y escucha su respuesta.",
                "Recuerda dos cosas que esa persona te contó."
            ],
            [
                "Una misión compartida",
                "Aprende a organizar tareas.",
                "Planea una actividad sencilla con otra persona y acuerden qué hará cada quien.",
                "Explica cómo se organizaron."
            ],
            [
                "Encontramos soluciones",
                "Explora formas de resolver desacuerdos.",
                "Imagina que dos personas quieren el mismo material. Propón soluciones respetuosas.",
                "Recuerda las soluciones y comenta cuál te parece más justa."
            ],
            [
                "Un juego para todos",
                "Diseña actividades inclusivas.",
                "Adapta las reglas de un juego para que puedan participar personas con diferentes habilidades.",
                "Explica las reglas de memoria."
            ],
            [
                "Mi proyecto en equipo",
                "Combina organización y colaboración.",
                "Organiza una actividad pequeña con familiares o compañeros.",
                "Cuenta qué aprendiste sobre escuchar y colaborar."
            ]
        ]
    },

    memoria: {
        emoji:"🧠", name:"Memoria y aprendizaje",
        description:"Recuperación, asociación y práctica espaciada",
        missions:[
            [
                "Descubro cómo recuerdo",
                "Prueba una actividad de memoria.",
                "Observa cinco objetos y después intenta nombrarlos sin mirar.",
                "Revisa cuáles recordaste y vuelve a intentarlo."
            ],
            [
                "Aprendo con imágenes",
                "Crea asociaciones visuales.",
                "Dibuja símbolos para representar tres ideas.",
                "Intenta recordar las ideas mirando solamente los símbolos."
            ],
            [
                "Agrupo para aprender",
                "Organiza información.",
                "Separa seis palabras u objetos en grupos que tengan algo en común.",
                "Recuerda los grupos y explica por qué los formaste."
            ],
            [
                "Explico sin mirar",
                "Practica recuperar información.",
                "Aprende tres datos pequeños y explícalos con tus propias palabras.",
                "Comprueba lo que recordaste y revisa lo que faltó."
            ],
            [
                "Recuerdo otro día",
                "Practica la memoria con intervalos.",
                "Vuelve a recordar las tres ideas de la misión anterior en otro momento o al día siguiente.",
                "Compara lo que recuerdas ahora con lo que recordabas antes."
            ]
        ]
    },

    expresion: {
        emoji:"🎭", name:"Expresión y confianza",
        description:"Narración, comunicación y expresión personal",
        missions:[
            [
                "Mis ideas importan",
                "Explora formas de comunicarte.",
                "Elige algo que te guste y explica por qué.",
                "Recuerda tres ideas principales de tu explicación."
            ],
            [
                "Historias con imaginación",
                "Construye narraciones.",
                "Inventa una historia con un personaje, un lugar y un problema.",
                "Cuenta la historia nuevamente sin leerla."
            ],
            [
                "Expreso emociones",
                "Prueba diferentes formas de expresión.",
                "Representa una emoción mediante un dibujo, gesto o pequeña actuación.",
                "Explica cómo elegiste representarla."
            ],
            [
                "Mi voz y mis ideas",
                "Practica compartir opiniones.",
                "Explica cómo mejorarías un juego que conoces.",
                "Recuerda las razones de tus propuestas."
            ],
            [
                "Mi presentación especial",
                "Comparte algo que descubriste.",
                "Prepara una pequeña presentación sobre un interés personal.",
                "Preséntala a tu ritmo usando pistas si las necesitas."
            ]
        ]
    }
};

const areaContainer = $("rutaAreas");
let selectedArea = "estrategia";
let selectedMission = 0;

let state = loadState();

function loadState() {
    try {
        const data = JSON.parse(localStorage.getItem(storageKey));
        return data && typeof data === "object" ? data : {};
    } catch {
        return {};
    }
}

function saveState() {
    try {
        localStorage.setItem(storageKey, JSON.stringify(state));
        return true;
    } catch {
        $("rutaSaveStatus").textContent =
            "No se pudo guardar en este navegador.";
        return false;
    }
}

function keyFor(area, age) {
    return area + "-" + age;
}

function getCurrentRoute() {
    const key = keyFor(state.area, state.age);
    if (!state.routes || typeof state.routes !== "object") {
        state.routes = {};
    }
    if (!state.routes[key] || !Array.isArray(state.routes[key].missions)) {
        state.routes[key] = {
            missions: Array.from({length:5}, () => ({
                done:false,
                note:"",
                recall:""
            })),
            celebrated:false
        };
    }
    return state.routes[key];
}

function renderAreas() {
    areaContainer.replaceChildren();

    Object.entries(areas).forEach(([id, area]) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "ruta-area" +
            (selectedArea === id ? " selected" : "");
        btn.setAttribute("aria-pressed", String(selectedArea === id));

        const emoji = document.createElement("span");
        emoji.className = "ruta-area-emoji";
        emoji.textContent = area.emoji;

        const title = document.createElement("strong");
        title.textContent = area.name;

        const desc = document.createElement("small");
        desc.textContent = area.description;

        btn.append(emoji, title, desc);
        btn.addEventListener("click", () => {
            selectedArea = id;
            renderAreas();
        });

        areaContainer.append(btn);
    });
}

function startRoute() {
    state.area = selectedArea;
    state.age = $("rutaEdad").value;
    getCurrentRoute();
    saveState();

    selectedMission = 0;
    $("rutaConfig").hidden = true;
    $("rutaWorkspace").hidden = false;

    renderWorkspace();
    window.scrollTo({top:0, behavior:"smooth"});
}

$("rutaStart").addEventListener("click", startRoute);

$("rutaChange").addEventListener("click", () => {
    selectedArea = state.area;
    $("rutaEdad").value = state.age;
    $("rutaConfig").hidden = false;
    $("rutaWorkspace").hidden = true;
    renderAreas();
    window.scrollTo({top:0, behavior:"smooth"});
});

function renderWorkspace() {
    const area = areas[state.area];
    const route = getCurrentRoute();
    const done = route.missions.filter(m => m.done).length;
    const percent = Math.round(done / 5 * 100);

    $("rutaTitle").textContent = area.emoji + " " + area.name;
    $("rutaSubtitle").textContent =
        area.description + " · " + state.age + " años";

    $("rutaProgressText").textContent = done + " de 5 misiones";
    $("rutaProgressPercent").textContent = percent + "%";
    $("rutaProgressFill").style.width = percent + "%";
    $("rutaProgressBar").setAttribute("aria-valuenow", done);

    $("rutaEncouragement").textContent =
        done === 0
        ? "🌱 Cada aventura comienza con un primer intento."
        : done < 5
        ? "💛 ¡Sigue explorando! Cada misión te ayuda a practicar."
        : "🌟 ¡Exploraste toda tu ruta! Puedes volver a practicar cuando quieras.";

    const list = $("rutaMissionList");
    list.replaceChildren();

    area.missions.forEach((mission, index) => {
        const item = document.createElement("button");
        item.type = "button";
        item.className = "ruta-mission-item" +
            (selectedMission === index ? " active" : "") +
            (route.missions[index].done ? " completed" : "");

        const number = document.createElement("span");
        number.className = "ruta-mission-number";
        number.textContent = route.missions[index].done
            ? "✓" : String(index + 1);

        const info = document.createElement("span");
        const name = document.createElement("strong");
        const description = document.createElement("small");

        name.textContent = mission[0];
        description.textContent = mission[1];
        info.append(name, description);
        item.append(number, info);

        item.addEventListener("click", () => {
            selectedMission = index;
            renderWorkspace();
        });

        list.append(item);
    });

    renderMission();
    renderStats();
}

function renderMission() {
    const area = areas[state.area];
    const route = getCurrentRoute();
    const mission = area.missions[selectedMission];
    const progress = route.missions[selectedMission];

    $("rutaMissionTitle").textContent = mission[0];
    $("rutaMissionDescription").textContent = mission[1];
    $("rutaMissionActivity").textContent = adaptActivity(
        mission[2], state.age
    );
    $("rutaMissionMemory").textContent = mission[3];
    $("rutaReflection").value = progress.note || "";
    $("rutaRecall").value = progress.recall || "";

    $("rutaComplete").textContent = progress.done
        ? "✓ Misión completada · Desmarcar"
        : "⭐ Completar misión";

    $("rutaSaveStatus").textContent = "";
    $("rutaRecallStatus").textContent = "";
}

function adaptActivity(text, age) {
    if (age === "4-6") {
        return text + " Puedes pedir a un adulto que te lea las instrucciones y te acompañe.";
    }
    if (age === "7-9") {
        return text + " Puedes hacerlo con ayuda o por tu cuenta, según lo necesites.";
    }
    return text + " Si quieres un desafío extra, explica por qué elegiste tu estrategia.";
}

$("rutaSaveNote").addEventListener("click", () => {
    const route = getCurrentRoute();
    route.missions[selectedMission].note =
        $("rutaReflection").value.trim().slice(0, 500);

    if (saveState()) {
        $("rutaSaveStatus").textContent = "💚 ¡Tu idea quedó guardada!";
    }
    renderStats();
});

$("rutaSaveRecall").addEventListener("click", () => {
    const route = getCurrentRoute();
    route.missions[selectedMission].recall =
        $("rutaRecall").value.trim().slice(0, 500);

    if (saveState()) {
        $("rutaRecallStatus").textContent =
            "🧠 ¡Guardaste lo que recuerdas! Puedes revisarlo más adelante.";
    }
    renderStats();
});

$("rutaComplete").addEventListener("click", () => {
    const route = getCurrentRoute();
    const mission = route.missions[selectedMission];

    mission.note = $("rutaReflection").value.trim().slice(0, 500);
    mission.recall = $("rutaRecall").value.trim().slice(0, 500);
    mission.done = !mission.done;

    const allDone = route.missions.every(m => m.done);

    if (allDone && !route.celebrated) {
        route.celebrated = true;
        saveState();
        renderWorkspace();
        $("rutaGift").showModal();
        return;
    }

    saveState();
    renderWorkspace();
});

function renderStats() {
    const route = getCurrentRoute();

    $("rutaDoneCount").textContent =
        route.missions.filter(m => m.done).length;

    $("rutaNotesCount").textContent =
        route.missions.filter(m => (m.note || "").trim()).length;

    $("rutaRecallCount").textContent =
        route.missions.filter(m => (m.recall || "").trim()).length;
}

$("rutaGiftClose").addEventListener("click", () => {
    $("rutaGift").close();
});

$("rutaVoice").addEventListener("click", () => {
    if (!("speechSynthesis" in window)) return;

    if (speechSynthesis.speaking) {
        speechSynthesis.cancel();
        $("rutaVoice").textContent = "🔊 Escuchar";
        return;
    }

    const text = $("rutaWorkspace").hidden
        ? "Elige una habilidad que quieras explorar y después selecciona tu edad."
        : $("rutaMissionTitle").textContent + ". " +
          $("rutaMissionActivity").textContent + ". " +
          $("rutaMissionMemory").textContent;

    const speech = new SpeechSynthesisUtterance(text);
    speech.lang = "es-MX";
    speech.rate = 0.85;
    speech.onend = speech.onerror = () => {
        $("rutaVoice").textContent = "🔊 Escuchar";
    };

    $("rutaVoice").textContent = "⏹ Detener";
    speechSynthesis.speak(speech);
});

window.addEventListener("pagehide", () => {
    if ("speechSynthesis" in window) speechSynthesis.cancel();
});

renderAreas();

if (state.area && areas[state.area] &&
    ["4-6","7-9","10-12"].includes(state.age)) {
    selectedArea = state.area;
    $("rutaEdad").value = state.age;
    $("rutaConfig").hidden = true;
    $("rutaWorkspace").hidden = false;
    getCurrentRoute();
    renderWorkspace();
}
});
</script>

</body>
</html>
