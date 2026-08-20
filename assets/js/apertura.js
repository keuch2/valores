/**
 * Apertura de cuenta — flujo simple.
 * Elegir tipo de cuenta muestra el formulario de contacto; el resto lo
 * resuelven la validación nativa del navegador y el servidor.
 */
(function () {
  'use strict';

  var form = document.getElementById('apertura-form');
  if (!form) return;

  // Borrador del wizard viejo: ya no aplica.
  try { localStorage.removeItem('valores_apertura_v1'); } catch (e) {}

  var panel = document.getElementById('ap-form');
  var radios = form.querySelectorAll('input[name="tipo_persona"]');

  function refrescar(scroll) {
    var hay = false;
    radios.forEach(function (r) {
      r.closest('.ap-tipo-btn').classList.toggle('sel', r.checked);
      if (r.checked) hay = true;
    });
    if (hay && panel.style.display === 'none') {
      panel.style.display = '';
      if (scroll) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  radios.forEach(function (r) {
    r.addEventListener('change', function () { refrescar(true); });
  });

  // Repoblado tras un error del servidor: mostrar el formulario de entrada.
  refrescar(false);

  // Evitar doble envío.
  form.addEventListener('submit', function () {
    var btn = document.getElementById('ap-submit');
    if (btn) { btn.disabled = true; btn.textContent = 'Enviando…'; }
  });
})();
