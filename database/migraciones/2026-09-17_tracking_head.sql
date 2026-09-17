-- Migración 2026-09-17 — Campo para códigos de tracking (Google, Meta y otros)
-- que se insertan en el <head> de todas las páginas públicas. Idempotente.
INSERT IGNORE INTO configuracion (clave, valor, grupo)
VALUES ('tracking_head', '', 'tracking');
