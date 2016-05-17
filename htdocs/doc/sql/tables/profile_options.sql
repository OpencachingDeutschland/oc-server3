SET NAMES 'utf8';
DROP TABLE IF EXISTS `profile_options`;
CREATE TABLE `profile_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `internal_use` tinyint(1) NOT NULL DEFAULT '1',
  `default_value` text,
  `check_regex` varchar(255) DEFAULT NULL,
  `option_order` int(11) NOT NULL DEFAULT '100',
  `option_input` varchar(20) NOT NULL DEFAULT 'text',
  `optionset` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
