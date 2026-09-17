<?php /** Vista: configuración por grupos. Recibe $grupos, $valores. */ ?>
<form method="post" action="<?= e(url('admin/?r=configuracion')) ?>" enctype="multipart/form-data">
    <?= csrf_campo() ?>
    <?php foreach ($grupos as $gk => $g): ?>
        <div class="card">
            <h2><?= e($g['titulo']) ?></h2>
            <?php foreach ($g['campos'] as $clave => $def): ?>
                <?php $tipo = is_array($def) ? ($def['tipo'] ?? 'text') : 'text'; ?>
                <div class="form-group">
                    <label class="form-label" for="c_<?= e($clave) ?>"><?= config_campo_etiqueta($def) ?></label>
                    <?php if ($tipo === 'imagen'): ?>
                        <?php $img = img_url((int) ($valores[$clave] ?? 0)); ?>
                        <?php if ($img): ?>
                            <p style="margin:0 0 8px"><img src="<?= e($img) ?>" alt="Favicon actual" style="width:48px;height:48px;object-fit:contain;border:1px solid var(--color-gray-ui,#D9E1E8);border-radius:6px;padding:4px;background:#fff"></p>
                        <?php endif; ?>
                        <input class="form-input" type="file" id="c_<?= e($clave) ?>" name="<?= e($clave) ?>" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                    <?php elseif ($tipo === 'textarea'): ?>
                        <textarea class="form-input" id="c_<?= e($clave) ?>" name="<?= e($clave) ?>" rows="10"
                                  spellcheck="false" style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px"><?= e($valores[$clave] ?? '') ?></textarea>
                    <?php else: ?>
                        <input class="form-input" type="<?= $tipo === 'password' ? 'password' : 'text' ?>"
                               id="c_<?= e($clave) ?>" name="<?= e($clave) ?>"
                               value="<?= e($valores[$clave] ?? '') ?>"
                               <?= $tipo === 'password' ? 'autocomplete="new-password"' : '' ?>>
                    <?php endif; ?>
                    <?php if (is_array($def) && !empty($def['hint'])): ?>
                        <p class="form-hint"><?= $def['hint'] ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-primary" type="submit">Guardar configuración</button>
</form>
