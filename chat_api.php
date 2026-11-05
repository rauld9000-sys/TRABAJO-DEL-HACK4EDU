<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function responderChat(
    bool $ok,
    string $mensaje,
    string $origen = 'sistema',
    int $codigo = 200
): void {
    http_response_code($codigo);

    echo json_encode([
        'ok' => $ok,
        'respuesta' => $mensaje,
        'origen' => $origen
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderChat(false, 'Método no permitido.', 'error', 405);
}

if (!isset($_SESSION['usuario'])) {
    responderChat(
        false,
        'Tu sesión terminó. Inicia sesión nuevamente.',
        'error',
        401
    );
}

$origen = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origen !== '') {
    $origenHost = parse_url($origen, PHP_URL_HOST);
    $hostActual = $_SERVER['HTTP_HOST'] ?? '';
    $hostActual = parse_url('http://' . $hostActual, PHP_URL_HOST);

    if ($origenHost !== $hostActual) {
        responderChat(false, 'Origen no autorizado.', 'error', 403);
    }
}

if (!str_contains(
    strtolower($_SERVER['CONTENT_TYPE'] ?? ''),
    'application/json'
)) {
    responderChat(false, 'Formato no válido.', 'error', 415);
}

$entrada = file_get_contents('php://input', false, null, 0, 12000);
$datos = json_decode($entrada ?: '', true);

if (!is_array($datos)) {
    responderChat(false, 'Solicitud incorrecta.', 'error', 400);
}

$mensaje = $datos['mensaje'] ?? '';
$tema = $datos['tema'] ?? null;

if (!is_string($mensaje)) {
    responderChat(false, 'Mensaje incorrecto.', 'error', 400);
}

$mensaje = trim($mensaje);

if ($mensaje === '' || mb_strlen($mensaje, 'UTF-8') > 500) {
    responderChat(
        false,
        'Escribe un mensaje de hasta 500 caracteres.',
        'error',
        400
    );
}

if (!is_string($tema)) {
    $tema = null;
}

$temas = [
    'deportes', 'arte', 'estrategia', 'musica',
    'exploracion', 'liderazgo', 'memoria', 'expresion'
];

if (!in_array($tema, $temas, true)) {
    $tema = null;
}

// Límite básico para evitar consultas excesivas.
$ahora = time();

$_SESSION['chat_tiempos'] = array_values(array_filter(
    $_SESSION['chat_tiempos'] ?? [],
    fn($t) => is_int($t) && $t > $ahora - 60
));

if (count($_SESSION['chat_tiempos']) >= 20) {
    responderChat(
        false,
        '🐧 Vamos a descansar un momento antes de continuar.',
        'limite',
        429
    );
}

$_SESSION['chat_tiempos'][] = $ahora;

require_once __DIR__ . '/chat_tree.php';
require_once __DIR__ . '/ai_fallback.php';

// Primero responder localmente.
$respuesta = penguichRespuestaLocal($mensaje, $tema);

if ($respuesta !== null) {
    responderChat(true, $respuesta, 'local');
}

// Si no hay respuesta local, consultar Gemini.
try {
    $historial = $_SESSION['penguich_historial'] ?? [];

    if (!is_array($historial)) {
        $historial = [];
    }

    $respuesta = penguichGemini($mensaje, $historial);

    $historial[] = [
        'role' => 'user',
        'text' => $mensaje
    ];

    $historial[] = [
        'role' => 'model',
        'text' => $respuesta
    ];

    $_SESSION['penguich_historial'] = array_slice($historial, -8);

    responderChat(true, $respuesta, 'gemini');

} catch (Throwable $e) {

    // El error técnico se guarda en el servidor.
    error_log('Penguich Gemini: ' . $e->getMessage());

    responderChat(
        false,
        '🐧 No pude conectarme con mis respuestas inteligentes. '
        . 'Podemos intentarlo nuevamente en un momento. 💚',
        'error',
        503
    );
}
