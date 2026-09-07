-- Migración 2026-09-07 — Bio del presidente actualizada (pedido del cliente). Idempotente.
UPDATE ejecutivos
SET bio = 'Presidente y accionista mayoritario de Valores Casa de Bolsa. Más de 30 años liderando operaciones bursátiles y estructuraciones fiduciarias en el mercado paraguayo.'
WHERE nombre = 'Diego Christian Borja Terán';
