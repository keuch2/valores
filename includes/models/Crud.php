<?php
/**
 * Crud — modelo genérico para las entidades de contenido simples.
 *
 * Cada entidad se describe con: nombre de tabla, columnas editables (whitelist),
 * y opciones (orden por defecto, si genera slug, etc.). El SQL sólo usa nombres
 * de columna que provienen de esa whitelist — nunca de la entrada del usuario.
 */

declare(strict_types=1);

final class Crud
{
    /**
     * Definición de cada entidad administrable.
     * 'campos'   => columnas que el formulario puede escribir.
     * 'slug_de'  => (opcional) columna de la que derivar slug único.
     * 'orden'    => ORDER BY del listado.
     * 'tiene_orden' => soporta reordenamiento por campo 'orden'.
     */
    private const ENTIDADES = [
        'noticias' => [
            'campos' => ['titulo','resumen','contenido','imagen_destacada_id','categoria','estado','visible_front','fecha_publicacion','autor','seo_title','seo_description'],
            'slug_de' => 'titulo',
            'orden' => 'created_at DESC',
        ],
        'oportunidades' => [
            'campos' => ['tipo','instrumento','emisor','tasa','plazo','moneda','calificacion','monto_minimo','estado','destacado','orden'],
            'orden' => 'orden ASC, id DESC',
            'tiene_orden' => true,
        ],
        'servicios' => [
            'campos' => ['titulo','icono','descripcion_corta','contenido','imagen_id','orden','activo'],
            'slug_de' => 'titulo',
            'orden' => 'orden ASC, id ASC',
            'tiene_orden' => true,
        ],
        'ejecutivos' => [
            'campos' => ['nombre','cargo','bio','foto_id','email','telefono','whatsapp','linkedin','orden','activo'],
            'orden' => 'orden ASC, id ASC',
            'tiene_orden' => true,
        ],
        'faqs' => [
            'campos' => ['pregunta','respuesta','categoria','orden','activo'],
            'orden' => 'categoria ASC, orden ASC',
            'tiene_orden' => true,
        ],
        'glosario' => [
            'campos' => ['termino','definicion','orden'],
            'orden' => 'termino ASC',
            'tiene_orden' => true,
        ],
        'academy_recursos' => [
            'campos' => ['tipo','titulo','descripcion','video_url','media_id','fecha','orden','activo'],
            'orden' => 'orden ASC, id DESC',
            'tiene_orden' => true,
        ],
    ];

    public static function existe(string $entidad): bool
    {
        return isset(self::ENTIDADES[$entidad]);
    }

    public static function def(string $entidad): array
    {
        if (!self::existe($entidad)) {
            throw new InvalidArgumentException('Entidad desconocida: ' . $entidad);
        }
        return self::ENTIDADES[$entidad];
    }

    public static function listar(string $entidad): array
    {
        $d = self::def($entidad);
        // $entidad y $d['orden'] provienen de la constante, no del usuario.
        return db()->query("SELECT * FROM {$entidad} ORDER BY {$d['orden']}")->fetchAll();
    }

    public static function buscar(string $entidad, int $id): ?array
    {
        self::def($entidad);
        $stmt = db()->prepare("SELECT * FROM {$entidad} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Crea un registro tomando sólo las columnas de la whitelist desde $datos.
     * @return int id insertado
     */
    public static function crear(string $entidad, array $datos): int
    {
        $d = self::def($entidad);
        $cols = [];
        $params = [];

        foreach ($d['campos'] as $campo) {
            if (array_key_exists($campo, $datos)) {
                $cols[$campo] = $datos[$campo];
            }
        }

        // Slug único si corresponde.
        if (!empty($d['slug_de']) && !empty($cols[$d['slug_de']])) {
            $cols['slug'] = slug_unico((string) $cols[$d['slug_de']], $entidad);
        }

        $nombres = array_keys($cols);
        $placeholders = array_map(fn($c) => ':' . $c, $nombres);
        foreach ($cols as $c => $v) { $params[':' . $c] = self::normalizar($v); }

        $sql = "INSERT INTO {$entidad} (" . implode(',', $nombres) . ") VALUES (" . implode(',', $placeholders) . ")";
        db()->prepare($sql)->execute($params);
        return (int) db()->lastInsertId();
    }

    /** Actualiza un registro (whitelist de columnas). */
    public static function actualizar(string $entidad, int $id, array $datos): void
    {
        $d = self::def($entidad);
        $sets = [];
        $params = [':id' => $id];

        foreach ($d['campos'] as $campo) {
            if (array_key_exists($campo, $datos)) {
                $sets[] = "{$campo} = :{$campo}";
                $params[':' . $campo] = self::normalizar($datos[$campo]);
            }
        }

        if (!empty($d['slug_de']) && !empty($datos[$d['slug_de']])) {
            $sets[] = 'slug = :slug';
            $params[':slug'] = slug_unico((string) $datos[$d['slug_de']], $entidad, $id);
        }

        if (!$sets) { return; }
        $sql = "UPDATE {$entidad} SET " . implode(',', $sets) . " WHERE id = :id";
        db()->prepare($sql)->execute($params);
    }

    public static function eliminar(string $entidad, int $id): void
    {
        self::def($entidad);
        db()->prepare("DELETE FROM {$entidad} WHERE id = :id")->execute([':id' => $id]);
    }

    /** Cambia el campo 'orden' de un registro (para reordenamiento). */
    public static function setOrden(string $entidad, int $id, int $orden): void
    {
        $d = self::def($entidad);
        if (empty($d['tiene_orden'])) { return; }
        db()->prepare("UPDATE {$entidad} SET orden = :o WHERE id = :id")
            ->execute([':o' => $orden, ':id' => $id]);
    }

    /** Convierte '' en NULL para columnas nullable; deja el resto tal cual. */
    private static function normalizar($v)
    {
        if (is_string($v) && trim($v) === '') { return null; }
        return $v;
    }
}
