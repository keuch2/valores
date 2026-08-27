-- Migración 2026-08-27 — Oportunidades: el tablero se reemplaza por un PDF
-- diario subido desde el admin. Idempotente.
--   mysql -u<usuario> -p <base_de_datos> < database/migraciones/2026-08-27_oportunidades_pdf.sql
INSERT IGNORE INTO configuracion (clave, valor, grupo)
VALUES ('oportunidades_pdf_id', '', 'oportunidades');
