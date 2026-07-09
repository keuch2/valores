<?php
/**
 * Publico — consultas de sólo lectura para el sitio público.
 *
 * Devuelve únicamente contenido activo/publicado. Nunca expone borradores ni,
 * en el caso de noticias, filas con visible_front = 0 (Noticias está oculto
 * en el front por ahora — decisión del cliente).
 */

declare(strict_types=1);

final class Publico
{
    /** Servicios activos, ordenados. */
    public static function servicios(): array
    {
        return db()->query(
            "SELECT * FROM servicios WHERE activo = 1 ORDER BY orden ASC, id ASC"
        )->fetchAll();
    }

    public static function servicioPorSlug(string $slug): ?array
    {
        $stmt = db()->prepare("SELECT * FROM servicios WHERE slug = :s AND activo = 1");
        $stmt->execute([':s' => $slug]);
        return $stmt->fetch() ?: null;
    }

    /** Oportunidades disponibles, opcionalmente filtradas por tipo. */
    public static function oportunidades(?string $tipo = null): array
    {
        $sql = "SELECT * FROM oportunidades WHERE estado = 'disponible'";
        $p = [];
        if ($tipo && in_array($tipo, ['bono','cda','accion','inter'], true)) {
            $sql .= " AND tipo = :t"; $p[':t'] = $tipo;
        }
        $sql .= " ORDER BY destacado DESC, orden ASC, id DESC";
        $stmt = db()->prepare($sql);
        $stmt->execute($p);
        return $stmt->fetchAll();
    }

    /** Ejecutivos activos. */
    public static function ejecutivos(): array
    {
        return db()->query(
            "SELECT * FROM ejecutivos WHERE activo = 1 ORDER BY orden ASC, id ASC"
        )->fetchAll();
    }

    /** FAQs activas, opcionalmente por categoría. */
    public static function faqs(?string $categoria = null): array
    {
        $sql = "SELECT * FROM faqs WHERE activo = 1";
        $p = [];
        if ($categoria) { $sql .= " AND categoria = :c"; $p[':c'] = $categoria; }
        $sql .= " ORDER BY orden ASC, id ASC";
        $stmt = db()->prepare($sql);
        $stmt->execute($p);
        return $stmt->fetchAll();
    }

    /** Glosario completo, ordenado por término. */
    public static function glosario(): array
    {
        return db()->query("SELECT * FROM glosario ORDER BY termino ASC")->fetchAll();
    }

    /** Recursos de Academy activos. */
    public static function academy(?string $tipo = null): array
    {
        $sql = "SELECT * FROM academy_recursos WHERE activo = 1";
        $p = [];
        if ($tipo && in_array($tipo, ['articulo','webinar'], true)) {
            $sql .= " AND tipo = :t"; $p[':t'] = $tipo;
        }
        $sql .= " ORDER BY orden ASC, fecha DESC, id DESC";
        $stmt = db()->prepare($sql);
        $stmt->execute($p);
        return $stmt->fetchAll();
    }

    /**
     * Noticias PUBLICADAS y visibles en el front.
     * Mientras la sección esté oculta, visible_front = 0 => no devuelve nada.
     */
    public static function noticias(int $limite = 20, int $offset = 0): array
    {
        $stmt = db()->prepare(
            "SELECT * FROM noticias
             WHERE estado = 'publicado' AND visible_front = 1
             ORDER BY fecha_publicacion DESC, id DESC
             LIMIT :lim OFFSET :off"
        );
        $stmt->bindValue(':lim', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function noticiaPorSlug(string $slug): ?array
    {
        $stmt = db()->prepare(
            "SELECT * FROM noticias WHERE slug = :s AND estado = 'publicado' AND visible_front = 1"
        );
        $stmt->execute([':s' => $slug]);
        return $stmt->fetch() ?: null;
    }

    /** Tasas del simulador desde configuración (con fallback a los valores base). */
    public static function tasasSimulador(): array
    {
        return [
            'bono'   => (float) Config::get('tasa_bono', '8.5'),
            'cda'    => (float) Config::get('tasa_cda', '6.0'),
            'accion' => (float) Config::get('tasa_accion', '12.0'),
            'inter'  => (float) Config::get('tasa_inter', '9.5'),
            'letra'  => (float) Config::get('tasa_letra', '5.5'),
        ];
    }
}
