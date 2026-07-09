<?php
/** Vista genérica: formulario de una entidad. Recibe $clave,$cfg,$reg,$error,$modo. */
$esEditar = ($modo === 'editar');
$accion = $esEditar
    ? url('admin/?r=' . $clave . '/editar&id=' . (int) $reg['id'])
    : url('admin/?r=' . $clave . '/crear');

/** Valor actual de un campo (registro o repost). */
$valor = function (string $campo) use ($reg) {
    return $reg[$campo] ?? '';
};
?>
<div class="card" style="max-width:760px">
    <h2><?= $esEditar ? 'Editar' : 'Nuevo' ?>: <?= e($cfg['titulo_singular']) ?></h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error" style="margin:0 0 16px"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e($accion) ?>">
        <?= csrf_campo() ?>

        <?php foreach ($cfg['campos'] as $campo):
            $n = $campo['nombre']; $v = $valor($n); $req = !empty($campo['requerido']) ? 'required' : ''; ?>
            <div class="form-group">
                <?php if ($campo['tipo'] !== 'checkbox'): ?>
                    <label class="form-label" for="f_<?= e($n) ?>"><?= e($campo['etiqueta']) ?><?= $req ? ' *' : '' ?></label>
                <?php endif; ?>

                <?php switch ($campo['tipo']):
                    case 'textarea': ?>
                        <textarea class="form-textarea" id="f_<?= e($n) ?>" name="<?= e($n) ?>" <?= $req ?>><?= e((string) $v) ?></textarea>
                        <?php break; ?>

                    <?php case 'richtext': ?>
                        <div class="rt-toolbar">
                            <button type="button" data-cmd="bold"><b>B</b></button>
                            <button type="button" data-cmd="italic"><i>I</i></button>
                            <button type="button" data-cmd="insertUnorderedList">• Lista</button>
                            <button type="button" data-cmd="createLink">🔗 Link</button>
                        </div>
                        <div class="rt-editor form-input" contenteditable="true" data-target="f_<?= e($n) ?>"><?= $v /* HTML de contenido controlado por admin */ ?></div>
                        <input type="hidden" id="f_<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e((string) $v) ?>">
                        <?php break; ?>

                    <?php case 'select': ?>
                        <select class="form-select" id="f_<?= e($n) ?>" name="<?= e($n) ?>" <?= $req ?>>
                            <?php foreach ($campo['opciones'] as $ov => $ol): ?>
                                <option value="<?= e($ov) ?>" <?= (string) $v === (string) $ov ? 'selected' : '' ?>><?= e($ol) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php break; ?>

                    <?php case 'number': ?>
                        <input class="form-input" type="number" id="f_<?= e($n) ?>" name="<?= e($n) ?>"
                               step="<?= e($campo['step'] ?? '1') ?>" value="<?= e((string) $v) ?>" <?= $req ?>>
                        <?php break; ?>

                    <?php case 'date': ?>
                        <input class="form-input" type="date" id="f_<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e((string) $v) ?>">
                        <?php break; ?>

                    <?php case 'checkbox': ?>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" id="f_<?= e($n) ?>" name="<?= e($n) ?>" value="1" <?= (int) $v === 1 ? 'checked' : '' ?>>
                            <span class="form-label" style="margin:0"><?= e($campo['etiqueta']) ?></span>
                        </label>
                        <?php break; ?>

                    <?php case 'media': ?>
                        <div class="media-field">
                            <input type="hidden" id="f_<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e((string) $v) ?>">
                            <button type="button" class="btn btn-ghost btn-sm" data-media-picker
                                    data-target="#f_<?= e($n) ?>" data-preview="#prev_<?= e($n) ?>"
                                    data-tipo="<?= e($campo['media_tipo'] ?? '') ?>">Elegir de la biblioteca</button>
                            <div class="media-preview" id="prev_<?= e($n) ?>">
                                <?php if ($v) { $m = Media::buscar((int) $v); if ($m): ?>
                                    <img src="<?= e(Media::urlPublica($m)) ?>" alt="" style="max-height:80px;border-radius:6px">
                                <?php endif; } ?>
                            </div>
                        </div>
                        <?php break; ?>

                    <?php default: ?>
                        <input class="form-input" type="text" id="f_<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e((string) $v) ?>" <?= $req ?>>
                <?php endswitch; ?>

                <?php if (!empty($campo['hint'])): ?>
                    <p class="form-hint"><?= e($campo['hint']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit"><?= $esEditar ? 'Guardar cambios' : 'Crear' ?></button>
            <a class="btn btn-ghost" href="<?= e(url('admin/?r=' . $clave)) ?>">Cancelar</a>
        </div>
    </form>
</div>
