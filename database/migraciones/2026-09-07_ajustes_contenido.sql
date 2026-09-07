-- Migración 2026-09-07 — Ajustes de contenido (pedido del cliente). Idempotente.
-- 1) Servicio "Análisis Económico y Financiero" desactivado (sale de /servicios, menú, footer y home).
UPDATE servicios SET activo = 0 WHERE slug = 'analisis-financiero';
-- 2) Bio de la Directora Titular.
UPDATE ejecutivos
SET bio = 'Directora titular especializada en gestión operativa, optimización de procesos e implementación de sistemas internos de información y proyectos de tecnología. Lidera iniciativas estratégicas orientadas a la eficiencia, innovación y transformación operativa de la organización.'
WHERE nombre = 'Yanina Monges Chávez';
