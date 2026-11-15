-- Page Builder v1. Apply only to expertsacademy_dev or an isolated test database.
-- The converter validates the target database before writing any content.
-- These additive tables leave all legacy CMS tables and rows intact.

CREATE TABLE IF NOT EXISTS `tbl_page_builder_layouts` (
  `page_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `locale` char(2) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `mode` varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'builder',
  `structure_mode` varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'independent',
  `revision` bigint unsigned NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_code`, `locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Also upgrades a layout table created by an earlier builder draft.
-- MySQL 8 does not support ADD COLUMN IF NOT EXISTS, so guard it explicitly.
SET @has_structure_mode := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tbl_page_builder_layouts'
    AND COLUMN_NAME = 'structure_mode'
);
SET @add_structure_mode := IF(
  @has_structure_mode = 0,
  'ALTER TABLE `tbl_page_builder_layouts` ADD COLUMN `structure_mode` varchar(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT ''independent'' AFTER `mode`',
  'DO 0'
);
PREPARE builder_stmt FROM @add_structure_mode;
EXECUTE builder_stmt;
DEALLOCATE PREPARE builder_stmt;

CREATE TABLE IF NOT EXISTS `tbl_page_component_instances` (
  `id` char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `page_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `marker` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `type_key` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `locale_scope` varchar(6) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `sort_order` int unsigned NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `schema_version` int unsigned NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_page_locale_order` (`page_code`, `locale_scope`, `deleted_at`, `sort_order`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
