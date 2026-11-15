-- Reverses 005. Only Phase 2 objects are dropped.
DROP TABLE IF EXISTS tbl_studio_component_presets;
DROP TABLE IF EXISTS tbl_studio_component_meta;
DROP TABLE IF EXISTS tbl_studio_theme_publications;
DROP TABLE IF EXISTS tbl_studio_theme_drafts;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_component_versions' AND column_name='released_at');
SET @sql := IF(@has=1, 'ALTER TABLE tbl_studio_component_versions DROP COLUMN released_at', 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_component_versions' AND column_name='notes');
SET @sql := IF(@has=1, 'ALTER TABLE tbl_studio_component_versions DROP COLUMN notes', 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
