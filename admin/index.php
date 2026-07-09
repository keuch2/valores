<?php
/**
 * admin/index.php — Router / front-controller del panel de administración.
 *
 * Mapea ?r=recurso/accion a un controlador en includes/controllers/admin/.
 * Apache no reescribe /admin (ver .htaccess raíz), así que el panel usa
 * URLs con query string: /valores-app/admin/?r=usuarios/crear
 */

declare(strict_types=1);

require dirname(__DIR__) . '/includes/core/bootstrap.php';

// Ruta solicitada: 'recurso/accion'. Default: dashboard.
$r = isset($_GET['r']) ? trim((string) $_GET['r'], '/') : 'dashboard';

// Sólo caracteres seguros; evita traversal y nombres raros.
if (!preg_match('#^[a-z0-9_]+(/[a-z0-9_]+)?$#', $r)) {
    http_response_code(404);
    $r = 'errores/notfound';
}

[$recurso, $accion] = array_pad(explode('/', $r, 2), 2, 'index');

// El login y el logout son públicos; todo lo demás exige sesión admin.
$publicos = ['auth/login', 'auth/logout'];
if (!in_array("$recurso/$accion", $publicos, true)) {
    auth_exigir();
}

// Resolver el controlador. Cada controlador define una función manejar_{accion}().
$controlador = dirname(__DIR__) . "/includes/controllers/admin/{$recurso}.php";

if (!is_file($controlador)) {
    http_response_code(404);
    require dirname(__DIR__) . '/includes/views/admin/404.php';
    exit;
}

require $controlador;

$fn = 'accion_' . $accion;
if (!function_exists($fn)) {
    http_response_code(404);
    require dirname(__DIR__) . '/includes/views/admin/404.php';
    exit;
}

$fn();
