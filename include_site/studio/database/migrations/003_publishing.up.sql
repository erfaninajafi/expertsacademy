-- Publishing metadata, slugs and a generic append-only audit trail.
-- Guarded ALTERs keep this migration safe for databases created by the
-- earlier prototype, which already contain the base studio tables.

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='slug');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_pages ADD COLUMN slug varchar(200) NULL AFTER title',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='direction');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_pages ADD COLUMN direction char(3) NOT NULL DEFAULT 'ltr' AFTER locale",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='status');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_pages ADD COLUMN status varchar(16) NOT NULL DEFAULT 'draft' AFTER lock_version",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='published_at');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_pages ADD COLUMN published_at datetime NULL AFTER published_revision_id',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND index_name='idx_studio_pages_status');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_pages ADD KEY idx_studio_pages_status (status, published_at)',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Audit: one append-only table. The prototype's page-scoped events table is
-- generalised instead of duplicated.
SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='entity_type');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_events ADD COLUMN entity_type varchar(32) NOT NULL DEFAULT 'page' AFTER page_id",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='entity_id');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_events ADD COLUMN entity_id char(36) NULL AFTER entity_type',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='action');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_events ADD COLUMN action varchar(64) NOT NULL DEFAULT 'event' AFTER event_type",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='ip_address');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_events ADD COLUMN ip_address varchar(45) NOT NULL DEFAULT '' AFTER payload",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='user_agent');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_events ADD COLUMN user_agent varchar(255) NOT NULL DEFAULT '' AFTER ip_address",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND index_name='idx_studio_events_entity');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_events ADD KEY idx_studio_events_entity (entity_type, entity_id, created_at)',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

CREATE TABLE IF NOT EXISTS tbl_studio_publications (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  page_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  revision_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  locale char(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  path varchar(255) NOT NULL DEFAULT '',
  checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'published',
  published_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  published_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  unpublished_at datetime NULL,
  UNIQUE KEY uq_studio_publication_revision (page_id, revision_id),
  KEY idx_studio_publication_live (page_id, status, published_at),
  CONSTRAINT fk_studio_publication_page FOREIGN KEY (page_id) REFERENCES tbl_studio_pages(id) ON DELETE RESTRICT,
  CONSTRAINT fk_studio_publication_revision FOREIGN KEY (revision_id) REFERENCES tbl_studio_revisions(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
