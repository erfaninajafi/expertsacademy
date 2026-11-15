ALTER TABLE tbl_studio_templates
  ADD COLUMN category varchar(40) NOT NULL DEFAULT 'general' AFTER name,
  ADD COLUMN description varchar(255) NOT NULL DEFAULT '' AFTER category;

CREATE TABLE IF NOT EXISTS tbl_studio_template_favorites (
  template_id char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  admin_id int unsigned NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (template_id, admin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
