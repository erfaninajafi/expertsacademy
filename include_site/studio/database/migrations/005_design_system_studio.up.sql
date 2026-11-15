-- Phase 2: controlled design-system publication and component-studio metadata.
-- Additive: existing Phase 1 tables and rows are untouched.

CREATE TABLE IF NOT EXISTS tbl_studio_theme_drafts (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  name varchar(120) NOT NULL,
  locale char(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  base_theme_id char(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  tokens json NOT NULL,
  checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  version bigint unsigned NOT NULL DEFAULT 1,
  status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'draft',
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  updated_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_studio_theme_drafts_locale (locale, status, updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_theme_publications (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  draft_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  theme_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  tokens_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  tokens_version bigint unsigned NOT NULL,
  locale char(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  published_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  published_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_studio_theme_publications_locale (locale, published_at),
  CONSTRAINT fk_studio_theme_publication_draft FOREIGN KEY (draft_id) REFERENCES tbl_studio_theme_drafts(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_component_meta (
  type_key varchar(64) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'stable',
  tags json NOT NULL,
  docs_md mediumtext NOT NULL,
  a11y_notes text NOT NULL,
  animation json NOT NULL,
  updated_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '',
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_component_presets (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  type_key varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  name varchar(120) NOT NULL,
  variant varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  props json NOT NULL,
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_studio_component_presets_type (type_key, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_component_versions' AND column_name='notes');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_component_versions ADD COLUMN notes text NULL AFTER schema_checksum",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_component_versions' AND column_name='released_at');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_component_versions ADD COLUMN released_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER notes",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Non-page entities (themes, components) also use the audit trail.
SET @nullable := (SELECT IS_NULLABLE FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_events' AND column_name='page_id');
SET @sql := IF(@nullable='NO',
  'ALTER TABLE tbl_studio_events MODIFY page_id char(36) CHARACTER SET ascii COLLATE ascii_bin NULL',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
