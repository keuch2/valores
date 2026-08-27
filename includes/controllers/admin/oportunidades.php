<?php
/**
 * Controlador admin/oportunidades — reporte diario de oportunidades en PDF.
 *
 * El tablero (tabla) quedó desactivado: la página pública ofrece la descarga
 * del PDF vigente (config `oportunidades_pdf_id`). Cada subida pasa por
 * Media::subir() y queda en el historial `oportunidades_reportes`, desde
 * donde se puede eliminar (archivo + registro).
 */
declare(strict_types=1);

/** Id del media del reporte vigente (0 si no hay). */
function oportunidades_vigente_id(): int
{
    return (int) Config::get('oportunidades_pdf_id', '0');
}

/** Historial completo, más reciente primero. */
function oportunidades_historial(): array
{
    return db()->query(
        'SELECT r.id, r.media_id, r.created_at, m.nombre_original, m.ruta, m.tamano_bytes, m.video_url, u.nombre AS subido_por_nombre
         FROM oportunidades_reportes r
         JOIN media m ON m.id = r.media_id
         LEFT JOIN admin_users u ON u.id = r.subido_por
         ORDER BY r.created_at DESC, r.id DESC'
    )->fetchAll();
}

function accion_index(): void
{
    render_admin('oportunidades/index', [
        'historial'  => oportunidades_historial(),
        'vigenteId'  => oportunidades_vigente_id(),
    ], 'Oportunidades — Reporte diario');
}

/** POST: sube el PDF del día, lo agrega al historial y lo deja como vigente. */
function accion_subir(): void
{
    csrf_exigir();
    $file = $_FILES['archivo'] ?? null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        flash('error', 'No se recibió ningún archivo.');
        redirigir('admin/?r=oportunidades');
    }
    if (strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION)) !== 'pdf') {
        flash('error', 'El reporte de oportunidades debe ser un archivo PDF.');
        redirigir('admin/?r=oportunidades');
    }

    $adminId = (int) auth_usuario()['id'];
    $r = Media::subir($file, $adminId);
    if (!$r['ok']) {
        flash('error', $r['error']);
        redirigir('admin/?r=oportunidades');
    }

    db()->prepare('INSERT INTO oportunidades_reportes (media_id, subido_por) VALUES (:m, :u)')
        ->execute([':m' => $r['id'], ':u' => $adminId]);
    Config::guardar(['oportunidades_pdf_id' => (string) $r['id']]);

    flash('exito', 'Reporte de oportunidades actualizado. Ya está disponible para descarga en el sitio.');
    redirigir('admin/?r=oportunidades');
}

/** POST: elimina un reporte del historial (archivo + registro). Si era el vigente, pasa a ser el más reciente que quede. */
function accion_eliminar(): void
{
    csrf_exigir();
    $id = (int) post('id');
    $st = db()->prepare('SELECT media_id FROM oportunidades_reportes WHERE id = :id');
    $st->execute([':id' => $id]);
    $mediaId = (int) $st->fetchColumn();
    if ($mediaId <= 0) {
        flash('error', 'El reporte no existe.');
        redirigir('admin/?r=oportunidades');
    }

    $eraVigente = ($mediaId === oportunidades_vigente_id());
    $rm = Media::eliminar($mediaId);   // borra archivo + fila media; el historial cae por FK CASCADE
    if (!$rm['ok']) {
        flash('error', $rm['error']);
        redirigir('admin/?r=oportunidades');
    }

    if ($eraVigente) {
        $nuevo = (int) db()->query('SELECT media_id FROM oportunidades_reportes ORDER BY created_at DESC, id DESC LIMIT 1')->fetchColumn();
        Config::guardar(['oportunidades_pdf_id' => $nuevo > 0 ? (string) $nuevo : '']);
        flash('exito', $nuevo > 0
            ? 'Reporte eliminado. El reporte anterior más reciente quedó como vigente.'
            : 'Reporte eliminado. No queda ningún reporte publicado: el botón de descarga no se muestra en el sitio.');
    } else {
        flash('exito', 'Reporte eliminado del historial.');
    }
    redirigir('admin/?r=oportunidades');
}
