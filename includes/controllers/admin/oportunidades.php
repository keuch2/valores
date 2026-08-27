<?php
/**
 * Controlador admin/oportunidades — reporte diario de oportunidades en PDF.
 *
 * El tablero de oportunidades (tabla) quedó desactivado: la página pública
 * ofrece la descarga del PDF que se sube acá cada día. El archivo pasa por
 * Media::subir() (whitelist de extensión + MIME real) y su id se guarda en
 * la config `oportunidades_pdf_id`.
 */
declare(strict_types=1);

/** Devuelve la fila de media del PDF vigente (o null). */
function oportunidades_pdf_actual(): ?array
{
    $id = (int) Config::get('oportunidades_pdf_id', '0');
    return $id > 0 ? Media::buscar($id) : null;
}

function accion_index(): void
{
    render_admin('oportunidades/index', ['pdf' => oportunidades_pdf_actual()], 'Oportunidades — Reporte diario');
}

/** POST: sube el PDF del día y lo deja como vigente. */
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

    $r = Media::subir($file, (int) auth_usuario()['id']);
    if (!$r['ok']) {
        flash('error', $r['error']);
        redirigir('admin/?r=oportunidades');
    }

    Config::guardar(['oportunidades_pdf_id' => (string) $r['id']]);
    flash('exito', 'Reporte de oportunidades actualizado. Ya está disponible para descarga en el sitio.');
    redirigir('admin/?r=oportunidades');
}
