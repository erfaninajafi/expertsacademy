# SQL dump schema reference

This is the DDL from `site.sql`, not the complete live schema. The dump also contains data rows; those are intentionally omitted here. There are no declared foreign keys in the dump. SQL types and constraints below are transcribed from CREATE TABLE declarations.

Tables in dump: **25**. Current development tables: **27**, as read on 2026-11-01. The historical declarations below are preserved; [the live metadata snapshot](DEVELOPMENT_SCHEMA_2026-11-01.json) and [Builder supplement](#builder-schema-added-on-2026-10-27) cover the later state.

## `tbl_ads`

Source: `site.sql:25`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_ads_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_ads_code` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_title` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_width` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_height` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_link` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_img` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_lng` | `varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_section` | `varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_status` | `varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_ads_order` | `int NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_ads_id`)`

## `tbl_categories`

Source: `site.sql:58`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_categories_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_categories_code` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_categories_parentid` | `varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_categories_type` | `int DEFAULT NULL` |
| `tbl_categories_title` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_categories_img` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_categories_level` | `int DEFAULT NULL` |
| `tbl_categories_lng` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_categories_order` | `int DEFAULT NULL` |
| `tbl_categories_catcode` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_categories_text` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_categories_status` | `int DEFAULT NULL` |
| `tbl_categories_url` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_categories_design` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_categories_keyword` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_categories_discription` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_categories_index` | `int DEFAULT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_categories_id`)`

## `tbl_certificate`

Source: `site.sql:97`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_certificate_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_certificate_title` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_certificate_price` | `int NOT NULL` |
| `tbl_certificate_workshop` | `int NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_certificate_id`)`

## `tbl_class`

Source: `site.sql:123`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_class_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_class_cat` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_title` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_classcode` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_code` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_number` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_price` | `int NOT NULL` |
| `tbl_class_teacher` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_img` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_status` | `int NOT NULL` |
| `tbl_class_dateadd` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_admin` | `int NOT NULL` |
| `tbl_class_parentcode` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_content` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_special` | `int NOT NULL` |
| `tbl_class_order` | `int NOT NULL` |
| `tbl_class_update` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_certificate` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_question` | `int NOT NULL` |
| `tbl_class_logo` | `varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_mortabet` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_sath` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_pish` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_time` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_qmodir` | `int NOT NULL` |
| `tbl_class_tamrin` | `int NOT NULL` |
| `tbl_class_emtehan` | `int NOT NULL` |
| `tbl_class_contentend` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_class_modelclass` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_class_id`)`

## `tbl_comment`

Source: `site.sql:174`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_comment_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_comment_user` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_comment_class` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_comment_date` | `varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_comment_status` | `int NOT NULL` |
| `tbl_comment_msg` | `varchar(750) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_comment_textadmin` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_comment_id`)`

## `tbl_content`

Source: `site.sql:203`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_content_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_content_type` | `int DEFAULT NULL` |
| `tbl_content_code` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_catid` | `varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_title` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_text` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_img` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_file` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_date` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_fld1` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_fld2` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_fld3` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_fld4` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_fld5` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_tag` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_brandname` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_brandcat` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_content_top` | `int DEFAULT NULL` |
| `tbl_content_slide` | `int DEFAULT NULL` |
| `tbl_content_url` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_keyword` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_discription` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_pageurl` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_order` | `int DEFAULT NULL` |
| `tbl_content_color` | `varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_position` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_content_section` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_content_parent` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_content_id`)`

## `tbl_course`

Source: `site.sql:253`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=528 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_course_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_course_cat` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_title` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_coursecode` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_code` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_number` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_price` | `int NOT NULL` |
| `tbl_course_teacher` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_img` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_status` | `int NOT NULL` |
| `tbl_course_dateadd` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_admin` | `int NOT NULL` |
| `tbl_course_parentcode` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_content` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_special` | `int NOT NULL` |
| `tbl_course_order` | `int NOT NULL` |
| `tbl_course_update` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_certificate` | `int NOT NULL` |
| `tbl_course_class` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_classcode` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_icon` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_course_free` | `int NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_course_id`)`

## `tbl_courseparent`

Source: `site.sql:297`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_courseparent_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_courseparent_cat` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_title` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_coursecode` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_code` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_number` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_price` | `int NOT NULL` |
| `tbl_courseparent_teacher` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_img` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_status` | `int NOT NULL` |
| `tbl_courseparent_dateadd` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_admin` | `int NOT NULL` |
| `tbl_courseparent_parentcode` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_content` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_special` | `int NOT NULL` |
| `tbl_courseparent_order` | `int NOT NULL` |
| `tbl_courseparent_update` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_certificate` | `int NOT NULL` |
| `tbl_courseparent_class` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_classcode` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_icon` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_courseparent_free` | `int NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_courseparent_id`)`

## `tbl_form`

Source: `site.sql:341`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_form_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_form_fld1` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld2` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld3` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld4` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld5` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld6` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld7` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld8` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld9` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld10` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld11` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld12` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_fld13` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_form_date` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_form_section` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_form_lng` | `varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_form_status` | `int DEFAULT NULL` |
| `tbl_form_file` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_form_kind` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_form_model` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_form_id`)`

## `tbl_logo`

Source: `site.sql:384`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_logo_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_logo_title` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_logo_img` | `varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_logo_url` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_logo_id`)`

## `tbl_menu`

Source: `site.sql:410`. Table options: `ENGINE=MyISAM AUTO_INCREMENT=271 DEFAULT CHARSET=latin1`.

| Column | SQL definition |
| --- | --- |
| `tbl_menu_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_menu_code` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_menu_parentid` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_menu_title` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_menu_link` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_menu_lng` | `varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_menu_order` | `int DEFAULT NULL` |
| `tbl_menu_discount` | `int DEFAULT NULL` |
| `tbl_menu_style` | `int DEFAULT NULL` |
| `tbl_menu_pro` | `int DEFAULT NULL` |
| `tbl_menu_color` | `varchar(45) DEFAULT NULL` |
| `tbl_menu_textcolor` | `varchar(45) DEFAULT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_menu_id`)`

## `tbl_menufooter`

Source: `site.sql:444`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=latin1`.

| Column | SQL definition |
| --- | --- |
| `tbl_menufooter_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_menufooter_code` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_menufooter_parentid` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_menufooter_title` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_menufooter_link` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_menufooter_lng` | `varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_menufooter_order` | `int DEFAULT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_menufooter_id`)`

## `tbl_pages`

Source: `site.sql:473`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_pages_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_pages_code` | `varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_pages_title` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_pages_text` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_pages_lng` | `varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_pages_time` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_pages_id`)`

## `tbl_question`

Source: `site.sql:501`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_question_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_question_title` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_question_answer` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_question_status` | `int NOT NULL` |
| `tbl_question_dateadd` | `varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_question_dateupdate` | `varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_question_disable` | `int NOT NULL` |
| `tbl_question_order` | `int NOT NULL` |
| `tbl_question_parent` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_question_sub` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_question_subsub` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_question_id`)`

## `tbl_rateuser`

Source: `site.sql:534`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_rateuser_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_rateuser_user` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_rateuser_product` | `int DEFAULT NULL` |
| `tbl_rateuser_date` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_rateuser_id`)`

## `tbl_rating`

Source: `site.sql:560`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci`.

| Column | SQL definition |
| --- | --- |
| `rating_id` | `int NOT NULL AUTO_INCREMENT` |
| `post_id` | `int DEFAULT NULL` |
| `rating_number` | `int DEFAULT NULL` |
| `total_points` | `int DEFAULT NULL` |
| `created` | `datetime DEFAULT NULL` |
| `modified` | `datetime DEFAULT NULL` |
| `status` | `tinyint(1) DEFAULT NULL COMMENT '1 = Block, 0 = Unblock'` |

Keys and indexes:

- `PRIMARY KEY (`rating_id`)`

## `tbl_register`

Source: `site.sql:589`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=2678 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_register_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_register_class` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_track` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_user` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_date` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_res` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_status` | `int NOT NULL` |
| `tbl_register_price` | `int NOT NULL` |
| `tbl_register_view` | `int NOT NULL` |
| `tbl_register_dateupdate` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_adminupdate` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_admin` | `int NOT NULL` |
| `tbl_register_pish` | `int NOT NULL` |
| `tbl_register_portfolio` | `int NOT NULL` |
| `tbl_register_discount` | `int NOT NULL` |
| `tbl_register_datepayment` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_free` | `int NOT NULL` |
| `tbl_register_certificate` | `int NOT NULL` |
| `tbl_register_discountvalue` | `int NOT NULL` |
| `tbl_register_discountcode` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_discountfinalprice` | `int NOT NULL` |
| `tbl_register_pricefinal` | `int NOT NULL` |
| `tbl_register_priceclass` | `int NOT NULL` |
| `tbl_register_pricecer` | `int NOT NULL` |
| `tbl_register_model` | `int NOT NULL` |
| `tbl_register_workshop` | `int NOT NULL` |
| `tbl_register_confirm` | `int NOT NULL` |
| `tbl_register_confirmdate` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_register_discountmodel` | `int NOT NULL` |
| `tbl_register_sazemani` | `int NOT NULL` |
| `tbl_register_trackerbank` | `varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_register_id`)`

## `tbl_section`

Source: `site.sql:642`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=364 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_section_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_section_title` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_brand` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_brand_name` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_category` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_category_name` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_afzayesh` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_link` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_section_dateadd` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_dateupdate` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_code` | `varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_section_qu` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL` |
| `tbl_section_text` | `longtext COLLATE utf8mb3_bin` |

Keys and indexes:

- `PRIMARY KEY (`tbl_section_id`)`

## `tbl_seo`

Source: `site.sql:677`. Table options: `ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin ROW_FORMAT=DYNAMIC`.

| Column | SQL definition |
| --- | --- |
| `tbl_seo_webmaster` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_seo_analysis` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

## `tbl_setting`

Source: `site.sql:700`. Table options: `ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_setting_title_fa` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_setting_keywords` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_setting_des` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_setting_url` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_setting_email` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_setting_title_en` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_setting_fld1` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_setting_fld2` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_setting_fld3` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_setting_noteindex` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_setting_note` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_setting_twitter` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_setting_facebook` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_setting_telegram` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_setting_whatsapp` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_setting_insta` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_setting_linkedin` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_setting_aparat` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_setting_youtube` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

## `tbl_teacher`

Source: `site.sql:740`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_teacher_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_teacher_lname` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_fname` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_lnameen` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_fnameen` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_username` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_nationalcode` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_sex` | `int NOT NULL` |
| `tbl_teacher_birthday` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_country` | `varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_mobile` | `varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_tel` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_postcode` | `varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_state` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_city` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_address` | `varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_email` | `varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_password` | `varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_zipcode` | `int NOT NULL` |
| `tbl_teacher_phone` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_showimage` | `int NOT NULL` |
| `tbl_teacher_profile` | `varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_date` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_ip` | `varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_status` | `int NOT NULL` |
| `tbl_teacher_lang` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `date_back` | `varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_oldid` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_code` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_info` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_text` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_linkdin` | `varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_twitter` | `varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_teacher_insta` | `varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_teacher_id`)`

## `tbl_textsection`

Source: `site.sql:796`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_textsection_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_textsection_title` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_textsection_fld1` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_textsection_fld2` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_textsection_fld3` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_textsection_fld4` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_textsection_type` | `varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_textsection_section` | `varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_textsection_parent` | `varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_textsection_lng` | `varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_textsection_code` | `varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_textsection_undertitle` | `longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_bin` |
| `tbl_textsection_img` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_textsection_id`)`

## `tbl_user`

Source: `site.sql:831`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin ROW_FORMAT=DYNAMIC`.

| Column | SQL definition |
| --- | --- |
| `tbl_user_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_user_username` | `varchar(255) COLLATE utf8mb3_bin DEFAULT NULL` |
| `tbl_user_password` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL` |
| `tbl_user_email` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL` |
| `tbl_user_lastlogin` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL` |
| `tbl_user_status` | `int DEFAULT NULL` |
| `tbl_user_name` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL` |
| `tbl_user_section` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci` |
| `tbl_user_format` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci` |
| `tbl_user_set` | `int DEFAULT NULL` |
| `tbl_user_lng` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL` |
| `tbl_user_rev` | `int DEFAULT NULL` |
| `tbl_user_lastip` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL` |
| `tbl_user_active` | `int DEFAULT NULL` |
| `tbl_user_type` | `varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_user_id`) USING BTREE`

## `tbl_view`

Source: `site.sql:868`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=6942 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_view_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_view_user` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_view_class` | `varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_view_date` | `varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_view_course` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_view_id`)`

## `tbl_workshop`

Source: `site.sql:895`. Table options: `ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin`.

| Column | SQL definition |
| --- | --- |
| `tbl_workshop_id` | `int NOT NULL AUTO_INCREMENT` |
| `tbl_workshop_cat` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_title` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_classcode` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_code` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_number` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_price` | `int NOT NULL` |
| `tbl_workshop_teacher` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_img` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_status` | `int NOT NULL` |
| `tbl_workshop_dateadd` | `varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_admin` | `int NOT NULL` |
| `tbl_workshop_parentcode` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_content` | `text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_special` | `int NOT NULL` |
| `tbl_workshop_order` | `int NOT NULL` |
| `tbl_workshop_update` | `varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_certificate` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_question` | `int NOT NULL` |
| `tbl_workshop_count` | `int NOT NULL` |
| `tbl_workshop_free` | `int NOT NULL` |
| `tbl_workshop_logo` | `int NOT NULL` |
| `tbl_workshop_start` | `varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_end` | `varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_mortabet` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_modelclass` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_sath` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_pish` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |
| `tbl_workshop_time` | `varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL` |

Keys and indexes:

- `PRIMARY KEY (`tbl_workshop_id`)`


## Builder schema added on 2026-10-27

Source: [`site_admin/migrations/20261027_page_builder.sql`](../site_admin/migrations/20261027_page_builder.sql). Both tables are InnoDB, default `utf8mb4_bin`; individual identifier fields use ASCII binary collations where declared. These tables were confirmed present in `expertsacademy_dev` on 2026-11-01. This supplement does not modify the historical `site.sql` dump above.

| Table | Columns |
| --- | --- |
| `tbl_page_builder_layouts` | `page_code varchar(255)`, `locale char(2)`, `mode varchar(16)` default `builder`, `structure_mode varchar(16)` default `independent`, `revision bigint unsigned` default 1, `created_at datetime`, `updated_at datetime` |
| `tbl_page_component_instances` | `id char(36)`, `page_code varchar(255)`, `marker varchar(255)`, `type_key varchar(64)`, `locale_scope varchar(6)`, `sort_order int unsigned`, `enabled tinyint(1)` default 1, `schema_version int unsigned` default 1, nullable `deleted_at datetime`, `created_at datetime`, `updated_at datetime` |

Layout primary key is `(page_code,locale)`. Instance primary key is `id`; `idx_page_locale_order` is `(page_code,locale_scope,deleted_at,sort_order,id)`. Creation/update timestamps default to current timestamp, with `updated_at` updating automatically. The migration includes `ADD COLUMN IF NOT EXISTS structure_mode` for the earlier draft table. See the SQL for the exact nullability/collations/default clauses.

There are no added foreign keys, type/locale enum constraints, or unique positions. Content stays in `tbl_content`/`tbl_textsection` under UUID parents, separate from Legacy page-code parents. Registry/API enforce ownership/types/locale/field rules. A revision number is a concurrency token; no content revision-history table exists.
