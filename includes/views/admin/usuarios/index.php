<?php /** Vista: listado de usuarios. Recibe $usuarios, $idSesion. */ ?>
<div class="toolbar">
    <div></div>
    <a class="btn btn-primary" href="<?= e(url('admin/?r=usuarios/crear')) ?>">+ Nuevo usuario</a>
</div>

<table class="tabla">
    <thead><tr><th>Nombre</th><th>Email</th><th>Estado</th><th>Último acceso</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($usuarios as $u): ?>
        <tr>
            <td>
                <?= e($u['nombre']) ?>
                <?php if ((int) $u['id'] === (int) $idSesion): ?>
                    <span class="badge badge-info">vos</span>
                <?php endif; ?>
            </td>
            <td><?= e($u['email']) ?></td>
            <td>
                <?php if ((int) $u['activo'] === 1): ?>
                    <span class="badge badge-ok">activo</span>
                <?php else: ?>
                    <span class="badge badge-off">inactivo</span>
                <?php endif; ?>
            </td>
            <td><?= e($u['ultimo_acceso'] ?? '—') ?></td>
            <td style="white-space:nowrap">
                <a href="<?= e(url('admin/?r=usuarios/editar&id=' . (int) $u['id'])) ?>">Editar</a>
                <?php if ((int) $u['id'] !== (int) $idSesion): ?>
                    &nbsp;·&nbsp;
                    <form method="post" action="<?= e(url('admin/?r=usuarios/eliminar')) ?>"
                          style="display:inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
