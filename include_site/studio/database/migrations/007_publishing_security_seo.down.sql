DROP TABLE IF EXISTS tbl_studio_media_variants;
DROP TABLE IF EXISTS tbl_studio_rate_limits;
DROP TABLE IF EXISTS tbl_studio_preview_tokens;
DROP TABLE IF EXISTS tbl_studio_redirects;
DROP TABLE IF EXISTS tbl_studio_user_roles;
DROP TABLE IF EXISTS tbl_studio_schedules;

SET @has := (SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND index_name='uq_studio_page_slug');
SET @sql := IF(@has>0, 'ALTER TABLE tbl_studio_pages DROP INDEX uq_studio_page_slug', 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='archived_at');
SET @sql := IF(@has>0, 'ALTER TABLE tbl_studio_pages DROP COLUMN archived_at', 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='scheduled_at');
SET @sql := IF(@has>0, 'ALTER TABLE tbl_studio_pages DROP COLUMN scheduled_at', 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
