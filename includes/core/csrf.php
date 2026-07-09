<?php
/**
 * csrf.php — Protección CSRF para todos los formularios (admin y público).
 *
 * Token por sesión, comparado con hash_equals(). Requiere sesión activa.
 */

declare(strict_types=1);

/**
 * Devuelve el token CSRF de la sesión, generándolo si no existe.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/**
 * Campo oculto listo para insertar dentro de un <form>.
 */
function csrf_campo(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/**
 * Verifica el token recibido por POST. Devuelve true si es válido.
 */
function csrf_verificar(): bool
{
    $enviado = $_POST['_csrf'] ?? '';
    $valido  = $_SESSION['_csrf'] ?? '';
    return is_string($enviado)
        && $valido !== ''
        && hash_equals($valido, $enviado);
}

/**
 * Exige un POST con CSRF válido; si falla, responde 403 y corta.
 * Usar al inicio de cualquier handler que procese un formulario.
 */
function csrf_exigir(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verificar()) {
        http_response_code(403);
        exit('Solicitud inválida (token CSRF). Recargá la página e intentá de nuevo.');
    }
}
