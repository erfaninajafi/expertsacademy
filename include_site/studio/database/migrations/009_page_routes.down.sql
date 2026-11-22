-- WARNING: Rolling back removes stored public paths.

ALTER TABLE tbl_studio_pages
    DROP INDEX uq_studio_page_full_path,
    DROP COLUMN full_path;
