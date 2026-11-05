
<?php
declare(strict_types=1);

/**
 * Árbol de decisiones local de Penguich.
 * Devuelve una respuesta o null si debe consultar Gemini.
 */

function penguichNormalizar(string $texto): string
{
    $texto = mb_strtolower(trim($texto), 'UTF-8');

    $texto = strtr($texto, [
        'á' => 'a', 'é' => 'e', 'í' => 'i',
        'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
        'ñ' => 'n'
    ]);

    return $texto;
}

function penguichRespuestaLocal(
    string $mensaje,
    ?string $tema = null
): ?string {

    $texto = penguichNormalizar($mensaje);

    $temas = [
        'deportes' => [
            '⚽ ¡Vamos a movernos! Crea un recorrido con tres objetos y camina alrededor de ellos. ¿Cómo lo harías más divertido?',
            '🏀 ¡Reto de puntería! Lanza una pelota suave hacia una caja. Prueba desde distintas distancias.'
        ],
        'arte' => [
            '🎨 ¡Hora de imaginar! Dibuja un animal fantástico que combine dos animales diferentes.',
            '🌈 ¡Reto creativo! Inventa un paisaje utilizando tus colores favoritos.'
        ],
        'estrategia' => [
            '♟️ ¡Detective de patrones! Crea una secuencia de figuras y descubre cuál sigue.',
            '🧩 ¡Reto de lógica! Piensa en tres formas diferentes de resolver un problema.'
        ],
        'musica' => [
            '🎵 ¡Crea tu ritmo! Haz tres palmadas y dos golpes suaves sobre la mesa. ¿Puedes repetirlo?',
            '🥁 ¡Explora sonidos! Busca objetos seguros que produzcan sonidos diferentes.'
        ],
        'exploracion' => [
            '🔍 ¡Pequeño científico! Observa una hoja y descubre cinco detalles interesantes.',
            '🌱 ¡Explora la naturaleza! Observa una planta y descubre qué necesita para crecer.'
        ],
        'liderazgo' => [
            '🤝 ¡Misión en equipo! Inventa un juego donde todas las personas puedan participar.',
            '💛 ¡Reto de empatía! Piensa cómo ayudarías a un compañero que se siente excluido.'
        ],
        'memoria' => [
            '🧠 ¡Memoria mágica! Observa cinco objetos, cúbrelos e intenta recordar sus nombres.',
            '📚 ¡Aprende jugando! Explica con tus propias palabras algo que descubriste hoy.'
        ],
        'expresion' => [
            '🎭 ¡Reto de actuación! Representa una emoción sin hablar y pide que la adivinen.',
            '🎤 ¡Tu voz importa! Cuenta una historia corta sobre algo que te guste.'
        ]
    ];

    if ($tema !== null && isset($temas[$tema])) {
        return $temas[$tema][array_rand($temas[$tema])];
    }

    $reglas = [
        [
            'patron' => '/^(hola+|holi+|buenos dias|buenas tardes|buenas noches)[!?. ]*$/u',
            'respuesta' => '🐧 ¡Hola! Soy Penguich. 💚 ¿Qué te gustaría descubrir o aprender hoy?'
        ],
        [
            'patron' => '/^(gracias|muchas gracias)[!?. ]*$/u',
            'respuesta' => '💚 ¡Con mucho gusto! Me encanta aprender contigo.'
        ],
        [
            'patron' => '/^(adios|hasta luego|nos vemos)[!?. ]*$/u',
            'respuesta' => '🐧 ¡Hasta pronto! Recuerda que cada pequeño avance cuenta. 🌟'
        ],
        [
            'patron' => '/^(como te llamas|quien eres)[?!. ]*$/u',
            'respuesta' => '🐧 ¡Soy Penguich! Tu compañero para descubrir, imaginar y aprender a tu propio ritmo.'
        ],
        [
            'patron' => '/^(que puedes hacer|en que me puedes ayudar)[?!. ]*$/u',
            'respuesta' => '🌈 Podemos aprender matemáticas, explorar la ciencia, inventar historias y practicar habilidades. ¿Qué te interesa?'
        ],
        [
            'patron' => '/^(estoy triste|me siento triste)[!?. ]*$/u',
            'respuesta' => '💛 Siento que estés triste. ¿Quieres contarme qué pasó? También puedes hablar con un adulto de confianza.'
        ],
        [
            'patron' => '/^(estoy feliz|me siento feliz)[!?. ]*$/u',
            'respuesta' => '🎉 ¡Qué alegría! ¿Qué ocurrió para que te sintieras así?'
        ],
        [
            'patron' => '/^(estoy nervioso|estoy nerviosa|tengo nervios)[!?. ]*$/u',
            'respuesta' => '🌿 Podemos respirar suavemente y hacer una pausa. ¿Quieres contarme qué te preocupa?'
        ],
        [
            'patron' => '/^(no puedo|no me sale|soy malo|soy mala)[!?. ]*$/u',
            'respuesta' => '💛 Que algo sea difícil no significa que no puedas aprenderlo. Podemos probar otra estrategia. ¿Qué estás intentando hacer?'
        ],
        [
            'patron' => '/^(quiero jugar|vamos a jugar)[!?. ]*$/u',
            'respuesta' => '🎮 ¡Me encanta! Podemos jugar con palabras, memoria, lógica o matemáticas. ¿Cuál eliges?'
        ],
        [
            'patron' => '/^(quiero dibujar|quiero pintar)[!?. ]*$/u',
            'respuesta' => '🎨 ¡Vamos a crear! Dibuja una isla fantástica donde vivan animales imaginarios.'
        ]
    ];

    foreach ($reglas as $regla) {
        if (preg_match($regla['patron'], $texto)) {
            return $regla['respuesta'];
        }
    }

    return null;
}
