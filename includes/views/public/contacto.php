<?php /** Contacto. Recibe $faqs. */ $sitio = $GLOBALS['_sitio']; $wa = preg_replace('/[^0-9]/', '', $sitio['contacto_whatsapp']); ?>
<section class="hero-inner" style="background:var(--color-blue-inst);color:#fff">
  <div class="container py-14">
    <div class="section-tag" style="color:var(--color-naranja)">Contacto</div>
    <h1 class="text-3xl md:text-4xl font-bold mt-2">Hablemos de tus inversiones</h1>
  </div>
</section>

<section class="section">
  <div class="container grid lg:grid-cols-2 gap-16">
    <!-- Formulario -->
    <div class="animate-fade-up">
      <div class="section-tag">Escríbenos</div>
      <h2 class="section-title mt-2">Envíanos un mensaje</h2>
      <form id="contact-form" class="mt-8 space-y-5" method="post" action="<?= e(url('contacto/enviar')) ?>" novalidate>
        <?= csrf_campo() ?>
        <!-- honeypot anti-spam -->
        <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">
        <div class="grid md:grid-cols-2 gap-5">
          <div class="form-group"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-input" required/></div>
          <div class="form-group"><label class="form-label">Apellido *</label><input type="text" name="apellido" class="form-input" required/></div>
        </div>
        <div class="form-group"><label class="form-label">Correo electrónico *</label><input type="email" name="email" class="form-input" required/></div>
        <div class="form-group"><label class="form-label">Teléfono / WhatsApp</label><input type="tel" name="telefono" class="form-input"/></div>
        <div class="form-group"><label class="form-label">Mensaje *</label><textarea name="mensaje" class="form-input" rows="5" required></textarea></div>
        <div class="flex items-start gap-3">
          <input type="checkbox" id="acepto-contacto" class="mt-1 w-4 h-4 text-celeste rounded"/>
          <label for="acepto-contacto" class="text-sm text-gray-txt">Acepto la Política de Privacidad.</label>
        </div>
        <button type="submit" id="contact-submit" class="btn btn-primary w-full">Enviar mensaje →</button>
        <div id="contact-success" class="hidden text-center p-5 bg-green-50 rounded-xl border border-green-200">
          <div class="font-bold text-green-800 mb-1">¡Mensaje enviado!</div>
          <div class="text-sm text-green-700">Un asesor de Valores se comunicará contigo pronto.</div>
        </div>
      </form>
    </div>

    <!-- Datos de contacto (dinámicos) -->
    <div class="animate-fade-up animate-delay-2">
      <div class="section-tag">Datos de contacto</div>
      <h2 class="section-title mt-2">Encuéntranos así</h2>
      <div class="mt-8 space-y-5">
        <div class="flex gap-4 p-5 bg-celeste-soft rounded-xl border border-celeste/20">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm"><i class="fa-solid fa-location-dot text-xl text-blue-inst"></i></div>
          <div><div class="font-bold text-blue-inst mb-1">Oficinas</div><div class="text-sm text-gray-txt"><?= e($sitio['contacto_direccion']) ?></div></div>
        </div>
        <div class="flex gap-4 p-5 bg-celeste-soft rounded-xl border border-celeste/20">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm"><i class="fa-solid fa-phone text-xl text-blue-inst"></i></div>
          <div><div class="font-bold text-blue-inst mb-1">Teléfono</div><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $sitio['contacto_telefono'])) ?>" class="text-sm text-celeste font-semibold hover:underline"><?= e($sitio['contacto_telefono']) ?></a></div>
        </div>
        <div class="flex gap-4 p-5 bg-celeste-soft rounded-xl border border-celeste/20">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm"><i class="fa-brands fa-whatsapp text-2xl text-blue-inst"></i></div>
          <div><div class="font-bold text-blue-inst mb-1">WhatsApp</div><a href="https://wa.me/<?= e($wa) ?>" target="_blank" class="text-sm text-celeste font-semibold hover:underline"><?= e($sitio['contacto_whatsapp']) ?></a></div>
        </div>
        <div class="flex gap-4 p-5 bg-celeste-soft rounded-xl border border-celeste/20">
          <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm"><i class="fa-regular fa-envelope text-xl text-blue-inst"></i></div>
          <div><div class="font-bold text-blue-inst mb-1">Email</div><a href="mailto:<?= e($sitio['contacto_email']) ?>" class="text-sm text-celeste font-semibold hover:underline"><?= e($sitio['contacto_email']) ?></a></div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($faqs)): ?>
<section class="section bg-gray">
  <div class="container max-w-3xl">
    <div class="text-center mb-10"><div class="section-tag">Ayuda</div><h2 class="section-title mt-2">Preguntas frecuentes</h2></div>
    <?php foreach ($faqs as $f): ?>
      <div class="faq-item">
        <button class="faq-trigger"><?= e($f['pregunta']) ?> <svg class="w-5 h-5 chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></button>
        <div class="faq-content"><?= e($f['respuesta']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
