<?php /** Vista genérica: listado de una entidad. Recibe $clave, $cfg, $registros. */ ?>
<div class="toolbar">
    <div></div>
    <a class="btn btn-primary" href="<?= e(url('admin/?r=' . $clave . '/crear')) ?>">+ Nuevo: <?= e($cfg['titulo_singular']) ?></a>
</div>

<?php if (empty($registros)): ?>
    <div class="card"><div class="empty">Todavía no hay registros. <a href="<?= e(url('admin/?r=' . $clave . '/crear')) ?>">Crear el primero</a>.</div></div>
<?php else: ?>
<table class="tabla">
    <thead>
        <tr>
            <?php foreach ($cfg['columnas_lista'] as $lbl): ?><th><?= e($lbl) ?></th><?php endforeach; ?>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($registros as $reg): ?>
        <tr>
            <?php foreach ($cfg['columnas_lista'] as $col => $lbl): $val = $reg[$col] ?? ''; ?>
                <td>
                    <?php if (in_array($col, ['activo','destacado','visible_front'], true)): ?>
                        <?= (int) $val === 1 ? '<span class="badge badge-ok">sí</span>' : '<span class="badge badge-off">no</span>' ?>
                    <?php elseif ($col === 'estado'): ?>
                        <span class="badge <?= $val === 'publicado' || $val === 'disponible' ? 'badge-ok' : 'badge-info' ?>"><?= e((string) $val) ?></span>
                    <?php else: ?>
                        <?= e(mb_strimwidth((string) $val, 0, 60, '…')) ?>
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
            <td style="white-space:nowrap">
                <a href="<?= e(url('admin/?r=' . $clave . '/editar&id=' . (int) $reg['id'])) ?>">Editar</a>
                &nbsp;·&nbsp;
                <form method="post" action="<?= e(url('admin/?r=' . $clave . '/eliminar')) ?>"
                      style="display:inline" onsubmit="return confirm('¿Eliminar este registro?')">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id" value="<?= (int) $reg['id'] ?>">
                    <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
