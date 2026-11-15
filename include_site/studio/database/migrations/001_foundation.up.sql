-- Page builder foundation. Additive: no legacy table is touched.
CREATE TABLE IF NOT EXISTS tbl_studio_migrations (
  version varchar(32) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  applied_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_pages (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  title varchar(200) NOT NULL,
  locale char(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  legacy_code varchar(64) CHARACTER SET ascii COLLATE ascii_bin NULL,
  lock_version bigint unsigned NOT NULL DEFAULT 1,
  draft_revision_id char(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  published_revision_id char(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_studio_legacy_locale (legacy_code, locale),
  KEY idx_studio_page_updated (updated_at, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_revisions (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  page_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  revision_number bigint unsigned NOT NULL,
  schema_version int unsigned NOT NULL,
  document json NOT NULL,
  checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_studio_revision_number (page_id, revision_number),
  KEY idx_studio_revision_created (page_id, created_at),
  CONSTRAINT fk_studio_revision_page FOREIGN KEY (page_id) REFERENCES tbl_studio_pages(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_component_versions (
  type_key varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  version int unsigned NOT NULL,
  label varchar(200) NOT NULL,
  schema_document json NOT NULL,
  schema_checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (type_key, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_events (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  page_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  actor_id varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  event_type varchar(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  payload json NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_studio_events_page (page_id, created_at),
  CONSTRAINT fk_studio_event_page FOREIGN KEY (page_id) REFERENCES tbl_studio_pages(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
