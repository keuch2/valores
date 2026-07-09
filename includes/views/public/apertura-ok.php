<?php /** Confirmación de solicitud. Recibe $numero, $agente. */ ?>
<section class="section" style="min-height:50vh">
  <div class="container text-center" style="max-width:560px">
    <div class="card p-10">
      <div class="text-5xl mb-4">✅</div>
      <h1 class="section-title">¡Solicitud recibida!</h1>
      <p class="text-gray-txt mt-3">Tu número de solicitud es</p>
      <div class="text-3xl font-bold text-blue-inst my-2">#<?= (int) $numero ?></div>
      <p class="text-gray-txt mt-3">
        <?php if (!empty($agente)): ?>
          Un asesor de Valores (<?= e($agente['nombre']) ?>) se comunicará con vos a la brevedad.
        <?php else: ?>
          Un asesor de Valores se comunicará con vos a la brevedad.
        <?php endif; ?>
      </p>
      <div class="mt-6"><a href="<?= e(url('')) ?>" class="btn btn-primary">Volver al inicio</a></div>
    </div>
  </div>
</section>
