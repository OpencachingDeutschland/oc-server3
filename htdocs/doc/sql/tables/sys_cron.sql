SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_cron`;
CREATE TABLE `sys_cron` (
  `name` varchar(60) NOT NULL,
  `last_run` datetime NOT NULL,
  PRIMARY KEY  (`name`),
  KEY `last_run` (`last_run`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via cronjob' ;
