<?php /** Confirmación de solicitud. Recibe $numero, $agente y $waUrl (string|null). */ $waUrl = $waUrl ?? null; ?>
<section class="section" style="min-height:50vh">
  <div class="container text-center" style="max-width:560px">
    <div class="card p-10">
      <div class="text-5xl mb-4">✅</div>
      <h1 class="section-title">¡Recibimos tus datos!</h1>
      <p class="text-gray-txt mt-3">Tu número de solicitud es</p>
      <div class="text-3xl font-bold text-blue-inst my-2">#<?= (int) $numero ?></div>
      <?php if ($waUrl): ?>
        <p class="text-gray-txt mt-3">Te llevamos a WhatsApp para continuar la conversación. Si no se abre solo, tocá el botón.</p>
        <div class="mt-6"><a href="<?= e($waUrl) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg">Continuar en WhatsApp →</a></div>
        <div class="mt-4"><a href="<?= e(url('')) ?>" class="text-sm text-celeste font-semibold hover:underline">Volver al inicio</a></div>
        <script>setTimeout(function () { window.location.href = <?= json_encode($waUrl) ?>; }, 1500);</script>
      <?php else: ?>
        <p class="text-gray-txt mt-3">
          <?php if (!empty($agente)): ?>
            Un asesor de Valores (<?= e($agente['nombre']) ?>) se comunicará con vos a la brevedad.
          <?php else: ?>
            Un asesor de Valores se comunicará con vos a la brevedad.
          <?php endif; ?>
        </p>
        <div class="mt-6"><a href="<?= e(url('')) ?>" class="btn btn-primary">Volver al inicio</a></div>
      <?php endif; ?>
    </div>
  </div>
</section>
