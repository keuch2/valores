<?php /** Vista: reporte diario de oportunidades (PDF). Recibe $pdf (fila media o null). */ ?>
<div class="card">
    <h2>Reporte vigente</h2>
    <?php if ($pdf): ?>
        <p><strong><?= e($pdf['nombre_original']) ?></strong>
           &nbsp;·&nbsp; <?= e(round(((int) $pdf['tamano_bytes']) / 1024)) ?> KB
           &nbsp;·&nbsp; subido el <?= e($pdf['created_at']) ?></p>
        <p class="form-hint">Es el archivo que descargan los visitantes desde <a href="<?= e(url('oportunidades')) ?>" target="_blank">/oportunidades</a>.
           &nbsp;<a href="<?= e(Media::urlPublica($pdf)) ?>" target="_blank">Ver PDF actual →</a></p>
    <?php else: ?>
        <div class="empty">Todavía no hay ningún reporte cargado. El botón de descarga no se muestra en el sitio hasta que subas uno.</div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Subir el reporte de hoy</h2>
    <form method="post" action="<?= e(url('admin/?r=oportunidades/subir')) ?>" enctype="multipart/form-data" style="max-width:480px">
        <?= csrf_campo() ?>
        <div class="form-group">
            <label class="form-label">Archivo PDF</label>
            <input class="form-input" type="file" name="archivo" accept=".pdf,application/pdf" required>
            <p class="form-hint">Máx. <?= (int) round(UPLOAD_MAX_BYTES / 1048576) ?> MB. Reemplaza al reporte vigente de inmediato; los anteriores quedan en la biblioteca de medios.</p>
        </div>
        <button class="btn btn-primary" type="submit">Publicar reporte</button>
    </form>
</div>
