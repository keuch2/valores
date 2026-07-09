<?php
/** Vista: formulario de usuario (crear/editar). Recibe $modo, $u, $error. */
$esEditar = ($modo === 'editar');
$accion   = $esEditar
    ? url('admin/?r=usuarios/editar&id=' . (int) $u['id'])
    : url('admin/?r=usuarios/crear');
?>
<div class="card" style="max-width:560px">
    <h2><?= $esEditar ? 'Editar usuario' : 'Nuevo usuario' ?></h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error" style="margin:0 0 16px"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e($accion) ?>">
        <?= csrf_campo() ?>

        <div class="form-group">
            <label class="form-label" for="nombre">Nombre</label>
            <input class="form-input" id="nombre" name="nombre" required
                   value="<?= e($u['nombre'] ?? post('nombre')) ?>">
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input class="form-input" type="email" id="email" name="email" required
                   value="<?= e($u['email'] ?? post('email')) ?>">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">
                Contraseña <?= $esEditar ? '(dejar vacío para no cambiarla)' : '' ?>
            </label>
            <input class="form-input" type="password" id="password" name="password"
                   <?= $esEditar ? '' : 'required' ?> minlength="8" autocomplete="new-password">
            <p class="form-hint">Mínimo 8 caracteres.</p>
        </div>

        <div class="form-group">
            <label class="form-label" for="activo">Estado</label>
            <select class="form-select" id="activo" name="activo">
                <option value="1" <?= (!$u || (int) $u['activo'] === 1) ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= ($u && (int) $u['activo'] === 0) ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $esEditar ? 'Guardar cambios' : 'Crear usuario' ?></button>
            <a class="btn btn-ghost" href="<?= e(url('admin/?r=usuarios')) ?>">Cancelar</a>
        </div>
    </form>
</div>
