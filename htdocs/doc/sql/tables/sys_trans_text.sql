SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_trans_text`;
CREATE TABLE `sys_trans_text` (
  `trans_id` int(10) unsigned NOT NULL,
  `lang` varchar(2) NOT NULL,
  `text` mediumtext NOT NULL,
  `last_modified` datetime NOT NULL COMMENT 'via trigger (sys_trans_text)',
  PRIMARY KEY  (`lang`,`trans_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
