SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_trans_ref`;
CREATE TABLE `sys_trans_ref` (
  `trans_id` int(10) unsigned NOT NULL,
  `resource_name` varchar(80) NOT NULL,
  `line` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`trans_id`,`resource_name`,`line`),
  KEY `style` (`resource_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
