<?php
declare(strict_types=1);

function penguichNormalizar(string $texto): string
{
    $texto = mb_strtolower(trim($texto), 'UTF-8');

    return strtr($texto, [
        'á' => 'a', 'é' => 'e', 'í' => 'i',
        'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
        'ñ' => 'n'
    ]);
}

function penguichRespuestaLocal(
    string $mensaje,
    ?string $tema = null
): ?string {

    $texto = penguichNormalizar($mensaje);

    $actividades = [
        'deportes' => '⚽ ¡Vamos a movernos! Inventa un recorrido con tres objetos y descubre diferentes formas de completarlo.',
        'arte' => '🎨 ¡Hora de crear! Dibuja un animal fantástico y piensa qué habilidades especiales tendría.',
        'estrategia' => '♟️ ¡Reto de lógica! Inventa una secuencia de colores y descubre cuál sigue.',
        'musica' => '🎵 ¡Crea tu ritmo! Haz tres palmadas y dos golpes suaves sobre una mesa.',
        'exploracion' => '🔍 ¡Pequeño científico! Observa una hoja y descubre cinco detalles interesantes.',
        'liderazgo' => '🤝 ¡Misión en equipo! Inventa un juego donde todas las personas puedan participar.',
        'memoria' => '🧠 ¡Memoria mágica! Observa cinco objetos, ocúltalos e intenta recordar sus nombres.',
        'expresion' => '🎭 ¡Reto de expresión! Representa una emoción sin hablar y pide que la adivinen.'
    ];

    if ($tema !== null && isset($actividades[$tema])) {
        return $actividades[$tema];
    }

    $reglas = [
        '/^(hola|holi|buenos dias|buenas tardes)[!?. ]*$/u'
            => '🐧 ¡Hola! Soy Penguich. ¿Qué te gustaría aprender hoy? 💚',

        '/^(como te llamas|quien eres)[?!. ]*$/u'
            => '🐧 ¡Soy Penguich! Tu compañero para aprender, imaginar y descubrir.',

        '/^(gracias|muchas gracias)[!?. ]*$/u'
            => '💚 ¡De nada! Me encanta aprender contigo.',

        '/^(adios|hasta luego|nos vemos)[!?. ]*$/u'
            => '🐧 ¡Hasta pronto! Recuerda que cada pequeño avance cuenta.',

        '/^(quiero jugar|vamos a jugar)[!?. ]*$/u'
            => '🎮 ¡Vamos a jugar! Podemos explorar memoria, lógica, arte o matemáticas.',

        '/^(estoy triste|me siento triste)[!?. ]*$/u'
            => '💛 Siento que estés triste. ¿Quieres contarme qué pasó? También puedes hablar con un adulto de confianza.',

        '/^(estoy feliz|me siento feliz)[!?. ]*$/u'
            => '🌟 ¡Qué alegría! ¿Qué pasó para que te sintieras así?',

        '/^(no puedo|no me sale)[!?. ]*$/u'
            => '💚 Podemos intentarlo de otra manera. Aprender requiere práctica. ¿Qué estás intentando hacer?'
    ];

    foreach ($reglas as $patron => $respuesta) {
        if (preg_match($patron, $texto)) {
            return $respuesta;
        }
    }

    // No inventar respuestas: enviar a Gemini.
    return null;
}
