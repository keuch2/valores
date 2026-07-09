<?php
/**
 * Modelo Media — biblioteca de medios (imágenes, video, PDF, otros).
 *
 * Subida con whitelist de extensión + MIME real (finfo), renombrado seguro,
 * y soporte de video embebido por URL externa (YouTube/Vimeo) sin subir archivo.
 */

declare(strict_types=1);

final class Media
{
    /** Extensiones permitidas -> tipo lógico. */
    private const EXT_PERMITIDAS = [
        'jpg' => 'imagen', 'jpeg' => 'imagen', 'png' => 'imagen', 'gif' => 'imagen',
        'webp' => 'imagen', 'avif' => 'imagen', 'svg' => 'imagen',
        'mp4' => 'video', 'webm' => 'video',
        'pdf' => 'pdf',
    ];

    /** MIME reales aceptados (defensa contra extensión falsificada). */
    private const MIME_PERMITIDOS = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif', 'image/svg+xml',
        'video/mp4', 'video/webm',
        'application/pdf',
    ];

    /** Lista medios, opcionalmente filtrados por tipo y/o búsqueda por nombre. */
    public static function listar(?string $tipo = null, string $q = ''): array
    {
        $sql = 'SELECT * FROM media WHERE 1=1';
        $p = [];
        if ($tipo && isset(array_flip(self::EXT_PERMITIDAS)[$tipo]) || in_array($tipo, ['imagen','video','pdf','otro'], true)) {
            $sql .= ' AND tipo = :tipo';
            $p[':tipo'] = $tipo;
        }
        if ($q !== '') {
            $sql .= ' AND (nombre_original LIKE :q OR alt_text LIKE :q)';
            $p[':q'] = '%' . $q . '%';
        }
        $sql .= ' ORDER BY created_at DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute($p);
        return $stmt->fetchAll();
    }

    public static function buscar(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM media WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** URL pública de un medio (o su video_url si es embebido). */
    public static function urlPublica(array $m): string
    {
        if (!empty($m['video_url'])) {
            return $m['video_url'];
        }
        // Imágenes preexistentes del tema (assets/img/…) se sirven en su ubicación;
        // el resto vive bajo uploads/media/.
        if (strncmp($m['ruta'], 'assets/', 7) === 0) {
            return url($m['ruta']);
        }
        return url('uploads/media/' . $m['ruta']);
    }

    /**
     * Procesa una subida de $_FILES['archivo'].
     * @return array{ok:bool, error?:string, id?:int}
     */
    public static function subir(array $file, int $subidoPor, ?int $maxBytes = null): array
    {
        $maxBytes = $maxBytes ?? UPLOAD_MAX_BYTES;

        if (!isset($file['error']) || is_array($file['error'])) {
            return ['ok' => false, 'error' => 'Parámetros de subida inválidos.'];
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Error en la subida (código ' . $file['error'] . ').'];
        }
        if ($file['size'] > $maxBytes) {
            return ['ok' => false, 'error' => 'El archivo supera el tamaño máximo (' . round($maxBytes / 1048576, 1) . ' MB).'];
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return ['ok' => false, 'error' => 'Origen de archivo no válido.'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!isset(self::EXT_PERMITIDAS[$ext])) {
            return ['ok' => false, 'error' => 'Extensión no permitida: .' . e($ext)];
        }

        // MIME real del contenido, no el que declara el navegador.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if (!in_array($mime, self::MIME_PERMITIDOS, true)) {
            return ['ok' => false, 'error' => 'El contenido del archivo no coincide con un tipo permitido.'];
        }

        $tipo = self::EXT_PERMITIDAS[$ext];

        // Nombre seguro: aleatorio + extensión. Nunca el nombre original en disco.
        $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $ext;
        $destino = UPLOAD_DIR_MEDIA . '/' . $nombreArchivo;

        if (!is_dir(UPLOAD_DIR_MEDIA)) {
            @mkdir(UPLOAD_DIR_MEDIA, 0755, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            return ['ok' => false, 'error' => 'No se pudo guardar el archivo.'];
        }

        // Dimensiones si es imagen rasterizada.
        $ancho = $alto = null;
        if ($tipo === 'imagen' && $mime !== 'image/svg+xml') {
            $dim = @getimagesize($destino);
            if ($dim) { $ancho = $dim[0]; $alto = $dim[1]; }
        }

        $stmt = db()->prepare(
            'INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, tamano_bytes, ancho, alto, subido_por)
             VALUES (:na, :no, :ruta, :tipo, :mime, :size, :ancho, :alto, :por)'
        );
        $stmt->execute([
            ':na' => $nombreArchivo,
            ':no' => mb_substr($file['name'], 0, 255),
            ':ruta' => $nombreArchivo,
            ':tipo' => $tipo,
            ':mime' => $mime,
            ':size' => (int) $file['size'],
            ':ancho' => $ancho,
            ':alto' => $alto,
            ':por' => $subidoPor,
        ]);
        return ['ok' => true, 'id' => (int) db()->lastInsertId()];
    }

    /**
     * Registra un video embebido por URL externa (sin subir archivo).
     * @return array{ok:bool, error?:string, id?:int}
     */
    public static function agregarVideoUrl(string $urlVideo, string $titulo, int $subidoPor): array
    {
        $urlVideo = trim($urlVideo);
        if (!filter_var($urlVideo, FILTER_VALIDATE_URL)) {
            return ['ok' => false, 'error' => 'URL de video inválida.'];
        }
        $stmt = db()->prepare(
            'INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, video_url, subido_por)
             VALUES (:na, :no, :ruta, :tipo, :mime, :url, :por)'
        );
        $na = 'video-ext-' . bin2hex(random_bytes(6));
        $stmt->execute([
            ':na' => $na,
            ':no' => ($titulo !== '' ? mb_substr($titulo, 0, 255) : 'Video externo'),
            ':ruta' => '',
            ':tipo' => 'video',
            ':mime' => 'text/uri-list',
            ':url' => $urlVideo,
            ':por' => $subidoPor,
        ]);
        return ['ok' => true, 'id' => (int) db()->lastInsertId()];
    }

    /**
     * Guarda una imagen de firma en /uploads/solicitudes (acceso restringido,
     * NO accesible por URL pública; se sirve por script con sesión admin).
     * Solo acepta imágenes JPG/PNG.
     * @return array{ok:bool, error?:string, id?:int}
     */
    public static function subirFirma(array $file, ?int $maxBytes = null): array
    {
        $maxBytes = $maxBytes ?? UPLOAD_MAX_BYTES;

        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return ['ok' => false, 'error' => 'No se recibió la firma correctamente.'];
        }
        if ($file['size'] > $maxBytes) {
            return ['ok' => false, 'error' => 'La firma supera el tamaño máximo permitido.'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            return ['ok' => false, 'error' => 'La firma debe ser una imagen JPG o PNG.'];
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return ['ok' => false, 'error' => 'El archivo de firma no es una imagen válida.'];
        }

        $nombreArchivo = 'firma-' . bin2hex(random_bytes(16)) . '.' . $ext;
        if (!is_dir(UPLOAD_DIR_SOLICITUDES)) {
            @mkdir(UPLOAD_DIR_SOLICITUDES, 0755, true);
        }
        $destino = UPLOAD_DIR_SOLICITUDES . '/' . $nombreArchivo;
        if (!move_uploaded_file($file['tmp_name'], $destino)) {
            return ['ok' => false, 'error' => 'No se pudo guardar la firma.'];
        }

        $dim = @getimagesize($destino);
        $stmt = db()->prepare(
            'INSERT INTO media (nombre_archivo, nombre_original, ruta, tipo, mime_type, tamano_bytes, ancho, alto)
             VALUES (:na, :no, :ruta, "imagen", :mime, :size, :ancho, :alto)'
        );
        $stmt->execute([
            ':na' => $nombreArchivo,
            ':no' => 'firma-solicitud',
            // La ruta lleva el prefijo "solicitudes/" para distinguir del media público.
            ':ruta' => 'solicitudes/' . $nombreArchivo,
            ':mime' => $mime,
            ':size' => (int) $file['size'],
            ':ancho' => $dim[0] ?? null,
            ':alto' => $dim[1] ?? null,
        ]);
        return ['ok' => true, 'id' => (int) db()->lastInsertId()];
    }

    /** Ruta absoluta en disco de un medio de firma (para servirlo por script). */
    public static function rutaFirmaEnDisco(array $m): ?string
    {
        if (empty($m['ruta']) || strncmp($m['ruta'], 'solicitudes/', 12) !== 0) {
            return null;
        }
        $nombre = basename($m['ruta']);
        $ruta = UPLOAD_DIR_SOLICITUDES . '/' . $nombre;
        return is_file($ruta) ? $ruta : null;
    }

    /** Actualiza el alt_text de un medio. */
    public static function actualizarAlt(int $id, string $alt): void
    {
        db()->prepare('UPDATE media SET alt_text = :a WHERE id = :id')
            ->execute([':a' => mb_substr(trim($alt), 0, 255), ':id' => $id]);
    }

    /**
     * ¿El medio está referenciado por alguna entidad? Devuelve la lista de usos.
     */
    public static function usos(int $id): array
    {
        $usos = [];
        $refs = [
            'noticias'            => ['imagen_destacada_id', 'Noticias'],
            'servicios'           => ['imagen_id', 'Servicios'],
            'ejecutivos'          => ['foto_id', 'Ejecutivos'],
            'academy_recursos'    => ['media_id', 'Academy'],
            'secciones_contenido' => ['imagen_id', 'Secciones'],
            'solicitudes_apertura'=> ['firma_media_id', 'Solicitudes'],
        ];
        foreach ($refs as $tabla => [$col, $etiqueta]) {
            $stmt = db()->prepare("SELECT COUNT(*) FROM {$tabla} WHERE {$col} = :id");
            $stmt->execute([':id' => $id]);
            $n = (int) $stmt->fetchColumn();
            if ($n > 0) {
                $usos[] = "{$etiqueta} ({$n})";
            }
        }
        return $usos;
    }

    /**
     * Elimina un medio (registro + archivo físico). Con FK ON DELETE SET NULL,
     * las referencias quedan en NULL automáticamente.
     * @return array{ok:bool, error?:string}
     */
    public static function eliminar(int $id): array
    {
        $m = self::buscar($id);
        if (!$m) {
            return ['ok' => false, 'error' => 'Medio no encontrado.'];
        }
        // Borrar archivo físico si existe (los video_url no tienen archivo).
        if (!empty($m['ruta'])) {
            $ruta = UPLOAD_DIR_MEDIA . '/' . $m['ruta'];
            if (is_file($ruta)) {
                @unlink($ruta);
            }
        }
        db()->prepare('DELETE FROM media WHERE id = :id')->execute([':id' => $id]);
        return ['ok' => true];
    }
}
