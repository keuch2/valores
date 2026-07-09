<?php
/**
 * bootstrap.php — Punto de entrada común de la aplicación.
 *
 * Carga configuración, núcleo de seguridad y utilidades, y arranca la sesión.
 * Tanto index.php (público) como admin/index.php hacen un único require de este archivo.
 */

declare(strict_types=1);

// --- Configuración (define APP_ENV, BASE_URL, credenciales, etc.) ---
$config = __DIR__ . '/../config/config.php';
if (!is_file($config)) {
    http_response_code(500);
    exit('Falta includes/config/config.php. Copialo desde config.sample.php y completá los valores.');
}
require $config;

// --- Manejo de errores según entorno ---
if (APP_ENV === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// --- Núcleo (orden importa: db y helpers antes que auth) ---
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/sesiones.php';
require __DIR__ . '/csrf.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/cripto.php';

// Autoload de modelos (clases en includes/models/<Clase>.php).
spl_autoload_register(static function (string $clase): void {
    $archivo = APP_ROOT . '/includes/models/' . $clase . '.php';
    if (is_file($archivo)) {
        require $archivo;
    }
});

// --- Sesión segura activa para toda la request ---
sesion_iniciar();
