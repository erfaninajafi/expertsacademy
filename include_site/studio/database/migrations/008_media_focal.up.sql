SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='focal_x');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_media ADD COLUMN focal_x tinyint unsigned NOT NULL DEFAULT 50 AFTER alt_text',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='focal_y');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_media ADD COLUMN focal_y tinyint unsigned NOT NULL DEFAULT 50 AFTER focal_x',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
