<?php /** Vista: dashboard. Recibe contadores y $ultimasNoticias. */ ?>
<div class="stats-grid">
    <a class="stat alerta" href="<?= e(url('admin/?r=solicitudes')) ?>" style="text-decoration:none">
        <div class="num"><?= (int) $solicitudesNuevas ?></div>
        <div class="lbl">Solicitudes nuevas</div>
    </a>
    <div class="stat">
        <div class="num"><?= (int) $totalSolicitudes ?></div>
        <div class="lbl">Solicitudes totales</div>
    </div>
    <div class="stat">
        <div class="num"><?= (int) $oportActivas ?></div>
        <div class="lbl">Oportunidades disponibles</div>
    </div>
    <div class="stat">
        <div class="num"><?= (int) $noticiasPub ?></div>
        <div class="lbl">Noticias publicadas</div>
    </div>
</div>

<div class="card">
    <h2>Últimas noticias</h2>
    <?php if (empty($ultimasNoticias)): ?>
        <div class="empty">Todavía no hay noticias cargadas.
            <a href="<?= e(url('admin/?r=noticias/crear')) ?>">Crear la primera</a>.</div>
    <?php else: ?>
        <table class="tabla">
            <thead><tr><th>Título</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($ultimasNoticias as $n): ?>
                <tr>
                    <td><?= e($n['titulo']) ?></td>
                    <td>
                        <?php if ($n['estado'] === 'publicado'): ?>
                            <span class="badge badge-ok">publicado</span>
                        <?php else: ?>
                            <span class="badge badge-info">borrador</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($n['fecha_publicacion'] ?? '—') ?></td>
                    <td><a href="<?= e(url('admin/?r=noticias/editar&id=' . (int) $n['id'])) ?>">Editar</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Accesos rápidos</h2>
    <div class="form-actions" style="margin-top:0;flex-wrap:wrap">
        <a class="btn btn-primary" href="<?= e(url('admin/?r=oportunidades/crear')) ?>">+ Oportunidad</a>
        <a class="btn btn-secondary" href="<?= e(url('admin/?r=noticias/crear')) ?>">+ Noticia</a>
        <a class="btn btn-ghost" href="<?= e(url('admin/?r=solicitudes')) ?>">Ver solicitudes</a>
        <a class="btn btn-ghost" href="<?= e(url('admin/?r=configuracion')) ?>">Configuración</a>
    </div>
</div>
