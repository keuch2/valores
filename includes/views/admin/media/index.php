<?php /** Vista: biblioteca de medios. Recibe $medios, $tipo, $q. */ ?>
<div class="card">
    <h2>Subir archivo</h2>
    <div style="display:flex;gap:24px;flex-wrap:wrap">
        <form method="post" action="<?= e(url('admin/?r=media/subir')) ?>" enctype="multipart/form-data"
              style="flex:1;min-width:280px">
            <?= csrf_campo() ?>
            <div class="form-group">
                <label class="form-label">Archivo (imagen, video, PDF)</label>
                <input class="form-input" type="file" name="archivo" required
                       accept=".jpg,.jpeg,.png,.gif,.webp,.avif,.svg,.mp4,.webm,.pdf">
                <p class="form-hint">Máx. <?= (int) round(UPLOAD_MAX_BYTES / 1048576) ?> MB. Se renombra automáticamente por seguridad.</p>
            </div>
            <button class="btn btn-primary" type="submit">Subir</button>
        </form>

        <form method="post" action="<?= e(url('admin/?r=media/video')) ?>" style="flex:1;min-width:280px">
            <?= csrf_campo() ?>
            <div class="form-group">
                <label class="form-label">…o embeber video por URL (YouTube/Vimeo)</label>
                <input class="form-input" type="url" name="video_url" placeholder="https://www.youtube.com/watch?v=…">
            </div>
            <div class="form-group">
                <label class="form-label">Título (opcional)</label>
                <input class="form-input" type="text" name="titulo">
            </div>
            <button class="btn btn-secondary" type="submit">Agregar video</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= e(url('admin/')) ?>" style="display:flex;gap:8px;align-items:center">
            <input type="hidden" name="r" value="media">
            <select class="form-select" name="tipo" style="width:auto" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                <?php foreach (['imagen'=>'Imágenes','video'=>'Videos','pdf'=>'PDF','otro'=>'Otros'] as $k=>$lbl): ?>
                    <option value="<?= $k ?>" <?= $tipo === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
            <input class="form-input" type="search" name="q" value="<?= e($q ?? '') ?>" placeholder="Buscar por nombre…" style="width:auto">
            <button class="btn btn-ghost btn-sm" type="submit">Filtrar</button>
        </form>
        <span class="form-hint"><?= count($medios) ?> elemento(s)</span>
    </div>

    <?php if (empty($medios)): ?>
        <div class="empty">No hay medios que coincidan.</div>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px">
            <?php foreach ($medios as $m): $u = Media::urlPublica($m); ?>
                <div style="border:1px solid var(--gray-ui);border-radius:8px;overflow:hidden;background:#fff">
                    <div style="height:120px;background:var(--gray-bg);display:flex;align-items:center;justify-content:center;overflow:hidden">
                        <?php if ($m['tipo'] === 'imagen' && empty($m['video_url'])): ?>
                            <img src="<?= e($u) ?>" alt="<?= e($m['alt_text'] ?? '') ?>" style="max-width:100%;max-height:100%;object-fit:cover">
                        <?php elseif ($m['tipo'] === 'video'): ?>
                            <span style="font-size:36px">🎬</span>
                        <?php elseif ($m['tipo'] === 'pdf'): ?>
                            <span style="font-size:36px">📄</span>
                        <?php else: ?>
                            <span style="font-size:36px">📎</span>
                        <?php endif; ?>
                    </div>
                    <div style="padding:10px">
                        <div style="font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="<?= e($m['nombre_original']) ?>">
                            <?= e($m['nombre_original']) ?>
                        </div>
                        <div style="font-size:11px;color:var(--gray-text);margin:4px 0">
                            <span class="badge badge-info"><?= e($m['tipo']) ?></span>
                        </div>

                        <form method="post" action="<?= e(url('admin/?r=media/alt')) ?>" style="margin:6px 0">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                            <input class="form-input" name="alt_text" value="<?= e($m['alt_text'] ?? '') ?>"
                                   placeholder="alt text" style="font-size:12px;padding:5px 8px">
                        </form>

                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <button class="btn btn-ghost btn-sm" type="button"
                                    onclick="navigator.clipboard.writeText('<?= e($u) ?>');this.textContent='¡Copiado!'">URL</button>
                            <form method="post" action="<?= e(url('admin/?r=media/eliminar')) ?>"
                                  onsubmit="return confirm('¿Eliminar este medio? Las referencias quedarán vacías.')">
                                <?= csrf_campo() ?>
                                <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                                <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
