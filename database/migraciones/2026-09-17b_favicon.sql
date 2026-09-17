-- Migración 2026-09-17 (b) — Favicon administrable desde el panel
-- (Configuración → Sitio). Guarda el id del archivo en la biblioteca de
-- medios. Idempotente.
INSERT IGNORE INTO configuracion (clave, valor, grupo)
VALUES ('favicon_media_id', '', 'sitio');
