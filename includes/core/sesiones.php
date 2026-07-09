<?php
/**
 * sesiones.php — Arranque de sesión PHP endurecida.
 *
 * Cookies HttpOnly / SameSite=Strict / Secure (bajo HTTPS), regeneración de ID
 * en momentos clave y expiración por inactividad.
 */

declare(strict_types=1);

/**
 * Inicia la sesión segura si aún no está activa. Llamar al principio de cada
 * request que use sesión (admin y formularios públicos con CSRF).
 */
function sesion_iniciar(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    // Detección de HTTPS (incluye el caso de proxy inverso).
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,              // cookie de sesión (se borra al cerrar el navegador)
        'path'     => BASE_URL,
        'httponly' => true,           // inaccesible desde JS
        'secure'   => $https,         // sólo por HTTPS en producción
        'samesite' => 'Strict',       // mitiga CSRF a nivel cookie
    ]);

    session_start();

    // Expiración por inactividad.
    $ahora = time();
    if (isset($_SESSION['ultima_actividad'])
        && ($ahora - (int) $_SESSION['ultima_actividad']) > SESSION_IDLE_TIMEOUT) {
        sesion_destruir();
        session_start();
    }
    $_SESSION['ultima_actividad'] = $ahora;

    // Regeneración periódica del ID para acotar el robo de sesión.
    if (!isset($_SESSION['creada'])) {
        $_SESSION['creada'] = $ahora;
    } elseif ($ahora - (int) $_SESSION['creada'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['creada'] = $ahora;
    }
}

/**
 * Regenera el ID de sesión (llamar tras un login exitoso).
 */
function sesion_regenerar(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
        $_SESSION['creada'] = time();
    }
}

/**
 * Destruye por completo la sesión y su cookie.
 */
function sesion_destruir(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires'  => time() - 42000,
            'path'     => $p['path'],
            'domain'   => $p['domain'],
            'secure'   => $p['secure'],
            'httponly' => $p['httponly'],
            'samesite' => 'Strict',
        ]);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Guarda un mensaje "flash" para mostrar tras un redirect (una sola vez).
 */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['_flash'][$tipo][] = $mensaje;
}

/**
 * Recupera y limpia los mensajes flash acumulados.
 */
function flash_obtener(): array
{
    $flashes = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flashes;
}
