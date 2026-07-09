/* ============================================================
   Valores CMS — JS del panel admin
   Incluye el modal selector de medios reutilizable.
   ============================================================ */
(function () {
  'use strict';

  // Base URL de la app (inyectada por el layout en window.ADMIN_BASE).
  var BASE = window.ADMIN_BASE || '/valores-app/';

  /**
   * Abre el selector de medios. Al elegir, ejecuta onSelect({id,url,alt,nombre}).
   * Uso: MediaPicker.abrir({ tipo:'imagen', onSelect: function(m){...} })
   */
  var MediaPicker = {
    abrir: function (opts) {
      opts = opts || {};
      var tipo = opts.tipo || '';
      var overlay = document.createElement('div');
      overlay.className = 'mp-overlay';
      overlay.innerHTML =
        '<div class="mp-modal">' +
          '<div class="mp-head">' +
            '<strong>Seleccionar medio</strong>' +
            '<input type="search" class="mp-search form-input" placeholder="Buscar…" style="max-width:220px">' +
            '<button type="button" class="mp-close btn btn-ghost btn-sm">Cerrar</button>' +
          '</div>' +
          '<div class="mp-grid">Cargando…</div>' +
        '</div>';
      document.body.appendChild(overlay);

      var grid = overlay.querySelector('.mp-grid');
      var search = overlay.querySelector('.mp-search');

      function cargar(q) {
        var url = BASE + 'admin/?r=media/api';
        if (tipo) url += '&tipo=' + encodeURIComponent(tipo);
        if (q) url += '&q=' + encodeURIComponent(q);
        grid.textContent = 'Cargando…';
        fetch(url, { credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            grid.innerHTML = '';
            if (!data.items || !data.items.length) { grid.textContent = 'Sin resultados.'; return; }
            data.items.forEach(function (m) {
              var cell = document.createElement('div');
              cell.className = 'mp-cell';
              var thumb = (m.tipo === 'imagen')
                ? '<img src="' + m.url + '" alt="">'
                : '<span class="mp-ico">' + (m.tipo === 'video' ? '🎬' : m.tipo === 'pdf' ? '📄' : '📎') + '</span>';
              cell.innerHTML = '<div class="mp-thumb">' + thumb + '</div><div class="mp-name">' + escapeHtml(m.nombre) + '</div>';
              cell.addEventListener('click', function () {
                if (typeof opts.onSelect === 'function') opts.onSelect(m);
                cerrar();
              });
              grid.appendChild(cell);
            });
          })
          .catch(function () { grid.textContent = 'Error al cargar la biblioteca.'; });
      }

      function cerrar() { if (overlay.parentNode) overlay.parentNode.removeChild(overlay); }
      function escapeHtml(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

      overlay.querySelector('.mp-close').addEventListener('click', cerrar);
      overlay.addEventListener('click', function (e) { if (e.target === overlay) cerrar(); });
      var t;
      search.addEventListener('input', function () { clearTimeout(t); t = setTimeout(function () { cargar(search.value); }, 250); });

      cargar('');
    }
  };

  window.MediaPicker = MediaPicker;

  /**
   * Conecta los botones [data-media-picker] con un input hidden + preview.
   * <button data-media-picker data-target="#foto_id" data-preview="#foto_prev" data-tipo="imagen">
   */
  /* --- Editor richtext liviano (contenteditable + toolbar) --- */
  document.querySelectorAll('.rt-editor').forEach(function (ed) {
    var hidden = document.getElementById(ed.getAttribute('data-target'));
    var toolbar = ed.previousElementSibling;
    function sync() { if (hidden) hidden.value = ed.innerHTML; }
    ed.addEventListener('input', sync);
    ed.addEventListener('blur', sync);
    if (toolbar && toolbar.classList.contains('rt-toolbar')) {
      toolbar.addEventListener('click', function (e) {
        var b = e.target.closest('[data-cmd]');
        if (!b) return;
        e.preventDefault();
        var cmd = b.getAttribute('data-cmd');
        if (cmd === 'createLink') {
          var u = prompt('URL del enlace:');
          if (u) document.execCommand('createLink', false, u);
        } else {
          document.execCommand(cmd, false, null);
        }
        ed.focus(); sync();
      });
    }
  });

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-media-picker]');
    if (!btn) return;
    e.preventDefault();
    MediaPicker.abrir({
      tipo: btn.getAttribute('data-tipo') || '',
      onSelect: function (m) {
        var target = document.querySelector(btn.getAttribute('data-target'));
        if (target) target.value = m.id;
        var prevSel = btn.getAttribute('data-preview');
        if (prevSel) {
          var prev = document.querySelector(prevSel);
          if (prev) {
            prev.innerHTML = (m.tipo === 'imagen')
              ? '<img src="' + m.url + '" alt="" style="max-height:80px;border-radius:6px">'
              : '<span class="badge badge-info">' + m.tipo + ': ' + (m.nombre || '') + '</span>';
          }
        }
      }
    });
  });
})();
