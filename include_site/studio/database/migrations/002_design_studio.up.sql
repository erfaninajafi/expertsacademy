-- Design tokens, templates, media metadata and review workflow.
CREATE TABLE IF NOT EXISTS tbl_studio_design_tokens (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  version int unsigned NOT NULL,
  name varchar(200) NOT NULL,
  document json NOT NULL,
  checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'system',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_templates (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  version int unsigned NOT NULL,
  name varchar(200) NOT NULL,
  page_schema_version int unsigned NOT NULL,
  document json NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_media (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  storage_key varchar(255) NOT NULL,
  mime_type varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  size_bytes bigint unsigned NOT NULL,
  width int unsigned NULL,
  height int unsigned NULL,
  alt_text varchar(500) NOT NULL DEFAULT '',
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_studio_media_key (storage_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_component_proposals (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  type_key varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  document json NOT NULL,
  status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'submitted',
  review_reference varchar(200) NOT NULL DEFAULT '',
  created_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  reviewed_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  reviewed_at datetime NULL,
  KEY idx_studio_proposal_status (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_component_lifecycle (
  type_key varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  version int unsigned NOT NULL,
  status varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'stable',
  replacement_type varchar(64) CHARACTER SET ascii COLLATE ascii_bin NULL,
  note varchar(1000) NOT NULL DEFAULT '',
  updated_by varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (type_key, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE IF NOT EXISTS tbl_studio_leads (
  id char(36) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
  intent varchar(20) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  name varchar(120) NOT NULL DEFAULT '',
  email varchar(200) NOT NULL,
  program varchar(160) NOT NULL DEFAULT '',
  message text NOT NULL,
  consent_version varchar(32) NOT NULL DEFAULT 'academy-contact-v1',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_studio_leads_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
