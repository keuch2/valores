<?php /** Apertura de cuenta (flujo simple). Recibe $error (string|null) y $viejos (array). */
$viejos = $viejos ?? [];
$error  = $error ?? null;
$tipoSel = (string) ($viejos['tipo_persona'] ?? '');
?>
<section class="hero-inner">
  <div class="container relative z-10">
    <div class="breadcrumb"><a href="<?= e(url('')) ?>">Inicio</a> <span>/</span> <span class="text-white/80">Apertura de Cuenta</span></div>
    <h1>Abrí tu cuenta<br/>de inversión</h1>
    <p>Elegí el tipo de cuenta, dejanos tus datos y seguimos la conversación por WhatsApp con un asesor de Valores.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:760px">
    <form id="apertura-form" method="post" action="<?= e(url('apertura-de-cuenta/enviar')) ?>">
      <?= csrf_campo() ?>
      <!-- honeypot anti-spam -->
      <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">

      <?php if ($error): ?>
        <div class="p-4 mb-6 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700"><?= e($error) ?></div>
      <?php endif; ?>

      <!-- Tipo de cuenta -->
      <div class="card p-8">
        <h2 class="text-xl font-bold text-blue-inst mb-4">¿Cómo querés abrir tu cuenta?</h2>
        <div class="grid md:grid-cols-3 gap-4">
          <label class="ap-tipo-btn card p-6 text-center cursor-pointer">
            <input type="radio" name="tipo_persona" value="fisica" class="sr-only" required <?= $tipoSel === 'fisica' ? 'checked' : '' ?>>
            <div class="text-3xl mb-2">👤</div><div class="font-bold text-blue-inst">Persona Física</div>
            <div class="text-xs text-gray-txt mt-1">Cuenta individual a tu nombre.</div>
          </label>
          <label class="ap-tipo-btn card p-6 text-center cursor-pointer">
            <input type="radio" name="tipo_persona" value="conjunta" class="sr-only" required <?= $tipoSel === 'conjunta' ? 'checked' : '' ?>>
            <div class="text-3xl mb-2">👥</div><div class="font-bold text-blue-inst">Cuenta Conjunta</div>
            <div class="text-xs text-gray-txt mt-1">Dos o más titulares personas físicas.</div>
          </label>
          <label class="ap-tipo-btn card p-6 text-center cursor-pointer">
            <input type="radio" name="tipo_persona" value="juridica" class="sr-only" required <?= $tipoSel === 'juridica' ? 'checked' : '' ?>>
            <div class="text-3xl mb-2">🏢</div><div class="font-bold text-blue-inst">Persona Jurídica</div>
            <div class="text-xs text-gray-txt mt-1">Cuenta a nombre de una empresa.</div>
          </label>
        </div>
      </div>

      <!-- Datos de contacto (se muestra al elegir el tipo) -->
      <div id="ap-form" class="card p-8 mt-6" style="display:none">
        <h2 class="text-xl font-bold text-blue-inst mb-1">Tus datos de contacto</h2>
        <p class="text-sm text-gray-txt mb-6">Con estos datos un asesor te contacta y continúan por WhatsApp.</p>
        <div class="grid md:grid-cols-2 gap-5">
          <div class="form-group md:col-span-2">
            <label class="form-label">Nombre completo *</label>
            <input type="text" name="nombre_completo" class="form-input" required maxlength="200" placeholder="Nombre y apellido" value="<?= e((string) ($viejos['nombre_completo'] ?? '')) ?>"/>
          </div>
          <div class="form-group">
            <label class="form-label">Teléfono *</label>
            <input type="tel" name="telefono" class="form-input" required maxlength="50" placeholder="+595 9XX XXX XXX" value="<?= e((string) ($viejos['telefono'] ?? '')) ?>"/>
          </div>
          <div class="form-group">
            <label class="form-label">Correo electrónico *</label>
            <input type="email" name="email" class="form-input" required maxlength="190" placeholder="tu@email.com" value="<?= e((string) ($viejos['email'] ?? '')) ?>"/>
          </div>
          <div class="form-group md:col-span-2">
            <label class="form-label">Ciudad / País *</label>
            <input type="text" name="ciudad_pais" class="form-input" required maxlength="120" placeholder="Asunción, Paraguay" value="<?= e((string) ($viejos['ciudad_pais'] ?? '')) ?>"/>
          </div>
        </div>
        <fieldset class="form-group mt-6">
          <legend class="form-label">¿Cuál es el rango aproximado que estás considerando invertir? *</legend>
          <p class="text-xs text-gray-txt mb-3">Esta información nos ayudará a brindarte una orientación más personalizada.</p>
          <div class="space-y-2">
            <?php $rangoSel = (string) ($viejos['rango_inversion'] ?? ''); foreach (apertura_rangos() as $k => $lbl): ?>
              <label class="flex items-center gap-3 text-sm text-gray-txt cursor-pointer">
                <input type="radio" name="rango_inversion" value="<?= e($k) ?>" class="w-4 h-4 text-celeste" required <?= $rangoSel === $k ? 'checked' : '' ?>>
                <span><?= e($lbl) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </fieldset>
        <button type="submit" id="ap-submit" class="btn btn-primary w-full mt-6">Enviar y continuar en WhatsApp →</button>
        <p class="text-xs text-gray-txt text-center mt-3">Al enviar aceptás ser contactado por un asesor de Valores Casa de Bolsa.</p>
      </div>
    </form>
  </div>
</section>

<script src="<?= e(url('assets/js/apertura.js')) ?>?v=2"></script>
