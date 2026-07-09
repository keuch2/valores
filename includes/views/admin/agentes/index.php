<?php /** Vista: listado de agentes con su reparto. Recibe $agentes. */ ?>
<div class="toolbar">
    <p class="form-hint" style="margin:0">Sólo los agentes <strong>activos</strong> entran en la rotación round-robin.</p>
    <a class="btn btn-primary" href="<?= e(url('admin/?r=agentes/crear')) ?>">+ Nuevo agente</a>
</div>

<?php if (empty($agentes)): ?>
    <div class="card"><div class="empty">No hay agentes cargados.</div></div>
<?php else: ?>
<table class="tabla">
    <thead><tr><th>Orden</th><th>Nombre</th><th>Email</th><th>Estado</th><th>Asignadas</th><th>Última asignación</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($agentes as $a): ?>
        <tr>
            <td><?= (int) $a['orden'] ?></td>
            <td><?= e($a['nombre']) ?></td>
            <td><?= e($a['email']) ?></td>
            <td><?= (int) $a['activo'] === 1 ? '<span class="badge badge-ok">activo</span>' : '<span class="badge badge-off">inactivo</span>' ?></td>
            <td><?= (int) $a['total_asignadas'] ?></td>
            <td><?= e($a['ultima_asignacion'] ?? '—') ?></td>
            <td style="white-space:nowrap">
                <a href="<?= e(url('admin/?r=agentes/editar&id=' . (int) $a['id'])) ?>">Editar</a>
                &nbsp;·&nbsp;
                <form method="post" action="<?= e(url('admin/?r=agentes/eliminar')) ?>" style="display:inline"
                      onsubmit="return confirm('¿Eliminar este agente?')">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                    <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
