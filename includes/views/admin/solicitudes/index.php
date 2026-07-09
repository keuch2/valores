<?php /** Listado de solicitudes con filtros. Recibe $solicitudes, $agentes, $filtros. */
$badgeEstado = ['nueva'=>'badge-info','en_proceso'=>'badge-info','aprobada'=>'badge-ok','rechazada'=>'badge-off'];
$tipoLbl = ['fisica'=>'Persona Física','juridica'=>'Persona Jurídica','conjunta'=>'Cuenta Conjunta'];
?>
<div class="card">
  <form method="get" action="<?= e(url('admin/')) ?>" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <input type="hidden" name="r" value="solicitudes">
    <select class="form-select" name="estado" style="width:auto">
      <option value="">Todos los estados</option>
      <?php foreach (['nueva'=>'Nueva','en_proceso'=>'En proceso','aprobada'=>'Aprobada','rechazada'=>'Rechazada'] as $k=>$v): ?>
        <option value="<?= $k ?>" <?= ($filtros['estado']??'')===$k?'selected':'' ?>><?= $v ?></option>
      <?php endforeach; ?>
    </select>
    <select class="form-select" name="tipo" style="width:auto">
      <option value="">Todos los tipos</option>
      <?php foreach ($tipoLbl as $k=>$v): ?>
        <option value="<?= $k ?>" <?= ($filtros['tipo']??'')===$k?'selected':'' ?>><?= $v ?></option>
      <?php endforeach; ?>
    </select>
    <select class="form-select" name="agente" style="width:auto">
      <option value="">Todos los agentes</option>
      <?php foreach ($agentes as $a): ?>
        <option value="<?= (int)$a['id'] ?>" <?= ((string)($filtros['agente']??''))===(string)$a['id']?'selected':'' ?>><?= e($a['nombre']) ?></option>
      <?php endforeach; ?>
    </select>
    <input class="form-input" type="search" name="q" value="<?= e($filtros['q']??'') ?>" placeholder="Nombre o documento…" style="width:auto">
    <button class="btn btn-ghost btn-sm" type="submit">Filtrar</button>
  </form>
</div>

<?php if (empty($solicitudes)): ?>
  <div class="card"><div class="empty">No hay solicitudes que coincidan.</div></div>
<?php else: ?>
<table class="tabla">
  <thead><tr><th>#</th><th>Tipo</th><th>Nombre / Razón social</th><th>Documento</th><th>Agente</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($solicitudes as $s): ?>
    <tr>
      <td>#<?= (int)$s['id'] ?></td>
      <td><?= e($tipoLbl[$s['tipo_persona']] ?? $s['tipo_persona']) ?></td>
      <td><?= e($s['nombre_referencia'] ?: '—') ?></td>
      <td><?= e($s['documento_referencia'] ?: '—') ?></td>
      <td><?= e($s['agente_nombre'] ?? '—') ?></td>
      <td><span class="badge <?= $badgeEstado[$s['estado']] ?? 'badge-info' ?>"><?= e($s['estado']) ?></span></td>
      <td><?= e(substr((string)$s['created_at'],0,10)) ?></td>
      <td><a href="<?= e(url('admin/?r=solicitudes/ver&id='.(int)$s['id'])) ?>">Ver detalle</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
