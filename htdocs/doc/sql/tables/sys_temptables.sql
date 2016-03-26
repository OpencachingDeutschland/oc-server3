SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_temptables`;
CREATE TABLE `sys_temptables` (
  `threadid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY  (`threadid`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
