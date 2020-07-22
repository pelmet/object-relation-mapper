
DROP TABLE IF EXISTS d_queued_commands;
CREATE TABLE IF NOT EXISTS `d_queued_commands` (
  `qc_id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_time_start` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 0,
  `qc_time_end` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 0,
  `qc_status` int(1) NOT NULL,
  `qc_command` varchar(2000) COLLATE utf8_unicode_ci NULL DEFAULT "",
  PRIMARY KEY (`qc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

DROP TABLE IF EXISTS d_queued_commands_logs;
CREATE TABLE IF NOT EXISTS `d_queued_commands_logs` (
  `qcl_id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `qcl_text` varchar(2000) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  PRIMARY KEY (`qcl_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

DROP TABLE IF EXISTS d_queued_commands_fogs;
CREATE TABLE IF NOT EXISTS `d_queued_commands_fogs` (
  `qcf_id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_id_1` int(11) NOT NULL,
  `qc_id_2` int(11) NOT NULL,
  `qcf_text` varchar(2000) COLLATE utf8_unicode_ci NOT NULL DEFAULT "",
  PRIMARY KEY (`qcf_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

DROP TABLE IF EXISTS d_validate_types;
CREATE TABLE IF NOT EXISTS `d_validate_types` (
  `qc_int` int(10) NOT NULL AUTO_INCREMENT,
  `qc_string` varchar(10) COLLATE utf8mb4_unicode_ci NULL DEFAULT "",
  `qc_decimal` decimal(5,2) NULL,
  `qc_boolean` tinyint(1) NULL,
  `qc_date` date NULL,
  `qc_time` int(11) NULL DEFAULT 1,
  `qc_text` TEXT COLLATE utf8mb4_unicode_ci NULL DEFAULT "",
  `qc_char` char(3) COLLATE utf8mb4_unicode_ci NULL DEFAULT "",
  `qc_enum` enum('abc','def','ghi','jkl'),
  PRIMARY KEY (`qc_int`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ;
