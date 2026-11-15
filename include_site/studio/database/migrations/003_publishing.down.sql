DROP TABLE IF EXISTS tbl_studio_publications;

SET @has := (SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND index_name='idx_studio_events_entity');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_events DROP INDEX idx_studio_events_entity','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='user_agent');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_events DROP COLUMN user_agent','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='ip_address');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_events DROP COLUMN ip_address','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='action');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_events DROP COLUMN action','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='entity_id');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_events DROP COLUMN entity_id','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='entity_type');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_events DROP COLUMN entity_type','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND index_name='idx_studio_pages_status');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_pages DROP INDEX idx_studio_pages_status','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='published_at');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_pages DROP COLUMN published_at','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='status');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_pages DROP COLUMN status','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='direction');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_pages DROP COLUMN direction','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='slug');
SET @sql := IF(@has=1,'ALTER TABLE tbl_studio_pages DROP COLUMN slug','DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
