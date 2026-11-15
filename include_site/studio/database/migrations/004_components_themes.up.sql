-- Component definitions/variants, themes, media references and template status.

CREATE TABLE IF NOT EXISTS tbl_studio_components (
  type_key varchar(64) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  label varchar(200) NOT NULL,
  category varchar(40) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  description varchar(400) NOT NULL DEFAULT '',
  current_version int unsigned NOT NULL,
  is_container tinyint(1) NOT NULL DEFAULT 1,
  status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'stable',
  fingerprint char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  synced_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_studio_components_category (category, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_component_variants (
  type_key varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  version int unsigned NOT NULL,
  variant_key varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  label varchar(120) NOT NULL,
  sort_order int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (type_key, version, variant_key),
  CONSTRAINT fk_studio_variant_version FOREIGN KEY (type_key, version)
    REFERENCES tbl_studio_component_versions(type_key, version) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_themes (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  version int unsigned NOT NULL,
  name varchar(200) NOT NULL,
  locale char(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'en',
  tokens_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  tokens_version int unsigned NOT NULL,
  status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'active',
  is_default tinyint(1) NOT NULL DEFAULT 0,
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id, version),
  KEY idx_studio_themes_locale (locale, is_default),
  CONSTRAINT fk_studio_theme_tokens FOREIGN KEY (tokens_id, tokens_version)
    REFERENCES tbl_studio_design_tokens(id, version) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_media_usages (
  revision_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  component_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  media_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  role varchar(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'image',
  PRIMARY KEY (revision_id, component_id, media_id),
  KEY idx_studio_usage_media (media_id),
  CONSTRAINT fk_studio_usage_revision FOREIGN KEY (revision_id)
    REFERENCES tbl_studio_revisions(id) ON DELETE CASCADE,
  CONSTRAINT fk_studio_usage_media FOREIGN KEY (media_id)
    REFERENCES tbl_studio_media(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Extend token versions with integrity metadata.
SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_design_tokens' AND column_name='checksum');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_design_tokens ADD COLUMN checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' AFTER document",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_design_tokens' AND column_name='created_by');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_design_tokens ADD COLUMN created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'system' AFTER checksum",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_design_tokens' AND column_name='created_at');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_design_tokens ADD COLUMN created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER created_by',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_templates' AND column_name='status');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_templates ADD COLUMN status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'active' AFTER page_schema_version",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- Media metadata needed for safe serving and deduplication.
SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='original_name');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_media ADD COLUMN original_name varchar(255) NOT NULL DEFAULT '' AFTER storage_key",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='checksum');
SET @sql := IF(@has=0,
  "ALTER TABLE tbl_studio_media ADD COLUMN checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' AFTER size_bytes",
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND column_name='deleted_at');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_media ADD COLUMN deleted_at datetime NULL AFTER created_at',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_media' AND index_name='idx_studio_media_checksum');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_media ADD KEY idx_studio_media_checksum (checksum)',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
