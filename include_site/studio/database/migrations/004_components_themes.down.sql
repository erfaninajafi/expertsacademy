DROP TABLE IF EXISTS tbl_studio_media_usages;
DROP TABLE IF EXISTS tbl_studio_themes;
DROP TABLE IF EXISTS tbl_studio_component_variants;
DROP TABLE IF EXISTS tbl_studio_components;

SET @has := (SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND index_name='idx_studio_media_checksum');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_media DROP INDEX idx_studio_media_checksum','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='deleted_at');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_media DROP COLUMN deleted_at','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='checksum');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_media DROP COLUMN checksum','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='original_name');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_media DROP COLUMN original_name','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_templates' AND column_name='status');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_templates DROP COLUMN status','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_design_tokens' AND column_name='created_at');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_design_tokens DROP COLUMN created_at','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_design_tokens' AND column_name='created_by');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_design_tokens DROP COLUMN created_by','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_design_tokens' AND column_name='checksum');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_design_tokens DROP COLUMN checksum','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
