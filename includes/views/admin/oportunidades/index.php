<?php /** Vista: reporte diario de oportunidades. Recibe $historial (array) y $vigenteId (int). */ ?>
<div class="card">
    <h2>Subir el reporte de hoy</h2>
    <form method="post" action="<?= e(url('admin/?r=oportunidades/subir')) ?>" enctype="multipart/form-data" style="max-width:480px">
        <?= csrf_campo() ?>
        <div class="form-group">
            <label class="form-label">Archivo PDF</label>
            <input class="form-input" type="file" name="archivo" accept=".pdf,application/pdf" required>
            <p class="form-hint">Máx. <?= (int) round(UPLOAD_MAX_BYTES / 1048576) ?> MB. Pasa a ser el reporte vigente de inmediato; los anteriores quedan en el historial.</p>
        </div>
        <button class="btn btn-primary" type="submit">Publicar reporte</button>
    </form>
</div>

<div class="card">
    <h2>Historial de reportes</h2>
    <?php if (empty($historial)): ?>
        <div class="empty">Todavía no hay ningún reporte cargado. El botón de descarga no se muestra en el sitio hasta que subas uno.</div>
    <?php else: ?>
        <p class="form-hint">El reporte <strong>vigente</strong> es el que descargan los visitantes desde <a href="<?= e(url('oportunidades')) ?>" target="_blank">/oportunidades</a>. Si lo eliminás, el más reciente que quede pasa a ser el vigente.</p>
        <table class="tabla">
            <thead><tr><th>Fecha</th><th>Archivo</th><th>Tamaño</th><th>Subido por</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($historial as $h): ?>
                <tr>
                    <td style="white-space:nowrap"><?= e($h['created_at']) ?></td>
                    <td><a href="<?= e(Media::urlPublica($h)) ?>" target="_blank"><?= e($h['nombre_original']) ?></a></td>
                    <td><?= e((string) round(((int) $h['tamano_bytes']) / 1024)) ?> KB</td>
                    <td><?= e($h['subido_por_nombre'] ?? '—') ?></td>
                    <td><?= (int) $h['media_id'] === (int) $vigenteId ? '<span class="badge badge-ok">vigente</span>' : '<span class="badge badge-off">anterior</span>' ?></td>
                    <td style="white-space:nowrap">
                        <a href="<?= e(Media::urlPublica($h)) ?>" target="_blank">Ver</a>
                        &nbsp;·&nbsp;
                        <form method="post" action="<?= e(url('admin/?r=oportunidades/eliminar')) ?>" style="display:inline"
                              onsubmit="return confirm('¿Eliminar este reporte del historial? Se borra el archivo PDF.')">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int) $h['id'] ?>">
                            <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
