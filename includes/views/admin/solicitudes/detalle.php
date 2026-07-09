<?php
/** Detalle de solicitud. Recibe $sol (con datos_desc), $etiquetas, $agentes. */
$d = $sol['datos_desc'] ?? [];
$tipoLbl = ['fisica'=>'Persona Física','juridica'=>'Persona Jurídica','conjunta'=>'Cuenta Conjunta'];
/** Muestra valor legible de un campo. */
$fmt = function ($v) {
    if (is_array($v)) return '';
    return trim((string) $v);
};
?>
<div style="display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start">
  <div style="flex:1;min-width:340px">

    <div class="card">
      <div class="toolbar">
        <h2 style="margin:0">Solicitud #<?= (int)$sol['id'] ?> — <?= e($tipoLbl[$sol['tipo_persona']] ?? $sol['tipo_persona']) ?></h2>
        <a class="btn btn-ghost btn-sm" href="<?= e(url('admin/?r=solicitudes')) ?>">← Volver</a>
      </div>

      <!-- Datos del wizard, ordenados por etiqueta conocida -->
      <table class="tabla" style="margin-top:8px">
        <tbody>
        <?php foreach ($etiquetas as $campo => $lbl):
          if (!array_key_exists($campo, $d)) continue;
          $val = $fmt($d[$campo]); if ($val === '') continue; ?>
          <tr><th style="width:240px"><?= e($lbl) ?></th><td><?= e($val) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Titulares adicionales (conjunta) -->
      <?php if (!empty($d['titulares_adicionales']) && is_array($d['titulares_adicionales'])): ?>
        <h3 style="margin-top:16px;color:var(--blue-inst)">Titulares adicionales</h3>
        <table class="tabla"><thead><tr><th>Nombre</th><th>Documento</th><th>Email</th></tr></thead><tbody>
          <?php foreach ($d['titulares_adicionales'] as $t): ?>
            <tr><td><?= e($t['nombre']??'') ?></td><td><?= e($t['documento']??'') ?></td><td><?= e($t['email']??'') ?></td></tr>
          <?php endforeach; ?>
        </tbody></table>
      <?php endif; ?>

      <!-- Repeaters jurídica (accionistas / firmantes) -->
      <?php foreach (['accionistas'=>'Accionistas/socios','firmantes'=>'Firmantes/representantes'] as $rk=>$rlbl): ?>
        <?php if (!empty($d[$rk]) && is_array($d[$rk])): ?>
          <h3 style="margin-top:16px;color:var(--blue-inst)"><?= e($rlbl) ?></h3>
          <table class="tabla"><tbody>
            <?php foreach ($d[$rk] as $fila): if(!is_array($fila)) continue; ?>
              <tr><td><?= e(implode(' · ', array_filter(array_map('strval', $fila)))) ?></td></tr>
            <?php endforeach; ?>
          </tbody></table>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <!-- Firma -->
    <div class="card">
      <h2>Firma cargada</h2>
      <?php if (!empty($sol['firma_media_id'])): ?>
        <img src="<?= e(url('admin/?r=solicitudes/firma&id='.(int)$sol['id'])) ?>" alt="Firma" style="max-height:160px;border:1px solid var(--gray-ui);border-radius:8px">
      <?php else: ?>
        <p class="form-hint">No se adjuntó firma.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Panel lateral: estado, agente, trazabilidad -->
  <div style="width:320px;flex-shrink:0">
    <div class="card">
      <h2>Estado</h2>
      <form method="post" action="<?= e(url('admin/?r=solicitudes/estado')) ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= (int)$sol['id'] ?>">
        <select class="form-select" name="estado">
          <?php foreach (['nueva'=>'Nueva','en_proceso'=>'En proceso','aprobada'=>'Aprobada','rechazada'=>'Rechazada'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= $sol['estado']===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-primary btn-sm" type="submit" style="margin-top:10px;width:100%">Actualizar estado</button>
      </form>
    </div>

    <div class="card">
      <h2>Agente asignado</h2>
      <p style="margin:0 0 10px"><strong><?= e($sol['agente_nombre'] ?? 'Sin asignar') ?></strong></p>
      <form method="post" action="<?= e(url('admin/?r=solicitudes/reasignar')) ?>">
        <?= csrf_campo() ?>
        <input type="hidden" name="id" value="<?= (int)$sol['id'] ?>">
        <select class="form-select" name="agente_id">
          <?php foreach ($agentes as $a): ?>
            <option value="<?= (int)$a['id'] ?>" <?= (int)($sol['agente_asignado_id']??0)===(int)$a['id']?'selected':'' ?>><?= e($a['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-ghost btn-sm" type="submit" style="margin-top:10px;width:100%">Reasignar</button>
      </form>
    </div>

    <div class="card">
      <h2>Trazabilidad</h2>
      <p class="form-hint" style="margin:0;line-height:1.8">
        Recibida: <?= e($sol['created_at']) ?><br>
        IP: <?= e($sol['ip_texto'] ?? '—') ?><br>
        Contacto: <?= e($sol['email_contacto'] ?: '—') ?><br>
        <?= e($sol['telefono_contacto'] ?: '') ?>
      </p>
    </div>
  </div>
</div>
