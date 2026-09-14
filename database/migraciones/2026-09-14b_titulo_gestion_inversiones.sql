-- Migración 2026-09-14 (b) — El servicio cambia su título visible a
-- "Gestión profesional de inversiones" (pedido del cliente). El slug se
-- mantiene para no romper la URL ya publicada. Idempotente.
UPDATE servicios SET titulo = 'Gestión profesional de inversiones'
WHERE slug = 'administracion-de-carteras';
