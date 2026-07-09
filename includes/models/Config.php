<?php
/**
 * Config — acceso a la tabla configuracion (clave/valor por grupos).
 * Cachea en memoria para no repetir consultas dentro de una request.
 */

declare(strict_types=1);

final class Config
{
    private static array $cache = [];
    private static bool $cargada = false;

    private static function cargar(): void
    {
        if (self::$cargada) { return; }
        foreach (db()->query('SELECT clave, valor FROM configuracion') as $row) {
            self::$cache[$row['clave']] = $row['valor'];
        }
        self::$cargada = true;
    }

    /** Devuelve un valor por clave (o $default). */
    public static function get(string $clave, ?string $default = null): ?string
    {
        self::cargar();
        return self::$cache[$clave] ?? $default;
    }

    /** Todas las claves de un grupo, ordenadas. */
    public static function grupo(string $grupo): array
    {
        $stmt = db()->prepare('SELECT clave, valor FROM configuracion WHERE grupo = :g ORDER BY clave');
        $stmt->execute([':g' => $grupo]);
        return $stmt->fetchAll();
    }

    /** Guarda (upsert) un conjunto clave=>valor. */
    public static function guardar(array $pares): void
    {
        $stmt = db()->prepare(
            'UPDATE configuracion SET valor = :v WHERE clave = :k'
        );
        foreach ($pares as $clave => $valor) {
            $stmt->execute([':v' => (string) $valor, ':k' => $clave]);
        }
        self::$cargada = false; // invalidar cache
    }
}
