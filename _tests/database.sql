CREATE TABLE IF NOT EXISTS `d_queued_commands` (
  `qc_id` int(11) NOT NULL AUTO_INCREMENT,
  `qc_time_start` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `qc_time_end` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `qc_status` int(1) NOT NULL,
  `qc_command` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`qc_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;