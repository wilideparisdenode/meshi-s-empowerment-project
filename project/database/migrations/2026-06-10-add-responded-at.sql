-- Migration: add responded_at to mentor_requests
-- Run: mysql -u root -p smart_girl_empowerment < 2026-06-10-add-responded-at.sql

ALTER TABLE mentor_requests
  ADD COLUMN responded_at TIMESTAMP NULL DEFAULT NULL AFTER created_at;

-- Optionally, backfill responded_at for non-pending rows (if any):
-- UPDATE mentor_requests SET responded_at = created_at WHERE status != 'pending' AND responded_at IS NULL;
