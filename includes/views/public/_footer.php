<?php
/** Footer público común. Usa $GLOBALS['_sitio']. */
$sitio = $GLOBALS['_sitio'];
$waNum = preg_replace('/[^0-9]/', '', $sitio['contacto_whatsapp']);
?>
<footer class="footer">
  <div class="container pb-0">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-8">

      <div class="lg:col-span-2">
        <div class="footer-logo">VALORES<span>CASA DE BOLSA</span></div>
        <p class="footer-tagline">Tu dinero trabajando con quienes más saben del mercado de capitales paraguayo.</p>
        <div class="social-links">
          <a href="<?= e($sitio['redes']['linkedin'] ?: '#') ?>" class="social-link" title="LinkedIn">in</a>
          <a href="<?= e($sitio['redes']['facebook'] ?: '#') ?>" class="social-link" title="Facebook">f</a>
          <a href="<?= e($sitio['redes']['twitter'] ?: '#') ?>" class="social-link" title="Twitter">𝕏</a>
          <a href="<?= e($sitio['redes']['instagram'] ?: '#') ?>" class="social-link" title="Instagram">ig</a>
          <a href="<?= e($sitio['redes']['youtube'] ?: '#') ?>" class="social-link" title="YouTube"></a>
        </div>
        <div class="newsletter-strip mt-6">
          <p class="text-white/70 text-xs mb-2 font-semibold">Boletín de oportunidades</p>
          <form class="newsletter-form flex gap-2" method="post" action="<?= e(url('newsletter')) ?>">
            <input type="email" name="email" placeholder="Tu email" class="flex-1 bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-sm text-white placeholder-white/40 focus:outline-none focus:border-celeste"/>
            <button type="submit" class="btn btn-primary btn-sm whitespace-nowrap">Suscribir</button>
          </form>
          <div class="nl-success text-xs text-naranja mt-2 font-semibold" style="display:none">✓ ¡Suscripto!</div>
        </div>
      </div>

      <div>
        <h4>Servicios</h4>
        <ul>
          <?php foreach (Publico::servicios() as $sv): ?>
            <li><a href="<?= e(url('servicios/' . $sv['slug'])) ?>"><?= e($sv['titulo']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div>
        <h4>Invierte</h4>
        <ul>
          <li><a href="<?= e(url('oportunidades?tipo=bono')) ?>">Bonos</a></li>
          <li><a href="<?= e(url('oportunidades?tipo=cda')) ?>">CDAs</a></li>
          <li><a href="<?= e(url('oportunidades?tipo=accion')) ?>">Acciones</a></li>
          <li><a href="<?= e(url('oportunidades')) ?>">Oportunidades</a></li>
          <li><a href="<?= e(url('glosario')) ?>">Glosario</a></li>
        </ul>
      </div>

      <div>
        <h4>Contacto</h4>
        <div class="footer-contact">
          <p><span class="ico"></span><?= e($sitio['contacto_direccion']) ?></p>
          <p><span class="ico"></span><a href="mailto:<?= e($sitio['contacto_email']) ?>"><?= e($sitio['contacto_email']) ?></a></p>
          <p><span class="ico"></span><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $sitio['contacto_telefono'])) ?>"><?= e($sitio['contacto_telefono']) ?></a></p>
          <p><span class="ico"></span><a href="https://wa.me/<?= e($waNum) ?>"><?= e($sitio['contacto_whatsapp']) ?></a></p>
        </div>
        <div class="mt-4">
          <a href="<?= e(url('apertura-de-cuenta')) ?>" class="btn btn-primary btn-sm w-full text-center">Abrir mi Cuenta</a>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container flex flex-col md:flex-row justify-between items-center gap-3">
      <p>© <?= date('Y') ?> Valores Casa de Bolsa S.A. Regulado por la Superintendencia de Valores (SIV) del Banco Central del Paraguay.</p>
      <div class="flex gap-4">
        <a href="#">Política de Privacidad</a>
        <a href="#">Términos y Condiciones</a>
        <a href="#">Normativa SIV</a>
      </div>
    </div>
  </div>
</footer>

<a href="https://wa.me/<?= e($waNum) ?>" class="whatsapp-float" target="_blank" title="Hablar por WhatsApp" aria-label="WhatsApp">
  <svg viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.71.306 1.263.489 1.694.625.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

<script src="<?= e(url('assets/js/main.js')) ?>"></script>
</body>
</html>
