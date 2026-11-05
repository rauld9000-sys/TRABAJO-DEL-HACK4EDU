
<?php
declare(strict_types=1);

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function responder(
    bool $ok,
    string $respuesta,
    string $origen = 'sistema',
    int $codigo = 200
): never {
    http_response_code($codigo);

    echo json_encode([
        'ok' => $ok,
        'respuesta' => $respuesta,
        'origen' => $origen
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método no permitido.', 'error', 405);
}

if (!isset($_SESSION['usuario'])) {
    responder(
        false,
        'Tu sesión terminó. Inicia sesión nuevamente.',
        'error',
        401
    );
}

// Comprobar origen cuando el navegador lo proporciona.
$origen = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origen !== '') {
    $hostOrigen = parse_url($origen, PHP_URL_HOST);
    $hostActual = explode(':', $_SERVER['HTTP_HOST'] ?? '')[0];

    if ($hostOrigen !== $hostActual) {
        responder(false, 'Origen no autorizado.', 'error', 403);
    }
}

$tipo = $_SERVER['CONTENT_TYPE'] ?? '';

if (!str_contains(strtolower($tipo), 'application/json')) {
    responder(false, 'Se requiere JSON.', 'error', 415);
}

$entrada = file_get_contents('php://input', false, null, 0, 12000);
$datos = json_decode($entrada ?: '', true);

if (!is_array($datos)) {
    responder(false, 'Solicitud incorrecta.', 'error', 400);
}

$mensaje = $datos['mensaje'] ?? '';
$tema = $datos['tema'] ?? null;

if (!is_string($mensaje)) {
    responder(false, 'Mensaje no válido.', 'error', 400);
}

$mensaje = trim($mensaje);

if ($mensaje === '' || mb_strlen($mensaje, 'UTF-8') > 500) {
    responder(
        false,
        'Escribe un mensaje de hasta 500 caracteres.',
        'error',
        400
    );
}

if (!is_string($tema)) {
    $tema = null;
}

$temasPermitidos = [
    'deportes', 'arte', 'estrategia', 'musica',
    'exploracion', 'liderazgo', 'memoria', 'expresion'
];

if (!in_array($tema, $temasPermitidos, true)) {
    $tema = null;
}

// Límite básico de solicitudes por sesión.
$ahora = time();

if (!isset($_SESSION['chat_limite'])
    || !is_array($_SESSION['chat_limite'])) {
    $_SESSION['chat_limite'] = [];
}

$_SESSION['chat_limite'] = array_values(array_filter(
    $_SESSION['chat_limite'],
    static fn($t) => is_int($t) && $t > $ahora - 60
));

if (count($_SESSION['chat_limite']) >= 20) {
    responder(
        false,
        '🐧 Vamos a descansar un momento antes de seguir.',
        'limite',
        429
    );
}

$_SESSION['chat_limite'][] = $ahora;

require_once __DIR__ . '/chat_tree.php';
require_once __DIR__ . '/ai_fallback.php';

// Primero se intenta responder sin consumir API.
$respuestaLocal = penguichRespuestaLocal($mensaje, $tema);

if ($respuestaLocal !== null) {
    responder(true, $respuestaLocal, 'local');
}

// Historial corto guardado temporalmente en sesión.
$historial = $_SESSION['penguich_historial'] ?? [];

if (!is_array($historial)) {
    $historial = [];
}

try {
    $respuestaIA = penguichGemini($mensaje, $historial);

    $historial[] = [
        'role' => 'user',
        'text' => $mensaje
    ];

    $historial[] = [
        'role' => 'model',
        'text' => $respuestaIA
    ];

    $_SESSION['penguich_historial'] = array_slice(
        $historial,
        -8
    );

    responder(true, $respuestaIA, 'gemini');

} catch (Throwable $e) {

    error_log('Penguich Chat: ' . $e->getMessage());

    responder(
        true,
        '🐧 Ahora mismo no puedo consultar mis respuestas especiales, '
        . 'pero podemos seguir aprendiendo. '
        . '¿Quieres explorar arte, memoria, música o ciencia?',
        'respaldo'
    );
}
