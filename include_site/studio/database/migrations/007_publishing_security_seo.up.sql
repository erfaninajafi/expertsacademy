-- Phase 4: publishing states, scheduler, permissions, redirects, preview tokens,
-- image variants and rate limiting. Guarded ALTERs keep older databases safe.

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='scheduled_at');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_pages ADD COLUMN scheduled_at datetime NULL AFTER published_at',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND column_name='archived_at');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_pages ADD COLUMN archived_at datetime NULL AFTER scheduled_at',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @has := (SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema=DATABASE() AND table_name='tbl_studio_pages' AND index_name='uq_studio_page_slug');
SET @sql := IF(@has=0,
  'ALTER TABLE tbl_studio_pages ADD UNIQUE KEY uq_studio_page_slug (slug, locale)',
  'DO 0');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

CREATE TABLE IF NOT EXISTS tbl_studio_schedules (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  page_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  revision_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  publish_at datetime NOT NULL,
  timezone varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'UTC',
  status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'pending',
  attempts int unsigned NOT NULL DEFAULT 0,
  last_error text NULL,
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  processed_at datetime NULL,
  KEY idx_studio_schedule_due (status, publish_at),
  KEY idx_studio_schedule_page (page_id, created_at),
  CONSTRAINT fk_studio_schedule_page FOREIGN KEY (page_id) REFERENCES tbl_studio_pages(id) ON DELETE RESTRICT,
  CONSTRAINT fk_studio_schedule_revision FOREIGN KEY (revision_id) REFERENCES tbl_studio_revisions(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_user_roles (
  user_id varchar(64) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  role varchar(32) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  updated_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_redirects (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  from_path varchar(255) NOT NULL,
  to_path varchar(255) NOT NULL,
  locale char(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  status_code smallint unsigned NOT NULL DEFAULT 301,
  active tinyint(1) NOT NULL DEFAULT 1,
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_studio_redirect_path (locale, from_path),
  KEY idx_studio_redirect_active (active, locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_preview_tokens (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  page_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  revision_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  token_hash char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  expires_at datetime NOT NULL,
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_studio_preview_token (token_hash),
  KEY idx_studio_preview_expiry (expires_at),
  CONSTRAINT fk_studio_preview_page FOREIGN KEY (page_id) REFERENCES tbl_studio_pages(id) ON DELETE CASCADE,
  CONSTRAINT fk_studio_preview_revision FOREIGN KEY (revision_id) REFERENCES tbl_studio_revisions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_rate_limits (
  bucket varchar(160) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  window_start bigint unsigned NOT NULL,
  hits int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_media_variants (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  media_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  format varchar(8) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  width int unsigned NOT NULL,
  height int unsigned NOT NULL,
  storage_key varchar(255) NOT NULL,
  bytes int unsigned NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_studio_media_variant (media_id, format, width),
  CONSTRAINT fk_studio_variant_media FOREIGN KEY (media_id) REFERENCES tbl_studio_media(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
