<?php
/**
 * config.sample.php — Plantilla de configuración (versionable).
 * Copiar a config.php y completar con los valores reales del entorno.
 */

declare(strict_types=1);

define('APP_ENV', 'prod');                 // 'dev' | 'prod'
define('BASE_URL', '/valores-app/');       // subcarpeta del webroot, con barras
define('APP_ROOT', dirname(__DIR__, 2));

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'valores_cms');
define('DB_USER', 'usuario_bd');
define('DB_PASS', 'contraseña_bd');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME', 'valores_admin');
define('SESSION_IDLE_TIMEOUT', 1800);
define('LOGIN_MAX_INTENTOS', 5);
define('LOGIN_BLOQUEO_MINUTOS', 15);

define('UPLOAD_DIR_MEDIA', APP_ROOT . '/uploads/media');
define('UPLOAD_DIR_SOLICITUDES', APP_ROOT . '/uploads/solicitudes');
define('UPLOAD_MAX_BYTES', 10 * 1024 * 1024);

// Clave de cifrado KYC (32 bytes en hex). Generar con:
//   php -r 'echo bin2hex(random_bytes(32));'
define('KYC_ENCRYPTION_KEY', 'CAMBIAR_por_64_caracteres_hex_generados_aleatoriamente_aqui_0000');

date_default_timezone_set('America/Asuncion');
