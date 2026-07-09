<?php
/**
 * Parcial: renderiza un campo del wizard. Espera $c (definición) y opcional $prefijo.
 * $prefijo permite nombres tipo titulares[0][nombre] en repeaters.
 */
$c = $c ?? [];
$prefijo = $prefijo ?? '';
$name = $prefijo !== '' ? $prefijo . '[' . $c['n'] . ']' : $c['n'];
$id = 'ap_' . preg_replace('/[^a-z0-9]/i', '_', $name);
$req = !empty($c['req']);
?>
<div class="form-group">
  <?php if ($c['t'] !== 'checkbox'): ?>
    <label class="form-label" for="<?= e($id) ?>"><?= e($c['l']) ?><?= $req ? ' *' : '' ?></label>
  <?php endif; ?>

  <?php switch ($c['t']):
    case 'textarea': ?>
      <textarea class="form-input" id="<?= e($id) ?>" name="<?= e($name) ?>" rows="3" <?= $req ? 'data-req="1"' : '' ?>></textarea>
      <?php break; ?>

    <?php case 'select': ?>
      <select class="form-select" id="<?= e($id) ?>" name="<?= e($name) ?>" <?= $req ? 'data-req="1"' : '' ?>>
        <option value="">Seleccioná…</option>
        <?php foreach ($c['op'] as $ov => $ol): $ov = is_int($ov) ? $ol : $ov; ?>
          <option value="<?= e((string) $ov) ?>"><?= e($ol) ?></option>
        <?php endforeach; ?>
      </select>
      <?php break; ?>

    <?php case 'radio': ?>
      <div class="flex gap-4" <?= $req ? 'data-req-radio="' . e($name) . '"' : '' ?>>
        <?php foreach ($c['op'] as $ov => $ol): ?>
          <label class="flex items-center gap-2"><input type="radio" name="<?= e($name) ?>" value="<?= e((string) $ov) ?>"> <?= e($ol) ?></label>
        <?php endforeach; ?>
      </div>
      <?php break; ?>

    <?php case 'checkbox': ?>
      <label class="flex items-start gap-2">
        <input type="checkbox" id="<?= e($id) ?>" name="<?= e($name) ?>" value="1" class="mt-1" <?= $req ? 'data-req-check="1"' : '' ?>>
        <span class="text-sm text-gray-txt"><?= e($c['l']) ?><?= $req ? ' *' : '' ?></span>
      </label>
      <?php break; ?>

    <?php case 'number': ?>
      <input class="form-input" type="number" step="any" id="<?= e($id) ?>" name="<?= e($name) ?>" <?= $req ? 'data-req="1"' : '' ?>>
      <?php break; ?>

    <?php case 'date': ?>
      <input class="form-input" type="date" id="<?= e($id) ?>" name="<?= e($name) ?>" <?= $req ? 'data-req="1"' : '' ?>>
      <?php break; ?>

    <?php case 'email': ?>
      <input class="form-input" type="email" id="<?= e($id) ?>" name="<?= e($name) ?>" <?= $req ? 'data-req="1"' : '' ?>>
      <?php break; ?>

    <?php case 'tel': ?>
      <input class="form-input" type="tel" id="<?= e($id) ?>" name="<?= e($name) ?>" <?= $req ? 'data-req="1"' : '' ?>>
      <?php break; ?>

    <?php default: ?>
      <input class="form-input" type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" <?= $req ? 'data-req="1"' : '' ?>>
  <?php endswitch; ?>
</div>
