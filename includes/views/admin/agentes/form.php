<?php
/** Vista: formulario de agente. Recibe $modo, $a, $error. */
$esEditar = ($modo === 'editar');
$accion = $esEditar ? url('admin/?r=agentes/editar&id=' . (int) $a['id']) : url('admin/?r=agentes/crear');
?>
<div class="card" style="max-width:560px">
    <h2><?= $esEditar ? 'Editar agente' : 'Nuevo agente' ?></h2>
    <?php if (!empty($error)): ?><div class="alert alert-error" style="margin:0 0 16px"><?= e($error) ?></div><?php endif; ?>

    <form method="post" action="<?= e($accion) ?>">
        <?= csrf_campo() ?>
        <div class="form-group">
            <label class="form-label" for="nombre">Nombre</label>
            <input class="form-input" id="nombre" name="nombre" required value="<?= e($a['nombre'] ?? post('nombre')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input class="form-input" type="email" id="email" name="email" required value="<?= e($a['email'] ?? post('email')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="orden">Orden (desempate en la rotación)</label>
            <input class="form-input" type="number" id="orden" name="orden" value="<?= e((string) ($a['orden'] ?? '0')) ?>">
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" name="activo" value="1" <?= (!$a || (int) $a['activo'] === 1) ? 'checked' : '' ?>>
                <span class="form-label" style="margin:0">Activo (entra en la rotación)</span>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit"><?= $esEditar ? 'Guardar' : 'Crear' ?></button>
            <a class="btn btn-ghost" href="<?= e(url('admin/?r=agentes')) ?>">Cancelar</a>
        </div>
    </form>
</div>
