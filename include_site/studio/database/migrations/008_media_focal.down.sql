SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='focal_y');
SET @sql := IF(@has>0, 'ALTER TABLE tbl_studio_media DROP COLUMN focal_y', 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='focal_x');
SET @sql := IF(@has>0, 'ALTER TABLE tbl_studio_media DROP COLUMN focal_x', 'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
