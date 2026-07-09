<?php
/**
 * db.php — Conexión PDO central (singleton).
 *
 * Todo el acceso a la base de datos pasa por aquí, siempre con sentencias
 * preparadas. Nunca concatenar valores en el SQL.
 */

declare(strict_types=1);

/**
 * Devuelve la instancia PDO compartida, creándola la primera vez.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // errores como excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // arrays asociativos
        PDO::ATTR_EMULATE_PREPARES   => false,                    // preparados reales
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
    } catch (PDOException $e) {
        // No exponer credenciales ni detalles internos al cliente.
        error_log('[DB] Fallo de conexión: ' . $e->getMessage());
        http_response_code(500);
        if (defined('APP_ENV') && APP_ENV === 'dev') {
            exit('Error de conexión a la base de datos: ' . $e->getMessage());
        }
        exit('Error de conexión a la base de datos.');
    }

    return $pdo;
}
