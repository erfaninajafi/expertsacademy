-- Studio Full Path Routing
-- Add a public path without changing legacy CMS bindings.

SET @has := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tbl_studio_pages'
      AND COLUMN_NAME = 'full_path'
);

SET @sql := IF(
    @has = 0,
    'ALTER TABLE tbl_studio_pages
     ADD COLUMN full_path VARCHAR(255)
     CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
     NULL AFTER slug',
    'DO 0'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tbl_studio_pages'
      AND INDEX_NAME = 'uq_studio_page_full_path'
);

SET @sql := IF(
    @has = 0,
    'ALTER TABLE tbl_studio_pages
     ADD UNIQUE KEY uq_studio_page_full_path (full_path)',
    'DO 0'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
