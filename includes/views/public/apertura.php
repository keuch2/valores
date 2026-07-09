<?php /** Wizard de apertura. Recibe $pasosFisica, $pasosJuridica. */ ?>
<section class="hero-inner" style="background:var(--color-blue-inst);color:#fff">
  <div class="container py-12">
    <div class="section-tag" style="color:var(--color-naranja)">Apertura de cuenta</div>
    <h1 class="text-3xl md:text-4xl font-bold mt-2">Abrí tu cuenta en Valores</h1>
    <p class="text-white/80 mt-3 max-w-2xl">Completá el formulario paso a paso. Tus datos se guardan en tu navegador mientras avanzás, y se envían cifrados al confirmar.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:760px">

    <!-- Barra de progreso -->
    <div class="ap-progress" id="ap-progress" style="display:none">
      <div class="ap-progress-bar"><div class="ap-progress-fill" id="ap-progress-fill"></div></div>
      <div class="ap-progress-label"><span id="ap-step-num">1</span> / <span id="ap-step-total">1</span> — <span id="ap-step-title"></span></div>
    </div>

    <form id="apertura-form" method="post" action="<?= e(url('apertura-de-cuenta/enviar')) ?>" enctype="multipart/form-data" novalidate>
      <?= csrf_campo() ?>
      <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">
      <input type="hidden" name="tipo_persona" id="tipo_persona" value="">

      <!-- PASO 0: tipo de persona -->
      <div class="ap-step" data-rama="all" data-step="0">
        <div class="card">
          <h2 class="text-xl font-bold text-blue-inst mb-4">¿Cómo querés abrir tu cuenta?</h2>
          <div class="grid md:grid-cols-3 gap-4">
            <button type="button" class="ap-tipo-btn card p-6 text-center" data-tipo="fisica">
              <div class="text-3xl mb-2">👤</div><div class="font-bold text-blue-inst">Persona Física</div>
              <div class="text-xs text-gray-txt mt-1">Cuenta individual a tu nombre.</div>
            </button>
            <button type="button" class="ap-tipo-btn card p-6 text-center" data-tipo="conjunta">
              <div class="text-3xl mb-2">👥</div><div class="font-bold text-blue-inst">Cuenta Conjunta</div>
              <div class="text-xs text-gray-txt mt-1">Dos o más titulares personas físicas.</div>
            </button>
            <button type="button" class="ap-tipo-btn card p-6 text-center" data-tipo="juridica">
              <div class="text-3xl mb-2">🏢</div><div class="font-bold text-blue-inst">Persona Jurídica</div>
              <div class="text-xs text-gray-txt mt-1">Cuenta a nombre de una empresa.</div>
            </button>
          </div>
        </div>
      </div>

      <!-- Pasos Persona Física / Conjunta (comparten los campos base) -->
      <?php foreach ($pasosFisica as $i => $paso): ?>
        <div class="ap-step" data-rama="fisica conjunta" data-step="<?= $i + 1 ?>" style="display:none">
          <div class="card">
            <h2 class="text-xl font-bold text-blue-inst mb-4"><?= e($paso['titulo']) ?></h2>
            <?php foreach ($paso['campos'] as $c) { require APP_ROOT . '/includes/views/public/_campo_apertura.php'; } ?>

            <?php if ($paso['clave'] === 'datos_personales'): ?>
              <!-- Anexo de titulares adicionales (solo Cuenta Conjunta) -->
              <div id="ap-titulares" data-conjunta-only style="display:none;margin-top:16px;border-top:1px dashed var(--color-gray-ui);padding-top:16px">
                <h3 class="font-bold text-blue-inst mb-2">Titulares adicionales</h3>
                <p class="form-hint mb-3">Agregá los demás titulares de la cuenta conjunta (nombre, documento y correo).</p>
                <div id="ap-titulares-lista"></div>
                <button type="button" class="btn btn-ghost btn-sm" id="ap-add-titular">+ Agregar titular</button>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- Pasos Persona Jurídica (provisional) -->
      <?php foreach ($pasosJuridica as $i => $paso): ?>
        <div class="ap-step" data-rama="juridica" data-step="<?= $i + 1 ?>" style="display:none">
          <div class="card">
            <h2 class="text-xl font-bold text-blue-inst mb-4"><?= e($paso['titulo']) ?></h2>
            <?php if (!empty($paso['repeater'])): ?>
              <div class="ap-repeater" data-clave="<?= e($paso['clave']) ?>" data-min="<?= (int) $paso['min'] ?>">
                <div class="ap-repeater-list"></div>
                <button type="button" class="btn btn-ghost btn-sm ap-repeater-add">+ Agregar</button>
                <template class="ap-repeater-tpl">
                  <div class="ap-repeater-row card p-4 mb-3">
                    <?php foreach ($paso['campos'] as $c) { $prefijo = $paso['clave'] . '[__i__]'; require APP_ROOT . '/includes/views/public/_campo_apertura.php'; } ?>
                    <button type="button" class="btn btn-danger btn-sm ap-repeater-del">Quitar</button>
                  </div>
                </template>
              </div>
            <?php else: ?>
              <?php foreach ($paso['campos'] as $c) { $prefijo = ''; require APP_ROOT . '/includes/views/public/_campo_apertura.php'; } ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <!-- Paso final: firma + revisión (común) -->
      <div class="ap-step" data-rama="fisica conjunta juridica" data-step="99" style="display:none">
        <div class="card">
          <h2 class="text-xl font-bold text-blue-inst mb-4">Firma y confirmación</h2>
          <div class="form-group">
            <label class="form-label" for="ap-firma">Imagen de tu firma (JPG o PNG) *</label>
            <input class="form-input" type="file" id="ap-firma" name="firma" accept="image/jpeg,image/png" data-req="1">
            <p class="form-hint">Subí una foto o escaneo de tu firma. Se almacena de forma restringida.</p>
            <div id="ap-firma-preview" style="margin-top:10px"></div>
          </div>
          <div id="ap-revision" class="bg-gray-bg rounded-xl p-4 text-sm" style="max-height:260px;overflow:auto"></div>
        </div>
      </div>

      <!-- Navegación -->
      <div class="flex justify-between mt-6" id="ap-nav" style="display:none">
        <button type="button" class="btn btn-ghost" id="ap-prev">← Atrás</button>
        <button type="button" class="btn btn-primary" id="ap-next">Siguiente →</button>
        <button type="submit" class="btn btn-primary" id="ap-submit" style="display:none">Enviar solicitud</button>
      </div>
    </form>
  </div>
</section>

<script src="<?= e(url('assets/js/apertura.js')) ?>"></script>
