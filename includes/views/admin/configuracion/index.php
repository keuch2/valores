<?php /** Vista: configuración por grupos. Recibe $grupos, $valores. */ ?>
<form method="post" action="<?= e(url('admin/?r=configuracion')) ?>">
    <?= csrf_campo() ?>
    <?php foreach ($grupos as $gk => $g): ?>
        <div class="card">
            <h2><?= e($g['titulo']) ?></h2>
            <?php foreach ($g['campos'] as $clave => $lbl): ?>
                <div class="form-group">
                    <label class="form-label" for="c_<?= e($clave) ?>"><?= e($lbl) ?></label>
                    <input class="form-input" type="<?= $clave === 'smtp_pass' ? 'password' : 'text' ?>"
                           id="c_<?= e($clave) ?>" name="<?= e($clave) ?>"
                           value="<?= e($valores[$clave] ?? '') ?>"
                           <?= $clave === 'smtp_pass' ? 'placeholder="(sin cambios)" autocomplete="new-password"' : '' ?>>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-primary" type="submit">Guardar configuración</button>
</form>
