<?php
/** Parcial: tabla de oportunidades. Espera $oportunidades (o $destacadas). */
$filas = $destacadas ?? $oportunidades ?? [];
$tipoLabel = ['bono' => 'Bono', 'cda' => 'CDA', 'accion' => 'Acción', 'inter' => 'Internacional'];
?>
<div class="overflow-x-auto">
  <table class="oportunidades-table w-full">
    <thead>
      <tr>
        <th>Instrumento</th><th>Tipo</th><th>Emisor</th><th>Tasa</th>
        <th>Plazo</th><th>Moneda</th><th>Calificación</th><th>Monto mín.</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($filas as $o): ?>
        <tr data-type="<?= e($o['tipo']) ?>">
          <td><strong><?= e($o['instrumento']) ?></strong></td>
          <td><span class="badge badge-<?= e($o['tipo']) ?>"><?= e($tipoLabel[$o['tipo']] ?? $o['tipo']) ?></span></td>
          <td><?= e($o['emisor'] ?? '—') ?></td>
          <td><?= $o['tasa'] !== null ? e(number_format((float) $o['tasa'], 2)) . '%' : '—' ?></td>
          <td><?= e($o['plazo'] ?? '—') ?></td>
          <td><?= e($o['moneda']) ?></td>
          <td><?php if (!empty($o['calificacion'])): ?><span class="badge badge-<?= e(strtolower($o['calificacion'])) ?>"><?= e($o['calificacion']) ?></span><?php else: ?>—<?php endif; ?></td>
          <td><?= $o['monto_minimo'] !== null ? e(number_format((float) $o['monto_minimo'], 0, ',', '.')) : '—' ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
