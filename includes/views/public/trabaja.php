<?php /** Trabajá con Nosotros. Recibe $error (string|null), $viejos (array), $enviado (bool). */
$viejos = $viejos ?? []; $error = $error ?? null; $enviado = $enviado ?? false;
$v = fn(string $k) => e((string) ($viejos[$k] ?? ''));
?>
<section class="hero-inner has-photo" style="background-image:url('<?= e(url('assets/img/hero-nosotros.webp')) ?>')">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <a href="<?= e(url('nosotros')) ?>">Nosotros</a> <span>/</span> <span class="text-white/80">Trabajá con Nosotros</span></div>
    <h1>Trabajá con Nosotros</h1>
    <p>Somos un equipo apasionado por el mercado de capitales. Si te identificás con nuestra cultura y querés crecer en el sector financiero, queremos conocerte.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:760px">
    <?php if ($enviado): ?>
      <div class="card p-10 text-center animate-fade-up">
        <div class="text-5xl mb-4">✅</div>
        <h2 class="section-title">¡Recibimos tu postulación!</h2>
        <p class="text-gray-txt mt-3">Gracias por tu interés en formar parte de Valores Casa de Bolsa. Revisaremos tus datos y nos pondremos en contacto con vos.</p>
        <div class="mt-6"><a href="<?= e(url('')) ?>" class="btn btn-primary">Volver al inicio</a></div>
      </div>
    <?php else: ?>
      <div class="card p-8 animate-fade-up">
        <div class="section-tag">Sumate</div>
        <h2 class="text-xl font-bold text-blue-inst mt-2 mb-6">Completá este formulario</h2>
        <?php if ($error): ?>
          <div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= e(url('trabaja-con-nosotros/enviar')) ?>" class="space-y-5">
          <?= csrf_campo() ?>
          <!-- honeypot anti-spam -->
          <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">
          <div class="grid md:grid-cols-2 gap-5">
            <div class="form-group"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-input" required maxlength="100" placeholder="Tu nombre" value="<?= $v('nombre') ?>"/></div>
            <div class="form-group"><label class="form-label">Apellido *</label><input type="text" name="apellido" class="form-input" required maxlength="100" placeholder="Tu apellido" value="<?= $v('apellido') ?>"/></div>
          </div>
          <div class="grid md:grid-cols-2 gap-5">
            <div class="form-group"><label class="form-label">Correo electrónico *</label><input type="email" name="email" class="form-input" required maxlength="190" placeholder="tu@email.com" value="<?= $v('email') ?>"/></div>
            <div class="form-group"><label class="form-label">Teléfono *</label><input type="tel" name="telefono" class="form-input" required maxlength="50" placeholder="+595 9XX XXX XXX" value="<?= $v('telefono') ?>"/></div>
          </div>
          <div class="form-group"><label class="form-label">Comentarios *</label><textarea name="comentarios" class="form-input" rows="6" required maxlength="3000" placeholder="Contanos sobre vos, tu experiencia y el área en la que te gustaría trabajar…"><?= $v('comentarios') ?></textarea></div>
          <button type="submit" class="btn btn-primary w-full">Enviar postulación →</button>
        </form>
      </div>
    <?php endif; ?>
  </div>
</section>
