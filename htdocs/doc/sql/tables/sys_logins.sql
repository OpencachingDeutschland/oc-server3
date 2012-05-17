SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_logins`;
CREATE TABLE `sys_logins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date_created` datetime NOT NULL COMMENT 'via trigger (sys_logins)',
  `remote_addr` varchar(15) NOT NULL,
  `success` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `timestamp` (`date_created`),
  KEY `remote_addr` (`remote_addr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
